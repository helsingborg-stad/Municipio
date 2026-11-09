import Puzzel from "./puzzel";

describe("Puzzel", () => {
	it("sets title and avatar on successful initialization", async () => {
		// Arrange
		const puzzelAuthenticator = {
			authenticate: jest.fn().mockResolvedValue({ access_token: "token" }),
			getAccessToken: jest.fn(),
		};
		const puzzelAvailabilityManager = {
			isAvailable: jest.fn().mockResolvedValue(true),
		};
		const puzzelConversationManager = {
			stopListening: jest.fn(),
			getCurrentConversation: jest.fn().mockResolvedValue([]),
			getCurrentMetaData: jest.fn().mockResolvedValue([]),
			startConversation: jest.fn().mockResolvedValue({ conversationId: "conversation-id" }),
			sendUserJoinEventToConversation: jest.fn().mockResolvedValue(undefined),
			listenForMetaData: jest.fn().mockResolvedValue(undefined),
			listenForConversation: jest.fn().mockResolvedValue(undefined),
			listenForConversationStatus: jest.fn().mockResolvedValue(undefined),
			stopListeningForConversationStatus: jest.fn(),
			resetConversationId: jest.fn(),
			getConversationStatus: jest.fn().mockResolvedValue([{ status: "IN_QUEUE" }]),
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
			getStatus: jest.fn().mockReturnValue({ status: "IN_QUEUE" }),
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
		const uiManager = {
			setTitle: jest.fn(),
			setAvatar: jest.fn(),
			clearActions: jest.fn(),
			addMinimizeAction: jest.fn(),
			addClearAction: jest.fn(),
			addCloseAction: jest.fn(),
		};
		const puzzelConfig = {
			getName: jest.fn().mockReturnValue("Puzzel Assistant"),
			getAvatar: jest.fn().mockReturnValue("https://example.com/puzzel.png"),
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
			uiManager as any,
			puzzelConfig as any,
		);

		// Act
		const initialized = await puzzel.init();

		// Assert
		expect(initialized).toBe(puzzel);
		expect(uiManager.setTitle).toHaveBeenCalledWith("Puzzel Assistant");
		expect(uiManager.setAvatar).toHaveBeenCalledWith("https://example.com/puzzel.png");
		expect(uiManager.clearActions).toHaveBeenCalledTimes(1);
		expect(uiManager.addMinimizeAction).toHaveBeenCalledTimes(1);
		expect(uiManager.addCloseAction).toHaveBeenCalledTimes(1);
	});

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
		const uiManager = {
			setTitle: jest.fn(),
			setAvatar: jest.fn(),
			clearActions: jest.fn(),
			addMinimizeAction: jest.fn(),
			addClearAction: jest.fn(),
			addCloseAction: jest.fn(),
		};
		const puzzelConfig = {
			getName: jest.fn().mockReturnValue("Puzzel Assistant"),
			getAvatar: jest.fn().mockReturnValue(""),
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
			uiManager as any,
			puzzelConfig as any,
		);

		// Act
		const initialized = await puzzel.init();

		// Assert
		expect(initialized).toBe(false);
		expect(uiManager.clearActions).not.toHaveBeenCalled();
		expect(uiManager.addMinimizeAction).not.toHaveBeenCalled();
		expect(puzzelAvailabilityManager.isAvailable).toHaveBeenCalledTimes(1);
		expect(puzzelConversationManager.startConversation).not.toHaveBeenCalled();
		expect(outgoingChatHandler.register).not.toHaveBeenCalled();
	});
});