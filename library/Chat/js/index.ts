import MarkdownIt from "markdown-it";
import { ChatSessionFactory } from "./ChatSessionFactory";
import Chat from "./chat";
import FeedbackApi from "./feedbackApi";
import FeedbackFactory from "./feedbackFactory";
import GreetingPhrase from "./greetingPhrase";
import NewChatSessionButton from "./newChatSessionButton";

document.addEventListener("chat:initialized", (e: any) => {
	const chat = e.detail;

	if (!chat.getElement().classList.contains("municipio-ai-chat")) return;

	const newChatButtonElement = chat
		.getElement()
		.querySelector("[data-js-chat-new]") as HTMLElement;
	const greetingsPhrase =
		chat.getElement().dataset.jsChatGreetingsPhrase || null;
	const feedbackTemplate =
		chat.getElement().querySelector("[data-js-chat-feedback]") || null;

	const markdownParser = new MarkdownIt({
		html: false,
		linkify: false,
		typographer: false,
	});

	const chatSessionFactory = new ChatSessionFactory(wpApiSettings.root);

	markdownParser.validateLink = (url: string): boolean => {
		return /^(https?:|mailto:|tel:|\/|#)/i.test(url);
	};

	const feedbackApi = new FeedbackApi(wpApiSettings.root);
	const feedbackFactory = new FeedbackFactory(
		chat,
		feedbackTemplate as HTMLTemplateElement,
		feedbackApi,
	);

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
	);

	if (newChatButtonElement) {
		new NewChatSessionButton(newChatButtonElement, chatInstance, chat);
	}

	chatInstance.init();
});
