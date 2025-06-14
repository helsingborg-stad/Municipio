import { CreateImageOverlay, MapInterface } from "@helsingborg-stad/openstreetmap";
import { SavedImageOverlay } from "../mapData";
import { ImageOverlaysData } from "./interface";
import { StorageInterface } from "./filtering/storageInterface";

class ImageOverlays {
    constructor(
        private map: MapInterface,
        private savedImageOverlays: SavedImageOverlay[],
        private storageInstance: StorageInterface
    ) {}

    public createImageOverlays(): ImageOverlaysData {
        let imageOverlays: ImageOverlaysData = {};
        this.savedImageOverlays.forEach(imageOverlayData => {
            const imageOverlay = new CreateImageOverlay().create({
                url: imageOverlayData.image,
                bounds: imageOverlayData.position
            });

            if (imageOverlayData.layerGroup && this.storageInstance.getOrderedLayerGroups().hasOwnProperty(imageOverlayData.layerGroup)) {
                imageOverlay.addTo(this.storageInstance.getOrderedLayerGroups()[imageOverlayData.layerGroup].getLayerGroup());
            } else {
                imageOverlay.addTo(this.map);
            }
        });

        return imageOverlays;
    }
}

export default ImageOverlays;