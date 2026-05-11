import Chat from "./chat";
import { ChatSessionFactory } from "./ChatSessionFactory";
import ClearButton from "./clearButton";
import MarkdownIt from "markdown-it";
import NewChatSessionButton from "./newChatSessionButton";

document.addEventListener("chat:initialized", (e: any) => {
	const chat = e.detail.chat;
	const clearButtonElement = e.detail.chat.getElement().querySelector("[data-js-chat-clear]") as HTMLElement;
	const newChatButtonElement = e.detail.chat.getElement().querySelector("[data-js-chat-new]") as HTMLElement;

	const markdownParser = new MarkdownIt({
		html: false,
		linkify: false,
		typographer: false,
	});

	const chatSessionFactory = new ChatSessionFactory(wpApiSettings.root);

	markdownParser.validateLink = (url: string): boolean => {
		return /^(https?:|mailto:|tel:|\/|#)/i.test(url);
	};

	if (clearButtonElement) {
		new ClearButton(clearButtonElement, chat);
	}

	const chatInstance = new Chat(
		chatSessionFactory,
		chat,
		markdownParser
	);

	if (newChatButtonElement) {
		new NewChatSessionButton(newChatButtonElement, chatInstance, chat);
	}

	chatInstance.init();
});