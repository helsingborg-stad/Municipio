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
		handler.setQueueBannerData(3);
		handler.setChatInactiveData();
		handler.setErrorData();
		handler.addAgentJoinedInfo();
		handler.addAgentLeftInfo();

		// Assert
		expect(chat.setBannerContent.mock.calls).toEqual([
			["Queue position: 3"],
			["The agent has left and the chat is now inactive"],
			["Something went wrong, please try again later"],
		]);
		expect(chat.addInfo.mock.calls).toEqual([
			["Agent has joined the chat"],
			["Agent has left the chat"],
		]);
	});

	it("uses localized strings when available", () => {
		// Arrange
		setLocale({
			queuePosition: "Köplats",
			agentLeftChatInactive: "Inaktiv",
			agentJoinedChat: "Anslöt",
			agentLeftChat: "Lämnade",
			errorMessage: "Något gick fel",
		});
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.setQueueBannerData(2);
		handler.setChatInactiveData();
		handler.setErrorData();
		handler.addAgentJoinedInfo();
		handler.addAgentLeftInfo();

		// Assert
		expect(chat.setBannerContent.mock.calls).toEqual([["Köplats: 2"], ["Inaktiv"], ["Något gick fel"]]);
		expect(chat.addInfo.mock.calls).toEqual([["Anslöt"], ["Lämnade"]]);
	});

	it("falls back when a localized string is empty or not a string", () => {
		// Arrange
		setLocale({ queuePosition: "", agentJoinedChat: 42 });
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.setQueueBannerData(1);
		handler.addAgentJoinedInfo();

		// Assert
		expect(chat.setBannerContent).toHaveBeenCalledWith("Queue position: 1");
		expect(chat.addInfo).toHaveBeenCalledWith("Agent has joined the chat");
	});

	it.each([
		["participantJoinEvent", "agent", "Agent has joined the chat"],
		["participantLeaveEvent", "agent", "Agent has left the chat"],
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
		["participantLeaveEvent", "visitor"],
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
