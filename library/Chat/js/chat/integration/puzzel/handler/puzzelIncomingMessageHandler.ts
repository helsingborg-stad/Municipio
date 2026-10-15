class PuzzelIncomingMessageHandler implements PuzzelIncomingMessageHandlerInterface {
    public constructor(
        private chat: any,
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
        this.chat.addMessage(data.message, true);
    }
}

export default PuzzelIncomingMessageHandler;