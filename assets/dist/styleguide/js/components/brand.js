var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _BrandViewBoxManager = class _BrandViewBoxManager {
  constructor(svg, container, textElement, figureElement) {
    this.svg = svg;
    this.container = container;
    this.textElement = textElement;
    this.figureElement = figureElement;
    this.updateViewBox();
    window.addEventListener("resize", this.handleResize);
  }
  svg;
  container;
  textElement;
  figureElement;
  resizeFrame = null;
  handleResize = /* @__PURE__ */ __name(() => {
    if (this.resizeFrame !== null) {
      window.cancelAnimationFrame(this.resizeFrame);
    }
    this.resizeFrame = window.requestAnimationFrame(() => {
      this.updateViewBox();
      this.resizeFrame = null;
    });
  }, "handleResize");
  updateViewBox() {
    const containerStyles = window.getComputedStyle(this.container);
    const containerHeight = parseFloat(containerStyles.getPropertyValue("height"));
    const renderedHeight = this.container.getBoundingClientRect().height;
    const scale = renderedHeight > 0 ? containerHeight / renderedHeight : 1;
    const gap = parseFloat(containerStyles.getPropertyValue("gap")) * scale;
    const textWidth = this.textElement.getBoundingClientRect().width * scale;
    const figureWidth = this.figureElement ? this.figureElement.getBoundingClientRect().width * scale : 0;
    const totalWidth = Math.ceil(figureWidth + textWidth + gap);
    this.svg.setAttribute("viewBox", `0 0 ${totalWidth} ${Math.ceil(containerHeight)}`);
  }
};
__name(_BrandViewBoxManager, "BrandViewBoxManager");
let BrandViewBoxManager = _BrandViewBoxManager;
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".c-brand").forEach((brandElement) => {
      if (brandElement.dataset.aspectRatio) {
        return;
      }
      const svg = brandElement.querySelector(".c-brand__viewbox");
      const container = brandElement.querySelector(".c-brand__container");
      const textElement = brandElement.querySelector(".c-brand__text");
      const figureElement = brandElement.querySelector(".c-brand__logotype");
      if (!svg || !container || !textElement) {
        return;
      }
      const img = brandElement.querySelector(".c-brand__logotype img");
      const initViewBoxManager = /* @__PURE__ */ __name(() => {
        new BrandViewBoxManager(svg, container, textElement, figureElement);
      }, "initViewBoxManager");
      if (!img || img.complete) {
        initViewBoxManager();
      } else {
        img.addEventListener("load", initViewBoxManager);
      }
    });
  });
}
__name(init, "init");
init();
//# sourceMappingURL=brand.js.map
