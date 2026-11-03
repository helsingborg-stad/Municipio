var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const acceptedSuppliers = JSON.parse(localStorage.getItem("acceptedSuppliers")) ?? [];
const hasSuppressedContent = /* @__PURE__ */ __name((modifier) => {
  const suppressedContentExists = document.querySelectorAll(".js-suppressed-content").length > 0;
  return suppressedContentExists;
}, "hasSuppressedContent");
const url = /* @__PURE__ */ __name((contentWrapper) => {
  if (contentWrapper.hasAttribute("data-src")) {
    const json = JSON.parse(contentWrapper.getAttribute("data-src"));
    const url2 = [];
    json.forEach((host) => {
      url2.push(new URL(host));
    });
    return url2;
  }
}, "url");
const handleReveal = /* @__PURE__ */ __name((contentWrapper) => {
  const needsAcceptance = [];
  const contentUrl = url(contentWrapper);
  contentUrl.forEach((supplier) => {
    if (acceptedSuppliers.includes(supplier.host)) {
      needsAcceptance.push(true);
    } else {
      needsAcceptance.push(false);
    }
  });
  if (!needsAcceptance.includes(false)) {
    revealContent(contentWrapper);
  }
}, "handleReveal");
const setLocalStorage = /* @__PURE__ */ __name((contentWrapper) => {
  const contentUrl = url(contentWrapper);
  if (contentUrl) {
    contentUrl.forEach((supplier) => {
      if (!acceptedSuppliers.includes(supplier.host) && supplier.host !== "https" && contentUrl.host !== "http") {
        acceptedSuppliers.push(supplier.host);
      }
      localStorage.setItem("acceptedSuppliers", JSON.stringify(acceptedSuppliers));
    });
  }
}, "setLocalStorage");
const revealContent = /* @__PURE__ */ __name((contentWrapper) => {
  const template = contentWrapper.querySelector("template");
  const suppressedContentWrapper = contentWrapper.querySelector(".c-acceptance__content");
  const clone = template.content.cloneNode(true);
  suppressedContentWrapper.appendChild(clone);
  contentWrapper.classList.remove("u-level-1");
  contentWrapper.classList.remove("js-suppressed-content");
  contentWrapper.classList.add("c-acceptance--accepted");
  contentWrapper.querySelector(".js-suppressed-content-prompt").classList.add("u-display--none");
}, "revealContent");
const revealContentLoop = /* @__PURE__ */ __name(() => {
  hasSuppressedContent() && [...document.querySelectorAll(".js-suppressed-content")].forEach((contentWrapper) => {
    if (contentWrapper.classList.contains("js-suppressed-content--none")) {
      handleReveal(contentWrapper);
    }
  });
}, "revealContentLoop");
const handleEvents = /* @__PURE__ */ __name((contentWrapper) => {
  setLocalStorage(contentWrapper);
  if (contentWrapper.classList.contains("js-suppressed-content--video")) {
    revealContent(contentWrapper);
  } else {
    revealContentLoop();
  }
}, "handleEvents");
const setEvents = /* @__PURE__ */ __name(() => {
  [...document.querySelectorAll(".js-suppressed-content")].forEach((contentWrapper) => {
    contentWrapper.querySelector(".js-suppressed-content-description").style.display = "block";
    const buttonEl = contentWrapper.querySelector("[js-suppressed-content-accept]");
    buttonEl?.addEventListener("click", () => {
      handleEvents(contentWrapper);
    });
  });
}, "setEvents");
function initializeIframeAcceptance() {
  if (acceptedSuppliers.length > 0 && hasSuppressedContent()) {
    [...document.querySelectorAll(".js-suppressed-content")].forEach((contentWrapper) => {
      if (contentWrapper.classList.contains("js-suppressed-content--none")) {
        handleReveal(contentWrapper);
      }
    });
  }
  hasSuppressedContent() && setEvents();
}
__name(initializeIframeAcceptance, "initializeIframeAcceptance");
if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initializeIframeAcceptance);
else initializeIframeAcceptance();
//# sourceMappingURL=acceptance.js.map
