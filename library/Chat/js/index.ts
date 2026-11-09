import ChatInitializer from "./chatInitializer";
import Popover from "./popover/popover";
import {
	persistChatBubbleOpenState,
	shouldOpenChatBubbleOnInit,
} from "./chat/chatBubbleOpenState";

const bubbleStateListeners = new WeakMap<HTMLElement, EventListener>();

/**
 * Checks whether chat markup exists on the page before event listeners are registered.
 */
document.addEventListener("popover:initialized", (e: any) => {
	const popover = e.detail;

	if (popover.id !== "chat-global-root") return;

	const chatContainer = popover.element?.querySelector(
		"[data-js-municipio-chat-wrapper]",
	);
	const messageArea = popover.element?.querySelector("[data-js-message-area]");

	if (!chatContainer || !messageArea) return;

	new Popover(popover, chatContainer as HTMLElement, messageArea as HTMLElement);
});

document.addEventListener("chat:initialized", async (e: any) => {
	const chat = e.detail;

	if (!chat.getElement().hasAttribute("data-js-municipio-chat")) return;
	if (chat.getElement().hasAttribute("data-js-municipio-chat-bubble")) {
		return initChatBubble(chat);
	}

	initializeChat(chat);
});

async function initChatBubble(chat: any) {
	const popover = getPopover();

	if (popover) {
		registerBubbleOpenStateListener(popover, chat);
	} else {
		scrollToBottom(chat);
		return initializeChat(chat);
	}

	if (
		shouldOpenChatBubbleOnInit(chat) &&
		!popover.matches(":popover-open")
	) {
		scrollToBottom(chat);
		await initializeChat(chat);
		popover.showPopover();
		return;
	}

	const listener = async () => {
		popover.removeEventListener('toggle', listener);
		scrollToBottom(chat);
		await initializeChat(chat);
	};

	popover.addEventListener('toggle', listener);
}

function scrollToBottom(chat: any) {
	chat.getScrollContainer().scrollTop = chat.getScrollContainer().scrollHeight;
	chat.getElement().classList.remove("u-visibility--hidden");
}

async function initializeChat(chat: any) {
	chat.getElement().classList.add('u-preloader');
	await new ChatInitializer(chat).init();
	chat.getElement().classList.remove('u-preloader');
}

function getPopover(): HTMLElement | null {
	const popover = document.querySelector(`#chat-global-root`);
	return popover ? (popover as HTMLElement) : null;
}

function registerBubbleOpenStateListener(popover: HTMLElement, chat: any): void {
	const existingListener = bubbleStateListeners.get(popover);

	if (existingListener) {
		popover.removeEventListener("toggle", existingListener);
	}

	const stateListener: EventListener = () => {
		persistChatBubbleOpenState(chat, popover.matches(":popover-open"));
	};

	popover.addEventListener("toggle", stateListener);
	bubbleStateListeners.set(popover, stateListener);
}