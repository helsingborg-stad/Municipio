var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
function extractSearchResultLinks(payload, limit = 10) {
  const links = [];
  const uniqueLinkKeys = /* @__PURE__ */ new Set();
  const addLink = /* @__PURE__ */ __name((labelCandidate, urlCandidate) => {
    if (links.length >= limit) {
      return;
    }
    if (typeof urlCandidate !== "string") {
      return;
    }
    const normalizedUrl = urlCandidate.trim();
    if (normalizedUrl === "") {
      return;
    }
    const normalizedLabel = typeof labelCandidate === "string" ? labelCandidate.trim() : String(labelCandidate ?? "").trim();
    const label = normalizedLabel !== "" ? normalizedLabel : normalizedUrl;
    const linkKey = `${label}::${normalizedUrl}`;
    if (uniqueLinkKeys.has(linkKey)) {
      return;
    }
    uniqueLinkKeys.add(linkKey);
    links.push({ label, url: normalizedUrl });
  }, "addLink");
  const tryExtractLink = /* @__PURE__ */ __name((candidate) => {
    const urlCandidate = candidate.url ?? candidate.href;
    if (urlCandidate == null) {
      return false;
    }
    const labelCandidate = candidate.label ?? candidate.name ?? candidate.title ?? candidate.value ?? candidate.slug ?? urlCandidate;
    addLink(labelCandidate, urlCandidate);
    return true;
  }, "tryExtractLink");
  const traverse = /* @__PURE__ */ __name((candidate) => {
    if (links.length >= limit || candidate == null) {
      return;
    }
    if (Array.isArray(candidate)) {
      for (const child of candidate) {
        traverse(child);
        if (links.length >= limit) {
          return;
        }
      }
      return;
    }
    if (typeof candidate !== "object") {
      return;
    }
    const objectCandidate = candidate;
    if ("results" in objectCandidate && objectCandidate.results && typeof objectCandidate.results === "object") {
      traverse(objectCandidate.results);
      return;
    }
    if ("data" in objectCandidate && objectCandidate.data && typeof objectCandidate.data === "object") {
      traverse(objectCandidate.data);
      return;
    }
    if (tryExtractLink(objectCandidate)) {
      return;
    }
    for (const child of Object.values(objectCandidate)) {
      traverse(child);
      if (links.length >= limit) {
        return;
      }
    }
  }, "traverse");
  traverse(payload);
  return links.slice(0, limit);
}
__name(extractSearchResultLinks, "extractSearchResultLinks");
function extractDatalistValues(payload, limit = 10) {
  const extractedLinks = extractSearchResultLinks(payload, limit);
  if (extractedLinks.length > 0) {
    return extractedLinks.map((link) => link.label);
  }
  const values = [];
  const uniqueValues = /* @__PURE__ */ new Set();
  const addValue = /* @__PURE__ */ __name((candidate) => {
    if (values.length >= limit) {
      return;
    }
    if (typeof candidate === "string" || typeof candidate === "number" || typeof candidate === "boolean") {
      const normalizedValue = String(candidate).trim();
      if (normalizedValue === "" || uniqueValues.has(normalizedValue)) {
        return;
      }
      uniqueValues.add(normalizedValue);
      values.push(normalizedValue);
    }
  }, "addValue");
  const extractFromObject = /* @__PURE__ */ __name((candidate) => {
    if ("results" in candidate && candidate.results && typeof candidate.results === "object") {
      traverse(candidate.results);
      return;
    }
    if ("data" in candidate && candidate.data && typeof candidate.data === "object") {
      traverse(candidate.data);
      return;
    }
    const prioritizedKeys = ["value", "label", "name", "title", "slug"];
    for (const key of prioritizedKeys) {
      if (key in candidate) {
        addValue(candidate[key]);
        if (values.length >= limit) {
          return;
        }
      }
    }
    for (const child of Object.values(candidate)) {
      traverse(child);
      if (values.length >= limit) {
        return;
      }
    }
  }, "extractFromObject");
  const traverse = /* @__PURE__ */ __name((candidate) => {
    if (values.length >= limit || candidate == null) {
      return;
    }
    if (Array.isArray(candidate)) {
      for (const child of candidate) {
        traverse(child);
        if (values.length >= limit) {
          return;
        }
      }
      return;
    }
    if (typeof candidate === "object") {
      extractFromObject(candidate);
      return;
    }
    addValue(candidate);
  }, "traverse");
  traverse(payload);
  return values.slice(0, limit);
}
__name(extractDatalistValues, "extractDatalistValues");
const _DatalistAutocomplete = class _DatalistAutocomplete {
  observer = null;
  init() {
    this.bindInputs(document);
    this.observeDomChanges();
  }
  bindInputs(root) {
    const inputs = root.querySelectorAll("input[data-datalist]");
    for (const input of Array.from(inputs)) {
      this.bindInput(input);
    }
  }
  bindInput(input) {
    if (input.dataset.datalistBound === "true") {
      return;
    }
    const endpoint = (input.dataset.datalist ?? "").trim();
    if (endpoint === "") {
      return;
    }
    const datalistId = this.resolveDatalistId(input);
    const datalistElement = this.ensureDatalistElement(input, datalistId);
    const searchResultsElement = this.ensureSearchResultsElement(input);
    const fieldInnerElement = input.closest(".c-field__inner");
    if (fieldInnerElement instanceof HTMLElement) {
      fieldInnerElement.classList.add("c-field__inner--datalist");
    }
    const minLength = Number.parseInt(input.dataset.datalistMinLength ?? "2", 10);
    const debounceMs = Number.parseInt(input.dataset.datalistDebounce ?? "180", 10);
    const maxItems = Number.parseInt(input.dataset.datalistMaxItems ?? "8", 10);
    let abortController = null;
    let debounceHandle = null;
    const execute = /* @__PURE__ */ __name(async () => {
      const query = input.value.trim();
      if (query.length < minLength) {
        this.renderOptions(datalistElement, []);
        this.renderSearchResults(searchResultsElement, []);
        return;
      }
      abortController?.abort();
      abortController = new AbortController();
      try {
        const requestUrl = this.buildRequestUrl(input, endpoint, query);
        const response = await fetch(requestUrl, {
          headers: {
            Accept: "application/json"
          },
          signal: abortController.signal
        });
        if (!response.ok) {
          this.renderOptions(datalistElement, []);
          this.renderSearchResults(searchResultsElement, []);
          return;
        }
        const payload = await response.json();
        const links = extractSearchResultLinks(payload, maxItems);
        const values = links.length > 0 ? links.map((link) => link.label) : extractDatalistValues(payload, maxItems);
        this.renderSearchResults(searchResultsElement, links);
        this.renderOptions(datalistElement, values);
      } catch {
        this.renderOptions(datalistElement, []);
        this.renderSearchResults(searchResultsElement, []);
      }
    }, "execute");
    input.addEventListener("input", () => {
      if (debounceHandle !== null) {
        window.clearTimeout(debounceHandle);
      }
      debounceHandle = window.setTimeout(() => {
        void execute();
      }, debounceMs);
    });
    input.dataset.datalistBound = "true";
    input.addEventListener("blur", () => {
      window.setTimeout(() => {
        searchResultsElement.hidden = true;
      }, 160);
    });
    input.addEventListener("focus", () => {
      if (searchResultsElement.childElementCount > 0) {
        searchResultsElement.hidden = false;
      }
    });
  }
  resolveDatalistId(input) {
    const currentListId = input.getAttribute("list");
    if (currentListId && currentListId.trim() !== "") {
      return currentListId;
    }
    const fieldName = input.getAttribute("name") || "field";
    return `datalist-${fieldName}-${Math.random().toString(36).slice(2, 10)}`;
  }
  ensureDatalistElement(input, datalistId) {
    let datalistElement = document.getElementById(datalistId);
    if (!datalistElement) {
      datalistElement = document.createElement("datalist");
      datalistElement.id = datalistId;
      const parent = input.parentElement;
      if (parent) {
        parent.appendChild(datalistElement);
      } else {
        document.body.appendChild(datalistElement);
      }
    }
    input.setAttribute("list", datalistId);
    return datalistElement;
  }
  ensureSearchResultsElement(input) {
    const fieldElement = input.closest(".c-field");
    const hostElement = fieldElement instanceof HTMLElement ? fieldElement : input.parentElement ?? document.body;
    if (fieldElement instanceof HTMLElement) {
      fieldElement.classList.add("c-field--search-results-enabled");
    }
    let resultsElement = hostElement.querySelector(".c-field__search-results");
    if (!resultsElement) {
      resultsElement = document.createElement("div");
      resultsElement.className = "c-field__search-results";
      resultsElement.hidden = true;
      hostElement.appendChild(resultsElement);
    }
    return resultsElement;
  }
  buildRequestUrl(input, endpoint, query) {
    if (endpoint.includes("{query}")) {
      return endpoint.replace("{query}", encodeURIComponent(query));
    }
    if (endpoint.includes("%s")) {
      return endpoint.replace("%s", encodeURIComponent(query));
    }
    const queryParam = (input.dataset.datalistQueryParam ?? "q").trim() || "q";
    try {
      const url = new URL(endpoint, window.location.origin);
      url.searchParams.set(queryParam, query);
      return url.toString();
    } catch {
      const separator = endpoint.includes("?") ? "&" : "?";
      return `${endpoint}${separator}${encodeURIComponent(queryParam)}=${encodeURIComponent(query)}`;
    }
  }
  renderOptions(datalistElement, values) {
    datalistElement.innerHTML = "";
    for (const value of values) {
      const option = document.createElement("option");
      option.value = value;
      datalistElement.appendChild(option);
    }
  }
  renderSearchResults(resultsElement, links) {
    resultsElement.innerHTML = "";
    if (links.length === 0) {
      resultsElement.hidden = true;
      return;
    }
    const listElement = document.createElement("ul");
    listElement.className = "c-field__search-results-list";
    for (const link of links) {
      const itemElement = document.createElement("li");
      itemElement.className = "c-field__search-results-item";
      const anchorElement = document.createElement("a");
      anchorElement.className = "c-field__search-results-link";
      anchorElement.href = link.url;
      anchorElement.textContent = link.label;
      itemElement.appendChild(anchorElement);
      listElement.appendChild(itemElement);
    }
    resultsElement.appendChild(listElement);
    resultsElement.hidden = false;
  }
  observeDomChanges() {
    if (this.observer) {
      return;
    }
    this.observer = new MutationObserver((mutations) => {
      for (const mutation of mutations) {
        for (const node of Array.from(mutation.addedNodes)) {
          if (!(node instanceof HTMLElement)) {
            continue;
          }
          if (node instanceof HTMLInputElement && node.matches("input[data-datalist]")) {
            this.bindInput(node);
          }
          this.bindInputs(node);
        }
      }
    });
    this.observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  }
};
__name(_DatalistAutocomplete, "DatalistAutocomplete");
let DatalistAutocomplete = _DatalistAutocomplete;
const datalistAutocomplete = new DatalistAutocomplete();
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => datalistAutocomplete.init());
} else {
  datalistAutocomplete.init();
}
export {
  DatalistAutocomplete as default,
  extractDatalistValues,
  extractSearchResultLinks
};
//# sourceMappingURL=datalistAutocomplete.js.map
