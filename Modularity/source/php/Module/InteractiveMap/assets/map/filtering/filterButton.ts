import { ContainerEventHelperInterface, OpenStateDetail } from "../helper/containerEventHelperInterface";

class FilterButton {
    private filterButton: HTMLElement|null;
    private filterItemsContainer: HTMLElement|null;
    private innerCloseButton: HTMLElement|null;
    private isOpen: boolean = false;
    private iconAttribute: string = "data-material-symbol";
    private openClass: string = "is-open";

    constructor(
        private container: HTMLElement,
        private containerEventHelper: ContainerEventHelperInterface,
    ) {
        this.filterButton = this.container.querySelector('[data-js-interactive-map-filter-icon]');
        this.innerCloseButton = this.container.querySelector('[data-js-interactive-map-filters-close-icon]');
        this.filterItemsContainer = this.container.querySelector('[data-js-interactive-map-filters-container]');

        if (this.filterButton && this.filterItemsContainer) {
            if (this.filterItemsContainer.classList.contains(this.openClass)) {
                this.isOpen = true;
            }

            this.setListener();
            this.setOpenCloseListeners();
        }
    }

    private setListener() {
        this.filterButton!.addEventListener('click', () => {
            if (!this.isOpen) {
                this.scrollIntoViewIfNeeded();
                this.open();
                this.containerEventHelper.dispatchWasOpenedEvent("filter");
            } else {
                this.close();
                this.containerEventHelper.dispatchWasClosedEvent("filter");
            }
        });

        this.innerCloseButton?.addEventListener('click', () => {
            this.close();
        });
    }

    private setOpenCloseListeners() {
        this.container.addEventListener(this.containerEventHelper.getWasOpenedEventName(), (event) => {
            const customEvent = event as CustomEvent<OpenStateDetail>
            if (customEvent.detail === "marker" && this.isOpen) {
                this.close();
            }
        });
    }

    private close() {
        this.filterItemsContainer!.classList.remove(this.openClass);
        this.filterButton?.setAttribute(this.iconAttribute, 'tune');
        this.isOpen = false;
    }
    
    private open() {
        this.filterItemsContainer!.classList.add(this.openClass);
        this.filterButton?.setAttribute(this.iconAttribute, 'close');
        this.isOpen = true;
    }

    private scrollIntoViewIfNeeded() {
        const rect = this.container.getBoundingClientRect();
        const isNotVisible = rect.bottom > window.innerHeight;

        if (isNotVisible) {
            this.container.scrollIntoView({ behavior: "smooth", block: "end" });
        }
    }
}

export default FilterButton;