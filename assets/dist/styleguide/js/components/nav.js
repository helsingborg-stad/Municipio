var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _Nav = class _Nav {
  targetItemSelector;
  constructor(menu) {
    this.targetItemSelector = ".c-nav__item.has-children.has-toggle";
    const selectorArray = [this.targetItemSelector, "> .c-nav__item-wrapper"];
    if (menu.classList.contains("c-nav--vertical")) {
      selectorArray.push(".c-nav__toggle");
    }
    if (menu.classList.contains("c-nav--extended-dropdown")) {
      selectorArray.unshift(":scope ul");
      selectorArray.push(".c-nav__toggle, :scope > li.has-toggle > .c-nav__item-wrapper");
    }
    const items = [...menu.querySelectorAll(selectorArray.join(" "))];
    if (items.length > 0) {
      this.setListeners(items, menu);
    }
  }
  setListeners(items, menu) {
    items.forEach((item) => {
      item.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (menu.classList.contains("c-nav--horizontal")) {
          this.closeSiblings(item.closest(this.targetItemSelector));
        }
        this.toggleChildren(item.closest(this.targetItemSelector));
      });
    });
  }
  closeSiblings(clickItem) {
    const items = this.getSiblings(clickItem);
    items.forEach((item) => {
      item.classList.remove("is-open");
    });
    return true;
  }
  toggleChildren(toggle) {
    if (!toggle.classList.contains("is-open")) {
      this.openChildren(toggle);
      return true;
    }
    this.closeChildren(toggle);
    return false;
  }
  openChildren(toggle) {
    toggle.classList.add("is-open");
    toggle.querySelector(".c-nav__toggle")?.setAttribute("aria-pressed", "true");
  }
  closeChildren(toggle) {
    toggle.classList.remove("is-open");
    toggle.querySelector(".c-nav__toggle")?.setAttribute("aria-pressed", "false");
  }
  getSiblings(elem) {
    const siblings = [];
    let sibling = elem.parentNode?.firstChild;
    while (sibling) {
      if (sibling.nodeType === Node.ELEMENT_NODE && sibling !== elem) {
        siblings.push(sibling);
      }
      sibling = sibling.nextSibling;
    }
    return siblings;
  }
};
__name(_Nav, "Nav");
let Nav = _Nav;
function enableHashLinkExpansion() {
  let closedByPointerDown = null;
  const getHashLinkToggle = /* @__PURE__ */ __name((target) => {
    if (!(target instanceof Element)) {
      return null;
    }
    const link = target.closest('.c-nav__item-wrapper > a[href="#"]');
    const itemWrapper = link?.closest(".c-nav__item-wrapper");
    const toggle = itemWrapper?.querySelector(":scope > .c-nav__toggle");
    if (!link?.closest(".c-nav") || !toggle) {
      return null;
    }
    return { link, toggle };
  }, "getHashLinkToggle");
  document.addEventListener("pointerdown", (event) => {
    closedByPointerDown = null;
    const hashLinkToggle = getHashLinkToggle(event.target);
    if (!hashLinkToggle || hashLinkToggle.toggle.getAttribute("aria-pressed") !== "true") {
      return;
    }
    hashLinkToggle.toggle.click();
    closedByPointerDown = hashLinkToggle.link;
  });
  document.addEventListener("click", (event) => {
    const hashLinkToggle = getHashLinkToggle(event.target);
    if (!hashLinkToggle) {
      return;
    }
    event.preventDefault();
    event.stopPropagation();
    if (closedByPointerDown === hashLinkToggle.link) {
      closedByPointerDown = null;
      return;
    }
    hashLinkToggle.toggle.click();
  });
  document.addEventListener("pointercancel", () => {
    closedByPointerDown = null;
  });
}
__name(enableHashLinkExpansion, "enableHashLinkExpansion");
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    enableHashLinkExpansion();
    const menus = [...document.querySelectorAll(".c-nav.c-nav--vertical.c-nav--depth-1,.c-nav.c-nav--extended-dropdown")];
    menus.forEach((menu) => {
      new Nav(menu);
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (mutation.type === "childList" && mutation.addedNodes.length > 0 && (mutation.target?.classList?.contains("c-nav__item") || mutation.target?.classList?.contains("c-nav__extended-content"))) {
            [...mutation.addedNodes].forEach((node) => {
              if (node.nodeType === Node.ELEMENT_NODE && node.classList?.contains("c-nav__child-container") && !node.querySelector(".c-nav.preloader")) {
                const element = node.classList.contains("c-nav") ? node : node.querySelector(".c-nav");
                if (element) {
                  new Nav(element);
                }
              }
            });
          }
        });
      });
      observer.observe(menu, { childList: true, subtree: true });
    });
  });
}
__name(init, "init");
init();
//# sourceMappingURL=nav.js.map
