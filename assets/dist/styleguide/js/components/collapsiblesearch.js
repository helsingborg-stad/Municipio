var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
const EXPANDED_CLASS = "c-collapsiblesearch--expanded";
const PILL_CLASS = "c-button--pill";
const AUTO_FOCUS_ATTR = "data-js-collapsible-search-auto-focus";
const ROOT_SELECTOR = "[data-js-collapsible-search]";
const _CollapsibleSearch = class _CollapsibleSearch {
  root;
  trigger;
  panel;
  input;
  closeButton;
  triggerWasPill;
  onDocumentClickBound;
  onKeydownBound;
  onFocusOutBound;
  constructor(root) {
    this.root = root;
    if (_CollapsibleSearch.initializedRoots.has(root)) return;
    const trigger = root.querySelector("[data-js-collapsible-search-trigger]");
    const panel = root.querySelector(":scope > form");
    const input = root.querySelector("[data-js-collapsible-search-input]");
    const closeButton = root.querySelector("[data-js-collapsible-search-close]");
    if (!trigger || !panel || !input || !closeButton) return;
    _CollapsibleSearch.initializedRoots.add(root);
    this.trigger = trigger;
    this.panel = panel;
    this.input = input;
    this.closeButton = closeButton;
    this.triggerWasPill = this.trigger.classList.contains(PILL_CLASS);
    this.onDocumentClickBound = (event) => this.onDocumentClick(event);
    this.onKeydownBound = (event) => this.onKeydown(event);
    this.onFocusOutBound = (event) => this.onFocusOut(event);
    this.trigger.addEventListener("click", () => this.open());
    this.closeButton.addEventListener("click", () => this.close());
    if (this.isOpen()) {
      this.setTriggerPill(true);
      this.attachGlobalListeners();
    }
  }
  open() {
    if (this.isOpen()) return;
    this.root.classList.add(EXPANDED_CLASS);
    this.setTriggerPill(true);
    this.trigger.setAttribute("aria-expanded", "true");
    this.panel.removeAttribute("inert");
    this.panel.setAttribute("aria-hidden", "false");
    this.attachGlobalListeners();
    this.root.setAttribute(AUTO_FOCUS_ATTR, "");
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        if (!this.isOpen()) return;
        this.input.focus();
      });
    });
  }
  close(returnFocus = true) {
    if (!this.isOpen()) return;
    this.root.classList.remove(EXPANDED_CLASS);
    this.setTriggerPill(false);
    this.trigger.setAttribute("aria-expanded", "false");
    this.panel.setAttribute("inert", "");
    this.panel.setAttribute("aria-hidden", "true");
    this.root.removeAttribute(AUTO_FOCUS_ATTR);
    this.detachGlobalListeners();
    if (returnFocus) this.trigger.focus();
  }
  isOpen() {
    return this.root.classList.contains(EXPANDED_CLASS);
  }
  setTriggerPill(isPill) {
    if (!this.triggerWasPill) this.trigger.classList.toggle(PILL_CLASS, isPill);
  }
  onDocumentClick(event) {
    if (event.target instanceof Node && !this.root.contains(event.target)) this.close(false);
  }
  onKeydown(event) {
    if (event.key === "Tab") this.root.removeAttribute(AUTO_FOCUS_ATTR);
    if (event.key === "Escape") {
      event.preventDefault();
      this.close();
    }
  }
  onFocusOut(_event) {
    setTimeout(() => {
      if (!this.root.contains(document.activeElement)) this.close(false);
    }, 0);
  }
  attachGlobalListeners() {
    document.addEventListener("click", this.onDocumentClickBound);
    document.addEventListener("keydown", this.onKeydownBound);
    this.root.addEventListener("focusout", this.onFocusOutBound);
  }
  detachGlobalListeners() {
    document.removeEventListener("click", this.onDocumentClickBound);
    document.removeEventListener("keydown", this.onKeydownBound);
    this.root.removeEventListener("focusout", this.onFocusOutBound);
  }
};
__name(_CollapsibleSearch, "CollapsibleSearch");
__publicField(_CollapsibleSearch, "initializedRoots", /* @__PURE__ */ new WeakSet());
let CollapsibleSearch = _CollapsibleSearch;
function init() {
  document.querySelectorAll(ROOT_SELECTOR).forEach((root) => new CollapsibleSearch(root));
}
__name(init, "init");
if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
else init();
//# sourceMappingURL=collapsiblesearch.js.map
