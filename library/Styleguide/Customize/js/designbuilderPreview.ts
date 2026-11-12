let previewActive = false;
let pendingState: Record<string, unknown> | null = null;

function sendPendingState(): void {
	if (!previewActive || pendingState === null) {
		return;
	}
	wp.customize.preview.send("tokens:update", pendingState);
	pendingState = null;
}

// WordPress ignores preview messages until the iframe finishes loading and its
// channel is activated. Keep early edits instead of silently losing them.
wp.customize.bind("preview-ready", () => {
	wp.customize.preview.bind("active", () => {
		previewActive = true;
		sendPendingState();
	});
});

// Listen on the document because switching views replaces the editor controls.
document.addEventListener("design-builder:action", (event: Event) => {
	if (!(event instanceof CustomEvent) || !event.detail?.state) {
		return;
	}

	// Capture a snapshot: the runtime may continue changing the same object
	// before the preview becomes active.
	pendingState = JSON.parse(JSON.stringify(event.detail.state));
	sendPendingState();
});
