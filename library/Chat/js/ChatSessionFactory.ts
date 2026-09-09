import { ChatSession } from "./ChatSession";

export class ChatSessionFactory {
	constructor(private readonly apiRoot: string) {}

	public create(
		assistantName: string | null,
		persistSession: boolean = true,
	): ChatSession {
		return new ChatSession({
			apiRoot: this.apiRoot,
			assistantName,
			persistSession,
		});
	}
}
