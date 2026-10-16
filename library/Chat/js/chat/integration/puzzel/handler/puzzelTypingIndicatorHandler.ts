class PuzzelTypingIndicatorHandler implements PuzzelTypingIndicatorHandlerInterface {
    private static readonly typingTimeoutMs = 7000;

    private pendingMessage: any = null;
    private typingTimeoutId: ReturnType<typeof setTimeout> | null = null;

    public constructor(
        private chat: any,
    ) {
    }

    public handleMetaDataEvent(data: PuzzelMetaDataEvent): void {
        if (!data || data.senderRole !== "agent" || data.type !== "typingEvent") {
            return;
        }

        if (!this.pendingMessage) {
            this.pendingMessage = this.chat.addPendingMessage();
        }

        this.resetTypingTimeout();
    }

    public clearTypingState(): void {
        if (this.typingTimeoutId) {
            clearTimeout(this.typingTimeoutId);
            this.typingTimeoutId = null;
        }

        if (this.pendingMessage) {
            this.chat.deleteMessage(this.pendingMessage);
            this.pendingMessage = null;
        }
    }

    private resetTypingTimeout(): void {
        if (this.typingTimeoutId) {
            clearTimeout(this.typingTimeoutId);
        }

        this.typingTimeoutId = setTimeout(() => {
            if (this.pendingMessage) {
                this.chat.deleteMessage(this.pendingMessage);
            }

            this.pendingMessage = null;
            this.typingTimeoutId = null;
        }, PuzzelTypingIndicatorHandler.typingTimeoutMs);
    }
}

export default PuzzelTypingIndicatorHandler;