import Chat from "./chat";
import { ChatSessionFactory } from "./ChatSessionFactory";
import ClearButton from "./clearButton";
import MarkdownIt from "markdown-it";

document.addEventListener("chat:initialized", (e: any) => {
	const chat = e.detail.chat;
	const clearButtonElement = e.detail.chat.getElement().querySelector("[data-js-chat-clear]") as HTMLElement;

	const markdownParser = new MarkdownIt({
		html: false,
		linkify: false,
		typographer: false,
	});

	markdownParser.validateLink = (url: string): boolean => {
		return /^(https?:|mailto:|tel:|\/|#)/i.test(url);
	};

	if (clearButtonElement) {
		new ClearButton(clearButtonElement, chat);
	}

	new Chat(
		new ChatSessionFactory(wpApiSettings.root),
		chat,
		markdownParser
	).init();
});