import { ChatSession } from "./ChatSession";

const SESSION_ID_KEY = "municipio:chat:global-chat:sessions";

type ChatSessionPrivateApi = {
	postMessage(message: string): Promise<Response>;
	assertSseResponse(response: Response): Promise<void>;
	parseErrorMessage(rawBody: string): string;
	processSseLine(
		line: string,
		eventType: string,
		accumulatedText: string,
	): { eventType: string; accumulatedText: string; event: ChatEvent | null };
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

	it("accumulates text chunks from SSE text events", () => {
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
			persistSession: true,
		});
		const privateSession = session as unknown as ChatSessionPrivateApi;

		const firstEvent = privateSession.handleSseEvent(
			"text",
			JSON.stringify({ answer: "Hello" }),
			"",
		);
		const secondEvent = privateSession.handleSseEvent(
			"text",
			JSON.stringify({ answer: " world" }),
			firstEvent.accumulatedText,
		);

		expect(firstEvent.event).toEqual({ type: "text", content: "Hello" });
		expect(secondEvent.event).toEqual({ type: "text", content: "Hello world" });
	});

	it("maps tool_call SSE events to tool_call chat events", () => {
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
			persistSession: true,
		});
		const privateSession = session as unknown as ChatSessionPrivateApi;

		const result = privateSession.handleSseEvent(
			"tool_call",
			JSON.stringify({}),
			"",
		);

		expect(result.event).toEqual({ type: "tool_call" });
	});

	it("parseErrorMessage returns message when response body contains message", () => {
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
			persistSession: true,
		});
		const privateSession = session as unknown as ChatSessionPrivateApi;

		expect(privateSession.parseErrorMessage('{"message":"Upstream failed"}')).toBe(
			"Upstream failed",
		);
	});

	it("assertSseResponse throws when content-type is not SSE", async () => {
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
			persistSession: true,
		});
		const privateSession = session as unknown as ChatSessionPrivateApi;

		const response = {
			ok: false,
			headers: {
				get: (headerName: string) =>
					headerName === "Content-Type" ? "application/json" : null,
			},
			text: async () => '{"message":"Not stream"}',
		} as unknown as Response;

		await expect(privateSession.assertSseResponse(response)).rejects.toThrow(
			"Not stream",
		);
	});

	it("processSseLine reads event lines and data lines consistently", () => {
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		const session = new ChatSession({
			apiRoot: "https://example.com/wp-json/",
			assistantName: "Ava",
			fetchImpl: fetchMock as unknown as typeof fetch,
			persistSession: true,
		});
		const privateSession = session as unknown as ChatSessionPrivateApi;

		const eventLine = privateSession.processSseLine("event: text", "", "");
		const dataLine = privateSession.processSseLine(
			'data: {"answer":"Hello"}',
			eventLine.eventType,
			eventLine.accumulatedText,
		);

		expect(eventLine.eventType).toBe("text");
		expect(dataLine.event).toEqual({ type: "text", content: "Hello" });
	});
});
