import PuzzelAgentLeftHandler from "./puzzelAgentLeftHandler";

describe("PuzzelAgentLeftHandler", () => {
	const createChatHandler = () => ({
		setChatInactiveData: jest.fn(),
		displayBanner: jest.fn(),
		addAgentLeftInfo: jest.fn(),
		disable: jest.fn(),
	});

	it("shows the inactive banner, adds left info and disables chat when an agent leaves", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelAgentLeftHandler(chatHandler as any);

		// Act
		const result = handler.handleMetaDataEvent({ senderRole: "agent", type: "participantLeaveEvent" } as any);

		// Assert
		expect(result).toBe(true);
		expect(chatHandler.setChatInactiveData).toHaveBeenCalledTimes(1);
		expect(chatHandler.displayBanner).toHaveBeenCalledWith(true);
		expect(chatHandler.addAgentLeftInfo).toHaveBeenCalledTimes(1);
		expect(chatHandler.disable).toHaveBeenCalledTimes(1);
	});

	it.each([
		["null data", null],
		["visitor leave", { senderRole: "visitor", type: "participantLeaveEvent" }],
		["agent join", { senderRole: "agent", type: "participantJoinEvent" }],
	])("ignores %s", (_label, data) => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelAgentLeftHandler(chatHandler as any);

		// Act
		const result = handler.handleMetaDataEvent(data as any);

		// Assert
		expect(result).toBe(false);
		expect(chatHandler.disable).not.toHaveBeenCalled();
		expect(chatHandler.displayBanner).not.toHaveBeenCalled();
	});
});
