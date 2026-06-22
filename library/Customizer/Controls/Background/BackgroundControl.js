(() => {
	function updateValue(container) {
		var valueInput = container.querySelector(".municipio-background-value");
		var values = {};

		container.querySelectorAll("[data-background-key]").forEach((input) => {
			values[input.dataset.backgroundKey] = input.value;
		});

		valueInput.value = JSON.stringify(values);
		valueInput.dispatchEvent(new Event("change", { bubbles: true }));
	}

	document.addEventListener("input", (event) => {
		if (
			!event.target.matches(
				".municipio-control--background [data-background-key]",
			)
		) {
			return;
		}

		updateValue(event.target.closest(".municipio-control--background"));
	});

	document.addEventListener("change", (event) => {
		if (
			!event.target.matches(
				".municipio-control--background [data-background-key]",
			)
		) {
			return;
		}

		updateValue(event.target.closest(".municipio-control--background"));
	});
})();
