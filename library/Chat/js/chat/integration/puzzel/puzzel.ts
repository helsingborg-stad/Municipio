class Puzzel {
    private conversationId?: string;
    private iqSessionToken?: string;
    private authenticated: AuthenticationResponse | undefined;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
        private puzzelConversationManager: PuzzelConversationManagerInterface,
        private chat: any,
        private isPersistent: boolean,
    ) {
    }

    public async init(): Promise<void> {
        this.authenticated = await this.puzzelAuthenticator.authenticate();

        if (!this.authenticated) {
            return;
        }

        const data = await this.puzzelConversationManager.startConversation();
    }
}

export default Puzzel;