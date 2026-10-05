var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _MegaMenu = class _MegaMenu {
  constructor(megaMenu, triggers) {
    this.megaMenu = megaMenu;
    this.triggers = triggers;
    this.setupListeners();
  }
  megaMenu;
  triggers;
  isOpen = false;
  /**
   * Sets up event listeners for the mega menu.
   */
  setupListeners() {
    this.triggers.forEach((trigger) => {
      trigger.addEventListener("click", () => {
        this.triggerClickHandler(trigger);
      });
    });
    document.addEventListener("click", (e) => {
      this.handleClickOutside(e);
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        this.close();
      }
    });
  }
  /**
   * Handles the click event on the trigger element.
   * Toggles the visibility of the mega menu and updates the aria-hidden attribute accordingly.
   * Also updates the state of the triggers based on the visibility of the mega menu.
   * 
   * @param trigger - The trigger element that was clicked.
   */
  triggerClickHandler(trigger) {
    this.megaMenu.classList.toggle("u-display--none");
    this.isOpen = this.megaMenu.classList.contains("u-display--none") ? false : true;
    this.megaMenu.setAttribute("aria-hidden", this.isOpen.toString());
    this.changeTriggersStates();
  }
  /**
   * Changes the states of the triggers based on the specified isOpen value.
   */
  changeTriggersStates() {
    this.triggers.forEach((trigger) => {
      trigger.setAttribute("aria-pressed", this.isOpen.toString());
    });
  }
  /**
   * Handles the click event outside of the mega menu.
   * If the click is not inside the menu or on any of the triggers, it closes the menu.
   * @param event - The pointer event object.
   */
  handleClickOutside(event) {
    if (!this.isOpen) return;
    const target = event.target;
    const isClickInsideMenu = this.megaMenu.contains(target);
    const isClickOnTrigger = Array.from(this.triggers).some((trigger) => trigger.contains(target));
    if (!isClickInsideMenu && !isClickOnTrigger) {
      this.close();
    }
  }
  /**
   * Closes the mega menu.
   * 
   * This method adds the 'u-display--none' class to the mega menu element, sets the 'aria-hidden' attribute to 'true',
   * and changes the triggers' states to false.
   */
  close() {
    if (!this.isOpen) return;
    this.isOpen = false;
    this.megaMenu.classList.add("u-display--none");
    this.megaMenu.setAttribute("aria-hidden", "true");
    this.changeTriggersStates();
  }
};
__name(_MegaMenu, "MegaMenu");
let MegaMenu = _MegaMenu;
function initializeMegaMenus() {
  document.querySelectorAll(".c-megamenu").forEach((megaMenu) => {
    const id = megaMenu.id;
    const triggers = document.querySelectorAll(`[data-js-mega-menu-trigger="${id}"]`);
    if (triggers.length <= 0) {
      return;
    }
    new MegaMenu(megaMenu, triggers);
  });
}
__name(initializeMegaMenus, "initializeMegaMenus");
if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initializeMegaMenus);
else initializeMegaMenus();
//# sourceMappingURL=megamenu.js.map
