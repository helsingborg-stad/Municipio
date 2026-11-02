var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
import { _ as __vitePreload } from "../../preload-helper.js";
const _MapFactory = class _MapFactory {
  /**
   * Creates a map instance for the specified provider using the provided arguments.
   * If the provider's factory has not been loaded yet, it dynamically imports
   * the module and initializes the factory before creating the map instance.
   *
   * @param provider - The name of the map provider (e.g., "openstreetmap").
   * @param args     - Configuration arguments required to create the map instance.
   * @returns A promise that resolves to the created map instance.
   */
  static async create(provider, args) {
    const key = provider.toLowerCase();
    if (!_MapFactory.providers[key]) {
      switch (key) {
        case "openstreetmap":
        default: {
          const module = await __vitePreload(() => import("../../openstreetmapFactory.js"), true ? [] : void 0);
          _MapFactory.providers[key] = new module.default();
          break;
        }
      }
    }
    return _MapFactory.providers[key].create(args);
  }
};
__name(_MapFactory, "MapFactory");
__publicField(_MapFactory, "providers", {});
let MapFactory = _MapFactory;
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-js-map]").forEach((mapContainer) => {
      const id = mapContainer.getAttribute("data-js-map");
      const provider = mapContainer.getAttribute("data-js-map-provider");
      const mapStyle = mapContainer.getAttribute("data-js-map-style");
      const lat = mapContainer.getAttribute("data-js-map-lat");
      const lng = mapContainer.getAttribute("data-js-map-lng");
      const zoom = mapContainer.getAttribute("data-js-map-zoom");
      const markers = mapContainer.getAttribute("data-js-map-markers");
      if (!id || !provider || !lat || !lng) {
        console.warn("Map element is missing required attributes: data-js-map and data-js-map-provider");
        return;
      }
      const args = {
        container: mapContainer,
        id,
        lat,
        lng,
        style: mapStyle,
        zoom,
        markers
      };
      MapFactory.create(provider, args);
    });
  });
}
__name(init, "init");
init();
//# sourceMappingURL=map.js.map
