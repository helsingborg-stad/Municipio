class ChatHandler {
	public constructor(private chat: any) {}

	private getLocalizedString(key: keyof MunicipioChatLocale, fallback: string): string {
		const locale = (globalThis as { municipioChatLocale?: Partial<MunicipioChatLocale> }).municipioChatLocale;
		const translated = locale?.[key];

		return typeof translated === "string" && translated.length > 0 ? translated : fallback;
	}

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

	public setQueueBannerData(position: number): void {
		const queuePositionLabel = this.getLocalizedString("queuePosition", "Queue position");
		this.chat.setBannerContent(`${queuePositionLabel}: ${position}`);
	}

	public setChatInactiveData(): void {
		this.chat.setBannerContent(
			this.getLocalizedString(
				"agentLeftChatInactive",
				"The agent has left and the chat is now inactive",
			),
		);
	}

	public addInfoFromType(type: string, senderRole: string = 'agent'): void {
		switch (type) {
			case "participantJoinEvent":
				if (senderRole === 'agent') {
					this.addAgentJoinedInfo();
				}
				break;
			case "participantLeaveEvent":
				if (senderRole === 'agent') {
					this.addAgentLeftInfo();
				}
				break;
		}
	}

	public addAgentJoinedInfo(): void {
		this.chat.addInfo(this.getLocalizedString("agentJoinedChat", "Agent has joined the chat"));
	}

	public addAgentLeftInfo(): void {
		this.chat.addInfo(this.getLocalizedString("agentLeftChat", "Agent has left the chat"));
	}

	public disable(): void {
		this.chat.disable();
	}

	public enable(): void {
		this.chat.enable();
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