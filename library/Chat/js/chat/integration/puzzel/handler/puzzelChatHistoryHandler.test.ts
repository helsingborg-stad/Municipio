import PuzzelChatHistoryHandler from "./puzzelChatHistoryHandler";
import ChatHandler from "./chatHandler";
import PuzzelChatStatusHandler from "./puzzelChatStatusHandler";
import PuzzelAgentJoinedHandler from "./puzzelAgentJoinedHandler";
import { PUZZEL_CHAT_STATUS } from "../enums";

describe("PuzzelChatHistoryHandler", () => {
	const createChatHandler = () => {
		const calls: Array<[string, ...unknown[]]> = [];
		return {
			calls,
			addMessage: jest.fn((...args: unknown[]) => calls.push(["addMessage", ...args])),
			addInfoFromType: jest.fn((...args: unknown[]) => calls.push(["addInfoFromType", ...args])),
			endSession: jest.fn(),
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

	it("filters visitor joins and typing events while retaining participant leaves", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelChatHistoryHandler(chatHandler as any);
		const metaDataEvents = [
			{ type: "participantJoinEvent", senderRole: "visitor", createdAt: "2024-01-01T10:00:00Z" },
			{ type: "typingEvent", senderRole: "agent", createdAt: "2024-01-01T10:01:00Z" },
			{ type: "typingEvent", senderRole: "visitor", createdAt: "2024-01-01T10:01:30Z" },
			{ type: "participantLeaveEvent", senderRole: "agent", createdAt: "2024-01-01T10:02:00Z" },
			{ type: "participantLeaveEvent", senderRole: "visitor", createdAt: "2024-01-01T10:03:00Z" },
		];

		// Act
		handler.load([], metaDataEvents as any);

		// Assert
		expect(chatHandler.calls).toEqual([
			["addInfoFromType", "participantLeaveEvent", "agent"],
			["addInfoFromType", "participantLeaveEvent", "visitor"],
		]);
		expect(chatHandler.endSession).toHaveBeenCalledTimes(1);
	});

	it("restores the user-left message after the agent joined message", () => {
		// Arrange
		const chat = { addInfo: jest.fn(), addMessage: jest.fn(), disable: jest.fn(), enable: jest.fn() };
		const handler = new PuzzelChatHistoryHandler(new ChatHandler(chat));
		const metaDataEvents = [
			{ type: "participantLeaveEvent", senderRole: "visitor", createdAt: "2024-01-01T10:03:00Z" },
			{ type: "participantJoinEvent", senderRole: "visitor", createdAt: "2024-01-01T10:00:00Z" },
			{ type: "participantJoinEvent", senderRole: "agent", createdAt: "2024-01-01T10:01:00Z" },
			{ type: "typingEvent", senderRole: "visitor", createdAt: "2024-01-01T10:02:00Z" },
		];

		// Act
		handler.load([], metaDataEvents as any);

		// Assert
		expect(chat.addInfo.mock.calls).toEqual([
			["Agent has joined the chat"],
			["User has left the chat"],
		]);
		expect(chat.disable).toHaveBeenCalledTimes(1);
	});

	it.each(["agent", "visitor"])("keeps chat disabled after a restored %s leave and later join or status", (role) => {
		// Arrange
		const chat = { addInfo: jest.fn(), addMessage: jest.fn(), disable: jest.fn(), enable: jest.fn(), hideBanner: jest.fn(), showBanner: jest.fn(), setBannerContent: jest.fn() };
		const chatHandler = new ChatHandler(chat);
		const handler = new PuzzelChatHistoryHandler(chatHandler);
		const statusHandler = new PuzzelChatStatusHandler(chatHandler, {} as any);
		const joinedHandler = new PuzzelAgentJoinedHandler(chatHandler);

		// Act
		handler.load([], [{ type: "participantLeaveEvent", senderRole: role, createdAt: "2024-01-01T10:00:00Z" }] as any);
		statusHandler.handleStatusEvent({ status: PUZZEL_CHAT_STATUS.CONNECTED } as any);
		statusHandler.handleStatusEvent({ status: PUZZEL_CHAT_STATUS.IN_QUEUE, queuePosition: 1 } as any);
		joinedHandler.handleMetaDataEvent({ type: "participantJoinEvent", senderRole: "agent" } as any);

		// Assert
		expect(chat.disable).toHaveBeenCalledTimes(1);
		expect(chat.enable).not.toHaveBeenCalled();
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
