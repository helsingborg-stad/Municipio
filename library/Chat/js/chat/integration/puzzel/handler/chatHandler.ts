class ChatHandler {
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
		// TODO: Add translation support for this message
		this.chat.addInfo("Agent has joined the chat.");
	}

	public addAgentLeftInfo(): void {
		// TODO: Add translation support for this message
		this.chat.addInfo("Agent has left the chat.");
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