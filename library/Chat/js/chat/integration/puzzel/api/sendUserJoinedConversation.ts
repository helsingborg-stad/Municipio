import { throwErrorIfResponseNotOk } from "./helper/throwError";

class SendUserJoinedConversation implements SendUserJoinedConversationInterface {
    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(conversationId: string): Promise<void> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();

        const response = await fetch(`
            ${this.puzzelConfig.getCommunicationUrl()}/api/metadata/${conversationId}
        `, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${accessToken}`,
                },
                body: JSON.stringify({
                    "senderName": "Visitor",
                    "type": "participantJoinEvent"
                }),
            },
        );

        await throwErrorIfResponseNotOk(response, "Failed to send user join event");
    }
}

export default SendUserJoinedConversation;