const targetSelector = "[data-view-transition-target]";

/**
 * Designate only the selected list item as the source of a cross-document
 * transition. Naming every item would cause unrelated results to animate too.
 */
export const initializeViewTransitions = (): void => {
	if (!CSS.supports("view-transition-name: none")) {
		return;
	}

	document.addEventListener("click", (event) => {
		if (
			event.defaultPrevented ||
			event.detail === 0 ||
			event.button !== 0 ||
			event.metaKey ||
			event.ctrlKey ||
			event.shiftKey ||
			event.altKey
		) {
			return;
		}

		const link = (event.target as Element | null)?.closest<HTMLAnchorElement>("a[href]");
		if (
			!link ||
			link.target ||
			link.hasAttribute("download") ||
			link.origin !== window.location.origin
		) {
			return;
		}

		const target = link.closest<HTMLElement>(targetSelector);
		target?.classList.add("is-view-transition-target");
	});
};
