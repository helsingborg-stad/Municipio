import EventSourceHandlerWithProgressBar from "./EventSourceHandlerWithProgressBar";
import { IProgressBar } from "./IProgressBar";
import ProgressBar from "./ProgressBar";
import ProgressBarWithLabel from "./UIComponents/ProgressBarWithLabel";

export default class EventSourceTrigger {

    private eventSourceHandlerWithProgressBar: EventSourceHandlerWithProgressBar;

    constructor(private triggerElement: HTMLElement, private eventSourceUrl: string) {
        this.eventSourceHandlerWithProgressBar = new EventSourceHandlerWithProgressBar(this.triggerElement, this.eventSourceUrl, this.createProgressBar());
        this.triggerElement.addEventListener('click', this.handleClick.bind(this));
    }

    private handleClick(event: Event) {
        event.preventDefault();
        this.eventSourceHandlerWithProgressBar.start();
    }

    private createProgressBar(): IProgressBar {
        const progressBarElement = document.createElement(ProgressBarWithLabel.customElementName) as ProgressBarWithLabel;
        const progressBar = new ProgressBar(progressBarElement, this.triggerElement);
        return progressBar;
    }
}