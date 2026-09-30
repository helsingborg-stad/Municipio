import type MarkdownIt from "markdown-it";
import InitChat from "./initChat";
import Popover from "./popover/popover";

/**
 * Checks whether chat markup exists on the page before event listeners are registered.
 */
const chatInitializer = new InitChat();
let markdownParserPromise: Promise<MarkdownIt> | null = null;

/**
 * Lazily creates and caches the markdown parser used by the chat UI.
 */
async function getMarkdownParser(): Promise<MarkdownIt> {
	if (!markdownParserPromise) {
		markdownParserPromise = (async () => {
			const MarkdownItConstructor = (await import("markdown-it")).default;
			const parser = new MarkdownItConstructor({
				html: false,
				linkify: false,
				typographer: false,
			});

			parser.validateLink = (url: string): boolean => {
				return /^(https?:|mailto:|tel:|\/|#)/i.test(url);
			};

			return parser;
		})();
	}

	return markdownParserPromise;
}

document.addEventListener("popover:initialized", (e: any) => {
	const popover = e.detail;

	if (popover.id !== "chat-global-root") return;

	const chatContainer = popover.element?.querySelector(
		"[data-js-municipio-ai-chat-wrapper]",
	);
	const messageArea = popover.element?.querySelector("[data-js-message-area]");

	if (!chatContainer || !messageArea) return;

	new Popover(popover, chatContainer as HTMLElement, messageArea as HTMLElement);
});

document.addEventListener("chat:initialized", async (e: any) => {
	const chat = e.detail;

	if (!chat.getElement().hasAttribute("data-js-municipio-ai-chat")) return;
	if (chat.getElement().hasAttribute("municipio-ai-chat-bubble")) {
		return initChatBubble(chat);
	}

	initChat(chat);
});

function initChatBubble(chat: any) {
	const popover = getPopover();
	const isOpen = popover?.matches(':popover-open');

	if (!popover) {
		return initChat(chat);
	}

	if (isOpen) {
		return initChat(chat);
	}

	const listener = () => {
		popover.removeEventListener('toggle', listener);
		initChat(chat);
	};

	popover.addEventListener('toggle', listener);
}

async function initChat(chat: any) {
	const markdownParser = await getMarkdownParser();
	chatInitializer.init(chat, markdownParser, wpApiSettings.root);
}

function getPopover(): HTMLElement | null {
	const popover = document.querySelector(`#chat-global-root`);
	return popover ? (popover as HTMLElement) : null;
}
