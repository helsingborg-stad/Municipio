import { A11yWarningMarkers } from "./a11yWarningMarkers";

describe("A11yWarningMarkers", () => {
	let markers: A11yWarningMarkers;

	beforeEach(() => {
		document.body.innerHTML = "";
		markers = new A11yWarningMarkers();
	});

	afterEach(() => {
		markers.stop();
	});

	it("renders a marker outside the warning target", () => {
		document.body.innerHTML = `
			<h2 data-a11y-error="Invalid heading level" data-tooltip-initialized>Skipped heading</h2>
		`;

		markers.start();

		const heading = document.querySelector("h2");
		const marker = document.querySelector(".a11y-warning-marker");
		expect(marker?.textContent).toBe("[!]");
		expect(marker?.parentElement?.classList.contains("a11y-warning-markers")).toBe(true);
		expect(heading?.contains(marker ?? null)).toBe(false);
		expect(heading?.dataset.a11yWarningAnchor).toBe("");
	});

	it("adds and removes markers as warnings change", async () => {
		markers.start();
		const link = document.createElement("a");
		link.dataset.a11yError = "Link text is not descriptive enough";
		link.setAttribute("data-tooltip-initialized", "");
		document.body.append(link);

		await Promise.resolve();
		expect(document.querySelector(".a11y-warning-marker")).not.toBeNull();

		link.removeAttribute("data-a11y-error");
		await Promise.resolve();
		expect(document.querySelector(".a11y-warning-marker")).toBeNull();
		expect(link.dataset.a11yWarningAnchor).toBeUndefined();
	});

	it("uses the image overlay variant for image warnings", () => {
		document.body.innerHTML = `
			<div class="c-image__image-wrapper" data-a11y-marker data-tooltip-initialized></div>
		`;

		markers.start();

		expect(document.querySelector(".a11y-warning-marker--image")).not.toBeNull();
	});
});
