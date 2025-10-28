export type OpenStateDetail = "marker" | "filter";
interface ContainerEventHelperInterface {
    getWasOpenedEventName(): string;
    getWasClosedEventName(): string;
    dispatchWasOpenedEvent(detail: any): void;
    dispatchWasClosedEvent(detail: any): void;
}