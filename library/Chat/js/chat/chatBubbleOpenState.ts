import { CHAT_BUBBLE_DEFAULTS, SESSION_ID_KEY } from "./enums";

function getStorage(): Storage | null {
	try {
		return window.localStorage;
	} catch {
		return null;
	}
}

function readBubbleOpenStates(storage: Storage): ChatBubbleOpenStates {
	const storedValue = storage.getItem(SESSION_ID_KEY.GLOBAL_CHAT_BUBBLE_OPEN_STATES);

	if (!storedValue) {
		return {};
	}

	try {
		const parsedValue = JSON.parse(storedValue);
		return typeof parsedValue === "object" && parsedValue !== null ? parsedValue : {};
	} catch {
		return {};
	}
}

function getChatBubbleName(chat: ChatBubbleOpenStateChat): string {
	const chatElement = chat?.getElement?.();
	const chatBubbleName = chatElement?.id;

	if (typeof chatBubbleName === "string" && chatBubbleName.length > 0) {
		return chatBubbleName;
	}

	return CHAT_BUBBLE_DEFAULTS.NAME;
}

function isDesktop(): boolean {
	return window.innerWidth >= CHAT_BUBBLE_DEFAULTS.DESKTOP_MIN_WIDTH;
}

function hasExpired(updatedAt: number): boolean {
	return Date.now() - updatedAt > CHAT_BUBBLE_DEFAULTS.OPEN_STATE_MAX_AGE_MS;
}

function isOpenAndFresh(state: ChatBubbleOpenState | boolean | undefined): boolean {
	if (!state || typeof state === "boolean") {
		return false;
	}

	if (state.isOpen !== true) {
		return false;
	}

	return !hasExpired(state.updatedAt);
}

export function shouldOpenChatBubbleOnInit(chat: ChatBubbleOpenStateChat): boolean {
	if (!isDesktop()) {
		return false;
	}

	const storage = getStorage();

	if (!storage) {
		return false;
	}

	const chatBubbleName = getChatBubbleName(chat);
	const openStates = readBubbleOpenStates(storage);

	return isOpenAndFresh(openStates[chatBubbleName]);
}

export function persistChatBubbleOpenState(chat: ChatBubbleOpenStateChat, isOpen: boolean): void {
	const storage = getStorage();

	if (!storage) {
		return;
	}

	const chatBubbleName = getChatBubbleName(chat);
	const openStates = readBubbleOpenStates(storage);
	openStates[chatBubbleName] = {
		isOpen,
		updatedAt: Date.now(),
	};

	storage.setItem(
		SESSION_ID_KEY.GLOBAL_CHAT_BUBBLE_OPEN_STATES,
		JSON.stringify(openStates),
	);
}
