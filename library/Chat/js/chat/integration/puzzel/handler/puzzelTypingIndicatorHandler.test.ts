import PuzzelTypingIndicatorHandler from "./puzzelTypingIndicatorHandler";

describe("PuzzelTypingIndicatorHandler", () => {
	const typingEvent = { senderRole: "agent", type: "typingEvent" } as any;

	const createChatHandler = () => {
		const pendingMessage = { id: "pending" };
		return {
			pendingMessage,
			addPendingMessage: jest.fn().mockReturnValue(pendingMessage),
			deleteMessage: jest.fn(),
		};
	};

	beforeEach(() => {
		jest.useFakeTimers();
	});

	afterEach(() => {
		jest.clearAllTimers();
		jest.useRealTimers();
	});

	it.each([
		["null data", null],
		["visitor typing", { senderRole: "visitor", type: "typingEvent" }],
		["agent non-typing event", { senderRole: "agent", type: "participantJoinEvent" }],
	])("ignores %s", (_label, data) => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelTypingIndicatorHandler(chatHandler as any);

		// Act
		const result = handler.handleMetaDataEvent(data as any);

		// Assert
		expect(result).toBe(false);
		expect(chatHandler.addPendingMessage).not.toHaveBeenCalled();
	});

	it("adds a single pending message for repeated agent typing events", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelTypingIndicatorHandler(chatHandler as any);

		// Act
		const first = handler.handleMetaDataEvent(typingEvent);
		const second = handler.handleMetaDataEvent(typingEvent);

		// Assert
		expect(first).toBe(true);
		expect(second).toBe(true);
		expect(chatHandler.addPendingMessage).toHaveBeenCalledTimes(1);
	});

	it("removes the pending message after 7 seconds without typing events", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelTypingIndicatorHandler(chatHandler as any);
		handler.handleMetaDataEvent(typingEvent);

		// Act
		jest.advanceTimersByTime(6999);
		const deletedBeforeTimeout = chatHandler.deleteMessage.mock.calls.length;
		jest.advanceTimersByTime(1);

		// Assert
		expect(deletedBeforeTimeout).toBe(0);
		expect(chatHandler.deleteMessage).toHaveBeenCalledWith(chatHandler.pendingMessage);
	});

	it("restarts the timeout when another typing event arrives", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelTypingIndicatorHandler(chatHandler as any);
		handler.handleMetaDataEvent(typingEvent);
		jest.advanceTimersByTime(5000);

		// Act
		handler.handleMetaDataEvent(typingEvent);
		jest.advanceTimersByTime(5000);
		const deletedAfterRestart = chatHandler.deleteMessage.mock.calls.length;
		jest.advanceTimersByTime(2000);

		// Assert
		expect(deletedAfterRestart).toBe(0);
		expect(chatHandler.deleteMessage).toHaveBeenCalledTimes(1);
	});

	it("adds a new pending message after the previous one timed out", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelTypingIndicatorHandler(chatHandler as any);
		handler.handleMetaDataEvent(typingEvent);
		jest.advanceTimersByTime(7000);

		// Act
		handler.handleMetaDataEvent(typingEvent);

		// Assert
		expect(chatHandler.addPendingMessage).toHaveBeenCalledTimes(2);
	});

	it("clears the pending message and cancels the timeout on clearTypingState", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelTypingIndicatorHandler(chatHandler as any);
		handler.handleMetaDataEvent(typingEvent);

		// Act
		handler.clearTypingState();
		jest.advanceTimersByTime(10000);

		// Assert
		expect(chatHandler.deleteMessage).toHaveBeenCalledTimes(1);
		expect(chatHandler.deleteMessage).toHaveBeenCalledWith(chatHandler.pendingMessage);
	});

	it("does nothing on clearTypingState when not typing", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelTypingIndicatorHandler(chatHandler as any);

		// Act
		handler.clearTypingState();

		// Assert
		expect(chatHandler.deleteMessage).not.toHaveBeenCalled();
	});
});
