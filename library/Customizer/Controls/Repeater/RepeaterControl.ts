import { dispatchCustomizerChange, readJsonObject } from "../controlTypes";

type RepeaterField = {
	default?: string;
	label?: string;
	type?: string;
};

export class RepeaterControlElement extends HTMLElement {
	private readonly handleClick = (event: Event): void => {
		if (!(event.target instanceof HTMLElement)) {
			return;
		}

		if (event.target.classList.contains("municipio-repeater-add")) {
			this.addRow();
			return;
		}

		if (event.target.classList.contains("municipio-repeater-remove")) {
			event.target.closest(".municipio-repeater-row")?.remove();
			this.updateValue();
		}
	};

	private readonly handleInput = (event: Event): void => {
		if (!(event.target instanceof HTMLInputElement) || !event.target.dataset.repeaterKey) {
			return;
		}

		this.updateValue();
	};

	public connectedCallback(): void {
		this.addEventListener("click", this.handleClick);
		this.addEventListener("input", this.handleInput);
	}

	public disconnectedCallback(): void {
		this.removeEventListener("click", this.handleClick);
		this.removeEventListener("input", this.handleInput);
	}

	private addRow(): void {
		this.querySelector(".municipio-repeater-rows")?.appendChild(this.createRow());
		this.updateValue();
	}

	private createRow(): HTMLElement {
		const row = document.createElement("div");
		row.className = "municipio-repeater-row";

		Object.entries(this.getFields()).forEach(([fieldKey, field]) => {
			row.appendChild(this.createField(fieldKey, field));
		});

		const removeButton = document.createElement("button");
		removeButton.type = "button";
		removeButton.className = "button-link-delete municipio-repeater-remove";
		removeButton.textContent = "Remove";
		row.appendChild(removeButton);

		return row;
	}

	private createField(fieldKey: string, field: RepeaterField): HTMLElement {
		const label = document.createElement("label");
		const labelText = document.createElement("span");
		const input = document.createElement("input");

		label.className = "municipio-repeater-field";
		labelText.textContent = field.label ?? fieldKey;
		input.type = field.type ?? "text";
		input.dataset.repeaterKey = fieldKey;
		input.value = field.default ?? "";

		label.appendChild(labelText);
		label.appendChild(input);

		return label;
	}

	private getFields(): Record<string, RepeaterField> {
		return readJsonObject(this.dataset.fields) as Record<string, RepeaterField>;
	}

	private updateValue(): void {
		const valueInput = this.querySelector<HTMLInputElement>(".municipio-repeater-value");
		const rows = Array.from(this.querySelectorAll<HTMLElement>(".municipio-repeater-row"))
			.map((row) => Object.fromEntries(
				Array.from(row.querySelectorAll<HTMLInputElement>("[data-repeater-key]"))
					.map((input) => [input.dataset.repeaterKey ?? "", input.value])
					.filter(([key]) => key !== ""),
			));

		if (!valueInput) {
			return;
		}

		valueInput.value = JSON.stringify(rows);
		dispatchCustomizerChange(valueInput);
	}
}