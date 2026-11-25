import PuzzelAgentLeftHandler from "./puzzelAgentLeftHandler";

describe("PuzzelAgentLeftHandler", () => {
	const createChatHandler = () => ({
		setBannerContent: jest.fn(),
		displayBanner: jest.fn(),
		addInfo: jest.fn(),
		endSession: jest.fn(),
	});

	const bannerDataManager = {
		getQueueBannerContent: jest.fn(),
		getChatInactiveBannerContent: () => "Inactive",
		getErrorBannerContent: jest.fn(),
	};

	const infoDataManager = {
		getAgentJoinedInfoContent: jest.fn(),
		getAgentLeftInfoContent: () => "Left",
		getUserLeftInfoContent: jest.fn(),
		getInfoContentFromType: jest.fn(),
	};

	it("shows the inactive banner, adds left info and disables chat when an agent leaves", () => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelAgentLeftHandler(chatHandler as any, bannerDataManager, infoDataManager);

		// Act
		const result = handler.handleMetaDataEvent({ senderRole: "agent", type: "participantLeaveEvent" } as any);

		// Assert
		expect(result).toBe(true);
		expect(chatHandler.setBannerContent).toHaveBeenCalledWith("Inactive");
		expect(chatHandler.displayBanner).toHaveBeenCalledWith(true);
		expect(chatHandler.addInfo).toHaveBeenCalledWith("Left");
		expect(chatHandler.endSession).toHaveBeenCalledTimes(1);
	});

	it.each([
		["null data", null],
		["visitor leave", { senderRole: "visitor", type: "participantLeaveEvent" }],
		["agent join", { senderRole: "agent", type: "participantJoinEvent" }],
	])("ignores %s", (_label, data) => {
		// Arrange
		const chatHandler = createChatHandler();
		const handler = new PuzzelAgentLeftHandler(chatHandler as any, bannerDataManager, infoDataManager);

		// Act
		const result = handler.handleMetaDataEvent(data as any);

		// Assert
		expect(result).toBe(false);
		expect(chatHandler.endSession).not.toHaveBeenCalled();
		expect(chatHandler.displayBanner).not.toHaveBeenCalled();
	});
});
