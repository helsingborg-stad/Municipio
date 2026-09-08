export interface ProgressStreamCallbacks {
	onMessage(message: string): void;
	onProgress(percentage: number): void;
	onFinish(message: string): void;
	onError(): void;
}

export interface ProgressStreamSource {
	start(callbacks: ProgressStreamCallbacks): void;
	stop(): void;
}