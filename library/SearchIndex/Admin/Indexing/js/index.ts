import ProgressBar from "../../../../../assets/source/js/admin/eventSourceProgress/ProgressBar";
import ProgressBarWithLabel from "../../../../../assets/source/js/admin/eventSourceProgress/UIComponents/ProgressBarWithLabel";

interface SseEvent {
	type: string;
	data: string;
}

if (!customElements.get(ProgressBarWithLabel.customElementName)) {
	customElements.define(
		ProgressBarWithLabel.customElementName,
		ProgressBarWithLabel,
	);
}

export class SearchIndexingClient {
	private progressBar: ProgressBar;
	private progressElement: ProgressBarWithLabel;

	public constructor(private button: HTMLButtonElement) {
		this.progressElement = document.createElement(
			ProgressBarWithLabel.customElementName,
		) as ProgressBarWithLabel;
		this.progressElement.setAttribute("role", "status");
		this.progressElement.setAttribute("aria-live", "polite");
		this.progressElement.style.display = "block";
		this.progressElement.style.marginTop = "8px";
		this.progressBar = new ProgressBar(this.progressElement, this.button);
		this.button.addEventListener("click", () => void this.start());
	}

	public async start(): Promise<void> {
		this.button.disabled = true;
		this.progressBar.show();
		this.progressBar.update({ label: "", value: 0 });

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
			this.progressBar.update({
				label: this.button.dataset.errorMessage ?? "",
				value: 100,
			});
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
			this.progressBar.update({ label: event.data, value: null });
		}

		if (event.type === "progress") {
			this.progressBar.update({ label: null, value: Number(event.data) });
		}

		if (event.type === "finish") {
			this.progressBar.update({ label: null, value: 100 });
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
