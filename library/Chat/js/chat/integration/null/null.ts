class Null implements ChatIntegrationInterface {
    public constructor(private chat: any) {}

    public init(): Promise<this|false> {
        this.removeChatAndPopover();
        return Promise.resolve(this);
    }

    private removeChatAndPopover(): void {
        const chatElement = this.chat?.getElement?.();

        if (!chatElement) {
            return;
        }

        const popoverElement = chatElement.closest("#chat-global-root, [popover]");
        const popoverTargetId = popoverElement?.id ?? "chat-global-root";
        const bubbleTrigger = document.querySelector(
            `[data-js-municipio-chat-bubble-trigger][popovertarget=\"${popoverTargetId}\"]`,
        );
        const chatWrapper = chatElement.closest("[data-js-municipio-chat-wrapper]");

        bubbleTrigger?.remove();
        if (popoverElement) {
            popoverElement.remove();
            return;
        }

        chatWrapper?.remove();
        chatElement.remove();
    }
}

export default Null;