var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _DynamicSidebar = class _DynamicSidebar {
  constructor() {
    this.dynamicSidebars = document.querySelectorAll(".c-sidebar[endpoint-children]");
    if (this.dynamicSidebars) {
      this.endpoints = {};
      this.pageId = document.getElementsByTagName("body")[0].getAttribute("data-js-page-id");
    }
  }
  /**
   * Finds all sidebars and applies appropriate classes and attributes on load
   * @return {void}
   */
  applySidebar() {
    if (this.dynamicSidebars) {
      this.dynamicSidebars.forEach((sidebar) => {
        this.endpoints.children = sidebar.getAttribute("endpoint-children");
        this.addTriggers(sidebar.querySelectorAll(".c-sidebar__toggle"));
        sidebar.querySelectorAll(".c-sidebar__subcontainer").forEach((subContainer) => {
          if (subContainer.childElementCount === 0) {
            subContainer.parentElement.removeChild(subContainer);
          }
        });
      });
    }
  }
  getChildren(parentId) {
    return fetch(`${this.endpoints.children}?pageId=${parentId}`).then((response) => {
      return response.json();
    }).then((data) => {
      return data;
    });
  }
  appendChildren(parentID) {
    return this.getChildren(parentID).then((children) => {
      const subContainer = document.createElement("div");
      subContainer.setAttribute("subContainerID", parentID);
      subContainer.classList.add("c-sidebar__subcontainer");
      children.forEach((child) => {
        const childItem = document.createElement("div");
        childItem.classList.add("c-sidebar__item");
        const link = document.createElement("a");
        link.href = child.href;
        link.classList.add("c-sidebar__link");
        link.text = child.label;
        link.setAttribute("item-active", "false");
        link.id = `${child.id}`;
        childItem.appendChild(link);
        if (child.children) {
          const bar = document.createElement("div");
          bar.classList.add("bar");
          const toggle = document.createElement("div");
          toggle.classList.add("c-sidebar__toggle");
          toggle.appendChild(bar);
          toggle.appendChild(bar.cloneNode(true));
          toggle.setAttribute("aria-label", child.id);
          childItem.appendChild(toggle);
        }
        subContainer.appendChild(childItem);
      });
      subContainer.classList.add("c-sidebar__item--is-expanded");
      return subContainer;
    });
  }
  /**
   * Adds listeners to buttons
   * @param {Object} sb The sidebar
  */
  addTriggers(toggleTriggers) {
    toggleTriggers.forEach((trigger) => {
      const parentId = trigger.getAttribute("aria-label");
      const parent = trigger.parentElement;
      trigger.addEventListener("click", () => {
        const ariaPressed = trigger.getAttribute("aria-pressed") === "true" ? "false" : "true";
        trigger.setAttribute("aria-pressed", ariaPressed);
        if (!trigger.getAttribute("aria-loaded")) {
          trigger.setAttribute("aria-loaded", "true");
          this.appendChildren(parentId).then((child) => {
            parent.appendChild(child);
            const parentSubcontainer2 = parent.querySelector(".c-sidebar__subcontainer");
            this.addTriggers(parentSubcontainer2.querySelectorAll(".c-sidebar__toggle"));
          });
        }
        const parentSubcontainer = parent.querySelector(".c-sidebar__subcontainer");
        if (parentSubcontainer) {
          parentSubcontainer.classList.toggle("c-sidebar__item--is-expanded");
        }
      });
    });
  }
};
__name(_DynamicSidebar, "DynamicSidebar");
let DynamicSidebar = _DynamicSidebar;
export {
  DynamicSidebar as default
};
//# sourceMappingURL=dynamicSidebar.js.map
