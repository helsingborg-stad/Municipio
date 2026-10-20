class Puzzel {
    private authenticated: AuthenticationData | undefined;
    private conversation: RouteConversationResponse | undefined;
    private hadStoredAuthentication: boolean = false;

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
        const localStorageData = this.conversationStorageManager.getLocalStorage();
        this.hadStoredAuthentication = !!localStorageData?.authenticationData;
        this.authenticated = localStorageData?.authenticationData ?? await this.puzzelAuthenticator.authenticate();

        if (!this.authenticated) {
            return;
        }

        this.hadStoredAuthentication ? 
            await this.handleStoredAuthenticationFlow() : 
            await this.handleNewAuthenticationFlow();

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

    private async handleStoredAuthenticationFlow(): Promise<void> {
        console.log("Handling stored authentication flow.");
        const currentConversation = await this.puzzelConversationManager.getCurrentConversation();

        console.log("Current conversation:", currentConversation);
    }

    private async handleNewAuthenticationFlow(): Promise<void> {
        console.log("Handling new authentication flow.");
        this.conversation = await this.puzzelConversationManager.startConversation();
        this.conversationStorageManager.setLocalStorage(this.conversation, this.authenticated!);
        await this.puzzelConversationManager.sendUserJoinEventToConversation();
    }
}

export default Puzzel;