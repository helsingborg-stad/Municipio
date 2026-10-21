import PollingEventListener from "./helper/pollingEventListener";
import { throwErrorIfResponseNotOk } from "./helper/throwError";

class GetConversation implements ListenForConversationInterface {
    private pollingEventListener = new PollingEventListener<PuzzelConversationEvent>();

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
        await this.pollingEventListener.listen(
            conversationId,
            callback,
            this.execute.bind(this),
            3000,
            "Failed to poll conversation",
        );
    }

    public stopListening(conversationId: string): void {
        this.pollingEventListener.stopListening(conversationId);
    }
}

export default GetConversation;