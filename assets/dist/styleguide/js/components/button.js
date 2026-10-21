var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const CUTOUT_BUTTON_SELECTOR = ".c-button.c-button__filled--inherit";
const TRANSPARENT = "rgba(0, 0, 0, 0)";
function findSurfaceColor(button) {
  let ancestor = button.parentElement;
  while (ancestor) {
    const backgroundColor = getComputedStyle(ancestor).backgroundColor;
    if (backgroundColor !== "transparent" && backgroundColor !== TRANSPARENT) {
      return backgroundColor;
    }
    ancestor = ancestor.parentElement;
  }
  return null;
}
__name(findSurfaceColor, "findSurfaceColor");
function syncCutoutSurface(button) {
  const surfaceColor = findSurfaceColor(button);
  if (surfaceColor && button.style.getPropertyValue("--c-button-cutout-surface") !== surfaceColor) {
    button.style.setProperty("--c-button-cutout-surface", surfaceColor);
  }
}
__name(syncCutoutSurface, "syncCutoutSurface");
function syncAllCutoutSurfaces() {
  document.querySelectorAll(CUTOUT_BUTTON_SELECTOR).forEach(syncCutoutSurface);
}
__name(syncAllCutoutSurfaces, "syncAllCutoutSurfaces");
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    syncAllCutoutSurfaces();
    let frame = 0;
    const observer = new MutationObserver(() => {
      cancelAnimationFrame(frame);
      frame = requestAnimationFrame(syncAllCutoutSurfaces);
    });
    observer.observe(document.documentElement, {
      attributeFilter: ["class", "style"],
      attributes: true,
      subtree: true
    });
  });
}
__name(init, "init");
init();
//# sourceMappingURL=button.js.map
