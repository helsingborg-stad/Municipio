import SendMetadata from "./sendMetadata";

describe("SendMetadata", () => {
	const originalFetch = global.fetch;

	const createConfig = () => ({ getCommunicationUrl: jest.fn().mockReturnValue("https://comm.example") });
	const createAuthenticator = () => ({ getAccessToken: jest.fn().mockResolvedValue("token-123") });
	const createResponse = (ok = true) => ({
		ok,
		status: ok ? 200 : 400,
		statusText: ok ? "OK" : "Bad Request",
		json: jest.fn(),
		text: jest.fn().mockResolvedValue("invalid"),
	});

	afterEach(() => {
		global.fetch = originalFetch;
	});

	it("posts the event type as a visitor to the metadata endpoint", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse()) as any;
		const api = new SendMetadata(createConfig() as any, createAuthenticator() as any);

		// Act
		await api.execute("conv-1", "typingEvent");

		// Assert
		const [url, options] = (global.fetch as jest.Mock).mock.calls[0];
		expect(url.trim()).toBe("https://comm.example/api/metadata/conv-1");
		expect(options.method).toBe("POST");
		expect(options.headers).toEqual({
			"Content-Type": "application/json",
			"Authorization": "Bearer token-123",
		});
		expect(JSON.parse(options.body)).toEqual({ senderName: "Visitor", type: "typingEvent" });
	});

	it("throws when response is not ok", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse(false)) as any;
		const api = new SendMetadata(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = api.execute("conv-1", "participantJoinEvent");

		// Assert
		await expect(result).rejects.toThrow("Failed to send metadata event: 400 Bad Request - invalid");
	});

	it("does not call fetch when the access token cannot be obtained", async () => {
		// Arrange
		global.fetch = jest.fn() as any;
		const authenticator = { getAccessToken: jest.fn().mockRejectedValue(new Error("auth")) };
		const api = new SendMetadata(createConfig() as any, authenticator as any);

		// Act
		const result = api.execute("conv-1", "typingEvent");

		// Assert
		await expect(result).rejects.toThrow("auth");
		expect(global.fetch).not.toHaveBeenCalled();
	});
});
