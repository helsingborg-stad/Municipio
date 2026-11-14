import PuzzelChatHistoryHandler from "./puzzelChatHistoryHandler";

describe("PuzzelChatHistoryHandler", () => {
	const createChatHandler = () => {
		const calls: Array<[string, ...unknown[]]> = [];
		return {
			calls,
			addMessage: jest.fn((...args: unknown[]) => calls.push(["addMessage", ...args])),
			addInfoFromType: jest.fn((...args: unknown[]) => calls.push(["addInfoFromType", ...args])),
		};
	};

	it("merges conversation and metadata events in chronological order", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelChatHistoryHandler(chatHandler as any);
		const conversationEvents = [
			{ type: "chat", senderRole: "visitor", message: "Hi", createdAt: "2024-01-01T10:00:00Z" },
			{ type: "chat", senderRole: "agent", message: "Hello", createdAt: "2024-01-01T10:02:00Z" },
		];
		const metaDataEvents = [
			{ type: "participantJoinEvent", senderRole: "agent", createdAt: "2024-01-01T10:01:00Z" },
		];

		// Act
		handler.load(conversationEvents as any, metaDataEvents as any);

		// Assert
		expect(chatHandler.calls).toEqual([
			["addMessage", "Hi", false],
			["addInfoFromType", "participantJoinEvent", "agent"],
			["addMessage", "Hello", true],
		]);
	});

	it("filters out visitor metadata and typing events", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelChatHistoryHandler(chatHandler as any);
		const metaDataEvents = [
			{ type: "participantJoinEvent", senderRole: "visitor", createdAt: "2024-01-01T10:00:00Z" },
			{ type: "typingEvent", senderRole: "agent", createdAt: "2024-01-01T10:01:00Z" },
			{ type: "participantLeaveEvent", senderRole: "agent", createdAt: "2024-01-01T10:02:00Z" },
		];

		// Act
		handler.load([], metaDataEvents as any);

		// Assert
		expect(chatHandler.calls).toEqual([["addInfoFromType", "participantLeaveEvent", "agent"]]);
	});

	it("uses an empty string for chat events without a string message", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelChatHistoryHandler(chatHandler as any);

		// Act
		handler.load([{ type: "chat", senderRole: "agent", message: null, createdAt: "2024-01-01T10:00:00Z" }] as any, []);

		// Assert
		expect(chatHandler.addMessage).toHaveBeenCalledWith("", true);
	});

	it("passes non-chat conversation events to addInfoFromType", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelChatHistoryHandler(chatHandler as any);

		// Act
		handler.load([{ type: "participantLeaveEvent", senderRole: "agent", createdAt: "2024-01-01T10:00:00Z" }] as any, []);

		// Assert
		expect(chatHandler.addMessage).not.toHaveBeenCalled();
		expect(chatHandler.addInfoFromType).toHaveBeenCalledWith("participantLeaveEvent", "agent");
	});
});
