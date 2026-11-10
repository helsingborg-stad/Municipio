import { ChatSession } from "./ChatSession";

const SESSION_ID_KEY = "municipio:chat:global-chat:sessions";

type ChatSessionPrivateApi = {
	postMessage(message: string): Promise<Response>;
	handleSseEvent(
		eventType: string,
		payload: string,
		accumulatedText: string,
	): { eventType: string; accumulatedText: string; event: ChatEvent | null };
};

describe("ChatSession", () => {
	beforeEach(() => {
		window.localStorage.clear();
		jest.restoreAllMocks();
		Reflect.deleteProperty(globalThis, "wpApiSettings");
	});

	it("sends the current REST nonce and login cookies with chat requests", async () => {
		Object.defineProperty(globalThis, "wpApiSettings", {
			configurable: true,
			value: { root: "https://example.com/wp-json/", nonce: "old-nonce" },
		});
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
		});

		wpApiSettings.nonce = "current-nonce";
		await (session as unknown as ChatSessionPrivateApi).postMessage("Hello");

		const [, requestInit] = fetchMock.mock.calls[0] as [string, RequestInit];
		expect(requestInit.credentials).toBe("same-origin");
		expect(requestInit.headers).toEqual({
			"Content-Type": "application/json",
			"X-WP-Nonce": "current-nonce",
		});
	});

	it("keeps guest requests without a REST nonce", async () => {
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
		});

		await (session as unknown as ChatSessionPrivateApi).postMessage("Hello");

		const [, requestInit] = fetchMock.mock.calls[0] as [string, RequestInit];
		expect(requestInit.headers).toEqual({ "Content-Type": "application/json" });
	});

	it("uses persisted session id when session persistence is enabled", async () => {
		window.localStorage.setItem(
			SESSION_ID_KEY,
			JSON.stringify({ Ava: { sessionId: "persisted-session" } }),
		);
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
			persistSession: true,
		});
		const privateSession = session as unknown as ChatSessionPrivateApi;

		await privateSession.postMessage("Hello");

		const [, requestInit] = fetchMock.mock.calls[0] as [string, RequestInit];
		const body = JSON.parse(String(requestInit.body));
		expect(body.session_id).toBe("persisted-session");
	});

	it("does not use persisted session id when session persistence is disabled", async () => {
		window.localStorage.setItem(
			SESSION_ID_KEY,
			JSON.stringify({ Ava: { sessionId: "persisted-session" } }),
		);
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
			persistSession: false,
		});
		const privateSession = session as unknown as ChatSessionPrivateApi;

		await privateSession.postMessage("Hello");

		const [, requestInit] = fetchMock.mock.calls[0] as [string, RequestInit];
		const body = JSON.parse(String(requestInit.body));
		expect(body.session_id).toBeNull();
	});

	it("does not persist session id to localStorage when session persistence is disabled", () => {
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
			persistSession: false,
		});
		const privateSession = session as unknown as ChatSessionPrivateApi;

		privateSession.handleSseEvent(
			"first_chunk",
			JSON.stringify({ session_id: "new-session" }),
			"",
		);

		expect(window.localStorage.getItem(SESSION_ID_KEY)).toBeNull();
	});

	it("persists session id to localStorage when session persistence is enabled", () => {
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
			persistSession: true,
		});
		const privateSession = session as unknown as ChatSessionPrivateApi;

		privateSession.handleSseEvent(
			"first_chunk",
			JSON.stringify({ session_id: "new-session" }),
			"",
		);

		const savedSessions = JSON.parse(
			window.localStorage.getItem(SESSION_ID_KEY) || "{}",
		) as Record<string, { sessionId: string }>;
		expect(savedSessions.Ava.sessionId).toBe("new-session");
	});
});
