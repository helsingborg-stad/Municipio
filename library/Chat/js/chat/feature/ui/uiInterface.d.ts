interface ChatUiManagerInterface {
	clearActions(): void;
	addMinimizeAction(): ChatAction;
	addClearAction(): ChatAction;
	addCloseAction(): ChatAction;
	setTitle(title: string): void;
	setAvatar(avatarUrl: string|null): void;
}
