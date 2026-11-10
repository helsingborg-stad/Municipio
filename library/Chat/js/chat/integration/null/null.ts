class Null implements ChatIntegrationInterface {
    public constructor(
        private chat: any,
        private uiManager: ChatUiManagerInterface,
    ) {}

    public init(): Promise<this|false> {
        this.uiManager.clearActions();
        this.removeChatAndPopover();
        return Promise.resolve(this);
    }

    private removeChatAndPopover(): void {
        const chatElement = this.chat?.getElement();

        if (!chatElement) {
            return;
        }

        const popoverElement = chatElement.closest("#chat-global-root, [popover]");

        // Only a chat inside a popover has a bubble trigger. A chat outside one (e.g. the
        // chat block) must never remove the global bubble button.
        if (popoverElement) {
            document
                .querySelectorAll("[data-js-municipio-chat-bubble-trigger]")
                .forEach((trigger) => {
                    if (trigger.getAttribute("popovertarget") === popoverElement.id) {
                        trigger.remove();
                    }
                });
            popoverElement.remove();
            return;
        }

        chatElement.closest("[data-js-municipio-chat-wrapper]")?.remove();
        chatElement.remove();
    }
}

export default Null;