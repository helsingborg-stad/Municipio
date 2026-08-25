import {
	dispatchCustomizerChange,
	getJQuery,
	readJsonObject,
	translate,
} from "../controlTypes";

type SortableItemOptions = {
	align: string;
	margin: string;
};

const optionDashicons: Record<
	keyof SortableItemOptions,
	Record<string, string>
> = {
	align: {
		left: "dashicons-editor-alignleft",
		center: "dashicons-editor-aligncenter",
		right: "dashicons-editor-alignright",
	},
	margin: {
		none: "dashicons-align-none",
		left: "dashicons-align-left",
		right: "dashicons-align-right",
		both: "dashicons-align-center",
	},
};

const defaultOptions: SortableItemOptions = {
	align: "right",
	margin: "none",
};

const optionLabels: Record<
	keyof SortableItemOptions,
	Record<string, string>
> = {
	align: {
		left: translate("Align left"),
		center: translate("Align center"),
		right: translate("Align right"),
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

		const removeButton = event.target.closest<HTMLElement>(
			".municipio-sortable-remove",
		);

		if (removeButton) {
			removeButton.closest(".municipio-sortable-item")?.remove();
			this.updateValue();
			return;
		}

		const optionButton = event.target.closest<HTMLElement>(
			".municipio-sortable-option",
		);

		if (optionButton) {
			this.rotateOption(optionButton);
		}
	};

	private readonly handleChange = (event: Event): void => {
		if (
			!(event.target instanceof HTMLSelectElement) ||
			!event.target.classList.contains("municipio-sortable-picker__select")
		) {
			return;
		}

		this.addSelectedItem();
	};

	public connectedCallback(): void {
		this.addEventListener("click", this.handleClick);
		this.addEventListener("change", this.handleChange);
		this.initializeSortable();
		this.initializeItemOptions();
	}

	public disconnectedCallback(): void {
		this.removeEventListener("click", this.handleClick);
		this.removeEventListener("change", this.handleChange);
	}

	private initializeSortable(): void {
		const jquery = getJQuery();
		const list = this.querySelector(".municipio-sortable-items");

		if (!jquery?.fn?.sortable || !list) {
			return;
		}

		jquery(list).sortable?.({
			axis: "y",
			cancel:
				".municipio-sortable-option, .municipio-sortable-remove, .municipio-sortable-picker__select",
			distance: 3,
			forcePlaceholderSize: true,
			handle: ".municipio-sortable-item__handle",
			items: "> .municipio-sortable-item",
			placeholder: "municipio-sortable-placeholder",
			tolerance: "pointer",
			update: () => this.updateValue(),
		});
	}

	private addSelectedItem(): void {
		const select = this.querySelector<HTMLSelectElement>(
			".municipio-sortable-picker__select",
		);
		const list = this.querySelector(".municipio-sortable-items");
		const option = select?.selectedOptions[0];

		if (!select || !list || !option || option.value === "" || option.disabled) {
			return;
		}

		list.appendChild(
			this.createSortableItem(
				option.value,
				option.textContent?.trim() ?? option.value,
			),
		);
		select.value = "";
		this.refreshSortable();
		this.updateValue();
	}

	private createSortableItem(value: string, label: string): HTMLElement {
		const item = document.createElement("li");
		const handle = document.createElement("span");
		const itemLabel = document.createElement("span");
		const actions = document.createElement("div");

		item.className = "municipio-sortable-item";
		item.dataset.sortableValue = value;
		item.dataset.sortableLabel = label;

		handle.className = "municipio-sortable-item__handle";
		handle.dataset.tooltip = translate("Drag to reorder");
		handle.setAttribute("aria-hidden", "true");

		itemLabel.className = "municipio-sortable-item__label";
		itemLabel.textContent = label;

		actions.className = "municipio-sortable-item__actions";
		actions.appendChild(this.createOptionButton("align", "left,center,right"));
		actions.appendChild(
			this.createOptionButton("margin", "none,left,right,both"),
		);
		actions.appendChild(this.createRemoveButton());

		item.appendChild(handle);
		item.appendChild(itemLabel);
		item.appendChild(actions);
		this.updateItemOptionButtons(item);

		return item;
	}

	private createOptionButton(
		optionName: keyof SortableItemOptions,
		values: string,
	): HTMLButtonElement {
		const button = document.createElement("button");
		button.type = "button";
		button.className = `button button-small municipio-sortable-option municipio-sortable-option--${optionName}`;
		button.dataset.sortableOption = optionName;
		button.dataset.sortableValues = values;

		if (optionDashicons[optionName]) {
			button.appendChild(
				this.createDashicon("municipio-sortable-option__icon"),
			);
		}

		return button;
	}

	private createDashicon(className: string): HTMLSpanElement {
		const icon = document.createElement("span");
		icon.className = `dashicons municipio-sortable-action__icon ${className}`;
		icon.setAttribute("aria-hidden", "true");

		return icon;
	}

	private createRemoveButton(): HTMLButtonElement {
		const button = document.createElement("button");
		button.type = "button";
		button.className = "button button-small municipio-sortable-remove";
		button.setAttribute("aria-label", translate("Remove"));
		button.dataset.tooltip = translate("Remove");
		button.appendChild(this.createDashicon("dashicons-trash"));

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
		const values = (button.dataset.sortableValues ?? "")
			.split(",")
			.filter(Boolean);
		const currentValue = button.dataset.sortableCurrentValue ?? values[0] ?? "";
		const currentIndex = values.indexOf(currentValue);

		return values[(currentIndex + 1) % values.length] ?? currentValue;
	}

	private setItemOption(
		item: HTMLElement,
		optionName: keyof SortableItemOptions,
		optionValue: string,
	): void {
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
		this.notifySortableSettingChange();
	}

	private initializeItemOptions(): void {
		this.querySelectorAll<HTMLElement>(".municipio-sortable-item").forEach(
			(item) => {
				this.updateItemOptionButtons(item);
			},
		);
	}

	private updateItemOptionButtons(item: HTMLElement): void {
		const itemOptions = this.getItemOptions(item.dataset.sortableValue ?? "");

		item
			.querySelectorAll<HTMLElement>(".municipio-sortable-option")
			.forEach((button) => {
				const optionName = this.getOptionName(button);

				if (optionName) {
					this.updateOptionButton(
						button,
						itemOptions[optionName] ?? defaultOptions[optionName],
					);
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
		button.dataset.tooltip = label;
		button.setAttribute("aria-label", label);

		this.updateOptionIcon(button, optionName, value);
	}

	private updateOptionIcon(
		button: HTMLElement,
		optionName: keyof SortableItemOptions,
		value: string,
	): void {
		const icon = button.querySelector<HTMLElement>(
			".municipio-sortable-option__icon",
		);
		const dashicons = optionDashicons[optionName];

		if (!icon || !dashicons) {
			return;
		}

		Object.values(dashicons).forEach((className) => {
			icon.classList.remove(className);
		});
		icon.classList.add(
			dashicons[value] ?? dashicons[defaultOptions[optionName]],
		);
	}

	private updateValue(): void {
		const valueInput = this.querySelector<HTMLInputElement>(
			".municipio-sortable-value",
		);
		const selectedValues = Array.from(
			this.querySelectorAll<HTMLElement>(".municipio-sortable-item"),
		)
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
		this.querySelectorAll<HTMLOptionElement>(
			".municipio-sortable-picker__select option",
		).forEach((option) => {
			option.disabled = selectedValues.includes(option.value);
			option.selected = false;
		});
	}

	private refreshSortable(): void {
		const jquery = getJQuery();
		const list = this.querySelector(".municipio-sortable-items");

		if (!jquery?.fn?.sortable || !list) {
			return;
		}

		jquery(list).sortable?.("refresh");
	}

	private updateHiddenSetting(): void {
		const storage = this.getHiddenStorage();
		const baseSettingName = this.getBaseSettingName();
		const nextSettingStorage: Record<string, SortableItemOptions> = {};

		this.querySelectorAll<HTMLElement>(".municipio-sortable-item").forEach(
			(item) => {
				const itemValue = item.dataset.sortableValue ?? "";

				if (itemValue) {
					nextSettingStorage[itemValue] = this.getItemOptions(itemValue);
				}
			},
		);

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

	private getHiddenStorage(): Record<
		string,
		Record<string, SortableItemOptions>
	> {
		const hiddenSetting = window.wp?.customize?.(this.getHiddenSettingName());
		const value = hiddenSetting?.get();

		return readJsonObject(typeof value === "string" ? value : "{}") as Record<
			string,
			Record<string, SortableItemOptions>
		>;
	}

	private setHiddenStorage(
		storage: Record<string, Record<string, SortableItemOptions>>,
	): void {
		const settingName = this.getHiddenSettingName();
		const nextValue = JSON.stringify(storage);

		window.wp?.customize?.(settingName)?.set(nextValue);

		this.updateLinkedHiddenInput(settingName, nextValue);
	}

	private updateLinkedHiddenInput(settingName: string, value: string): void {
		const input = document.getElementById(`_customize-input-${settingName}`);

		if (!(input instanceof HTMLInputElement)) {
			return;
		}

		input.value = value;
		dispatchCustomizerChange(input);
	}

	private notifySortableSettingChange(): void {
		const valueInput = this.querySelector<HTMLInputElement>(
			".municipio-sortable-value",
		);

		if (!valueInput) {
			return;
		}

		dispatchCustomizerChange(valueInput);
	}

	private getSettingStorage(
		storage: Record<string, Record<string, SortableItemOptions>>,
		baseSettingName: string,
	): Record<string, SortableItemOptions> {
		return storage[baseSettingName] ?? {};
	}

	private getHiddenSettingName(): string {
		return (
			this.dataset.sortableHiddenSetting ?? "header_sortable_hidden_storage"
		);
	}

	private getBaseSettingName(): string {
		return (
			this.dataset.sortableBaseSetting ?? this.dataset.sortableSetting ?? ""
		);
	}

	private getOptionName(button: HTMLElement): keyof SortableItemOptions | null {
		const optionName = button.dataset.sortableOption;

		return optionName === "align" || optionName === "margin"
			? optionName
			: null;
	}
}
