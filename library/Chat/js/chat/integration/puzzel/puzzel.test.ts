import Puzzel from "./puzzel";

type Stored = { conversationData?: unknown; authenticationData?: unknown } | null;

const storedConversation = {
	conversationData: { conversationId: "stored-conversation" },
	authenticationData: { access_token: "stored-token" },
};

const createPuzzel = ({
	stored = null as Stored,
	isAvailable = jest.fn().mockResolvedValue(true),
	authenticate = jest.fn().mockResolvedValue({ access_token: "new-token" }),
	getConversationStatus = jest.fn().mockResolvedValue([{ status: "IN_QUEUE" }]),
} = {}) => {
	let storage: Stored = stored;

	const deps = {
		puzzelAuthenticator: { authenticate, getAccessToken: jest.fn() },
		puzzelAvailabilityManager: { isAvailable },
		puzzelConversationManager: {
			stopListening: jest.fn(),
			getCurrentConversation: jest.fn().mockResolvedValue(["message"]),
			getCurrentMetaData: jest.fn().mockResolvedValue(["meta"]),
			startConversation: jest.fn().mockResolvedValue({ conversationId: "new-conversation" }),
			sendUserJoinEventToConversation: jest.fn().mockResolvedValue(undefined),
			listenForMetaData: jest.fn().mockResolvedValue(undefined),
			listenForConversation: jest.fn().mockResolvedValue(undefined),
			listenForConversationStatus: jest.fn().mockResolvedValue(undefined),
			stopListeningForConversationStatus: jest.fn(),
			resetConversationId: jest.fn(),
			getConversationStatus,
		},
		conversationStorageManager: {
			getLocalStorage: jest.fn(() => storage),
			setLocalStorage: jest.fn((conversationData, authenticationData) => {
				storage = { conversationData, authenticationData };
			}),
			clearLocalStorage: jest.fn(() => {
				storage = null;
			}),
			updateLastActivity: jest.fn(),
		},
		typingIndicatorHandler: { clearTypingState: jest.fn(), handleMetaDataEvent: jest.fn() },
		incomingMessageHandler: { handleConversationEvent: jest.fn() },
		outgoingChatHandler: { register: jest.fn() },
		agentLeftHandler: { handleMetaDataEvent: jest.fn() },
		agentJoinedHandler: { handleMetaDataEvent: jest.fn() },
		chatHistoryHandler: { load: jest.fn() },
		chatStatusHandler: {
			getStatus: jest.fn((statuses) => (statuses?.length ? statuses[statuses.length - 1] : null)),
			getCurrentStatus: jest.fn(),
			handleStatusEvent: jest.fn(),
		},
		chatHandler: {
			setDefaultPersistedState: jest.fn(),
			setErrorData: jest.fn(),
			displayBanner: jest.fn(),
			disable: jest.fn(),
			enable: jest.fn(),
		},
		uiManager: {
			setTitle: jest.fn(),
			setAvatar: jest.fn(),
			clearActions: jest.fn(),
			addMinimizeAction: jest.fn(),
			addClearAction: jest.fn(),
			addCloseAction: jest.fn(),
		},
		puzzelConfig: {
			getName: jest.fn().mockReturnValue("Puzzel Assistant"),
			getAvatar: jest.fn().mockReturnValue("https://example.com/puzzel.png"),
		},
	};

	const puzzel = new Puzzel(
		deps.puzzelAuthenticator as any,
		deps.puzzelAvailabilityManager as any,
		deps.puzzelConversationManager as any,
		deps.conversationStorageManager as any,
		deps.typingIndicatorHandler as any,
		deps.incomingMessageHandler as any,
		deps.outgoingChatHandler as any,
		deps.agentLeftHandler as any,
		deps.agentJoinedHandler as any,
		deps.chatHistoryHandler as any,
		deps.chatStatusHandler as any,
		deps.chatHandler as any,
		deps.uiManager as any,
		deps.puzzelConfig as any,
	);

	return { puzzel, ...deps, getStorage: () => storage };
};

