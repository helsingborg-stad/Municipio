interface ConversationStorageManagerInterface {
    getConversationId(): string | null;
    getAccessToken(): string | null;
    getLocalStorage(): PuzzelLocalStorageData | null;
    setLocalStorage(conversationResponse: RouteConversationResponse, authentication: AuthenticationData): void;
    clearLocalStorage(): void;
    getConversationData(): RouteConversationResponse | null;
    getAuthenticationData(): AuthenticationData | null;
}