class CloseConfirmationController implements CloseConfirmationControllerInterface {
	private readonly closeConfirmationTargetString = "[data-js-chat-close-confirmation]";
	private readonly closeConfirmButtonTargetString = "[data-js-chat-close-confirm]";
	private readonly closeCancelButtonTargetString = "[data-js-chat-close-cancel]";
	private closeConfirmationElement: HTMLElement | null = null;
	private closeConfirmButtonElement: HTMLElement | null = null;
	private closeCancelButtonElement: HTMLElement | null = null;
	private onConfirmAction: (() => void) | null = null;
	private onCancelAction: (() => void) | null = null;

	constructor(
		private actionTemplates: HTMLTemplateElement,
		private chat: any,
	) {
	}

	public initialize(): void {
		if (this.closeConfirmationElement) {
			return;
		}

		const templateConfirmation = this.actionTemplates.content.querySelector(
			this.closeConfirmationTargetString,
		);

		if (!templateConfirmation) {
			return;
		}

		this.closeConfirmationElement = templateConfirmation.cloneNode(true) as HTMLElement;
		this.closeConfirmationElement.style.display = "none";

		this.closeConfirmButtonElement = this.closeConfirmationElement.querySelector(
			this.closeConfirmButtonTargetString,
		) as HTMLElement | null;
		this.closeCancelButtonElement = this.closeConfirmationElement.querySelector(
			this.closeCancelButtonTargetString,
		) as HTMLElement | null;

		const chatElement = this.chat?.getElement?.();

		if (chatElement instanceof HTMLElement) {
			chatElement.appendChild(this.closeConfirmationElement);
		} else {
			this.closeConfirmationElement = null;
			this.closeConfirmButtonElement = null;
			this.closeCancelButtonElement = null;
		}
	}

	public requestConfirmation(onConfirm: () => void, onCancel?: () => void): void {
		this.initialize();

		if (!this.closeConfirmationElement || !this.closeConfirmButtonElement || !this.closeCancelButtonElement) {
			onConfirm();
			return;
		}

		this.onConfirmAction = onConfirm;
		this.onCancelAction = onCancel ?? null;
		this.closeConfirmationElement.style.display = "";

		this.setListeners();
		document.removeEventListener("keydown", this.onKeyDown);
		document.addEventListener("keydown", this.onKeyDown);
	}

	public remove(): void {
		if (this.closeConfirmButtonElement) {
			this.closeConfirmButtonElement.removeEventListener("click", this.onConfirmClick);
		}

		if (this.closeCancelButtonElement) {
			this.closeCancelButtonElement.removeEventListener("click", this.onCancelClick);
		}

		if (this.closeConfirmationElement) {
			this.closeConfirmationElement.remove();
		}

		document.removeEventListener("keydown", this.onKeyDown);

		this.closeConfirmationElement = null;
		this.closeConfirmButtonElement = null;
		this.closeCancelButtonElement = null;
		this.onConfirmAction = null;
		this.onCancelAction = null;
	}

	private setListeners(): void {
		if (!this.closeConfirmButtonElement || !this.closeCancelButtonElement) {
			return;
		}

		this.closeConfirmButtonElement.removeEventListener("click", this.onConfirmClick);
		this.closeCancelButtonElement.removeEventListener("click", this.onCancelClick);

		this.closeConfirmButtonElement.addEventListener("click", this.onConfirmClick);
		this.closeCancelButtonElement.addEventListener("click", this.onCancelClick);
	}

	private onConfirmClick = (): void => {
		this.hide();
		this.onConfirmAction?.();
	};

	private onCancelClick = (): void => {
		this.hide();
		this.onCancelAction?.();
	};

	private onKeyDown = (event: KeyboardEvent): void => {
		if (event.key !== "Escape") {
			return;
		}

		event.preventDefault();
		this.hide();
		this.onCancelAction?.();
	};

	private hide(): void {
		if (!this.closeConfirmationElement) {
			return;
		}

		this.closeConfirmationElement.style.display = "none";
		document.removeEventListener("keydown", this.onKeyDown);
	}
}

export default CloseConfirmationController;
