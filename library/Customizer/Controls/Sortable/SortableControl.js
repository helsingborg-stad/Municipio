(($) => {
	function updateValue(container) {
		var valueInput = container.querySelector(".municipio-sortable-value");
		var selectedValues = Array.prototype.slice
			.call(container.querySelectorAll(".municipio-sortable-item"))
			.filter((item) => {
				var checkbox = item.querySelector('input[type="checkbox"]');
				return checkbox && checkbox.checked;
			})
			.map((item) => item.dataset.sortableValue);

		if (!valueInput) {
			return;
		}

		valueInput.value = JSON.stringify(selectedValues);
		valueInput.dispatchEvent(new Event("change", { bubbles: true }));
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
	}

	document.addEventListener("change", (event) => {
		if (
			!event.target.matches(
				'.municipio-control--sortable input[type="checkbox"]',
			)
		) {
			return;
		}

		updateValue(event.target.closest(".municipio-control--sortable"));
	});

	document.addEventListener("DOMContentLoaded", () => {
		document
			.querySelectorAll(".municipio-control--sortable")
			.forEach(initializeSortable);
	});
})(jQuery);
