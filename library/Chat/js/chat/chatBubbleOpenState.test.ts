import {
	persistChatBubbleInitializeState,
	persistChatBubbleOpenState,
	shouldInitializeChatBubbleOnInit,
	shouldOpenChatBubbleOnInit,
} from "./chatBubbleOpenState";
import { SESSION_ID_KEY } from "./enums";

describe("chatBubbleOpenState", () => {
	let dateNowSpy: jest.SpyInstance<number, []>;

	beforeEach(() => {
		window.localStorage.clear();
		Object.defineProperty(window, "innerWidth", {
			value: 1024,
			writable: true,
			configurable: true,
		});
		dateNowSpy = jest.spyOn(Date, "now").mockReturnValue(1_000_000);
	});

	afterEach(() => {
		dateNowSpy.mockRestore();
	});

	const createChat = (
		id: string,
		assistants: Record<string, string> = {},
	) => {
		const chatElement = document.createElement("div");
		chatElement.id = id;
		chatElement.dataset.jsChatAssistants = JSON.stringify(assistants);

		return {
			getElement: jest.fn().mockReturnValue(chatElement),
		};
	};

	it("persists and restores an open chat bubble state for the same chat id", () => {
		// Arrange
		const chat = createChat("global-chat-bubble");

		// Act
		persistChatBubbleOpenState(chat, true);
		const shouldOpen = shouldOpenChatBubbleOnInit(chat);

		// Assert
		expect(shouldOpen).toBe(true);
	});

	it("returns false when no state exists for the chat id", () => {
		// Arrange
		const chat = createChat("global-chat-bubble");

		// Act
		const shouldOpen = shouldOpenChatBubbleOnInit(chat);

		// Assert
		expect(shouldOpen).toBe(false);
	});

	it("stores chat bubble states per chat id", () => {
		// Arrange
		const firstChat = createChat("chat-first");
		const secondChat = createChat("chat-second");

		// Act
		persistChatBubbleOpenState(firstChat, true);
		persistChatBubbleOpenState(secondChat, false);

		// Assert
		expect(shouldOpenChatBubbleOnInit(firstChat)).toBe(true);
		expect(shouldOpenChatBubbleOnInit(secondChat)).toBe(false);
	});

	it("ignores malformed persisted data and safely stores new values", () => {
		// Arrange
		const chat = createChat("chat-malformed");
		window.localStorage.setItem(
			SESSION_ID_KEY.GLOBAL_CHAT_BUBBLE_OPEN_STATES,
			"this-is-not-json",
		);

		// Act
		persistChatBubbleOpenState(chat, true);

		// Assert
		expect(shouldOpenChatBubbleOnInit(chat)).toBe(true);
	});

	it("returns false when viewport is 768px or smaller even if state is open", () => {
		// Arrange
		const chat = createChat("chat-mobile");
		persistChatBubbleOpenState(chat, true);
		Object.defineProperty(window, "innerWidth", {
			value: 768,
			writable: true,
			configurable: true,
		});

		// Act
		const shouldOpen = shouldOpenChatBubbleOnInit(chat);

		// Assert
		expect(shouldOpen).toBe(false);
	});

	it("returns true when viewport is wider than 768px and state is open", () => {
		// Arrange
		const chat = createChat("chat-desktop");
		persistChatBubbleOpenState(chat, true);
		Object.defineProperty(window, "innerWidth", {
			value: 769,
			writable: true,
			configurable: true,
		});

		// Act
		const shouldOpen = shouldOpenChatBubbleOnInit(chat);

		// Assert
		expect(shouldOpen).toBe(true);
	});

	it("returns true for initialization when generic initialize state is true", () => {
		// Arrange
		const chat = createChat("chat-puzzel", { support: "puzzel" });
		persistChatBubbleInitializeState(chat, true);

		// Act
		const shouldInitialize = shouldInitializeChatBubbleOnInit(chat);

		// Assert
		expect(shouldInitialize).toBe(true);
	});

	it("returns false for initialization when generic initialize state is false", () => {
		// Arrange
		const chat = createChat("chat-not-puzzel", { support: "eneo" });
		persistChatBubbleInitializeState(chat, false);

		// Act
		const shouldInitialize = shouldInitializeChatBubbleOnInit(chat);

		// Assert
		expect(shouldInitialize).toBe(false);
	});

	it("returns false for initialization when no initialize state exists", () => {
		// Arrange
		const chat = createChat("chat-puzzel-incomplete", { support: "puzzel" });

		// Act
		const shouldInitialize = shouldInitializeChatBubbleOnInit(chat);

		// Assert
		expect(shouldInitialize).toBe(false);
	});

	it("returns false when open state is older than one hour", () => {
		// Arrange
		const chat = createChat("chat-expired");
		persistChatBubbleOpenState(chat, true);
		dateNowSpy.mockReturnValue(1_000_000 + (60 * 60 * 1000) + 1);

		// Act
		const shouldOpen = shouldOpenChatBubbleOnInit(chat);

		// Assert
		expect(shouldOpen).toBe(false);
	});

	it("returns true when open state is within one hour", () => {
		// Arrange
		const chat = createChat("chat-fresh");
		persistChatBubbleOpenState(chat, true);
		dateNowSpy.mockReturnValue(1_000_000 + (60 * 60 * 1000));

		// Act
		const shouldOpen = shouldOpenChatBubbleOnInit(chat);

		// Assert
		expect(shouldOpen).toBe(true);
	});
});
