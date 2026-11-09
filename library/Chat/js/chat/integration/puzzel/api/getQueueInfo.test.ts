import GetQueueInfo from "./getQueueInfo";

describe("GetQueueInfo", () => {
	const originalFetch = global.fetch;

	const createConfig = () => ({
		getCommunicationUrl: jest.fn().mockReturnValue("https://comm.example"),
		getVisualQueueId: jest.fn().mockReturnValue("vq-1"),
	});
	const createAuthenticator = () => ({ getAccessToken: jest.fn().mockResolvedValue("token-123") });
	const createResponse = (body: unknown, ok = true) => ({
		ok,
		status: ok ? 200 : 500,
		statusText: ok ? "OK" : "Server Error",
		json: jest.fn().mockResolvedValue(body),
		text: jest.fn().mockResolvedValue("details"),
	});

	afterEach(() => {
		global.fetch = originalFetch;
	});

	it("requests the queue info endpoint with bearer token", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse({ openForWebAccess: true, agentsReady: 1 })) as any;
		const api = new GetQueueInfo(createConfig() as any, createAuthenticator() as any);

		// Act
		await api.execute();

		// Assert
		expect(global.fetch).toHaveBeenCalledWith("https://comm.example/api/queue/info/vq-1", {
			method: "GET",
			headers: {
				"Content-Type": "application/json",
				"Authorization": "Bearer token-123",
			},
		});
	});

	it.each([
		["open with ready agents", { openForWebAccess: true, agentsReady: 2 }, true],
		["open, falls back to logged in agents", { openForWebAccess: true, agentsLoggedIn: 1 }, true],
		["open with zero ready agents (does not fall back)", { openForWebAccess: true, agentsReady: 0, agentsLoggedIn: 3 }, false],
		["open without agent counts", { openForWebAccess: true }, false],
		["closed for web access", { openForWebAccess: false, agentsReady: 5 }, false],
		["missing openForWebAccess", { agentsReady: 5 }, false],
	])("%s", async (_label, body, expected) => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse(body)) as any;
		const api = new GetQueueInfo(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = await api.execute();

		// Assert
		expect(result).toBe(expected);
	});

	it("throws when response is not ok", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse({}, false)) as any;
		const api = new GetQueueInfo(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = api.execute();

		// Assert
		await expect(result).rejects.toThrow("Failed to get queue info: 500 Server Error - details");
	});
});
