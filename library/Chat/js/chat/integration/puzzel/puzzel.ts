import { PUZZEL_CHAT_STATUS } from "./enums";

class Puzzel implements ChatIntegrationInterface {
    private authenticated: AuthenticationData | undefined;
    private conversation: RouteConversationResponse | undefined;
    private chatStatus: PuzzelConversationStatusResponse | null = null;

    public constructor(
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
        private puzzelAvailabilityManager: PuzzelAvailabilityManagerInterface,
        private puzzelConversationManager: PuzzelConversationManagerInterface,
        private conversationStorageManager: ConversationStorageManagerInterface,
        private typingIndicatorHandler: PuzzelTypingIndicatorHandlerInterface,
        private incomingMessageHandler: PuzzelIncomingMessageHandlerInterface,
        private outgoingChatHandler: PuzzelOutgoingChatHandlerInterface,
        private agentLeftHandler: MetaDataHandler,
        private agentJoinedHandler: MetaDataHandler,
        private chatHistoryHandler: PuzzelChatHistoryHandlerInterface,
        private chatStatusHandler: PuzzelChatStatusHandlerInterface,
        private chatHandler: ChatHandlerInterface,
        private uiManager: ChatUiManagerInterface,
        private puzzelConfig: PuzzelConfigInterface,
    ) {
    }

    /**
     * Without an ongoing conversation, a failure returns false so the chat can be removed.
     * With one, the visitor expects their chat to still be there, so it stays and shows an
     * error instead. Storage is cleared either way, so the next page load starts clean.
     */
    public async init(): Promise<this|false> {
        const hadOngoingConversation = this.conversationStorageManager.getLocalStorage() !== null;

        let result = await this.connect();

        if (result === "failed") {
            // Stale stored conversation (or a new one without a status): start over once.
            this.resetStoredConversation();
            result = await this.connect();
        }

        if (result === "connected") {
            await this.startSession();
            return this;
        }

        this.resetStoredConversation();

        if (!hadOngoingConversation) {
            return false;
        }

        console.error("Failed to resume or restart the Puzzel conversation.");
        this.showConnectionError();
        return this;
    }

    private async connect(): Promise<PuzzelConnectResult> {
        this.puzzelConversationManager.stopListening();
        this.typingIndicatorHandler.clearTypingState();
        this.chatHandler.setDefaultPersistedState(false);
        this.chatStatus = null;

        const storedAuthentication = this.conversationStorageManager.getLocalStorage()?.authenticationData;

        try {
            return storedAuthentication
                ? await this.resumeConversation(storedAuthentication)
                : await this.startNewConversation();
        } catch (error) {
            console.error("Failed to connect to the Puzzel conversation.", error);
            return "failed";
        }
    }

    /**
     * An ongoing conversation is resumed even outside opening hours: the agent may still be
     * in it, and the conversation status tells us whether it is still alive.
     */
    private async resumeConversation(authentication: AuthenticationData): Promise<PuzzelConnectResult> {
        this.authenticated = authentication;

        if (!await this.getChatStatus()) {
            return "failed";
        }

        const currentConversation = await this.puzzelConversationManager.getCurrentConversation();
        const currentMetaData = await this.puzzelConversationManager.getCurrentMetaData();
        this.chatHistoryHandler.load(currentConversation, currentMetaData);

        return "connected";
    }

    private async startNewConversation(): Promise<PuzzelConnectResult> {
        const authenticated = await this.authenticate();

        if (!authenticated || !await this.isAvailable()) {
            return "unavailable";
        }

        this.authenticated = authenticated;
        this.conversation = await this.puzzelConversationManager.startConversation();
        this.conversationStorageManager.setLocalStorage(this.conversation, authenticated);
        await this.puzzelConversationManager.sendUserJoinEventToConversation();

        return await this.getChatStatus() ? "connected" : "failed";
    }

    private async startSession(): Promise<void> {
        this.chatStatusHandler.handleStatusEvent(this.chatStatus!);
        await this.startConversationStatusListener();

        this.outgoingChatHandler.register();

        await this.startMetaDataListener();
        await this.startConversationListener();
        this.setupUi();
    }

    private showConnectionError(): void {
        this.setupUi();
        this.chatHandler.setErrorData();
        this.chatHandler.displayBanner(true);
        this.chatHandler.disable();
    }

    private setupUi(): void {
        this.uiManager.setTitle(this.puzzelConfig.getName());
        this.uiManager.setAvatar(this.puzzelConfig.getAvatar());
        this.uiManager.clearActions();
        this.uiManager.addMinimizeAction();
        this.uiManager.addCloseAction();
    }

    private resetStoredConversation(): void {
        this.conversationStorageManager.clearLocalStorage();
        this.puzzelConversationManager.resetConversationId();
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
            if (data?.type === "chat") {
                this.conversationStorageManager.updateLastActivity();
            }

            this.incomingMessageHandler.handleConversationEvent(data);
        });
    }

    private async startConversationStatusListener(): Promise<void> {
         await this.puzzelConversationManager.listenForConversationStatus((updatedStatus) => {
             if (
                 updatedStatus.status !== PUZZEL_CHAT_STATUS.IN_QUEUE &&
                 updatedStatus.status !== PUZZEL_CHAT_STATUS.ALLOCATED
                ) {
                this.puzzelConversationManager.stopListeningForConversationStatus();
            }

            this.chatStatusHandler.handleStatusEvent(updatedStatus);
        });
    }

    private async authenticate(): Promise<AuthenticationData | null> {
        try {
            return await this.puzzelAuthenticator.authenticate() ?? null;
        } catch (error) {
            console.error("Failed to authenticate with Puzzel.", error);
            return null;
        }
    }

    private async isAvailable(): Promise<boolean> {
        try {
            return await this.puzzelAvailabilityManager.isAvailable();
        } catch (error) {
            console.error("Failed to verify chat availability.", error);
            return false;
        }
    }

    private async getChatStatus(): Promise<PuzzelConversationStatusResponse|null> {
        this.chatStatus = this.chatStatus ?? this.chatStatusHandler.getStatus(await this.puzzelConversationManager.getConversationStatus());

        return this.chatStatus;
    }
}

export default Puzzel;
