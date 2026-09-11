import { getTopOffsetPx, topOffsetChangeEvent } from "./headerScrollOffset";

// Absorbs sub-pixel rounding from scroll corrections so the boundary heading isn't skipped.
const OFFSET_TOLERANCE_PX = 2;

export class HashUpdateManager {
	private static currentHash: string | null = null;

	/**
	 * Initialize the hash update tracking.
	 */
	public static init(): void {
		document.addEventListener("DOMContentLoaded", () => {
			const elements = document.querySelectorAll<HTMLElement>(
				"[data-update-hash-when-focused]",
			);

			if (!elements.length) {
				return;
			}

			// Seed with the requested hash so a deep link isn't immediately rewritten.
			HashUpdateManager.currentHash =
				decodeURIComponent(location.hash.replace(/^#/, "")) || null;

			window.addEventListener("scroll", () =>
				HashUpdateManager.handleIntersect(),
			);
			window.addEventListener("resize", () =>
				HashUpdateManager.handleIntersect(),
			);
			// A header resize changes the offset without a scroll/resize of its own.
			window.addEventListener(topOffsetChangeEvent, () =>
				HashUpdateManager.handleIntersect(),
			);
		});
	}

	private static handleIntersect(): void {
		const elements = document.querySelectorAll<HTMLElement>(
			"[data-update-hash-when-focused]",
		);
		if (elements.length === 0) return;

		let candidate: HTMLElement | null = null;
		let candidateDistance = -Infinity;

		const offset = getTopOffsetPx();

		elements.forEach((el) => {
			const top = el.getBoundingClientRect().top;

			// Respect the shared top offset so the highlight matches what's visible below the header.
			if (top <= offset + OFFSET_TOLERANCE_PX && top > candidateDistance) {
				candidate = el;
				candidateDistance = top;
			}
		});

		if (!candidate) candidate = elements[0];

		const newHash = candidate.dataset.updateHashValue ?? candidate.id;

		if (!newHash || HashUpdateManager.currentHash === newHash) return;

		HashUpdateManager.currentHash = newHash;
		history.replaceState(null, "", `#${newHash}`);
		window.dispatchEvent(new HashChangeEvent("hashchange"));
	}
}

export function initializeHashUpdateManager(): void {
	HashUpdateManager.init();
}
