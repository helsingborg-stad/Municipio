import type MarkdownIt from "markdown-it";
import { EneoSessionFactory } from "./eneoSessionFactory";
import { EneoSession } from "./eneoSession";

class Eneo implements EneoInterface {
	private session: EneoSession | null = null;
	private streamedContent: string = "";

	constructor(
		private readonly sessionFactory: EneoSessionFactory,
		private readonly chat: any,
		private readonly markdownParser: MarkdownIt,
		private readonly feedbackFactory: FeedbackFactoryInterface,
		private readonly feedbackApi: FeedbackApiInterface,
		private readonly uiManager: ChatUiManagerInterface,
		private readonly assistantName: string,
		private readonly persistSession: boolean = true,
	) {}

	public init(): Promise<this|false> {
		this.session = this.sessionFactory.create(
			this.assistantName,
			this.persistSession,
		);

		this.listenForUserMessages();
		this.chat.setDefaultShouldPersist(this.persistSession);
		this.uiManager.clearActions();
		this.uiManager.addMinimizeAction();
		this.uiManager.addCloseAction();
		this.uiManager.setTitle(this.assistantName);
		const clear = this.uiManager.addClearAction();

		clear.addListener(() => {
			this.createNewEneoSession();
		});

		return Promise.resolve(this);
	}

	private postMessageStat(): void {
		this.feedbackApi.postStat("message");
	}

	public createNewEneoSession(): void {
		this.session = this.sessionFactory.create(
			this.assistantName,
			this.persistSession,
		);
		this.session.clearSessionForAssistant(this.assistantName ?? "");
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
		if (!this.session) return;

		const pendingMessage = this.chat.addPendingMessage();
		this.chat.disableSend();
		this.streamedContent = "";
		let contentAdded = false;

		try {
			for await (const event of this.session.ask(message)) {
				switch (event.type) {
					case "text":
						this.streamedContent = event.content;
						this.chat.editMessage(
							this.renderMarkdown(this.streamedContent),
							pendingMessage,
						);
						contentAdded = true;
						break;
					case "tool_call":
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
							this.feedbackFactory.create(pendingMessage);
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

export default Eneo;