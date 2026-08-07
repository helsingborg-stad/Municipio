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

function applyLogoAspectRatioVariable(logotypeItem: HTMLElement): void {
	const aspectRatio = getLogoAspectRatio(logotypeItem);

	if (!aspectRatio) {
		return;
	}

	logotypeItem.style.setProperty(
		logoAspectRatioCssVariable,
		aspectRatio.toString(),
	);
}

function setupLogoAspectRatioVariable(logotypeItem: HTMLElement): void {
	applyLogoAspectRatioVariable(logotypeItem);

	const logotypeImage = logotypeItem.querySelector<HTMLImageElement>(
		".c-header__logotype img",
	);

	if (!logotypeImage || logotypeImage.complete) {
		return;
	}

	logotypeImage.addEventListener(
		"load",
		() => applyLogoAspectRatioVariable(logotypeItem),
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
