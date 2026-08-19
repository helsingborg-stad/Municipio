type HeaderLogoScrollShrinkElements = {
	lowerHeader: HTMLElement;
	logotypeItem: HTMLElement;
};

type AspectRatioMeasurement = {
	aspectRatio: number | null;
};

type CustomizeSetting = {
	get: () => unknown;
	set: (value: string) => void;
	bind?: (callback: () => void) => void;
};

type CustomizeApi = {
	preview?: {
		bind: (eventName: string, callback: () => void) => void;
		send: (eventName: string, value: string) => void;
	};
};

declare global {
	interface Window {
		wp?: {
			customize?: CustomizeApi;
		};
	}

	interface Document {
		fonts?: {
			ready: Promise<unknown>;
		};
	}
}

const aspectRatioSettingId = "header_logo_scroll_aspect_ratio";
const aspectRatioUpdateEvent = "municipio:headerLogoScrollAspectRatio:update";

function getHeaderLogoScrollShrinkElements(): HeaderLogoScrollShrinkElements | null {
	const lowerHeader = document.querySelector<HTMLElement>(
		"#site-header-flexible-lower.c-header--logotype-scroll-shrink",
	);

	if (!lowerHeader) {
		return null;
	}

	const logotypeItem = lowerHeader.querySelector<HTMLElement>(
		".c-header__lower-left .c-header__item--logotype",
	);

	if (!logotypeItem) {
		return null;
	}

	return {
		lowerHeader,
		logotypeItem,
	};
}

function measureLogoAspectRatio(
	logotypeItem: HTMLElement,
): AspectRatioMeasurement {
	const brandElement = logotypeItem.querySelector<HTMLElement>(
		".c-header__logotype.c-brand",
	);

	if (brandElement) {
		const brandContainer = logotypeItem.querySelector<HTMLElement>(
			".c-brand__container",
		);

		if (!brandContainer) {
			return { aspectRatio: null };
		}

		const { width, height } = brandContainer.getBoundingClientRect();

		if (width && height) {
			return { aspectRatio: width / height };
		}

		return { aspectRatio: null };
	}

	const logotypeImage = logotypeItem.querySelector<HTMLImageElement>(
		".c-header__logotype img",
	);

	if (logotypeImage?.naturalWidth && logotypeImage.naturalHeight) {
		return {
			aspectRatio: logotypeImage.naturalWidth / logotypeImage.naturalHeight,
		};
	}

	const logotypeFigure = logotypeItem.querySelector<HTMLElement>(
		".c-header__logotype",
	);

	if (!logotypeFigure) {
		return { aspectRatio: null };
	}

	const { width, height } = logotypeFigure.getBoundingClientRect();

	if (!width || !height) {
		return { aspectRatio: null };
	}

	return { aspectRatio: width / height };
}

export function getLogoAspectRatio(logotypeItem: HTMLElement): number | null {
	return measureLogoAspectRatio(logotypeItem).aspectRatio;
}

function formatAspectRatio(aspectRatio: number): string {
	return aspectRatio.toFixed(6).replace(/0+$/, "").replace(/\.$/, "");
}

function sendAspectRatioUpdate(value: string): void {
	window.wp?.customize?.preview?.send(aspectRatioUpdateEvent, value);
}

export function syncHeaderLogoScrollAspectRatio(): boolean {
	const elements = getHeaderLogoScrollShrinkElements();

	if (!elements) {
		sendAspectRatioUpdate("");
		return false;
	}

	const { aspectRatio } = measureLogoAspectRatio(elements.logotypeItem);

	if (!aspectRatio) {
		sendAspectRatioUpdate("");
		return false;
	}

	sendAspectRatioUpdate(formatAspectRatio(aspectRatio));

	return true;
}

export function initializeCustomizerHeaderLogoScrollAspectRatioPreview(): void {
	const initializeWhenReady = (): void => {
		setupCustomizerHeaderLogoScrollAspectRatioPreview();
	};

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initializeWhenReady, {
			once: true,
		});
		return;
	}

	initializeWhenReady();
}

function setupCustomizerHeaderLogoScrollAspectRatioPreview(): void {
	let isTicking = false;
	let observedLogotypeItem: HTMLElement | null = null;
	const resizeObserver =
		typeof ResizeObserver === "function"
			? new ResizeObserver(() => requestSync())
			: null;

	const requestSync = (): void => {
		if (isTicking) {
			return;
		}

		isTicking = true;
		window.requestAnimationFrame(() => {
			isTicking = false;
			rebindResizeObserver();
			syncHeaderLogoScrollAspectRatio();
		});
	};

	const bindImageLoad = (logotypeItem: HTMLElement): void => {
		const logotypeImage = logotypeItem.querySelector<HTMLImageElement>(
			".c-header__logotype img",
		);

		if (!logotypeImage || logotypeImage.complete) {
			return;
		}

		logotypeImage.addEventListener("load", requestSync, { once: true });
	};

	const rebindResizeObserver = (): void => {
		if (!resizeObserver) {
			return;
		}

		const elements = getHeaderLogoScrollShrinkElements();
		const nextLogotypeItem = elements?.logotypeItem ?? null;

		if (observedLogotypeItem === nextLogotypeItem) {
			return;
		}

		if (observedLogotypeItem) {
			resizeObserver.unobserve(observedLogotypeItem);
		}

		observedLogotypeItem = nextLogotypeItem;

		if (!observedLogotypeItem) {
			return;
		}

		resizeObserver.observe(observedLogotypeItem);
		bindImageLoad(observedLogotypeItem);
	};

	const mutationObserver = new MutationObserver(() => {
		rebindResizeObserver();
		requestSync();
	});

	mutationObserver.observe(document.body, {
		attributes: true,
		attributeFilter: ["class", "style", "src"],
		childList: true,
		subtree: true,
	});

	rebindResizeObserver();
	requestSync();

	window.addEventListener("resize", requestSync, { passive: true });
	window.addEventListener("load", requestSync, { once: true });

	if (document.fonts?.ready) {
		document.fonts.ready.then(() => requestSync());
	}

	window.wp?.customize?.preview?.bind("active", requestSync);
}

initializeCustomizerHeaderLogoScrollAspectRatioPreview();
