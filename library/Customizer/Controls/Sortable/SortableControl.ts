import { dispatchCustomizerChange, getJQuery, readJsonObject, translate } from "../controlTypes";

type SortableItemOptions = {
	align: string;
	margin: string;
};

const defaultOptions: SortableItemOptions = {
	align: "right",
	margin: "none",
};

const optionLabels: Record<keyof SortableItemOptions, Record<string, string>> = {
	align: {
		left: translate("Left"),
		center: translate("Center"),
		right: translate("Right"),
	},
	margin: {
		none: translate("No margin"),
		left: translate("Left margin"),
		right: translate("Right margin"),
		both: translate("Both margins"),
	},
};

export class SortableControlElement extends HTMLElement {
	private readonly handleClick = (event: Event): void => {
		if (!(event.target instanceof HTMLElement)) {
			return;
		}

		if (event.target.classList.contains("municipio-sortable-picker__add")) {
			this.addSelectedItems();
			return;
		}

		if (event.target.classList.contains("municipio-sortable-remove")) {
			event.target.closest(".municipio-sortable-item")?.remove();
			this.updateValue();
			return;
		}

		if (event.target.classList.contains("municipio-sortable-option")) {
			this.rotateOption(event.target);
		}
	};

	public connectedCallback(): void {
		this.addEventListener("click", this.handleClick);
		this.initializeSortable();
		this.initializeItemOptions();
	}

	public disconnectedCallback(): void {
		this.removeEventListener("click", this.handleClick);
	}

	private initializeSortable(): void {
		const jquery = getJQuery();
		const list = this.querySelector(".municipio-sortable-items");

		if (!jquery?.fn?.sortable || !list) {
			return;
		}

		jquery(list).sortable?.({
			handle: ".municipio-sortable-item__handle",
			update: () => this.updateValue(),
		});
	}

	private addSelectedItems(): void {
		const list = this.querySelector(".municipio-sortable-items");
		const selectedOptions = Array.from(
			this.querySelectorAll<HTMLOptionElement>(".municipio-sortable-picker__select option:checked:not(:disabled)"),
		);

		if (!list) {
			return;
		}

		selectedOptions.forEach((option) => {
			list.appendChild(this.createSortableItem(option.value, option.textContent?.trim() ?? option.value));
		});

		this.updateValue();
	}

	private createSortableItem(value: string, label: string): HTMLElement {
		const item = document.createElement("li");
		const handle = document.createElement("button");
		const itemLabel = document.createElement("span");
		const actions = document.createElement("div");

		item.className = "municipio-sortable-item";
		item.dataset.sortableValue = value;
		item.dataset.sortableLabel = label;

		handle.type = "button";
		handle.className = "municipio-sortable-item__handle";
		handle.setAttribute("aria-label", translate("Move item"));

		itemLabel.className = "municipio-sortable-item__label";
		itemLabel.textContent = label;

		actions.className = "municipio-sortable-item__actions";
		actions.appendChild(this.createOptionButton("align", "left,center,right"));
		actions.appendChild(this.createOptionButton("margin", "none,left,right,both"));
		actions.appendChild(this.createRemoveButton());

		item.appendChild(handle);
		item.appendChild(itemLabel);
		item.appendChild(actions);
		this.updateItemOptionButtons(item);

		return item;
	}

	private createOptionButton(optionName: keyof SortableItemOptions, values: string): HTMLButtonElement {
		const button = document.createElement("button");
		button.type = "button";
		button.className = "button button-small municipio-sortable-option";
		button.dataset.sortableOption = optionName;
		button.dataset.sortableValues = values;

		return button;
	}

	private createRemoveButton(): HTMLButtonElement {
		const button = document.createElement("button");
		button.type = "button";
		button.className = "button-link-delete municipio-sortable-remove";
		button.textContent = translate("Remove");

		return button;
	}

	private rotateOption(button: HTMLElement): void {
		const item = button.closest<HTMLElement>(".municipio-sortable-item");
		const optionName = this.getOptionName(button);

		if (!item || !optionName) {
			return;
		}

		this.setItemOption(item, optionName, this.getNextOptionValue(button));
	}

	private getNextOptionValue(button: HTMLElement): string {
		const values = (button.dataset.sortableValues ?? "").split(",").filter(Boolean);
		const currentValue = button.dataset.sortableCurrentValue ?? values[0] ?? "";
		const currentIndex = values.indexOf(currentValue);

		return values[(currentIndex + 1) % values.length] ?? currentValue;
	}

