type HeaderLogoScrollShrinkElements = {
	logotypeItem: HTMLElement;
};

const desktopHeaderMediaQuery = "(min-width: 1247px)";
const logoAspectRatioCssVariable =
	"--municipio-header-logo-scroll-aspect-ratio";

function getCurrentScrollPosition(): number {
	return (
		window.scrollY ||
		document.documentElement.scrollTop ||
		document.body.scrollTop ||
		0
	);
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
		logotypeItem,
	};
}

function toggleScrollShrinkState(
	elements: HeaderLogoScrollShrinkElements,
	isScrolled: boolean,
): void {
	elements.logotypeItem.classList.toggle("is-logotype-scrolled", isScrolled);
}

function getLogoAspectRatio(logotypeItem: HTMLElement): number | null {
	const brandElement = logotypeItem.querySelector<HTMLElement>(
		".c-header__logotype.c-brand",
	);

	if (brandElement) {
		const brandContainer = logotypeItem.querySelector<HTMLElement>(
			".c-brand__container",
		);

		if (!brandContainer) {
			return null;
		}

		const { width, height } = brandContainer.getBoundingClientRect();

		if (width && height) {
			return width / height;
		}

		// Brand markup exists but layout has not produced measurable dimensions yet.
		// Avoid falling back to the symbol image ratio, which differs from full brand width.
		return null;
	}

	const logotypeImage = logotypeItem.querySelector<HTMLImageElement>(
		".c-header__logotype img",
	);

	if (logotypeImage?.naturalWidth && logotypeImage.naturalHeight) {
		return logotypeImage.naturalWidth / logotypeImage.naturalHeight;
	}

	const logotypeFigure = logotypeItem.querySelector<HTMLElement>(
		".c-header__logotype",
	);

	if (!logotypeFigure) {
		return null;
	}

	const { width, height } = logotypeFigure.getBoundingClientRect();

	if (!width || !height) {
		return null;
	}

	return width / height;
}

function applyLogoAspectRatioVariable(logotypeItem: HTMLElement): boolean {
	const aspectRatio = getLogoAspectRatio(logotypeItem);

	if (!aspectRatio) {
		return false;
	}

	logotypeItem.style.setProperty(
		logoAspectRatioCssVariable,
		aspectRatio.toString(),
	);

	return true;
}

function setupLogoAspectRatioVariable(logotypeItem: HTMLElement): void {
	const applyWithRetry = (attemptsLeft = 6): void => {
		if (applyLogoAspectRatioVariable(logotypeItem) || attemptsLeft <= 0) {
			return;
		}

		window.requestAnimationFrame(() => applyWithRetry(attemptsLeft - 1));
	};

	applyWithRetry();

	window.addEventListener("load", () => applyWithRetry(), { once: true });

	if ("fonts" in document) {
		document.fonts.ready.then(() => applyWithRetry());
	}

	const logotypeImage = logotypeItem.querySelector<HTMLImageElement>(
		".c-header__logotype img",
	);

	if (!logotypeImage || logotypeImage.complete) {
		return;
	}

	logotypeImage.addEventListener(
		"load",
		() => applyWithRetry(),
		{ once: true },
	);
}

export function initializeHeaderLogoScrollShrink(): void {
	const initializeWhenReady = (): void => {
		setupHeaderLogoScrollShrink();
	};

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initializeWhenReady, {
			once: true,
		});
		return;
	}

	initializeWhenReady();
}

function setupHeaderLogoScrollShrink(): void {
	const elements = getHeaderLogoScrollShrinkElements();

	if (!elements) {
		return;
	}

	setupLogoAspectRatioVariable(elements.logotypeItem);

	const mediaQuery = window.matchMedia(desktopHeaderMediaQuery);
	let isTicking = false;

	const updateState = (): void => {
		isTicking = false;
		toggleScrollShrinkState(
			elements,
			mediaQuery.matches && getCurrentScrollPosition() > 0,
		);
	};

	const requestUpdate = (): void => {
		if (isTicking) {
			return;
		}

		isTicking = true;
		window.requestAnimationFrame(updateState);
	};

	requestUpdate();
	window.addEventListener("scroll", requestUpdate, { passive: true });

	if (typeof mediaQuery.addEventListener === "function") {
		mediaQuery.addEventListener("change", requestUpdate);
		return;
	}

	mediaQuery.addListener(requestUpdate);
}
