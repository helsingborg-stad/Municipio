import MarkdownIt from "markdown-it";
import { ChatSessionFactory } from "./chat/ChatSessionFactory";
import Chat from "./chat/chat";
import FeedbackApi from "./chat/feedbackApi";
import FeedbackFactory from "./chat/feedbackFactory";
import GreetingPhrase from "./chat/greetingPhrase";
import NewChatSessionButton from "./chat/newChatSessionButton";
import Popover from "./popover/popover";

document.addEventListener("popover:initialized", (e: any) => {
	const popover = e.detail;

	if (popover.id !== "chat-global-root") return;

	const chatContainer = popover.element?.querySelector(".municipio-ai-chat");

	if (!chatContainer) return;

	new Popover(popover, chatContainer as HTMLElement);
});

document.addEventListener("chat:initialized", (e: any) => {
	const chat = e.detail;

	if (!chat.getElement().classList.contains("municipio-ai-chat__chat")) return;
	const newChatButtonElement = chat
		.getElement()
		.querySelector("[data-js-chat-new]") as HTMLElement;
	const greetingsPhrase =
		chat.getElement().dataset.jsChatGreetingsPhrase || null;
	const feedbackTemplate =
		chat.getElement().querySelector("[data-js-chat-feedback]") || null;
	const chatAssistant = chat.getElement().dataset.jsChatAssistant || null;
	const persistentAttribute = chat
		.getElement()
		.getAttribute("data-js-chat-persistent");
	const isPersistentChat =
		persistentAttribute !== null && persistentAttribute !== "false";

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
		chatAssistant ?? null,
		isPersistentChat,
	);

	if (newChatButtonElement) {
		new NewChatSessionButton(newChatButtonElement, chatInstance, chat);
	}

	chatInstance.init();
});
