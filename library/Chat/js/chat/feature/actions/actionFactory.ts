import Minimize from "./minimize/minimize";
import Clear from "./clear/clear";
import Close from "./close/close";
import CloseConfirmationController from "./close/closeConfirmationController";

class ActionFactory implements ChatActionFactoryInterface {
    public constructor(
        private actionsContainer: HTMLElement,
        private actionTemplates: HTMLTemplateElement,
        private chat: any
    ) {
    }

    public addMinimize(): ChatAction {
        return new Minimize(this.actionsContainer, this.actionTemplates).add();
    }

    public addClear(): ChatAction {
        return new Clear(this.actionsContainer, this.actionTemplates, this.chat).add();
    }

    public addClose(): ChatAction {
        const closeConfirmationController = new CloseConfirmationController(this.actionTemplates, this.chat);

        return new Close(this.actionsContainer, this.actionTemplates, closeConfirmationController).add();
    }
}

export default ActionFactory;