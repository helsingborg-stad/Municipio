var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const CONTAINER = "[js-expand-container]";
const BUTTON = "[js-expand-button]";
const EXPANDED = "aria-expanded";
const CONTROLS = "aria-controls";
const HIDDEN = "aria-hidden";
const toggle = /* @__PURE__ */ __name((button, expanded, container) => {
  button.setAttribute(EXPANDED, String(expanded));
  const id = button.getAttribute(CONTROLS);
  const controls = id ? container.querySelector(`:scope > [id="${CSS.escape(id)}"]`) ?? document.getElementById(id) : null;
  if (!controls) {
    return expanded;
  }
  controls.setAttribute(HIDDEN, expanded ? "false" : "true");
  return expanded;
}, "toggle");
const toggleButton = /* @__PURE__ */ __name((button) => {
  const alreadyExpanded = button.getAttribute(EXPANDED) === "true";
  if (alreadyExpanded) {
    return;
  }
  const container = button.closest(CONTAINER);
  if (!container) {
    return;
  }
  toggle(button, true, container);
  container.querySelectorAll(BUTTON).forEach((sibling) => {
    if (sibling !== button) {
      toggle(sibling, false, container);
    }
  });
}, "toggleButton");
const initButtons = /* @__PURE__ */ __name((root = document) => {
  root.querySelectorAll(BUTTON).forEach((button) => {
    button.addEventListener("click", () => toggleButton(button));
  });
}, "initButtons");
function init() {
  const setup = /* @__PURE__ */ __name(() => {
    initButtons();
    new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node instanceof HTMLElement) {
            initButtons(node);
          }
        });
      });
    }).observe(document.body, { childList: true, subtree: true });
  }, "setup");
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", setup);
  } else {
    setup();
  }
}
__name(init, "init");
init();
//# sourceMappingURL=tabs.js.map
