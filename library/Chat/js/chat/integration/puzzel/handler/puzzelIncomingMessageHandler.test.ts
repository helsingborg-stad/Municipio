import PuzzelIncomingMessageHandler from "./puzzelIncomingMessageHandler";

describe("PuzzelIncomingMessageHandler", () => {
	const createDependencies = () => ({
		chatHandler: { addMessage: jest.fn() },
		typingIndicatorHandler: { clearTypingState: jest.fn() },
	});

	it("clears typing state and adds agent chat messages", () => {
		// Arrange
		const { chatHandler, typingIndicatorHandler } = createDependencies();
		const handler = new PuzzelIncomingMessageHandler(chatHandler as any, typingIndicatorHandler as any);

		// Act
		handler.handleConversationEvent({ type: "chat", senderRole: "agent", message: "Hello" } as any);

		// Assert
		expect(typingIndicatorHandler.clearTypingState).toHaveBeenCalledTimes(1);
		expect(chatHandler.addMessage).toHaveBeenCalledWith("Hello", true);
	});

	it.each([
		["null data", null],
		["non-chat events", { type: "participantJoinEvent", senderRole: "agent", message: "x" }],
		["visitor messages", { type: "chat", senderRole: "visitor", message: "x" }],
		["missing message", { type: "chat", senderRole: "agent" }],
		["whitespace message", { type: "chat", senderRole: "agent", message: "   " }],
	])("ignores %s", (_label, data) => {
		// Arrange
		const { chatHandler, typingIndicatorHandler } = createDependencies();
		const handler = new PuzzelIncomingMessageHandler(chatHandler as any, typingIndicatorHandler as any);

		// Act
		handler.handleConversationEvent(data as any);

		// Assert
		expect(chatHandler.addMessage).not.toHaveBeenCalled();
		expect(typingIndicatorHandler.clearTypingState).not.toHaveBeenCalled();
	});
});
