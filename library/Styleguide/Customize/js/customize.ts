document.addEventListener("DOMContentLoaded", () => {
	const designBuilder = document.querySelector("design-builder");
	designBuilder?.addEventListener("design-builder:save", (event: Event) => {
		const customEvent = event as CustomEvent<{ state: any }>;
		const endpoint =
			window.wpApiSettings.root + "municipio/v1/customize/design";
		fetch(endpoint, {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"X-WP-Nonce": window.wpApiSettings.nonce,
			},
			body: JSON.stringify({
				design: customEvent.detail.state,
			}),
		})
			.then((response) => {
				if (!response.ok) {
					throw new Error("Network response was not ok");
				}
				return response.json();
			})
			.then((data) => {
				console.log("Design saved successfully:", data);
			})
			.catch((error) => {
				console.error("Error saving design:", error);
			});
	});
});
