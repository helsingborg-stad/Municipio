interface PuzzelConfigInterface {
    getAuthenticationUrl(): string;
    getCommunicationUrl(): string;
    getContentUploadUrl(): string;
    getCustomerId(): string;
    getChatQueueKey(): string;
    getClientId(): string;
}

interface PuzzelConversationManagerInterface {
    startConversation(): Promise<void>;
    getConversationId(): string | undefined;
}

interface PuzzelAuthenticatorInterface {
    getAccessToken(): Promise<string>;
    authenticate(): Promise<AuthenticationResponse>;
}

type AuthenticationResponse = {
    access_token: string;
    refresh_token: string;
    expires_in: number;
    scope: string;
    token_type: string;
    visitor_id: string;
};