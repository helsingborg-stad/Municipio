const EDITOR_BODY_CLASS = "user-can-upload_files";
const CONTROL_SELECTOR = "a[href], button";
const DYNAMIC_ERROR_ATTRIBUTE = "data-a11y-dynamic-error";

const vagueLabels = new Set([
	"click here",
	"här",
	"klicka här",
	"läs mer",
	"läs vidare",
	"more",
	"mer",
	"read more",
]);

const messages = {
	button:
		typeof MunicipioLocale !== "undefined"
			? MunicipioLocale.a11yWarnings?.button
			: undefined,
	link:
		typeof MunicipioLocale !== "undefined"
			? MunicipioLocale.a11yWarnings?.link
			: undefined,
};

const fallbackMessages = {
	button: "Button text is not descriptive enough",
	link: "Link text is not descriptive enough",
};

/**
 * Finds vague labels in rendered controls, including controls inserted after
 * the initial page load.
 */
export class VagueControlTextIndicator {
	private observer: MutationObserver | null = null;

	public start(): void {
		this.scan(document.body);

		this.observer = new MutationObserver((mutations) => {
			mutations.forEach((mutation) => this.handleMutation(mutation));
		});
		this.observer.observe(document.body, {
			attributes: true,
			attributeFilter: ["href"],
			characterData: true,
			childList: true,
			subtree: true,
		});
	}

	public stop(): void {
		this.observer?.disconnect();
		this.observer = null;
	}

	public scan(root: ParentNode): void {
		if (root instanceof HTMLElement && root.matches(CONTROL_SELECTOR)) {
			this.scanControl(root);
		}

		root.querySelectorAll<HTMLElement>(CONTROL_SELECTOR).forEach((control) =>
			this.scanControl(control),
		);
	}

	private handleMutation(mutation: MutationRecord): void {
		if (mutation.type === "attributes" && mutation.target instanceof HTMLElement) {
			this.scanControl(mutation.target);
			return;
		}

		mutation.addedNodes.forEach((node) => {
			if (node instanceof HTMLElement) {
				this.scan(node);
			}
		});

		const control = this.getClosestControl(mutation.target);
		if (control) {
			this.scanControl(control);
		}
	}

	private scanControl(control: HTMLElement): void {
		if (!control.matches(CONTROL_SELECTOR)) {
			this.removeDynamicWarning(control);
			return;
		}

		const label = this.normalizeLabel(control.textContent ?? "");
		const isVague = vagueLabels.has(label);
		const isLink = control.matches("a[href]");

		if (isVague) {
			if (!control.hasAttribute("data-a11y-error")) {
				const message = isLink
					? (messages.link ?? fallbackMessages.link)
					: (messages.button ?? fallbackMessages.button);
				control.setAttribute("data-a11y-error", message);
				control.setAttribute(DYNAMIC_ERROR_ATTRIBUTE, "");
			}
			return;
		}

		if (control.hasAttribute(DYNAMIC_ERROR_ATTRIBUTE)) {
			this.removeDynamicWarning(control);
		}
	}

	private removeDynamicWarning(control: HTMLElement): void {
		if (control.hasAttribute(DYNAMIC_ERROR_ATTRIBUTE)) {
			control.removeAttribute("data-a11y-error");
			control.removeAttribute(DYNAMIC_ERROR_ATTRIBUTE);
		}
	}

	private getClosestControl(node: Node): HTMLElement | null {
		const element = node instanceof HTMLElement ? node : node.parentElement;
		return element?.closest<HTMLElement>(CONTROL_SELECTOR) ?? null;
	}

	private normalizeLabel(label: string): string {
		return label.replace(/\s+/gu, " ").trim().toLocaleLowerCase();
	}
}

export function initializeVagueControlTextIndicator(): void {
	if (!document.body.classList.contains(EDITOR_BODY_CLASS)) {
		return;
	}

	new VagueControlTextIndicator().start();
}
