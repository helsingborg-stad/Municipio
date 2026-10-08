export {};

declare global {
	const municipioChatLocale: {
		errorMessage: string;
	};

	interface WpApiSettings {
		root: string;
	}

	const wpApiSettings: WpApiSettings;

	type ChatRole = "user" | "assistant";

	type ConversationEvent =
		| { type: "text"; content: string }
		| { type: "tool_call" }
		| { type: "done" };

	interface ConversationSession {
		ask(message: string): AsyncGenerator<ConversationEvent>;
		clearSessionForAssistant(assistantId: string): void;
	}

	interface ConversationSessionConfig {
		assistantName: string | null;
		apiRoot: string;
		persistSession?: boolean;
		fetchImpl?: typeof fetch;
	}

	interface ConversationProviderFactory {
		create(
			assistantName: string | null,
			persistSession?: boolean,
		): ConversationSession;
	}

	type ChatEvent =
		ConversationEvent;

	interface ChatSession extends ConversationSession {}

	interface ChatSessionConfig extends ConversationSessionConfig {}

	interface ChatSessionFactory extends ConversationProviderFactory {}

	interface ChatProviderFactory extends ConversationProviderFactory {}

	interface ChatProviderSession extends ConversationSession {
		ask(message: string): AsyncGenerator<ChatEvent>;
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

	interface ChatUIDependencies {
		utils: ChatUtilsApi;
		sessionFactory: ChatSessionFactory;
	}
}