import { CreateLayerGroup, type MapInterface } from "@helsingborg-stad/openstreetmap";
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
	constructor(map: MapInterface, mapData: SaveData, container: HTMLElement) {
		// Helper
		const containerEventHelper = new ContainerEvent(container);

		// Options
		const allowFiltering = mapData.layerFilter ?? false;
		const onlyOneLevelLayerGroup = container.hasAttribute(
			"data-js-interactive-map-one-level-only",
		);
		const onlyOneParentLayerGroup = container.hasAttribute(
			"data-js-interactive-map-one-parent-only",
		);

		// Filter
		const storageInstance = new Storage();
		const filterButton = new FilterButton(container, containerEventHelper);
		const filterHelperInstance = new FilterHelper(map, storageInstance);
		const layerGroupFilterFactory = new LayerGroupFilterFactory(
			container,
			map,
			storageInstance,
			filterHelperInstance,
			allowFiltering,
			onlyOneLevelLayerGroup,
			onlyOneParentLayerGroup,
		);

		// Adding layerGroups, markers and imageOverlays to the map
		const layerGroups = new LayerGroups(
			container,
			storageInstance,
			new CreateLayerGroup(),
			layerGroupFilterFactory,
			mapData.layerGroups,
		).createLayerGroups();

		const markers = new Markers(
			map,
			mapData.markers,
			storageInstance,
			new MarkerClick(container, containerEventHelper),
		).createMarkers();

		const imageOverlays = new ImageOverlays(
			map,
			mapData.imageOverlays,
			storageInstance,
		).createImageOverlays();
	}
}

document.addEventListener("DOMContentLoaded", function () {
	(
		document.querySelectorAll(
			"[data-js-interactive-map]",
		) as NodeListOf<HTMLElement>
	).forEach((container) => {
		const mapData = JSON.parse(container.dataset.jsInteractiveMapData ?? "");

		if (!mapData) {
			console.error("Missing mapData");
			return;
		}

		container.addEventListener(
			"map:created",
			(event: Event) => {
				const map = (event as CustomEvent<{ map: MapInterface }>).detail.map;
				new InteractiveMap(map, mapData as SaveData, container);
			},
			{ once: true },
		);
	});
});
