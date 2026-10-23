import PollingEventListener from "./helper/pollingEventListener";
import { throwErrorIfResponseNotOk } from "./helper/throwError";

class GetConversationStatus implements GetConversationStatusInterface {
    private pollingEventListener = new PollingEventListener<PuzzelConversationStatusResponse>();

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(conversationId: string): Promise<PuzzelConversationStatusResponse[]> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();

        const response = await fetch(`${this.puzzelConfig.getCommunicationUrl()}/api/allocation/requestStatus/${conversationId}/`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Content-Type': 'application/json'
            }
        });

        throwErrorIfResponseNotOk(response, `Failed to get conversation status for conversation ID: ${conversationId}`);

        return await response.json();
    }

    public async listen(conversationId: string, callback: (data: PuzzelConversationStatusResponse) => void): Promise<void> {
        await this.pollingEventListener.listen(
            conversationId,
            callback,
            this.execute.bind(this),
            3000,
            "Failed to poll conversation status",
            true,
        );
    }

    public stopListening(conversationId: string): void {
        this.pollingEventListener.stopListening(conversationId);
    }
}

export default GetConversationStatus;