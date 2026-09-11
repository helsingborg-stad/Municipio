/**
 * Highlight elements based on URL hash matching a data attribute.
 */
export class HashHighlightManager {
	private static lastManualHashChange = 0;

	/**
	 * Initialize the hash highlight system.
	 * Finds all elements with `data-highlight-on-hash-match` and toggles
	 * the class specified in `data-highlight-on-hash-match-class` when the URL hash matches.
	 */
	public static init(): void {
		document.addEventListener("DOMContentLoaded", () => {
			const items = document.querySelectorAll<HTMLElement>(
				"[data-highlight-on-hash-match]",
			);

			if (!items.length) return;

			const updateHighlights = (): void => {
				const currentHash = decodeURIComponent(location.hash.replace(/^#/, ""));
				items.forEach((item) => {
					const targetHash = item.dataset.highlightOnHashMatch;
					const activeClass =
						item.dataset.highlightOnHashMatchClass ?? "is-current";
					const shouldBeActive = targetHash === currentHash;
					item.classList.toggle(activeClass, shouldBeActive);

					if (shouldBeActive) {
						scrollActiveItemIntoView(item);
					}
				});
			};

			// Debounce so fast scrolling (which can change the active heading many
			// times a second) doesn't repeatedly toggle classes and restart the
			// scroll-into-view animation; only the settled hash gets applied.
			let debounceTimeoutId: ReturnType<typeof setTimeout> | undefined;
			const scheduleHighlightUpdate = (): void => {
				clearTimeout(debounceTimeoutId);
				debounceTimeoutId = setTimeout(
					updateHighlights,
					HIGHLIGHT_UPDATE_DEBOUNCE_MS,
				);
			};

			window.addEventListener("hashchange", scheduleHighlightUpdate);
			updateHighlights(); // initial check, applied immediately
		});
	}
}

const HIGHLIGHT_UPDATE_DEBOUNCE_MS = 150;

/**
 * Nudge the item's own scrollable ancestor (not the page) so a newly active
 * item is never hidden behind a sticky header or below the visible area.
 */
function scrollActiveItemIntoView(item: HTMLElement): void {
	const container = findScrollableAncestor(item);

	if (!container) {
		return;
	}

	const stickyHeader = container.querySelector<HTMLElement>(".s-toc__header");
	const stickyHeaderHeight = stickyHeader?.getBoundingClientRect().height ?? 0;

	const navList = container.querySelector<HTMLElement>(".s-nav-toc");
	const navListMarginTop = navList ? parseFloat(getComputedStyle(navList).marginTop) : 0;

	const itemRect = item.getBoundingClientRect();
	const containerRect = container.getBoundingClientRect();
	const visibleTop = containerRect.top + stickyHeaderHeight + navListMarginTop;

	if (itemRect.top < visibleTop) {
		container.scrollBy({ top: itemRect.top - visibleTop, behavior: "smooth" });
	} else if (itemRect.bottom > containerRect.bottom) {
		container.scrollBy({
			top: itemRect.bottom - containerRect.bottom,
			behavior: "smooth",
		});
	}
}

function findScrollableAncestor(element: HTMLElement): HTMLElement | null {
	let node = element.parentElement;

	while (node) {
		const isScrollable = /(auto|scroll)/.test(getComputedStyle(node).overflowY);

		if (isScrollable && node.scrollHeight > node.clientHeight) {
			return node;
		}

		node = node.parentElement;
	}

	return null;
}

export function initializeHashHighlightManager(): void {
	HashHighlightManager.init();
}
