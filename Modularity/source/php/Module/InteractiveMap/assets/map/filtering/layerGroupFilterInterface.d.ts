import { LayerGroupInterface } from "@helsingborg-stad/openstreetmap";
import { SavedLayerGroup } from "../../mapData";

export interface LayerGroupFilterInterface {
    init(): void;
    getLayerGroup(): LayerGroupInterface;
    getSavedLayerGroup(): SavedLayerGroup;
    hideFilter(): void;
    showFilter(): void;
    isActive(): void;
}