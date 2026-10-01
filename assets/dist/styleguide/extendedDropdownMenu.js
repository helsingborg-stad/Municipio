var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _ExtendedDropdownMenu = class _ExtendedDropdownMenu {
  constructor(parentContainer, titleElement, triggerElement) {
    this.parentContainer = parentContainer;
    this.titleElement = titleElement;
    this.triggerElement = triggerElement;
  }
  parentContainer;
  titleElement;
  triggerElement;
  MAX_ROWS_PER_COLUMN = 5;
  NAV_ITEM_PADDING = 48;
  MIN_EDGE_PADDING = 32;
  triggerElementPosition;
  resizeTimeout = 0;
  calculatedLeftPosition = null;
  cachedResults = {};
  init() {
    requestAnimationFrame(() => {
      this.setElementPositionsAndSizes();
    });
    this.setupResizeListener();
    this.correctColumnBorders();
  }
  /**
   * Sets up a listener for window resize events to adjust the position and size of the dropdown menu.
   */
  setupResizeListener() {
    window.addEventListener("resize", () => {
      clearTimeout(this.resizeTimeout);
      this.resizeTimeout = window.setTimeout(() => {
        this.setElementPositionsAndSizes();
      }, 200);
    });
  }
  /**
   * Sets the positions and sizes of the dropdown menu elements based on the trigger element's position.
   */
  setElementPositionsAndSizes(ignoreCache = false) {
    this.triggerElementPosition = this.triggerElement.getBoundingClientRect();
    if (this.cachedResults[this.triggerElementPosition.left] && !ignoreCache) {
      this.parentContainer.style.left = `${this.cachedResults[this.triggerElementPosition.left]}px`;
      return;
    }
    const titleElementSize = this.titleElement.offsetWidth;
    const parentContainerSize = this.parentContainer.offsetWidth;
    const maxLeft = window.innerWidth - parentContainerSize + this.NAV_ITEM_PADDING - this.MIN_EDGE_PADDING;
    const calculatedLeftPosition = this.triggerElementPosition.left - titleElementSize - this.NAV_ITEM_PADDING;
    this.calculatedLeftPosition = calculatedLeftPosition > maxLeft ? maxLeft : calculatedLeftPosition < this.MIN_EDGE_PADDING ? this.MIN_EDGE_PADDING : calculatedLeftPosition;
    this.cachedResults[this.triggerElementPosition.left] = this.calculatedLeftPosition;
    this.parentContainer.style.left = `${this.calculatedLeftPosition}px`;
  }
  /**
   * Adjusts the grid row spans of the last item in the dropdown menu to ensure proper border alignment.
   */
  correctColumnBorders() {
    const items = this.parentContainer.querySelectorAll("[data-js-extended-dropdown-child-menu] > .c-nav__item");
    if (!items.length) return;
    const totalItems = items.length;
    const lastItem = items[totalItems - 1];
    const itemsInLastColumn = totalItems % this.MAX_ROWS_PER_COLUMN || this.MAX_ROWS_PER_COLUMN;
    const emptySlots = this.MAX_ROWS_PER_COLUMN - itemsInLastColumn;
    if (emptySlots > 0) {
      lastItem.style.gridRow = `span ${emptySlots + 1}`;
    }
  }
};
__name(_ExtendedDropdownMenu, "ExtendedDropdownMenu");
let ExtendedDropdownMenu = _ExtendedDropdownMenu;
function initializeExtendedDropdownMenu() {
  document.querySelectorAll("[data-js-extended-dropdown-content]").forEach((extendedDropdownMenu) => {
    const openElement = extendedDropdownMenu.closest(".c-nav__item")?.querySelector(".c-nav__item-wrapper");
    const titleElement = extendedDropdownMenu.querySelector("[data-js-extended-dropdown-title]");
    const asyncChildContainer = extendedDropdownMenu.querySelector("[data-js-async-children]");
    if (!openElement || !titleElement) {
      console.error("ExtendedDropdownMenu: Sibling element with class .c-nav__item-wrapper not found.");
      return;
    }
    const extendedDropdownMenuInstance = new ExtendedDropdownMenu(
      extendedDropdownMenu,
      titleElement,
      openElement
    );
    extendedDropdownMenuInstance.init();
    if (asyncChildContainer) {
      addObserverForAsyncContent(asyncChildContainer, extendedDropdownMenuInstance);
    }
  });
}
__name(initializeExtendedDropdownMenu, "initializeExtendedDropdownMenu");
function addObserverForAsyncContent(targetNode, extendedDropdownMenuInstance) {
  if (!targetNode.parentElement) {
    console.error("ExtendedDropdownMenu: asyncChildContainer has no parent element.");
    return;
  }
  const observer = new MutationObserver((mutations) => {
    for (const mutation of mutations) {
      for (const removedNode of mutation.removedNodes) {
        if (removedNode === targetNode) {
          extendedDropdownMenuInstance.correctColumnBorders();
          extendedDropdownMenuInstance.setElementPositionsAndSizes(true);
          observer.disconnect();
        }
      }
    }
  });
  observer.observe(targetNode.parentElement, { childList: true, subtree: true });
}
__name(addObserverForAsyncContent, "addObserverForAsyncContent");
export {
  initializeExtendedDropdownMenu
};
//# sourceMappingURL=extendedDropdownMenu.js.map
