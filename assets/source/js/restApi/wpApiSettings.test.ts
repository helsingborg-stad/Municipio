describe("initializeWpApiSettingsNonceRefresh", () => {
	const originalFetch = global.fetch;

	afterEach(() => {
		delete (window as any).wpApiSettings;

		if (originalFetch) {
			global.fetch = originalFetch;
		} else {
			delete (global as any).fetch;
		}
	});

	it("refreshes the nonce using the localized REST settings", async () => {
		Object.defineProperty(document, "readyState", {
			configurable: true,
			value: "complete",
		});
		(window as any).wpApiSettings = {
			root: "https://example.test/wp-json/",
			nonce: "initial-nonce",
		};
		const fetchMock = jest.fn().mockResolvedValue({
			headers: {
				get: jest.fn().mockReturnValue("refreshed-nonce"),
			},
		});
		global.fetch = fetchMock;

		const { initializeWpApiSettingsNonceRefresh } = require("./wpApiSettings");
		initializeWpApiSettingsNonceRefresh();

		await (window as any).wpApiSettings.refreshNonce();

		expect(fetchMock).toHaveBeenCalledWith(
			expect.stringContaining(
				"https://example.test/wp-json/municipio/v1/nonce/refresh",
			),
			expect.objectContaining({
				credentials: "include",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": "initial-nonce",
				},
			}),
		);
		expect((window as any).wpApiSettings.nonce).toBe("refreshed-nonce");
	});
});
