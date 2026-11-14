interface ConversationStorageManagerInterface {
    getConversationId(): string | null;
    getAccessToken(): string | null;
    getLocalStorage(): PuzzelLocalStorageData | null;
    setLocalStorage(conversationResponse: RouteConversationResponse, authentication: AuthenticationData): void;
    clearLocalStorage(): void;
    updateLastActivity(): void;
    getConversationData(): RouteConversationResponse | null;
    getAuthenticationData(): AuthenticationData | null;
}