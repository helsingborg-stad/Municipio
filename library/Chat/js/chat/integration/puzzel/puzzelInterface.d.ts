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
    listenForMetaData(callback: (data: PuzzelMetaDataEvent) => void): Promise<void>;
    listenForConversation(callback: (data: PuzzelConversationEvent) => void): Promise<void>;
    sendUserMessage(content: string): Promise<void>;
    sendUserTypingEvent(): Promise<void>;
    getCurrentConversation(): Promise<PuzzelConversationEvent[]>;
}

interface PuzzelAuthenticatorInterface {
    getAccessToken(): Promise<string>;
    authenticate(): Promise<AuthenticationData>;
}

type AuthenticationResponse = {
    access_token: string;
    refresh_token: string;
    expires_in: number;
    scope: string;
    token_type: string;
    visitor_id: string;
};

type AuthenticationData = AuthenticationResponse & {
    expires: number;
};

type RouteConversationResponse = {
    conversationId: string;
    iqSessionId: string;
    requestId: number;
    result: string | number;
};

type PuzzelLocalStorageData = RouteConversationResponse & {
    conversationData: RouteConversationResponse;
    authenticationData: AuthenticationData;
};

type PuzzelMetaDataEvent = {
    eventId?: number;
    createdAt?: string;
    senderId?: string;
    senderRole?: string;
    senderName?: string;
    type?: string;
    [key: string]: unknown;
};

type PuzzelConversationEvent = {
    eventId?: number;
    createdAt?: string;
    senderId?: string;
    senderRole?: string;
    senderName?: string;
    type?: string;
    message?: string;
    [key: string]: unknown;
};