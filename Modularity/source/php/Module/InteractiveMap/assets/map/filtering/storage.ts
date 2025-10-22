import { OrderedLayerGroups, StructuredLayerGroups } from "../interface";
import { StorageInterface } from "./storageInterface";
import { LayerGroupFilterInterface } from "./layerGroupFilterInterface";

class Storage implements StorageInterface {
    protected structuredLayerGroups: StructuredLayerGroups = {};
    protected orderedLayerGroups: OrderedLayerGroups = {};

    public setStructuredLayerGroup(id: string, value: LayerGroupFilterInterface[]): void {
        this.structuredLayerGroups[id] = value;
    }

    public getStructuredLayerGroups(): StructuredLayerGroups {
        return this.structuredLayerGroups;
    }

    public setOrderedLayerGroup(id: string, value: LayerGroupFilterInterface): void {
        this.orderedLayerGroups[id] = value;
    }

    public getOrderedLayerGroups(): OrderedLayerGroups {
        return this.orderedLayerGroups;
    }
}

export default Storage;