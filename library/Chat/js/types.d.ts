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

	interface ChatUtilsApi {
		unsafeGetElement<T extends HTMLElement = HTMLElement>(id: string): T | null;
		safeGetElement<T extends HTMLElement = HTMLElement>(id: string): T;
		safeQueryElement<T extends Element = HTMLElement>(
			selector: string,
			parent?: Element | DocumentFragment | null,
		): T;
		appendMessageElement(
			role: ChatRole,
			parent: Element,
			userTemplate: HTMLTemplateElement,
			assistantTemplate: HTMLTemplateElement,
		): Element;
		renderMarkdown(text: string): string;
	}

	interface ChatSessionConfig {
		assistantName: string | null;
		apiRoot: string;
		persistSession?: boolean;
		fetchImpl?: typeof fetch;
	}

	interface ChatSessionFactory {
		create(assistantName: string | null, persistSession?: boolean): ChatSession;
	}

	interface ChatUIDependencies {
		utils: ChatUtilsApi;
		sessionFactory: ChatSessionFactory;
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