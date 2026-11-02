var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _NotificationDoc = class _NotificationDoc {
  addListener() {
    const notificationButton = document.getElementsByClassName("notification__button")[0];
    const notification = document.getElementsByClassName("c-notification")[0];
    if (notification) {
      const direction = notification.getAttribute("direction");
      const container = document.createElement("DIV");
      container.classList.add("c-notification__container");
      container.classList.add("c-notification__container--" + direction);
      container.setAttribute("maxAmount", 3);
      document.body.appendChild(container);
      notificationButton.addEventListener("click", () => {
        const notificationCopy = notification.cloneNode(true);
        notificationCopy.classList.remove("u-display--none");
        container.appendChild(notificationCopy);
      });
    }
  }
};
__name(_NotificationDoc, "NotificationDoc");
let NotificationDoc = _NotificationDoc;
export {
  NotificationDoc as default
};
//# sourceMappingURL=notificationDoc.js.map
