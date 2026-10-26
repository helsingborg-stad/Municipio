class MinimizeChat implements ChatAction {
	private minimizeButtonElement: HTMLElement | null = null;
	private buttonTargetString = '[data-js-chat-minimize]';
	private listeners: ((event: Event) => void)[] = [];

	constructor(
		private actionsContainer: HTMLElement,
		private actionTemplates: HTMLTemplateElement,
	) {
	}

	public add(): this {
		if (!this.minimizeButtonElement) {
			this.minimizeButtonElement = this.createButton();
			this.actionsContainer.appendChild(this.minimizeButtonElement);
			this.setListeners();
		}

		return this;
	}

	public remove(): void {
		if (!this.minimizeButtonElement) {
			return;
		}

		this.minimizeButtonElement.removeEventListener("click", this.onClick);
		this.minimizeButtonElement.remove();
		this.minimizeButtonElement = null;
	}

	private setListeners(): void {
		if (!this.minimizeButtonElement) {
			return;
		}

		this.minimizeButtonElement.removeEventListener("click", this.onClick);
		this.minimizeButtonElement.addEventListener("click", this.onClick);
	}

	private onClick = (): void => {
		if (!this.minimizeButtonElement) {
			return;
		}

		this.listeners.forEach(listener => listener(new Event("click")));

		const popoverTargetId =
			this.minimizeButtonElement.getAttribute("popovertarget") ??
			"chat-global-root";
		const popoverTarget = document.getElementById(popoverTargetId);

		if (!popoverTarget) {
			return;
		}

		if (typeof popoverTarget.hidePopover === "function") {
			popoverTarget.hidePopover();
		}
	};

	public addListener(listener: (event: Event) => void): void {
		this.listeners.push(listener);
	}

	private createButton(): HTMLElement {
		const templateButton = this.actionTemplates.content.querySelector(
			this.buttonTargetString
		);

		if (!templateButton) {
			throw new Error("Minimize button template not found");
		}

		return templateButton.cloneNode(true) as HTMLElement;
	}
}

export default MinimizeChat;