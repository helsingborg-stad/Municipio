import GetConversation from "./getConversation";

describe("GetConversation", () => {
	const originalFetch = global.fetch;

	const createConfig = () => ({ getCommunicationUrl: jest.fn().mockReturnValue("https://comm.example") });
	const createAuthenticator = () => ({ getAccessToken: jest.fn().mockResolvedValue("token-123") });
	const createResponse = (body: unknown, ok = true) => ({
		ok,
		status: ok ? 200 : 401,
		statusText: ok ? "OK" : "Unauthorized",
		json: jest.fn().mockResolvedValue(body),
		text: jest.fn().mockResolvedValue(""),
	});

	afterEach(() => {
		global.fetch = originalFetch;
		jest.clearAllTimers();
		jest.useRealTimers();
	});

	it("requests the conversation endpoint with an encoded conversation id and returns parsed events", async () => {
		// Arrange
		const events = [{ eventId: 1 }, { eventId: 2 }];
		global.fetch = jest.fn().mockResolvedValue(createResponse(events)) as any;
		const api = new GetConversation(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = await api.execute("conv/1 2");

		// Assert
		expect(result).toEqual(events);
		expect(global.fetch).toHaveBeenCalledWith("https://comm.example/api/conversation/conv%2F1%202", {
			method: "GET",
			headers: {
				"Content-Type": "application/json",
				"Authorization": "Bearer token-123",
			},
		});
	});

	it("throws when response is not ok", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse([], false)) as any;
		const api = new GetConversation(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = api.execute("conv-1");

		// Assert
		await expect(result).rejects.toThrow("Failed to listen for conversation: 401 Unauthorized");
	});

	it("polls every 3000ms and notifies only new events until stopped", async () => {
		// Arrange
		jest.useFakeTimers();
		global.fetch = jest.fn()
			.mockResolvedValueOnce(createResponse([{ eventId: 1 }]))
			.mockResolvedValueOnce(createResponse([{ eventId: 1 }, { eventId: 2 }]))
			.mockResolvedValue(createResponse([{ eventId: 1 }, { eventId: 2 }, { eventId: 3 }])) as any;
		const api = new GetConversation(createConfig() as any, createAuthenticator() as any);
		const callback = jest.fn();

		// Act
		await api.listen("conv-1", callback);
		await jest.advanceTimersByTimeAsync(3000);
		api.stopListening("conv-1");
		await jest.advanceTimersByTimeAsync(3000 * 5);

		// Assert
		expect(global.fetch).toHaveBeenCalledTimes(2);
		expect(callback.mock.calls).toEqual([[{ eventId: 2 }]]);
	});
});
