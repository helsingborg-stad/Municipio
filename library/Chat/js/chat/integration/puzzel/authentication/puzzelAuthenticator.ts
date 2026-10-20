class PuzzelAuthenticator implements PuzzelAuthenticatorInterface {
    private authenticationData: AuthenticationData | null = null;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private conversationStorageManager: ConversationStorageManagerInterface,
    ) {}

    public async getAccessToken(): Promise<string> {
        const isTokenValid =
            this.authenticationData?.access_token !== null &&
            this.authenticationData?.expires !== undefined &&
            Date.now() < this.authenticationData.expires;

        if (!isTokenValid) {
            await this.authenticate();
        }

        if (!this.authenticationData?.access_token) {
            throw new Error("Authentication failed: missing access token");
        }

        return this.authenticationData.access_token;
    }

    public async authenticate(): Promise<AuthenticationData> {
        this.authenticationData = this.conversationStorageManager.getAuthenticationData() ?? null;

        if (this.isValidToken()) {
            return this.authenticationData!;
        }

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
                    tenant_id: this.puzzelConfig.getCustomerId(),
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

        const authenticationData: AuthenticationData = {
            ...authenticated,
            expires: Date.now() + (authenticated.expires_in * 1000),
        };

        this.authenticationData = authenticationData;

        return authenticationData;
    }

    private isValidToken(): boolean {
        return (
            this.authenticationData !== null &&
            this.authenticationData.access_token !== undefined &&
            this.authenticationData.expires !== undefined &&
            Date.now() < this.authenticationData.expires
        );
    }
}

export default PuzzelAuthenticator;