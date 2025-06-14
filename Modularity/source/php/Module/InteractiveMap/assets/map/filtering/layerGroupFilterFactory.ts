import { LayerGroupInterface, MapInterface } from "@helsingborg-stad/openstreetmap";
import { LayerGroupFilterInterface } from "./layerGroupFilterInterface";
import { SavedLayerGroup } from "../../mapData";
import Storage from "./storage";
import LayerGroupWithSelectFilter from "./layerGroupWithSelectFilter";
import { FilterHelperInterface } from "./filterHelperInterface";
import LayerGroupWithButtonFilter from "./layerGroupWithButtonFilter";
import LayerGroupWithoutFilter from "./LayerGroupWithoutFilter";

class LayerGroupFilterFactory {
    constructor(
        private container: HTMLElement,
        private mapInstance: MapInterface,
        private storageInstance: Storage,
        private filterHelperInstance: FilterHelperInterface,
        private allowFiltering: string,
        private onlyOneLevelLayerGroup: boolean,
        private onlyOneParentLayerGroup: boolean,
    ) {}
    createLayerGroupFilter(
        savedLayerGroup: SavedLayerGroup,
        layerGroup: LayerGroupInterface
    ): LayerGroupFilterInterface {
        const isTopLevel = !savedLayerGroup.layerGroup || savedLayerGroup.layerGroup === '';

        if (!this.filteringIsAllowed() || (this.onlyOneParentLayerGroup && isTopLevel)) {
            return new LayerGroupWithoutFilter(this.container, this.mapInstance, this.filterHelperInstance, savedLayerGroup, layerGroup);
        }

        if (!this.onlyOneLevelLayerGroup && isTopLevel) {
            return new LayerGroupWithSelectFilter(this.container, this.mapInstance, this.filterHelperInstance, savedLayerGroup, layerGroup);
        }

        return new LayerGroupWithButtonFilter(this.container, this.mapInstance, this.filterHelperInstance,savedLayerGroup, layerGroup);
    }

    private filteringIsAllowed(): boolean {
        return !!this.allowFiltering && this.allowFiltering !== 'false';
    }
}

export default LayerGroupFilterFactory;