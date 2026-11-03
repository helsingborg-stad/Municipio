var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _QuickLinksHeader = class _QuickLinksHeader {
  stickyQuickLinks;
  constructor() {
    this.stickyQuickLinks = document.querySelector("#quicklinks-header.c-header--sticky");
    this.init();
  }
  init() {
    if (!this.stickyQuickLinks) return;
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", () => this.observe(), { once: true });
    } else {
      this.observe();
    }
  }
  observe() {
    const observer = new IntersectionObserver(
      ([e]) => this.setClasses(e),
      { threshold: [1] }
    );
    if (this.stickyQuickLinks) {
      observer.observe(this.stickyQuickLinks);
    }
  }
  setClasses(event) {
    if (event.boundingClientRect.top <= 0) {
      event.target.classList.add("is-stuck");
    } else {
      event.target.classList.remove("is-stuck");
    }
  }
};
__name(_QuickLinksHeader, "QuickLinksHeader");
let QuickLinksHeader = _QuickLinksHeader;
export {
  QuickLinksHeader as default
};
//# sourceMappingURL=quickLinksHeader.js.map
