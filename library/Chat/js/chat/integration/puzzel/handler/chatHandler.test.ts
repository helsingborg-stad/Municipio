import ChatHandler from "./chatHandler";

describe("ChatHandler", () => {
	const createChat = () => {
		const element = document.createElement("div");
		return {
			element,
			setDefaultShouldPersist: jest.fn(),
			addMessage: jest.fn(),
			addInfo: jest.fn(),
			showBanner: jest.fn(),
			hideBanner: jest.fn(),
			setBannerContent: jest.fn(),
			disable: jest.fn(),
			enable: jest.fn(),
			addPendingMessage: jest.fn().mockReturnValue({ id: "pending" }),
			deleteMessage: jest.fn(),
			getElement: jest.fn().mockReturnValue(element),
		};
	};

	const setLocale = (value: unknown) => {
		Object.defineProperty(globalThis, "municipioChatLocale", { value, configurable: true, writable: true });
	};

	afterEach(() => {
		delete (globalThis as any).municipioChatLocale;
	});

	it("forwards banner content to the chat", () => {
		// Arrange
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.setBannerContent("Queue position: 2");

		// Assert
		expect(chat.setBannerContent).toHaveBeenCalledWith("Queue position: 2");
	});

	it("shows or hides the banner", () => {
		// Arrange
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.displayBanner(true);
		handler.displayBanner(false);

		// Assert
		expect(chat.showBanner).toHaveBeenCalledTimes(1);
		expect(chat.hideBanner).toHaveBeenCalledTimes(1);
	});

	it("uses fallback strings when no locale is defined", () => {
		// Arrange
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.addAgentJoinedInfo();
		handler.addAgentLeftInfo();

		// Assert
		expect(chat.addInfo.mock.calls).toEqual([
			["Agent has joined the chat"],
			["Agent has left the chat"],
		]);
	});

	it("uses localized strings when available", () => {
		// Arrange
		setLocale({
			agentJoinedChat: "Anslöt",
			userLeftChat: "Lämnade användaren",
			agentLeftChat: "Lämnade",
		});
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.addAgentJoinedInfo();
		handler.addAgentLeftInfo();

		// Assert
		expect(chat.addInfo.mock.calls).toEqual([["Anslöt"], ["Lämnade"]]);
	});

	it("falls back when a localized string is empty or not a string", () => {
		// Arrange
		setLocale({ agentJoinedChat: 42 });
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.addAgentJoinedInfo();

		// Assert
		expect(chat.addInfo).toHaveBeenCalledWith("Agent has joined the chat");
	});

	it.each([
		["participantJoinEvent", "agent", "Agent has joined the chat"],
		["participantLeaveEvent", "agent", "Agent has left the chat"],
		["participantLeaveEvent", "visitor", "User has left the chat"],
		["participantLeaveEvent", undefined, "Agent has left the chat"],
	])("adds info for %s from %s", (type, role, expected) => {
		// Arrange
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.addInfoFromType(type, role);

		// Assert
		expect(chat.addInfo).toHaveBeenCalledWith(expected);
	});

	it.each([
		["participantJoinEvent", "visitor"],
		["typingEvent", "agent"],
	])("ignores %s from %s", (type, role) => {
		// Arrange
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.addInfoFromType(type, role);

		// Assert
		expect(chat.addInfo).not.toHaveBeenCalled();
	});

	it("keeps an ended session disabled until a new session starts", () => {
		// Arrange
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.endSession();
		handler.enable();

		// Assert
		expect(chat.disable).toHaveBeenCalledTimes(1);
		expect(chat.enable).not.toHaveBeenCalled();

		// Act
		handler.startNewSession();
		handler.enable();

		// Assert
		expect(chat.enable).toHaveBeenCalledTimes(1);
	});

	it("uses the localized user-left message for visitor leave events", () => {
		// Arrange
		setLocale({ userLeftChat: "Användaren lämnade chatten" });
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.addInfoFromType("participantLeaveEvent", "visitor");

		// Assert
		expect(chat.addInfo).toHaveBeenCalledWith("Användaren lämnade chatten");
	});

	it("forwards the detail of chat:message-added events to the listener", () => {
		// Arrange
		const chat = createChat();
		const handler = new ChatHandler(chat);
		const listener = jest.fn();
		const detail = { content: "hi" };
		handler.onMessageAdded(listener);

		// Act
		chat.element.dispatchEvent(new CustomEvent("chat:message-added", { detail }));

		// Assert
		expect(listener).toHaveBeenCalledWith(detail);
	});

	it("calls the typing listener on chat:typing events", () => {
		// Arrange
		const chat = createChat();
		const handler = new ChatHandler(chat);
		const listener = jest.fn();
		handler.onTyping(listener);

		// Act
		chat.element.dispatchEvent(new Event("chat:typing"));

		// Assert
		expect(listener).toHaveBeenCalledTimes(1);
	});
});
