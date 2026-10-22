var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _KeepInViewPort = class _KeepInViewPort {
  constructor() {
    this.VIEWPORTRESIZE = "data-js-keep-in-viewport-after-resize";
    this.VIEWPORT = "data-js-keep-in-viewport";
    const ViewPortResizeContainer = document.querySelectorAll(`[${this.VIEWPORTRESIZE}]`);
    if (ViewPortResizeContainer) {
      ViewPortResizeContainer.forEach((item) => {
        item.addEventListener("resizeByChildren", _KeepInViewPort.resizeEvent);
      });
    }
    const ViewPortContainer = document.querySelectorAll(`[${this.VIEWPORT}]`);
    if (ViewPortContainer) {
      ViewPortContainer.forEach((item) => {
        _KeepInViewPort.moveInsideViewPort(item, 8);
      });
    }
  }
  /**
   * Keep the actual viewport function cleaned from event objects.
   * @param {object} event 
   * @return void
   */
  static resizeEvent(event) {
    _KeepInViewPort.moveInsideViewPort(event.target, 8);
  }
  /**
   * Move element inside viewport.
   * @param {object} element 
   * @param {integer} margin
   * @return void
   */
  static moveInsideViewPort(element, margin) {
    element.classList.add("u-display--block");
    const viewPortRightDistance = window.innerWidth - element.getBoundingClientRect().right;
    if (viewPortRightDistance < 0) {
      element.setAttribute("style", `${element.getAttribute("style")} left: ${viewPortRightDistance - margin}px;`);
    }
    element.classList.remove("u-display--block");
  }
};
__name(_KeepInViewPort, "KeepInViewPort");
let KeepInViewPort = _KeepInViewPort;
export {
  KeepInViewPort as default
};
//# sourceMappingURL=keepInViewPort.js.map
