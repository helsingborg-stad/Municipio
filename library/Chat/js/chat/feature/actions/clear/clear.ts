class Clear implements ChatAction {
	private newSessionButtonElement: HTMLElement | null = null;
	private buttonTargetString = "[data-js-chat-new-session]";
	private listeners: ((event: Event) => void)[] = [];

	constructor(
		private actionsContainer: HTMLElement,
		private actionTemplates: HTMLTemplateElement,
		private chat: any
	) {
		this.newSessionButtonElement = this.createButton();
	}

	public add(): this {
		if (!this.newSessionButtonElement) {
			this.newSessionButtonElement = this.createButton();
			this.actionsContainer.appendChild(this.newSessionButtonElement);
			this.setListeners();
		}

		return this;
	}

	public remove(): void {
		if (!this.newSessionButtonElement) {
			return;
		}

		this.newSessionButtonElement.removeEventListener("click", this.onClick);
		this.newSessionButtonElement.remove();
		this.newSessionButtonElement = null;
	}

	private setListeners(): void {
		if (!this.newSessionButtonElement) {
			return;
		}

		this.newSessionButtonElement.removeEventListener("click", this.onClick);
		this.newSessionButtonElement.addEventListener("click", this.onClick);
	}

	private onClick = (): void => {
		if (!this.newSessionButtonElement) {
			return;
		}

		this.listeners.forEach(listener => listener(new Event("click")));
		this.chat.clearMessages();
	};

	public addListener(listener: (event: Event) => void): void {
		this.listeners.push(listener);
	}

	private createButton(): HTMLElement {
		const templateButton = this.actionTemplates.content.querySelector(
			this.buttonTargetString
		);

		if (!templateButton) {
			throw new Error("New session button template not found");
		}

		return templateButton.cloneNode(true) as HTMLElement;
	}
}

export default Clear;
