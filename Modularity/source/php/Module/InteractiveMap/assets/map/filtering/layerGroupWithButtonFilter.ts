import {
	Addable,
	LayerGroup,
	LayerGroupInterface,
	MapInterface,
} from "@helsingborg-stad/openstreetmap";
import { SavedLayerGroup } from "../../mapData";
import { LayerGroupFilterInterface } from "./layerGroupFilterInterface";
import { FilterHelperInterface } from "./filterHelperInterface";
import { setIcon } from "../icon";

class LayerGroupWithButtonFilter implements LayerGroupFilterInterface {
	private listenerIsInitiated: boolean = false;
	private parent: LayerGroupFilterInterface | null;
	private filterButton: HTMLElement | null;
	private addable: Addable;
	private activeClass: string = "is-active";
	private displayNoneClass: string = "u-display--none";
	private checkedIcon: string = "check_box";
	private unCheckedIcon: string = "check_box_outline_blank";
	private active: boolean = false;

	constructor(
		private container: HTMLElement,
		private mapInstance: MapInterface,
		private filterHelperInstance: FilterHelperInterface,
		private savedLayerGroup: SavedLayerGroup,
		private layerGroup: LayerGroupInterface,
	) {
		this.parent = this.filterHelperInstance.findParent(
			this.getSavedLayerGroup().layerGroup,
		);
		this.addable = this.parent ? this.parent.getLayerGroup() : this.mapInstance;
		this.filterButton = this.container.querySelector(
			`[data-js-layer-group="${this.getSavedLayerGroup().id}"]`,
		) as HTMLElement;
		this.setDefaultValue();
	}

	public init(): void {
		if (this.listenerIsInitiated || !this.filterButton) {
			return;
		}

		// Set the default value of the filter button
		this.listenerIsInitiated = true;
		this.setSubListener();
	}

	private setDefaultValue(): void {
		if (!this.filterButton) {
			return;
		}

		if (this.savedLayerGroup.preselected) {
			this.active = true;
			this.filterButton.setAttribute("aria-pressed", "true");
			setIcon(this.filterButton, this.checkedIcon);
			this.getLayerGroup().addTo(this.addable);
			this.filterButton.classList.add(this.activeClass);
		}
	}

	private setSubListener(): void {
		this.filterButton!.addEventListener("click", () => {
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
		this.mapInstance.getAddable().closePopup();
		this.getLayerGroup().removeLayerGroupFrom(this.addable);
		this.filterButton!.setAttribute("aria-pressed", "false");
		setIcon(this.filterButton, this.unCheckedIcon);
		this.filterButton!.classList.remove(this.activeClass);
	}

	private setActive(): void {
		this.active = true;
		this.getLayerGroup().addTo(this.addable);
		this.filterButton!.setAttribute("aria-pressed", "true");
		setIcon(this.filterButton, this.checkedIcon);
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
