class PuzzelAuthenticator implements PuzzelAuthenticatorInterface {

    private accessToken?: string;
    private authenticated?: AuthenticationResponse;
    private expiresAt?: number;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
    ) {}

    public async getAccessToken(): Promise<string> {
        const isTokenValid =
            this.accessToken !== undefined &&
            this.expiresAt !== undefined &&
            Date.now() < this.expiresAt;

        if (!isTokenValid) {
            await this.authenticate();
        }

        if (!this.accessToken) {
            throw new Error("Authentication failed: missing access token");
        }

        return this.accessToken;
    }

    public async authenticate(): Promise<AuthenticationResponse> {
        const response = await fetch(
            `${this.puzzelConfig.getAuthenticationUrl()}/connect/token`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    client_id: this.puzzelConfig.getClientId(),
                    grant_type: "visitor",
                    tenant_id: this.puzzelConfig.getChatQueueKey(),
                }),
            },
        );

        if (!response.ok) {
            throw new Error(
                `Authentication failed: ${response.status} ${response.statusText}`,
            );
        }

        const authenticated =
            await response.json() as AuthenticationResponse;

        if (!authenticated.access_token) {
            throw new Error("Authentication failed: missing access token");
        }

        if (
            typeof authenticated.expires_in !== "number" ||
            authenticated.expires_in <= 0
        ) {
            throw new Error("Authentication failed: invalid token expiration");
        }

        this.accessToken = authenticated.access_token;
        this.authenticated = authenticated;
        this.expiresAt = Date.now() + authenticated.expires_in;

        return authenticated;
    }

    }

    export default PuzzelAuthenticator;