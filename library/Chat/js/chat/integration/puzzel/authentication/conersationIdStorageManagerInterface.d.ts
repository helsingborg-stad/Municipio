interface ConversationIdStorageManagerInterface {
    getConversationId(): string | null;
    getConversation(): RouteConversationResponse | null;
    setConversationId(conversationResponse: RouteConversationResponse): void;
}