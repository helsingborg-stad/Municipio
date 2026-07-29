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
		const palettes = this.getPalettes();

		if (!jquery?.fn?.wpColorPicker) {
			return;
		}

		this.querySelectorAll<HTMLInputElement>(".municipio-multicolor-input").forEach((input) => {
			jquery(input).wpColorPicker?.({
				palettes,
				change: () => window.setTimeout(() => this.updateValue(), 0),
				clear: () => window.setTimeout(() => this.updateValue(), 0),
			});
		});
	}

	private getPalettes(): string[] {
		const rawValue = this.dataset.palettes;

		if (!rawValue) {
			return [];
		}

		try {
			const parsedValue = JSON.parse(rawValue);
			if (!Array.isArray(parsedValue)) {
				return [];
			}

			return parsedValue.filter((value): value is string => typeof value === "string" && value.trim().length > 0);
		} catch {
			return [];
		}
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