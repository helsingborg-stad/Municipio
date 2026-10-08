import type { ConversationProvider } from "./conversationProvider";

/**
 * Generic factory contract for frontend conversation providers.
 */
interface ConversationProviderFactory {
	/**
	 * Creates a provider instance for a specific assistant context.
	 *
	 * @param assistantName - Logical assistant identifier.
	 * @param persistSession - Whether session persistence should be enabled.
	 * @returns A configured conversation provider.
	 */
	create(
		assistantName: string | null,
		persistSession?: boolean,
	): ConversationProvider;
}

export default ConversationProviderFactory;
