import { HeadingHierarchyIndicator } from "./headingHierarchyIndicator";

describe("HeadingHierarchyIndicator", () => {
	let indicator: HeadingHierarchyIndicator;

	beforeEach(() => {
		document.body.innerHTML = "";
		indicator = new HeadingHierarchyIndicator();
	});

	afterEach(() => {
		indicator.stop();
	});

	it("marks headings that skip a level within the main content", () => {
		document.body.innerHTML = `
			<main id="main-content">
				<h1>Page title</h1>
				<h2>Section</h2>
				<h4>Skipped level</h4>
				<h3>Valid lower level</h3>
			</main>
		`;

		indicator.start();

		const headings = document.querySelectorAll("h1, h2, h3, h4");
		expect(headings[2].dataset.a11yError).toBe(
			"Invalid heading level (level is skipped)",
		);
		expect(headings[2].dataset.tooltip).toBe(
			"Invalid heading level (level is skipped)",
		);
		expect(headings[3].dataset.a11yError).toBeUndefined();
	});

	it("updates the warning when dynamically added headings correct the hierarchy", async () => {
		document.body.innerHTML = `<main id="main-content"><h2>Section</h2><h4>Subsection</h4></main>`;
		indicator.start();

		const main = document.querySelector("main");
		const heading = document.querySelector("h4");
		expect(heading?.dataset.a11yError).toBe(
			"Invalid heading level (level is skipped)",
		);

		main?.insertBefore(document.createRange().createContextualFragment("<h3>Subsection</h3>"), heading);
		await Promise.resolve();

		expect(heading?.dataset.a11yError).toBeUndefined();
	});
});
