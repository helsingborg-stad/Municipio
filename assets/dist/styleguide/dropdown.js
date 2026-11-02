var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const init = /* @__PURE__ */ __name(() => {
  let elements = [];
  const toggleDropdownElements = /* @__PURE__ */ __name((dropdowns2 = []) => dropdowns2.forEach((e) => e.classList.toggle("is-open")), "toggleDropdownElements");
  const toggleDropdowns = /* @__PURE__ */ __name((dropdowns2 = []) => {
    toggleDropdownElements([...elements, ...dropdowns2]);
    elements = [...dropdowns2];
  }, "toggleDropdowns");
  const dropdowns = [...document.querySelectorAll(".js-dropdown")].map(
    (dropdown) => {
      [...dropdown.querySelectorAll(".js-dropdown-button")].forEach((btn) => {
        btn.addEventListener("click", () => {
          const isOpen = [...dropdown?.classList ?? []].includes("is-open");
          toggleDropdowns(!isOpen ? [dropdown] : []);
        });
      });
      return dropdown;
    }
  );
  if (dropdowns.length > 0) {
    document.addEventListener(
      "click",
      (e) => {
        const el = e.target;
        if (!el?.closest(".js-dropdown")) {
          toggleDropdowns([]);
        }
      },
      false
    );
  }
}, "init");
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", init, { once: true });
} else {
  init();
}
//# sourceMappingURL=dropdown.js.map
