type HashUpdateManagerModule = typeof import("./hashUpdateManager");

describe("initializeHashUpdateManager", () => {
	let hashUpdateManager: HashUpdateManagerModule;

	beforeEach(() => {
		jest.resetModules();
		// eslint-disable-next-line @typescript-eslint/no-var-requires
		hashUpdateManager = require("./hashUpdateManager");
		document.body.innerHTML = `
			<h2 id="first-heading" data-update-hash-when-focused>First heading</h2>
		`;
		history.replaceState(null, "", window.location.pathname);
	});

	afterEach(() => {
		jest.restoreAllMocks();
	});

	it("leaves the hash empty until the page is scrolled", () => {
		const heading = document.getElementById("first-heading");
		if (!heading) {
			throw new Error("Expected heading fixture to exist.");
		}

		jest.spyOn(heading, "getBoundingClientRect").mockReturnValue({
			...new DOMRect(),
			top: 500,
		});

		const documentListenerSpy = jest.spyOn(document, "addEventListener");
		const windowListenerSpy = jest.spyOn(window, "addEventListener");
		hashUpdateManager.initializeHashUpdateManager();

		const domContentLoadedHandler = documentListenerSpy.mock.calls.find(
			([type]) => type === "DOMContentLoaded",
		)?.[1] as EventListener;
		domContentLoadedHandler(new Event("DOMContentLoaded"));

		const registeredWindowEvents = windowListenerSpy.mock.calls.map(
			([type]) => type,
		);
		expect(registeredWindowEvents).toEqual(["scroll"]);
		expect(location.hash).toBe("");

		const scrollHandler = windowListenerSpy.mock.calls[0][1] as EventListener;
		scrollHandler(new Event("scroll"));

		expect(location.hash).toBe("#first-heading");
	});
});
