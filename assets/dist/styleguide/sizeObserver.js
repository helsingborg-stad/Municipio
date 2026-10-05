var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _SizeObserver = class _SizeObserver {
  constructor(element, propertyName, axis, includePadding, includeFullSize) {
    this.element = element;
    this.propertyName = propertyName;
    this.axis = axis;
    this.includePadding = includePadding;
    this.includeFullSize = includeFullSize;
    this.axis = this.axis || "both";
    this.propertyName = this.propertyName || "size-observer";
    const resizeObserver = new ResizeObserver((entries) => {
      for (const entry of entries) {
        const { width, height } = this.getHeightWidth(entry);
        this.setElementProperties(width, height);
      }
    });
    resizeObserver.observe(element);
  }
  element;
  propertyName;
  axis;
  includePadding;
  includeFullSize;
  getHeightWidth(entry) {
    if (this.includeFullSize && entry.target) {
      return { width: entry.target.scrollWidth ?? 0, height: entry.target.scrollHeight ?? 0 };
    }
    if (this.includePadding && entry.borderBoxSize) {
      return { width: entry.borderBoxSize[0].inlineSize, height: entry.borderBoxSize[0].blockSize };
    }
    return { width: entry.contentRect.width, height: entry.contentRect.height };
  }
  setElementProperties(width, height) {
    if (this.axis == "y" || this.axis == "both") {
      this.element.style.setProperty(`--${this.propertyName}-height`, `${height}px`);
    }
    if (this.axis == "x" || this.axis == "both") {
      this.element.style.setProperty(`--${this.propertyName}-width`, `${width}px`);
    }
  }
};
__name(_SizeObserver, "SizeObserver");
let SizeObserver = _SizeObserver;
function initializeSizeObserver() {
  document.querySelectorAll("[data-js-sizeobserver]").forEach((element) => {
    const axis = element.getAttribute("data-js-sizeobserver-axis");
    const propertyName = element.getAttribute("data-js-sizeobserver");
    const includePadding = element.hasAttribute("data-js-sizeobserver-use-box-size");
    const includeFullSize = element.hasAttribute("data-js-sizeobserver-element-full-size");
    new SizeObserver(element, propertyName, axis, includePadding, includeFullSize);
  });
}
__name(initializeSizeObserver, "initializeSizeObserver");
export {
  initializeSizeObserver
};
//# sourceMappingURL=sizeObserver.js.map
