import { ChatUiApi, UserMessageHandler } from "../../chat/chatUiApi";
import { MarkdownRenderer } from "../../shared/markdownRenderer";
import AiFeedbackApi from "./AiFeedbackApi";
import { AiChatSessionFactory } from "./AiChatSessionFactory";
import { AiChatSession } from "./aiTypes";

interface AiChatIntegrationConfig {
	subscribeToUserMessages: (handler: UserMessageHandler) => () => void;
	uiApi: ChatUiApi;
	sessionFactory: AiChatSessionFactory;
	feedbackApi: AiFeedbackApi;
	renderMarkdown: MarkdownRenderer;
	assistantName: string | null;
	persistSession: boolean;
	errorMessage: string;
}

class AiChatIntegration {
	private session: AiChatSession | null = null;
	private streamedContent: string = "";
	private unsubscribeUserMessage?: () => void;

	constructor(private readonly config: AiChatIntegrationConfig) {}

	public init(): void {
		this.session = this.createSession();
		this.unsubscribeUserMessage = this.config.subscribeToUserMessages((message) =>
			this.handleUserMessage(message),
		);
	}

	public destroy(): void {
		if (this.unsubscribeUserMessage) {
			this.unsubscribeUserMessage();
		}
	}

	public createNewChatSession(): void {
		this.session = this.createSession();
		this.session.clearSessionForAssistant(this.config.assistantName ?? "");
	}

	private createSession(): AiChatSession {
		return this.config.sessionFactory.create(
			this.config.assistantName,
			this.config.persistSession,
		);
	}

	private postMessageStat(): void {
		this.config.feedbackApi.postStat("message");
	}

	private async handleUserMessage(message: string): Promise<void> {
		if (!this.session) {
			this.session = this.createSession();
		}

		const pendingMessage = this.config.uiApi.addPendingMessage();
		this.config.uiApi.disableInput();
		this.streamedContent = "";
		let contentAdded = false;

		try {
			for await (const event of this.session.ask(message)) {
				switch (event.type) {
					case "text":
						this.streamedContent = event.content;
						this.config.uiApi.updateMessage(
							this.config.renderMarkdown(this.streamedContent),
							pendingMessage,
						);
						contentAdded = true;
						break;
					case "tool_call":
						contentAdded = true;
						break;
					case "done":
						if (this.streamedContent) {
							this.config.uiApi.updateMessage(
								this.config.renderMarkdown(this.streamedContent),
								pendingMessage,
							);
						}
						this.config.uiApi.enableInput();
						if (!contentAdded) {
							this.config.uiApi.removeMessage(pendingMessage);
						} else {
							this.config.uiApi.attachFeedback(pendingMessage);
							this.postMessageStat();
						}
						break;
				}
			}
		} catch (error) {
			console.error("[AiChatIntegration] Error during message processing:", error);
			this.config.uiApi.enableInput();
			this.config.uiApi.updateMessage(this.config.errorMessage, pendingMessage);
		}
	}
}

export default AiChatIntegration;
