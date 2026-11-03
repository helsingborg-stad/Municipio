var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const CONTAINER = "[js-filter-container]", ITEM = "[js-filter-item]", DATA = "[js-filter-data]", INPUT = "[js-filter-input]";
const _Filter = class _Filter {
  constructor() {
    this.list = [];
    this.enableSearch();
  }
  enableSearch() {
    const containers = document.querySelectorAll(CONTAINER);
    containers.forEach((container) => {
      container.querySelectorAll(ITEM).forEach((item) => {
        let dataItems;
        let dataString = "";
        if (item.hasAttribute("js-filter-data")) {
          dataItems = [item, ...item.querySelectorAll(DATA)];
        } else {
          dataItems = item.querySelectorAll(DATA);
        }
        dataItems.forEach((data) => {
          dataString = dataString.concat(data.innerHTML);
          dataString = dataString.replace(/(<([^>]+)>)/gi, "");
        });
        this.list.push({
          searchId: container.getAttribute("js-filter-container"),
          //Get id
          element: item,
          parent: item.parentNode,
          data: dataString.toLowerCase()
        });
      });
      container.querySelectorAll(INPUT).forEach((input) => {
        input.addEventListener("input", () => {
          const inputId = input.getAttribute("js-filter-input");
          this.list.forEach((item) => {
            if (item.searchId === inputId) {
              const res = item.data.search(input.value.toLocaleLowerCase());
              if (res < 0) {
                item.element.remove();
              } else {
                item.parent.append(item.element);
              }
            }
          });
        });
      });
    });
  }
};
__name(_Filter, "Filter");
let Filter = _Filter;
export {
  Filter as default
};
//# sourceMappingURL=filter.js.map
