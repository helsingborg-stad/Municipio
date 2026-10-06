var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const PAGINATION_ATTRIBUTES = {
  target: "data-js-pagination-target",
  async: "data-js-pagination-async",
  root: "data-js-pagination",
  container: "data-js-pagination-container",
  item: "data-js-pagination-item",
  itemTitle: "data-js-pagination-item-title",
  itemPage: "data-js-pagination-page",
  indexLink: "data-js-pagination-index",
  previous: "data-js-pagination-prev",
  next: "data-js-pagination-next",
  sort: "data-js-pagination-sort",
  linksContainer: "js-table-pagination--links",
  currentPage: "js-table-pagination--current"
};
const _Pagination = class _Pagination {
  constructor(container, index, urlHandler, sorter, renderer, navigation, asyncItemSync, paginationElements, paginationAttributes, paginationItems) {
    this.container = container;
    this.urlHandler = urlHandler;
    this.sorter = sorter;
    this.renderer = renderer;
    this.navigation = navigation;
    this.asyncItemSync = asyncItemSync;
    this.paginationElements = paginationElements;
    this.paginationAttributes = paginationAttributes;
    this.paginationItems = paginationItems;
    this.activeList = this.sorter.getSortedList(this.paginationItems, "default", this.paginationAttributes.randomize);
    this.currentPage = this.clampPage(this.urlHandler.getCurrentPage());
    this.currentSortMode = "default";
    this.setPageNumberAttribute();
    this.bindControlListeners();
    this.bindSortListener();
    this.bindPopstateListener();
    this.refresh();
    this.bindAsyncItemSync();
    const instanceId = `pagination-${index}`;
    this.container.dataset.paginationInstance = instanceId;
  }
  container;
  urlHandler;
  sorter;
  renderer;
  navigation;
  asyncItemSync;
  paginationElements;
  paginationAttributes;
  paginationItems;
  activeList;
  currentPage;
  currentSortMode;
  paginateSetCurrent(current = 1) {
    this.setCurrentPage(current, true);
    this.refresh();
  }
  bindControlListeners() {
    this.navigation.bindListeners({
      onNavigate: /* @__PURE__ */ __name((action, pageNumber) => {
        if (action === "next") {
          this.setCurrentPage(this.currentPage + 1, true);
          this.refresh();
          this.renderer.scrollToTop();
          return;
        }
        if (action === "previous") {
          this.setCurrentPage(this.currentPage - 1, true);
          this.refresh();
          this.renderer.scrollToTop();
          return;
        }
        if (typeof pageNumber !== "number") {
          return;
        }
        this.setCurrentPage(pageNumber, true);
        this.refresh();
        this.renderer.scrollToTop();
        this.renderer.setFocusToFirstItem();
      }, "onNavigate")
    });
  }
  bindSortListener() {
    const sortElement = this.paginationElements.sortElement;
    if (!sortElement) {
      return;
    }
    const urlSortMode = this.urlHandler.getSortMode();
    const initialSortMode = this.sorter.resolveSortMode(urlSortMode ?? sortElement.value ?? "default");
    sortElement.value = initialSortMode;
    this.applySort(initialSortMode, false);
    sortElement.addEventListener("change", (event) => {
      const selectedSortMode = this.sorter.resolveSortMode(event.target.value);
      this.applySort(selectedSortMode, true);
      this.refresh();
    });
  }
  bindPopstateListener() {
    window.addEventListener("popstate", () => {
      const pageFromUrl = this.clampPage(this.urlHandler.getCurrentPage());
      if (pageFromUrl !== this.currentPage) {
        this.setCurrentPage(pageFromUrl, false);
        this.refresh();
      }
    });
  }
  refresh() {
    this.asyncItemSync?.pause();
    this.container.setAttribute(PAGINATION_ATTRIBUTES.currentPage, this.currentPage.toString());
    this.renderer.renderPageItems(this.paginateList(this.activeList));
    this.renderer.renderLinks(this.paginatePages(), this.currentPage);
    this.navigation.updateButtonState(this.currentPage, this.paginatePages());
    this.asyncItemSync?.resume();
  }
  applySort(mode, resetPage) {
    this.currentSortMode = mode;
    this.activeList = this.sorter.getSortedList(this.paginationItems, mode, this.paginationAttributes.randomize);
    this.setPageNumberAttribute();
    this.urlHandler.setSortMode(mode === "default" ? "default" : mode);
    if (resetPage) {
      this.setCurrentPage(1, true);
    }
  }
  setPageNumberAttribute() {
    this.activeList.forEach((item, index) => {
      const pageNumber = Math.floor(index / this.paginationAttributes.perPage) + 1;
      item.setAttribute(PAGINATION_ATTRIBUTES.itemPage, pageNumber.toString());
    });
  }
  paginatePages() {
    const numberOfPages = Math.ceil(this.activeList.length / this.paginationAttributes.perPage);
    if (this.paginationAttributes.maxPages && numberOfPages > this.paginationAttributes.maxPages) {
      return this.paginationAttributes.maxPages;
    }
    return numberOfPages;
  }
  paginateList(list) {
    const firstIndex = (this.currentPage - 1) * this.paginationAttributes.perPage;
    const lastIndex = this.currentPage * this.paginationAttributes.perPage;
    return Array.from(list).slice(firstIndex, lastIndex);
  }
  setCurrentPage(pageNumber, updateUrl) {
    const nextPage = this.clampPage(pageNumber);
    this.currentPage = nextPage;
    if (updateUrl) {
      this.urlHandler.setCurrentPage(nextPage, false);
    }
  }
  clampPage(pageNumber) {
    if (pageNumber <= 1) {
      return 1;
    }
    const maxPage = this.paginatePages() || 1;
    if (pageNumber > maxPage) {
      return maxPage;
    }
    return pageNumber;
  }
  bindAsyncItemSync() {
    if (!this.asyncItemSync) {
      return;
    }
    this.asyncItemSync.start((itemsChange) => {
      this.handleAsyncItemsUpdated(itemsChange);
    });
  }
  handleAsyncItemsUpdated(itemsChange) {
    const nextItems = this.getNextPaginationItems(itemsChange);
    if (this.isSameItemsReferenceOrder(nextItems, this.paginationItems)) {
      return;
    }
    this.paginationItems = nextItems;
    this.activeList = this.sorter.getSortedList(this.paginationItems, this.currentSortMode, this.paginationAttributes.randomize);
    this.setCurrentPage(this.currentPage, false);
    this.setPageNumberAttribute();
    this.refresh();
  }
  getNextPaginationItems(itemsChange) {
    const removedSet = new Set(itemsChange.removedItems);
    const nextItems = this.paginationItems.filter((item) => !removedSet.has(item));
    itemsChange.addedItems.forEach((item) => {
      if (!nextItems.includes(item)) {
        nextItems.push(item);
      }
    });
    return nextItems;
  }
  isSameItemsReferenceOrder(a, b) {
    if (a.length !== b.length) {
      return false;
    }
    for (let index = 0; index < a.length; index++) {
      if (a[index] !== b[index]) {
        return false;
      }
    }
    return true;
  }
};
__name(_Pagination, "Pagination");
let Pagination = _Pagination;
const _PaginationUrlState = class _PaginationUrlState {
  constructor(pageQueryParameter = "pagenum", sortQueryParameter = "sortby") {
    this.pageQueryParameter = pageQueryParameter;
    this.sortQueryParameter = sortQueryParameter;
  }
  pageQueryParameter;
  sortQueryParameter;
  getCurrentPage() {
    const pageValue = new URLSearchParams(window.location.search).get(this.pageQueryParameter);
    const currentPage = pageValue ? parseInt(pageValue, 10) : 1;
    return Number.isNaN(currentPage) ? 1 : currentPage;
  }
  setCurrentPage(pageNumber, useReplaceState = false) {
    const urlSearchParams = new URLSearchParams(window.location.search);
    urlSearchParams.set(this.pageQueryParameter, pageNumber.toString());
    this.write(urlSearchParams, useReplaceState);
  }
  getSortMode() {
    return new URLSearchParams(window.location.search).get(this.sortQueryParameter);
  }
  setSortMode(sortMode) {
    const urlSearchParams = new URLSearchParams(window.location.search);
    if (sortMode) {
      urlSearchParams.set(this.sortQueryParameter, sortMode);
    } else {
      urlSearchParams.delete(this.sortQueryParameter);
    }
    this.write(urlSearchParams, true);
  }
  write(urlSearchParams, useReplaceState) {
    const queryString = urlSearchParams.toString();
    const updatedUrl = `${window.location.pathname}${queryString ? `?${queryString}` : ""}`;
    if (useReplaceState) {
      history.replaceState({}, "", updatedUrl);
      return;
    }
    history.pushState({}, "", updatedUrl);
  }
};
__name(_PaginationUrlState, "PaginationUrlState");
let PaginationUrlState = _PaginationUrlState;
const _PaginationSorter = class _PaginationSorter {
  resolveSortMode(value) {
    if (value === "alphabetical" || value === "random") {
      return value;
    }
    return "default";
  }
  getSortedList(sourceList, mode, randomizeByDefault) {
    const defaultList = randomizeByDefault ? this.randomizeList(sourceList) : [...sourceList];
    if (mode === "random") {
      return this.randomizeList(sourceList);
    }
    if (mode === "alphabetical") {
      return [...sourceList].sort((a, b) => {
        const firstTitle = a.getAttribute(PAGINATION_ATTRIBUTES.itemTitle) || "";
        const secondTitle = b.getAttribute(PAGINATION_ATTRIBUTES.itemTitle) || "";
        return firstTitle.localeCompare(secondTitle);
      });
    }
    return defaultList;
  }
  randomizeList(list) {
    return [...list].sort(() => Math.random() - 0.5);
  }
};
__name(_PaginationSorter, "PaginationSorter");
let PaginationSorter = _PaginationSorter;
const _PaginationDomRenderer = class _PaginationDomRenderer {
  constructor(elements, attributes) {
    this.elements = elements;
    this.attributes = attributes;
  }
  elements;
  attributes;
  renderPageItems(listItems) {
    const isAsyncPagination = this.elements.container.hasAttribute(PAGINATION_ATTRIBUTES.async) || this.elements.paginationContainer.hasAttribute(PAGINATION_ATTRIBUTES.async);
    if (this.attributes.keepDOM && !isAsyncPagination) {
      Array.from(this.elements.listContainer.children).forEach((element) => {
        if (!element.querySelector(`[${PAGINATION_ATTRIBUTES.sort}]`)) {
          element.classList.add("u-display--none");
        }
      });
      listItems.forEach((element) => {
        element.classList.remove("u-display--none");
        this.elements.listContainer.appendChild(element);
      });
      return;
    }
    this.elements.listContainer.innerHTML = "";
    listItems.forEach((element) => {
      this.elements.listContainer.appendChild(element);
    });
  }
  renderLinks(numberOfPages, currentPage) {
    this.elements.paginationContainer.classList.remove("u-display--none");
    if (numberOfPages <= 1 || !this.elements.linkTemplate) {
      this.elements.paginationContainer.classList.add("u-display--none");
      this.elements.linksContainer.innerHTML = "";
      return;
    }
    this.elements.linksContainer.innerHTML = "";
    const range = this.getPageRangeToRender(currentPage, numberOfPages);
    for (let pageNumber = range.start; pageNumber <= range.end; pageNumber++) {
      const linkElement = this.elements.linkTemplate.cloneNode(true);
      linkElement.setAttribute(PAGINATION_ATTRIBUTES.indexLink, pageNumber.toString());
      const buttonLabel = linkElement.querySelector(".c-button__label-text");
      if (buttonLabel) {
        buttonLabel.innerHTML = pageNumber.toString();
      }
      const buttonElement = linkElement.querySelector(".c-button");
      if (buttonElement) {
        buttonElement.classList.remove("c-button__filled--primary");
        buttonElement.classList.add("c-button__filled--default");
        if (pageNumber === currentPage) {
          buttonElement.classList.add("c-button__filled--primary");
          buttonElement.classList.remove("c-button__filled--default");
        }
      }
      this.elements.linksContainer.appendChild(linkElement);
    }
  }
  setFocusToFirstItem() {
    const firstVisibleItem = this.elements.listContainer.querySelector(`[${PAGINATION_ATTRIBUTES.item}]:first-child`);
    firstVisibleItem?.focus();
  }
  scrollToTop() {
    const offset = document.querySelector(".c-header--sticky") ? 100 : 0;
    const elementPosition = this.elements.container.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - offset;
    window.scrollTo({ top: offsetPosition });
  }
  getPageRangeToRender(currentPage, numberOfPages) {
    const fallbackWindow = 100;
    const windowSize = this.attributes.pagesToShow || fallbackWindow;
    const halfWindow = Math.floor(windowSize / 2);
    let start = Math.max(currentPage - halfWindow, 1);
    let end = Math.min(currentPage + halfWindow, numberOfPages);
    if (start === 1) {
      end = Math.min(numberOfPages, start + windowSize);
    } else if (end === numberOfPages) {
      start = Math.max(1, end - windowSize);
    }
    return { start, end };
  }
};
__name(_PaginationDomRenderer, "PaginationDomRenderer");
let PaginationDomRenderer = _PaginationDomRenderer;
const _PaginationNavigation = class _PaginationNavigation {
  constructor(elements) {
    this.elements = elements;
  }
  elements;
  bindListeners(callbacks) {
    this.elements.nextButton?.addEventListener("click", (event) => {
      event.preventDefault();
      callbacks.onNavigate("next");
    });
    this.elements.prevButton?.addEventListener("click", (event) => {
      event.preventDefault();
      callbacks.onNavigate("previous");
    });
    this.elements.linksContainer.addEventListener("click", (event) => {
      event.preventDefault();
      const target = event.target.closest(`[${PAGINATION_ATTRIBUTES.indexLink}]`);
      if (!target) {
        return;
      }
      const nextPage = target.getAttribute(PAGINATION_ATTRIBUTES.indexLink);
      if (!nextPage) {
        return;
      }
      const parsedPage = parseInt(nextPage, 10);
      if (Number.isNaN(parsedPage)) {
        return;
      }
      callbacks.onNavigate("index", parsedPage);
    });
  }
  updateButtonState(currentPage, numberOfPages) {
    this.elements.nextButton?.toggleAttribute("disabled", currentPage >= numberOfPages);
    this.elements.prevButton?.toggleAttribute("disabled", currentPage <= 1);
  }
};
__name(_PaginationNavigation, "PaginationNavigation");
let PaginationNavigation = _PaginationNavigation;
const _PaginationAsyncItemSync = class _PaginationAsyncItemSync {
  constructor(listContainer) {
    this.listContainer = listContainer;
  }
  listContainer;
  observer = null;
  start(onItemsChanged) {
    if (typeof MutationObserver === "undefined") {
      return;
    }
    this.observer = new MutationObserver((mutations) => {
      const itemsChange = this.getItemsChange(mutations);
      if (itemsChange.addedItems.length === 0 && itemsChange.removedItems.length === 0) {
        return;
      }
      onItemsChanged(itemsChange);
    });
    this.resume();
  }
  pause() {
    this.observer?.disconnect();
  }
  resume() {
    this.observer?.observe(this.listContainer, { childList: true, subtree: true });
  }
  getItemsChange(mutations) {
    const addedItems = this.extractItemsFromMutations(mutations, "addedNodes");
    const removedItems = this.extractItemsFromMutations(mutations, "removedNodes");
    return {
      addedItems,
      removedItems
    };
  }
  extractItemsFromMutations(mutations, nodeListKey) {
    const extractedItems = [];
    mutations.forEach((mutation) => {
      mutation[nodeListKey].forEach((node) => {
        if (!(node instanceof HTMLElement)) {
          return;
        }
        if (node.hasAttribute(PAGINATION_ATTRIBUTES.item)) {
          extractedItems.push(node);
        }
        extractedItems.push(...[...node.querySelectorAll(`[${PAGINATION_ATTRIBUTES.item}]`)]);
      });
    });
    return extractedItems;
  }
};
__name(_PaginationAsyncItemSync, "PaginationAsyncItemSync");
let PaginationAsyncItemSync = _PaginationAsyncItemSync;
const _PaginationFactory = class _PaginationFactory {
  create(container, index) {
    const initialization = this.resolve(container);
    if (!initialization) {
      return null;
    }
    const isAsyncPagination = container.hasAttribute(PAGINATION_ATTRIBUTES.async) || initialization.elements.paginationContainer.hasAttribute(PAGINATION_ATTRIBUTES.async);
    const asyncItemSync = isAsyncPagination ? new PaginationAsyncItemSync(initialization.elements.listContainer) : null;
    return new Pagination(
      container,
      index,
      new PaginationUrlState(),
      new PaginationSorter(),
      new PaginationDomRenderer(initialization.elements, initialization.attributes),
      new PaginationNavigation(initialization.elements),
      asyncItemSync,
      initialization.elements,
      initialization.attributes,
      initialization.paginationItems
    );
  }
  resolve(container) {
    const elements = this.resolveElements(container);
    if (!elements) {
      return null;
    }
    return {
      elements,
      attributes: this.getAttributes(elements.paginationContainer),
      paginationItems: [...container.querySelectorAll(`[${PAGINATION_ATTRIBUTES.item}]`)]
    };
  }
  resolveElements(container) {
    const paginationContainer = container.querySelector(`[${PAGINATION_ATTRIBUTES.root}]`);
    const listContainer = container.querySelector(`[${PAGINATION_ATTRIBUTES.container}]`);
    const linksContainer = container.querySelector(`[${PAGINATION_ATTRIBUTES.linksContainer}]`);
    if (!paginationContainer || !listContainer || !linksContainer) {
      return null;
    }
    const linkTemplate = container.querySelector(`[${PAGINATION_ATTRIBUTES.indexLink}]`);
    linkTemplate?.classList.remove("c-pagination__item--is-active");
    const sortElement = container.querySelector(`[${PAGINATION_ATTRIBUTES.sort}] select`);
    return {
      container,
      paginationContainer,
      listContainer,
      linksContainer,
      prevButton: container.querySelector(`[${PAGINATION_ATTRIBUTES.previous}]`),
      nextButton: container.querySelector(`[${PAGINATION_ATTRIBUTES.next}]`),
      linkTemplate,
      sortElement
    };
  }
  getAttributes(paginationContainer) {
    const perPage = paginationContainer.getAttribute("data-js-pagination-per-page");
    const maxPages = paginationContainer.getAttribute("data-js-pagination-max-pages");
    const randomize = paginationContainer.hasAttribute("data-js-pagination-randomize-order");
    const keepDOM = paginationContainer.hasAttribute("data-js-pagination-keep-dom");
    const pagesToShow = paginationContainer.hasAttribute("data-js-pagination-pages-to-show") ? parseInt(paginationContainer.getAttribute("data-js-pagination-pages-to-show") ?? "0", 10) : 0;
    return {
      perPage: perPage ? parseInt(perPage, 10) : 10,
      maxPages: maxPages ? parseInt(maxPages, 10) : 0,
      randomize,
      keepDOM,
      pagesToShow: pagesToShow > 0 ? pagesToShow % 2 === 0 ? pagesToShow : pagesToShow + 1 : 0
    };
  }
};
__name(_PaginationFactory, "PaginationFactory");
let PaginationFactory = _PaginationFactory;
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    const paginations = [...document.querySelectorAll(`[${PAGINATION_ATTRIBUTES.target}]`)];
    const paginationFactory = new PaginationFactory();
    paginations.forEach((pagination, index) => {
      paginationFactory.create(pagination, index + 1);
    });
  }, { once: true });
}
__name(init, "init");
init();
//# sourceMappingURL=pagination.js.map
