import {
	getLogoAspectRatio,
	syncHeaderLogoScrollAspectRatio,
} from "./customizerHeaderLogoScrollAspectRatioPreview";

describe("customizerHeaderLogoScrollAspectRatioPreview", () => {
	let sendSpy: jest.Mock;

	beforeEach(() => {
		sendSpy = jest.fn();

		document.body.innerHTML = `
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

		Object.defineProperty(window, "requestAnimationFrame", {
			configurable: true,
			writable: true,
			value: (callback: FrameRequestCallback) => {
				callback(0);
				return 1;
			},
		});

		Object.defineProperty(document, "fonts", {
			configurable: true,
			value: {
				ready: Promise.resolve(),
			},
		});

		(
			global as typeof global & { ResizeObserver: typeof ResizeObserver }
		).ResizeObserver = class {
			observe(): void {}
			unobserve(): void {}
			disconnect(): void {}
		} as unknown as typeof ResizeObserver;

		window.wp = {
			customize: {
				preview: {
					bind: jest.fn(),
					send: sendSpy,
				},
			},
		};
	});

	it("uses the logotype image natural dimensions when available", () => {
		const logotypeItem = document.querySelector<HTMLElement>(
			".c-header__item--logotype",
		);
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

		expect(getLogoAspectRatio(logotypeItem!)).toBe(300 / 81);
	});

	it("prefers the rendered brand container ratio when brand markup is used", () => {
		document.body.innerHTML = `
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

		const logotypeItem = document.querySelector<HTMLElement>(
			".c-header__item--logotype",
		);
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

		expect(getLogoAspectRatio(logotypeItem!)).toBe(240 / 96);
	});

	it("stores the calculated aspect ratio in the hidden customizer setting", () => {
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

		expect(syncHeaderLogoScrollAspectRatio()).toBe(true);
		expect(sendSpy).toHaveBeenCalledWith(
			"municipio:headerLogoScrollAspectRatio:update",
			(300 / 81).toFixed(6).replace(/0+$/, "").replace(/\.$/, ""),
		);
	});
});
