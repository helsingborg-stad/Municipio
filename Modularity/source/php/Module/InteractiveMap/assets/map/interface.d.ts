import { ImageOverlayInterface, LayerGroupInterface, MarkerInterface } from "@helsingborg-stad/openstreetmap";
import { SavedImageOverlay, SavedLayerGroup, SavedMarker } from "../mapData";
import { LayerGroupFilterInterface } from "./filtering/layerGroupFilterInterface";

type MarkersData = {
    [key: string]: {
        data: SavedMarker;
        marker: MarkerInterface;
    }
}

type LayerGroupData = {
    data: SavedLayerGroup;
    layerGroup: LayerGroupInterface;
    filterButton: HTMLElement|null;
}

type OrderedLayerGroups = {
    [key: string]: LayerGroupFilterInterface;
}

type StructuredLayerGroups = {
    [key: string]: LayerGroupFilterInterface[];
}

type ImageOverlaysData = {
    [key: string]: {
        data: SavedImageOverlay;
        imageOverlay: ImageOverlayInterface;
    }
}
