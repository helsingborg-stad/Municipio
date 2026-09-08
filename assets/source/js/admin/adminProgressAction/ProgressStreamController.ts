import { IProgressBar } from "./IProgressBar";
import { ProgressStreamSource } from "./ProgressStreamSource";

export default class ProgressStreamController {
	private disabledStates = new Map<HTMLElement, string | null>();
	private running = false;

	public constructor(
		private target: HTMLElement,
		private url: string,
		private progressBar: IProgressBar,
		private source: ProgressStreamSource,
		private errorMessage: string,
	) {}

	public start(): void {
		if (this.running) {
			return;
		}

		this.running = true;
		this.disableMatchingTriggers();
		this.progressBar.show();
		this.progressBar.update({ label: "", value: 0 });
		this.source.start({
			onMessage: (message) =>
				this.progressBar.update({ label: message, value: null }),
			onProgress: (percentage) =>
				this.progressBar.update({ label: null, value: percentage }),
			onFinish: (message) => this.finish(message),
			onError: () => this.fail(),
		});
	}

	private disableMatchingTriggers(): void {
		document.querySelectorAll<HTMLElement>("[data-js-progress-url]").forEach((element) => {
			if (element.dataset.jsProgressUrl !== this.url) {
				return;
			}

			this.disabledStates.set(element, element.getAttribute("disabled"));
			element.setAttribute("disabled", "disabled");
		});
	}

	private finish(message: string): void {
		this.progressBar.update({ label: message, value: 100 });
		this.complete();
	}

	private fail(): void {
		this.progressBar.update({ label: this.errorMessage, value: 100 });
		this.complete();
	}

	private complete(): void {
		this.source.stop();
		this.disabledStates.forEach((disabledValue, element) => {
			if (disabledValue === null) {
				element.removeAttribute("disabled");
			} else {
				element.setAttribute("disabled", disabledValue);
			}
		});
		this.disabledStates.clear();
		this.running = false;
	}
}