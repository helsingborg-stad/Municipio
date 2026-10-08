import MarkdownIt from "markdown-it";
import { AiConversationProviderFactory } from "./chat/ChatSessionFactory";
import Chat from "./chat/chat";
import FeedbackApi from "./chat/feedbackApi";
import FeedbackFactory from "./chat/feedbackFactory";
import GreetingPhrase from "./chat/greetingPhrase";
import NewChatSessionButton from "./chat/newChatSessionButton";

function createMarkdownParser(): MarkdownIt {
	const parser = new MarkdownIt({ html: false, linkify: false, typographer: false });

	parser.validateLink = (url: string): boolean => /^(https?:|mailto:|tel:|\/|#)/i.test(url);

	return parser;
}

class ChatFactory {
	public init(chat: any): void {
		const chatElement = chat.getElement() as HTMLElement;
		const greetingsPhrase = chatElement.dataset.jsChatGreetingsPhrase || null;
		const feedbackTemplate = chatElement.querySelector("[data-js-chat-feedback]") as HTMLTemplateElement | null;
		const chatAssistant = chatElement.dataset.jsChatAssistant || null;
		const persistentAttribute = chatElement.getAttribute("data-js-chat-persistent");
		const newChatButtonElement = chatElement.querySelector("[data-js-chat-new]") as HTMLElement | null;
		const markdownParser = createMarkdownParser();
		const chatSessionFactory = new AiConversationProviderFactory(wpApiSettings.root);
		const feedbackApi = new FeedbackApi(wpApiSettings.root);
		const feedbackFactory = new FeedbackFactory(chat, feedbackTemplate as HTMLTemplateElement, feedbackApi);

		chat.getMessages().forEach((message: any, index: number) => {
			if (!message.getIsReply()) {
				return;
			}

			if (index === 0 && greetingsPhrase === message.getContent()) {
				return;
			}

			feedbackFactory.create(message);
		});

		if (greetingsPhrase) {
			new GreetingPhrase(chat, greetingsPhrase);
		}

		const chatInstance = new Chat(
			chatSessionFactory,
			chat,
			markdownParser,
			feedbackFactory,
			feedbackApi,
			chatAssistant,
			persistentAttribute !== null && persistentAttribute !== "false",
		);
		
		if (newChatButtonElement) {
			new NewChatSessionButton(newChatButtonElement, chatInstance, chat);
		}

		chatInstance.init();
	}
}

export default ChatFactory;