export {};

declare global {
	interface WpApiSettings {
		root: string;
	}

	const wpApiSettings: WpApiSettings;

	type ChatRole = "user" | "assistant";

	type ChatEvent =
		| { type: "text"; content: string }
		| { type: "tool_call" }
		| { type: "done" };

	interface ChatSession {
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

	interface ChatSessionConfig {
		assistantName: string | null;
		apiRoot: string;
		fetchImpl?: typeof fetch;
	}

	interface ChatSessionFactory {
		create(assistantName: string | null): ChatSession;
	}

	interface ChatUIDependencies {
		utils: ChatUtilsApi;
		sessionFactory: ChatSessionFactory;
	}
}
