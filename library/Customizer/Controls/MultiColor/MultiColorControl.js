(($) => {
	function updateValue(container) {
		var valueInput = container.querySelector(".municipio-multicolor-value");
		var values = {};

		container
			.querySelectorAll(".municipio-multicolor-input")
			.forEach((input) => {
				values[input.dataset.choice] = input.value;
			});

		valueInput.value = JSON.stringify(values);
		valueInput.dispatchEvent(new Event("change", { bubbles: true }));
	}

	function initializeColorPicker(input) {
		if (!$.fn.wpColorPicker) {
			return;
		}

		$(input).wpColorPicker({
			change: () => {
				window.setTimeout(() => {
					updateValue(input.closest(".municipio-control--multicolor"));
				}, 0);
			},
			clear: () => {
				window.setTimeout(() => {
					updateValue(input.closest(".municipio-control--multicolor"));
				}, 0);
			},
		});
	}

	document.addEventListener("input", (event) => {
		if (!event.target.matches(".municipio-multicolor-input")) {
			return;
		}

		updateValue(event.target.closest(".municipio-control--multicolor"));
	});

	document.addEventListener("DOMContentLoaded", () => {
		document
			.querySelectorAll(".municipio-multicolor-input")
			.forEach(initializeColorPicker);
	});
})(jQuery);
