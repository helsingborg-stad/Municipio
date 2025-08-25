import { IProgressBar, ProgressBarUpdate } from "./IProgressBar";
import ProgressBarWithLabel from "./UIComponents/ProgressBarWithLabel";

export default class ProgressBar implements IProgressBar {

    constructor(private element: ProgressBarWithLabel, private insertAfterElement: HTMLElement) {
    }

    public update(event: ProgressBarUpdate): void {
        if (event.label !== null) {
            this.element.setAttribute('label', event.label);
        }

        if (event.value !== null) {
            this.element.setAttribute('progress', event.value.toString());
        }
    }

    public show(): void {
        this.insertAfterElement.insertAdjacentElement('afterend', this.element);
    }

    public hide(): void {
        this.element.remove();
    }
}