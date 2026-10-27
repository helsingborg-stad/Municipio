import {
	initializeVagueControlTextIndicator,
	VagueControlTextIndicator,
} from "./vagueControlTextIndicator";

describe("VagueControlTextIndicator", () => {
	let indicator: VagueControlTextIndicator;

	beforeEach(() => {
		document.body.innerHTML = "";
		indicator = new VagueControlTextIndicator();
	});

	afterEach(() => {
		indicator.stop();
	});

	it("marks vague native links and buttons in rendered HTML", () => {
		document.body.innerHTML = `
			<a href="/bygglov"> Read more. </a>
			<button>Click here!</button>
			<a href="/bygglov">Read more about building permits</a>
		`;

		indicator.start();

		expect(document.querySelector("a")?.dataset.a11yError).toBe(
			"Link text is not descriptive enough",
		);
		expect(document.querySelector("button")?.dataset.a11yError).toBe(
			"Button text is not descriptive enough",
		);
		expect(document.querySelectorAll("a")[1].dataset.a11yError).toBeUndefined();
	});

	it("checks controls added dynamically and removes its warning when the label gains context", async () => {
		indicator.start();
		const link = document.createElement("a");
		link.href = "/bygglov";
		link.textContent = "Read more";
		document.body.append(link);

		await Promise.resolve();
		expect(link.dataset.a11yError).toBe("Link text is not descriptive enough");
		expect(link.dataset.tooltip).toBe("Link text is not descriptive enough");

		link.textContent = "Read more about building permits";
		await Promise.resolve();
		expect(link.dataset.a11yError).toBeUndefined();
		expect(link.dataset.tooltip).toBeUndefined();
	});

	it("does not activate the rendered HTML check for visitors", () => {
		document.body.innerHTML = `<a href="/bygglov">Read more</a>`;

		initializeVagueControlTextIndicator();

		expect(document.querySelector("a")?.dataset.a11yError).toBeUndefined();
	});

	it("adds a Styleguide tooltip to a warning that is already present in the HTML", () => {
		document.body.innerHTML = `
			<a href="/bygglov" data-a11y-error="Link text is not descriptive enough">Read more</a>
		`;

		indicator.start();

		expect(document.querySelector("a")?.dataset.tooltip).toBe(
			"Link text is not descriptive enough",
		);
	});
});
