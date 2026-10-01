var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var TooltipSetup = /* @__PURE__ */ ((TooltipSetup2) => {
  TooltipSetup2["TriggerSelector"] = "[data-tooltip]";
  TooltipSetup2["TooltipId"] = "styleguide-tooltip";
  TooltipSetup2["InitializedAttribute"] = "data-tooltip-initialized";
  TooltipSetup2["UninitializedSelector"] = "[data-tooltip]:not([data-tooltip-initialized])";
  return TooltipSetup2;
})(TooltipSetup || {});
var TooltipDom = /* @__PURE__ */ ((TooltipDom2) => {
  TooltipDom2["AriaDescribedBy"] = "aria-describedby";
  return TooltipDom2;
})(TooltipDom || {});
var TooltipOffset = /* @__PURE__ */ ((TooltipOffset2) => {
  TooltipOffset2[TooltipOffset2["Top"] = 7] = "Top";
  TooltipOffset2[TooltipOffset2["Viewport"] = 12] = "Viewport";
  TooltipOffset2[TooltipOffset2["Arrow"] = 16] = "Arrow";
  return TooltipOffset2;
})(TooltipOffset || {});
var TooltipAttribute = /* @__PURE__ */ ((TooltipAttribute2) => {
  TooltipAttribute2["AriaHidden"] = "aria-hidden";
  TooltipAttribute2["Role"] = "role";
  return TooltipAttribute2;
})(TooltipAttribute || {});
var TooltipClassName = /* @__PURE__ */ ((TooltipClassName2) => {
  TooltipClassName2["Root"] = "o-tooltip";
  TooltipClassName2["Visible"] = "is-visible";
  return TooltipClassName2;
})(TooltipClassName || {});
var TooltipStyleProperty = /* @__PURE__ */ ((TooltipStyleProperty2) => {
  TooltipStyleProperty2["X"] = "--o-tooltip-x";
  TooltipStyleProperty2["Y"] = "--o-tooltip-y";
  TooltipStyleProperty2["ArrowLeft"] = "--o-tooltip-arrow-left";
  return TooltipStyleProperty2;
})(TooltipStyleProperty || {});
const _TooltipController = class _TooltipController {
  constructor(triggers, view, positioner, events) {
    this.triggers = triggers;
    this.view = view;
    this.positioner = positioner;
    this.events = events;
  }
  triggers;
  view;
  positioner;
  events;
  activeTrigger = null;
  repositionFrameId = null;
  showFrameId = null;
  init() {
    this.events.bindTriggers(this.triggers);
  }
  registerTrigger(trigger) {
    this.events.bindTriggers([trigger]);
  }
  show(trigger) {
    const content = trigger.dataset.tooltip?.trim();
    if (!content || this.activeTrigger === trigger) {
      return;
    }
    if (this.activeTrigger) {
      this.activeTrigger.removeAttribute(TooltipDom.AriaDescribedBy);
    }
    this.activeTrigger = trigger;
    this.events.addGlobalListeners();
    trigger.setAttribute(TooltipDom.AriaDescribedBy, this.view.id);
    this.view.show(content);
    this.position(trigger);
    this.scheduleShow(trigger);
  }
  hide(trigger) {
    if (trigger && this.activeTrigger !== trigger) {
      return;
    }
    this.activeTrigger?.removeAttribute(TooltipDom.AriaDescribedBy);
    this.activeTrigger = null;
    this.events.removeGlobalListeners();
    this.cancelScheduledShow();
    this.cancelScheduledReposition();
    this.view.hide();
  }
  handlePointerDown(target) {
    if (this.activeTrigger && this.getTrigger(target) !== this.activeTrigger) {
      this.hide(this.activeTrigger);
    }
  }
  repositionActiveTooltip() {
    if (!this.activeTrigger || !this.view.isVisible()) {
      return;
    }
    this.schedulePosition(this.activeTrigger);
  }
  schedulePosition(trigger) {
    this.cancelScheduledReposition();
    this.repositionFrameId = requestAnimationFrame(() => {
      this.position(trigger);
      this.repositionFrameId = null;
    });
  }
  scheduleShow(trigger) {
    this.cancelScheduledShow();
    this.showFrameId = requestAnimationFrame(() => {
      if (this.activeTrigger !== trigger) {
        this.showFrameId = null;
        return;
      }
      this.view.reveal();
      this.showFrameId = null;
    });
  }
  position(trigger) {
    this.view.setPosition(this.positioner.calculate(trigger, this.view.element));
  }
  cancelScheduledReposition() {
    if (this.repositionFrameId === null) {
      return;
    }
    cancelAnimationFrame(this.repositionFrameId);
    this.repositionFrameId = null;
  }
  cancelScheduledShow() {
    if (this.showFrameId === null) {
      return;
    }
    cancelAnimationFrame(this.showFrameId);
    this.showFrameId = null;
  }
  getTrigger(target) {
    if (!(target instanceof Element)) {
      return null;
    }
    return target.closest(TooltipSetup.TriggerSelector);
  }
};
__name(_TooltipController, "TooltipController");
let TooltipController = _TooltipController;
const _TooltipEvents = class _TooltipEvents {
  constructor(callbacks) {
    this.callbacks = callbacks;
  }
  callbacks;
  bindTriggers(triggers) {
    triggers.forEach((trigger) => {
      trigger.addEventListener("pointerenter", this.handlePointerEnter, { passive: true });
      trigger.addEventListener("pointerleave", this.handlePointerLeave, { passive: true });
      trigger.addEventListener("focus", this.handleFocus);
      trigger.addEventListener("blur", this.handleBlur);
    });
  }
  addGlobalListeners() {
    document.addEventListener("pointerdown", this.handlePointerDown, true);
    document.addEventListener("keydown", this.handleKeyDown, true);
    window.addEventListener("resize", this.handleViewportChange, { passive: true });
    document.addEventListener("scroll", this.handleViewportChange, { capture: true, passive: true });
  }
  removeGlobalListeners() {
    document.removeEventListener("pointerdown", this.handlePointerDown, true);
    document.removeEventListener("keydown", this.handleKeyDown, true);
    window.removeEventListener("resize", this.handleViewportChange);
    document.removeEventListener("scroll", this.handleViewportChange, true);
  }
  handlePointerEnter = /* @__PURE__ */ __name((event) => {
    const trigger = event.currentTarget;
    if (trigger) {
      this.callbacks.onTriggerEnter(trigger);
    }
  }, "handlePointerEnter");
  handlePointerLeave = /* @__PURE__ */ __name((event) => {
    const trigger = event.currentTarget;
    if (trigger) {
      this.callbacks.onTriggerLeave(trigger);
    }
  }, "handlePointerLeave");
  handleFocus = /* @__PURE__ */ __name((event) => {
    const trigger = event.currentTarget;
    if (trigger) {
      this.callbacks.onTriggerFocus(trigger);
    }
  }, "handleFocus");
  handleBlur = /* @__PURE__ */ __name((event) => {
    const trigger = event.currentTarget;
    if (trigger) {
      this.callbacks.onTriggerBlur(trigger);
    }
  }, "handleBlur");
  handlePointerDown = /* @__PURE__ */ __name((event) => {
    this.callbacks.onPointerDown(event.target);
  }, "handlePointerDown");
  handleKeyDown = /* @__PURE__ */ __name((event) => {
    if (event.key === "Escape") {
      this.callbacks.onEscape();
    }
  }, "handleKeyDown");
  handleViewportChange = /* @__PURE__ */ __name(() => {
    this.callbacks.onViewportChange();
  }, "handleViewportChange");
};
__name(_TooltipEvents, "TooltipEvents");
let TooltipEvents = _TooltipEvents;
const _TooltipPositioner = class _TooltipPositioner {
  calculate(trigger, tooltip) {
    const triggerRect = trigger.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();
    const triggerCenter = triggerRect.left + triggerRect.width / 2;
    const maxLeft = Math.max(TooltipOffset.Viewport, window.innerWidth - tooltipRect.width - TooltipOffset.Viewport);
    const preferredLeft = triggerCenter - tooltipRect.width / 2;
    const left = Math.min(maxLeft, Math.max(TooltipOffset.Viewport, preferredLeft));
    const top = triggerRect.top - tooltipRect.height - TooltipOffset.Top;
    const arrowLeft = Math.min(tooltipRect.width - TooltipOffset.Arrow, Math.max(TooltipOffset.Arrow, triggerCenter - left));
    return {
      left: Math.round(left),
      top: Math.round(top),
      arrowLeft: Math.round(arrowLeft)
    };
  }
};
__name(_TooltipPositioner, "TooltipPositioner");
let TooltipPositioner = _TooltipPositioner;
const _TooltipView = class _TooltipView {
  element;
  id;
  constructor(id) {
    this.id = id;
    this.element = this.createTooltip();
  }
  show(content) {
    this.element.textContent = content;
    this.element.style.visibility = "hidden";
    this.element.classList.remove(TooltipClassName.Visible);
    this.element.setAttribute(TooltipAttribute.AriaHidden, "false");
  }
  reveal() {
    this.element.style.visibility = "";
    this.element.classList.add(TooltipClassName.Visible);
  }
  hide() {
    this.element.classList.remove(TooltipClassName.Visible);
    this.element.setAttribute(TooltipAttribute.AriaHidden, "true");
    this.element.style.visibility = "";
    this.element.textContent = "";
    this.element.style.removeProperty(TooltipStyleProperty.X);
    this.element.style.removeProperty(TooltipStyleProperty.Y);
    this.element.style.removeProperty(TooltipStyleProperty.ArrowLeft);
  }
  setPosition(position) {
    this.element.style.setProperty(TooltipStyleProperty.X, `${position.left}px`);
    this.element.style.setProperty(TooltipStyleProperty.Y, `${position.top}px`);
    this.element.style.setProperty(TooltipStyleProperty.ArrowLeft, `${position.arrowLeft}px`);
  }
  isVisible() {
    return this.element.getAttribute(TooltipAttribute.AriaHidden) === "false";
  }
  createTooltip() {
    const existingTooltip = document.getElementById(this.id);
    if (existingTooltip) {
      return existingTooltip;
    }
    const element = document.createElement("div");
    element.id = this.id;
    element.className = TooltipClassName.Root;
    element.setAttribute(TooltipAttribute.Role, "tooltip");
    element.setAttribute(TooltipAttribute.AriaHidden, "true");
    document.body.appendChild(element);
    return element;
  }
};
__name(_TooltipView, "TooltipView");
let TooltipView = _TooltipView;
const _Tooltip = class _Tooltip {
  init() {
    const triggers = Array.from(document.querySelectorAll(TooltipSetup.UninitializedSelector));
    const view = new TooltipView(TooltipSetup.TooltipId);
    const positioner = new TooltipPositioner();
    let controller;
    const events = new TooltipEvents({
      onTriggerEnter: /* @__PURE__ */ __name((trigger) => controller.show(trigger), "onTriggerEnter"),
      onTriggerLeave: /* @__PURE__ */ __name((trigger) => controller.hide(trigger), "onTriggerLeave"),
      onTriggerFocus: /* @__PURE__ */ __name((trigger) => controller.show(trigger), "onTriggerFocus"),
      onTriggerBlur: /* @__PURE__ */ __name((trigger) => controller.hide(trigger), "onTriggerBlur"),
      onPointerDown: /* @__PURE__ */ __name((target) => controller.handlePointerDown(target), "onPointerDown"),
      onEscape: /* @__PURE__ */ __name(() => controller.hide(), "onEscape"),
      onViewportChange: /* @__PURE__ */ __name(() => controller.repositionActiveTooltip(), "onViewportChange")
    });
    controller = new TooltipController(triggers, view, positioner, events);
    controller.init();
    triggers.forEach((trigger) => {
      trigger.setAttribute(TooltipSetup.InitializedAttribute, "");
    });
    this.observeDom(controller);
  }
  observeDom(controller) {
    const observer = new MutationObserver((mutations) => {
      for (const mutation of mutations) {
        for (const node of Array.from(mutation.addedNodes)) {
          if (!(node instanceof HTMLElement)) {
            continue;
          }
          const newTriggers = [
            ...node.matches(TooltipSetup.UninitializedSelector) ? [node] : [],
            ...Array.from(node.querySelectorAll(TooltipSetup.UninitializedSelector))
          ];
          for (const trigger of newTriggers) {
            trigger.setAttribute(TooltipSetup.InitializedAttribute, "");
            controller.registerTrigger(trigger);
          }
        }
      }
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }
};
__name(_Tooltip, "Tooltip");
let Tooltip = _Tooltip;
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => new Tooltip().init(), { once: true });
} else {
  new Tooltip().init();
}
export {
  Tooltip as default
};
//# sourceMappingURL=tooltip.js.map
