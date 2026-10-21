import { throwErrorIfResponseNotOk } from "./helper/throwError";

class GetConversationStatus implements GetConversationStatusInterface {
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
}

export default GetConversationStatus;