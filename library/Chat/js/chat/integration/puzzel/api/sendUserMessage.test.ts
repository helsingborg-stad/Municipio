import SendUserMessage from "./sendUserMessage";

describe("SendUserMessage", () => {
	const originalFetch = global.fetch;

	const createConfig = () => ({ getCommunicationUrl: jest.fn().mockReturnValue("https://comm.example") });
	const createAuthenticator = () => ({ getAccessToken: jest.fn().mockResolvedValue("token-123") });
	const createResponse = (ok = true) => ({
		ok,
		status: ok ? 200 : 500,
		statusText: ok ? "OK" : "Server Error",
		json: jest.fn(),
		text: jest.fn().mockResolvedValue(""),
	});

	afterEach(() => {
		global.fetch = originalFetch;
	});

	it("posts the message as a visitor to the conversation message endpoint", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse()) as any;
		const api = new SendUserMessage(createConfig() as any, createAuthenticator() as any);

		// Act
		await api.execute("conv-1", "Hello \"agent\"");

		// Assert
		const [url, options] = (global.fetch as jest.Mock).mock.calls[0];
		expect(url.trim()).toBe("https://comm.example/api/conversation/conv-1/message");
		expect(options.method).toBe("POST");
		expect(options.headers).toEqual({
			"Content-Type": "application/json",
			"Authorization": "Bearer token-123",
		});
		expect(JSON.parse(options.body)).toEqual({ senderName: "Visitor", message: "Hello \"agent\"" });
	});

	it("throws when response is not ok", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse(false)) as any;
		const api = new SendUserMessage(createConfig() as any, createAuthenticator() as any);

		// Act
		const result = api.execute("conv-1", "Hello");

		// Assert
		await expect(result).rejects.toThrow(": 500 Server Error");
	});
});
