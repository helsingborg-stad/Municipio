import StartConversation from "./startConversation";

describe("StartConversation", () => {
	const originalFetch = global.fetch;

	const createConfig = () => ({
		getCommunicationUrl: jest.fn().mockReturnValue("https://comm.example"),
		getChatQueueKey: jest.fn().mockReturnValue("queue-key"),
	});
	const createAuthenticator = () => ({ getAccessToken: jest.fn().mockResolvedValue("token-123") });
	const createResponse = (body: unknown, ok = true) => ({
		ok,
		status: ok ? 200 : 409,
		statusText: ok ? "OK" : "Conflict",
		json: jest.fn().mockResolvedValue(body),
		text: jest.fn().mockResolvedValue(""),
	});

	afterEach(() => {
		global.fetch = originalFetch;
	});

	it("routes the conversation to the configured queue and returns the parsed response", async () => {
		// Arrange
		const routeResponse = { conversationId: "conv-1", iqSessionId: "iq", requestId: 7, result: "ok" };
		global.fetch = jest.fn().mockResolvedValue(createResponse(routeResponse)) as any;
		const api = new StartConversation(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = await api.execute("conv-1");

		// Assert
		const [url, options] = (global.fetch as jest.Mock).mock.calls[0];
		expect(result).toEqual(routeResponse);
		expect(url.trim()).toBe("https://comm.example/api/conversation/conv-1/route/queue-key");
		expect(options).toEqual({
			method: "PUT",
			headers: {
				"Content-Type": "application/json",
				"Authorization": "Bearer token-123",
			},
			body: "[]",
		});
	});

	it("throws when response is not ok", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse({}, false)) as any;
		const api = new StartConversation(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = api.execute("conv-1");

		// Assert
		await expect(result).rejects.toThrow("Failed to start conversation: 409 Conflict");
	});
});
