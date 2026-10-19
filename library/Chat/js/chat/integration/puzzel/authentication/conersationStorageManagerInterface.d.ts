interface ConversationStorageManagerInterface {
    getConversationId(): string | null;
    getAccessToken(): string | null;
    getLocalStorage(): PuzzelLocalStorageData | null;
    setLocalStorage(conversationResponseS: RouteConversationResponse, accessToken: string): void;
}