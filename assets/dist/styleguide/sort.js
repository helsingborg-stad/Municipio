var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _Sort = class _Sort {
  constructor() {
    this.CONTAINER = "js-sort-container";
    this.BUTTON = "js-sort-button";
    this.SORTABLE = "js-sort-sortable";
    this.ORDER = "js-sort-order";
    this.DATA = "js-sort-data";
    this.DATACONTAINER = "js-sort-data-container";
    this.INITIAL = "js-sort-initial";
  }
  compare(a, b) {
    return a.data.toLowerCase().localeCompare(b.data.toLowerCase());
  }
  appendSortable(container, dataId, initialSort = false) {
    let sorted = [];
    const sortOrder = container.getAttribute(this.ORDER);
    const sortData = container.querySelectorAll(`[${this.DATA}="${dataId}"]`);
    const comparableData = [...sortData].map((data) => {
      return { data: data.innerText, index: data.closest(`[${this.SORTABLE}]`) };
    });
    if (sortOrder === "asc" || initialSort && initialSort === "asc") {
      sorted = comparableData.sort(this.compare);
      container.setAttribute(this.ORDER, "desc");
    } else if (initialSort && initialSort === "desc") {
      comparableData.sort(this.compare);
      sorted = comparableData.reverse(this.compare);
      container.setAttribute(this.ORDER, "asc");
    } else {
      sorted = comparableData.reverse(this.compare);
      container.setAttribute(this.ORDER, "asc");
    }
    sorted.forEach((sort) => {
      sort.index.closest(`[${this.DATACONTAINER}]`).appendChild(sort.index);
    });
  }
  applySort() {
    const sortContainers = document.querySelectorAll(`[${this.CONTAINER}]`);
    sortContainers.forEach((container) => {
      const sortButtons = container.querySelectorAll(`[${this.BUTTON}]`);
      sortButtons.forEach((button) => {
        const dataId = button.getAttribute(this.BUTTON);
        const initialSort = container.getAttribute(this.INITIAL);
        if (initialSort) {
          container.setAttribute(this.ORDER, initialSort);
          this.appendSortable(container, dataId, initialSort);
        }
        button.addEventListener("click", (event) => {
          this.appendSortable(container, dataId);
        });
      });
    });
  }
};
__name(_Sort, "Sort");
let Sort = _Sort;
export {
  Sort as default
};
//# sourceMappingURL=sort.js.map
