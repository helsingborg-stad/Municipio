import Chat from "./chat";
import { ChatSessionFactory } from "./ChatSessionFactory";
import MarkdownIt from "markdown-it";
import NewChatSessionButton from "./newChatSessionButton";
import GreetingPhrase from "./greetingPhrase";
import FeedbackFactory from "./feedbackFactory";

document.addEventListener("chat:initialized", (e: any) => {
	const chat = e.detail;

	if (!chat.getElement().classList.contains("municipio-ai-chat")) return;

	const newChatButtonElement = chat.getElement().querySelector("[data-js-chat-new]") as HTMLElement;
	const greetingsPhrase = chat.getElement().dataset.jsChatGreetingsPhrase || null;
	const feedbackTemplate = chat.getElement().querySelector("[data-js-chat-feedback]") || null;

	const markdownParser = new MarkdownIt({
		html: false,
		linkify: false,
		typographer: false,
	});

	const chatSessionFactory = new ChatSessionFactory(wpApiSettings.root);

	markdownParser.validateLink = (url: string): boolean => {
		return /^(https?:|mailto:|tel:|\/|#)/i.test(url);
	};

	if (greetingsPhrase) {
		new GreetingPhrase(chat, greetingsPhrase);
	}

	const chatInstance = new Chat(
		chatSessionFactory,
		chat,
		markdownParser,
		new FeedbackFactory(chat, feedbackTemplate as HTMLTemplateElement)
	);

	if (newChatButtonElement) {
		new NewChatSessionButton(newChatButtonElement, chatInstance, chat);
	}

	chatInstance.init();
});