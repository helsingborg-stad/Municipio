import AiConversationProvider from "./aiConversationProvider";
import type ConversationProviderFactory from "./conversationProviderFactory";

/**
 * Factory that creates AI-backed conversation providers.
 */
class AiConversationProviderFactory implements ConversationProviderFactory {
	constructor(private readonly sessionFactory: ChatSessionFactory) {}

	public create(
		assistantName: string | null,
		persistSession: boolean = true,
	) {
		const session = this.sessionFactory.create(assistantName, persistSession);
		return new AiConversationProvider(session, assistantName);
	}
}

export default AiConversationProviderFactory;
