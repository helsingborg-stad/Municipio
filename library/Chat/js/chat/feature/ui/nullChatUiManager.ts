/**
 * UI manager for chats rendered without a header (e.g. the chat block).
 *
 * There is no title, avatar or actions area to update, so every call is a no-op.
 * Integrations can use it exactly like ChatUiManager without checking for a header.
 */
class NullChatAction implements ChatAction {
	public add(): void {}
	public remove(): void {}
	public addListener(_listener: (event: Event) => void): void {}
}

class NullChatUiManager implements ChatUiManagerInterface {
	public clearActions(): void {}

	public addMinimizeAction(): ChatAction {
		return new NullChatAction();
	}

	public addClearAction(): ChatAction {
		return new NullChatAction();
	}

	public addCloseAction(): ChatAction {
		return new NullChatAction();
	}

	public setTitle(_title: string): void {}

	public setAvatar(_avatarUrl: string | null): void {}
}

export default NullChatUiManager;
