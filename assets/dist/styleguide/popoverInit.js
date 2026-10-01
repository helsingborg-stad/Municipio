var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var PopoverEnums = /* @__PURE__ */ ((PopoverEnums2) => {
  PopoverEnums2["PopoverSelectorAttribute"] = "popover";
  PopoverEnums2["PopoverTargetSelectorAttribute"] = "popovertarget";
  PopoverEnums2["InitializedAttribute"] = "data-popover-initialized";
  PopoverEnums2["HorizontalPlacementAttribute"] = "data-js-popover-horizontal-placement";
  PopoverEnums2["VerticalPlacementAttribute"] = "data-js-popover-vertical-placement";
  PopoverEnums2["CoverAttribute"] = "data-js-popover-cover";
  PopoverEnums2["RelativeAttribute"] = "data-js-popover-relative";
  PopoverEnums2["RelativeElementAttribute"] = "data-js-popover-relative";
  return PopoverEnums2;
})(PopoverEnums || {});
var HorizontalPlacement = /* @__PURE__ */ ((HorizontalPlacement2) => {
  HorizontalPlacement2["left"] = "left";
  HorizontalPlacement2["center"] = "center";
  HorizontalPlacement2["right"] = "right";
  return HorizontalPlacement2;
})(HorizontalPlacement || {});
var VerticalPlacement = /* @__PURE__ */ ((VerticalPlacement2) => {
  VerticalPlacement2["top"] = "top";
  VerticalPlacement2["center"] = "center";
  VerticalPlacement2["bottom"] = "bottom";
  return VerticalPlacement2;
})(VerticalPlacement || {});
const _Popover = class _Popover {
  constructor(popoverData, popoverPositioner) {
    this.popoverData = popoverData;
    this.popoverPositioner = popoverPositioner;
  }
  popoverData;
  popoverPositioner;
  init() {
    this.reposition();
    this.dispatchCustomEvent();
  }
  getPopoverData() {
    return this.popoverData;
  }
  setPositionData(positionData) {
    this.popoverData.horizontalPlacement = HorizontalPlacement[positionData.horizontalPlacement ?? this.popoverData.horizontalPlacement] || "center";
    this.popoverData.verticalPlacement = VerticalPlacement[positionData.verticalPlacement ?? this.popoverData.verticalPlacement] || "center";
  }
  setCover(isCover) {
    this.popoverData.cover = isCover;
    this.popoverData.popoverElement.toggleAttribute(PopoverEnums.CoverAttribute, isCover);
  }
  reposition() {
    this.popoverPositioner.applyPosition(this.popoverData);
  }
  dispatchCustomEvent() {
    const event = new CustomEvent("popover:initialized", {
      detail: {
        popover: this,
        id: this.popoverData.id,
        element: this.popoverData.popoverElement
      }
    });
    document.dispatchEvent(event);
  }
};
__name(_Popover, "Popover");
let Popover = _Popover;
const _PopoverPositioner = class _PopoverPositioner {
  applyPosition(popoverData) {
    this.resetPositionStyles(popoverData);
    if (this.isCoverPopover(popoverData)) {
      return;
    }
    if (popoverData.relativeElement) {
      this.setRelativeAnchorStyles(popoverData);
      this.setRelativePositionStyles(popoverData);
      return;
    }
    this.setViewportPositionStyles(popoverData);
  }
  resetPositionStyles(popoverData) {
    const { popoverElement, relativeElement } = popoverData;
    popoverElement.style.left = "";
    popoverElement.style.right = "";
    popoverElement.style.top = "";
    popoverElement.style.bottom = "";
    popoverElement.style.transform = "";
    popoverElement.style.positionArea = "";
    popoverElement.style.positionAnchor = "";
    if (relativeElement) {
      relativeElement.style.anchorName = "";
    }
  }
  setRelativeAnchorStyles(popoverData) {
    const anchorName = `--${popoverData.id}`;
    popoverData.relativeElement.style.anchorName = anchorName;
    popoverData.popoverElement.style.positionAnchor = anchorName;
  }
  isCoverPopover(popoverData) {
    return popoverData.popoverElement.hasAttribute(PopoverEnums.CoverAttribute);
  }
  setRelativePositionStyles(popoverData) {
    popoverData.popoverElement.style.positionArea = popoverData.verticalPlacement;
    if (popoverData.verticalPlacement === "center") {
      popoverData.popoverElement.style.positionArea = `center ${popoverData.horizontalPlacement}`;
    }
    if (popoverData.horizontalPlacement === "left") {
      popoverData.popoverElement.style.left = `anchor(left)`;
    }
    if (popoverData.horizontalPlacement === "right") {
      popoverData.popoverElement.style.right = `anchor(right)`;
    }
  }
  setViewportPositionStyles(popoverData) {
    const popoverElement = popoverData.popoverElement;
    if (popoverData.horizontalPlacement === "left") {
      popoverElement.style.left = "0";
      popoverElement.style.transform = "none";
    } else if (popoverData.horizontalPlacement === "right") {
      popoverElement.style.right = "0";
      popoverElement.style.left = "auto";
      popoverElement.style.transform = "none";
    } else {
      popoverElement.style.left = "50%";
      popoverElement.style.transform = "translateX(-50%)";
    }
    if (popoverData.verticalPlacement === "top") {
      popoverElement.style.top = "0";
      popoverElement.style.bottom = "auto";
      popoverElement.style.transform += " translateY(0)";
    } else if (popoverData.verticalPlacement === "bottom") {
      popoverElement.style.bottom = "0";
      popoverElement.style.top = "auto";
      popoverElement.style.transform += " translateY(0)";
    } else {
      popoverElement.style.top = "50%";
      popoverElement.style.transform += `translateY(-50%)`;
    }
  }
};
__name(_PopoverPositioner, "PopoverPositioner");
let PopoverPositioner = _PopoverPositioner;
const initializePopovers = /* @__PURE__ */ __name(() => {
  document.querySelectorAll(`[${PopoverEnums.PopoverSelectorAttribute}]`).forEach((popoverElement) => {
    const popoverData = tryGetPopoverData(popoverElement);
    if (!popoverData) {
      return;
    }
    new Popover(popoverData, new PopoverPositioner()).init();
  });
}, "initializePopovers");
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initializePopovers, { once: true });
} else {
  initializePopovers();
}
function tryGetPopoverData(popoverElement) {
  const id = popoverElement.id;
  const horizontalPlacement = getHorizontalPlacement(popoverElement);
  const verticalPlacement = getVerticalPlacement(popoverElement);
  const relative = popoverElement.hasAttribute(PopoverEnums.RelativeAttribute);
  const popoverTarget = document.querySelector(`[${PopoverEnums.PopoverTargetSelectorAttribute}="${id}"]`);
  const relativeElement = getRelativeElement(popoverElement, popoverTarget, relative);
  const cover = popoverElement.hasAttribute(PopoverEnums.CoverAttribute);
  if (!id || !popoverTarget) {
    console.error(`Popover with id "${id}" or target "${popoverTarget}" is missing.`);
    return null;
  }
  return { id, popoverElement, popoverTarget, relativeElement, horizontalPlacement, verticalPlacement, relative, cover };
}
__name(tryGetPopoverData, "tryGetPopoverData");
function getRelativeElement(popoverElement, popoverTarget, relative) {
  return document.querySelector(`[${PopoverEnums.RelativeElementAttribute}="${popoverElement.id}"]`) || (relative ? popoverTarget : null);
}
__name(getRelativeElement, "getRelativeElement");
function getHorizontalPlacement(popoverElement) {
  const horizontalPlacement = popoverElement.getAttribute(PopoverEnums.HorizontalPlacementAttribute) || "center";
  if (!["left", "center", "right"].includes(horizontalPlacement)) {
    return "center";
  }
  return horizontalPlacement;
}
__name(getHorizontalPlacement, "getHorizontalPlacement");
function getVerticalPlacement(popoverElement) {
  const verticalPlacement = popoverElement.getAttribute(PopoverEnums.VerticalPlacementAttribute) || "center";
  if (!["top", "center", "bottom"].includes(verticalPlacement)) {
    return "center";
  }
  return verticalPlacement;
}
__name(getVerticalPlacement, "getVerticalPlacement");
//# sourceMappingURL=popoverInit.js.map
