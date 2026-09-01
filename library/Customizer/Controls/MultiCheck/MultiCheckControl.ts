import { dispatchCustomizerChange } from "../controlTypes";

export class MultiCheckControlElement extends HTMLElement {
	private readonly handleChange = (event: Event): void => {
		if (!(event.target instanceof HTMLInputElement) || event.target.type !== "checkbox") {
			return;
		}

		this.updateValue();
	};

	public connectedCallback(): void {
		this.addEventListener("change", this.handleChange);
	}

	public disconnectedCallback(): void {
		this.removeEventListener("change", this.handleChange);
	}

	private updateValue(): void {
		const valueInput = this.querySelector<HTMLInputElement>(".municipio-multicheck-value");
		const selectedValues = Array.from(this.querySelectorAll<HTMLInputElement>('input[type="checkbox"]:checked'))
			.map((checkbox) => checkbox.value);

		if (!valueInput) {
			return;
		}

		valueInput.value = JSON.stringify(selectedValues);
		dispatchCustomizerChange(valueInput);
	}
}