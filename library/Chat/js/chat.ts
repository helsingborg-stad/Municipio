import MarkdownIt from "markdown-it";
import FeedbackApi from "./feedbackApi";

class Chat implements ChatInterface {
	private session: ChatSession | null = null;
	private streamedContent: string = "";

	constructor(
		private readonly sessionFactory: ChatSessionFactory,
		private readonly chat: any,
		private readonly markdownParser: MarkdownIt,
		private readonly feedbackFactory: FeedbackFactoryInterface,
		private readonly feedbackApi: FeedbackApi,
		private readonly assistantName: string | null = null,
	) {}

	public init(): void {
		this.session = this.sessionFactory.create(this.assistantName);
		console.log(this.session);
		this.listenForUserMessages();
	}

	private postMessageStat(): void {
		this.feedbackApi.postStat('message');
	}

	public createNewChatSession(): void {
		this.session = this.sessionFactory.create(this.assistantName);
	}

	private listenForUserMessages(): void {
		this.chat.getElement().addEventListener('chat:message-added', (e: any) => {
			const message = e.detail;

			if (message.getIsReply()) {
				return;
			}

			this.sendMessage(message.getContent());
		});
	}

	private renderMarkdown(content: string): string {
		try {
			return this.markdownParser.render(content);
		} catch (error) {
			console.error("[Chat] Failed to render markdown, falling back to escaped text.", error);
			return `<p>${this.markdownParser.utils.escapeHtml(content)}</p>`;
		}
	}

	private async sendMessage(message: string): Promise<void> {
		if (!this.session) return;

		const pendingMessage = this.chat.addPendingMessage();
		this.chat.disableSend();
		this.streamedContent = "";
		let contentAdded = false;

		for await (const event of this.session.ask(message)) {
			switch (event.type) {
				case "text":
					this.streamedContent = event.content;
					this.chat.editMessage(this.renderMarkdown(this.streamedContent), pendingMessage);
					contentAdded = true;
					break;
				case "tool_call":
					contentAdded = true;
					break;
				case "done":
					if (this.streamedContent) {
						this.chat.editMessage(this.renderMarkdown(this.streamedContent), pendingMessage);
					}
					this.chat.enableSend();
					if (!contentAdded) {
						this.chat.deleteMessage(pendingMessage);
					} else {
						this.feedbackFactory.create(pendingMessage);
						this.postMessageStat();
					}
					break;
			}
		}
	}
}

export default Chat;
