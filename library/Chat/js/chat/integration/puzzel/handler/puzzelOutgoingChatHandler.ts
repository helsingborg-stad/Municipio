class PuzzelOutgoingChatHandler implements PuzzelOutgoingChatHandlerInterface {
	private isRegistered = false;

	public constructor(
		private chatHandler: ChatHandlerInterface,
		private puzzelConversationManager: PuzzelConversationManagerInterface,
	) {
	}

	public register(): void {
		if (this.isRegistered) {
			return;
		}

		this.chatHandler.onMessageAdded((message: any) => {

			if (message.getIsReply()) {
				return;
			}

			if (message.getContent() && message.getContent().trim() !== "") {
				void this.puzzelConversationManager.sendUserMessage(message.getContent());
			}
		});

		this.chatHandler.onTyping(() => {
			void this.puzzelConversationManager.sendUserTypingEvent();
		});

		this.isRegistered = true;
	}
}

export default PuzzelOutgoingChatHandler;