class ChatUiManager implements ChatUiManagerInterface {
	public constructor(
		private readonly actionFactory: ChatActionFactoryInterface,
		private readonly actionsContainer: HTMLElement,
		private readonly chatTitleElement: HTMLElement | null,
		private readonly chatAvatarElement: HTMLElement | null,
	) {}

	public clearActions(): void {
		this.actionsContainer.innerHTML = "";
	}

	public addMinimizeAction(): ChatAction {
		return this.actionFactory.addMinimize();
	}

	public addClearAction(): ChatAction {
		return this.actionFactory.addClear();
	}

	public addCloseAction(): ChatAction {
		return this.actionFactory.addClose();
	}

    public setTitle(title: string): void {
		if (this.chatTitleElement) {
			this.chatTitleElement.textContent = title;
		}
    }

    public setAvatar(avatar: string): void {

    }
}

export default ChatUiManager;
