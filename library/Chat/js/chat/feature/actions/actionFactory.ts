import MinimizeChat from "./minimize/minimizeChat";
import NewSession from "./newSession/newSession";

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

    public addNewSession(): ChatAction {
        return new NewSession(this.actionsContainer, this.actionTemplates, this.chat).add();
    }
}

export default ActionFactory;