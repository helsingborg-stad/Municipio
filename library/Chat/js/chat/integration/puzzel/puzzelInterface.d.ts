interface PuzzelConfigInterface {
    getAuthenticationUrl(): string;
    getCommunicationUrl(): string;
    getContentUploadUrl(): string;
    getCustomerId(): string;
    getChatQueueKey(): string;
    getClientId(): string;
}

interface PuzzelConversationManagerInterface {
    startConversation(): Promise<RouteConversationResponse>;
    getConversationId(): string;
    sendUserJoinEventToConversation(): Promise<void>;
    listenForMetaData(callback: (data: any) => void): Promise<void>;
    listenForConversation(callback: (data: any) => void): Promise<void>;
    sendUserMessage(content: string): Promise<void>;
    sendUserTypingEvent(): Promise<void>;
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

type RouteConversationResponse = {
    conversationId: string;
    iqSessionId: string;
    requestId: number;
    result: string | number;
};