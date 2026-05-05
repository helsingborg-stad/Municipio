const matomoTrackingSelector = [
	"button",
	"a.btn",
	"a.c-button",
	'a[role="button"]',
].join(", ");

const defaultTrackingCategory = "UI Interaction";
const defaultTrackingAction = "Click";

/**
 * Normalize whitespace in tracking labels.
 *
 * @param {string | null | undefined} value The label to normalize.
 * @returns {string} The normalized label.
 */
export function normalizeTrackingLabel(value) {
	return (value || "").replace(/\s+/g, " ").trim();
}

/**
 * Derive a Matomo tracking name for a CTA element.
 *
 * @param {Element} element The element to derive a name from.
 * @returns {string} The derived tracking name.
 */
export function getMatomoTrackingName(element) {
	return normalizeTrackingLabel(
		element.getAttribute("data-matomo-name") ||
			element.getAttribute("aria-label") ||
			element.getAttribute("title") ||
			element.innerText ||
			element.textContent,
	);
}

/**
 * Apply default Matomo data attributes unless they are explicitly set.
 *
 * @param {HTMLElement} element The element to normalize.
 * @returns {void}
 */
export function applyMatomoTrackingAttributes(element) {
	const trackingName = getMatomoTrackingName(element);

	if (!element.dataset.matomoCategory) {
		element.dataset.matomoCategory = defaultTrackingCategory;
	}

	if (!element.dataset.matomoAction) {
		element.dataset.matomoAction = defaultTrackingAction;
	}

	if (!element.dataset.matomoName && trackingName) {
		element.dataset.matomoName = trackingName;
	}
}

/**
 * Find a trackable CTA element from an event target.
 *
 * @param {EventTarget | null} target The event target.
 * @returns {HTMLElement | null} The closest trackable element.
 */
export function getMatomoTrackableElement(target) {
	if (!(target instanceof Element)) {
		return null;
	}

	const element = target.closest(matomoTrackingSelector);

	return element instanceof HTMLElement ? element : null;
}

/**
 * Normalize trackable elements in the current DOM.
 *
 * @returns {void}
 */
export function normalizeMatomoTrackableElements() {
	document
		.querySelectorAll(matomoTrackingSelector)
		.forEach((element) =>
			applyMatomoTrackingAttributes(element),
		);
}

/**
 * Handle delegated click tracking for Matomo.
 *
 * @param {MouseEvent} event The click event.
 * @returns {void}
 */
export function handleMatomoTrackingClick(event) {
	const trackableElement = getMatomoTrackableElement(event.target);

	if (!trackableElement) {
		return;
	}

	applyMatomoTrackingAttributes(trackableElement);

	const trackingQueue = window._paq;

	if (!trackingQueue || typeof trackingQueue.push !== "function") {
		return;
	}

	const trackingName = trackableElement.dataset.matomoName || "";

	if (!trackingName) {
		return;
	}

	trackingQueue.push([
		"trackEvent",
		trackableElement.dataset.matomoCategory || defaultTrackingCategory,
		trackableElement.dataset.matomoAction || defaultTrackingAction,
		trackingName,
	]);
}

/**
 * Initialize global Matomo CTA tracking.
 *
 * @returns {void}
 */
export function initializeMatomoTracking() {
	document.addEventListener("click", handleMatomoTrackingClick);

	if (document.readyState === "loading") {
		document.addEventListener(
			"DOMContentLoaded",
			normalizeMatomoTrackableElements,
			{ once: true },
		);

		return;
	}

	normalizeMatomoTrackableElements();
}
