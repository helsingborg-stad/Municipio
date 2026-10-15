interface PuzzelOutgoingChatHandlerInterface {
	register(): void;
}

interface PuzzelTypingIndicatorHandlerInterface {
	handleMetaDataEvent(data: PuzzelMetaDataEvent): void;
	clearTypingState(): void;
}

interface PuzzelIncomingMessageHandlerInterface {
	handleConversationEvent(data: PuzzelConversationEvent): void;
}
