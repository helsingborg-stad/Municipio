interface ChatInterface {
	init(): void;
	createNewChatSession(): void;
}

interface ChatIntegration {
	setup(): void;
}
