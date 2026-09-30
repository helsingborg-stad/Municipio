import { ChatSessionFactory } from "./chat/ChatSessionFactory";
import Chat from "./chat/chat";
import FeedbackApi from "./chat/feedbackApi";
import FeedbackFactory from "./chat/feedbackFactory";
import GreetingPhrase from "./chat/greetingPhrase";
import NewChatSessionButton from "./chat/newChatSessionButton";

/**
 * Orchestrates chat setup from the initialized chat element.
 */
class InitChat {
	/**
	 * Initializes the chat services and UI bindings for a chat instance.
	 */
	public init(chat: any, markdownParser: MarkdownIt, apiRoot: string): void {
		const configuration = this.getChatConfiguration(chat);
		const services = this.createChatServices(
			chat,
			configuration.feedbackTemplate,
			apiRoot,
		);

		this.restoreFeedbackButtons(
			chat,
			configuration.greetingsPhrase,
			services.feedbackFactory,
		);
		this.createGreetingPhrase(chat, configuration.greetingsPhrase);

		const chatInstance = this.createChatInstance(
			chat,
			markdownParser,
			configuration,
			services,
		);

		this.createNewChatSessionButton(
			configuration.newChatButtonElement,
			chatInstance,
			chat,
		);

		chatInstance.init();
	}

	/**
	 * Reads the relevant DOM-backed chat configuration from the chat element.
	 */
	private getChatConfiguration(chat: any): ChatConfiguration {
		const chatElement = chat.getElement() as HTMLElement;
		const persistentAttribute = chatElement.getAttribute(
			"data-js-chat-persistent",
		);

		return {
			chatAssistant: chatElement.dataset.jsChatAssistant || null,
			feedbackTemplate: chatElement.querySelector(
				"[data-js-chat-feedback]",
			) as HTMLTemplateElement | null,
			greetingsPhrase: chatElement.dataset.jsChatGreetingsPhrase || null,
			isPersistentChat:
				persistentAttribute !== null && persistentAttribute !== "false",
			newChatButtonElement: chatElement.querySelector(
				"[data-js-chat-new]",
			) as HTMLElement | null,
		};
	}

	/**
	 * Creates the service objects used during chat initialization.
	 */
	private createChatServices(
		chat: any,
		feedbackTemplate: HTMLTemplateElement | null,
		apiRoot: string,
	): ChatServices {
		const chatSessionFactory = new ChatSessionFactory(apiRoot);
		const feedbackApi = new FeedbackApi(apiRoot);
		const feedbackFactory = new FeedbackFactory(
			chat,
			feedbackTemplate as HTMLTemplateElement,
			feedbackApi,
		);

		return {
			chatSessionFactory,
			feedbackApi,
			feedbackFactory,
		};
	}

	/**
	 * Recreates feedback controls for reply messages that already exist.
	 */
	private restoreFeedbackButtons(
		chat: any,
		greetingsPhrase: string | null,
		feedbackFactory: FeedbackFactory,
	): void {
		chat.getMessages().forEach((message: any, index: number) => {
			if (!message.getIsReply()) {
				return;
			}

			if (index === 0 && greetingsPhrase === message.getContent()) {
				return;
			}

			feedbackFactory.create(message);
		});
	}

	/**
	 * Creates the optional greeting phrase helper when configured.
	 */
	private createGreetingPhrase(chat: any, greetingsPhrase: string | null): void {
		if (greetingsPhrase) {
			new GreetingPhrase(chat, greetingsPhrase);
		}
	}

	/**
	 * Creates the main chat instance with all required dependencies.
	 */
	private createChatInstance(
		chat: any,
		markdownParser: MarkdownIt,
		configuration: ChatConfiguration,
		services: ChatServices,
	): Chat {
		return new Chat(
			services.chatSessionFactory,
			chat,
			markdownParser,
			services.feedbackFactory,
			services.feedbackApi,
			configuration.chatAssistant,
			configuration.isPersistentChat,
		);
	}

	/**
	 * Creates the optional new chat session button when present in the markup.
	 */
	private createNewChatSessionButton(
		newChatButtonElement: HTMLElement | null,
		chatInstance: Chat,
		chat: any,
	): void {
		if (newChatButtonElement) {
			new NewChatSessionButton(newChatButtonElement, chatInstance, chat);
		}
	}
}

export default InitChat;

