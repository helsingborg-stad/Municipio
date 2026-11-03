var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _ModifierPreview = class _ModifierPreview {
  constructor(el, format, selects, previewEl, outputEl, baseClass) {
    this.el = el;
    this.format = format;
    this.selects = selects;
    this.previewEl = previewEl;
    this.outputEl = outputEl;
    this.baseClass = baseClass;
  }
  el;
  format;
  selects;
  previewEl;
  outputEl;
  baseClass;
  composeClass(values) {
    let cls = this.format.replace(/^\./, "");
    for (const [key, value] of Object.entries(values)) {
      cls = cls.split(`{${key}}`).join(value);
    }
    return cls;
  }
  update() {
    const values = {};
    for (const select of this.selects) {
      const key = select.dataset.modifierKey;
      if (key) {
        values[key] = select.value;
      }
    }
    const cls = this.composeClass(values);
    this.previewEl.className = [this.baseClass, cls].filter(Boolean).join(" ");
    this.previewEl.setAttribute("style", "outline: 2px dashed currentColor; outline-offset: 4px; min-height: 5rem;");
    if (this.outputEl) {
      this.outputEl.textContent = cls ? `.${cls}` : "";
    }
  }
  init() {
    for (const select of this.selects) {
      select.addEventListener("change", () => this.update());
    }
    this.update();
  }
};
__name(_ModifierPreview, "ModifierPreview");
let ModifierPreview = _ModifierPreview;
function initModifierPreviews() {
  document.querySelectorAll("[data-modifier-preview]").forEach((el) => {
    const format = el.dataset.format;
    if (!format) return;
    const selects = Array.from(el.querySelectorAll("select[data-modifier-key]"));
    const previewEl = el.querySelector("[data-preview-element]");
    const outputEl = el.querySelector("[data-applied-class]");
    if (!previewEl) return;
    const baseClass = previewEl.dataset.baseClass ?? "";
    new ModifierPreview(el, format, selects, previewEl, outputEl, baseClass).init();
  });
}
__name(initModifierPreviews, "initModifierPreviews");
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initModifierPreviews, { once: true });
} else {
  initModifierPreviews();
}
export {
  initModifierPreviews
};
//# sourceMappingURL=modifierPreview.js.map
