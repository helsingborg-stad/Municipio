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
			<a href="/bygglov">Läs mer</a>
			<button>Klicka här</button>
			<a href="/bygglov">Läs mer om bygglov</a>
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
		link.textContent = "Läs mer";
		document.body.append(link);

		await Promise.resolve();
		expect(link.dataset.a11yError).toBe("Link text is not descriptive enough");

		link.textContent = "Läs mer om bygglov";
		await Promise.resolve();
		expect(link.dataset.a11yError).toBeUndefined();
	});

	it("does not activate the rendered HTML check for visitors", () => {
		document.body.innerHTML = `<a href="/bygglov">Läs mer</a>`;

		initializeVagueControlTextIndicator();

		expect(document.querySelector("a")?.dataset.a11yError).toBeUndefined();
	});
});
