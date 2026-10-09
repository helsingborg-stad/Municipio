class PuzzelConversationManager implements PuzzelConversationManagerInterface {
    private conversationId: string | undefined = undefined;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public getConversationId(): string {
        this.conversationId ??= crypto.randomUUID();
        return this.conversationId;
    }

    public async startConversation(): Promise<void> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();
        console.log(accessToken);
        const response = await fetch(`
            ${this.puzzelConfig.getCommunicationUrl()}/api/conversation/${this.getConversationId()}/route/${this.puzzelConfig.getChatQueueKey()}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${accessToken}`,
                },
                body: JSON.stringify([]),
            },
        );
        console.log(response);
        if (!response.ok) {
            throw new Error(
                `Failed to start conversation: ${response.status} ${response.statusText}`,
            );
        }

        const conversationData = await response.json();
        return conversationData;
    }
}

export default PuzzelConversationManager;