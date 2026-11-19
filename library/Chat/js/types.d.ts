export {};

declare global {
	interface MunicipioChatLocale {
		errorMessage: string;
		queuePosition: string;
		agentLeftChatInactive: string;
		agentJoinedChat: string;
		agentLeftChat: string;
	}

	const municipioChatLocale: MunicipioChatLocale;

	interface WpApiSettings {
		root: string;
		nonce?: string;
	}

	const wpApiSettings: WpApiSettings;

	type AvailableAssistants = {
		[key: string]: string;
	}

	type ChatRole = "user" | "assistant";

	type ChatEvent =
		| { type: "text"; content: string }
		| { type: "tool_call" }
		| { type: "done" };

	interface ChatSession {
		ask(message: string): AsyncGenerator<ChatEvent>;
		clearSessionForAssistant(assistantId: string): void;
	}

	interface ChatSessionConfig {
		assistantName: string | null;
		apiRoot: string;
		persistSession?: boolean;
		fetchImpl?: typeof fetch;
	}

	interface ChatIntegrationFactory {
		async create(chat: any, uiManager: ChatUiManagerInterface, name: string): Promise<ChatIntegrationInterface|false>;
	}

	interface ChatInitializerInterface {
		init(chat: any): Promise<boolean>;
	}

	interface ChatIntegrationInterface {
		init(): Promise<this|false>;
	}
}