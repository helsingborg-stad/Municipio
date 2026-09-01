import { dispatchCustomizerChange } from "../controlTypes";

export class ColorChoiceControlElement extends HTMLElement {
	private readonly handleClick = (event: Event): void => {
		if (!(event.target instanceof Element)) {
			return;
		}

		const swatchButton = event.target.closest<HTMLButtonElement>(
			".municipio-color-choice-swatch",
		);
		if (!(swatchButton instanceof HTMLButtonElement)) {
			return;
		}

		const nextValue = swatchButton.dataset.value;
		if (!nextValue) {
			return;
		}

		const valueInput = this.querySelector<HTMLInputElement>(
			".municipio-color-choice-value",
		);
		if (!(valueInput instanceof HTMLInputElement)) {
			return;
		}

		valueInput.value = nextValue;
		this.querySelectorAll<HTMLButtonElement>(
			".municipio-color-choice-swatch",
		).forEach((button) => {
			button.classList.remove("is-active");
		});
		swatchButton.classList.add("is-active");

		dispatchCustomizerChange(valueInput);
	};

	public connectedCallback(): void {
		this.addEventListener("click", this.handleClick);
	}

	public disconnectedCallback(): void {
		this.removeEventListener("click", this.handleClick);
	}
}
