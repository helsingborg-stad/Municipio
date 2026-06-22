(() => {
	function getFields(container) {
		try {
			return JSON.parse(container.dataset.fields || "{}");
		} catch (error) {
			return {};
		}
	}

	function updateValue(container) {
		var valueInput = container.querySelector(".municipio-repeater-value");
		var rows = Array.prototype.slice
			.call(container.querySelectorAll(".municipio-repeater-row"))
			.map((row) => {
				var rowValue = {};

				row.querySelectorAll("[data-repeater-key]").forEach((input) => {
					rowValue[input.dataset.repeaterKey] = input.value;
				});

				return rowValue;
			});

		valueInput.value = JSON.stringify(rows);
		valueInput.dispatchEvent(new Event("change", { bubbles: true }));
	}

	function createRow(container) {
		var fields = getFields(container);
		var row = document.createElement("div");
		row.className = "municipio-repeater-row";

		Object.keys(fields).forEach((fieldKey) => {
			var field = fields[fieldKey];
			var label = document.createElement("label");
			var labelText = document.createElement("span");
			var input = document.createElement("input");

			label.className = "municipio-repeater-field";
			labelText.textContent = field.label || fieldKey;
			input.type = field.type || "text";
			input.dataset.repeaterKey = fieldKey;
			input.value = field.default || "";

			label.appendChild(labelText);
			label.appendChild(input);
			row.appendChild(label);
		});

		var removeButton = document.createElement("button");
		removeButton.type = "button";
		removeButton.className = "button-link-delete municipio-repeater-remove";
		removeButton.textContent = "Remove";
		row.appendChild(removeButton);

		return row;
	}

	document.addEventListener("click", (event) => {
		if (event.target.matches(".municipio-repeater-add")) {
			var container = event.target.closest(".municipio-control--repeater");
			container
				.querySelector(".municipio-repeater-rows")
				.appendChild(createRow(container));
			updateValue(container);
			return;
		}

		if (event.target.matches(".municipio-repeater-remove")) {
			var repeater = event.target.closest(".municipio-control--repeater");
			event.target.closest(".municipio-repeater-row").remove();
			updateValue(repeater);
		}
	});

	document.addEventListener("input", (event) => {
		if (!event.target.matches(".municipio-repeater-field input")) {
			return;
		}

		updateValue(event.target.closest(".municipio-control--repeater"));
	});
})();
