import Popover from "./popover/popover";

/**
 * Checks whether chat markup exists on the page before event listeners are registered.
 */
let chatInitializer: any = null;

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

function initChatBubble(chat: any) {
	const popover = getPopover();
	const isOpen = popover?.matches(':popover-open');

	// TODO: REMOVE TEST CODE BELOW
	return initializeChat(chat);

	if (!popover || isOpen) {
		scrollToBottom(chat);
		return initializeChat(chat);
	}

	const listener = () => {
		popover.removeEventListener('toggle', listener);
		scrollToBottom(chat);
		initializeChat(chat);
	};

	popover.addEventListener('toggle', listener);
}

function scrollToBottom(chat: any) {
	chat.getScrollContainer().scrollTop = chat.getScrollContainer().scrollHeight;
	chat.getElement().classList.remove("u-visibility--hidden");
}

async function initializeChat(chat: any) {
	if (!chatInitializer) {
		chatInitializer = new (await import("./chatFactory")).default();
	}

	await chatInitializer.init(chat);
}

function getPopover(): HTMLElement | null {
	const popover = document.querySelector(`#chat-global-root`);
	return popover ? (popover as HTMLElement) : null;
}