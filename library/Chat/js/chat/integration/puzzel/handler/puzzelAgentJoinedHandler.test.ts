import PuzzelAgentJoinedHandler from "./puzzelAgentJoinedHandler";

describe("PuzzelAgentJoinedHandler", () => {
	const createChatHandler = () => ({
		displayBanner: jest.fn(),
		addAgentJoinedInfo: jest.fn(),
		enable: jest.fn(),
	});

	it("hides the banner, adds joined info and enables chat when an agent joins", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelAgentJoinedHandler(chatHandler as any);

		// Act
		const result = handler.handleMetaDataEvent({ senderRole: "agent", type: "participantJoinEvent" } as any);

		// Assert
		expect(result).toBe(true);
		expect(chatHandler.displayBanner).toHaveBeenCalledWith(false);
		expect(chatHandler.addAgentJoinedInfo).toHaveBeenCalledTimes(1);
		expect(chatHandler.enable).toHaveBeenCalledTimes(1);
	});

	it.each([
		["null data", null],
		["visitor join", { senderRole: "visitor", type: "participantJoinEvent" }],
		["agent leave", { senderRole: "agent", type: "participantLeaveEvent" }],
	])("ignores %s", (_label, data) => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelAgentJoinedHandler(chatHandler as any);

		// Act
		const result = handler.handleMetaDataEvent(data as any);

		// Assert
		expect(result).toBe(false);
		expect(chatHandler.displayBanner).not.toHaveBeenCalled();
		expect(chatHandler.addAgentJoinedInfo).not.toHaveBeenCalled();
		expect(chatHandler.enable).not.toHaveBeenCalled();
	});
});
