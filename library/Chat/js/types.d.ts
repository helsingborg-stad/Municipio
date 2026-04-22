export {};

declare global {
	interface WpApiSettings {
		root: string;
	}

	interface MunicipioChatStrings {
		sending: string;
		writing: string;
		usingTools: string;
		error: string;
	}

	const wpApiSettings: WpApiSettings;
	const municipioChatStrings: MunicipioChatStrings;

	type ChatRole = "user" | "assistant";

	interface ChatSessionState {
		isMessagePending: boolean;
	}

	interface ChatSessionEvents {
		onMessageAdded: (role: ChatRole) => void;
		onUserMessageText: (text: string) => void;
		onAssistantMessageText: (html: string) => void;
		onStateChanged: (state: ChatSessionState) => void;
		onStatusTextChanged: (status: string | null) => void;
	}

	interface ChatSession {
		ask(message: string): Promise<void>;
		clear(): void;
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
		assistantId: string | null;
		apiRoot: string;
		strings: MunicipioChatStrings;
		events: ChatSessionEvents;
		utils: Pick<ChatUtilsApi, "renderMarkdown">;
		fetchImpl?: typeof fetch;
	}

	type CreateChatSession = (config: ChatSessionConfig) => ChatSession;

	interface ChatUIDependencies {
		utils: ChatUtilsApi;
		sessionFactory: CreateChatSession;
		apiRoot: string;
		strings: MunicipioChatStrings;
	}
}
