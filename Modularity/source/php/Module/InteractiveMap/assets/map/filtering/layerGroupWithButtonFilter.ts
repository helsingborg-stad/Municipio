import { Addable, LayerGroup, LayerGroupInterface, MapInterface } from "@helsingborg-stad/openstreetmap";
import { SavedLayerGroup } from "../../mapData";
import { LayerGroupFilterInterface } from "./layerGroupFilterInterface";
import { FilterHelperInterface } from "./filterHelperInterface";

class LayerGroupWithButtonFilter implements LayerGroupFilterInterface {
    private listenerIsInitiated: boolean = false;
    private parent: LayerGroupFilterInterface|null;
    private filterButton: HTMLElement|null;
    private icon: HTMLElement|null;
    private addable: Addable;
    private activeClass: string = 'is-active';
    private displayNoneClass: string = 'u-display--none';
    private iconTarget: string = 'data-material-symbol';
    private checkedIcon: string = 'check_box';
    private unCheckedIcon: string = 'check_box_outline_blank';
    private active: boolean = false;

    constructor(
        private container: HTMLElement,
        private mapInstance: MapInterface,
        private filterHelperInstance: FilterHelperInterface,
        private savedLayerGroup: SavedLayerGroup,
        private layerGroup: LayerGroupInterface
    ) {
        this.parent = this.filterHelperInstance.findParent(this.getSavedLayerGroup().layerGroup);
        this.addable = this.parent ? this.parent.getLayerGroup() : this.mapInstance;
        this.filterButton = this.container.querySelector(`[data-js-layer-group="${this.getSavedLayerGroup().id}"]`) as HTMLElement;
        this.icon = this.filterButton?.querySelector(`[${this.iconTarget}]`);
        this.setDefaultValue();
    }

    public init(): void {
        if (this.listenerIsInitiated || !this.filterButton || !this.icon) {
            return;
        }

        // Set the default value of the filter button
        this.listenerIsInitiated = true;
        this.setSubListener();
    }

    private setDefaultValue(): void {
        if (!this.filterButton || !this.icon) {
            return;
        }

        if (this.savedLayerGroup.preselected) {
            this.active = true;
            this.filterButton.setAttribute('aria-pressed', 'true');
            this.icon.setAttribute(this.iconTarget, this.checkedIcon);
            this.getLayerGroup().addTo(this.addable);
            this.filterButton.classList.add(this.activeClass);
        }
    }

    private setSubListener(): void {
        this.filterButton!.addEventListener('click', () => {
            if (this.filterButton!.classList.contains(this.activeClass)) {
                this.removeActive();
                this.hideChildren();
            } else {
                this.setActive();
                this.showChildren();
            }
        });
    }

    private removeActive(): void {
        this.active = false;
        this.getLayerGroup().removeLayerGroupFrom(this.addable);
        this.icon!.setAttribute(this.iconTarget, this.unCheckedIcon);
        this.filterButton!.classList.remove(this.activeClass);
    }

    private setActive(): void {
        this.active = true;
        this.getLayerGroup().addTo(this.addable);
        this.icon!.setAttribute(this.iconTarget, this.checkedIcon);
        this.filterButton!.classList.add(this.activeClass);
    }

    private hideChildren(): void {
        this.filterHelperInstance.hideChildrenFilter(this.getSavedLayerGroup().id);
    }

    private showChildren(): void {
        this.filterHelperInstance.showChildrenFilter(this.getSavedLayerGroup().id);
    }

    public isActive(): boolean {
        return this.active;
    }

    public hideFilter(): void {
        this.filterButton?.classList.add(this.displayNoneClass);
        this.hideChildren();
    }

    public showFilter(): void {
        this.filterButton?.classList.remove(this.displayNoneClass);
        if (this.isActive()) {
            this.showChildren();
        }
    }

    public getSavedLayerGroup(): SavedLayerGroup {
        return this.savedLayerGroup;
    }

    public getLayerGroup(): LayerGroupInterface {
        return this.layerGroup;
    }
}

export default LayerGroupWithButtonFilter;