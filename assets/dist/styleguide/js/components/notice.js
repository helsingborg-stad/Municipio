var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var NoticeTimeout = /* @__PURE__ */ ((NoticeTimeout2) => {
  NoticeTimeout2["Session"] = "session";
  NoticeTimeout2["Permanent"] = "permanent";
  NoticeTimeout2["Immediate"] = "immediate";
  return NoticeTimeout2;
})(NoticeTimeout || {});
const _DismissableNotice = class _DismissableNotice {
  notice;
  dismissTrigger;
  uid;
  timeout;
  constructor(notice) {
    this.notice = notice;
    this.dismissTrigger = this.notice.querySelector("[data-dismissable-notice-trigger]");
    this.uid = this.notice.getAttribute("data-dismissable-notice-uid") || "";
    this.timeout = this.uid ? this.getTimeoutValue() : "immediate";
    this.init();
  }
  /**
   * Initializes the dismissable notice by setting up event listeners
   * and checking if the notice should be displayed.
   */
  init() {
    if (this.timeout !== "immediate" && !this.shouldShowNotice()) {
      this.removeNotice();
      return;
    }
    this.setupListeners();
  }
  /**
   * Sets up the event listener for the dismiss button.
   */
  setupListeners() {
    if (this.dismissTrigger) {
      this.dismissTrigger.addEventListener("click", () => this.dismiss(), { once: true });
    }
  }
  /**
   * Checks if the notice should be shown based on its timeout value
   * and the stored state in sessionStorage or localStorage.
   */
  shouldShowNotice() {
    const storage = this.getStorage();
    return this.timeout === "immediate" || !storage.getItem(this.uid);
  }
  /**
   * Dismisses the notice by storing its state in sessionStorage or localStorage
   * and removing it from the DOM.
   */
  dismiss() {
    if (this.timeout !== "immediate" && this.uid) {
      const storage = this.getStorage();
      storage.setItem(this.uid, "dismissed");
    }
    this.removeNotice();
  }
  /**
   * Removes the notice from the DOM.
   */
  removeNotice() {
    this.notice.remove();
  }
  /**
   * Returns the appropriate storage object (sessionStorage or localStorage)
   * based on the timeout value.
   */
  getStorage() {
    switch (this.timeout) {
      case "permanent":
        return localStorage;
      case "session":
      default:
        return sessionStorage;
    }
  }
  /**
   * Retrieves and validates the timeout value from the data attribute.
   */
  getTimeoutValue() {
    const timeout = this.notice.getAttribute("data-dismissable-notice-timeout");
    if (Object.values(NoticeTimeout).includes(timeout)) {
      return timeout;
    }
    return "session";
  }
};
__name(_DismissableNotice, "DismissableNotice");
let DismissableNotice = _DismissableNotice;
function initializeDismissableNotices() {
  const notices = document.querySelectorAll("[data-dismissable-notice]");
  notices.forEach((notice) => {
    new DismissableNotice(notice);
  });
}
__name(initializeDismissableNotices, "initializeDismissableNotices");
if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initializeDismissableNotices);
else initializeDismissableNotices();
//# sourceMappingURL=notice.js.map
