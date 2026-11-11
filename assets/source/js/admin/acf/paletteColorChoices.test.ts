describe("ACF palette selector", () => {
	it("keeps the native field in sync and enhances cloned repeater fields", async () => {
		window.MunicipioPaletteColorChoices = {
			choices: [
				{
					value: "primary",
					label: "Primary",
					background: "#123456",
					contrast: "#ffffff",
				},
				{
					value: "secondary",
					label: "Secondary",
					background: "#654321",
					contrast: "#ffffff",
				},
				{
					value: "palette-1",
					label: "Palette 1",
					background: "#eeeeee",
					contrast: "#000000",
				},
			],
		};
		document.body.innerHTML = `<div class="municipio-palette-color-selector"><label>Colours</label><select>
            <option value="primary">Primary</option><option value="secondary">Secondary</option>
        </select></div>`;
		jest.isolateModules(() => require("./paletteColorChoices"));
		document.dispatchEvent(new Event("DOMContentLoaded"));
		const field = document.querySelector<HTMLElement>(
			".municipio-palette-color-selector",
		)!;
		const select = field.querySelector("select")!;
		const radios = field.querySelectorAll<HTMLButtonElement>('[role="radio"]');
		expect(radios).toHaveLength(2);
		expect(radios[0].classList.contains("acf-js-tooltip")).toBe(true);
		expect(radios[0].title).toBe("Primary");
		expect(radios[0].style.backgroundColor).toBe("rgb(18, 52, 86)");
		expect(radios[0].style.color).toBe("rgb(255, 255, 255)");
		expect(
			radios[0].querySelector(".dashicons-editor-textcolor"),
		).not.toBeNull();
		expect(
			field.querySelector(".municipio-palette-color-option__label"),
		).toBeNull();
		const changed = jest.fn();
		select.addEventListener("change", changed);
		radios[0].focus();
		radios[0].dispatchEvent(
			new KeyboardEvent("keydown", { key: "ArrowRight", bubbles: true }),
		);
		expect(select.value).toBe("secondary");
		expect(changed).toHaveBeenCalledTimes(1);
		expect(radios[1].getAttribute("aria-checked")).toBe("true");

		const clone = field.cloneNode(true) as HTMLElement;
		document.body.append(clone);
		await Promise.resolve();
		expect(
			clone.querySelectorAll(".municipio-palette-color-grid"),
		).toHaveLength(1);
		clone.querySelector<HTMLButtonElement>('[data-value="primary"]')!.click();
		expect(clone.querySelector("select")!.value).toBe("primary");
		expect(select.value).toBe("secondary");

		const optional = document.createElement("div");
		optional.className = "municipio-palette-color-selector";
		optional.innerHTML = `<select><option value="">- None -</option><option value="primary" selected>Primary</option></select>`;
		document.body.append(optional);
		await Promise.resolve();
		const none = optional.querySelector<HTMLButtonElement>('[data-value=""]')!;
		expect(none.title).toBe("None");
		expect(none.getAttribute("aria-label")).toBe("None");
		expect(none.querySelector(".dashicons-no-alt")).not.toBeNull();
		const cleared = jest.fn();
		optional.querySelector("select")!.addEventListener("change", cleared);
		none.click();
		expect(optional.querySelector("select")!.value).toBe("");
		expect(none.getAttribute("aria-checked")).toBe("true");
		expect(cleared).toHaveBeenCalledTimes(1);
		document.body.replaceChildren();
	});
});
