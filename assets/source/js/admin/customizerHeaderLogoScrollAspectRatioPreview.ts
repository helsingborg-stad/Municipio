type HeaderLogoScrollShrinkElements = {
	lowerHeader: HTMLElement;
	logotypeItem: HTMLElement;
};

type AspectRatioMeasurement = {
	aspectRatio: number | null;
	reason:
		| "brand-container"
		| "brand-container-missing"
		| "brand-container-unmeasurable"
		| "image-natural-size"
		| "figure-missing"
		| "figure-unmeasurable"
		| "figure-size";
	width?: number;
	height?: number;
};

type CustomizeSetting = {
	get: () => unknown;
	set: (value: string) => void;
	bind?: (callback: () => void) => void;
};

type CustomizeApiFunction = (
	id: string,
	callback: (setting: CustomizeSetting) => void,
) => void;

type CustomizeApiObject = {
	preview?: {
		bind: (eventName: string, callback: () => void) => void;
		send: (eventName: string, value: string) => void;
	};
};

type CustomizeApi = CustomizeApiFunction & CustomizeApiObject;

declare global {
	interface Window {
		wp?: {
			customize?: unknown;
		};
		__municipioHeaderLogoScrollAspectRatioDebug?: Record<string, unknown>;
	}

	interface Document {
		fonts?: {
			ready: Promise<unknown>;
		};
	}
}

const aspectRatioSettingId = "header_logo_scroll_aspect_ratio";
const aspectRatioUpdateEvent = "municipio:headerLogoScrollAspectRatio:update";

function setDebugState(key: string, value: Record<string, unknown>): void {
	window.__municipioHeaderLogoScrollAspectRatioDebug = {
		...(window.__municipioHeaderLogoScrollAspectRatioDebug ?? {}),
		[key]: value,
	};

	console.debug("[Municipio] header logo scroll aspect ratio", key, value);
}

function getCustomizeApiFunction(): CustomizeApiFunction | null {
	const customizeApi = window.wp?.customize;

	return typeof customizeApi === "function"
		? (customizeApi as CustomizeApiFunction)
		: null;
}

function getCustomizeApiObject(): CustomizeApiObject | null {
	const customizeApi = window.wp?.customize;

	return customizeApi && typeof customizeApi === "object"
		? (customizeApi as CustomizeApiObject)
		: null;
}

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
			return {
				aspectRatio: null,
				reason: "brand-container-missing",
			};
		}

		const { width, height } = brandContainer.getBoundingClientRect();

		if (width && height) {
			return {
				aspectRatio: width / height,
				reason: "brand-container",
				width,
				height,
			};
		}

		return {
			aspectRatio: null,
			reason: "brand-container-unmeasurable",
			width,
			height,
		};
	}

	const logotypeImage = logotypeItem.querySelector<HTMLImageElement>(
		".c-header__logotype img",
	);

	if (logotypeImage?.naturalWidth && logotypeImage.naturalHeight) {
		return {
			aspectRatio: logotypeImage.naturalWidth / logotypeImage.naturalHeight,
			reason: "image-natural-size",
			width: logotypeImage.naturalWidth,
			height: logotypeImage.naturalHeight,
		};
	}

	const logotypeFigure = logotypeItem.querySelector<HTMLElement>(
		".c-header__logotype",
	);

	if (!logotypeFigure) {
		return {
			aspectRatio: null,
			reason: "figure-missing",
		};
	}

	const { width, height } = logotypeFigure.getBoundingClientRect();

	if (!width || !height) {
		return {
			aspectRatio: null,
			reason: "figure-unmeasurable",
			width,
			height,
		};
	}

	return {
		aspectRatio: width / height,
		reason: "figure-size",
		width,
		height,
	};
}

export function getLogoAspectRatio(logotypeItem: HTMLElement): number | null {
	return measureLogoAspectRatio(logotypeItem).aspectRatio;
}

function formatAspectRatio(aspectRatio: number): string {
	return aspectRatio.toFixed(6).replace(/0+$/, "").replace(/\.$/, "");
}

function sendAspectRatioUpdate(value: string): void {
	const customizeApiObject = getCustomizeApiObject();

	setDebugState("preview-send", {
		event: aspectRatioUpdateEvent,
		value,
		hasPreviewApi: typeof customizeApiObject?.preview?.send === "function",
	});

	customizeApiObject?.preview?.send(aspectRatioUpdateEvent, value);
}

function applyAspectRatioSettingDirectly(value: string): void {
	const customizeApiFunction = getCustomizeApiFunction();

	if (!customizeApiFunction) {
		setDebugState("preview-direct-apply", {
			status: "missing-customize-function",
			settingId: aspectRatioSettingId,
			value,
		});
		return;
	}

	customizeApiFunction(aspectRatioSettingId, (setting: CustomizeSetting) => {
		const previousValue = setting.get();

		setDebugState("preview-direct-apply", {
			status: previousValue === value ? "unchanged" : "updated",
			settingId: aspectRatioSettingId,
			previousValue,
			value,
		});

		if (previousValue === value) {
			return;
		}

		setting.set(value);
	});
}

export function syncHeaderLogoScrollAspectRatio(): boolean {
	const elements = getHeaderLogoScrollShrinkElements();

	if (!elements) {
		setDebugState("preview-measurement", {
			settingId: aspectRatioSettingId,
			status: "missing-elements",
		});
		applyAspectRatioSettingDirectly("");
		sendAspectRatioUpdate("");
		return false;
	}

	const measurement = measureLogoAspectRatio(elements.logotypeItem);

	setDebugState("preview-measurement", {
		settingId: aspectRatioSettingId,
		status: measurement.aspectRatio ? "measured" : "failed",
		reason: measurement.reason,
		aspectRatio: measurement.aspectRatio,
		width: measurement.width,
		height: measurement.height,
	});

	if (!measurement.aspectRatio) {
		applyAspectRatioSettingDirectly("");
		sendAspectRatioUpdate("");
		return false;
	}

	const formattedAspectRatio = formatAspectRatio(measurement.aspectRatio);

	applyAspectRatioSettingDirectly(formattedAspectRatio);
	sendAspectRatioUpdate(formattedAspectRatio);

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
