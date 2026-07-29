import { dispatchCustomizerChange } from "../controlTypes";

export class MultiColorControlElement extends HTMLElement {
	private readonly handleInput = (event: Event): void => {
		if (
			!(event.target instanceof HTMLInputElement) ||
			!event.target.classList.contains("municipio-multicolor-input")
		) {
			return;
		}

		this.updateValue();
	};

	private readonly handleSwatchClick = (event: Event): void => {
		if (!(event.target instanceof Element)) {
			return;
		}

		const swatchButton = event.target.closest<HTMLButtonElement>(
			".municipio-multicolor-swatch",
		);
		if (!(swatchButton instanceof HTMLButtonElement)) {
			return;
		}

		const choiceKey = swatchButton.dataset.choice;
		const nextColor = swatchButton.dataset.background;
		if (!choiceKey || !nextColor) {
			return;
		}

		const input = this.querySelector<HTMLInputElement>(
			`.municipio-multicolor-input[data-choice="${choiceKey}"]`,
		);
		if (!(input instanceof HTMLInputElement)) {
			return;
		}

		input.value = nextColor;

		const swatchContainer = swatchButton.closest(
			".municipio-multicolor-swatches",
		);
		swatchContainer
			?.querySelectorAll<HTMLButtonElement>(".municipio-multicolor-swatch")
			.forEach((button) => {
				button.classList.remove("is-active");
			});
		swatchButton.classList.add("is-active");

		this.updateValue();
	};

	public connectedCallback(): void {
		this.addEventListener("input", this.handleInput);
		this.addEventListener("click", this.handleSwatchClick);
	}

	public disconnectedCallback(): void {
		this.removeEventListener("input", this.handleInput);
		this.removeEventListener("click", this.handleSwatchClick);
	}

	private updateValue(): void {
		const valueInput = this.querySelector<HTMLInputElement>(
			".municipio-multicolor-value",
		);
		const values = Object.fromEntries(
			Array.from(
				this.querySelectorAll<HTMLInputElement>(".municipio-multicolor-input"),
			)
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
