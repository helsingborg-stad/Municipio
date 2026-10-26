import { PUZZEL_CHAT_STATUS } from "./enums";

class Puzzel {
    private authenticated: AuthenticationData | undefined;
    private conversation: RouteConversationResponse | undefined;
    private hadStoredAuthentication: boolean = false;
    private triedRerun: boolean = false;
    private chatStatus: PuzzelConversationStatusResponse | null = null;

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
        private chatHandler: ChatHandlerInterface,
        private actionFactory: ChatActionFactoryInterface
    ) {
    }

    public async init(): Promise<void> {
        this.actionFactory.addMinimize();
        this.puzzelConversationManager.stopListening();
        this.typingIndicatorHandler.clearTypingState();

        this.chatHandler.setDefaultPersistedState(false);
        const localStorageData = this.conversationStorageManager.getLocalStorage();
        this.hadStoredAuthentication = !!localStorageData?.authenticationData;
        this.authenticated = localStorageData?.authenticationData ?? await this.puzzelAuthenticator.authenticate();

        if (!this.authenticated) {
            return;
        }

        if (this.hadStoredAuthentication && !await this.validateChatStatus()) {
            return;
        }

        this.hadStoredAuthentication ? 
            await this.handleStoredAuthenticationFlow() : 
            await this.handleNewAuthenticationFlow();


        const localChatStatus = await this.getChatStatus();

        if (!localChatStatus) {
            await this.handleFailedChatStatus();
            return;
        }

        this.chatStatusHandler.handleStatusEvent(localChatStatus);
        await this.startConversationStatusListener();

        this.outgoingChatHandler.register();

        await this.startMetaDataListener();
        await this.startConversationListener();
        this.triedRerun = false;
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

    private async startConversationStatusListener(): Promise<void> {
         await this.puzzelConversationManager.listenForConversationStatus((updatedStatus) => {
             console.log("Updated chat status:", updatedStatus);
             if (
                 updatedStatus.status !== PUZZEL_CHAT_STATUS.IN_QUEUE &&
                 updatedStatus.status !== PUZZEL_CHAT_STATUS.ALLOCATED
                ) {
                this.puzzelConversationManager.stopListeningForConversationStatus();
            }

            this.chatStatusHandler.handleStatusEvent(updatedStatus);
        });
    }

    private async handleFailedChatStatus(): Promise<void> {
        this.conversationStorageManager.clearLocalStorage();
        this.puzzelConversationManager.resetConversationId();

        if (!this.triedRerun) {
            this.triedRerun = true;
            await this.init();
        } else {
            console.error("Failed to get chat status after retry with fresh setup.");
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

    private async getChatStatus(): Promise<PuzzelConversationStatusResponse|null> {
        this.chatStatus = this.chatStatus ?? this.chatStatusHandler.getStatus(await this.puzzelConversationManager.getConversationStatus());

        return this.chatStatus;
    }
}

export default Puzzel;