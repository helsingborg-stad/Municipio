const EDITOR_BODY_CLASS = "user-can-upload_files";
const MAIN_CONTENT_SELECTOR = "main#main-content";
const WARNING_ID = "a11y-missing-h1-warning";

const message =
	typeof MunicipioLocale !== "undefined"
		? (MunicipioLocale.a11yWarnings?.missingH1 ?? "Page is missing an H1 heading")
		: "Page is missing an H1 heading";

/**
 * Shows a page-level warning when no H1 heading exists. The warning is
 * is inserted in the main content area, where the H1 belongs. Its fixed
 * position keeps it visible even when that area is inside a clipped container.
 */
export class MissingH1Indicator {
	private observer: MutationObserver | null = null;

	public start(): void {
		this.scan();

		this.observer = new MutationObserver(() => this.scan());
		this.observer.observe(document.body, {
			childList: true,
			subtree: true,
		});
	}

	public stop(): void {
		this.observer?.disconnect();
		this.observer = null;
		this.removeWarning();
	}

	public scan(): void {
		if (document.querySelector("h1")) {
			this.removeWarning();
			return;
		}

		this.addWarning();
	}

	private addWarning(): void {
		if (document.getElementById(WARNING_ID)) {
			return;
		}

		const warning = document.createElement("div");
		warning.id = WARNING_ID;
		warning.className = "a11y-floating-warning";
		warning.setAttribute("role", "status");
		warning.dataset.tooltip = message;

		const icon = document.createElement("span");
		icon.setAttribute("aria-hidden", "true");
		icon.textContent = "[ ! ]";

		const text = document.createElement("span");
		text.textContent = message;

		warning.append(icon, text);
		const mainContent = document.querySelector(MAIN_CONTENT_SELECTOR);
		(mainContent ?? document.body).prepend(warning);
	}

	private removeWarning(): void {
		document.getElementById(WARNING_ID)?.remove();
	}
}

export function initializeMissingH1Indicator(): void {
	if (!document.body.classList.contains(EDITOR_BODY_CLASS)) {
		return;
	}

	new MissingH1Indicator().start();
}
