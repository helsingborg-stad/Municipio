(($) => {
	const translate = window.wp?.i18n?.__ || ((text) => text);
	const optionLabels = {
		align: {
			left: translate("Left", "municipio"),
			center: translate("Center", "municipio"),
			right: translate("Right", "municipio"),
		},
		margin: {
			none: translate("No margin", "municipio"),
			left: translate("Left margin", "municipio"),
			right: translate("Right margin", "municipio"),
			both: translate("Both margins", "municipio"),
		},
	};

 const defaultOptions = {
  align: "right",
  margin: "none",
 };

	function updateValue(container) {
		var valueInput = container.querySelector(".municipio-sortable-value");
		var selectedValues = Array.prototype.slice
			.call(container.querySelectorAll(".municipio-sortable-item"))
			.map((item) => item.dataset.sortableValue);

		if (!valueInput) {
			return;
		}

		valueInput.value = JSON.stringify(selectedValues);
		valueInput.dispatchEvent(new Event("change", { bubbles: true }));
		updatePickerOptions(container, selectedValues);
		updateHiddenSetting(container);
	}

	function getHiddenSettingName(container) {
		return container.dataset.sortableHiddenSetting || "header_sortable_hidden_storage";
	}

	function getBaseSettingName(container) {
		return container.dataset.sortableBaseSetting || container.dataset.sortableSetting;
	}

	function getHiddenStorage(container) {
		var hiddenSettingName = getHiddenSettingName(container);
		var hiddenSetting = wp.customize && wp.customize(hiddenSettingName);
		var value = hiddenSetting ? hiddenSetting.get() : "{}";

		if (!value || typeof value !== "string") {
			return {};
		}

		try {
			return JSON.parse(value) || {};
		} catch (error) {
			return {};
		}
	}

	function setHiddenStorage(container, storage) {
		var hiddenSettingName = getHiddenSettingName(container);
		var hiddenSetting = wp.customize && wp.customize(hiddenSettingName);

		if (hiddenSetting) {
			hiddenSetting.set(JSON.stringify(storage));
		}
	}

	function getItemOptions(container, itemValue) {
		var storage = getHiddenStorage(container);
		var baseSettingName = getBaseSettingName(container);
		var settingStorage = storage[baseSettingName] || {};

		return Object.assign({}, defaultOptions, settingStorage[itemValue] || {});
	}

	function updateHiddenSetting(container) {
		var storage = getHiddenStorage(container);
		var baseSettingName = getBaseSettingName(container);
		var selectedItems = Array.prototype.slice.call(
			container.querySelectorAll(".municipio-sortable-item"),
		);
		var nextSettingStorage = {};

		selectedItems.forEach((item) => {
			nextSettingStorage[item.dataset.sortableValue] = getItemOptions(
				container,
				item.dataset.sortableValue,
			);
		});

		storage[baseSettingName] = nextSettingStorage;
		setHiddenStorage(container, storage);
	}

	function updatePickerOptions(container, selectedValues) {
		container
			.querySelectorAll(".municipio-sortable-picker__select option")
			.forEach((option) => {
				option.disabled = selectedValues.includes(option.value);
				option.selected = false;
			});
	}

	function updateOptionButton(button, value) {
		var optionName = button.dataset.sortableOption;
		var labels = optionLabels[optionName] || {};

		button.dataset.sortableCurrentValue = value;
		button.textContent = labels[value] || value;
		button.title = labels[value] || value;
	}

	function updateItemOptionButtons(container, item) {
		var itemOptions = getItemOptions(container, item.dataset.sortableValue);

		item.querySelectorAll(".municipio-sortable-option").forEach((button) => {
			var optionName = button.dataset.sortableOption;
			updateOptionButton(button, itemOptions[optionName] || defaultOptions[optionName]);
		});
	}

	function getNextOptionValue(button) {
		var values = (button.dataset.sortableValues || "")
			.split(",")
			.filter(Boolean);
		var currentValue = button.dataset.sortableCurrentValue || values[0];
		var currentIndex = values.indexOf(currentValue);

		return values[(currentIndex + 1) % values.length] || currentValue;
	}

	function setItemOption(container, item, optionName, optionValue) {
		var storage = getHiddenStorage(container);
		var baseSettingName = getBaseSettingName(container);
		storage[baseSettingName] = storage[baseSettingName] || {};
		storage[baseSettingName][item.dataset.sortableValue] = Object.assign(
			{},
			defaultOptions,
			storage[baseSettingName][item.dataset.sortableValue] || {},
			{ [optionName]: optionValue },
		);

		setHiddenStorage(container, storage);
		updateItemOptionButtons(container, item);
	}

	function createSortableItem(container, value, label) {
		var item = document.createElement("li");
		var handle = document.createElement("button");
		var itemLabel = document.createElement("span");
		var actions = document.createElement("div");
		var alignButton = document.createElement("button");
		var marginButton = document.createElement("button");
		var removeButton = document.createElement("button");

		item.className = "municipio-sortable-item";
		item.dataset.sortableValue = value;
		item.dataset.sortableLabel = label;

		handle.type = "button";
		handle.className = "municipio-sortable-item__handle";
		handle.setAttribute("aria-label", translate("Move item", "municipio"));

		itemLabel.className = "municipio-sortable-item__label";
		itemLabel.textContent = label;

		actions.className = "municipio-sortable-item__actions";

		alignButton.type = "button";
		alignButton.className = "button button-small municipio-sortable-option";
		alignButton.dataset.sortableOption = "align";
		alignButton.dataset.sortableValues = "left,center,right";

		marginButton.type = "button";
		marginButton.className = "button button-small municipio-sortable-option";
		marginButton.dataset.sortableOption = "margin";
		marginButton.dataset.sortableValues = "none,left,right,both";

		removeButton.type = "button";
		removeButton.className = "button-link-delete municipio-sortable-remove";
		removeButton.textContent = translate("Remove", "municipio");

		actions.appendChild(alignButton);
		actions.appendChild(marginButton);
		actions.appendChild(removeButton);
		item.appendChild(handle);
		item.appendChild(itemLabel);
		item.appendChild(actions);

		updateItemOptionButtons(container, item);

		return item;
	}

	function initializeItemOptions(container) {
		container.querySelectorAll(".municipio-sortable-item").forEach((item) => {
			updateItemOptionButtons(container, item);
		});

		updateHiddenSetting(container);
	}

	function initializeSortable(container) {
		var list = container.querySelector(".municipio-sortable-items");

		if (!list || !$.fn.sortable) {
			return;
		}

		$(list).sortable({
			handle: ".municipio-sortable-item__handle",
			update: () => {
				updateValue(container);
			},
		});

		initializeItemOptions(container);
	}

	document.addEventListener("click", (event) => {
		if (event.target.matches(".municipio-sortable-picker__add")) {
			var container = event.target.closest(".municipio-control--sortable");
			var selectedOptions = Array.prototype.slice.call(
				container.querySelectorAll(
					".municipio-sortable-picker__select option:checked:not(:disabled)",
				),
			);
			var list = container.querySelector(".municipio-sortable-items");

			selectedOptions.forEach((option) => {
				list.appendChild(createSortableItem(container, option.value, option.textContent.trim()));
			});

			updateValue(container);
			return;
		}

		if (event.target.matches(".municipio-sortable-remove")) {
			var item = event.target.closest(".municipio-sortable-item");
			var container = event.target.closest(".municipio-control--sortable");
			item.remove();
			updateValue(container);
			return;
		}

		if (!event.target.matches(".municipio-sortable-option")) {
			return;
		}

		var button = event.target;
		var optionValue = getNextOptionValue(button);
		var optionName = button.dataset.sortableOption;
		var optionItem = button.closest(".municipio-sortable-item");
		var optionContainer = button.closest(".municipio-control--sortable");

		setItemOption(optionContainer, optionItem, optionName, optionValue);
	});

	document.addEventListener("DOMContentLoaded", () => {
		document
			.querySelectorAll(".municipio-control--sortable")
			.forEach(initializeSortable);
	});
})(jQuery);
