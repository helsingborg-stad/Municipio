import Puzzel from "./puzzel";

describe("Puzzel", () => {
	it("returns false when chat is closed", async () => {
		// Arrange
		const puzzelAuthenticator = {
			authenticate: jest.fn().mockResolvedValue({ access_token: "token" }),
			getAccessToken: jest.fn(),
		};
		const puzzelAvailabilityManager = {
			isAvailable: jest.fn().mockResolvedValue(false),
		};
		const puzzelConversationManager = {
			stopListening: jest.fn(),
			getCurrentConversation: jest.fn(),
			getCurrentMetaData: jest.fn(),
			startConversation: jest.fn(),
			sendUserJoinEventToConversation: jest.fn(),
			listenForMetaData: jest.fn(),
			listenForConversation: jest.fn(),
			listenForConversationStatus: jest.fn(),
			stopListeningForConversationStatus: jest.fn(),
			resetConversationId: jest.fn(),
			getConversationStatus: jest.fn(),
		};
		const conversationStorageManager = {
			getLocalStorage: jest.fn().mockReturnValue(null),
			setLocalStorage: jest.fn(),
			clearLocalStorage: jest.fn(),
		};
		const typingIndicatorHandler = {
			clearTypingState: jest.fn(),
			handleMetaDataEvent: jest.fn(),
		};
		const incomingMessageHandler = {
			handleConversationEvent: jest.fn(),
		};
		const outgoingChatHandler = {
			register: jest.fn(),
		};
		const agentLeftHandler = {
			handleMetaDataEvent: jest.fn(),
		};
		const agentJoinedHandler = {
			handleMetaDataEvent: jest.fn(),
		};
		const chatHistoryHandler = {
			load: jest.fn(),
		};
		const chatStatusHandler = {
			getStatus: jest.fn(),
			getCurrentStatus: jest.fn(),
			handleStatusEvent: jest.fn(),
		};
		const chatHandler = {
			setDefaultPersistedState: jest.fn(),
			addMessage: jest.fn(),
			addInfo: jest.fn(),
			addInfoFromType: jest.fn(),
			addAgentJoinedInfo: jest.fn(),
			addAgentLeftInfo: jest.fn(),
			disable: jest.fn(),
			enable: jest.fn(),
			addPendingMessage: jest.fn(),
			deleteMessage: jest.fn(),
			onMessageAdded: jest.fn(),
			onTyping: jest.fn(),
			displayBanner: jest.fn(),
			setQueueBannerData: jest.fn(),
			setChatInactiveData: jest.fn(),
		};
		const actionFactory = {
			addMinimize: jest.fn(),
			addClear: jest.fn(),
		};

		const puzzel = new Puzzel(
			puzzelAuthenticator as any,
			puzzelAvailabilityManager as any,
			puzzelConversationManager as any,
			conversationStorageManager as any,
			typingIndicatorHandler as any,
			incomingMessageHandler as any,
			outgoingChatHandler as any,
			agentLeftHandler as any,
			agentJoinedHandler as any,
			chatHistoryHandler as any,
			chatStatusHandler as any,
			chatHandler as any,
			actionFactory as any,
		);

		// Act
		const initialized = await puzzel.init();

		// Assert
		expect(initialized).toBe(false);
		expect(actionFactory.addMinimize).not.toHaveBeenCalled();
		expect(puzzelAvailabilityManager.isAvailable).toHaveBeenCalledTimes(1);
		expect(puzzelConversationManager.startConversation).not.toHaveBeenCalled();
		expect(outgoingChatHandler.register).not.toHaveBeenCalled();
	});
});