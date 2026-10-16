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
