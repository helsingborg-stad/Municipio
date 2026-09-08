import {
	ProgressStreamCallbacks,
	ProgressStreamSource,
} from "./ProgressStreamSource";

export default class EventSourceStreamSource implements ProgressStreamSource {
	private source: EventSource | null = null;

	public constructor(private url: string) {}

	public start(callbacks: ProgressStreamCallbacks): void {
		this.source = new EventSource(this.url);
		this.source.addEventListener("message", (event) =>
			callbacks.onMessage((event as MessageEvent).data),
		);
		this.source.addEventListener("progress", (event) =>
			callbacks.onProgress(Number((event as MessageEvent).data)),
		);
		this.source.addEventListener("finish", (event) =>
			callbacks.onFinish((event as MessageEvent).data),
		);
		this.source.addEventListener("error", () => callbacks.onError());
	}

	public stop(): void {
		this.source?.close();
		this.source = null;
	}
}