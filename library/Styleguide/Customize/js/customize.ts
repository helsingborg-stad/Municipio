wp.customize.bind("ready", () => {
	wp.customize.previewer.bind("design-builder:save", (data) => {
		console.log("Received design-builder:save event in customizer:", data);
		console.log("Updating customizer input with new data...");
		wp.customize("tokens", (setting) => {
			setting.set(JSON.stringify(data));
		});
		console.log("Received from preview:", data);
	});
});
