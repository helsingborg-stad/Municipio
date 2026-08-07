import { initializeHeaderLogoScrollShrink } from "./headerLogoScrollShrink";

describe("initializeHeaderLogoScrollShrink", () => {
	beforeEach(() => {
		document.body.innerHTML = `
            <header id="site-header-flexible-upper" class="c-header--logotype-scroll-shrink"></header>
            <header id="site-header-flexible-lower" class="c-header--logotype-scroll-shrink">
                <div class="c-header__lower-left">
                    <div class="c-header__item c-header__item--logotype"></div>
                </div>
            </header>
        `;

		Object.defineProperty(window, "scrollY", {
			configurable: true,
			writable: true,
			value: 0,
		});

		Object.defineProperty(window, "matchMedia", {
			configurable: true,
			writable: true,
			value: jest.fn().mockReturnValue({
				matches: true,
				addEventListener: jest.fn(),
				addListener: jest.fn(),
			}),
		});

		Object.defineProperty(window, "requestAnimationFrame", {
			configurable: true,
			writable: true,
			value: (callback: FrameRequestCallback) => {
				callback(0);
				return 1;
			},
		});
	});

	it("toggles the scroll class on the lower logotype item when the page scrolls", () => {
		initializeHeaderLogoScrollShrink();

		const logotypeItem = document.querySelector<HTMLElement>(
			".c-header__item--logotype",
		);
		const lowerHeader = document.querySelector<HTMLElement>(
			"#site-header-flexible-lower",
		);

		expect(logotypeItem?.classList.contains("is-logotype-scrolled")).toBe(
			false,
		);
		expect(lowerHeader?.classList.contains("is-logotype-scrolled")).toBe(false);

		window.scrollY = 48;
		window.dispatchEvent(new Event("scroll"));

		expect(logotypeItem?.classList.contains("is-logotype-scrolled")).toBe(true);
		expect(lowerHeader?.classList.contains("is-logotype-scrolled")).toBe(true);
	});
});