describe("Puzzel", () => {
	beforeEach(() => {
		jest.spyOn(console, "error").mockImplementation(() => {});
	});

	afterEach(() => {
		jest.restoreAllMocks();
	});

	describe("without an ongoing conversation", () => {
		it("starts a new conversation and sets up the chat", async () => {
			const { puzzel, uiManager, puzzelConversationManager, outgoingChatHandler, getStorage } = createPuzzel();

			const initialized = await puzzel.init();

			expect(initialized).toBe(puzzel);
			expect(puzzelConversationManager.startConversation).toHaveBeenCalledTimes(1);
			expect(puzzelConversationManager.sendUserJoinEventToConversation).toHaveBeenCalledTimes(1);
			expect(getStorage()).toEqual({
				conversationData: { conversationId: "new-conversation" },
				authenticationData: { access_token: "new-token" },
			});
			expect(outgoingChatHandler.register).toHaveBeenCalledTimes(1);
			expect(uiManager.setTitle).toHaveBeenCalledWith("Puzzel Assistant");
			expect(uiManager.setAvatar).toHaveBeenCalledWith("https://example.com/puzzel.png");
			expect(uiManager.clearActions).toHaveBeenCalledTimes(1);
			expect(uiManager.addMinimizeAction).toHaveBeenCalledTimes(1);
			expect(uiManager.addCloseAction).toHaveBeenCalledTimes(1);
		});

		it("returns false when the chat is closed, so the chat can be removed", async () => {
			const { puzzel, uiManager, puzzelAvailabilityManager, puzzelConversationManager, outgoingChatHandler, chatHandler } =
				createPuzzel({ isAvailable: jest.fn().mockResolvedValue(false) });

			const initialized = await puzzel.init();

			expect(initialized).toBe(false);
			expect(puzzelAvailabilityManager.isAvailable).toHaveBeenCalledTimes(1);
			expect(puzzelConversationManager.startConversation).not.toHaveBeenCalled();
			expect(outgoingChatHandler.register).not.toHaveBeenCalled();
			expect(uiManager.addMinimizeAction).not.toHaveBeenCalled();
			expect(chatHandler.setErrorData).not.toHaveBeenCalled();
		});

		it("returns false when authentication throws", async () => {
			const { puzzel, puzzelAvailabilityManager } = createPuzzel({
				authenticate: jest.fn().mockRejectedValue(new Error("Authentication failed")),
			});

			expect(await puzzel.init()).toBe(false);
			expect(puzzelAvailabilityManager.isAvailable).not.toHaveBeenCalled();
		});

		it("retries once and returns false when a new conversation never gets a status, clearing storage", async () => {
			const { puzzel, puzzelConversationManager, chatHandler, getStorage } = createPuzzel({
				getConversationStatus: jest.fn().mockResolvedValue([]),
			});

			const initialized = await puzzel.init();

			expect(initialized).toBe(false);
			expect(puzzelConversationManager.startConversation).toHaveBeenCalledTimes(2);
			expect(getStorage()).toBeNull();
			expect(chatHandler.setErrorData).not.toHaveBeenCalled();
		});
	});

	describe("with an ongoing conversation", () => {
		it("resumes it and loads the history without starting a new conversation", async () => {
			const { puzzel, puzzelAuthenticator, puzzelConversationManager, chatHistoryHandler, chatStatusHandler, conversationStorageManager } =
				createPuzzel({ stored: storedConversation });

			const initialized = await puzzel.init();

			expect(initialized).toBe(puzzel);
			expect(chatHistoryHandler.load).toHaveBeenCalledWith(["message"], ["meta"]);
			expect(chatStatusHandler.handleStatusEvent).toHaveBeenCalledWith({ status: "IN_QUEUE" });
			expect(puzzelConversationManager.getConversationStatus).toHaveBeenCalledTimes(1);
			expect(puzzelAuthenticator.authenticate).not.toHaveBeenCalled();
			expect(puzzelConversationManager.startConversation).not.toHaveBeenCalled();
			expect(conversationStorageManager.clearLocalStorage).not.toHaveBeenCalled();
		});

		it("resumes it outside opening hours, since the agent may still be in the conversation", async () => {
			const isAvailable = jest.fn().mockResolvedValue(false);
			const { puzzel } = createPuzzel({ stored: storedConversation, isAvailable });

			expect(await puzzel.init()).toBe(puzzel);
			expect(isAvailable).not.toHaveBeenCalled();
		});

		it("starts a fresh conversation when the stored one is stale, and returns that result", async () => {
			const { puzzel, puzzelConversationManager, chatHistoryHandler, chatHandler, getStorage } = createPuzzel({
				stored: storedConversation,
				getConversationStatus: jest.fn()
					.mockResolvedValueOnce([])
					.mockResolvedValue([{ status: "IN_QUEUE" }]),
			});

			const initialized = await puzzel.init();

			expect(initialized).toBe(puzzel);
			expect(puzzelConversationManager.resetConversationId).toHaveBeenCalledTimes(1);
			expect(puzzelConversationManager.startConversation).toHaveBeenCalledTimes(1);
			expect(chatHistoryHandler.load).not.toHaveBeenCalled();
			expect(chatHandler.setErrorData).not.toHaveBeenCalled();
			expect(getStorage()?.conversationData).toEqual({ conversationId: "new-conversation" });
		});

		it("starts a fresh conversation when fetching the stored status throws", async () => {
			const { puzzel, puzzelConversationManager } = createPuzzel({
				stored: storedConversation,
				getConversationStatus: jest.fn()
					.mockRejectedValueOnce(new Error("401"))
					.mockResolvedValue([{ status: "IN_QUEUE" }]),
			});

			expect(await puzzel.init()).toBe(puzzel);
			expect(puzzelConversationManager.startConversation).toHaveBeenCalledTimes(1);
		});

		it("keeps the chat and shows an error when neither resuming nor restarting works", async () => {
			const { puzzel, chatHandler, uiManager, outgoingChatHandler, puzzelConversationManager, getStorage } = createPuzzel({
				stored: storedConversation,
				isAvailable: jest.fn().mockResolvedValue(false),
				getConversationStatus: jest.fn().mockResolvedValue([]),
			});

			const initialized = await puzzel.init();

			expect(initialized).toBe(puzzel);
			expect(chatHandler.setErrorData).toHaveBeenCalledTimes(1);
			expect(chatHandler.displayBanner).toHaveBeenCalledWith(true);
			expect(chatHandler.disable).toHaveBeenCalledTimes(1);
			expect(uiManager.setTitle).toHaveBeenCalledWith("Puzzel Assistant");
			expect(uiManager.addCloseAction).toHaveBeenCalledTimes(1);
			expect(outgoingChatHandler.register).not.toHaveBeenCalled();
			expect(puzzelConversationManager.listenForConversation).not.toHaveBeenCalled();
			expect(getStorage()).toBeNull();
		});
	});
});
