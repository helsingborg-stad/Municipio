import {
	dispatchCustomizerChange,
	getJQuery,
	readJsonObject,
	translate,
} from "../controlTypes";

type SortableOptionName = "align" | "margin";
type SortableItemOptions = { align: string; margin: string };
type SortableSection = Record<string, Partial<SortableItemOptions>>;
type SortableStorageValue = Record<string, SortableSection>;

const optionValues: Record<SortableOptionName, string[]> = {
	align: ["left", "center", "right"],
	margin: ["none", "left", "right", "both"],
};

const defaultOptions: SortableItemOptions = { align: "right", margin: "none" };
const itemDefaultOptions: Record<string, Partial<SortableItemOptions>> = {
	logotype: { align: "left" },
};

function sanitizeItemOptions(
	itemValue: string,
	options: Partial<SortableItemOptions> | undefined,
): SortableItemOptions {
	const defaults = {
		...defaultOptions,
		...(itemDefaultOptions[itemValue] ?? {}),
	};

	return {
		align: optionValues.align.includes(options?.align ?? "")
			? (options?.align ?? defaults.align)
			: defaults.align,
		margin: optionValues.margin.includes(options?.margin ?? "")
			? (options?.margin ?? defaults.margin)
			: defaults.margin,
	};
}

/**
 * Owns the shared hidden setting. A read never inspects or rewrites the DOM;
 * controls submit explicit changes for their own section instead.
 */
class SortableStorage {
	public constructor(private readonly settingName: string) {}

	public getItemOptions(
		sectionName: string,
		itemValue: string,
	): SortableItemOptions {
		return sanitizeItemOptions(
			itemValue,
			this.read()[sectionName]?.[itemValue],
		);
	}

	public getSectionItemValues(sectionName: string): string[] {
		return Object.keys(this.read()[sectionName] ?? {});
	}

	public updateItemOption(
		sectionName: string,
		itemValue: string,
		optionName: SortableOptionName,
		optionValue: string,
	): SortableItemOptions {
		const storage = this.read();
		const section = storage[sectionName] ?? {};
		const itemOptions = sanitizeItemOptions(itemValue, {
			...section[itemValue],
			[optionName]: optionValue,
		});

		storage[sectionName] = { ...section, [itemValue]: itemOptions };
		this.write(storage);

		return itemOptions;
	}

	public replaceSection(
		sectionName: string,
		items: Array<{ value: string; options: Partial<SortableItemOptions> }>,
	): void {
		const storage = this.read();

		storage[sectionName] = Object.fromEntries(
			items.map(({ value, options }) => [
				value,
				sanitizeItemOptions(value, options),
			]),
		);
		this.write(storage);
	}

	private read(): SortableStorageValue {
		const rawValue = window.wp?.customize?.(this.settingName)?.get();
		const parsedValue =
			typeof rawValue === "string" ? readJsonObject(rawValue) : rawValue;

		if (
			parsedValue === null ||
			typeof parsedValue !== "object" ||
			Array.isArray(parsedValue)
		) {
			return {};
		}

		return Object.fromEntries(
			Object.entries(parsedValue).filter(
				([, section]) =>
					section !== null &&
					typeof section === "object" &&
					!Array.isArray(section),
			),
		) as SortableStorageValue;
	}

	private write(storage: SortableStorageValue): void {
		const value = JSON.stringify(storage);
		const input = document.getElementById(
			`_customize-input-${this.settingName}`,
		);

		if (input instanceof HTMLInputElement) {
			input.value = value;
			dispatchCustomizerChange(input);
		} else {
			window.wp?.customize?.(this.settingName)?.set(value);
		}
	}
}

