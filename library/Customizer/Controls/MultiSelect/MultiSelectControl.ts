import { dispatchCustomizerChange, translate } from "../controlTypes";

export class MultiSelectControlElement extends HTMLElement {
	private readonly handleClick = (event: Event): void => {
		if (!(event.target instanceof HTMLElement)) {
			return;
		}

		if (event.target.classList.contains("municipio-multiselect-picker__add")) {
			this.addSelectedValue();
			return;
		}

		if (event.target.classList.contains("municipio-multiselect-pill__remove")) {
			event.target.closest(".municipio-multiselect-pill")?.remove();
			this.updateValue();
		}
	};

	private readonly handleChange = (event: Event): void => {
		if (
			!(event.target instanceof HTMLSelectElement) ||
			!event.target.classList.contains("municipio-multiselect-picker__select")
		) {
			return;
		}

		this.addSelectedValue();
	};

	public connectedCallback(): void {
		this.addEventListener("click", this.handleClick);
		this.addEventListener("change", this.handleChange);
		this.updatePickerOptions();
	}

	public disconnectedCallback(): void {
		this.removeEventListener("click", this.handleClick);
		this.removeEventListener("change", this.handleChange);
	}

	private addSelectedValue(): void {
		const select = this.querySelector<HTMLSelectElement>(
			".municipio-multiselect-picker__select",
		);
		const list = this.querySelector(".municipio-multiselect-pills");
		const option = select?.selectedOptions[0];

		if (
			!select ||
			!list ||
			!option ||
			option.value === "" ||
			option.disabled ||
			this.isMaxReached()
		) {
			return;
		}

		list.appendChild(
			this.createPill(option.value, option.textContent?.trim() ?? option.value),
		);
		select.value = "";
		this.updateValue();
	}

	private createPill(value: string, label: string): HTMLElement {
		const item = document.createElement("li");
		const itemLabel = document.createElement("span");
		const removeButton = document.createElement("button");

		item.className = "municipio-multiselect-pill";
		item.dataset.multiselectValue = value;

		itemLabel.className = "municipio-multiselect-pill__label";
		itemLabel.textContent = label;

		removeButton.type = "button";
		removeButton.className = "municipio-multiselect-pill__remove";
		removeButton.setAttribute("aria-label", translate("Remove"));
		removeButton.textContent = "×";

		item.appendChild(itemLabel);
		item.appendChild(removeButton);

		return item;
	}

	private updateValue(): void {
		const valueInput = this.querySelector<HTMLInputElement>(
			".municipio-multiselect-value",
		);
		const selectedValues = this.getSelectedValues();

		if (!valueInput) {
			return;
		}

		valueInput.value = JSON.stringify(selectedValues);
		dispatchCustomizerChange(valueInput);
		this.updatePickerOptions();
	}

	private updatePickerOptions(): void {
		const selectedValues = this.getSelectedValues();
		const maxReached = this.isMaxReached();

		this.querySelectorAll<HTMLOptionElement>(
			".municipio-multiselect-picker__select option",
		).forEach((option) => {
			option.disabled =
				option.value !== "" &&
				(selectedValues.includes(option.value) || maxReached);
		});
	}

	private getSelectedValues(): string[] {
		return Array.from(
			this.querySelectorAll<HTMLElement>(".municipio-multiselect-pill"),
		)
			.map((item) => item.dataset.multiselectValue ?? "")
			.filter(Boolean);
	}

	private isMaxReached(): boolean {
		const maxItems = Number.parseInt(this.dataset.maxItems ?? "0", 10);

		return maxItems > 0 && this.getSelectedValues().length >= maxItems;
	}
}
