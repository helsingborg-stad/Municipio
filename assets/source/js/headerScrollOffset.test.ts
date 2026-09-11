type HeaderScrollOffsetModule = typeof import("./headerScrollOffset");

describe("initializeHeaderScrollOffset", () => {
	let headerScrollOffset: HeaderScrollOffsetModule;

	beforeEach(() => {
		jest.resetModules();
		// eslint-disable-next-line @typescript-eslint/no-var-requires
		headerScrollOffset = require("./headerScrollOffset");
		document.documentElement.style.removeProperty(
			"--municipio-top-offset",
		);
		document.body.innerHTML = "";
		location.hash = "";
	});

	afterEach(() => {
		jest.restoreAllMocks();
	});

	it("sets the offset to the tallest visible sticky header", () => {
		document.body.innerHTML = `
			<header class="c-header--sticky" id="desktop-header"></header>
			<header class="c-header--sticky" id="mobile-header"></header>
		`;
		const desktopHeader = document.getElementById("desktop-header");
		const mobileHeader = document.getElementById("mobile-header");

		if (!desktopHeader || !mobileHeader) {
			throw new Error("Expected sticky header fixtures to exist.");
		}

		jest.spyOn(desktopHeader, "getBoundingClientRect").mockReturnValue({
			...new DOMRect(),
			height: 144,
		});
		jest.spyOn(mobileHeader, "getBoundingClientRect").mockReturnValue({
			...new DOMRect(),
			height: 0,
		});

		headerScrollOffset.initializeHeaderScrollOffset();

		expect(
			document.documentElement.style.getPropertyValue(
				"--municipio-top-offset",
			),
		).toBe("calc(144px + var(--base, 8px) * var(--space, 1) * 8)");
	});

	it("falls back to just the spacing offset when no sticky header exists", () => {
		headerScrollOffset.initializeHeaderScrollOffset();

		expect(
			document.documentElement.style.getPropertyValue(
				"--municipio-top-offset",
			),
		).toBe("calc(0px + var(--base, 8px) * var(--space, 1) * 8)");
	});

	it("exposes the resolved pixel value through getTopOffsetPx", () => {
		document.body.innerHTML = `<header class="c-header--sticky" id="header"></header>`;
		const header = document.getElementById("header");

		if (!header) {
			throw new Error("Expected sticky header fixture to exist.");
		}

		jest.spyOn(header, "getBoundingClientRect").mockReturnValue({
			...new DOMRect(),
			height: 144,
		});
		jest
			.spyOn(HTMLElement.prototype, "getBoundingClientRect")
			.mockReturnValue({ ...new DOMRect(), height: 200 });

		headerScrollOffset.initializeHeaderScrollOffset();

		expect(headerScrollOffset.getTopOffsetPx()).toBe(200);
	});

	it("aligns a requested hash target using the resolved offset once", () => {
		document.body.innerHTML = `
			<header class="c-header--sticky" id="header"></header>
			<h2 id="target-heading">Heading</h2>
		`;
		const header = document.getElementById("header");
		const target = document.getElementById("target-heading");

		if (!header || !target) {
			throw new Error("Expected header and target fixtures to exist.");
		}

		location.hash = "#target-heading";
		jest.spyOn(header, "getBoundingClientRect").mockReturnValue({
			...new DOMRect(),
			height: 144,
		});
		jest.spyOn(target, "getBoundingClientRect").mockReturnValue({
			...new DOMRect(),
			top: 500,
		});
		Object.defineProperty(window, "scrollY", {
			configurable: true,
			value: 100,
		});
		const scrollTo = jest.fn();
		Object.defineProperty(window, "scrollTo", {
			configurable: true,
			value: scrollTo,
		});

		headerScrollOffset.initializeHeaderScrollOffset();

		const offsetPx = headerScrollOffset.getTopOffsetPx();
		expect(scrollTo).toHaveBeenCalledTimes(1);
		expect(scrollTo).toHaveBeenCalledWith({
			top: 500 + 100 - offsetPx,
			behavior: "instant",
		});
	});

	it("aligns a requested hash target using only the spacing offset when the sticky header is disabled", () => {
		document.body.innerHTML = `<h2 id="target-heading">Heading</h2>`;
		const target = document.getElementById("target-heading");

		if (!target) {
			throw new Error("Expected target fixture to exist.");
		}

		location.hash = "#target-heading";
		// jest.spyOn reuses an existing prototype mock instead of creating a
		// separate per-instance one, so branch on `this` to give the probe and
		// the target different rects from a single prototype-level mock.
		jest
			.spyOn(HTMLElement.prototype, "getBoundingClientRect")
			.mockImplementation(function (this: HTMLElement) {
				if (this === target) {
					return { ...new DOMRect(), top: 500 };
				}
				return { ...new DOMRect(), height: 64 };
			});
		Object.defineProperty(window, "scrollY", {
			configurable: true,
			value: 100,
		});
		const scrollTo = jest.fn();
		Object.defineProperty(window, "scrollTo", {
			configurable: true,
			value: scrollTo,
		});

		headerScrollOffset.initializeHeaderScrollOffset();

		expect(headerScrollOffset.getTopOffsetPx()).toBe(64);
		expect(scrollTo).toHaveBeenCalledWith({
			top: 500 + 100 - 64,
			behavior: "instant",
		});
	});

	it("keeps re-aligning the hash target until the load event settles", () => {
		document.body.innerHTML = `
			<header class="c-header--sticky" id="header"></header>
			<h2 id="target-heading">Heading</h2>
		`;
		const header = document.getElementById("header");
		const target = document.getElementById("target-heading");

		if (!header || !target) {
			throw new Error("Expected header and target fixtures to exist.");
		}

		location.hash = "#target-heading";
		const headerRect = jest.spyOn(header, "getBoundingClientRect");
		headerRect.mockReturnValue({ ...new DOMRect(), height: 144 });
		jest.spyOn(target, "getBoundingClientRect").mockReturnValue({
			...new DOMRect(),
			top: 500,
		});
		Object.defineProperty(window, "scrollY", {
			configurable: true,
			value: 100,
		});
		const scrollTo = jest.fn();
		Object.defineProperty(window, "scrollTo", {
			configurable: true,
			value: scrollTo,
		});

		// Capture this run's own listeners so leftover listeners from other
		// tests in this file can't cause cross-test interference.
		const addEventListenerSpy = jest.spyOn(window, "addEventListener");
		headerScrollOffset.initializeHeaderScrollOffset();
		expect(scrollTo).toHaveBeenCalledTimes(1);

		const resizeHandler = addEventListenerSpy.mock.calls.find(
			([type]) => type === "resize",
		)?.[1] as EventListener;
		const loadHandler = addEventListenerSpy.mock.calls.find(
			([type]) => type === "load",
		)?.[1] as EventListener;
		addEventListenerSpy.mockRestore();

		// A late-loading logo grows the header, so a resize should re-align.
		headerRect.mockReturnValue({ ...new DOMRect(), height: 200 });
		resizeHandler(new Event("resize"));
		expect(scrollTo).toHaveBeenCalledTimes(2);

		// The load event gives one final correction, then closes the window.
		loadHandler(new Event("load"));
		expect(scrollTo).toHaveBeenCalledTimes(3);

		resizeHandler(new Event("resize"));
		expect(scrollTo).toHaveBeenCalledTimes(3);
	});
});