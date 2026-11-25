import { getLocalizedString } from "../../../helper/translations";
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

	public addInfoFromType(type: string, senderRole: string = 'agent'): void {
		switch (type) {
			case "participantJoinEvent":
				if (senderRole === 'agent') {
					this.addAgentJoinedInfo();
				}
				break;
			case "participantLeaveEvent":
				senderRole === 'agent' ? this.addAgentLeftInfo() : this.addUserLeftInfo();
				break;
		}
	}

	public addAgentJoinedInfo(): void {
		this.chat.addInfo(getLocalizedString("agentJoinedChat", "Agent has joined the chat"));
	}

	public addUserLeftInfo(): void {
		this.chat.addInfo(getLocalizedString("userLeftChat", "User has left the chat"));
	}

	public addAgentLeftInfo(): void {
		this.chat.addInfo(getLocalizedString("agentLeftChat", "Agent has left the chat"));
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