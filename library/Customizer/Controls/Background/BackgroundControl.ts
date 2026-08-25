import { dispatchCustomizerChange } from "../controlTypes";

export class BackgroundControlElement extends HTMLElement {
	private readonly handleChange = (event: Event): void => {
		if (!(event.target instanceof HTMLInputElement || event.target instanceof HTMLSelectElement)) {
			return;
		}

		if (!event.target.dataset.backgroundKey) {
			return;
		}

		this.updateValue();
	};

	public connectedCallback(): void {
		this.addEventListener("input", this.handleChange);
		this.addEventListener("change", this.handleChange);
	}

	public disconnectedCallback(): void {
		this.removeEventListener("input", this.handleChange);
		this.removeEventListener("change", this.handleChange);
	}

	private updateValue(): void {
		const valueInput = this.querySelector<HTMLInputElement>(".municipio-background-value");
		const values = Object.fromEntries(
			Array.from(this.querySelectorAll<HTMLInputElement | HTMLSelectElement>("[data-background-key]"))
				.map((input) => [input.dataset.backgroundKey ?? "", input.value])
				.filter(([key]) => key !== ""),
		);

		if (!valueInput) {
			return;
		}

		valueInput.value = JSON.stringify(values);
		dispatchCustomizerChange(valueInput);
	}
}