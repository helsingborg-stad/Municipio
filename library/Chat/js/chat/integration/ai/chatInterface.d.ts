interface ChatInterface implements ChatIntegrationInterface {
	createNewChatSession(): void;
}

type CreateAiFunction = (
    chat: unknown,
    actionFactory: ChatActionFactoryInterface,
) => ChatIntegrationInterface;