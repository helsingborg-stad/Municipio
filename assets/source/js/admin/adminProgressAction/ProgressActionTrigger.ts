import EventSourceStreamSource from "./EventSourceStreamSource";
import FetchStreamSource from "./FetchStreamSource";
import { IProgressBar } from "./IProgressBar";
import ProgressBar from "./ProgressBar";
import ProgressStreamController from "./ProgressStreamController";
import { ProgressStreamSource } from "./ProgressStreamSource";
import ProgressBarWithLabel from "./UIComponents/ProgressBarWithLabel";

export default class ProgressActionTrigger {
	private controller: ProgressStreamController;

	public constructor(private triggerElement: HTMLElement) {
		const url = triggerElement.dataset.jsProgressUrl ?? "";
		this.controller = new ProgressStreamController(
			triggerElement,
			url,
			this.createProgressBar(),
			this.createSource(url),
			triggerElement.dataset.jsProgressErrorMessage ?? "An error occurred",
		);
		triggerElement.addEventListener("click", this.handleClick.bind(this));
	}

	private handleClick(event: Event): void {
		event.preventDefault();
		this.controller.start();
	}

	private createProgressBar(): IProgressBar {
		const element = document.createElement(
			ProgressBarWithLabel.customElementName,
		) as ProgressBarWithLabel;
		element.setAttribute("role", "status");
		element.setAttribute("aria-live", "polite");
		element.style.display = "block";
		element.style.marginTop = "8px";
		return new ProgressBar(element, this.triggerElement);
	}

	private createSource(url: string): ProgressStreamSource {
		if (this.triggerElement.dataset.jsProgressMethod?.toLowerCase() === "post") {
			return new FetchStreamSource(
				url,
				this.triggerElement.dataset.jsProgressNonce ?? "",
			);
		}

		return new EventSourceStreamSource(url);
	}
}