class ClearButton {
    constructor(private clearButtonElement: HTMLElement, private chat: any) {
        this.setClearButtonListener();
    }

    private setClearButtonListener(): void {
        this.clearButtonElement.addEventListener("click", () => {
            this.chat.clearMessages();
        });
    }
}

export default ClearButton;