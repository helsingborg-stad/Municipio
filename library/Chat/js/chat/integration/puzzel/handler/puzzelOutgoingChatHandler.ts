class PuzzelOutgoingChatHandler implements PuzzelOutgoingChatHandlerInterface {
	private isRegistered = false;

	public constructor(
		private puzzelConversationManager: PuzzelConversationManagerInterface,
		private chat: any,
	) {
	}

	public register(): void {
		if (this.isRegistered) {
			return;
		}

		this.chat.getElement().addEventListener("chat:message-added", (event: any) => {
			const message = event.detail;

			if (message.getIsReply()) {
				return;
			}

			if (message.getContent() && message.getContent().trim() !== "") {
				void this.puzzelConversationManager.sendUserMessage(message.getContent());
			}
		});

		this.chat.getElement().addEventListener("chat:typing", () => {
			void this.puzzelConversationManager.sendUserTypingEvent();
		});

		this.isRegistered = true;
	}
}

export default PuzzelOutgoingChatHandler;