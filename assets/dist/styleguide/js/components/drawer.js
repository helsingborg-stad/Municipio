var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
const _DrawerAccessibility = class _DrawerAccessibility {
  constructor(button, drawer) {
    this.button = button;
    this.drawer = drawer;
    this.drawer.setAttribute("aria-hidden", "true");
    this.closeButton = drawer.querySelector(".c-drawer__close");
    this.firstMenuItem = this.getFirstMenuItem();
    this.lastItem = this.getLastItem();
    this.setupViewOffsetListeners();
    this.lastItem && this.closeButton && this.setupAccessibilityListeners();
  }
  button;
  drawer;
  closeButton;
  firstMenuItem;
  lastItem;
  /**
   * Watches drawer state and size-affecting changes so global view offsets stay current.
   */
  setupViewOffsetListeners() {
    const observer = new MutationObserver(() => updateDrawerViewOffsets());
    observer.observe(this.drawer, {
      attributes: true,
      attributeFilter: ["class", "style"]
    });
    _DrawerAccessibility.setupGlobalViewOffsetListeners();
    updateDrawerViewOffsets();
  }
  /**
   * Adds viewport listeners once, shared by all drawer instances.
   */
  static setupGlobalViewOffsetListeners() {
    if (_DrawerAccessibility.hasGlobalViewOffsetListeners) {
      return;
    }
    window.addEventListener("resize", updateDrawerViewOffsets);
    window.visualViewport?.addEventListener("resize", updateDrawerViewOffsets);
    _DrawerAccessibility.hasGlobalViewOffsetListeners = true;
  }
  /**
   * Focuses on the first menu item (or the close button if there are no menu items)
   * Loop the focus back to the close button when tabbing past the last item.
   * Sets the focus back on the "open" button when the close button is clicked.
   */
  setupAccessibilityListeners() {
    this.button.addEventListener("click", () => {
      (this.firstMenuItem || this.closeButton).focus();
    });
    this.lastItem.addEventListener("keydown", (e) => {
      if (e.key === "Tab") {
        e.preventDefault();
        this.closeButton.focus();
      }
    });
    this.closeButton.addEventListener("click", () => {
      this.button.focus();
    });
    document.addEventListener("keydown", (e) => {
      if (this.drawer.classList.contains("is-open") && e.key === "Escape") {
        this.closeButton.click();
        this.button.focus();
      }
    });
    this.drawer.addEventListener("transitionend", () => {
      if (this.drawer.classList.contains("is-open")) {
        this.drawer.removeAttribute("aria-hidden");
        this.drawer.querySelectorAll("a").forEach((element) => {
          element.setAttribute("tabindex", "0");
        });
      }
    });
    this.drawer.addEventListener("transitionend", () => {
      if (!this.drawer.classList.contains("is-open")) {
        this.drawer.setAttribute("aria-hidden", "true");
      }
    });
    this.drawer.addEventListener("DOMNodeInserted", (event) => {
      const target = event.target;
      if (target.matches("a")) {
        target.setAttribute("tabindex", "0");
      }
    });
  }
  /**
   * Retrieves the first menu item element within the drawer.
   * 
   * @param drawer - The drawer element.
   * @returns The first menu item element, or null if not found.
   */
  getFirstMenuItem() {
    return this.drawer.querySelector(".c-drawer__body a, .c-drawer__body button");
  }
  /**
   * Retrieves the last item in the drawer.
   * 
   * @param drawer - The HTML element representing the drawer.
   * @returns The last item in the drawer.
   */
  getLastItem() {
    const drawerItems = [...this.drawer.querySelectorAll("button, a, input")];
    return drawerItems[drawerItems.length - 1];
  }
};
__name(_DrawerAccessibility, "DrawerAccessibility");
__publicField(_DrawerAccessibility, "hasGlobalViewOffsetListeners", false);
let DrawerAccessibility = _DrawerAccessibility;
function initializeDrawerAccessibility() {
  const drawerToggleButtons = document.querySelectorAll(".c-drawer__toggle");
  drawerToggleButtons.forEach((button) => {
    if (button.hasAttribute("data-js-toggle-trigger") && document.querySelector(`[data-js-toggle-item="${button.getAttribute("data-js-toggle-trigger")}"]`)) {
      const drawer = document.querySelector(`[data-js-toggle-item="${button.getAttribute("data-js-toggle-trigger")}"]`);
      new DrawerAccessibility(button, drawer);
    }
  });
}
__name(initializeDrawerAccessibility, "initializeDrawerAccessibility");
function updateDrawerViewOffsets() {
  const openDrawers = [...document.querySelectorAll(".c-drawer.is-open")];
  const baseUnit = getBaseUnitInPixels();
  const leftOffset = getMaxDrawerOffsetInBaseUnits(
    openDrawers.filter((drawer) => !drawer.classList.contains("c-drawer--right")),
    baseUnit
  );
  const rightOffset = getMaxDrawerOffsetInBaseUnits(
    openDrawers.filter((drawer) => drawer.classList.contains("c-drawer--right")),
    baseUnit
  );
  setViewOffset("left", leftOffset);
  setViewOffset("right", rightOffset);
}
__name(updateDrawerViewOffsets, "updateDrawerViewOffsets");
function setViewOffset(side, offset) {
  const property = `--view-offset-${side}`;
  const currentOffset = Number.parseFloat(document.documentElement.style.getPropertyValue(property)) || 0;
  const easing = offset < currentOffset ? "ease-in" : "ease-out";
  document.documentElement.style.setProperty(`--view-offset-${side}-transition-easing`, easing);
  document.documentElement.style.setProperty(property, offset.toString());
}
__name(setViewOffset, "setViewOffset");
function getMaxDrawerOffsetInBaseUnits(drawers, baseUnit) {
  const maxWidth = drawers.reduce((width, drawer) => {
    const drawerWidth = drawer.getBoundingClientRect().width || drawer.offsetWidth;
    return Math.max(width, drawerWidth);
  }, 0);
  return Number((maxWidth / baseUnit).toFixed(4));
}
__name(getMaxDrawerOffsetInBaseUnits, "getMaxDrawerOffsetInBaseUnits");
function getBaseUnitInPixels() {
  const rawBaseValue = getComputedStyle(document.documentElement).getPropertyValue("--base").trim();
  const parsedBaseValue = Number.parseFloat(rawBaseValue);
  if (rawBaseValue.endsWith("px") && parsedBaseValue > 0) {
    return parsedBaseValue;
  }
  const measuringElement = document.createElement("div");
  measuringElement.style.position = "absolute";
  measuringElement.style.visibility = "hidden";
  measuringElement.style.width = "var(--base, 8px)";
  document.body.appendChild(measuringElement);
  const measuredBaseValue = measuringElement.getBoundingClientRect().width;
  measuringElement.remove();
  return measuredBaseValue || 8;
}
__name(getBaseUnitInPixels, "getBaseUnitInPixels");
if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initializeDrawerAccessibility);
else initializeDrawerAccessibility();
//# sourceMappingURL=drawer.js.map
