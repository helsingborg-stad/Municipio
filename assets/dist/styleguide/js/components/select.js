var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _Select = class _Select {
  element;
  selectElement;
  dropdownElement;
  actionOverlayElement;
  dropdownOptionElements;
  clearButton;
  dropDownElement;
  expandLessIcon;
  expandMoreIcon;
  optionTemplate;
  placeholderText;
  searchFieldElement = null;
  constructor(element) {
    this.element = element;
    this.selectElement = this.getSelectElement();
    this.dropdownElement = this.getDropdownElement();
    this.actionOverlayElement = this.getActionOverlayElement();
    this.dropdownOptionElements = this.getDropdownOptionElements();
    this.dropDownElement = this.element.querySelector(`[${"data-js-dropdown-element"}]`);
    this.clearButton = this.element.querySelector(`[${"data-js-select-clear"}]`);
    this.expandLessIcon = this.element.querySelector(`.${"c-icon--expand-less"}`);
    this.expandMoreIcon = this.element.querySelector(`.${"c-icon--expand-more"}`);
    this.placeholderText = this.element.querySelector(`[${"data-js-placeholder"}]`)?.getAttribute(
      "data-js-placeholder"
      /* placeholderAttribute */
    ) || "";
    this.optionTemplate = this.element.querySelector("template");
    this.searchFieldElement = this.element.querySelector(`[${"data-js-select-search-input"}]`);
    this.setupEventListeners();
  }
  setupEventListeners() {
    this.setupOptionsObserver();
    this.selectElement.addEventListener("focusin", (e) => this.triggerDropdown(e));
    this.element.addEventListener("focusout", (e) => this.triggerBlurEvent(e));
    this.selectElement.addEventListener("change", () => this.disableMultiSelectOptionsWhenMaxSelectionsReached());
    this.selectElement.addEventListener("change", () => this.updatePlaceholderText());
    this.selectElement.addEventListener("change", () => this.setIsEmptyState());
    this.selectElement.addEventListener("change", () => this.updateClearButtonVisibilityState());
    this.selectElement.addEventListener("change", () => this.closeSingleSelectDropdown());
    this.actionOverlayElement.addEventListener("keydown", (event) => this.openDropdownOnSpacebar(event));
    this.clearButton?.addEventListener("click", () => this.setSingleSelectValue(null));
    this.element.addEventListener("classListChange", () => this.updateDropdownAriaStateOnTopElementClassListChange());
    this.element.addEventListener("classListChange", () => this.updateExpandIconsAriaStateOnTopElementClassListChange());
    this.actionOverlayElement.addEventListener("click", () => this.focusSearchInput());
    this.searchFieldElement?.addEventListener("input", (e) => this.handleSearchInput(e));
    this.runFunctionsRequiredForInitialization();
  }
  focusSearchInput() {
    if (this.searchFieldElement) {
      this.searchFieldElement.value = "";
      setTimeout(() => {
        this.searchFieldElement?.focus();
      }, 100);
    }
  }
  handleSearchInput(e) {
    const target = e.target;
    const searchTerm = target.value.toLowerCase().trim();
    const optionElements = this.dropdownElement.querySelectorAll(`[${"data-js-dropdown-option"}]`);
    let allHidden = true;
    optionElements.forEach((optionElement) => {
      const optionLabelElement = optionElement.querySelector(".c-select__option-label");
      const optionLabelText = optionLabelElement ? optionLabelElement.textContent?.toLowerCase() || "" : "";
      if (optionLabelText.includes(searchTerm)) {
        optionElement.style.display = "";
        allHidden = false;
      } else {
        optionElement.style.display = "none";
      }
    });
    if (allHidden) {
      this.element.classList.add("search-no-results");
    } else {
      this.element.classList.remove("search-no-results");
    }
  }
  isIos() {
    return this.element.classList.contains("is-ios");
  }
  isAndroid() {
    return this.element.classList.contains("is-android");
  }
  updateExpandIconsAriaStateOnTopElementClassListChange() {
    const isOpen = this.element.classList.contains("is-open");
    this.expandMoreIcon.setAttribute("aria-hidden", Boolean(isOpen).toString());
    this.expandLessIcon.setAttribute("aria-hidden", Boolean(!isOpen).toString());
  }
  updateDropdownAriaStateOnTopElementClassListChange() {
    const isOpen = this.element.classList.contains("is-open");
    this.dropDownElement.setAttribute("aria-hidden", Boolean(!isOpen).toString());
  }
  closeSingleSelectDropdown() {
    if (!this.isMultiSelect()) {
      const element = this.element.classList;
      if (element.contains("is-open")) {
        if (this.searchFieldElement) {
          this.searchFieldElement.value = "";
        }
        element.remove("is-open");
      }
    }
  }
  selectOptionOnElementClick(optionElement) {
    const newValue = optionElement.getAttribute(
      "data-js-dropdown-option"
      /* selectDropdownOptionElementAttribute */
    );
    if (newValue === null) {
      return;
    }
    if (this.isMultiSelect()) {
      this.setMultiSelectValue(newValue);
    } else {
      this.setSingleSelectValue(newValue);
    }
  }
  selectOptionOnDropdownOptionElementKeyDown(event) {
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      event.target.click();
    }
  }
  openDropdownOnSpacebar(event) {
    if (event.key === " ") {
      event.preventDefault();
      this.actionOverlayElement.click();
    }
  }
  runFunctionsRequiredForInitialization() {
    this.disableMultiSelectOptionsWhenMaxSelectionsReached();
    this.updateSelectedItemsListeners();
    this.updateVisualRepresentation();
    this.setIsEmptyState();
    this.updateClearButtonVisibilityState();
    this.updatePlaceholderText();
    this.disableMultiSelectOptionsWhenMaxSelectionsReached();
    this.setupClassListChangeEventDispatcher();
  }
  setupClassListChangeEventDispatcher() {
    const classListChangeMutationObserver = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => mutation.attributeName === "class" && mutation.target.dispatchEvent(new Event("classListChange")));
    });
    classListChangeMutationObserver.observe(this.element, { attributes: true });
  }
  // This function is used to trigger the dropdown the label is clicked
  triggerDropdown(e) {
    if (this.isIos() || this.isAndroid()) {
      return;
    }
    this.actionOverlayElement.click();
    this.actionOverlayElement.focus();
  }
  // This method is used to trigger the blur event on the select element when the focus is moved outside of it
  triggerBlurEvent(e) {
    const relatedTarget = e.relatedTarget;
    if (!relatedTarget || !this.element.contains(relatedTarget)) {
      this.selectElement.dispatchEvent(new Event("blur"));
    }
  }
  disableMultiSelectOptionsWhenMaxSelectionsReached() {
    if (!this.isMultiSelect()) return;
    const limitReached = this.maxSelectionsReached();
    const optionElements = this.selectElement.querySelectorAll("option");
    optionElements.forEach((optionElement) => {
      const disabled = limitReached && !optionElement.selected;
      const optionListElementSelector = `[${"data-js-dropdown-option"}="${optionElement.value}"]`;
      const optionListElement = this.dropdownElement.querySelector(optionListElementSelector);
      optionElement.disabled = disabled;
      optionListElement?.setAttribute("aria-disabled", disabled ? "true" : "false");
    });
  }
  updatePlaceholderText() {
    const optionElements = this.selectElement.querySelectorAll("option:checked");
    const placeholderText = Array.from(optionElements).map((option) => option.textContent?.trim()).join(", ");
    this.actionOverlayElement.textContent = placeholderText ? placeholderText : this.placeholderText;
  }
  updateSelectedItemsListeners(updatedVisualOptionsList = false) {
    const visualOptionsList = updatedVisualOptionsList ? updatedVisualOptionsList : this.getVisualOptionsList();
    if (visualOptionsList.length) {
      visualOptionsList.forEach((optionElement) => {
        optionElement.addEventListener("click", () => this.selectOptionOnElementClick(optionElement));
        optionElement.addEventListener("keydown", (event) => this.selectOptionOnDropdownOptionElementKeyDown(event));
      });
    }
  }
  updateClearButtonVisibilityState() {
    const clearButton = this.element.querySelector(`[${"data-js-select-clear"}]`);
    if (!clearButton) return;
    if (this.selectElement.value === "") {
      clearButton?.setAttribute("aria-hidden", "true");
    } else {
      clearButton?.setAttribute("aria-hidden", "false");
    }
  }
  setMultiSelectValue(newValue) {
    const selectedValues = this.getSelectedValues();
    if (selectedValues.includes(newValue)) {
      selectedValues.splice(selectedValues.indexOf(newValue), 1);
      this.deSelectOption(newValue);
    } else if (!this.maxSelectionsReached()) {
      selectedValues.push(newValue);
      this.selectOption(newValue);
    }
    selectedValues.forEach((value) => {
      const option = this.dropdownElement.querySelector(`[${"data-js-dropdown-option"}="${value}"]`);
      if (option instanceof HTMLElement) {
        option.classList.add(
          "is-selected"
          /* activeOptionCssClass */
        );
        option.setAttribute("aria-selected", "true");
      }
    });
    this.setIsEmptyState();
  }
  maxSelectionsReached() {
    const maxSelections = this.getMaxSelections();
    return maxSelections > 0 && this.getSelectedValues().length >= maxSelections;
  }
  getMaxSelections() {
    const maxSelections = this.selectElement.getAttribute(
      "data-js-select-max"
      /* maxSelectionsAttribute */
    );
    return maxSelections ? parseInt(maxSelections) : 0;
  }
  selectOption(value) {
    const option = this.getOptionElementByValue(value);
    if (option) {
      option.selected = true;
      option.setAttribute("selected", "selected");
      this.dispatchSelectChangeEvent();
    }
  }
  deSelectOption(value) {
    const option = this.getOptionElementByValue(value);
    if (option) {
      option.selected = false;
      option.removeAttribute("selected");
      this.dispatchSelectChangeEvent();
    }
  }
  getOptionElementByValue(value) {
    for (let i = 0; i < this.selectElement.options.length; i++) {
      const option = this.selectElement.options[i];
      if (option.value === value) {
        return option;
      }
    }
  }
  dispatchSelectChangeEvent() {
    this.selectElement.dispatchEvent(new Event("change"));
  }
  setSingleSelectValue(newValue) {
    this.selectElement.value = newValue || "";
    this.dispatchSelectChangeEvent();
  }
  setIsEmptyState() {
    if (this.selectElement.value === "") {
      this.element.classList.add(
        "is-empty"
        /* emptySelectCssClass */
      );
    } else {
      this.element.classList.remove(
        "is-empty"
        /* emptySelectCssClass */
      );
    }
  }
  updateVisualRepresentation() {
    this.selectElement.addEventListener("change", () => {
      this.resetDropdownElement(this.dropdownElement);
      const selectedValues = this.getSelectedValues();
      if (selectedValues.length) {
        selectedValues.forEach((value) => {
          const option = this.dropdownElement.querySelector(`[${"data-js-dropdown-option"}="${value}"]`);
          if (option instanceof HTMLElement) {
            option.classList.add(
              "is-selected"
              /* activeOptionCssClass */
            );
            option.setAttribute("aria-selected", "true");
          }
        });
      }
    });
  }
  resetDropdownElement(dropdownElement) {
    const options = dropdownElement.querySelectorAll(`[${"data-js-dropdown-option"}]`);
    if (options.length) {
      options.forEach((option) => {
        option.classList.remove(
          "is-selected"
          /* activeOptionCssClass */
        );
        option.setAttribute("aria-selected", "false");
      });
    }
  }
  setupOptionsObserver() {
    const observerOptions = {
      childList: true,
      subtree: true
    };
    let options = [];
    const optionsObserver = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === "childList") {
          mutation.addedNodes.forEach((node) => {
            if (node instanceof HTMLOptionElement) {
              if (node.value) {
                options.push(node);
              }
            }
          });
        }
      });
      this.addNewOptionsToList(options);
      options = [];
    });
    optionsObserver.observe(this.selectElement, observerOptions);
  }
  addNewOptionsToList(options) {
    options.forEach((option) => {
      const optionTemplateClone = this.optionTemplate.content.cloneNode(true);
      const dropdownOptionElement = optionTemplateClone.querySelector(".c-select__option");
      if (!dropdownOptionElement || !optionTemplateClone) return;
      dropdownOptionElement.dataset.jsDropdownOption = option.value;
      dropdownOptionElement.classList.add("is-fetched");
      const optionLabelElement = optionTemplateClone.querySelector(".c-select__option-label");
      if (optionLabelElement) {
        optionLabelElement.textContent = option.textContent;
      }
      this.dropdownElement.appendChild(optionTemplateClone);
    });
    this.updateSelectedItemsListeners(this.getUpdatedVisualOptionsList());
  }
  getUpdatedVisualOptionsList() {
    return this.dropdownElement.querySelectorAll(`[${"data-js-dropdown-option"}].is-fetched`) ?? false;
  }
  getSelectedValues() {
    return this.isMultiSelect() ? Array.from(this.selectElement.selectedOptions).map((option) => option.value) : [this.selectElement.value];
  }
  getVisualOptionsList() {
    return this.dropdownElement.querySelectorAll(`[${"data-js-dropdown-option"}]`);
  }
  getSelectElement() {
    return this.element.querySelector(`[${"data-js-select-element"}]`);
  }
  isMultiSelect() {
    return this.selectElement.hasAttribute("multiple");
  }
  getDropdownElement() {
    return this.element.querySelector(`[${"data-js-dropdown-element"}]`);
  }
  getActionOverlayElement() {
    return this.element.querySelector(`[${"data-js-select-action-overlay"}]`);
  }
  getDropdownOptionElements() {
    return this.element.querySelectorAll(`[${"data-js-dropdown-option"}]`);
  }
};
__name(_Select, "Select");
let Select = _Select;
const _SelectComponentObserver = class _SelectComponentObserver {
  selectComponentElementAttribute = "data-js-select-component";
  //Add to main div of component
  constructor() {
    const container = document.documentElement || document.body;
    this.createInstance([...container.querySelectorAll(`[${this.selectComponentElementAttribute}]`)]);
  }
  observe() {
    const container = document.documentElement || document.body;
    const observerOptions = {
      childList: true,
      subtree: true
    };
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === "childList") {
          mutation.addedNodes.forEach((node) => {
            if (node instanceof HTMLElement) {
              const selects = [...node.querySelectorAll(`[${this.selectComponentElementAttribute}]`)];
              if (node.hasAttribute(this.selectComponentElementAttribute)) {
                selects.push(node);
              }
              this.createInstance(selects);
            }
          });
        }
      });
    });
    observer.observe(container, observerOptions);
  }
  createInstance(selects) {
    selects.forEach((select) => {
      new Select(select);
    });
  }
};
__name(_SelectComponentObserver, "SelectComponentObserver");
let SelectComponentObserver = _SelectComponentObserver;
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    const selectComponentObserverInstance = new SelectComponentObserver();
    selectComponentObserverInstance.observe();
  });
}
__name(init, "init");
const _SelectFilter = class _SelectFilter {
  constructor(selectContainer) {
    this.selectContainer = selectContainer;
    this.selectContainerId = selectContainer.getAttribute("data-js-filter-select-container");
    const selectFilterElements = this.getSelectFilterElements();
    if (selectFilterElements.length) {
      this.trySetfilterSelectComponent(selectFilterElements);
      this.listenForSelectChanges();
      this.handleFilterableElements();
      this.observeAddedFilterableElements();
    }
  }
  selectContainer;
  selectContainerId;
  filterableElementComponents = [];
  filterSelects = {};
  trySetfilterSelectComponent(selectFilterElements) {
    [...selectFilterElements].forEach((select) => {
      if (select.hasAttribute("data-js-filter-select")) {
        const attr = select.getAttribute("data-js-filter-select");
        if (!this.filterSelects[attr]) {
          this.filterSelects[attr] = {
            selects: [select],
            selected: []
          };
        } else {
          this.filterSelects[attr].selects.push(select);
        }
      }
    });
  }
  listenForSelectChanges() {
    Object.keys(this.filterSelects).forEach((key) => {
      this.filterSelects[key].selects.forEach((select) => {
        select.addEventListener("change", (e) => {
          this.updateSelected(key);
          this.filterFilterableElements();
        });
      });
    });
  }
  filterFilterableElements() {
    this.filterableElementComponents.forEach((filterableElementComponent) => {
      const showElement = [true];
      for (const key in this.filterSelects) {
        if (this.filterSelects[key].selected.length > 0) {
          showElement.push(this.filterSelects[key].selected.some((selected) => {
            return filterableElementComponent["filterProperties"][key].includes(selected);
          }));
        } else {
          showElement.push(true);
        }
      }
      this.toggleHideElement(filterableElementComponent.element, showElement.includes(false));
    });
  }
  toggleHideElement(element, hide) {
    if (hide) {
      element.classList.add("u-display--none");
    } else {
      element.classList.remove("u-display--none");
    }
  }
  updateSelected(key) {
    const selected = [];
    this.filterSelects[key].selects.forEach((select) => {
      const selectedOptions = select.querySelectorAll("option:checked");
      [...selectedOptions].forEach((option) => {
        selected.push(option.value);
      });
    });
    this.filterSelects[key].selected = selected;
  }
  getSelectFilterElements() {
    return document.querySelectorAll(`[data-js-filter-select-target="${this.selectContainerId}"]`);
  }
  handleFilterableElements() {
    [...this.selectContainer.querySelectorAll("[data-js-filter-item]")].forEach((element) => {
      this.setFilterableElementComponent(element);
    });
  }
  setFilterableElementComponent(element) {
    const filterableElementComponent = {};
    filterableElementComponent["element"] = element;
    filterableElementComponent["filterProperties"] = {};
    for (const key in this.filterSelects) {
      filterableElementComponent["filterProperties"][key] = [];
      if (element.getAttribute(key)) {
        filterableElementComponent["filterProperties"][key] = element.getAttribute(key).split(",");
      }
    }
    this.filterableElementComponents.push(filterableElementComponent);
  }
  observeAddedFilterableElements() {
    const observer = new MutationObserver((mutationsList) => {
      for (const mutation of mutationsList) {
        if (mutation.type === "childList" && mutation.addedNodes.length) {
          mutation.addedNodes.forEach((node) => {
            if (node instanceof HTMLElement && node.hasAttribute("data-js-filter-item")) {
              this.setFilterableElementComponent(node);
            }
          });
        }
      }
    });
    observer.observe(this.selectContainer, {
      childList: true,
      subtree: true
    });
  }
};
__name(_SelectFilter, "SelectFilter");
let SelectFilter = _SelectFilter;
function initializeSelectFilter() {
  document.querySelectorAll("[data-js-filter-select-container]").forEach((selectContainer) => {
    new SelectFilter(selectContainer);
  });
}
__name(initializeSelectFilter, "initializeSelectFilter");
const _SelectSort = class _SelectSort {
  constructor(selectSort, sortContainer) {
    this.selectSort = selectSort;
    this.sortContainer = sortContainer;
    this.observer = new MutationObserver(this.handleMutations.bind(this));
    this.observe();
    this.setSortListener();
  }
  selectSort;
  sortContainer;
  sortedItemsCache = {};
  observer;
  setSortListener() {
    this.selectSort.addEventListener("change", () => this.sort());
  }
  sortFunction(a, b, order) {
    const aValue = a.getAttribute("data-js-sort-item") || "";
    const bValue = b.getAttribute("data-js-sort-item") || "";
    return aValue.localeCompare(bValue) * order;
  }
  getSortedItems(sortOrder) {
    const sortableItems = this.sortContainer.querySelectorAll("[data-js-sort-item]");
    if (this.sortedItemsCache[sortOrder] && this.sortedItemsCache[sortOrder].length === sortableItems.length) {
      return this.sortedItemsCache[sortOrder];
    }
    let sortedItems = [];
    switch (sortOrder) {
      case "asc":
        sortedItems = [...sortableItems].sort((a, b) => this.sortFunction(a, b, 1));
        break;
      case "desc":
        sortedItems = [...sortableItems].sort((a, b) => this.sortFunction(a, b, -1));
        break;
      case "rand":
        sortedItems = [...sortableItems].sort(() => Math.random() - 0.5);
        break;
      default:
        sortedItems = [...sortableItems];
        break;
    }
    this.sortedItemsCache[sortOrder] = sortedItems;
    return this.sortedItemsCache[sortOrder] || [];
  }
  sort() {
    this.disconnect();
    const sortOrder = this.selectSort.value;
    const sortedItems = this.getSortedItems(sortOrder ?? "");
    this.sortContainer.innerHTML = "";
    sortedItems.forEach((item) => {
      this.sortContainer.appendChild(item);
    });
    this.observe();
  }
  handleMutations(mutations) {
    let sortableWasAdded = false;
    mutations.forEach((mutation) => {
      if (mutation.type === "childList") {
        sortableWasAdded = false;
        mutation.addedNodes.forEach((node) => {
          if (node instanceof HTMLElement && node.hasAttribute("data-js-sort-item")) {
            sortableWasAdded = true;
          }
        });
        if (sortableWasAdded) {
          this.sort();
        }
      }
    });
  }
  disconnect() {
    this.observer.disconnect();
  }
  observe() {
    this.observer.observe(this.sortContainer, { childList: true, subtree: true });
  }
};
__name(_SelectSort, "SelectSort");
let SelectSort = _SelectSort;
function initializeSelectSort() {
  const sortSelects = document.querySelectorAll("[data-js-sort-select]");
  sortSelects.forEach((sortSelect) => {
    const sortableContainerId = sortSelect.getAttribute("data-js-sort-select");
    const sortableContainer = document.querySelector(`#${sortableContainerId}`);
    if (sortableContainer) {
      new SelectSort(sortSelect, sortableContainer);
    }
  });
}
__name(initializeSelectSort, "initializeSelectSort");
init();
const initFilters = /* @__PURE__ */ __name(() => {
  initializeSelectFilter();
  initializeSelectSort();
}, "initFilters");
if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initFilters);
else initFilters();
//# sourceMappingURL=select.js.map
