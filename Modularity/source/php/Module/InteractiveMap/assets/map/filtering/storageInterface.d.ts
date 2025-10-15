import { OrderedLayerGroups, StructuredLayerGroups } from "../interface";
import { LayerGroupFilterInterface } from "./layerGroupFilterInterface";

interface StorageInterface {
    setStructuredLayerGroup(id: string, value: LayerGroupFilterInterface[]): void;
    getStructuredLayerGroups(): StructuredLayerGroups;
    setOrderedLayerGroup(id: string, value: LayerGroupFilterInterface): void;
    getOrderedLayerGroups(): OrderedLayerGroups;
}