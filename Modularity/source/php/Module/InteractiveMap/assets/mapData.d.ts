import { LatLngObject, LatLngBoundsObject } from "@helsingborg-stad/openstreetmap";

type SavedLayerGroup = {
    title: string;
    color: string;
    icon: string;
    layerGroup: string;
    id: string;
    preselected: boolean;
};

type SavedMarker = {
    title: string;
    description: string;
    url: string;
    position: LatLngObject;
    layerGroup: string;
    image: string;
};

type SavedImageOverlay = {
    title: string;
    image: string;
    position: LatLngBoundsObject;
    layerGroup: string;
    aspectRatio: number;
};

type SavedStartPosition = {
    latlng: LatLngObject;
    zoom: number;
}

type SaveData = {
    layerGroups: SavedLayerGroup[];
    markers: SavedMarker[];
    imageOverlays: SavedImageOverlay[];
    startPosition: SavedStartPosition;
    mapStyle: string;
    layerFilter: string;
    layerFilterTitle: string;
    layerFilterDefaultOpen: string;
}
