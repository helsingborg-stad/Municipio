var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _ResizeByChildren = class _ResizeByChildren {
  constructor() {
    this.RESIZE = "js-resize-by-children";
    const resizeContainer = document.querySelectorAll(`[${this.RESIZE}]`);
    if (resizeContainer) {
      resizeContainer.forEach((item) => {
        item.classList.add("u-display--block");
        const currentChilds = item.querySelectorAll("li > a");
        const widthStack = [];
        currentChilds.forEach((child) => {
          widthStack.push(child.getBoundingClientRect().width);
        });
        const maxSize = Math.round(Math.max.apply(null, widthStack));
        if (item.getBoundingClientRect().width > maxSize) {
          item.setAttribute("style", "width:" + maxSize + "px !important;");
          item.dispatchEvent(new Event("resizeByChildren"));
        }
        item.classList.remove("u-display--block");
        item.classList.add(item.classList[0] + "--calculated");
      });
    }
  }
};
__name(_ResizeByChildren, "ResizeByChildren");
let ResizeByChildren = _ResizeByChildren;
export {
  ResizeByChildren as default
};
//# sourceMappingURL=resizeByChildren.js.map
