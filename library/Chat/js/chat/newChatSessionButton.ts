import { ChatUiApi } from "./chatUiApi";

class NewChatSessionButton {
	constructor(
		private newChatButtonElement: HTMLElement,
		private chatUiApi: ChatUiApi,
		private onResetConversation: () => void,
	) {
		this.setListeners();
	}

	private setListeners(): void {
		this.newChatButtonElement.addEventListener("click", () => {
			this.chatUiApi.clearMessages();
			this.onResetConversation();
		});
	}
}

export default NewChatSessionButton;