	private setItemOption(item: HTMLElement, optionName: keyof SortableItemOptions, optionValue: string): void {
		const storage = this.getHiddenStorage();
		const baseSettingName = this.getBaseSettingName();
		const settingStorage = this.getSettingStorage(storage, baseSettingName);
		const itemValue = item.dataset.sortableValue ?? "";

		settingStorage[itemValue] = {
			...defaultOptions,
			...(settingStorage[itemValue] ?? {}),
			[optionName]: optionValue,
		};

		storage[baseSettingName] = settingStorage;
		this.setHiddenStorage(storage);
		this.updateItemOptionButtons(item);
	}

	private initializeItemOptions(): void {
		this.querySelectorAll<HTMLElement>(".municipio-sortable-item").forEach((item) => {
			this.updateItemOptionButtons(item);
		});

		this.updateHiddenSetting();
	}

	private updateItemOptionButtons(item: HTMLElement): void {
		const itemOptions = this.getItemOptions(item.dataset.sortableValue ?? "");

		item.querySelectorAll<HTMLElement>(".municipio-sortable-option").forEach((button) => {
			const optionName = this.getOptionName(button);

			if (optionName) {
				this.updateOptionButton(button, itemOptions[optionName] ?? defaultOptions[optionName]);
			}
		});
	}

	private updateOptionButton(button: HTMLElement, value: string): void {
		const optionName = this.getOptionName(button);

		if (!optionName) {
			return;
		}

		const label = optionLabels[optionName][value] ?? value;
		button.dataset.sortableCurrentValue = value;
		button.textContent = label;
		button.title = label;
	}

	private updateValue(): void {
		const valueInput = this.querySelector<HTMLInputElement>(".municipio-sortable-value");
		const selectedValues = Array.from(this.querySelectorAll<HTMLElement>(".municipio-sortable-item"))
			.map((item) => item.dataset.sortableValue ?? "")
			.filter(Boolean);

		if (!valueInput) {
			return;
		}

		valueInput.value = JSON.stringify(selectedValues);
		dispatchCustomizerChange(valueInput);
		this.updatePickerOptions(selectedValues);
		this.updateHiddenSetting();
	}

	private updatePickerOptions(selectedValues: string[]): void {
		this.querySelectorAll<HTMLOptionElement>(".municipio-sortable-picker__select option").forEach((option) => {
			option.disabled = selectedValues.includes(option.value);
			option.selected = false;
		});
	}

	private updateHiddenSetting(): void {
		const storage = this.getHiddenStorage();
		const baseSettingName = this.getBaseSettingName();
		const nextSettingStorage: Record<string, SortableItemOptions> = {};

		this.querySelectorAll<HTMLElement>(".municipio-sortable-item").forEach((item) => {
			const itemValue = item.dataset.sortableValue ?? "";

			if (itemValue) {
				nextSettingStorage[itemValue] = this.getItemOptions(itemValue);
			}
		});

		storage[baseSettingName] = nextSettingStorage;
		this.setHiddenStorage(storage);
	}

	private getItemOptions(itemValue: string): SortableItemOptions {
		const storage = this.getHiddenStorage();
		const baseSettingName = this.getBaseSettingName();
		const settingStorage = this.getSettingStorage(storage, baseSettingName);

		return {
			...defaultOptions,
			...(settingStorage[itemValue] ?? {}),
		};
	}

	private getHiddenStorage(): Record<string, Record<string, SortableItemOptions>> {
		const hiddenSetting = window.wp?.customize?.(this.getHiddenSettingName());
		const value = hiddenSetting?.get();

		return readJsonObject(typeof value === "string" ? value : "{}") as Record<string, Record<string, SortableItemOptions>>;
	}

	private setHiddenStorage(storage: Record<string, Record<string, SortableItemOptions>>): void {
		window.wp?.customize?.(this.getHiddenSettingName())?.set(JSON.stringify(storage));
	}

	private getSettingStorage(
		storage: Record<string, Record<string, SortableItemOptions>>,
		baseSettingName: string,
	): Record<string, SortableItemOptions> {
		return storage[baseSettingName] ?? {};
	}

	private getHiddenSettingName(): string {
		return this.dataset.sortableHiddenSetting ?? "header_sortable_hidden_storage";
	}

	private getBaseSettingName(): string {
		return this.dataset.sortableBaseSetting ?? this.dataset.sortableSetting ?? "";
	}

	private getOptionName(button: HTMLElement): keyof SortableItemOptions | null {
		const optionName = button.dataset.sortableOption;

		return optionName === "align" || optionName === "margin" ? optionName : null;
	}
}