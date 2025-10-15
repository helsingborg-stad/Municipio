import { SavedMarker } from "../../mapData";

interface MarkerClickInterface {
    click(markerData: SavedMarker, isAlreadyOpen: boolean): void;
}