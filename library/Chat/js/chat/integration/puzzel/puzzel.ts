class Puzzel {
    private authenticated: AuthenticationResponse | undefined;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
        private puzzelConversationManager: PuzzelConversationManagerInterface,
        private typingIndicatorHandler: MetaDataHandler,
        private incomingMessageHandler: PuzzelIncomingMessageHandlerInterface,
        private outgoingChatHandler: PuzzelOutgoingChatHandlerInterface,
        private agentLeftHandler: MetaDataHandler,
        private agentJoinedHandler: MetaDataHandler,
    ) {
    }

    public async init(): Promise<void> {
        this.authenticated = await this.puzzelAuthenticator.authenticate();

        if (!this.authenticated) {
            return;
        }

        await this.puzzelConversationManager.startConversation();
        await this.puzzelConversationManager.sendUserJoinEventToConversation();

        this.outgoingChatHandler.register();

        await this.puzzelConversationManager.listenForMetaData((data) => {
            console.log("MetaData received:", data);
            this.typingIndicatorHandler.handleMetaDataEvent(data);
            this.agentLeftHandler.handleMetaDataEvent(data);
            this.agentJoinedHandler.handleMetaDataEvent(data);
        });

        await this.puzzelConversationManager.listenForConversation((data) => {
            console.log("Conversation received:", data);
            this.incomingMessageHandler.handleConversationEvent(data);
        });
    }
}

export default Puzzel;