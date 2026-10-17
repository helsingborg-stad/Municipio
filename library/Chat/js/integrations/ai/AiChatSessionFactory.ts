import { AiChatSession } from "./AiChatSession";

export class AiChatSessionFactory {
	constructor(private readonly apiRoot: string) {}

	public create(
		assistantName: string | null,
		persistSession: boolean = true,
	): AiChatSession {
		return new AiChatSession({
			apiRoot: this.apiRoot,
			assistantName,
			persistSession,
		});
	}
}
