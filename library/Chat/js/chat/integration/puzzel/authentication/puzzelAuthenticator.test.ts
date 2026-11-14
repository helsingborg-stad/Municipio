import PuzzelAuthenticator from "./puzzelAuthenticator";

describe("PuzzelAuthenticator", () => {
	const originalFetch = global.fetch;
	const now = 1_700_000_000_000;

	const createConfig = () => ({
		getAuthenticationUrl: jest.fn().mockReturnValue("https://auth.example"),
		getClientId: jest.fn().mockReturnValue("client-1"),
		getCustomerId: jest.fn().mockReturnValue("customer-1"),
	});
	const createStorage = (authenticationData: unknown = null) => ({
		getAuthenticationData: jest.fn().mockReturnValue(authenticationData),
	});
	const createResponse = (body: unknown, ok = true) => ({
		ok,
		status: ok ? 200 : 401,
		statusText: ok ? "OK" : "Unauthorized",
		json: jest.fn().mockResolvedValue(body),
	});
	const tokenResponse = {
		access_token: "fresh-token",
		refresh_token: "refresh",
		expires_in: 60,
		scope: "scope",
		token_type: "Bearer",
		visitor_id: "visitor",
	};

	beforeEach(() => {
		jest.spyOn(Date, "now").mockReturnValue(now);
	});

	afterEach(() => {
		global.fetch = originalFetch;
		jest.restoreAllMocks();
	});

	it("returns stored authentication data without fetching when it is still valid", async () => {
		// Arrange
		const stored = { ...tokenResponse, access_token: "stored-token", expires: now + 1000 };
		global.fetch = jest.fn() as any;
		const authenticator = new PuzzelAuthenticator(createConfig() as any, createStorage(stored) as any);

		// Act
		const result = await authenticator.authenticate();

		// Assert
		expect(result).toBe(stored);
		expect(global.fetch).not.toHaveBeenCalled();
	});

	it("requests a visitor token as form data when no stored data exists", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse(tokenResponse)) as any;
		const authenticator = new PuzzelAuthenticator(createConfig() as any, createStorage() as any);

		// Act
		const result = await authenticator.authenticate();

		// Assert
		const [url, options] = (global.fetch as jest.Mock).mock.calls[0];
		expect(url).toBe("https://auth.example/connect/token");
		expect(options.method).toBe("POST");
		expect(options.headers).toEqual({ "Content-Type": "application/x-www-form-urlencoded" });
		expect(Object.fromEntries(new URLSearchParams(options.body.toString()))).toEqual({
			client_id: "client-1",
			grant_type: "visitor",
			tenant_id: "customer-1",
		});
		expect(result).toEqual({ ...tokenResponse, expires: now + 60_000 });
	});

	it("fetches a new token when stored data has expired", async () => {
		// Arrange
		const stored = { ...tokenResponse, access_token: "stale-token", expires: now - 1 };
		global.fetch = jest.fn().mockResolvedValue(createResponse(tokenResponse)) as any;
		const authenticator = new PuzzelAuthenticator(createConfig() as any, createStorage(stored) as any);

		// Act
		const result = await authenticator.authenticate();

		// Assert
		expect(global.fetch).toHaveBeenCalledTimes(1);
		expect(result.access_token).toBe("fresh-token");
	});

	it("throws when the token endpoint responds with an error", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse({}, false)) as any;
		const authenticator = new PuzzelAuthenticator(createConfig() as any, createStorage() as any);

		// Act
		const result = authenticator.authenticate();

		// Assert
		await expect(result).rejects.toThrow("Authentication failed: 401 Unauthorized");
	});

	it("throws when the token response lacks an access token", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse({ ...tokenResponse, access_token: "" })) as any;
		const authenticator = new PuzzelAuthenticator(createConfig() as any, createStorage() as any);

		// Act
		const result = authenticator.authenticate();

		// Assert
		await expect(result).rejects.toThrow("Authentication failed: missing access token");
	});

	it("caches the access token in memory between calls while it is valid", async () => {
		// Arrange
		global.fetch = jest.fn().mockResolvedValue(createResponse(tokenResponse)) as any;
		const storage = createStorage();
		const authenticator = new PuzzelAuthenticator(createConfig() as any, storage as any);

		// Act
		const first = await authenticator.getAccessToken();
		const second = await authenticator.getAccessToken();

		// Assert
		expect(first).toBe("fresh-token");
		expect(second).toBe("fresh-token");
		expect(global.fetch).toHaveBeenCalledTimes(1);
		expect(storage.getAuthenticationData).toHaveBeenCalledTimes(1);
	});

	it("re-authenticates once the cached token has expired", async () => {
		// Arrange
		global.fetch = jest.fn()
			.mockResolvedValueOnce(createResponse({ ...tokenResponse, access_token: "first" }))
			.mockResolvedValueOnce(createResponse({ ...tokenResponse, access_token: "second" })) as any;
		const authenticator = new PuzzelAuthenticator(createConfig() as any, createStorage() as any);
		await authenticator.getAccessToken();
		(Date.now as jest.Mock).mockReturnValue(now + 60_000);

		// Act
		const result = await authenticator.getAccessToken();

		// Assert
		expect(result).toBe("second");
		expect(global.fetch).toHaveBeenCalledTimes(2);
	});
});
