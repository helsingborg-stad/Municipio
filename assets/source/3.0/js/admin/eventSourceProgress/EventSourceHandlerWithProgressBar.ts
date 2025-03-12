/**
 * @jest-environment jsdom
 */

import { IProgressBar } from "./IProgressBar";

export default class EventSourceHandlerWithProgressBar {
    private source: EventSource | null = null;

    constructor(private target: HTMLElement, private url: string, private progressBar: IProgressBar) {
    }

    public start(): void {
        this.source = new EventSource(this.url);
        this.disableAllTriggersWithSameUrl();
        this.progressBar.show();
        this.addEventListeners(this.source);
    }

    private disableAllTriggersWithSameUrl(): void {
        document.querySelectorAll(`[data-js-progress-url="${this.url}"]`).forEach((element) => {
            element.setAttribute('disabled', 'disabled');
        });
    }


    private addEventListeners(source: EventSource): void {
        source.addEventListener('message', this.updateLabel.bind(this));
        source.addEventListener('progress', this.updateProgress.bind(this));
        source.addEventListener('finish', this.finish.bind(this));
    }

    private removeEventListeners(source: EventSource): void {
        source.removeEventListener('message', this.updateLabel.bind(this));
        source.removeEventListener('progress', this.updateProgress.bind(this));
        source.removeEventListener('finish', this.finish.bind(this));
    }

    private updateLabel(event: MessageEvent) {
        this.progressBar.update({ label: event.data, value: null });
    }

    private updateProgress(event: MessageEvent) {
        this.progressBar.update({ label: null, value: event.data });
    }

    private finish(event: MessageEvent) {
        this.progressBar.update({ label: event.data, value: 100 });
        this.source!.close();
        this.removeEventListeners(this.source!);
    }
}