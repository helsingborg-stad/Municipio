import MinimizeChat from "./minimize/minimizeChat";
import Clear from "./clear/clear";

class ActionFactory implements ChatActionFactoryInterface {
    public constructor(
        private actionsContainer: HTMLElement,
        private actionTemplates: HTMLTemplateElement,
        private chat: any
    ) {
    }
    public addMinimize(): ChatAction {
        return new MinimizeChat(this.actionsContainer, this.actionTemplates).add();
    }

    public addClear(): ChatAction {
        return new Clear(this.actionsContainer, this.actionTemplates, this.chat).add();
    }
}

export default ActionFactory;