class Close implements ChatAction {
	private closeButtonElement: HTMLElement | null = null;
	private buttonTargetString = "[data-js-chat-close]";
    private closeConfirmationTargetString = "[data-js-chat-close-confirmation]";
    private static closeConfirmationElement: HTMLElement | null = null;
	private listeners: ((event: Event) => void)[] = [];

	constructor(
		private actionsContainer: HTMLElement,
		private actionTemplates: HTMLTemplateElement,
		private chat: any
	) {
        Close.closeConfirmationElement = this.actionTemplates.content.querySelector(this.closeConfirmationTargetString);
	}

	public add(): this {
		if (!this.closeButtonElement) {
			this.closeButtonElement = this.createButton();
			this.actionsContainer.appendChild(this.closeButtonElement);
		}

		this.setListeners();

		return this;
	}

	public remove(): void {
		if (!this.closeButtonElement) {
			return;
		}

		this.closeButtonElement.removeEventListener("click", this.onClick);
		this.closeButtonElement.remove();
		this.closeButtonElement = null;
	}

	private setListeners(): void {
		if (!this.closeButtonElement) {
			return;
		}

		this.closeButtonElement.removeEventListener("click", this.onClick);
		this.closeButtonElement.addEventListener("click", this.onClick);
	}

	private onClick = (): void => {
		if (!this.closeButtonElement) {
			return;
		}

		this.listeners.forEach(listener => listener(new Event("click")));

		const popoverTargetId =
			this.closeButtonElement.getAttribute("popovertarget") ??
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
			this.buttonTargetString,
		);

		if (!templateButton) {
			throw new Error("Close button template not found");
		}

		return templateButton.cloneNode(true) as HTMLElement;
	}
}

export default Close;
