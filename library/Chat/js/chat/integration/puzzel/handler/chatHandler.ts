class ChatHandler implements ChatHandlerInterface {
	private sessionEnded = false;

	public constructor(private chat: any) {}

	public setDefaultPersistedState(state: boolean): void {
		this.chat.setDefaultShouldPersist(state);
	}

	public addMessage(message: string, isAgent: boolean): void {
		this.chat.addMessage(message, isAgent);
	}

	public addInfo(content: string): void {
		this.chat.addInfo(content);
	}

	public displayBanner(show: boolean): void {
		show ? this.chat.showBanner() : this.chat.hideBanner();
	}

	public setBannerContent(content: string|Node): void {
		this.chat.setBannerContent(content);
	}

	public disable(): void {
		this.chat.disable();
	}

	/** Permanently disables the current conversation until a new one starts. */
	public endSession(): void {
		this.sessionEnded = true;
		this.disable();
	}

	/** Clears the terminal lock for a newly created conversation. */
	public startNewSession(): void {
		this.sessionEnded = false;
	}

	public enable(): void {
		if (!this.sessionEnded) {
			this.chat.enable();
		}
	}

	public addPendingMessage(): any {
		return this.chat.addPendingMessage();
	}

	public deleteMessage(message: any): void {
		this.chat.deleteMessage(message);
	}

	public onMessageAdded(listener: (message: any) => void): void {
		this.chat.getElement().addEventListener("chat:message-added", (event: any) => {
			listener(event.detail);
		});
	}

	public onTyping(listener: () => void): void {
		this.chat.getElement().addEventListener("chat:typing", listener);
	}
}

export default ChatHandler;