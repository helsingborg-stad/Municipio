/**
 * Represents optional frontend capabilities that a conversation provider may support.
 */
export interface ConversationProviderCapabilities {
	supportsStreaming: boolean;
	supportsFeedback: boolean;
	supportsSessionReset: boolean;
}

/**
 * Normalized event model emitted by conversation providers.
 */
export type ConversationProviderEvent =
	| { type: "text"; content: string }
	| { type: "activity"; name: string }
	| { type: "done" };

/**
 * Generic contract for a frontend conversation provider.
 */
export interface ConversationProvider {
	/**
	 * Sends a user message through the provider and yields normalized events.
	 *
	 * @param message - End-user message content.
	 * @returns Async stream of provider events.
	 */
	sendMessage(message: string): AsyncGenerator<ConversationProviderEvent>;

	/**
	 * Resets the provider-side conversation/session state.
	 */
	resetConversation(): void;

	/**
	 * Returns supported optional provider capabilities.
	 *
	 * @returns Provider capability map.
	 */
	getCapabilities(): ConversationProviderCapabilities;
}
