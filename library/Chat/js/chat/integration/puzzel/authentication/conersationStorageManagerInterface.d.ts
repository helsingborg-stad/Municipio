interface ConversationStorageManagerInterface {
    getConversationId(): string | null;
    getAccessToken(): string | null;
    getLocalStorage(): PuzzelLocalStorageData | null;
    setLocalStorage(conversationResponse: RouteConversationResponse, authentication: AuthenticationResponse): void;
    getConversationData(): RouteConversationResponse | null;
    getAuthenticationData(): AuthenticationResponse | null;
}