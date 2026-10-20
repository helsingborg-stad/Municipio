type ViewTransitionsModule = typeof import("./viewTransitions");

describe("initializeViewTransitions", () => {
	let viewTransitions: ViewTransitionsModule;

	beforeEach(() => {
		jest.resetModules();
		Object.defineProperty(window, "CSS", {
			configurable: true,
			value: { supports: jest.fn().mockReturnValue(true) },
		});
		// eslint-disable-next-line @typescript-eslint/no-var-requires
		viewTransitions = require("./viewTransitions");
		document.body.innerHTML = `
			<a href="#destination" data-view-transition-target="post">
				<span>Read more</span>
			</a>
		`;
	});

	it("designates a pointer-selected post as the transition source", () => {
		const target = document.querySelector<HTMLElement>("[data-view-transition-target]");
		if (!target) {
			throw new Error("Expected a post transition target.");
		}

		viewTransitions.initializeViewTransitions();
		target.dispatchEvent(new MouseEvent("click", { bubbles: true, button: 0, detail: 1 }));

		expect(target.classList.contains("is-view-transition-target")).toBe(true);
	});

	it("does not animate keyboard navigation", () => {
		const target = document.querySelector<HTMLElement>("[data-view-transition-target]");
		if (!target) {
			throw new Error("Expected a post transition target.");
		}

		viewTransitions.initializeViewTransitions();
		target.dispatchEvent(new MouseEvent("click", { bubbles: true, button: 0, detail: 0 }));

		expect(target.classList.contains("is-view-transition-target")).toBe(false);
	});
});
