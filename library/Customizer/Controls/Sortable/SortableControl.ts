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

const defaultOptions: SortableItemOptions = {
	align: "right",
	margin: "none",
};

const itemDefaultOptions: Record<string, Partial<SortableItemOptions>> = {
	logotype: { align: "left" },
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

		const settingsButton = event.target.closest<HTMLButtonElement>(
			".municipio-sortable-settings-toggle",
		);

		if (settingsButton) {
			this.toggleSettings(settingsButton);
		}
	};

	private readonly handleChange = (event: Event): void => {
		if (
			event.target instanceof HTMLInputElement &&
			event.target.classList.contains("municipio-sortable-setting-input")
		) {
			this.updateItemSetting(event.target);
			return;
		}

		if (
			event.target instanceof HTMLSelectElement &&
			event.target.classList.contains("municipio-sortable-picker__select")
		) {
			this.addSelectedItem();
		}
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
				".municipio-sortable-settings-toggle, .municipio-sortable-item__settings, .municipio-sortable-remove, .municipio-sortable-picker__select",
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
		const settings = document.createElement("div");

		item.className = "municipio-sortable-item";
		item.dataset.sortableValue = value;
		item.dataset.sortableLabel = label;

		handle.className = "municipio-sortable-item__handle";
		handle.dataset.tooltip = translate("Drag to reorder");
		handle.setAttribute("aria-hidden", "true");

		itemLabel.className = "municipio-sortable-item__label";
		itemLabel.textContent = label;

		actions.className = "municipio-sortable-item__actions";
		actions.appendChild(this.createSettingsButton());
		actions.appendChild(this.createRemoveButton());
		settings.className = "municipio-sortable-item__settings";
		settings.hidden = true;
		settings.appendChild(this.createSettingsGroup(value, "align"));
		settings.appendChild(this.createSettingsGroup(value, "margin"));

		item.appendChild(handle);
		item.appendChild(itemLabel);
		item.appendChild(actions);
		item.appendChild(settings);
		this.updateItemOptionButtons(item);

		return item;
	}

	private createSettingsButton(): HTMLButtonElement {
		const button = document.createElement("button");
		button.type = "button";
		button.className = "button button-small municipio-sortable-settings-toggle";
		button.dataset.tooltip = translate("Settings");
		button.setAttribute("aria-label", translate("Settings"));
		button.setAttribute("aria-expanded", "false");
		const icon = this.createDashicon("dashicons-admin-generic");
		button.appendChild(icon);

		return button;
	}

	private createSettingsGroup(
		itemValue: string,
		optionName: keyof SortableItemOptions,
	): HTMLFieldSetElement {
		const fieldset = document.createElement("fieldset");
		const legend = document.createElement("legend");
		const options = document.createElement("div");
		const optionValues =
			optionName === "align"
				? ["left", "center", "right"]
				: ["none", "left", "right", "both"];

		fieldset.className = "municipio-sortable-item__settings-group";
		legend.textContent =
			optionName === "align" ? translate("Alignment") : translate("Margin");
		options.className = "municipio-sortable-item__settings-options";

		optionValues.forEach((optionValue) => {
			const label = document.createElement("label");
			const input = document.createElement("input");

			input.type = "radio";
			input.name = `sortable-${itemValue}-${optionName}`;
			input.value = optionValue;
			input.className = "municipio-sortable-setting-input";
			input.dataset.sortableOption = optionName;
			label.appendChild(input);
			label.append(
				` ${translate(this.getOptionLabel(optionName, optionValue))}`,
			);
			options.appendChild(label);
		});

		fieldset.appendChild(legend);
		fieldset.appendChild(options);

		return fieldset;
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

	private toggleSettings(button: HTMLButtonElement): void {
		const item = button.closest<HTMLElement>(".municipio-sortable-item");
		const settings = item?.querySelector<HTMLElement>(
			".municipio-sortable-item__settings",
		);

		if (!settings) {
			return;
		}

		settings.hidden = !settings.hidden;
		button.setAttribute("aria-expanded", String(!settings.hidden));
	}

	private updateItemSetting(input: HTMLInputElement): void {
		const item = input.closest<HTMLElement>(".municipio-sortable-item");
		const optionName = this.getOptionName(input);

		if (!item || !optionName || !input.checked) {
			return;
		}

		this.setItemOption(item, optionName, input.value);
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
			...this.sanitizeItemOptions(itemValue, settingStorage[itemValue]),
			[optionName]: optionValue,
		};

		storage[baseSettingName] = settingStorage;
		this.setHiddenStorage(storage);
		this.updateItemOptionButtons(item, settingStorage[itemValue]);
	}

	private initializeItemOptions(): void {
		this.querySelectorAll<HTMLElement>(".municipio-sortable-item").forEach(
			(item) => {
				this.updateItemOptionButtons(item);
			},
		);
	}

	private updateItemOptionButtons(
		item: HTMLElement,
		itemOptions = this.getItemOptions(item.dataset.sortableValue ?? ""),
	): void {
		item
			.querySelectorAll<HTMLInputElement>(".municipio-sortable-setting-input")
			.forEach((input) => {
				const optionName = this.getOptionName(input);

				if (optionName) {
					input.checked = input.value === itemOptions[optionName];
				}
			});
	}

	private getOptionLabel(
		optionName: keyof SortableItemOptions,
		optionValue: string,
	): string {
		const labels: Record<keyof SortableItemOptions, Record<string, string>> = {
			align: {
				left: "Align left",
				center: "Align center",
				right: "Align right",
			},
			margin: {
				none: "No margin",
				left: "Left margin",
				right: "Right margin",
				both: "Both margins",
			},
		};

		return labels[optionName][optionValue] ?? optionValue;
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

		return this.sanitizeItemOptions(itemValue, settingStorage[itemValue]);
	}

	private getHiddenStorage(): Record<
		string,
		Record<string, SortableItemOptions>
	> {
		const hiddenSetting = window.wp?.customize?.(this.getHiddenSettingName());
		const value = hiddenSetting?.get();

		return this.sanitizeStorage(this.parseHiddenStorage(value));
	}

	private parseHiddenStorage(
		value: unknown,
	): Record<string, Record<string, SortableItemOptions>> {
		const parsedValue =
			typeof value === "string" ? readJsonObject(value) : value;

		if (
			parsedValue === null ||
			typeof parsedValue !== "object" ||
			Array.isArray(parsedValue)
		) {
			return {};
		}

		return Object.fromEntries(
			Object.entries(parsedValue).filter(
				([, section]): section is Record<string, SortableItemOptions> =>
					section !== null &&
					typeof section === "object" &&
					!Array.isArray(section),
			),
		);
	}

	private setHiddenStorage(
		storage: Record<string, Record<string, SortableItemOptions>>,
	): void {
		const settingName = this.getHiddenSettingName();
		const nextValue = JSON.stringify(this.sanitizeStorage(storage));
		this.updateLinkedHiddenInput(settingName, nextValue, true);
	}

	private sanitizeStorage(
		storage: Record<string, Record<string, SortableItemOptions>>,
	): Record<string, Record<string, SortableItemOptions>> {
		const baseSettingName = this.getBaseSettingName();
		const selectedItems = this.getOrderedItemValues();
		const sanitizedStorage = { ...storage };
		const currentSection = this.getSettingStorage(storage, baseSettingName);

		sanitizedStorage[baseSettingName] = Object.fromEntries(
			selectedItems.map((itemValue) => [
				itemValue,
				this.sanitizeItemOptions(itemValue, currentSection[itemValue]),
			]),
		);

		return sanitizedStorage;
	}

	private getOrderedItemValues(): string[] {
		const valueInput = this.querySelector<HTMLInputElement>(
			".municipio-sortable-value",
		);

		if (!valueInput) {
			return [];
		}

		try {
			const values = JSON.parse(valueInput.value);

			return Array.isArray(values)
				? values.filter(
						(value): value is string =>
							typeof value === "string" && value !== "",
					)
				: [];
		} catch {
			return [];
		}
	}

	private sanitizeItemOptions(
		itemValue: string,
		options: Partial<SortableItemOptions> | undefined,
	): SortableItemOptions {
		const defaults = {
			...defaultOptions,
			...(itemDefaultOptions[itemValue] ?? {}),
		};

		return {
			align: ["left", "center", "right"].includes(options?.align ?? "")
				? (options?.align ?? defaults.align)
				: defaults.align,
			margin: ["none", "left", "right", "both"].includes(options?.margin ?? "")
				? (options?.margin ?? defaults.margin)
				: defaults.margin,
		};
	}

	private updateLinkedHiddenInput(settingName: string, value: string): void {
		const input = document.getElementById(`_customize-input-${settingName}`);

		if (!(input instanceof HTMLInputElement)) {
			return;
		}

		input.value = value;
		dispatchCustomizerChange(input);
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

	private getOptionName(
		element: HTMLElement,
	): keyof SortableItemOptions | null {
		const optionName = element.dataset.sortableOption;

		return optionName === "align" || optionName === "margin"
			? optionName
			: null;
	}
}
