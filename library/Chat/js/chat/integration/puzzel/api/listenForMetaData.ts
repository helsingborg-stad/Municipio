import { throwErrorIfResponseNotOk } from "./helper/throwError";

class ListenForMetaData implements ListenForMetaDataInterface {
    private callbacks: { [conversationId: string]: Array<(data: PuzzelMetaDataEvent) => void> } = {};
    private currentEventLength: { [conversationId: string]: number } = {};
    private activeIntervals = new Map<string, ReturnType<typeof setInterval>>();

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(conversationId: string): Promise<PuzzelMetaDataEvent[]> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();
        const metadataUrl = `${this.puzzelConfig.getCommunicationUrl()}/api/metadata/${encodeURIComponent(conversationId)}`;

        const response = await fetch(metadataUrl, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${accessToken}`,
            },
        });

        await throwErrorIfResponseNotOk(response, "Failed to listen for meta data");

        return response.json();
    }

    public async listen(conversationId: string, callback: (data: PuzzelMetaDataEvent) => void): Promise<void> {
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
                console.error("Failed to poll metadata", error);
            });
        }, 3000);

        this.activeIntervals.set(conversationId, intervalId);
    }

    private notifyOnNewEvents(conversationId: string, data: PuzzelMetaDataEvent[]): void {
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

        newEvents.forEach((newEvent: PuzzelMetaDataEvent) => {
            this.callbacks[conversationId].forEach((cb) => cb(newEvent));
        });
    }
}

export default ListenForMetaData;