const SSE_CONTENT_TYPE = "text/event-stream";
const CHAT_API_ENDPOINT = "municipio/v1/chat";

export class ChatSession {
	private readonly fetchFn: typeof fetch;
	private sessionId: string | null = null;

	constructor(private readonly config: ChatSessionConfig) {
		this.fetchFn = config.fetchImpl ?? fetch.bind(globalThis);
	}

	public async *ask(message: string): AsyncGenerator<ChatEvent> {
		const trimmedMessage = message.trim();
		if (trimmedMessage.length === 0) return;

		const response = await this.postMessage(trimmedMessage);
		await this.assertSseResponse(response);
		yield* this.consumeSseStream(response);
	}

	private async postMessage(message: string): Promise<Response> {
		const { apiRoot, assistantId } = this.config;
		console.log("Posting message to chat API...", { message, sessionId: this.sessionId, assistantId });
		return this.fetchFn(`${apiRoot}${CHAT_API_ENDPOINT}`, {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({
				message,
				session_id: this.sessionId,
				assistant_id: assistantId,
			}),
		});
	}

	private async assertSseResponse(response: Response): Promise<void> {
		const contentType = response.headers.get("Content-Type") ?? "";
		if (response.ok && contentType.includes(SSE_CONTENT_TYPE)) return;
		const body = await response.text();
		throw new Error(this.parseErrorMessage(body));
	}

	private async *consumeSseStream(response: Response): AsyncGenerator<ChatEvent> {
		if (!response.body) throw new Error("Response has no body");

		const reader = response.body.getReader();
		const decoder = new TextDecoder("utf-8");
		let buffer = "";
		let eventType = "";
		let accumulatedText = "";

		while (true) {
			const { done, value } = await reader.read();
			if (done) break;

			buffer += decoder.decode(value, { stream: true });
			const lines = buffer.split("\n");
			buffer = lines.pop() ?? "";

			for (const line of lines) {
				const result = this.processSseLine(line, eventType, accumulatedText);
				eventType = result.eventType;
				accumulatedText = result.accumulatedText;
				if (result.event !== null) {
					yield result.event;
				}
			}
		}

		yield { type: "done" };
	}

	private processSseLine(
		line: string,
		eventType: string,
		accumulatedText: string,
	): { eventType: string; accumulatedText: string; event: ChatEvent | null } {
		if (line === "") return { eventType: "", accumulatedText, event: null };

		if (line.startsWith("event: ")) {
			return { eventType: line.slice(7).trim(), accumulatedText, event: null };
		}

		if (line.startsWith("data: ")) {
			const payload = line.slice(6).trim();
			try {
				return this.handleSseEvent(eventType, payload, accumulatedText);
			} catch (error) {
				throw new Error(
					`Failed to handle SSE event "${eventType}": ${payload}. Error: ${error}`,
				);
			}
		}

		return { eventType, accumulatedText, event: null };
	}

	private handleSseEvent(
		eventType: string,
		payload: string,
		accumulatedText: string,
	): { eventType: string; accumulatedText: string; event: ChatEvent | null } {
		const data = JSON.parse(payload);

		switch (eventType) {
			case "first_chunk":
				this.sessionId = data.session_id;
				return { eventType, accumulatedText, event: null };
			case "text":
				accumulatedText += data.answer;
				return { eventType, accumulatedText, event: { type: "text", content: accumulatedText } };
			case "tool_call":
				return { eventType, accumulatedText, event: { type: "tool_call" } };
			case "error":
				throw new Error(`Chat error: ${payload}`);
			default:
				return { eventType, accumulatedText, event: null };
		}
	}

	private parseErrorMessage(rawBody: string): string {
		try {
			const parsed = JSON.parse(rawBody) as {
				message?: unknown;
				error?: unknown;
			};
			if (typeof parsed.message === "string" && parsed.message.length > 0) {
				return parsed.message;
			}
			if (typeof parsed.error === "string" && parsed.error.length > 0) {
				return parsed.error;
			}
		} catch {
			// Fall through to raw body
		}
		return rawBody;
	}
}
