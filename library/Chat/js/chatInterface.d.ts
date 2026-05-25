interface ChatInterface {
    init(): void;
    createNewChatSession(assistantId: string | null): void;
}