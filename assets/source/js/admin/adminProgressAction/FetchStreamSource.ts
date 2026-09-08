import {
	ProgressStreamCallbacks,
	ProgressStreamSource,
} from "./ProgressStreamSource";

interface SseEvent {
	type: string;
	data: string;
}

export default class FetchStreamSource implements ProgressStreamSource {
	private abortController: AbortController | null = null;

	public constructor(
		private url: string,
		private nonce: string,
	) {}

	public start(callbacks: ProgressStreamCallbacks): void {
		this.abortController = new AbortController();
		void this.consume(callbacks);
	}

	public stop(): void {
		this.abortController?.abort();
		this.abortController = null;
	}

	private async consume(callbacks: ProgressStreamCallbacks): Promise<void> {
		try {
			const response = await fetch(this.url, {
				method: "POST",
				credentials: "same-origin",
				headers: { "Content-Type": "application/x-www-form-urlencoded" },
				body: new URLSearchParams({ _ajax_nonce: this.nonce }),
				signal: this.abortController?.signal,
			});

			if (!response.ok || !response.body) {
				throw new Error("Progress request failed");
			}

			await this.consumeStream(response.body, callbacks);
		} catch (error) {
			if (!(error instanceof DOMException && error.name === "AbortError")) {
				callbacks.onError();
			}
		}
	}

	private async consumeStream(
		stream: ReadableStream<Uint8Array>,
		callbacks: ProgressStreamCallbacks,
	): Promise<void> {
		const reader = stream.getReader();
		const decoder = new TextDecoder();
		let buffer = "";

		while (true) {
			const { done, value } = await reader.read();
			buffer += decoder.decode(value, { stream: !done }).replace(/\r\n/g, "\n");
			const blocks = buffer.split("\n\n");
			buffer = blocks.pop() ?? "";

			for (const block of blocks) {
				if (this.handleEvent(this.parseEvent(block), callbacks)) {
					await reader.cancel();
					return;
				}
			}

			if (done) {
				if (buffer.trim() !== "") {
					this.handleEvent(this.parseEvent(buffer), callbacks);
				}
				return;
			}
		}
	}

	private parseEvent(block: string): SseEvent {
		let type = "message";
		const data: string[] = [];

		block.split("\n").forEach((line) => {
			if (line.startsWith("event:")) {
				type = line.slice(6).trim();
			} else if (line.startsWith("data:")) {
				data.push(line.slice(5).trimStart());
			}
		});

		return { type, data: data.join("\n") };
	}

	private handleEvent(
		event: SseEvent,
		callbacks: ProgressStreamCallbacks,
	): boolean {
		if (event.type === "message") {
			callbacks.onMessage(event.data);
		} else if (event.type === "progress") {
			callbacks.onProgress(Number(event.data));
		} else if (event.type === "finish") {
			callbacks.onFinish(event.data);
			return true;
		} else if (event.type === "error") {
			callbacks.onError();
			return true;
		}

		return false;
	}
}