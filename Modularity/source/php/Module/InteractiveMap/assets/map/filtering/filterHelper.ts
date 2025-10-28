import { Addable, MapInterface } from "@helsingborg-stad/openstreetmap";
import { LayerGroupFilterInterface } from "./layerGroupFilterInterface";
import { StorageInterface } from "./storageInterface";
import { FilterHelperInterface } from "./filterHelperInterface";

class FilterHelper implements FilterHelperInterface {
    constructor(
        private mapInstance: MapInterface,
        private storageInstance: StorageInterface
    ) {

    }

    public findParent(parentId: string): LayerGroupFilterInterface|null {
        if (this.storageInstance.getOrderedLayerGroups().hasOwnProperty(parentId)) {
            return this.storageInstance.getOrderedLayerGroups()[parentId];
        }

        return null;
    }

    public hideChildrenFilter(id: string): void {
        this.findChildren(id).forEach(child => {
            child.hideFilter();
        });
    }

    public showChildrenFilter(id: string): void {
        this.findChildren(id).forEach(child => {
            child.showFilter();
        });
    }

    public findChildren(id: string): LayerGroupFilterInterface[] {
        return this.storageInstance.getStructuredLayerGroups()[id] ?? [];
    }
}

export default FilterHelper;