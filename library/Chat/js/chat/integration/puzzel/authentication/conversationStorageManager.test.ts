import ConversationStorageManager from "./conversationStorageManager";
import { SESSION_ID_KEY } from "../../../enums";

describe("ConversationStorageManager", () => {
	const conversationData = { conversationId: "conv-1", iqSessionId: "iq", requestId: 1, result: "ok" };
	const authenticationData = {
		access_token: "token-123",
		refresh_token: "refresh",
		expires_in: 60,
		scope: "scope",
		token_type: "Bearer",
		visitor_id: "visitor",
		expires: 123,
	};

	const readJson = (key: string) => JSON.parse(window.localStorage.getItem(key) as string);

	afterEach(() => {
		window.localStorage.clear();
		jest.restoreAllMocks();
	});

	it("returns null for everything when nothing is stored", () => {
		// Arrange
		const manager = new ConversationStorageManager();

		// Act / Assert
		expect(manager.getLocalStorage()).toBeNull();
		expect(manager.getConversationData()).toBeNull();
		expect(manager.getAuthenticationData()).toBeNull();
		expect(manager.getConversationId()).toBeNull();
		expect(manager.getAccessToken()).toBeNull();
	});

	it("returns null when sessions exist but contain no puzzel entry", () => {
		// Arrange
		window.localStorage.setItem(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS, JSON.stringify({ other: { id: 1 } }));
		const manager = new ConversationStorageManager();

		// Act
		const result = manager.getLocalStorage();

		// Assert
		expect(result).toBeNull();
	});

	it("stores conversation and authentication data under the puzzel key, preserving other sessions", () => {
		// Arrange
		window.localStorage.setItem(
			SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS,
			JSON.stringify({ other: { id: 1 }, puzzel: { extra: "kept" } }),
		);
		const manager = new ConversationStorageManager();

		// Act
		manager.setLocalStorage(conversationData as any, authenticationData as any);

		// Assert
		expect(readJson(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS)).toEqual({
			other: { id: 1 },
			puzzel: { extra: "kept", conversationData, authenticationData },
		});
		expect(manager.getConversationId()).toBe("conv-1");
		expect(manager.getAccessToken()).toBe("token-123");
	});

	it("marks the global chat bubble for initialization when storing, preserving other bubble states", () => {
		// Arrange
		window.localStorage.setItem(
			SESSION_ID_KEY.GLOBAL_CHAT_BUBBLE_INITIALIZE_STATES,
			JSON.stringify({ "other-bubble": false }),
		);
		const manager = new ConversationStorageManager();

		// Act
		manager.setLocalStorage(conversationData as any, authenticationData as any);

		// Assert
		expect(readJson(SESSION_ID_KEY.GLOBAL_CHAT_BUBBLE_INITIALIZE_STATES)).toEqual({
			"other-bubble": false,
			"global-chat-bubble": true,
		});
	});

	it("caches the parsed data after the first read", () => {
		// Arrange
		window.localStorage.setItem(
			SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS,
			JSON.stringify({ puzzel: { conversationData, authenticationData } }),
		);
		const manager = new ConversationStorageManager();
		manager.getLocalStorage();
		const getItemSpy = jest.spyOn(Storage.prototype, "getItem");

		// Act
		const conversationId = manager.getConversationId();

		// Assert
		expect(conversationId).toBe("conv-1");
		expect(getItemSpy).not.toHaveBeenCalled();
	});

	it("re-reads storage after setLocalStorage invalidates the cache", () => {
		// Arrange
		const manager = new ConversationStorageManager();
		manager.setLocalStorage(conversationData as any, authenticationData as any);
		manager.getLocalStorage();

		// Act
		manager.setLocalStorage({ ...conversationData, conversationId: "conv-2" } as any, authenticationData as any);

		// Assert
		expect(manager.getConversationId()).toBe("conv-2");
	});

	it("removes only the puzzel entry and resets bubble initialization on clear", () => {
		// Arrange
		window.localStorage.setItem(
			SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS,
			JSON.stringify({ other: { id: 1 }, puzzel: { conversationData, authenticationData } }),
		);
		const manager = new ConversationStorageManager();
		manager.getLocalStorage();

		// Act
		manager.clearLocalStorage();

		// Assert
		expect(readJson(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS)).toEqual({ other: { id: 1 } });
		expect(readJson(SESSION_ID_KEY.GLOBAL_CHAT_BUBBLE_INITIALIZE_STATES)).toEqual({ "global-chat-bubble": false });
		expect(manager.getConversationId()).toBeNull();
	});

	it("does nothing to storage on clear when no sessions are stored", () => {
		// Arrange
		const manager = new ConversationStorageManager();

		// Act
		manager.clearLocalStorage();

		// Assert
		expect(window.localStorage.getItem(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS)).toBeNull();
		expect(window.localStorage.getItem(SESSION_ID_KEY.GLOBAL_CHAT_BUBBLE_INITIALIZE_STATES)).toBeNull();
	});
});
