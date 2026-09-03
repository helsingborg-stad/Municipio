import { SortableControlElement } from "./SortableControl";

const testElementName = "municipio-sortable-control-test";

if (!customElements.get(testElementName)) {
	customElements.define(testElementName, SortableControlElement);
}

describe("SortableControlElement", () => {
	let hiddenSettingValue: unknown;
	let control: HTMLElement;
	let orderedValueChangeCount: number;
	let hiddenStorageChangeCount: number;

	beforeEach(() => {
		hiddenSettingValue = JSON.stringify({
			header_sortable_section_main_lower: {
				primary: { align: "left", margin: "none" },
				"collapsible-search": { align: "right", margin: "left" },
			},
		});
		orderedValueChangeCount = 0;
		hiddenStorageChangeCount = 0;

		window.wp = {
			customize: (settingName) => {
				if (settingName !== "header_sortable_hidden_storage") {
					return undefined;
				}

				return {
					get: () => hiddenSettingValue,
					set: (value: string) => {
						hiddenSettingValue = value;
					},
				};
			},
		};

		document.body.innerHTML = `
			<input id="_customize-input-header_sortable_hidden_storage" type="hidden" />
		`;
		const hiddenStorageInput = document.getElementById(
			"_customize-input-header_sortable_hidden_storage",
		) as HTMLInputElement;
		hiddenStorageInput.addEventListener("change", () => {
			hiddenSettingValue = hiddenStorageInput.value;
		});
		control = document.createElement(testElementName);
		control.dataset.sortableBaseSetting = "header_sortable_section_main_lower";
		control.dataset.sortableHiddenSetting = "header_sortable_hidden_storage";
		control.innerHTML = `
			<input class="municipio-sortable-value" type="hidden" value="[&quot;primary&quot;,&quot;collapsible-search&quot;]" />
			<ul class="municipio-sortable-items">
				<li class="municipio-sortable-item" data-sortable-value="collapsible-search">
					<button type="button" class="municipio-sortable-settings-toggle" aria-expanded="false"></button>
					<div class="municipio-sortable-item__settings" hidden>
						<input class="municipio-sortable-setting-input" type="radio" name="align" value="left" data-sortable-option="align" />
						<input class="municipio-sortable-setting-input" type="radio" name="align" value="center" data-sortable-option="align" />
						<input class="municipio-sortable-setting-input" type="radio" name="align" value="right" data-sortable-option="align" />
						<input class="municipio-sortable-setting-input" type="radio" name="margin" value="none" data-sortable-option="margin" />
						<input class="municipio-sortable-setting-input" type="radio" name="margin" value="left" data-sortable-option="margin" />
						<input class="municipio-sortable-setting-input" type="radio" name="margin" value="right" data-sortable-option="margin" />
						<input class="municipio-sortable-setting-input" type="radio" name="margin" value="both" data-sortable-option="margin" />
					</div>
				</li>
			</ul>
		`;
		document.body.appendChild(control);

		control
			.querySelector(".municipio-sortable-value")
			?.addEventListener("change", () => {
				orderedValueChangeCount += 1;
			});
		document
			.getElementById("_customize-input-header_sortable_hidden_storage")
			?.addEventListener("change", () => {
				hiddenStorageChangeCount += 1;
			});
	});

	afterEach(() => {
		control.remove();
		document.body.innerHTML = "";
	});

	it("shows and hides the item settings from the settings button", () => {
		const button = control.querySelector<HTMLButtonElement>(
			".municipio-sortable-settings-toggle",
		);
		const settings = control.querySelector<HTMLElement>(
			".municipio-sortable-item__settings",
		);

		if (!button || !settings) {
			throw new Error("Expected sortable item settings to be rendered.");
		}

		button.click();
		expect(settings.hidden).toBe(false);
		expect(button.getAttribute("aria-expanded")).toBe("true");

		button.click();
		expect(settings.hidden).toBe(true);
		expect(button.getAttribute("aria-expanded")).toBe("false");
	});

	it("updates item options without re-saving the ordered item list", () => {
		control
			.querySelector<HTMLInputElement>(
				'.municipio-sortable-setting-input[data-sortable-option="margin"][value="right"]',
			)
			?.click();

		expect(orderedValueChangeCount).toBe(0);
		expect(hiddenStorageChangeCount).toBe(1);
		expect(
			(
				document.getElementById(
					"_customize-input-header_sortable_hidden_storage",
				) as HTMLInputElement
			).value,
		).toBe(hiddenSettingValue as string);
		expect(
			control.querySelector<HTMLInputElement>(
				'.municipio-sortable-setting-input[data-sortable-option="margin"][value="right"]',
			)?.checked,
		).toBe(true);
		expect(JSON.parse(hiddenSettingValue as string)).toEqual({
			header_sortable_section_main_lower: {
				primary: { align: "left", margin: "none" },
				"collapsible-search": { align: "right", margin: "right" },
			},
		});
	});

	it("normalizes only the item being changed and preserves unrelated data", () => {
		hiddenSettingValue = JSON.stringify({
			header_sortable_section_main_lower: {
				primary: { align: "invalid", margin: "both" },
				"collapsible-search": { align: "right", margin: "invalid" },
				obsolete: { align: "left", margin: "none" },
			},
		});

		control
			.querySelector<HTMLInputElement>(
				'.municipio-sortable-setting-input[data-sortable-option="margin"][value="right"]',
			)
			?.click();

		expect(JSON.parse(hiddenSettingValue as string)).toEqual({
			header_sortable_section_main_lower: {
				primary: { align: "invalid", margin: "both" },
				"collapsible-search": { align: "right", margin: "right" },
				obsolete: { align: "left", margin: "none" },
			},
		});
	});

	it("uses the logotype defaults when legacy option storage is missing", () => {
		hiddenSettingValue = "{}";
		const valueInput = control.querySelector<HTMLInputElement>(
			".municipio-sortable-value",
		);
		const item = control.querySelector<HTMLElement>(".municipio-sortable-item");
		const input = control.querySelector<HTMLInputElement>(
			'.municipio-sortable-setting-input[data-sortable-option="margin"][value="right"]',
		);

		if (!valueInput || !item || !input) {
			throw new Error("Expected sortable controls to be rendered.");
		}

		valueInput.value = '["logotype"]';
		item.dataset.sortableValue = "logotype";
		input.click();

		expect(JSON.parse(hiddenSettingValue as string)).toEqual({
			header_sortable_section_main_lower: {
				logotype: { align: "left", margin: "right" },
			},
		});
	});

	it("preserves storage when the Customizer provides an already decoded value", () => {
		hiddenSettingValue = {
			header_sortable_section_main_lower: {
				primary: { align: "left", margin: "none" },
				"collapsible-search": { align: "right", margin: "left" },
			},
		};

		control
			.querySelector<HTMLInputElement>(
				'.municipio-sortable-setting-input[data-sortable-option="margin"][value="right"]',
			)
			?.click();

		expect(JSON.parse(hiddenSettingValue as string)).toEqual({
			header_sortable_section_main_lower: {
				primary: { align: "left", margin: "none" },
				"collapsible-search": { align: "right", margin: "right" },
			},
		});
	});

	it("keeps alignment and margin as independent radio groups", () => {
		const alignCenter = control.querySelector<HTMLInputElement>(
			'.municipio-sortable-setting-input[data-sortable-option="align"][value="center"]',
		);
		const marginNone = control.querySelector<HTMLInputElement>(
			'.municipio-sortable-setting-input[data-sortable-option="margin"][value="none"]',
		);
		const marginLeft = control.querySelector<HTMLInputElement>(
			'.municipio-sortable-setting-input[data-sortable-option="margin"][value="left"]',
		);

		if (!alignCenter || !marginNone || !marginLeft) {
			throw new Error("Expected alignment and margin choices to be rendered.");
		}

		alignCenter.click();
		expect(alignCenter.checked).toBe(true);
		expect(marginLeft.checked).toBe(true);

		marginNone.click();
		expect(marginNone.checked).toBe(true);
		expect(alignCenter.checked).toBe(true);

		marginLeft.click();
		expect(marginLeft.checked).toBe(true);
		expect(alignCenter.checked).toBe(true);
		expect(JSON.parse(hiddenSettingValue as string)).toMatchObject({
			header_sortable_section_main_lower: {
				"collapsible-search": { align: "center", margin: "left" },
			},
		});
	});

	it("isolates identical items in different header sections", () => {
		const otherControl = control.cloneNode(true) as HTMLElement;
		otherControl.dataset.sortableBaseSetting =
			"header_sortable_section_main_upper_responsive";
		document.body.appendChild(otherControl);

		const lowerCenter = control.querySelector<HTMLInputElement>(
			'.municipio-sortable-setting-input[data-sortable-option="align"][value="center"]',
		);
		const upperLeft = otherControl.querySelector<HTMLInputElement>(
			'.municipio-sortable-setting-input[data-sortable-option="align"][value="left"]',
		);

		if (!lowerCenter || !upperLeft) {
			throw new Error("Expected controls for both header sections.");
		}

		lowerCenter.click();
		upperLeft.click();

		expect(lowerCenter.checked).toBe(true);
		expect(upperLeft.checked).toBe(true);
		expect(lowerCenter.name).not.toBe(upperLeft.name);
		otherControl.remove();
	});
});
