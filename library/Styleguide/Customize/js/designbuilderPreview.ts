jQuery(() => {
	wp.customize.preview.bind("active", () => {
		document
			.querySelector("design-builder")
			?.addEventListener("design-builder:save", (event: Event) => {
				wp.customize.preview.send("design-builder:save", event.detail.state);
			});
	});
});
