export type AiChatEvent =
	| { type: "text"; content: string }
	| { type: "tool_call" }
	| { type: "done" };

export interface AiChatSession {
	ask(message: string): AsyncGenerator<AiChatEvent>;
	clearSessionForAssistant(assistantId: string): void;
}

export interface AiChatSessionConfig {
	assistantName: string | null;
	apiRoot: string;
	persistSession?: boolean;
	fetchImpl?: typeof fetch;
}
