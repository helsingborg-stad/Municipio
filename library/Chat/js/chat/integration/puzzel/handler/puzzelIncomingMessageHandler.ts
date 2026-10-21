class PuzzelIncomingMessageHandler implements PuzzelIncomingMessageHandlerInterface {
    public constructor(
        private chatHandler: ChatHandlerInterface,
        private typingIndicatorHandler: PuzzelTypingIndicatorHandlerInterface,
    ) {
    }

    public handleConversationEvent(data: PuzzelConversationEvent): void {
        if (
            !data ||
            data.type !== "chat" ||
            data.senderRole !== "agent" ||
            !data.message ||
            data.message.trim() === ""
        ) {
            return;
        }

        this.typingIndicatorHandler.clearTypingState();
        this.chatHandler.addMessage(data.message, true);
    }
}

export default PuzzelIncomingMessageHandler;