import { dispatchCustomizerChange, getJQuery } from "../controlTypes";

export class MultiColorControlElement extends HTMLElement {
	private readonly handleInput = (event: Event): void => {
		if (!(event.target instanceof HTMLInputElement) || !event.target.classList.contains("municipio-multicolor-input")) {
			return;
		}

		this.updateValue();
	};

	public connectedCallback(): void {
		this.addEventListener("input", this.handleInput);
		this.initializeColorPickers();
	}

	public disconnectedCallback(): void {
		this.removeEventListener("input", this.handleInput);
	}

	private initializeColorPickers(): void {
		const jquery = getJQuery();

		if (!jquery?.fn?.wpColorPicker) {
			return;
		}

		this.querySelectorAll<HTMLInputElement>(".municipio-multicolor-input").forEach((input) => {
			jquery(input).wpColorPicker?.({
				change: () => window.setTimeout(() => this.updateValue(), 0),
				clear: () => window.setTimeout(() => this.updateValue(), 0),
			});
		});
	}

	private updateValue(): void {
		const valueInput = this.querySelector<HTMLInputElement>(".municipio-multicolor-value");
		const values = Object.fromEntries(
			Array.from(this.querySelectorAll<HTMLInputElement>(".municipio-multicolor-input"))
				.map((input) => [input.dataset.choice ?? "", input.value])
				.filter(([key]) => key !== ""),
		);

		if (!valueInput) {
			return;
		}

		valueInput.value = JSON.stringify(values);
		dispatchCustomizerChange(valueInput);
	}
}