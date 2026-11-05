class ChatUiManager implements ChatUiManagerInterface {
	private readonly avatarAttributeSelector = "data-js-chat-avatar";

	public constructor(
		private readonly actionFactory: ChatActionFactoryInterface,
		private readonly actionsContainer: HTMLElement,
		private readonly chatTitleElement: HTMLElement | null,
		private readonly chatTemplate: HTMLTemplateElement,
		private readonly chatHeadingAreaElement: HTMLElement | null,
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

	public setAvatar(avatarUrl: string|null): void {
		this.chatHeadingAreaElement?.querySelector(`[${this.avatarAttributeSelector}]`)?.remove();

		const newAvatarElement = this.getAvatarElement(avatarUrl);

		if (!newAvatarElement) {
			return;
		}

		if (!avatarUrl) {
			this.prependAvatar(newAvatarElement);
			return;
		}

		this.prependAvatar(this.changeAvatarUrl(newAvatarElement, avatarUrl));
    }

	private prependAvatar(avatarElement: HTMLElement): void {
		this.chatHeadingAreaElement?.prepend(avatarElement);
	}

	private getAvatarElement(avatar: string|null): HTMLElement {
		return this.chatTemplate.content.querySelector(`[${this.avatarAttributeSelector}="${avatar ? '1' : '0'}"]`) as HTMLElement;
	}

	private changeAvatarUrl(avatar: HTMLElement, avatarUrl: string): HTMLElement {
		const avatarImg = avatar.querySelector('img');

		if (!avatarImg) {
			return this.getAvatarElement(null);
		}

		avatarImg.src = avatarUrl;
		return avatar;
	}
}

export default ChatUiManager;
