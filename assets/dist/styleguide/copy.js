var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _Copy = class _Copy {
  constructor(element) {
    this.copy(element);
  }
  copy(element) {
    const target = element.getAttribute("data-js-copy-target");
    element.addEventListener("click", () => {
      const targetEl = target == "self" ? element : document.querySelector(`[data-js-copy-item="${target}"]`);
      if (targetEl && targetEl.hasAttribute("data-js-copy-data")) {
        const data = targetEl.getAttribute("data-js-copy-data") ?? "";
        if ("permissions" in navigator) {
          navigator.permissions.query({ name: "clipboard-write" }).then((result) => {
            if (result.state === "granted" || result.state === "prompt") {
              navigator.clipboard.writeText(data).then(() => {
                this.append(element, true);
              }).catch((error) => {
                console.error("Error copying text to clipboard:", error);
              });
            } else {
              this.append(element, false);
            }
          }).catch((error) => {
            console.error("Error requesting permission:", error);
            this.append(element, false);
          });
        } else {
          console.warn("Clipboard API not supported in this browser");
          this.append(element, false);
        }
      } else return;
    });
  }
  append(element, success = false) {
    if (!element || !element.parentNode) return;
    const successNotice = element.getAttribute("data-js-copy-success") ?? "Content was successfully copied.";
    const errorNotice = element.getAttribute("data-js-copy-error") ?? "Something went wrong";
    const notice = success ? successNotice : errorNotice;
    const sibling = element.nextSibling;
    if (sibling instanceof Element && sibling.hasAttribute("js-data-copy-notice")) {
      sibling.textContent = notice;
    } else {
      const span = document.createElement("span");
      span.innerText = notice;
      span.setAttribute("js-data-copy-notice", "");
      element.parentNode.insertBefore(span, element.nextSibling);
    }
  }
};
__name(_Copy, "Copy");
let Copy = _Copy;
function setupCopy() {
  [...document.querySelectorAll("[data-js-copy-target]")].forEach((element) => {
    new Copy(element);
  });
}
__name(setupCopy, "setupCopy");
export {
  setupCopy
};
//# sourceMappingURL=copy.js.map
