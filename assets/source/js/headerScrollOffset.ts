const topOffsetProperty = "--municipio-top-offset";
const topOffsetSpacing = "var(--base, 8px) * var(--space, 1) * 4";
export const topOffsetChangeEvent = "municipio:top-offset-change";

let currentTopOffsetPx = 0;
let isInitialAlignmentWindowOpen = true;
let hasUserScrolled = false;

/**
 * Get the resolved pixel value of the shared top offset, kept in sync with the
 * visible sticky header height.
 *
 * @returns The offset in pixels.
 */
export function getTopOffsetPx(): number {
	return currentTopOffsetPx;
}

function measureTopOffsetPx(): number {
	const probe = document.createElement("div");
	probe.style.position = "absolute";
	probe.style.visibility = "hidden";
	probe.style.height = "var(--municipio-top-offset)";
	document.documentElement.appendChild(probe);
	const heightPx = probe.getBoundingClientRect().height;
	probe.remove();
	return heightPx;
}

/**
 * How much space a sticky element reserves at the top once stuck: its own
 * intrinsic height plus its CSS sticky inset (e.g. pushed down by the WP
 * admin bar). Deliberately not getBoundingClientRect().bottom/top, which
 * reflect the element's current document position and can be huge for a
 * sticky element (e.g. quicklinks) that hasn't stuck yet.
 */
function getStickyContributionPx(header: HTMLElement): number {
	const stickyInset = parseFloat(getComputedStyle(header).top) || 0;
	return header.getBoundingClientRect().height + stickyInset;
}

function markUserScrolled(): void {
	hasUserScrolled = true;
}

/**
 * Re-align a requested hash target while the offset is still settling (fonts
 * and images can resize the header after the first paint), so a page reload
 * lands in the same position a same-page click would.
 */
function alignInitialHashTarget(offsetPx: number): void {
	if (!isInitialAlignmentWindowOpen || hasUserScrolled || !location.hash) {
		return;
	}

	const target = document.getElementById(
		decodeURIComponent(location.hash.slice(1)),
	);

	if (!target) {
		return;
	}

	// Instant, not smooth: a smooth glide fires many intermediate scroll
	// events that can momentarily point the hash-highlight tracker at the
	// wrong heading before it settles.
	window.scrollTo({
		top: target.getBoundingClientRect().top + window.scrollY - offsetPx,
		behavior: "instant",
	});
}

/**
 * Set the shared scroll offset to the height of the visible sticky header.
 *
 * @returns void
 */
export function initializeHeaderScrollOffset(): void {
	const initializeWhenReady = (): void => {
		const stickyHeaders = Array.from(
			document.querySelectorAll<HTMLElement>(".c-header--sticky"),
		);

		window.addEventListener("wheel", markUserScrolled, {
			passive: true,
			once: true,
		});
		window.addEventListener("touchmove", markUserScrolled, {
			passive: true,
			once: true,
		});
		window.addEventListener("keydown", markUserScrolled, { once: true });

		const updateOffset = (): void => {
			// See getStickyContributionPx: intrinsic height + sticky inset, not
			// viewport-relative position.
			const stickyHeaderContribution = stickyHeaders.length
				? Math.max(...stickyHeaders.map(getStickyContributionPx))
				: 0;

			document.documentElement.style.setProperty(
				topOffsetProperty,
				`calc(${stickyHeaderContribution}px + ${topOffsetSpacing})`,
			);

			currentTopOffsetPx = measureTopOffsetPx();
			alignInitialHashTarget(currentTopOffsetPx);
			window.dispatchEvent(new Event(topOffsetChangeEvent));
		};

		updateOffset();
		// Give the alignment one final correction once fonts/images have settled.
		window.addEventListener(
			"load",
			() => {
				updateOffset();
				isInitialAlignmentWindowOpen = false;
			},
			{ once: true },
		);

		if (stickyHeaders.length === 0) {
			return;
		}

		if (typeof ResizeObserver === "function") {
			const resizeObserver = new ResizeObserver(updateOffset);
			stickyHeaders.forEach((header) => {
				resizeObserver.observe(header);
			});
			return;
		}

		window.addEventListener("resize", updateOffset, { passive: true });
	};

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initializeWhenReady, {
			once: true,
		});
		return;
	}

	initializeWhenReady();
}
