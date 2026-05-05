/**
 * @jest-environment jsdom
 */

import {
	getMatomoTrackableElement,
	handleMatomoTrackingClick,
	initializeMatomoTracking,
	normalizeMatomoTrackableElements,
} from "./matomoTracking";

describe("matomoTracking", () => {
	beforeEach(() => {
		document.body.innerHTML = "";
		window._paq = [];
	});

	afterEach(() => {
		delete window._paq;
	});

	it("normalizes trackable elements with derived aria-label names", () => {
		document.body.innerHTML = `
			<button aria-label="Open menu">
				<span>Ignored text</span>
			</button>
		`;

		normalizeMatomoTrackableElements();

		const button = document.querySelector("button");

		expect(button.dataset.matomoCategory).toBe("UI Interaction");
		expect(button.dataset.matomoAction).toBe("Click");
		expect(button.dataset.matomoName).toBe("Open menu");
	});

	it("preserves explicitly provided matomo attributes", () => {
		document.body.innerHTML = `
			<a
				href="/example"
				data-matomo-category="Custom category"
				data-matomo-action="Open"
				data-matomo-name="Custom name"
			>
				Read more
			</a>
		`;

		normalizeMatomoTrackableElements();

		const link = document.querySelector("a");

		expect(link.dataset.matomoCategory).toBe("Custom category");
		expect(link.dataset.matomoAction).toBe("Open");
		expect(link.dataset.matomoName).toBe("Custom name");
	});

	it("finds the closest trackable element for nested CTA content", () => {
		document.body.innerHTML = `
			<a
				href="/example"
				data-matomo-category="UI Interaction"
				data-matomo-action="Click"
			>
				<span class="c-button__label-text">Read more</span>
			</a>
		`;

		const target = document.querySelector(".c-button__label-text");
		const trackableElement = getMatomoTrackableElement(target);

		expect(trackableElement).toBe(document.querySelector("a"));
	});

	it("tracks clicks for delegated button-like links", () => {
		document.body.innerHTML = `
			<a
				href="/example"
				data-matomo-category="UI Interaction"
				data-matomo-action="Click"
			>
				<span class="c-button__label-text">Read more</span>
			</a>
		`;

		initializeMatomoTracking();

		const target = document.querySelector(".c-button__label-text");
		target.dispatchEvent(new MouseEvent("click", { bubbles: true }));

		expect(window._paq).toEqual([
			["trackEvent", "UI Interaction", "Click", "Read more"],
		]);
		expect(document.querySelector("a").dataset.matomoName).toBe("Read more");
	});

	it("does not track links without backend-provided matomo markers", () => {
		document.body.innerHTML = `
			<a class="c-button" href="/example">
				<span class="c-button__label-text">Read more</span>
			</a>
		`;

		initializeMatomoTracking();

		document
			.querySelector(".c-button__label-text")
			.dispatchEvent(new MouseEvent("click", { bubbles: true }));

		expect(window._paq).toEqual([]);
	});

	it("tracks dynamically injected buttons through delegated listeners", () => {
		initializeMatomoTracking();

		const button = document.createElement("button");
		button.textContent = "Dynamic CTA";
		document.body.appendChild(button);

		button.dispatchEvent(new MouseEvent("click", { bubbles: true }));

		expect(window._paq).toEqual([
			["trackEvent", "UI Interaction", "Click", "Dynamic CTA"],
		]);
		expect(button.dataset.matomoName).toBe("Dynamic CTA");
	});

	it("returns a cleanup function that removes delegated tracking", () => {
		document.body.innerHTML = '<button>Track me</button>';

		const cleanup = initializeMatomoTracking();
		cleanup();

		document
			.querySelector("button")
			.dispatchEvent(new MouseEvent("click", { bubbles: true }));

		expect(window._paq).toEqual([]);
	});

	it("does not push events when the tracking queue is unavailable", () => {
		document.body.innerHTML = '<button>Track me</button>';
		delete window._paq;

		expect(() =>
			handleMatomoTrackingClick({
				target: document.querySelector("button"),
			}),
		).not.toThrow();
	});
});
