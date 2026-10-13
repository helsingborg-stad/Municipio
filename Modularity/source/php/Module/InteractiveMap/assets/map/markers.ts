import { CreateMarker, MapInterface } from "@helsingborg-stad/openstreetmap";
import { SavedMarker } from "../mapData";
import { MarkersData } from "./interface";
import { StorageInterface } from "./filtering/storageInterface";
import { MarkerClickInterface } from "./markerClick/markerClickInterface";
import { createIcon } from "./icon";

class Markers {
	constructor(
		private map: MapInterface,
		private savedMarkers: SavedMarker[],
		private storageInstance: StorageInterface,
		private markerClick: MarkerClickInterface,
	) {}

	public createMarkers(): MarkersData {
		let markers: MarkersData = [];
		this.savedMarkers.forEach((markerData) => {
			const hasParent = this.hasParent(markerData.layerGroup);
			const markerColor = this.getMarkerColor(hasParent, markerData.layerGroup);
			const markerIcon = this.getMarkerIcon(hasParent, markerData.layerGroup);
			const html = this.getHtml(markerIcon, markerColor);

			const marker = new CreateMarker().create(
				{
					position: markerData.position,
					html: html,
					className: "interactive-map__marker",
					iconSize: [32, 32],
					iconAnchor: [16, 2],
				},
				{
					content: `<h2 class="interactive-map__popup-title">${markerData.title}</h2>`,
					closeButton: false,
				},
			);

			if (
				markerData.layerGroup &&
				this.storageInstance
					.getOrderedLayerGroups()
					.hasOwnProperty(markerData.layerGroup)
			) {
				marker.addTo(
					this.storageInstance
						.getOrderedLayerGroups()
						[markerData.layerGroup].getLayerGroup(),
				);
			} else {
				marker.addTo(this.map);
			}

			marker.addListener("click", () => {
				this.markerClick.click(markerData, marker.isPopupOpen());
			});

			marker.addListener("popupopen", () => {
				marker.setIcon({
					html: this.getHighlightedHtml(markerIcon, markerColor),
					iconSize: [32, 32],
					iconAnchor: [16, 2],
				});
			});

			marker.addListener("popupclose", () => {
				marker.setIcon({
					html: this.getHtml(markerIcon, markerColor),
					iconSize: [32, 32],
					iconAnchor: [16, 2],
				});
			});

			markers.push({
				data: markerData,
				marker,
			});
		});

		return markers;
	}

	private getHighlightedHtml(icon: string, color: string): string {
		return this.getMarkerHtml(icon, color, true);
	}

	private getHtml(icon: string, color: string): string {
		return this.getMarkerHtml(icon, color);
	}

	private getMarkerHtml(icon: string, color: string, highlighted = false): string {
		const backgroundColor = highlighted ? "white" : color;
		const markerClass = highlighted ? " interactive-map__highlighted-marker" : "";

		return `<span class="interactive-map__marker-icon${markerClass}" style="align-items: center; background-color: ${backgroundColor}; border: solid 2px ${color}; border-radius: 50%; box-sizing: border-box; color: ${highlighted ? color : "white"}; display: flex; flex: 0 0 32px; height: 32px; justify-content: center; line-height: 0; overflow: hidden; width: 32px;">${createIcon(icon)}</span>`;
	}

	private getMarkerIcon(hasParent: boolean, layerGroup: string): string {
		if (
			!hasParent ||
			!this.storageInstance
				.getOrderedLayerGroups()
				[layerGroup].getSavedLayerGroup().icon
		) {
			return "location_on";
		}

		return this.storageInstance
			.getOrderedLayerGroups()
			[layerGroup].getSavedLayerGroup().icon;
	}

	private getMarkerColor(hasParent: boolean, layerGroup: string): string {
		if (
			!hasParent ||
			!this.storageInstance
				.getOrderedLayerGroups()
				[layerGroup].getSavedLayerGroup().color
		) {
			return "#E04A39";
		}

		return this.storageInstance
			.getOrderedLayerGroups()
			[layerGroup].getSavedLayerGroup().color;
	}

	private hasParent(layerGroup: string): boolean {
		return (
			!!layerGroup &&
			this.storageInstance.getOrderedLayerGroups().hasOwnProperty(layerGroup)
		);
	}
}

export default Markers;
