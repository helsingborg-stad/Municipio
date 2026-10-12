var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
import { _ as __vitePreload } from "../preload-helper.js";
import { i as isLocalStoragePersistenceEnabled, T as TokenOverrideLocalStorageStore } from "../designBuilderStorageOptIn.js";
if (isLocalStoragePersistenceEnabled(document.documentElement)) {
  const overrides = new TokenOverrideLocalStorageStore().load();
  for (const [prop, value] of Object.entries(overrides)) {
    document.documentElement.style.setProperty(prop, value);
  }
}
const _ButtonToggleContent = class _ButtonToggleContent {
  labelAttr;
  iconAttr;
  toggles;
  constructor() {
    this.labelAttr = "data-toggle-label";
    this.iconAttr = "data-toggle-icon";
    this.toggles = [];
    this.init();
    this.setupMutationObserver();
  }
  init() {
    this.toggles = Array.from(document.querySelectorAll(`[${this.labelAttr}], [${this.iconAttr}]`));
    this.toggles.forEach((toggle) => {
      toggle.addEventListener(
        "click",
        (event) => this.handleToggleClick(toggle, event)
      );
    });
  }
  handleToggleClick = /* @__PURE__ */ __name((toggle, event) => {
    const labelAttrVal = toggle.hasAttribute(this.labelAttr) ? toggle.getAttribute(this.labelAttr) : null;
    const iconAttrVal = toggle.hasAttribute(this.iconAttr) ? toggle.getAttribute(this.iconAttr) : null;
    if (labelAttrVal) {
      const labelEl = toggle.querySelector('[class*="c-button__label-text"]');
      if (labelEl) {
        const currentLabel = labelEl.innerHTML.trim();
        labelEl.innerHTML = labelAttrVal;
        toggle.setAttribute(this.labelAttr, currentLabel);
        toggle.setAttribute("aria-label", labelAttrVal);
      }
    }
    if (iconAttrVal) {
      const iconEl = toggle.querySelector('[class*="c-icon"] > span');
      if (iconEl) {
        const currentIcon = iconEl.innerHTML.trim();
        iconEl.innerHTML = iconAttrVal;
        toggle.setAttribute(this.iconAttr, currentIcon);
      }
    }
  }, "handleToggleClick");
  setupMutationObserver() {
    const observer = new MutationObserver((mutationsList) => {
      for (const mutation of mutationsList) {
        if (mutation.type === "childList") {
          const addedNodes = Array.from(mutation.addedNodes);
          const toggles = addedNodes.reduce((acc, node) => {
            if (node instanceof HTMLElement) {
              return [...acc, ...Array.from(node.querySelectorAll(`[${this.labelAttr}], [${this.iconAttr}]`))];
            } else {
              return acc;
            }
          }, []);
          toggles.forEach((toggle) => {
            toggle.addEventListener(
              "click",
              (event) => this.handleToggleClick(toggle, event)
            );
          });
          this.toggles = [...this.toggles, ...toggles];
        }
      }
    });
    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  }
};
__name(_ButtonToggleContent, "ButtonToggleContent");
let ButtonToggleContent = _ButtonToggleContent;
var ClassToggleAttr = /* @__PURE__ */ ((ClassToggleAttr2) => {
  ClassToggleAttr2["TRIGGER"] = "data-js-toggle-trigger";
  ClassToggleAttr2["ITEM"] = "data-js-toggle-item";
  ClassToggleAttr2["CLASS"] = "data-js-toggle-class";
  ClassToggleAttr2["GROUP"] = "data-js-toggle-group";
  ClassToggleAttr2["TRIGGER_DEPRECATED"] = "js-toggle-trigger";
  ClassToggleAttr2["ITEM_DEPRECATED"] = "js-toggle-item";
  ClassToggleAttr2["CLASS_DEPRECATED"] = "js-toggle-class";
  ClassToggleAttr2["GROUP_DEPRECATED"] = "js-toggle-group";
  return ClassToggleAttr2;
})(ClassToggleAttr || {});
const _ClassToggle = class _ClassToggle {
  /**
   * Creates an instance of the ClassToggle class.
   * @param trigger The HTML element that triggers the toggle.
   * @param id The unique identifier for the toggle.
   * @param groupId The group identifier for the toggle, if any.
   */
  constructor(trigger, id, groupId = null) {
    this.trigger = trigger;
    this.id = id;
    this.groupId = groupId;
    if (this.groupId) {
      _ClassToggle.groups[this.groupId] = _ClassToggle.groups[this.groupId] || {};
      _ClassToggle.groups[this.groupId][this.id] = this;
    }
    this.setToggleListener();
  }
  trigger;
  id;
  groupId;
  /**
   * Sets the event listener on the trigger element to handle toggle actions.
   */
  setToggleListener() {
    this.trigger.addEventListener("click", (event) => {
      this.toggle();
      if (this.groupId) {
        this.toggleGroupMembers();
      }
    });
  }
  /**
   * Toggles the class on all associated toggle items.
   */
  toggle() {
    this.getToggleItems().forEach((item) => {
      const classAttr = item.getAttribute(ClassToggleAttr.CLASS) || item.getAttribute(ClassToggleAttr.CLASS_DEPRECATED) || "is-active";
      classAttr.split(/\s+/).forEach((cls) => {
        if (cls) item.classList.toggle(cls);
      });
    });
  }
  /**
   * Gets all toggle items associated with this toggle instance.
   * @returns A NodeList of all HTML elements associated with this toggle instance.
   */
  getToggleItems() {
    return document.querySelectorAll(`[${ClassToggleAttr.ITEM}="${this.id}"], [${ClassToggleAttr.ITEM_DEPRECATED}="${this.id}"]`);
  }
  toggleGroupMembers() {
    const group = _ClassToggle.groups[this.groupId];
    for (const memberId in group) {
      if (memberId !== this.id) {
        group[memberId].close();
      }
    }
  }
  /**
   * Closes the toggle by removing the active class from all associated toggle items.
   */
  close() {
    this.trigger.setAttribute("aria-pressed", "false");
    this.getToggleItems().forEach((item) => {
      const classAttr = item.getAttribute(ClassToggleAttr.CLASS) || item.getAttribute(ClassToggleAttr.CLASS_DEPRECATED) || "is-active";
      classAttr.split(/\s+/).forEach((cls) => {
        if (cls) item.classList.remove(cls);
      });
    });
  }
};
__name(_ClassToggle, "ClassToggle");
__publicField(_ClassToggle, "groups", {});
let ClassToggle = _ClassToggle;
const _ClassToggleInitializer = class _ClassToggleInitializer {
  /**
   * Initializes ClassToggle instances for all triggers found in the document and sets up a MutationObserver to handle dynamically added triggers.
   */
  init() {
    this.findTriggers(document.documentElement || document.body).forEach((trigger) => {
      this.initTrigger(trigger);
    });
    this.observeAddedNodes();
  }
  /**
   * Observes the document for added nodes and initializes ClassToggle instances for any new triggers found.
   */
  observeAddedNodes() {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === "childList") {
          mutation.addedNodes.forEach((node) => {
            if (node instanceof HTMLElement) {
              const triggers = [...this.findTriggers(node)];
              if (node.hasAttribute(ClassToggleAttr.TRIGGER) || node.hasAttribute(ClassToggleAttr.TRIGGER_DEPRECATED)) {
                triggers.push(node);
              }
              triggers.forEach((trigger) => {
                this.initTrigger(trigger);
              });
            }
          });
        }
      });
    });
    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  }
  /**
   * Finds all trigger elements within the specified root element.
   * @param element The root element to search within.
   * @returns A NodeList of all trigger elements found.
   */
  findTriggers(element) {
    return element.querySelectorAll(`[${ClassToggleAttr.TRIGGER}], [${ClassToggleAttr.TRIGGER_DEPRECATED}]`);
  }
  /**
   * Initializes a ClassToggle instance for the specified trigger element.
   * @param trigger The trigger element to initialize.
   */
  initTrigger(trigger) {
    const triggerId = trigger.getAttribute(ClassToggleAttr.TRIGGER) || trigger.getAttribute(ClassToggleAttr.TRIGGER_DEPRECATED);
    const groupId = trigger.getAttribute(ClassToggleAttr.GROUP) || trigger.getAttribute(ClassToggleAttr.GROUP_DEPRECATED);
    if (triggerId) {
      new ClassToggle(trigger, triggerId, groupId);
    }
  }
};
__name(_ClassToggleInitializer, "ClassToggleInitializer");
let ClassToggleInitializer = _ClassToggleInitializer;
const _Notification = class _Notification {
  setup() {
    const containers = document.getElementsByClassName("c-notification__container");
    if (containers && containers.length > 0) {
      containers.forEach((container) => {
        const direction = container.getAttribute("direction");
        const directionClass = `c-notification__container--${direction}`;
        container.classList.add(directionClass);
        this.setOnClickClose(container);
      });
    }
  }
  removeFirst(target) {
    const notifications = target.querySelectorAll(".c-notification");
    const maxAmount = target.getAttribute("maxamount");
    if (notifications.length > maxAmount) {
      notifications[0].outerHTML = "";
    }
  }
  setOnClickClose(targetNode) {
    let count = 0;
    const observerOptions = {
      childList: true
    };
    const observer = new MutationObserver((event) => {
      count++;
      this.removeFirst(targetNode);
      event.forEach((record) => {
        record.addedNodes.forEach((node) => {
          if (node.classList.contains("c-notification")) {
            this.setAutoHideDuration(node);
            node.addEventListener("click", () => {
              node.classList.add(`c-notification--dying--${count}`);
              node.outerHTML = "";
            });
          }
        });
      });
    });
    observer.observe(targetNode, observerOptions);
  }
  setAutoHideDuration(notification) {
    const autoHideDuration = notification.getAttribute("autoHideDuration");
    setTimeout(() => {
      notification.outerHTML = "";
    }, autoHideDuration);
  }
};
__name(_Notification, "Notification");
let Notification = _Notification;
const setScrollbarCSS = /* @__PURE__ */ __name(() => {
  const body = document.querySelector("body");
  if (!body) return;
  const viewportWidth = window.innerWidth - document.documentElement.clientWidth;
  const scrollbar = Math.min(viewportWidth, 15);
  body.setAttribute("style", `--scrollbar: ${scrollbar}px`);
}, "setScrollbarCSS");
document.addEventListener("touchstart", handleTouchStart, false);
document.addEventListener("touchmove", handleTouchMove, false);
const swipeUp = new CustomEvent("swipeUp", {
  bubbles: true
});
const swipeDown = new CustomEvent("swipeDown", {
  bubbles: true
});
const swipeRight = new CustomEvent("swipeRight", {
  bubbles: true
});
const swipeLeft = new CustomEvent("swipeLeft", {
  bubbles: true
});
var xDown = null;
var yDown = null;
function getTouches(evt) {
  return evt.touches || // browser API
  evt.originalEvent.touches;
}
__name(getTouches, "getTouches");
function handleTouchStart(evt) {
  const firstTouch = getTouches(evt)[0];
  xDown = firstTouch.clientX;
  yDown = firstTouch.clientY;
}
__name(handleTouchStart, "handleTouchStart");
function handleTouchMove(evt) {
  if (!xDown || !yDown) {
    return;
  }
  var xUp = evt.touches[0].clientX;
  var yUp = evt.touches[0].clientY;
  var xDiff = xDown - xUp;
  var yDiff = yDown - yUp;
  if (Math.abs(xDiff) > Math.abs(yDiff)) {
    if (xDiff > 0) {
      evt.target.dispatchEvent(swipeLeft);
    } else {
      evt.target.dispatchEvent(swipeRight);
    }
  } else {
    if (yDiff > 0) {
      evt.target.dispatchEvent(swipeUp);
    } else {
      evt.target.dispatchEvent(swipeDown);
    }
  }
  xDown = null;
  yDown = null;
}
__name(handleTouchMove, "handleTouchMove");
const _AriaPressedToggler = class _AriaPressedToggler {
  constructor() {
    this.init();
    this.observe();
  }
  init() {
    const initElements = document.querySelectorAll("[aria-pressed='true'], [aria-pressed='false']");
    if (initElements.length) {
      initElements.forEach((element) => {
        this.applyOnClickEvent(element);
      });
    }
  }
  applyOnClickEvent(element) {
    element.addEventListener("click", () => {
      this.toggleAriaPressed(element);
    });
  }
  toggleAriaPressed(el) {
    const currentState = el.getAttribute("aria-pressed");
    const newState = currentState === "true" ? "false" : "true";
    el.setAttribute("aria-pressed", newState);
  }
  observe() {
    const container = document.documentElement || document.body;
    const observerOptions = {
      childList: true,
      subtree: true,
      attributeFilter: ["aria-pressed"]
    };
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === "childList") {
          mutation.addedNodes.forEach((node) => {
            if (node instanceof HTMLElement && node.getAttribute("aria-pressed")) {
              this.applyOnClickEvent(node);
            }
          });
        }
      });
    });
    observer.observe(container, observerOptions);
  }
};
__name(_AriaPressedToggler, "AriaPressedToggler");
let AriaPressedToggler = _AriaPressedToggler;
const _ClickAway = class _ClickAway {
  constructor(element, classesToRemove, removePressed) {
    this.element = element;
    this.classesToRemove = classesToRemove;
    this.removePressed = removePressed;
  }
  element;
  classesToRemove;
  removePressed;
  handleClickAway(target) {
    if (this.element.contains(target)) return;
    this.removeClasses();
  }
  removeClasses() {
    this.classesToRemove.forEach((className) => {
      if (this.element.classList.contains(className)) {
        this.element.classList.remove(className);
      }
    });
    if (this.element.hasAttribute("aria-pressed")) {
      this.element.setAttribute("aria-pressed", "false");
    }
    this.removePressed.forEach((element) => {
      element.setAttribute("aria-pressed", "false");
    });
  }
};
__name(_ClickAway, "ClickAway");
let ClickAway = _ClickAway;
function initializeClickAways() {
  const clickAwayInstances = [];
  document.querySelectorAll("[data-js-click-away]").forEach((element) => {
    const classesToRemove = element.getAttribute("data-js-click-away")?.split(",").map((className) => className.trim());
    if (!classesToRemove || classesToRemove.length <= 0) return;
    const removePressed = element.querySelectorAll("[aria-pressed][data-js-click-away-remove-pressed]");
    clickAwayInstances.push(new ClickAway(element, classesToRemove, removePressed));
  });
  if (clickAwayInstances.length <= 0) return;
  document.addEventListener("click", (e) => {
    const target = e.target;
    if (!target) return;
    clickAwayInstances.forEach((clickAwayInstance) => {
      clickAwayInstance.handleClickAway(target);
    });
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      clickAwayInstances.forEach((clickAwayInstance) => {
        clickAwayInstance.removeClasses();
      });
    }
  });
}
__name(initializeClickAways, "initializeClickAways");
const _Compressed = class _Compressed {
  element = null;
  parentElement = null;
  siblingElements = [];
  compressedAmount = null;
  className = null;
  toggle = false;
  constructor(element) {
    this.element = element;
    this.parentElement = element.parentElement;
    this.compressedAmount = this.element.hasAttribute("data-js-compressed") ? parseInt(this.element.getAttribute("data-js-compressed") || "0", 10) : 0;
    this.siblingElements = [...this.parentElement?.children || []].filter((child) => child !== this.element).slice(this.compressedAmount);
    this.className = this.element?.getAttribute("data-js-compressed-class");
    this.toggle = this.element.hasAttribute("data-js-compressed-toggle");
    if (!this.className) {
      this.siblingElements.forEach((sibling) => {
        sibling.style.display = "none";
      });
    }
    this.init();
  }
  init() {
    if (!this.element || !this.parentElement || this.siblingElements.length <= 0) return;
    this.element.setAttribute("is-compressed", "");
    this.element.style.cursor = "pointer";
    this.clickListener();
  }
  clickListener() {
    this.element?.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      this.handleClick();
      if (!this.toggle) this.element?.remove();
    });
  }
  handleClick() {
    if (this.element?.hasAttribute("is-compressed")) {
      this.element.removeAttribute("is-compressed");
      this.toggleSiblingElements(false);
    } else {
      this.element?.setAttribute("is-compressed", "");
      this.toggleSiblingElements(true);
    }
  }
  toggleSiblingElements(isCompressed) {
    this.siblingElements?.forEach((sibling) => {
      if (this.className) {
        isCompressed ? sibling.classList.add(this.className) : sibling.classList.remove(this.className);
      } else {
        sibling.style.display = isCompressed ? "none" : "";
      }
    });
  }
};
__name(_Compressed, "Compressed");
let Compressed = _Compressed;
function initializeElements(elements) {
  elements.forEach((element) => {
    if (element.hasAttribute("compressed-was-initialized")) {
      return;
    }
    element.setAttribute("compressed-was-initialized", "");
    new Compressed(element);
  });
}
__name(initializeElements, "initializeElements");
function initializeCompressed() {
  initializeElements([...document.querySelectorAll("[data-js-compressed]")]);
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (!(node instanceof HTMLElement)) {
          return;
        }
        if (node.hasAttribute("data-js-compressed")) {
          initializeElements([node]);
        } else {
          initializeElements([...node.querySelectorAll("[data-js-compressed]")]);
        }
      });
    });
  });
  observer.observe(document.body, {
    childList: true,
    subtree: true
  });
}
__name(initializeCompressed, "initializeCompressed");
const _GoogleTranslate = class _GoogleTranslate {
  element;
  originalLink;
  constructor(element) {
    this.element = element;
    this.originalLink = element.getAttribute("data-js-original-link");
    if (this.originalLink && this.originalLink !== "") {
      this.element.href = this.originalLink;
    }
  }
};
__name(_GoogleTranslate, "GoogleTranslate");
let GoogleTranslate = _GoogleTranslate;
function runCondition(htmlElement) {
  if (htmlElement.classList.contains("translated-ltr") || htmlElement.classList.contains("translated-rtl")) {
    [...document.querySelectorAll("a[data-js-original-link]")].forEach((element) => {
      new GoogleTranslate(element);
    });
  }
}
__name(runCondition, "runCondition");
function initializeGoogleTranslate() {
  const htmlElement = document.documentElement;
  runCondition(htmlElement);
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.attributeName === "class") {
        runCondition(htmlElement);
      }
    });
  });
  const config = { attributes: true, attributeFilter: ["class"] };
  observer.observe(htmlElement, config);
}
__name(initializeGoogleTranslate, "initializeGoogleTranslate");
const moveElement = /* @__PURE__ */ __name((element, target) => {
  target.appendChild(element);
}, "moveElement");
const moveToSelector = "[data-move-to]";
const moveToAttributeName = "data-move-to";
const canMoveTo = /* @__PURE__ */ __name((moveTo) => {
  return document.querySelector(moveToSelector) !== null;
}, "canMoveTo");
const moveElements = /* @__PURE__ */ __name((moveElement2) => {
  const elements = document.querySelectorAll(moveToSelector);
  elements.forEach((element) => {
    const moveToSelector2 = element.getAttribute(moveToAttributeName);
    if (canMoveTo()) {
      moveElement2(element, document.querySelector(moveToSelector2));
      element.removeAttribute(moveToAttributeName);
    }
  });
}, "moveElements");
const _ResizeMediaQuery = class _ResizeMediaQuery {
  prefixName;
  element;
  defaultBreakpoints;
  resizeClass;
  constructor(element) {
    this.prefixName = "--size-";
    this.defaultBreakpoints = { xs: 384, sm: 576, md: 768, lg: 960, xl: 1200 };
    this.element = element;
    this.resizeClass = null;
    const resizeClass = element.getAttribute("data-observe-resizes");
    if (resizeClass !== "") {
      this.resizeClass = resizeClass;
    }
    if ("ResizeObserver" in self) {
      this.resizeObserver();
    }
  }
  resizeObserver() {
    new ResizeObserver((entries) => {
      entries.forEach((entry) => {
        this.handleResize(entry);
      });
    }).observe(this.element);
  }
  handleResize(entry) {
    const element = entry.target;
    const breakpoints = element.dataset.breakpoints ? JSON.parse(element.dataset.breakpoints) : this.defaultBreakpoints;
    Object.keys(breakpoints).forEach((breakpoint) => {
      const minWidth = breakpoints[breakpoint];
      if (entry.contentRect.width >= minWidth) {
        element.classList.add((this.resizeClass ?? element.classList[0]) + this.prefixName + breakpoint);
      } else {
        element.classList.remove((this.resizeClass ?? element.classList[0]) + this.prefixName + breakpoint);
      }
    });
  }
};
__name(_ResizeMediaQuery, "ResizeMediaQuery");
let ResizeMediaQuery = _ResizeMediaQuery;
function initializeResizeMediaQuery() {
  const elements = document.querySelectorAll("[data-observe-resizes]");
  if (elements.length) {
    elements.forEach((element) => {
      new ResizeMediaQuery(element);
    });
  }
  const observer = new MutationObserver((mutationsList) => {
    mutationsList.forEach((mutation) => {
      const elements2 = mutation.addedNodes;
      if (elements2?.length) {
        elements2.forEach((element) => {
          if (element?.nodeType === Node.ELEMENT_NODE && element.matches("[data-observe-resizes]")) {
            new ResizeMediaQuery(element);
          }
        });
      }
    });
  });
  observer.observe(document.body, {
    childList: true,
    subtree: true
  });
}
__name(initializeResizeMediaQuery, "initializeResizeMediaQuery");
const _SimulateClick = class _SimulateClick {
  simulateClickAttr = "data-simulate-click";
  triggers = [];
  constructor() {
    this.init();
    this.observe();
  }
  init() {
    const initElements = document.querySelectorAll(`[${this.simulateClickAttr}]`);
    if (initElements.length) {
      initElements.forEach((element) => {
        const target = element.getAttribute(this.simulateClickAttr) ?? "";
        if (document.querySelectorAll(target).length) {
          this.applyOnClickEvent(element, target);
        }
      });
    }
  }
  applyOnClickEvent(element, target) {
    if (this.triggers.includes(element)) return;
    this.triggers.push(element);
    element.addEventListener("click", (event) => {
      if (event.target instanceof HTMLElement) {
        const targetElements = document.querySelectorAll(target);
        targetElements.forEach((targetElement) => {
          targetElement.click();
        });
      }
    });
  }
  observe() {
    const container = document.documentElement || document.body;
    const observerOptions = {
      childList: true,
      subtree: true,
      attributeFilter: [this.simulateClickAttr]
    };
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === "childList") {
          mutation.addedNodes.forEach((node) => {
            if (node instanceof HTMLElement && node.getAttribute(this.simulateClickAttr)) {
              const target = node.getAttribute(this.simulateClickAttr) ?? "";
              this.applyOnClickEvent(node, target);
            }
          });
        }
      });
    });
    observer.observe(container, observerOptions);
  }
};
__name(_SimulateClick, "SimulateClick");
let SimulateClick = _SimulateClick;
const NotificationInstance = new Notification();
function loadOptionalFeatures() {
  if (document.querySelector("[data-js-device-detect]")) {
    void __vitePreload(async () => {
      const { DeviceDetect } = await import("../deviceDetect.js");
      return { DeviceDetect };
    }, true ? [] : void 0).then(({ DeviceDetect }) => new DeviceDetect());
  }
  if (document.querySelector("[js-sort-container]")) {
    void __vitePreload(async () => {
      const { default: Sort } = await import("../sort.js");
      return { default: Sort };
    }, true ? [] : void 0).then(({ default: Sort }) => new Sort().applySort());
  }
  if (document.querySelector("[js-split]")) {
    void __vitePreload(async () => {
      const { default: SplitButton } = await import("../splitButton.js");
      return { default: SplitButton };
    }, true ? [] : void 0).then(({ default: SplitButton }) => new SplitButton().syncSplitButton());
  }
  if (document.querySelector(".notification__button")) {
    void __vitePreload(async () => {
      const { default: NotificationDoc } = await import("../notificationDoc.js");
      return { default: NotificationDoc };
    }, true ? [] : void 0).then(({ default: NotificationDoc }) => new NotificationDoc().addListener());
  }
  if (document.querySelector(".c-sidebar[endpoint-children]")) {
    void __vitePreload(async () => {
      const { default: DynamicSidebar } = await import("../dynamicSidebar.js");
      return { default: DynamicSidebar };
    }, true ? [] : void 0).then(({ default: DynamicSidebar }) => new DynamicSidebar().applySidebar());
  }
  if (document.querySelector("[js-filter-container]")) {
    void __vitePreload(async () => {
      const { default: Filter } = await import("../filter.js");
      return { default: Filter };
    }, true ? [] : void 0).then(({ default: Filter }) => new Filter());
  }
  if (document.querySelector("[data-js-keep-in-viewport], [data-js-keep-in-viewport-after-resize]")) {
    void __vitePreload(async () => {
      const { default: KeepInViewPort } = await import("../keepInViewPort.js");
      return { default: KeepInViewPort };
    }, true ? [] : void 0).then(({ default: KeepInViewPort }) => new KeepInViewPort());
  }
  if (document.querySelector("[js-resize-by-children]")) {
    void __vitePreload(async () => {
      const { default: ResizeByChildren } = await import("../resizeByChildren.js");
      return { default: ResizeByChildren };
    }, true ? [] : void 0).then(({ default: ResizeByChildren }) => new ResizeByChildren());
  }
  if (document.querySelector('input[type="checkbox"], input[type="email"], input[type="text"], input[type="date"], input[type="search"], input[type="datetime-local"], input[type="month"], input[type="number"]')) {
    void __vitePreload(async () => {
      const { default: StickyKeys } = await import("../stickyKeys.js");
      return { default: StickyKeys };
    }, true ? [] : void 0).then(({ default: StickyKeys }) => new StickyKeys());
  }
  if (document.querySelector("#quicklinks-header.c-header--sticky")) {
    void __vitePreload(async () => {
      const { default: QuickLinksHeader } = await import("../quickLinksHeader.js");
      return { default: QuickLinksHeader };
    }, true ? [] : void 0).then(({ default: QuickLinksHeader }) => new QuickLinksHeader());
  }
  if (document.querySelector("[data-js-copy-target]")) {
    void __vitePreload(async () => {
      const { setupCopy } = await import("../copy.js");
      return { setupCopy };
    }, true ? [] : void 0).then(({ setupCopy }) => setupCopy());
  }
  if (document.querySelector("[data-js-extended-dropdown-content]")) {
    void __vitePreload(async () => {
      const { initializeExtendedDropdownMenu } = await import("../extendedDropdownMenu.js");
      return { initializeExtendedDropdownMenu };
    }, true ? [] : void 0).then(({ initializeExtendedDropdownMenu }) => initializeExtendedDropdownMenu());
  }
  if (document.querySelector("[data-js-sizeobserver]")) {
    void __vitePreload(async () => {
      const { initializeSizeObserver } = await import("../sizeObserver.js");
      return { initializeSizeObserver };
    }, true ? [] : void 0).then(({ initializeSizeObserver }) => initializeSizeObserver());
  }
  if (document.querySelector("#scroll-spy")) {
    void __vitePreload(async () => {
      const { default: AnchorMenu } = await import("../anchorMenu.js");
      return { default: AnchorMenu };
    }, true ? [] : void 0).then(({ default: AnchorMenu }) => AnchorMenu());
  }
  if (document.querySelector(".js-dropdown")) {
    void __vitePreload(() => import("../dropdown.js"), true ? [] : void 0);
  }
}
__name(loadOptionalFeatures, "loadOptionalFeatures");
document.addEventListener("DOMContentLoaded", () => {
  new ButtonToggleContent();
  new SimulateClick();
  new AriaPressedToggler();
  new ClassToggleInitializer().init();
  NotificationInstance.setup();
  initializeResizeMediaQuery();
  initializeCompressed();
  initializeGoogleTranslate();
  setScrollbarCSS();
  moveElements(moveElement);
  initializeClickAways();
  loadOptionalFeatures();
});
function loadPageFeatures() {
  const pending = /* @__PURE__ */ new Map([
    ["[data-tooltip]", () => __vitePreload(() => import("../tooltip.js"), true ? [] : void 0)],
    ["[popover]", () => __vitePreload(() => import("../popoverInit.js"), true ? [] : void 0)],
    ["[data-modifier-preview]", () => __vitePreload(() => import("../modifierPreview.js"), true ? [] : void 0)],
    ["input[data-datalist]", () => __vitePreload(() => import("../datalistAutocomplete.js"), true ? [] : void 0)]
  ]);
  const loadMatching = /* @__PURE__ */ __name((root) => {
    for (const [selector, load] of pending) {
      if (root.matches?.(selector) || root.querySelector?.(selector)) {
        pending.delete(selector);
        void load();
      }
    }
  }, "loadMatching");
  loadMatching(document.documentElement);
  if (pending.size === 0) return;
  const observer = new MutationObserver((mutations) => {
    for (const mutation of mutations) {
      for (const node of mutation.addedNodes) {
        if (node instanceof Element) loadMatching(node);
      }
    }
    if (pending.size === 0) observer.disconnect();
  });
  observer.observe(document.body, { childList: true, subtree: true });
}
__name(loadPageFeatures, "loadPageFeatures");
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", loadPageFeatures, { once: true });
} else {
  loadPageFeatures();
}
//# sourceMappingURL=styleguide-js.js.map
