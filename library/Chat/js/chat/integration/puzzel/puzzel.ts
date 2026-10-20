class Puzzel {
    private authenticated: AuthenticationResponse | undefined;
    private conversation: RouteConversationResponse | undefined;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
        private puzzelConversationManager: PuzzelConversationManagerInterface,
        private conversationStorageManager: ConversationStorageManagerInterface,
        private typingIndicatorHandler: MetaDataHandler,
        private incomingMessageHandler: PuzzelIncomingMessageHandlerInterface,
        private outgoingChatHandler: PuzzelOutgoingChatHandlerInterface,
        private agentLeftHandler: MetaDataHandler,
        private agentJoinedHandler: MetaDataHandler,
    ) {
    }

    public async init(): Promise<void> {
        this.authenticated = this.conversationStorageManager.getAuthenticationData() ?? await this.puzzelAuthenticator.authenticate();

        if (!this.authenticated) {
            return;
        }

        this.conversation = this.conversationStorageManager.getConversationData() ?? await this.puzzelConversationManager.startConversation();

        this.conversationStorageManager.setLocalStorage(this.conversation, this.authenticated);

        console.log("Conversation:", this.conversation, "Authenticated:", this.authenticated);
        // await this.puzzelConversationManager.sendUserJoinEventToConversation();

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