import PuzzelAgentJoinedHandler from "./puzzelAgentJoinedHandler";

describe("PuzzelAgentJoinedHandler", () => {
	const createChatHandler = () => ({
		displayBanner: jest.fn(),
		addInfo: jest.fn(),
		enable: jest.fn(),
	});

	const infoDataManager = {
		getAgentJoinedInfoContent: () => "Joined",
		getAgentLeftInfoContent: jest.fn(),
		getUserLeftInfoContent: jest.fn(),
		getInfoContentFromType: jest.fn(),
	};

	it("hides the banner, adds joined info and enables chat when an agent joins", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelAgentJoinedHandler(chatHandler as any, infoDataManager);

		// Act
		const result = handler.handleMetaDataEvent({ senderRole: "agent", type: "participantJoinEvent" } as any);

		// Assert
		expect(result).toBe(true);
		expect(chatHandler.displayBanner).toHaveBeenCalledWith(false);
		expect(chatHandler.addInfo).toHaveBeenCalledWith("Joined");
		expect(chatHandler.enable).toHaveBeenCalledTimes(1);
	});

	it.each([
		["null data", null],
		["visitor join", { senderRole: "visitor", type: "participantJoinEvent" }],
		["agent leave", { senderRole: "agent", type: "participantLeaveEvent" }],
	])("ignores %s", (_label, data) => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelAgentJoinedHandler(chatHandler as any, infoDataManager);

		// Act
		const result = handler.handleMetaDataEvent(data as any);

		// Assert
		expect(result).toBe(false);
		expect(chatHandler.displayBanner).not.toHaveBeenCalled();
		expect(chatHandler.addInfo).not.toHaveBeenCalled();
		expect(chatHandler.enable).not.toHaveBeenCalled();
	});
});
