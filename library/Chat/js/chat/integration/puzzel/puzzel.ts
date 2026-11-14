import { PUZZEL_CHAT_STATUS } from "./enums";

class Puzzel implements ChatIntegrationInterface {
    private authenticated: AuthenticationData | undefined;
    private conversation: RouteConversationResponse | undefined;
    private hadStoredAuthentication: boolean = false;
    private triedRerun: boolean = false;
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

    public async init(): Promise<this|false> {
        this.puzzelConversationManager.stopListening();
        this.typingIndicatorHandler.clearTypingState();
        this.chatHandler.setDefaultPersistedState(false);

        const localStorageData = this.conversationStorageManager.getLocalStorage();
        this.hadStoredAuthentication = !!localStorageData?.authenticationData;
        this.authenticated = localStorageData?.authenticationData ?? await this.puzzelAuthenticator.authenticate();

        if (!this.authenticated) {
            return false;
        }

        if (!await this.isAvailable()) {
            return false;
        }

        if (this.hadStoredAuthentication && !await this.validateChatStatus()) {
            return false;
        }

        this.hadStoredAuthentication ? 
            await this.handleStoredAuthenticationFlow() : 
            await this.handleNewAuthenticationFlow();


        const localChatStatus = await this.getChatStatus();

        if (!localChatStatus) {
            const result = await this.handleFailedChatStatus();
            return result;
        }

        this.chatStatusHandler.handleStatusEvent(localChatStatus);
        await this.startConversationStatusListener();

        this.outgoingChatHandler.register();

        await this.startMetaDataListener();
        await this.startConversationListener();
        this.uiManager.setTitle(this.puzzelConfig.getName());
        this.uiManager.setAvatar(this.puzzelConfig.getAvatar());
        this.uiManager.clearActions();
        this.uiManager.addMinimizeAction();
        this.uiManager.addCloseAction();
        this.triedRerun = false;
        return this;
    }

    private async handleStoredAuthenticationFlow(): Promise<void> {
        const currentConversation = await this.puzzelConversationManager.getCurrentConversation();
        const currentMetaData = await this.puzzelConversationManager.getCurrentMetaData();
        this.chatHistoryHandler.load(currentConversation, currentMetaData);
    }

    private async handleNewAuthenticationFlow(): Promise<void> {
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

    private async handleFailedChatStatus(): Promise<this|false> {
        this.conversationStorageManager.clearLocalStorage();
        this.puzzelConversationManager.resetConversationId();

        if (!this.triedRerun) {
            this.triedRerun = true;
            return await this.init();
        } else {
            console.error("Failed to get chat status after retry with fresh setup.");
            return false;
        }
    }

    private async validateChatStatus(): Promise<boolean> {
        try {
            if (!await this.getChatStatus()) {
                await this.handleFailedChatStatus();
                return false;
            }
        } catch (error) {
            console.error("Failed to validate conversation status.", error);
            await this.handleFailedChatStatus();
            return false;
        }

        return true;
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