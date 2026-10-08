import { ChatUiApi, UserMessageHandler } from "./chatUiApi";

class Chat {
	constructor(
		private readonly chat: any,
		private readonly feedbackFactory: FeedbackFactoryInterface,
	) {}

	public onUserMessage(handler: UserMessageHandler): () => void {
		const listener = (event: Event): void => {
			const customEvent = event as CustomEvent<any>;
			const message = customEvent.detail;

			if (!message || message.getIsReply()) {
				return;
			}

			void handler(message.getContent());
		};

		this.chat.getElement().addEventListener("chat:message-added", listener);

		return () => this.chat.getElement().removeEventListener("chat:message-added", listener);
	}

	public getUiApi(): ChatUiApi {
		return {
			addMessage: (content: string, isReply: boolean) =>
				this.chat.addMessage(content, isReply),
			addPendingMessage: () => this.chat.addPendingMessage(),
			updateMessage: (content: string, messageInstance: any) =>
				this.chat.editMessage(content, messageInstance),
			removeMessage: (messageInstance: any) =>
				this.chat.deleteMessage(messageInstance),
			enableInput: () => this.chat.enableSend(),
			disableInput: () => this.chat.disableSend(),
			clearMessages: () => this.chat.clearMessages(),
			getMessages: () => this.chat.getMessages(),
			attachFeedback: (messageInstance: any) => this.feedbackFactory.create(messageInstance),
		};
	}
}

export default Chat;
