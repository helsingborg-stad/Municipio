const EDITOR_BODY_CLASS = "user-can-upload_files";
const MARKER_ROOT_CLASS = "a11y-warning-markers";
const MARKER_CLASS = "a11y-warning-marker";
const IMAGE_MARKER_CLASS = "a11y-warning-marker--image";
const MARKER_TARGET_SELECTOR = [
	"[data-a11y-error][data-tooltip-initialized]:not(figure.c-image)",
	".c-image__image-wrapper[data-a11y-marker][data-tooltip-initialized]",
].join(", ");

interface WarningMarker {
	marker: HTMLDivElement;
}

/**
 * Renders visual a11y warning markers outside their targets so warnings never
 * claim a target's pseudo-elements or alter its child-element selectors.
 */
export class A11yWarningMarkers {
	private observer: MutationObserver | null = null;
	private root: HTMLDivElement | null = null;
	private markers = new Map<HTMLElement, WarningMarker>();
	private nextAnchorId = 0;

	public start(): void {
		this.root = document.createElement("div");
		this.root.className = MARKER_ROOT_CLASS;
		this.root.setAttribute("aria-hidden", "true");
		document.body.append(this.root);

		this.sync();
		this.observer = new MutationObserver(() => this.sync());
		this.observer.observe(document.body, {
			attributes: true,
			attributeFilter: [
				"data-a11y-error",
				"data-a11y-marker",
				"data-tooltip-initialized",
			],
			childList: true,
			subtree: true,
		});
	}

	public stop(): void {
		this.observer?.disconnect();
		this.observer = null;
		this.markers.forEach((warningMarker, target) =>
			this.removeMarker(target, warningMarker),
		);
		this.markers.clear();
		this.root?.remove();
		this.root = null;
	}

	private sync(): void {
		const targets = new Set(
			Array.from(document.querySelectorAll<HTMLElement>(MARKER_TARGET_SELECTOR)),
		);

		this.markers.forEach((warningMarker, target) => {
			if (!targets.has(target) || !target.isConnected) {
				this.removeMarker(target, warningMarker);
				this.markers.delete(target);
			}
		});

		targets.forEach((target) => {
			if (!this.markers.has(target)) {
				this.addMarker(target);
			}
		});
	}

	private addMarker(target: HTMLElement): void {
		if (!this.root) {
			return;
		}

		const anchorName = `--a11y-warning-anchor-${this.nextAnchorId++}`;
		const marker = document.createElement("div");
		marker.className = MARKER_CLASS;
		marker.textContent = "[!]";
		marker.style.setProperty("--a11y-warning-anchor", anchorName);

		if (target.matches(".c-image__image-wrapper")) {
			marker.classList.add(IMAGE_MARKER_CLASS);
		}

		target.dataset.a11yWarningAnchor = "";
		target.style.setProperty("--a11y-warning-anchor", anchorName);
		this.root.append(marker);
		this.markers.set(target, { marker });
	}

	private removeMarker(target: HTMLElement, warningMarker: WarningMarker): void {
		warningMarker.marker.remove();
		delete target.dataset.a11yWarningAnchor;
		target.style.removeProperty("--a11y-warning-anchor");
	}
}

export function initializeA11yWarningMarkers(): void {
	if (!document.body.classList.contains(EDITOR_BODY_CLASS)) {
		return;
	}

	new A11yWarningMarkers().start();
}
