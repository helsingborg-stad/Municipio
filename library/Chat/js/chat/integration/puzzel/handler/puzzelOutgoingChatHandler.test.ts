import PuzzelOutgoingChatHandler from "./puzzelOutgoingChatHandler";

describe("PuzzelOutgoingChatHandler", () => {
	const createDependencies = () => {
		let messageListener: (message: any) => void = () => {};
		let typingListener: () => void = () => {};
		const chatHandler = {
			onMessageAdded: jest.fn((listener) => { messageListener = listener; }),
			onTyping: jest.fn((listener) => { typingListener = listener; }),
		};
		const conversationManager = {
			sendUserMessage: jest.fn().mockResolvedValue(undefined),
			sendUserTypingEvent: jest.fn().mockResolvedValue(undefined),
		};
		return {
			chatHandler,
			conversationManager,
			emitMessage: (message: any) => messageListener(message),
			emitTyping: () => typingListener(),
		};
	};

	const createMessage = (content: string, isReply = false) => ({
		getIsReply: () => isReply,
		getContent: () => content,
	});

	it("sends user messages added to the chat", () => {
		// Arrange
		const { chatHandler, conversationManager, emitMessage } = createDependencies();
		new PuzzelOutgoingChatHandler(chatHandler as any, conversationManager as any).register();

		// Act
		emitMessage(createMessage("Hello"));

		// Assert
		expect(conversationManager.sendUserMessage).toHaveBeenCalledWith("Hello");
	});

	it.each([
		["replies", createMessage("Agent reply", true)],
		["empty messages", createMessage("")],
		["whitespace messages", createMessage("   ")],
	])("does not send %s", (_label, message) => {
		// Arrange
		const { chatHandler, conversationManager, emitMessage } = createDependencies();
		new PuzzelOutgoingChatHandler(chatHandler as any, conversationManager as any).register();

		// Act
		emitMessage(message);

		// Assert
		expect(conversationManager.sendUserMessage).not.toHaveBeenCalled();
	});

	it("sends a typing event when the user types", () => {
		// Arrange
		const { chatHandler, conversationManager, emitTyping } = createDependencies();
		new PuzzelOutgoingChatHandler(chatHandler as any, conversationManager as any).register();

		// Act
		emitTyping();

		// Assert
		expect(conversationManager.sendUserTypingEvent).toHaveBeenCalledTimes(1);
	});

	it("registers listeners only once", () => {
		// Arrange
		const { chatHandler, conversationManager } = createDependencies();
		const handler = new PuzzelOutgoingChatHandler(chatHandler as any, conversationManager as any);

		// Act
		handler.register();
		handler.register();

		// Assert
		expect(chatHandler.onMessageAdded).toHaveBeenCalledTimes(1);
		expect(chatHandler.onTyping).toHaveBeenCalledTimes(1);
	});
});
