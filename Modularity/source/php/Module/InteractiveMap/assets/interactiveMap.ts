import { CreateMap, CreateAttribution, CreateTileLayer, TilesHelper, CreateLayerGroup } from "@helsingborg-stad/openstreetmap";
import { SaveData } from "./mapData";
import LayerGroups from "./map/layerGroups";
import Markers from "./map/markers";
import ImageOverlays from "./map/imageOverlays";
import LayerGroupFilterFactory from "./map/filtering/layerGroupFilterFactory";
import Storage from "./map/filtering/storage";
import FilterHelper from "./map/filtering/filterHelper";
import FilterButton from "./map/filtering/filterButton";
import MarkerClick from "./map/markerClick/markerClick";
import ContainerEvent from "./map/helper/containerEventHelper";

class InteractiveMap {
    constructor(
        mapId: string,
        mapData: SaveData,
        container: HTMLElement
    ) {
        const map = new CreateMap({
            id: mapId,
            center: mapData.startPosition.latlng ?? { lat: 56.046467, lng: 12.694512 },
            zoom: mapData.startPosition.zoom ?? 16,
        }).create();

        // Helper
        const containerEventHelper = new ContainerEvent(container);

        // Options
        const allowFiltering = mapData.layerFilter ?? false;
        const onlyOneLevelLayerGroup = container.hasAttribute('data-js-interactive-map-one-level-only');
        const onlyOneParentLayerGroup = container.hasAttribute('data-js-interactive-map-one-parent-only');

        // Add the tiles and attribution to the map
        const {url, attribution} = new TilesHelper().getDefaultTiles(mapData.mapStyle);
        new CreateTileLayer().create().setUrl(url).addTo(map);
        new CreateAttribution().create().setPrefix(attribution).addTo(map);

        // Filter
        const storageInstance = new Storage();
        const filterButton = new FilterButton(container, containerEventHelper);
        const filterHelperInstance = new FilterHelper(map, storageInstance);
        const layerGroupFilterFactory = new LayerGroupFilterFactory(container, map, storageInstance, filterHelperInstance, allowFiltering, onlyOneLevelLayerGroup, onlyOneParentLayerGroup);

        // Adding layerGroups, markers and imageOverlays to the map
        const layerGroups = new LayerGroups(
            container,
            storageInstance,
            new CreateLayerGroup(),
            layerGroupFilterFactory,
            mapData.layerGroups
        ).createLayerGroups();

        const markers = new Markers(
            map,
            mapData.markers,
            storageInstance,
            new MarkerClick(container, containerEventHelper)
        ).createMarkers();

        const imageOverlays = new ImageOverlays(
            map,
            mapData.imageOverlays,
            storageInstance
        ).createImageOverlays();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    (document.querySelectorAll('[data-js-interactive-map]') as NodeListOf<HTMLElement>).forEach(container => {
        const mapId = container.dataset.jsInteractiveMap;
        const mapData = JSON.parse(container.dataset.jsInteractiveMapData ?? '');

        if (mapId && mapData) {
            new InteractiveMap(mapId, mapData as SaveData, container);
        } else {
            console.error('Missing mapId or mapData');
        }
    });
});