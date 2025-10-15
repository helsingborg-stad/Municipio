import { SavedMarker } from "../../mapData";
import { ContainerEventHelperInterface, OpenStateDetail } from "../helper/containerEventHelperInterface";
import { MarkerClickInterface } from "./markerClickInterface";

class MarkerClick implements MarkerClickInterface {
    private markerInfoContainer: HTMLElement;
    private markerInfoTitle: HTMLElement;
    private markerInfoDescription: HTMLElement;
    private markerInfoImage: HTMLElement;
    private closeButton: HTMLElement;
    private hasCorrectMarkup: boolean;
    private openClass: string = "is-open";
    private shouldBeOpen: boolean = false;

    constructor(private container: HTMLElement, private containerEventHelper: ContainerEventHelperInterface) {
        this.markerInfoContainer = this.container.querySelector('[data-js-interactive-map-marker-info-container]') as HTMLElement;
        this.markerInfoTitle = this.container.querySelector('[data-js-interactive-map-marker-info-title]') as HTMLElement;
        this.markerInfoDescription = this.container.querySelector('[data-js-interactive-map-marker-info-description]') as HTMLElement;
        this.markerInfoImage = this.container.querySelector('[data-js-interactive-map-marker-info-image]') as HTMLElement;
        this.closeButton = this.container.querySelector('[data-js-interactive-map-marker-info-close-icon]') as HTMLElement;

        this.hasCorrectMarkup = !!(
            this.markerInfoContainer &&
            this.markerInfoTitle &&
            this.markerInfoDescription &&
            this.markerInfoImage &&
            this.closeButton
        );

        this.setCloseButtonListener();
        this.setOpenCloseListeners();
    }

    public click(markerData: SavedMarker, shouldOpen: boolean): void {
        if (!this.hasCorrectMarkup) {
            return;
        }

        this.shouldBeOpen = shouldOpen;
        this.markerInfoTitle.innerHTML = this.createImageTitleMarkup(markerData);
        this.markerInfoDescription.innerHTML = markerData.description;
        
        if (markerData.image) {
            this.markerInfoImage.innerHTML = this.createImageMarkup(markerData);
        } else {
            this.markerInfoImage.innerHTML = "";
        }

        if (shouldOpen) {
            this.open();
            this.containerEventHelper.dispatchWasOpenedEvent("marker");
        } else {
            this.close();
            this.containerEventHelper.dispatchWasClosedEvent("marker");
        }
    }

    private setCloseButtonListener() {
        this.closeButton.addEventListener('click', () => {
            this.close();
            this.containerEventHelper.dispatchWasClosedEvent("marker");
            this.shouldBeOpen = false;
        });
    }

    private createImageTitleMarkup(markerData: SavedMarker): string {
        if (markerData.url) {
            return `<a href="${markerData.url}">${markerData.title}</a>`;
        }
        
        return markerData.title;
    }
    
    private createImageMarkup(markerData: SavedMarker): string {
        return `<img src="${markerData.image}" alt="${markerData.title}" />`;
    }

    private setOpenCloseListeners() {
        this.container.addEventListener(this.containerEventHelper.getWasOpenedEventName(), (event) => {
            const customEvent = event as CustomEvent<OpenStateDetail>
            if (customEvent.detail === "filter" && this.shouldBeOpen) {
                this.close();
            }
        });

        this.container.addEventListener(this.containerEventHelper.getWasClosedEventName(), (event) => {
            const customEvent = event as CustomEvent<OpenStateDetail>
            if (customEvent.detail === "filter" && this.shouldBeOpen) {
                setTimeout(() => {
                    this.open();
                }, 100);
            }
        });
    }

    private close() {
        this.markerInfoContainer.classList.remove(this.openClass);
    }

    private open() {
        this.markerInfoContainer.classList.add(this.openClass);
    }
}

export default MarkerClick;