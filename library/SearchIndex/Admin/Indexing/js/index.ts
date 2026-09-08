interface SseEvent {
	type: string;
	data: string;
}

export class SearchIndexingClient {
	private progressContainer: HTMLDivElement;
	private progressElement: HTMLProgressElement;
	private statusElement: HTMLElement;

	public constructor(private button: HTMLButtonElement) {
		this.progressContainer = document.createElement("div");
		this.progressElement = document.createElement("progress");
		this.statusElement = document.createElement("p");
		this.createProgressUi();
		this.button.addEventListener("click", () => void this.start());
	}

	private createProgressUi(): void {
		this.progressElement.max = 100;
		this.progressElement.value = 0;
		this.statusElement.setAttribute("role", "status");
		this.statusElement.setAttribute("aria-live", "polite");
		this.progressContainer.hidden = true;
		this.progressContainer.append(this.progressElement, this.statusElement);
		this.button.insertAdjacentElement("afterend", this.progressContainer);
	}

	public async start(): Promise<void> {
		this.button.disabled = true;
		this.progressContainer.hidden = false;
		this.statusElement.textContent = "";

		const requestBody = new URLSearchParams({
			action: "municipio_search_index_build",
			_ajax_nonce: this.button.dataset.nonce ?? "",
		});

		try {
			const response = await fetch(this.button.dataset.endpoint ?? "", {
				method: "POST",
				credentials: "same-origin",
				headers: { "Content-Type": "application/x-www-form-urlencoded" },
				body: requestBody,
			});

			if (!response.ok || !response.body) {
				throw new Error("Indexing request failed");
			}

			await this.consumeStream(response.body);
		} catch {
			this.statusElement.textContent = this.button.dataset.errorMessage ?? "";
		} finally {
			this.button.disabled = false;
		}
	}

	private async consumeStream(
		stream: ReadableStream<Uint8Array>,
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
				if (this.handleEvent(this.parseEvent(block))) {
					await reader.cancel();
					return;
				}
			}

			if (done) {
				if (buffer.trim() !== "") {
					this.handleEvent(this.parseEvent(buffer));
				}
				break;
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

	private handleEvent(event: SseEvent): boolean {
		if (event.type === "message" || event.type === "finish") {
			this.statusElement.textContent = event.data;
		}

		if (event.type === "progress") {
			this.progressElement.value = Number(event.data);
		}

		if (event.type === "finish") {
			this.progressElement.value = 100;
			return true;
		}

		return false;
	}
}

document
	.querySelectorAll<HTMLButtonElement>("[data-search-index-build]")
	.forEach((button) => {
		new SearchIndexingClient(button);
	});
