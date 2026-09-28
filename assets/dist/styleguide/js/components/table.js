var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
var AttributeNames = /* @__PURE__ */ ((AttributeNames2) => {
  AttributeNames2["HasTableSort"] = "data-js-table-sort";
  AttributeNames2["HasTableFilter"] = "data-js-table-filter";
  AttributeNames2["HasMultidimensional"] = "data-js-table-multidimensional";
  AttributeNames2["TableElement"] = "data-js-table-element";
  AttributeNames2["TableScrollIndicatorContainer"] = "data-js-table-scroll-indicator-container";
  AttributeNames2["TableScrollIndicator"] = "data-js-table-scroll-indicator";
  AttributeNames2["TableHead"] = "data-js-table-head";
  AttributeNames2["TableBody"] = "data-js-table-body";
  AttributeNames2["TableCell"] = "data-js-table-cell";
  AttributeNames2["ColumnIndex"] = "data-js-column-index";
  AttributeNames2["RowIndex"] = "data-js-row-index";
  AttributeNames2["SortingOrder"] = "data-js-table-sort-order";
  AttributeNames2["FilterInput"] = "data-js-table-filter-input";
  AttributeNames2["SummaryRow"] = "data-js-table-summary-row";
  AttributeNames2["TableWrapper"] = "data-js-table-wrapper";
  return AttributeNames2;
})(AttributeNames || {});
const _TableSort = class _TableSort {
  constructor(tableConfig, itemsInstance) {
    this.tableConfig = tableConfig;
    this.itemsInstance = itemsInstance;
    this.setupSortingButtons();
  }
  tableConfig;
  itemsInstance;
  setupSortingButtons() {
    const sortingButtons = this.itemsInstance.getHeadingCells();
    sortingButtons.forEach((sortingButton) => {
      if (!sortingButton.hasAttribute(`${AttributeNames.SortingOrder}`)) {
        sortingButton.setAttribute(`${AttributeNames.SortingOrder}`, "desc");
      }
      sortingButton.addEventListener("click", (e) => {
        this.handleClick(sortingButton);
      });
    });
    return sortingButtons;
  }
  handleClick(sortingButton) {
    const nextSortOrder = this.getNextSortOrder(sortingButton);
    const sortingColumn = this.getSortingColumn(sortingButton);
    if (sortingColumn === null) {
      console.warn("Sorting column index is not defined.");
      return;
    }
    const dataCells = this.itemsInstance.getDataCellsFromColumnIndex(sortingColumn);
    const sortedDataCells = dataCells.sort((a, b) => {
      const aValue = a.textContent?.trim() ?? "";
      const bValue = b.textContent?.trim() ?? "";
      if (nextSortOrder === "asc") {
        return aValue.localeCompare(bValue, void 0, { numeric: true, sensitivity: "base" });
      } else {
        return bValue.localeCompare(aValue, void 0, { numeric: true, sensitivity: "base" });
      }
    });
    const sortedRows = sortedDataCells.map((dataCell) => dataCell.closest(`[${AttributeNames.RowIndex}]`)).filter((row) => row !== null);
    sortedRows.forEach((row) => {
      this.tableConfig.getTableBody().appendChild(row);
    });
  }
  getSortingColumn(sortingButton) {
    return sortingButton.hasAttribute(`${AttributeNames.ColumnIndex}`) ? parseInt(sortingButton.getAttribute(`${AttributeNames.ColumnIndex}`), 10) : null;
  }
  getNextSortOrder(sortingButton) {
    const states = ["asc", "desc"];
    const currentState = sortingButton.getAttribute(`${AttributeNames.SortingOrder}`);
    const currentIndex = states.indexOf(currentState);
    const nextState = states[(currentIndex + 1) % states.length];
    sortingButton.setAttribute(`${AttributeNames.SortingOrder}`, nextState);
    return nextState;
  }
};
__name(_TableSort, "TableSort");
let TableSort = _TableSort;
const _TableFilter = class _TableFilter {
  constructor(tableConfig, itemsInstance) {
    this.tableConfig = tableConfig;
    this.itemsInstance = itemsInstance;
    this.setupFilterInput();
  }
  tableConfig;
  itemsInstance;
  setupFilterInput() {
    const input = this.tableConfig.getRoot().querySelector(`[${AttributeNames.FilterInput}]`);
    input?.addEventListener("input", () => {
      this.handleInput(input.value);
    });
  }
  handleInput(query) {
    const lowerQuery = query.toLowerCase().trim();
    this.itemsInstance.getRows().forEach((row) => {
      const cells = Array.from(row.querySelectorAll(`[${AttributeNames.TableCell}]`));
      const rowText = cells.map((cell) => cell.textContent?.toLowerCase() ?? "").join(" ");
      row.hidden = !rowText.includes(lowerQuery);
    });
  }
};
__name(_TableFilter, "TableFilter");
let TableFilter = _TableFilter;
const _TableConfig = class _TableConfig {
  constructor(root) {
    this.root = root;
    this.isSortable = root.hasAttribute(`${AttributeNames.HasTableSort}`);
    this.isFilterable = root.hasAttribute(`${AttributeNames.HasTableFilter}`);
    this.tableWrapper = root.querySelector(`[${AttributeNames.TableWrapper}]`);
    this.tableElement = root.querySelector(`[${AttributeNames.TableElement}]`);
    this.scrollIndicatorContainer = root.querySelector(`[${AttributeNames.TableScrollIndicatorContainer}]`);
    this.scrollIndicatorElement = root.querySelector(`[${AttributeNames.TableScrollIndicator}]`);
    this.isMultidimensional = root.hasAttribute(`${AttributeNames.HasMultidimensional}`);
    this.hasSummaryRow = root.querySelector(`[${AttributeNames.SummaryRow}]`) ? true : false;
    this.tableBody = root.querySelector(`[${AttributeNames.TableBody}]`);
    this.tableHead = root.querySelector(`[${AttributeNames.TableHead}]`);
  }
  root;
  isSortable;
  isFilterable;
  tableElement;
  scrollIndicatorContainer;
  scrollIndicatorElement;
  isMultidimensional;
  hasSummaryRow;
  tableBody;
  tableHead;
  tableWrapper;
  getRoot() {
    return this.root;
  }
  getTableBody() {
    return this.tableBody;
  }
  getTableHead() {
    return this.tableHead;
  }
  getTableWrapper() {
    return this.tableWrapper;
  }
  getTableElement() {
    return this.tableElement;
  }
  getScrollIndicatorContainer() {
    return this.scrollIndicatorContainer;
  }
  getScrollIndicator() {
    return this.scrollIndicatorElement;
  }
  isTableSortable() {
    return this.isSortable;
  }
  isTableFilterable() {
    return this.isFilterable;
  }
  isTableMultidimensional() {
    return this.isMultidimensional;
  }
  hasTableSummaryRow() {
    return this.hasSummaryRow;
  }
};
__name(_TableConfig, "TableConfig");
let TableConfig = _TableConfig;
const _Items = class _Items {
  constructor(tableConfig) {
    this.tableConfig = tableConfig;
  }
  tableConfig;
  targetWithoutSummaryRow = `[${AttributeNames.RowIndex}]:not([${AttributeNames.SummaryRow}])`;
  getHeadingCells() {
    return Array.from(this.tableConfig.getTableHead().querySelectorAll(`[${AttributeNames.TableCell}]`));
  }
  getDataCells() {
    return Array.from(this.tableConfig.getTableBody().querySelectorAll(`${this.targetWithoutSummaryRow} [${AttributeNames.TableCell}]`));
  }
  getRows() {
    return Array.from(this.tableConfig.getTableBody().querySelectorAll(this.targetWithoutSummaryRow));
  }
  getDataCellsFromColumnIndex(columnIndex) {
    return Array.from(this.tableConfig.getTableBody().querySelectorAll(`${this.targetWithoutSummaryRow} [${AttributeNames.TableCell}][${AttributeNames.ColumnIndex}="${columnIndex}"]`));
  }
  getSummaryRow() {
    return this.tableConfig.getTableBody().querySelector(`[${AttributeNames.SummaryRow}]`);
  }
};
__name(_Items, "Items");
let Items = _Items;
const _TableScrollIndicator = class _TableScrollIndicator {
  constructor(tableConfig) {
    this.tableConfig = tableConfig;
    this.wrapper = this.tableConfig.getTableWrapper();
    this.table = this.tableConfig.getTableElement();
    this.indicator = this.tableConfig.getScrollIndicator();
    this.indicatorContainer = this.tableConfig.getScrollIndicatorContainer();
    this.init();
  }
  tableConfig;
  wrapper;
  table;
  indicator;
  indicatorContainer;
  onPointerDown = /* @__PURE__ */ __name((event) => this.startDrag(event), "onPointerDown");
  onScroll = /* @__PURE__ */ __name(() => this.scheduleSync(), "onScroll");
  onPointerMove = /* @__PURE__ */ __name((event) => this.handlePointerMove(event), "onPointerMove");
  onPointerUp = /* @__PURE__ */ __name(() => this.stopDrag(), "onPointerUp");
  resizeObserver = new ResizeObserver(() => this.scheduleSync());
  pointerStartX = 0;
  pointerStartLeft = 0;
  isDragging = false;
  syncScheduled = false;
  init() {
    this.indicator.style.marginLeft = "0px";
    this.wrapper.addEventListener("scroll", this.onScroll, { passive: true });
    this.indicator.addEventListener("pointerdown", this.onPointerDown);
    window.addEventListener("pointermove", this.onPointerMove);
    window.addEventListener("pointerup", this.onPointerUp);
    window.addEventListener("pointercancel", this.onPointerUp);
    this.resizeObserver.observe(this.wrapper);
    this.resizeObserver.observe(this.table);
    this.scheduleSync();
  }
  scheduleSync() {
    if (this.syncScheduled) {
      return;
    }
    this.syncScheduled = true;
    requestAnimationFrame(() => {
      this.syncScheduled = false;
      this.syncIndicator();
    });
  }
  startDrag(event) {
    event.preventDefault();
    this.isDragging = true;
    this.pointerStartX = event.clientX;
    this.pointerStartLeft = Number.parseFloat(this.indicator.style.marginLeft || "0") || 0;
    this.indicator.setPointerCapture?.(event.pointerId);
  }
  handlePointerMove(event) {
    if (!this.isDragging) {
      return;
    }
    const maxTrack = Math.max(this.indicatorContainer.clientWidth - this.indicator.offsetWidth, 0);
    if (maxTrack === 0) {
      return;
    }
    const delta = event.clientX - this.pointerStartX;
    const targetLeft = Math.min(Math.max(this.pointerStartLeft + delta, 0), maxTrack);
    const maxScroll = Math.max(this.table.scrollWidth - this.wrapper.clientWidth, 0);
    this.indicator.style.marginLeft = `${targetLeft}px`;
    this.wrapper.scrollLeft = maxScroll * (targetLeft / (maxTrack || 1));
  }
  stopDrag() {
    if (!this.isDragging) {
      return;
    }
    this.isDragging = false;
    this.pointerStartX = 0;
    this.pointerStartLeft = 0;
  }
  syncIndicator() {
    const maxScroll = Math.max(this.table.scrollWidth - this.wrapper.clientWidth, 0);
    const shouldShow = maxScroll > 0;
    this.indicatorContainer.classList.toggle("u-display--none", !shouldShow);
    this.indicator.classList.toggle("u-display--none", !shouldShow);
    if (!shouldShow) {
      this.indicator.style.marginLeft = "0px";
      return;
    }
    const indicatorWidth = Math.max(this.wrapper.clientWidth / this.table.scrollWidth * 100, 15);
    this.indicator.style.width = `${indicatorWidth}%`;
    const maxTrack = Math.max(this.indicatorContainer.clientWidth - this.indicator.offsetWidth, 0);
    const ratio = Math.min(Math.max(this.wrapper.scrollLeft / maxScroll, 0), 1);
    this.indicator.style.marginLeft = `${ratio * maxTrack}px`;
  }
  destroy() {
    this.wrapper.removeEventListener("scroll", this.onScroll);
    this.indicator.removeEventListener("pointerdown", this.onPointerDown);
    window.removeEventListener("pointermove", this.onPointerMove);
    window.removeEventListener("pointerup", this.onPointerUp);
    window.removeEventListener("pointercancel", this.onPointerUp);
    this.resizeObserver.disconnect();
  }
};
__name(_TableScrollIndicator, "TableScrollIndicator");
let TableScrollIndicator = _TableScrollIndicator;
const _TableFactory = class _TableFactory {
  create(table) {
    const config = new TableConfig(table);
    if (this.hasRequiredElements(config)) {
      console.error("Table element, table body, or table head is missing.");
      return;
    }
    const itemsInstance = new Items(config);
    if (config.isTableSortable()) {
      new TableSort(config, itemsInstance);
    }
    if (config.isTableFilterable()) {
      new TableFilter(config, itemsInstance);
    }
    if (config.getScrollIndicator() && config.getScrollIndicatorContainer() && config.getTableElement() && config.getTableWrapper()) {
      new TableScrollIndicator(config);
    }
  }
  hasRequiredElements(config) {
    return !config.getTableElement() || !config.getTableBody() || !config.getTableHead();
  }
  static getInstance() {
    return _TableFactory.factoryInstance ?? (_TableFactory.factoryInstance = new _TableFactory());
  }
};
__name(_TableFactory, "TableFactory");
__publicField(_TableFactory, "factoryInstance", null);
let TableFactory = _TableFactory;
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-js-table]").forEach((table) => {
      TableFactory.getInstance().create(table);
    });
  });
}
__name(init, "init");
init();
//# sourceMappingURL=table.js.map
