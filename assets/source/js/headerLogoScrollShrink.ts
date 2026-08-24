type HeaderLogoScrollShrinkElements = {
	logotypeItem: HTMLElement;
};

const desktopHeaderMediaQuery = "(min-width: 1247px)";

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
