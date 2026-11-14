import PuzzelConversationManager from "./puzzelConversationManager";

describe("PuzzelConversationManager", () => {
	const createDependencies = (storedConversationId: string | null = null) => ({
		startConversationApi: { execute: jest.fn().mockResolvedValue({ conversationId: "conv" }) },
		sendMetaDataApi: { execute: jest.fn().mockResolvedValue(undefined) },
		listenForMetaDataApi: { execute: jest.fn().mockResolvedValue(["meta"]), listen: jest.fn().mockResolvedValue(undefined), stopListening: jest.fn() },
		listenForConversationApi: { execute: jest.fn().mockResolvedValue(["chat"]), listen: jest.fn().mockResolvedValue(undefined), stopListening: jest.fn() },
		sendUserMessageApi: { execute: jest.fn().mockResolvedValue(undefined) },
		getConversationStatusApi: { execute: jest.fn().mockResolvedValue(["status"]), listen: jest.fn().mockResolvedValue(undefined), stopListening: jest.fn() },
		conversationStorage: { getConversationId: jest.fn().mockReturnValue(storedConversationId) },
	});

	const createManager = (deps: ReturnType<typeof createDependencies>) => new PuzzelConversationManager(
		deps.startConversationApi as any,
		deps.sendMetaDataApi as any,
		deps.listenForMetaDataApi as any,
		deps.listenForConversationApi as any,
		deps.sendUserMessageApi as any,
		deps.getConversationStatusApi as any,
		deps.conversationStorage as any,
	);

	const originalCrypto = globalThis.crypto;

	beforeEach(() => {
		Object.defineProperty(globalThis, "crypto", {
			value: { randomUUID: jest.fn().mockReturnValue("generated-uuid") },
			configurable: true,
			writable: true,
		});
	});

	afterEach(() => {
		Object.defineProperty(globalThis, "crypto", { value: originalCrypto, configurable: true, writable: true });
	});

	it("uses the stored conversation id when available", () => {
		// Arrange
		const deps = createDependencies("stored-id");
		const manager = createManager(deps);

		// Act
		const result = manager.getConversationId();

		// Assert
		expect(result).toBe("stored-id");
		expect(globalThis.crypto.randomUUID).not.toHaveBeenCalled();
	});

	it("generates and memoizes a new id when nothing is stored", () => {
		// Arrange
		const deps = createDependencies();
		const manager = createManager(deps);

		// Act
		const first = manager.getConversationId();
		const second = manager.getConversationId();

		// Assert
		expect(first).toBe("generated-uuid");
		expect(second).toBe("generated-uuid");
		expect(globalThis.crypto.randomUUID).toHaveBeenCalledTimes(1);
		expect(deps.conversationStorage.getConversationId).toHaveBeenCalledTimes(1);
	});

	it("resolves the id again after resetConversationId", () => {
		// Arrange
		const deps = createDependencies("old-id");
		const manager = createManager(deps);
		manager.getConversationId();
		deps.conversationStorage.getConversationId.mockReturnValue(null);

		// Act
		manager.resetConversationId();
		const result = manager.getConversationId();

		// Assert
		expect(result).toBe("generated-uuid");
	});

	it("sends join and typing metadata events for the current conversation", async () => {
		// Arrange
		const deps = createDependencies("conv-1");
		const manager = createManager(deps);

		// Act
		await manager.sendUserJoinEventToConversation();
		await manager.sendUserTypingEvent();

		// Assert
		expect(deps.sendMetaDataApi.execute.mock.calls).toEqual([
			["conv-1", "participantJoinEvent"],
			["conv-1", "typingEvent"],
		]);
	});

	it("delegates conversation calls with the current conversation id", async () => {
		// Arrange
		const deps = createDependencies("conv-1");
		const manager = createManager(deps);
		const callback = jest.fn();

		// Act
		const started = await manager.startConversation();
		await manager.sendUserMessage("Hello");
		const status = await manager.getConversationStatus();
		const conversation = await manager.getCurrentConversation();
		const metaData = await manager.getCurrentMetaData();
		await manager.listenForMetaData(callback);
		await manager.listenForConversation(callback);
		await manager.listenForConversationStatus(callback);

		// Assert
		expect(started).toEqual({ conversationId: "conv" });
		expect(deps.startConversationApi.execute).toHaveBeenCalledWith("conv-1");
		expect(deps.sendUserMessageApi.execute).toHaveBeenCalledWith("conv-1", "Hello");
		expect(status).toEqual(["status"]);
		expect(conversation).toEqual(["chat"]);
		expect(metaData).toEqual(["meta"]);
		expect(deps.listenForMetaDataApi.listen).toHaveBeenCalledWith("conv-1", callback);
		expect(deps.listenForConversationApi.listen).toHaveBeenCalledWith("conv-1", callback);
		expect(deps.getConversationStatusApi.listen).toHaveBeenCalledWith("conv-1", callback);
	});

	it("stops all listeners for the current conversation", () => {
		// Arrange
		const deps = createDependencies("conv-1");
		const manager = createManager(deps);

		// Act
		manager.stopListening();

		// Assert
		expect(deps.listenForMetaDataApi.stopListening).toHaveBeenCalledWith("conv-1");
		expect(deps.listenForConversationApi.stopListening).toHaveBeenCalledWith("conv-1");
		expect(deps.getConversationStatusApi.stopListening).toHaveBeenCalledWith("conv-1");
	});
});
