interface PuzzelOutgoingChatHandlerInterface {
	register(): void;
}

interface MetaDataHandler {
    handleMetaDataEvent(data: PuzzelMetaDataEvent): void;
}
interface PuzzelTypingIndicatorHandlerInterface extends MetaDataHandler {
	clearTypingState(): void;
}

interface PuzzelIncomingMessageHandlerInterface {
	handleConversationEvent(data: PuzzelConversationEvent): void;
}

interface PuzzelChatHistoryHandlerInterface {
	load(conversationEvents: PuzzelConversationEvent[], metaDataEvents: PuzzelMetaDataEvent[]): void;
}

interface PuzzelChatStatusHandlerInterface {
    getStatus(statuses: PuzzelConversationStatusResponse[]): string;
}

interface ChatHandlerInterface {
	setDefaultPersistedState(state: boolean): void;
	addMessage(message: string, isAgent: boolean): void;
	addInfo(content: string): void;
	addInfoFromType(type: string, senderRole?: string): void;
	addAgentJoinedInfo(): void;
	addAgentLeftInfo(): void;
	disable(): void;
	enable(): void;
	addPendingMessage(): any;
	deleteMessage(message: any): void;
	onMessageAdded(listener: (message: any) => void): void;
	onTyping(listener: () => void): void;
}