jQuery(() => {
	// The Design Builder can be re-rendered while switching modes. Its action
	// events are composed and bubble, so listen once on the document instead of
	// binding to a transient custom-element instance.
	document.addEventListener("design-builder:action", (event: Event) => {
		if (!(event instanceof CustomEvent) || !event.detail?.state) {
			return;
		}

		// Send a snapshot. The runtime keeps mutating its override-state object
		// while controls are edited, whereas Customizer needs the exact state that
		// produced this change notification.
		wp.customize.preview.send("tokens:update", JSON.parse(JSON.stringify(event.detail.state)));
	});
});