export class SortableControlElement extends HTMLElement {
	private readonly handleClick = (event: Event): void => {
		if (!(event.target instanceof HTMLElement)) return;

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
		if (settingsButton) this.toggleSettings(settingsButton);
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
		this.recoverEmptySection();
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
		if (!jquery?.fn?.sortable || !list) return;

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

	private recoverEmptySection(): void {
		const list = this.querySelector(".municipio-sortable-items");
		const valueInput = this.querySelector<HTMLInputElement>(
			".municipio-sortable-value",
		);
		const select = this.querySelector<HTMLSelectElement>(
			".municipio-sortable-picker__select",
		);

		if (
			!list ||
			!valueInput ||
			!select ||
			this.querySelector(".municipio-sortable-item")
		) {
			return;
		}

		const recoveredValues = this.getStorage()
			.getSectionItemValues(this.getBaseSettingName())
			.filter((itemValue) => {
				const option = Array.from(select.options).find(
					(candidate) => candidate.value === itemValue,
				);

				if (!option) return false;
				list.appendChild(
					this.createSortableItem(
						itemValue,
						option.textContent?.trim() ?? itemValue,
					),
				);

				return true;
			});

		if (recoveredValues.length === 0) return;

		valueInput.value = JSON.stringify(recoveredValues);
		dispatchCustomizerChange(valueInput);
		this.updatePickerOptions(recoveredValues);
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
		const content = document.createElement("div");
		const handle = document.createElement("span");
		const itemLabel = document.createElement("span");
		const actions = document.createElement("div");
		const settings = document.createElement("div");

		item.className = "municipio-sortable-item";
		item.dataset.sortableValue = value;
		item.dataset.sortableLabel = label;
		content.className = "municipio-sortable-item__content";
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
		content.appendChild(handle);
		content.appendChild(itemLabel);
		content.appendChild(actions);
		item.appendChild(content);
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
		button.appendChild(this.createDashicon("dashicons-admin-generic"));
		return button;
	}

	private createSettingsGroup(
		itemValue: string,
		optionName: SortableOptionName,
	): HTMLFieldSetElement {
		const fieldset = document.createElement("fieldset");
		const legend = document.createElement("legend");
		const options = document.createElement("div");
		fieldset.className = "municipio-sortable-item__settings-group";
		legend.textContent =
			optionName === "align" ? translate("Alignment") : translate("Margin");
		options.className = "municipio-sortable-item__settings-options";

		optionValues[optionName].forEach((optionValue) => {
			const label = document.createElement("label");
			const input = document.createElement("input");
			input.type = "radio";
			input.name = this.getRadioGroupName(itemValue, optionName);
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
		const settings = button
			.closest<HTMLElement>(".municipio-sortable-item")
			?.querySelector<HTMLElement>(".municipio-sortable-item__settings");
		if (!settings) return;

		settings.hidden = !settings.hidden;
		button.setAttribute("aria-expanded", String(!settings.hidden));
	}

	private updateItemSetting(input: HTMLInputElement): void {
		const item = input.closest<HTMLElement>(".municipio-sortable-item");
		const optionName = this.getOptionName(input);
		const itemValue = item?.dataset.sortableValue ?? "";
		if (!item || !optionName || !itemValue || !input.checked) return;

		const itemOptions = this.getStorage().updateItemOption(
			this.getBaseSettingName(),
			itemValue,
			optionName,
			input.value,
		);
		this.updateItemOptionButtons(item, itemOptions);
	}

	private initializeItemOptions(): void {
		this.querySelectorAll<HTMLElement>(".municipio-sortable-item").forEach(
			(item) => {
				this.assignRadioGroupNames(item);
				this.updateItemOptionButtons(item);
			},
		);
	}

	private assignRadioGroupNames(item: HTMLElement): void {
		const itemValue = item.dataset.sortableValue ?? "";
		item
			.querySelectorAll<HTMLInputElement>(".municipio-sortable-setting-input")
			.forEach((input) => {
				const optionName = this.getOptionName(input);
				if (itemValue && optionName) {
					input.name = this.getRadioGroupName(itemValue, optionName);
				}
			});
	}

	private updateItemOptionButtons(
		item: HTMLElement,
		itemOptions = this.getStorage().getItemOptions(
			this.getBaseSettingName(),
			item.dataset.sortableValue ?? "",
		),
	): void {
		item
			.querySelectorAll<HTMLInputElement>(".municipio-sortable-setting-input")
			.forEach((input) => {
				const optionName = this.getOptionName(input);
				if (optionName) input.checked = input.value === itemOptions[optionName];
			});
	}

	private getOptionLabel(
		optionName: SortableOptionName,
		optionValue: string,
	): string {
		const labels: Record<SortableOptionName, Record<string, string>> = {
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
		const items = Array.from(
			this.querySelectorAll<HTMLElement>(".municipio-sortable-item"),
		);
		const selectedValues = items
			.map((item) => item.dataset.sortableValue ?? "")
			.filter(Boolean);
		if (!valueInput) return;

		valueInput.value = JSON.stringify(selectedValues);
		dispatchCustomizerChange(valueInput);
		this.updatePickerOptions(selectedValues);
		this.getStorage().replaceSection(
			this.getBaseSettingName(),
			items.flatMap((item) => {
				const value = item.dataset.sortableValue ?? "";
				return value ? [{ value, options: this.getSelectedOptions(item) }] : [];
			}),
		);
	}

	private getSelectedOptions(item: HTMLElement): Partial<SortableItemOptions> {
		const selectedOptions: Partial<SortableItemOptions> = {};
		item
			.querySelectorAll<HTMLInputElement>(
				".municipio-sortable-setting-input:checked",
			)
			.forEach((input) => {
				const optionName = this.getOptionName(input);
				if (optionName) selectedOptions[optionName] = input.value;
			});
		return selectedOptions;
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
		if (!jquery?.fn?.sortable || !list) return;
		jquery(list).sortable?.("refresh");
	}

	private getStorage(): SortableStorage {
		return new SortableStorage(this.getHiddenSettingName());
	}

	private getRadioGroupName(
		itemValue: string,
		optionName: SortableOptionName,
	): string {
		return `sortable-${this.getBaseSettingName()}-${itemValue}-${optionName}`;
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

	private getOptionName(element: HTMLElement): SortableOptionName | null {
		const optionName = element.dataset.sortableOption;
		return optionName === "align" || optionName === "margin"
			? optionName
			: null;
	}
}
