import { throwErrorIfResponseNotOk } from "./helper/throwError";

class ListenForConversation implements ListenForConversationInterface {
    private callbacks: { [conversationId: string]: Array<(data: PuzzelConversationEvent) => void> } = {};
    private currentEventLength: { [conversationId: string]: number } = {};
    private activeIntervals = new Map<string, ReturnType<typeof setInterval>>();

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(conversationId: string): Promise<PuzzelConversationEvent[]> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();
        const conversationUrl = `${this.puzzelConfig.getCommunicationUrl()}/api/conversation/${encodeURIComponent(conversationId)}`;

        const response = await fetch(conversationUrl, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${accessToken}`,
            },
        });

        await throwErrorIfResponseNotOk(response, "Failed to listen for conversation");

        return response.json();
    }

    public async listen(conversationId: string, callback: (data: PuzzelConversationEvent) => void): Promise<void> {
        if (!this.callbacks[conversationId]) {
            this.callbacks[conversationId] = [];
        }

        this.callbacks[conversationId].push(callback);

        const poll = async (): Promise<void> => {
            const data = await this.execute(conversationId);
            this.notifyOnNewEvents(conversationId, data);
        };

        if (this.activeIntervals.has(conversationId)) {
            clearInterval(this.activeIntervals.get(conversationId));
        }

        await poll();

        const intervalId = setInterval(() => {
            void poll().catch((error: unknown) => {
                console.error("Failed to poll conversation", error);
            });
        }, 3000);

        this.activeIntervals.set(conversationId, intervalId);
    }

    private notifyOnNewEvents(conversationId: string, data: PuzzelConversationEvent[]): void {
        if (!Array.isArray(data)) {
            return;
        }

        const previousLength = this.currentEventLength[conversationId];

        if (previousLength === undefined) {
            this.currentEventLength[conversationId] = data.length;
            return;
        }

        if (data.length <= previousLength) {
            this.currentEventLength[conversationId] = data.length;
            return;
        }

        const newEvents = data.slice(previousLength);
        this.currentEventLength[conversationId] = data.length;

        newEvents.forEach((newEvent: PuzzelConversationEvent) => {
            this.callbacks[conversationId].forEach((cb) => cb(newEvent));
        });
    }
}

export default ListenForConversation;