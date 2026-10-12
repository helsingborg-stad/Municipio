const EDITOR_BODY_CLASS = "user-can-upload_files";
const MAIN_CONTENT_SELECTOR = "main#main-content";
const HEADING_SELECTOR = "h1, h2, h3, h4, h5, h6";
const ERROR_ATTRIBUTE = "data-a11y-heading-hierarchy-error";
const TOOLTIP_ATTRIBUTE = "data-a11y-heading-hierarchy-tooltip";

const message =
	typeof MunicipioLocale !== "undefined"
		? (MunicipioLocale.a11yWarnings?.headingHierarchy ?? "Invalid heading level (level is skipped)")
		: "Invalid heading level (level is skipped)";

/**
 * Marks headings in the main content area that skip a level, for example h2
 * followed by h4. Decreasing heading levels is valid and is not marked.
 */
export class HeadingHierarchyIndicator {
	private observer: MutationObserver | null = null;

	public start(): void {
		this.scan();

		this.observer = new MutationObserver(() => this.scan());
		this.observer.observe(document.body, {
			characterData: true,
			childList: true,
			subtree: true,
		});
	}

	public stop(): void {
		this.observer?.disconnect();
		this.observer = null;
	}

	public scan(): void {
		document.querySelectorAll<HTMLElement>(MAIN_CONTENT_SELECTOR).forEach((main) => {
			let previousLevel: number | null = null;

			main.querySelectorAll<HTMLHeadingElement>(HEADING_SELECTOR).forEach((heading) => {
				const currentLevel = Number.parseInt(heading.tagName.slice(1), 10);
				const isSkippedLevel = previousLevel !== null && currentLevel > previousLevel + 1;

				if (isSkippedLevel) {
					this.addWarning(heading);
				} else {
					this.removeWarning(heading);
				}

				previousLevel = currentLevel;
			});
		});
	}

	private addWarning(heading: HTMLHeadingElement): void {
		heading.setAttribute("data-a11y-error", message);
		heading.setAttribute(ERROR_ATTRIBUTE, "");

		if (!heading.dataset.tooltip) {
			heading.dataset.tooltip = message;
			heading.setAttribute(TOOLTIP_ATTRIBUTE, "");
		}
	}

	private removeWarning(heading: HTMLHeadingElement): void {
		if (!heading.hasAttribute(ERROR_ATTRIBUTE)) {
			return;
		}

		heading.removeAttribute("data-a11y-error");
		heading.removeAttribute(ERROR_ATTRIBUTE);

		if (heading.hasAttribute(TOOLTIP_ATTRIBUTE)) {
			heading.removeAttribute("data-tooltip");
			heading.removeAttribute(TOOLTIP_ATTRIBUTE);
		}
	}
}

export function initializeHeadingHierarchyIndicator(): void {
	if (!document.body.classList.contains(EDITOR_BODY_CLASS)) {
		return;
	}

	new HeadingHierarchyIndicator().start();
}
