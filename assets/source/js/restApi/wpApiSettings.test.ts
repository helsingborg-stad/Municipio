describe("initializeWpApiSettingsNonceRefresh", () => {
	const originalFetch = globalThis.fetch;
	const testWindow = window as Window & {
		wpApiSettings?: {
			root: string;
			nonce: string;
			refreshNonce?: () => Promise<string | null>;
		};
	};

	afterEach(() => {
		delete testWindow.wpApiSettings;

		if (originalFetch) {
			globalThis.fetch = originalFetch;
		} else {
			delete (globalThis as { fetch?: typeof fetch }).fetch;
		}
	});

	it("refreshes the nonce using the localized REST settings", async () => {
		Object.defineProperty(document, "readyState", {
			configurable: true,
			value: "complete",
		});
		testWindow.wpApiSettings = {
			root: "https://example.test/wp-json/",
			nonce: "initial-nonce",
		};
		const fetchMock = jest.fn().mockResolvedValue({
			headers: {
				get: jest.fn().mockReturnValue("refreshed-nonce"),
			},
		});
		globalThis.fetch = fetchMock as unknown as typeof fetch;

		const { initializeWpApiSettingsNonceRefresh } = require("./wpApiSettings");
		initializeWpApiSettingsNonceRefresh();

		await testWindow.wpApiSettings.refreshNonce?.();

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
		expect(testWindow.wpApiSettings.nonce).toBe("refreshed-nonce");
	});
});
