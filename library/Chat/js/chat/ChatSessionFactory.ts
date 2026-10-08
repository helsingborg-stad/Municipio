import { AiConversationSession } from "./ChatSession";

export class AiConversationProviderFactory implements ConversationProviderFactory {
	constructor(private readonly apiRoot: string) {}

	public create(
		assistantName: string | null,
		persistSession: boolean = true,
	): ConversationSession {
		return new AiConversationSession({
			apiRoot: this.apiRoot,
			assistantName,
			persistSession,
		});
	}
}

export class ChatSessionFactory extends AiConversationProviderFactory {}
