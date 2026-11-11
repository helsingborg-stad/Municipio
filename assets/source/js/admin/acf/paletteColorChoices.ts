type PaletteChoice = {
	value: string;
	label: string;
	background: string;
	contrast: string;
};

export {};

declare global {
	interface Window {
		MunicipioPaletteColorChoices?: { choices: PaletteChoice[] };
	}
}

const selector = ".municipio-palette-color-selector";
const enhancedSelects = new WeakSet<HTMLSelectElement>();

function enhanceField(field: Element): void {
	const select = field.querySelector<HTMLSelectElement>("select");
	const choices = window.MunicipioPaletteColorChoices?.choices;
	if (!select || enhancedSelects.has(select) || !choices?.length) {
		return;
	}

	// ACF duplicates repeater markup without copying event listeners.
	field.querySelector(".municipio-palette-color-grid")?.remove();

	const grid = document.createElement("div");
	grid.className = "municipio-palette-color-grid";
	grid.setAttribute("role", "radiogroup");
	grid.setAttribute(
		"aria-label",
		field.querySelector("label")?.textContent?.trim() || "Colour palette",
	);

	const updateSelection = (): void => {
		const buttons = grid.querySelectorAll<HTMLButtonElement>('[role="radio"]');
		const hasSelection = Array.from(buttons).some(
			(button) => button.dataset.value === select.value,
		);
		buttons.forEach((button, index) => {
			const isSelected = button.dataset.value === select.value;
			button.setAttribute("aria-checked", String(isSelected));
			button.tabIndex = isSelected || (!hasSelection && index === 0) ? 0 : -1;
		});
	};

	const availableValues = new Set(
		Array.from(select.options, (option) => option.value),
	);
	const visualChoices = choices.filter((choice) =>
		availableValues.has(choice.value),
	);
	const defaultOption = Array.from(select.options).find(
		(option) => option.value === "",
	);
	if (defaultOption) {
		visualChoices.unshift({
			value: "",
			label:
				defaultOption.textContent?.trim().replace(/^-\s*(.*?)\s*-$/, "$1") ||
				"None",
			background: "#f0f0f1",
			contrast: "#1d2327",
		});
	}

	visualChoices.forEach((choice) => {
		const option = document.createElement("button");
		option.type = "button";
		option.className = "municipio-palette-color-option acf-js-tooltip";
		option.dataset.value = choice.value;
		option.setAttribute("role", "radio");
		option.setAttribute("aria-label", choice.label);
		option.title = choice.label;
		option.style.backgroundColor = choice.background;
		option.style.color = choice.contrast;
		option.disabled = select.disabled;

		const icon = document.createElement("span");
		icon.className = `dashicons ${choice.value === "" ? "dashicons-no-alt" : "dashicons-editor-textcolor"}`;
		icon.setAttribute("aria-hidden", "true");
		option.append(icon);
		option.addEventListener("click", () => {
			select.value = choice.value;
			select.dispatchEvent(new Event("input", { bubbles: true }));
			select.dispatchEvent(new Event("change", { bubbles: true }));
			updateSelection();
		});
		grid.append(option);
	});

	grid.addEventListener("keydown", (event: KeyboardEvent) => {
		const options = Array.from(
			grid.querySelectorAll<HTMLButtonElement>('[role="radio"]'),
		);
		const currentIndex = options.indexOf(
			document.activeElement as HTMLButtonElement,
		);
		if (
			currentIndex === -1 ||
			![
				"ArrowLeft",
				"ArrowUp",
				"ArrowRight",
				"ArrowDown",
				"Home",
				"End",
			].includes(event.key)
		) {
			return;
		}
		event.preventDefault();
		const direction = ["ArrowLeft", "ArrowUp"].includes(event.key) ? -1 : 1;
		const nextIndex =
			event.key === "Home"
				? 0
				: event.key === "End"
					? options.length - 1
					: (currentIndex + direction + options.length) % options.length;
		options[nextIndex].focus();
		options[nextIndex].click();
	});

	select.after(grid);
	select.tabIndex = -1;
	select.setAttribute("aria-hidden", "true");
	select.addEventListener("change", updateSelection);
	field.classList.add("is-enhanced");
	enhancedSelects.add(select);
	updateSelection();
}

function enhanceAll(root: ParentNode = document): void {
	root.querySelectorAll(selector).forEach(enhanceField);
}

function initialize(): void {
	enhanceAll();
	new MutationObserver((mutations) => {
		mutations.forEach((mutation) => {
			mutation.addedNodes.forEach((node) => {
				if (!(node instanceof Element)) {
					return;
				}

				if (node.matches(selector)) {
					enhanceField(node);
				}
				enhanceAll(node);
			});
		});
	}).observe(document.body, { childList: true, subtree: true });
}

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initialize, { once: true });
} else {
	initialize();
}
