import { ChatSession } from "./ChatSession";

export class ChatSessionFactory {
	constructor(private readonly apiRoot: string) {}

	public create(assistantName: string | null): ChatSession {
		return new ChatSession({ apiRoot: this.apiRoot, assistantName });
	}
}
