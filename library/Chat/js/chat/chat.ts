import type MarkdownIt from "markdown-it";
import type FeedbackApi from "./feedbackApi";
import type {
	ConversationProvider,
	ConversationProviderCapabilities,
} from "./conversationProvider";
import type ConversationProviderFactory from "./conversationProviderFactory";

class Chat implements ChatInterface {
	private provider: ConversationProvider | null = null;
	private providerCapabilities: ConversationProviderCapabilities | null = null;
	private streamedContent: string = "";

	constructor(
		private readonly providerFactory: ConversationProviderFactory,
		private readonly chat: any,
		private readonly markdownParser: MarkdownIt,
		private readonly feedbackFactory: FeedbackFactoryInterface,
		private readonly feedbackApi: FeedbackApi,
		private readonly assistantName: string | null = null,
		private readonly persistSession: boolean = true,
	) {}

	public init(): void {
		this.provider = this.providerFactory.create(
			this.assistantName,
			this.persistSession,
		);
		this.providerCapabilities = this.provider.getCapabilities();
		this.listenForUserMessages();
	}

	private postMessageStat(): void {
		this.feedbackApi.postStat("message");
	}

	public createNewChatSession(): void {
		this.provider = this.providerFactory.create(
			this.assistantName,
			this.persistSession,
		);
		this.providerCapabilities = this.provider.getCapabilities();

		if (this.providerCapabilities.supportsSessionReset) {
			this.provider.resetConversation();
		}
	}

	private listenForUserMessages(): void {
		this.chat.getElement().addEventListener("chat:message-added", (e: any) => {
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
			console.error(
				"[Chat] Failed to render markdown, falling back to escaped text.",
				error,
			);
			return `<p>${this.markdownParser.utils.escapeHtml(content)}</p>`;
		}
	}

	private async sendMessage(message: string): Promise<void> {
		if (!this.provider) return;

		const pendingMessage = this.chat.addPendingMessage();
		this.chat.disableSend();
		this.streamedContent = "";
		let contentAdded = false;

		try {
			for await (const event of this.provider.sendMessage(message)) {
				switch (event.type) {
					case "text":
						this.streamedContent = event.content;
						this.chat.editMessage(
							this.renderMarkdown(this.streamedContent),
							pendingMessage,
						);
						contentAdded = true;
						break;
					case "activity":
						contentAdded = true;
						break;
					case "done":
						if (this.streamedContent) {
							this.chat.editMessage(
								this.renderMarkdown(this.streamedContent),
								pendingMessage,
							);
						}
						this.chat.enableSend();
						if (!contentAdded) {
							this.chat.deleteMessage(pendingMessage);
						} else {
							if (this.providerCapabilities?.supportsFeedback === true) {
								this.feedbackFactory.create(pendingMessage);
							}
							this.postMessageStat();
						}
						break;
				}
			}
		} catch (error) {
			console.error("[Chat] Error during message processing:", error);
			this.chat.enableSend();
			this.chat.editMessage(municipioChatLocale.errorMessage, pendingMessage);
		}
	}
}

export default Chat;
