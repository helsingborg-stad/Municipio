(() => {
	const syncAndDispatch = (container, nextValue) => {
		const hiddenInput = container.querySelector(".municipio-slider-value");
		const rangeInput = container.querySelector(".municipio-slider-range");
		const numberInput = container.querySelector(".municipio-slider-number");

		if (
			!(hiddenInput instanceof HTMLInputElement) ||
			!(rangeInput instanceof HTMLInputElement) ||
			!(numberInput instanceof HTMLInputElement)
		) {
			return;
		}

		hiddenInput.value = nextValue;
		rangeInput.value = nextValue;
		numberInput.value = nextValue;

		hiddenInput.dispatchEvent(new Event("input", { bubbles: true }));
		hiddenInput.dispatchEvent(new Event("change", { bubbles: true }));
	};

	document.addEventListener("input", (event) => {
		if (!(event.target instanceof Element)) {
			return;
		}

		const container = event.target.closest(".municipio-control--slider");
		if (!(container instanceof HTMLElement)) {
			return;
		}

		if (
			event.target.matches(".municipio-slider-range, .municipio-slider-number")
		) {
			syncAndDispatch(container, event.target.value);
		}
	});
})();
