import GetChatOpen from "./getChatOpen";

describe("GetChatOpen", () => {
	const originalFetch = global.fetch;

	const createConfig = () => ({
		getCommunicationUrl: jest.fn().mockReturnValue("https://comm.example"),
		getTimeId: jest.fn().mockReturnValue("time-1"),
	});
	const createAuthenticator = () => ({ getAccessToken: jest.fn().mockResolvedValue("token-123") });
	const createResponse = (body: unknown, ok = true) => ({
		ok,
		status: ok ? 200 : 503,
		statusText: ok ? "OK" : "Unavailable",
		json: jest.fn().mockResolvedValue(body),
		text: jest.fn().mockResolvedValue(""),
	});

	afterEach(() => {
		global.fetch = originalFetch;
	});

	it("requests the time endpoint with bearer token", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse({ status: "open" })) as any;
		const api = new GetChatOpen(createConfig() as any, createAuthenticator() as any);

		// Act
		await api.execute();

		// Assert
		expect(global.fetch).toHaveBeenCalledWith("https://comm.example/api/time/time-1", {
			method: "GET",
			headers: {
				"Content-Type": "application/json",
				"Authorization": "Bearer token-123",
			},
		});
	});

	it.each([
		[{ status: "open" }, true],
		[{ status: "closed" }, false],
		[{}, false],
	])("maps response %p to %p", async (body, expected) => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse(body)) as any;
		const api = new GetChatOpen(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = await api.execute();

		// Assert
		expect(result).toBe(expected);
	});

	it("throws when response is not ok", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse({}, false)) as any;
		const api = new GetChatOpen(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = api.execute();

		// Assert
		await expect(result).rejects.toThrow("Failed to get chat open status: 503 Unavailable");
	});
});
