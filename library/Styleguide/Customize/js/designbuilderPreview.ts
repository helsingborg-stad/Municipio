jQuery(() => {
	const designBuilderElement = document.querySelector("design-builder");

	if (!designBuilderElement) {
		console.error("Design Builder element not found in the preview.");
		return;
	}

	wp.customize.preview.bind("active", () => {
		designBuilderElement?.addEventListener(
			"design-builder:action",
			(event: Event) => {
				wp.customize.preview.send("tokens:update", event.detail.state);
			},
		);
	});
});
