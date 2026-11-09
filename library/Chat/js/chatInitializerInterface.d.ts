type ChatIntegrationName = "eneo" | "puzzel" | "null";

interface ChatIntegrationFactoryModule {
	default: new () => ChatIntegrationFactory;
}

type ChatIntegrationFactoryLoaders = Record<
	ChatIntegrationName,
	() => Promise<ChatIntegrationFactoryModule>
>;
