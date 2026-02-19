export const updateUrlParams = (
	params: Record<string, unknown>,
	clearAll: boolean = false,
) => {
	const url = new URL(window.location.href);

	if (clearAll) {
		url.search = "";
	}

	url.searchParams.forEach((_v, k) => {
		if (!Object.keys(params).includes(k)) {
			url.searchParams.delete(k);
		}
	});

	Object.entries(params).forEach(([key, value]) => {
		// Handle array values (multiselect) - key should already have [] suffix
		if (Array.isArray(value)) {
			// Clean up both with and without [] suffix
			const baseKey = key.replace(/\[\]$/, "");
			url.searchParams.delete(baseKey);
			url.searchParams.delete(key);

			if (value.length > 0) {
				value.forEach((v) => {
					if (v !== null && v !== undefined && v !== "") {
						url.searchParams.append(key, String(v));
					}
				});
			}
		} else if (value !== null && value !== undefined && value !== "") {
			url.searchParams.set(key, String(value));
		} else {
			url.searchParams.delete(key);
		}
	});

	history.pushState({ postsListAsync: true }, "", url.toString());
	window.dispatchEvent(new Event("pushstate"));
};
