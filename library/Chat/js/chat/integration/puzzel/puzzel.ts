class Puzzel {
    private authenticated: AuthenticationData | undefined;
    private conversation: RouteConversationResponse | undefined;
    private hadStoredAuthentication: boolean = false;
    private triedRerun: boolean = false;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
        private puzzelConversationManager: PuzzelConversationManagerInterface,
        private conversationStorageManager: ConversationStorageManagerInterface,
        private typingIndicatorHandler: PuzzelTypingIndicatorHandlerInterface,
        private incomingMessageHandler: PuzzelIncomingMessageHandlerInterface,
        private outgoingChatHandler: PuzzelOutgoingChatHandlerInterface,
        private agentLeftHandler: MetaDataHandler,
        private agentJoinedHandler: MetaDataHandler,
        private chatHistoryHandler: PuzzelChatHistoryHandlerInterface,
        private chatStatusHandler: PuzzelChatStatusHandlerInterface,
        private chatHandler: ChatHandlerInterface
    ) {
    }

    public async init(): Promise<void> {
        this.puzzelConversationManager.stopListening();
        this.typingIndicatorHandler.clearTypingState();

        this.chatHandler.setDefaultPersistedState(false);
        const localStorageData = this.conversationStorageManager.getLocalStorage();
        this.hadStoredAuthentication = !!localStorageData?.authenticationData;
        this.authenticated = localStorageData?.authenticationData ?? await this.puzzelAuthenticator.authenticate();

        if (!this.authenticated) {
            return;
        }

        if (this.hadStoredAuthentication) {
            const chatStatus = this.chatStatusHandler.getStatus(await this.puzzelConversationManager.getConversationStatus());
            console.log("Conversation status:", chatStatus);
            if (!chatStatus) {
                this.handleFailedChatStatus();
                return;
            }
        }

        this.hadStoredAuthentication ? 
            await this.handleStoredAuthenticationFlow() : 
            await this.handleNewAuthenticationFlow();

        this.outgoingChatHandler.register();

        await this.startMetaDataListener();
        await this.startConversationListener();
    }

    private async handleStoredAuthenticationFlow(): Promise<void> {
        console.log("Handling stored authentication flow.");
        const currentConversation = await this.puzzelConversationManager.getCurrentConversation();
        const currentMetaData = await this.puzzelConversationManager.getCurrentMetaData();
        this.chatHistoryHandler.load(currentConversation, currentMetaData);
    }

    private async handleNewAuthenticationFlow(): Promise<void> {
        console.log("Handling new authentication flow.");
        this.conversation = await this.puzzelConversationManager.startConversation();
        this.conversationStorageManager.setLocalStorage(this.conversation, this.authenticated!);
        await this.puzzelConversationManager.sendUserJoinEventToConversation();
    }

    private async startMetaDataListener(): Promise<void> {
        await this.puzzelConversationManager.listenForMetaData((data) => {
            this.typingIndicatorHandler.handleMetaDataEvent(data);
            this.agentLeftHandler.handleMetaDataEvent(data);
            this.agentJoinedHandler.handleMetaDataEvent(data);
        });
    }


    private async startConversationListener(): Promise<void> {
        await this.puzzelConversationManager.listenForConversation((data) => {
            this.incomingMessageHandler.handleConversationEvent(data);
        });
    }

    private handleFailedChatStatus(): void {
        this.conversationStorageManager.clearLocalStorage();
        if (!this.triedRerun) {
            this.triedRerun = true;
            this.init();
        } else {
            console.error("Failed to get chat status after retry.");
        }
    }
}

export default Puzzel;