class NewChatSessionButton {
    constructor(private newChatButtonElement: HTMLElement, private chatInstance: ChatInterface, private chat: any) {
        this.setListeners();
    }

    private setListeners(): void {
        this.newChatButtonElement.addEventListener("click", () => {
            this.chat.clearMessages();
            this.chatInstance.createNewChatSession(null);
        });
    }
}

export default NewChatSessionButton;