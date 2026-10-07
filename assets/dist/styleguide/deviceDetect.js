var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _DeviceDetect = class _DeviceDetect {
  attributeName = "data-js-device-detect";
  constructor() {
    const device = this.getDeviceType();
    if (typeof device === "string") {
      this.init("is-" + device);
    }
  }
  /**
   * Initializes the device detection by adding the device class to elements
   * with the specified attribute.
   * @param deviceClass - The device class to be added to the elements.
   */
  init(deviceClass) {
    const elements = document.querySelectorAll(`[${this.attributeName}]`);
    elements.forEach((element) => {
      element.classList.add(deviceClass);
    });
  }
  /**
   * Retrieves the device type based on the user agent string.
   * @returns The device type as a string or null if it's unknown.
   */
  getDeviceType() {
    const userAgent = navigator.userAgent.toLowerCase();
    if (/iphone|ipad|ipod/.test(userAgent)) {
      return "ios";
    } else if (/android/.test(userAgent)) {
      return "android";
    } else if (/windows/.test(userAgent)) {
      return "windows";
    } else if (/macintosh/.test(userAgent)) {
      return "macos";
    } else if (/linux/.test(userAgent)) {
      return "linux";
    }
    return null;
  }
};
__name(_DeviceDetect, "DeviceDetect");
let DeviceDetect = _DeviceDetect;
export {
  DeviceDetect
};
//# sourceMappingURL=deviceDetect.js.map
