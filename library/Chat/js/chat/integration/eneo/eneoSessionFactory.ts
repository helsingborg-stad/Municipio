import { EneoSession } from "./eneoSession";

export class EneoSessionFactory {
	constructor(private readonly apiRoot: string) {}

	public create(
		assistantName: string | null,
		persistSession: boolean = true,
	): EneoSession {
		return new EneoSession({
			apiRoot: this.apiRoot,
			assistantName,
			persistSession,
		});
	}
}