import GetConversationStatus from "./getConversationStatus";

describe("GetConversationStatus", () => {
	const originalFetch = global.fetch;

	const createConfig = () => ({ getCommunicationUrl: jest.fn().mockReturnValue("https://comm.example") });
	const createAuthenticator = () => ({ getAccessToken: jest.fn().mockResolvedValue("token-123") });
	const createResponse = (body: unknown) => ({
		ok: true,
		status: 200,
		statusText: "OK",
		json: jest.fn().mockResolvedValue(body),
		text: jest.fn().mockResolvedValue(""),
	});

	afterEach(() => {
		global.fetch = originalFetch;
		jest.clearAllTimers();
		jest.useRealTimers();
	});

	it("requests the allocation status endpoint and returns parsed statuses", async () => {
		// Arrange
		const statuses = [{ status: "InQueue", queuePosition: 2 }];
		global.fetch = jest.fn().mockResolvedValue(createResponse(statuses)) as any;
		const api = new GetConversationStatus(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = await api.execute("conv-1");

		// Assert
		expect(result).toEqual(statuses);
		expect(global.fetch).toHaveBeenCalledWith("https://comm.example/api/allocation/requestStatus/conv-1/", {
			method: "GET",
			headers: {
				"Authorization": "Bearer token-123",
				"Content-Type": "application/json",
			},
		});
	});

	it("notifies the latest status on the initial poll and every 3000ms poll until stopped", async () => {
		// Arrange
		jest.useFakeTimers();
		global.fetch = jest.fn()
			.mockResolvedValueOnce(createResponse([{ status: "InQueue", queuePosition: 2 }]))
			.mockResolvedValueOnce(createResponse([{ status: "InQueue", queuePosition: 2 }, { status: "Connected" }]))
			.mockResolvedValue(createResponse([])) as any;
		const api = new GetConversationStatus(createConfig() as any, createAuthenticator() as any);
		const callback = jest.fn();

		// Act
		await api.listen("conv-1", callback);
		await jest.advanceTimersByTimeAsync(3000);
		api.stopListening("conv-1");
		await jest.advanceTimersByTimeAsync(15000);

		// Assert
		expect(global.fetch).toHaveBeenCalledTimes(2);
		expect(callback.mock.calls).toEqual([
			[{ status: "InQueue", queuePosition: 2 }],
			[{ status: "Connected" }],
		]);
	});
});
