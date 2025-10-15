import { CreateLayerGroupInterface } from "@helsingborg-stad/openstreetmap";
import { SavedLayerGroup } from "../mapData";
import LayerGroupFilterFactory from "./filtering/layerGroupFilterFactory";
import { StorageInterface } from "./filtering/storageInterface";

class LayerGroups {
    constructor(
        private container: HTMLElement,
        private storageInstance: StorageInterface,
        private createLayerGroup: CreateLayerGroupInterface,
        private layerGroupFilterFactory: LayerGroupFilterFactory,
        private savedLayerGroups: SavedLayerGroup[],
    ) {}

    public createLayerGroups(): LayerGroups {
        // Add to storage
        this.savedLayerGroups.forEach(layer => {
            const layerGroup = this.createLayerGroup.create();

            const layerGroupDataFilter = this.layerGroupFilterFactory.createLayerGroupFilter(
                layer,
                layerGroup
            );

            this.storageInstance.setOrderedLayerGroup(layer.id, layerGroupDataFilter);

            const parent = layer.layerGroup ? layer.layerGroup : '0';
            const structuredLayerGroups = this.storageInstance.getStructuredLayerGroups();
            const structuredLayerGroup = structuredLayerGroups[parent] ?? [];
            structuredLayerGroup.push(layerGroupDataFilter);
            this.storageInstance.setStructuredLayerGroup(parent, structuredLayerGroup);
        });

        // Initiate the layer group filters after added to storage
        const orderedLayerGroups = this.storageInstance.getOrderedLayerGroups();

        for (const layerGroupId in orderedLayerGroups) {
            orderedLayerGroups[layerGroupId].init();
        }

        return this;
    }
}

export default LayerGroups;