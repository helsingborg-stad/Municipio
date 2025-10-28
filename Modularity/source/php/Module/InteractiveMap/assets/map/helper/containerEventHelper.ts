import { ContainerEventHelperInterface, OpenStateDetail } from "./containerEventHelperInterface";

class ContainerEventHelper implements ContainerEventHelperInterface {
    private wasOpenedEventName: string = "wasOpened";
    private wasClosedEventName: string = "wasClosed";
    constructor(private container: HTMLElement) {

    }

    public getWasOpenedEventName(): string {
        return this.wasOpenedEventName;
    }

    public getWasClosedEventName(): string {
        return this.wasClosedEventName;
    }

    public dispatchWasOpenedEvent(detail: OpenStateDetail) {
        const event = new CustomEvent('wasOpened', {detail});
        this.container.dispatchEvent(event);
    }

    public dispatchWasClosedEvent(detail: OpenStateDetail) {
        const event = new CustomEvent('wasClosed', {detail});
        this.container.dispatchEvent(event);
    }
}

export default ContainerEventHelper;