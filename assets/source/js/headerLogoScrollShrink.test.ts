import { initializeHeaderLogoScrollShrink } from "./headerLogoScrollShrink";

describe("initializeHeaderLogoScrollShrink", () => {
	beforeEach(() => {
		document.body.innerHTML = `
            <header id="site-header-flexible-upper" class="c-header--logotype-scroll-shrink"></header>
            <header id="site-header-flexible-lower" class="c-header--logotype-scroll-shrink">
                <div class="c-header__lower-left">
                    <div class="c-header__item c-header__item--logotype">
                        <figure class="c-header__logotype">
                            <img alt="Logotype" />
                        </figure>
                    </div>
                </div>
            </header>
        `;

		const logotypeImage = document.querySelector<HTMLImageElement>(
			".c-header__logotype img",
		);

		Object.defineProperty(logotypeImage, "naturalWidth", {
			configurable: true,
			value: 300,
		});

		Object.defineProperty(logotypeImage, "naturalHeight", {
			configurable: true,
			value: 81,
		});

		Object.defineProperty(logotypeImage, "complete", {
			configurable: true,
			value: true,
		});

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

		expect(logotypeItem?.classList.contains("is-logotype-scrolled")).toBe(
			false,
		);

		window.scrollY = 48;
		window.dispatchEvent(new Event("scroll"));

		expect(logotypeItem?.classList.contains("is-logotype-scrolled")).toBe(true);
	});

	it("sets the logo aspect ratio css variable from the logotype image", () => {
		initializeHeaderLogoScrollShrink();

		const logotypeItem = document.querySelector<HTMLElement>(
			".c-header__item--logotype",
		);

		expect(
			logotypeItem?.style.getPropertyValue(
				"--municipio-header-logo-scroll-aspect-ratio",
			),
		).toBe((300 / 81).toString());
	});

	it("prefers brand container dimensions when using brand component", () => {
		document.body.innerHTML = `
            <header id="site-header-flexible-upper" class="c-header--logotype-scroll-shrink"></header>
            <header id="site-header-flexible-lower" class="c-header--logotype-scroll-shrink">
                <div class="c-header__lower-left">
                    <div class="c-header__item c-header__item--logotype">
                        <div class="c-brand c-header__logotype">
                            <div class="c-brand__container">
                                <figure class="c-brand__logotype">
                                    <img alt="Logotype" />
                                </figure>
                                <div class="c-brand__text"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        `;

		const brandContainer = document.querySelector<HTMLElement>(
			".c-brand__container",
		);

		Object.defineProperty(brandContainer, "getBoundingClientRect", {
			configurable: true,
			value: () => ({
				width: 240,
				height: 96,
				top: 0,
				left: 0,
				right: 240,
				bottom: 96,
				x: 0,
				y: 0,
				toJSON: () => ({}),
			}),
		});

		const logotypeImage = document.querySelector<HTMLImageElement>(
			".c-header__logotype img",
		);

		Object.defineProperty(logotypeImage, "naturalWidth", {
			configurable: true,
			value: 300,
		});

		Object.defineProperty(logotypeImage, "naturalHeight", {
			configurable: true,
			value: 300,
		});

		Object.defineProperty(logotypeImage, "complete", {
			configurable: true,
			value: true,
		});

		initializeHeaderLogoScrollShrink();

		const logotypeItem = document.querySelector<HTMLElement>(
			".c-header__item--logotype",
		);

		expect(
			logotypeItem?.style.getPropertyValue(
				"--municipio-header-logo-scroll-aspect-ratio",
			),
		).toBe((240 / 96).toString());
	});
});
