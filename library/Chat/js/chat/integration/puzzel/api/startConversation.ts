
import { throwErrorIfResponseNotOk } from "./helper/throwError";
import { SESSION_ID_KEY } from "../../../enums";

class StartConversation implements StartConversationInterface {
    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(conversationId: string): Promise<RouteConversationResponse> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();

        const response = await fetch(`
            ${this.puzzelConfig.getCommunicationUrl()}/api/conversation/${conversationId}/route/${this.puzzelConfig.getChatQueueKey()}
        `, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${accessToken}`,
                },
                body: JSON.stringify([]),
            },
        );

        await throwErrorIfResponseNotOk(response, "Failed to start conversation");

        const conversationData = await response.json() as RouteConversationResponse;
        return conversationData;
    }
}

export default StartConversation;