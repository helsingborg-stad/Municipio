(() => {
	function updateValue(container) {
		var selectedValues = Array.prototype.slice
			.call(container.querySelectorAll('input[type="checkbox"]:checked'))
			.map((checkbox) => checkbox.value);
		var valueInput = container.parentElement.querySelector(
			".municipio-multicheck-value",
		);

		if (!valueInput) {
			return;
		}

		valueInput.value = JSON.stringify(selectedValues);
		valueInput.dispatchEvent(new Event("change", { bubbles: true }));
	}

	document.addEventListener("change", (event) => {
		if (
			!event.target.matches(
				'.municipio-multicheck-options input[type="checkbox"]',
			)
		) {
			return;
		}

		updateValue(event.target.closest(".municipio-multicheck-options"));
	});
})();
