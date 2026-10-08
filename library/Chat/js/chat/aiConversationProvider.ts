import type {
	ConversationProvider,
	ConversationProviderCapabilities,
	ConversationProviderEvent,
} from "./conversationProvider";

/**
 * Adapter that maps the current AI session implementation to the generic
 * conversation provider contract.
 */
class AiConversationProvider implements ConversationProvider {
	constructor(
		private readonly session: ChatSession,
		private readonly assistantName: string | null,
	) {}

	public async *sendMessage(
		message: string,
	): AsyncGenerator<ConversationProviderEvent> {
		for await (const event of this.session.ask(message)) {
			switch (event.type) {
				case "text":
					yield event;
					break;
				case "tool_call":
					yield { type: "activity", name: "tool_call" };
					break;
				case "done":
					yield event;
					break;
			}
		}
	}

	public resetConversation(): void {
		this.session.clearSessionForAssistant(this.assistantName ?? "");
	}

	public getCapabilities(): ConversationProviderCapabilities {
		return {
			supportsStreaming: true,
			supportsFeedback: true,
			supportsSessionReset: true,
		};
	}
}

export default AiConversationProvider;
