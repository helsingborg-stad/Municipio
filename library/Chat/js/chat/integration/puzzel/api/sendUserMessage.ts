import { throwErrorIfResponseNotOk } from "./helper/throwError";

class SendUserMessage {
    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(conversationId: string, message: string): Promise<void> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();

        const response = await fetch(`
            ${this.puzzelConfig.getCommunicationUrl()}/api/conversation/${conversationId}/message
        `, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${accessToken}`,
                },
                body: JSON.stringify({
                    "senderName": "Visitor",
                    "message": message,
                }),
            },
        );

        await throwErrorIfResponseNotOk(response, "Failed to send user join event");
    }
}

export default SendUserMessage;