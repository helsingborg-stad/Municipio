import Minimize from "./minimize/minimize";
import Clear from "./clear/clear";
import Close from "./close/close";
import CloseConfirmationController from "./close/closeConfirmationController";

class ActionFactory implements ChatActionFactoryInterface {
    public constructor(
        private actionsContainer: HTMLElement,
        private chatTemplates: HTMLTemplateElement,
        private chat: any
    ) {
    }

    public addMinimize(): ChatAction {
        return new Minimize(this.actionsContainer, this.chatTemplates).add();
    }

    public addClear(): ChatAction {
        return new Clear(this.actionsContainer, this.chatTemplates, this.chat).add();
    }

    public addClose(): ChatAction {
        const closeConfirmationController = new CloseConfirmationController(this.chatTemplates, this.chat);

        return new Close(this.actionsContainer, this.chatTemplates, closeConfirmationController).add();
    }
}

export default ActionFactory;