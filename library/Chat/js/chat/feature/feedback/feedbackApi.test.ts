import FeedbackApi from "./feedbackApi";

describe("FeedbackApi", () => {
	const originalFetch = global.fetch;

	afterEach(() => {
		global.fetch = originalFetch;
		jest.restoreAllMocks();
	});

	it("posts the stat type as JSON to the chat stats endpoint", () => {
		// Arrange
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		global.fetch = fetchMock as any;
		const api = new FeedbackApi("https://example.com/wp-json/");

		// Act
		api.postStat("like");

		// Assert
		expect(fetchMock).toHaveBeenCalledTimes(1);
		const [url, init] = fetchMock.mock.calls[0];
		expect(url).toBe("https://example.com/wp-json/municipio/v1/chat/stats");
		expect(init.method).toBe("POST");
		expect(init.headers).toEqual({ "Content-Type": "application/json" });
		expect(JSON.parse(init.body)).toEqual({ type: "like" });
	});

	it("logs an error instead of throwing when the request fails", async () => {
		// Arrange
		global.fetch = jest.fn().mockRejectedValue(new Error("network")) as any;
		const consoleErrorSpy = jest.spyOn(console, "error").mockImplementation(() => {});
		const api = new FeedbackApi("/wp-json/");

		// Act
		api.postStat("dislike");
		await new Promise(resolve => setTimeout(resolve, 0));

		// Assert
		expect(consoleErrorSpy).toHaveBeenCalledWith("[FeedbackApi] Failed to post stat: dislike");
	});
});
