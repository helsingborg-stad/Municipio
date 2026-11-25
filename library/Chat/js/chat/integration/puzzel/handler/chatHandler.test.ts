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

	it("forwards info content to the chat", () => {
		// Arrange
		const chat = createChat();
		const handler = new ChatHandler(chat);

		// Act
		handler.addInfo("Agent has joined the chat");

		// Assert
		expect(chat.addInfo).toHaveBeenCalledWith("Agent has joined the chat");
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
