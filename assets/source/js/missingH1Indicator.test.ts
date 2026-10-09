import { MissingH1Indicator } from "./missingH1Indicator";

describe("MissingH1Indicator", () => {
	let indicator: MissingH1Indicator;

	beforeEach(() => {
		document.body.innerHTML = "";
		indicator = new MissingH1Indicator();
	});

	afterEach(() => {
		indicator.stop();
	});

	it("shows a floating warning when the page has no H1", () => {
		document.body.innerHTML = "<main id=\"main-content\"></main>";
		indicator.start();

		const warning = document.getElementById("a11y-missing-h1-warning");
		expect(warning?.classList.contains("a11y-floating-warning")).toBe(true);
		expect(warning?.textContent).toContain("Page is missing an H1 heading");
		expect(warning?.parentElement?.id).toBe("main-content");
		expect(warning?.parentElement?.firstElementChild).toBe(warning);
	});

	it("does not show a warning when the page has an H1", () => {
		document.body.innerHTML = "<main><h1>Page title</h1></main>";
		indicator.start();

		expect(document.getElementById("a11y-missing-h1-warning")).toBeNull();
	});

	it("removes the warning when an H1 is added dynamically", async () => {
		indicator.start();
		expect(document.getElementById("a11y-missing-h1-warning")).not.toBeNull();

		document.body.append(document.createRange().createContextualFragment("<h1>Page title</h1>"));
		await Promise.resolve();

		expect(document.getElementById("a11y-missing-h1-warning")).toBeNull();
	});
});
