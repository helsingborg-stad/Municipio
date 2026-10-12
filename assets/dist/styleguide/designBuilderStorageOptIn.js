var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const GLOBAL_SCOPE_KEY = "__global__";
const GENERAL_SCOPE_KEY = "__general__";
const NON_CUSTOMIZABLE_COMPONENTS = /* @__PURE__ */ new Set(["scope", "fab"]);
const SPLIT_STORAGE_KEY = "design-builder-split";
const MIN_SPLIT = 20;
const MAX_SPLIT = 80;
function createEmptyOverrideState() {
  return {
    token: {},
    component: {}
  };
}
__name(createEmptyOverrideState, "createEmptyOverrideState");
function normalizeOverrideValue(value) {
  if (typeof value === "string" && value.trim() !== "") {
    return value;
  }
  if (typeof value === "number" && Number.isFinite(value)) {
    return value.toString();
  }
  return null;
}
__name(normalizeOverrideValue, "normalizeOverrideValue");
function normalizeTokenOverrides(input) {
  if (!input || typeof input !== "object" || Array.isArray(input)) {
    return {};
  }
  const filtered = {};
  for (const [key, value] of Object.entries(input)) {
    const normalizedValue = normalizeOverrideValue(value);
    if (normalizedValue !== null) {
      filtered[key] = normalizedValue;
    }
  }
  return filtered;
}
__name(normalizeTokenOverrides, "normalizeTokenOverrides");
function isLegacyComponentOverrides(input) {
  const values = Object.values(input);
  if (values.length === 0) {
    return false;
  }
  return values.every((value) => {
    if (!value || typeof value !== "object" || Array.isArray(value)) {
      return false;
    }
    const variableValues = Object.values(value);
    return variableValues.every((entry) => normalizeOverrideValue(entry) !== null);
  });
}
__name(isLegacyComponentOverrides, "isLegacyComponentOverrides");
function normalizeComponentValueMap(input) {
  const filtered = {};
  for (const [variable, value] of Object.entries(input)) {
    const normalizedValue = normalizeOverrideValue(value);
    if (normalizedValue !== null) {
      filtered[variable] = normalizedValue;
    }
  }
  return filtered;
}
__name(normalizeComponentValueMap, "normalizeComponentValueMap");
function normalizeComponentMap(input) {
  const cleaned = {};
  for (const [componentName, values] of Object.entries(input)) {
    if (!values || typeof values !== "object" || Array.isArray(values)) continue;
    const filtered = normalizeComponentValueMap(values);
    if (Object.keys(filtered).length > 0) {
      cleaned[componentName] = filtered;
    }
  }
  return cleaned;
}
__name(normalizeComponentMap, "normalizeComponentMap");
function normalizeComponentOverrides(input) {
  if (!input || typeof input !== "object" || Array.isArray(input)) {
    return {};
  }
  const record = input;
  if (isLegacyComponentOverrides(record)) {
    const legacy = normalizeComponentMap(record);
    if (Object.keys(legacy).length === 0) {
      return {};
    }
    return {
      [GENERAL_SCOPE_KEY]: legacy
    };
  }
  const result = {};
  for (const [scopeKey, scopeValue] of Object.entries(record)) {
    if (!scopeValue || typeof scopeValue !== "object" || Array.isArray(scopeValue)) continue;
    const componentOverrides = normalizeComponentMap(scopeValue);
    if (Object.keys(componentOverrides).length > 0) {
      const normalizedScopeKey = scopeKey === GLOBAL_SCOPE_KEY ? GENERAL_SCOPE_KEY : scopeKey;
      result[normalizedScopeKey] = {
        ...result[normalizedScopeKey] || {},
        ...componentOverrides
      };
    }
  }
  return result;
}
__name(normalizeComponentOverrides, "normalizeComponentOverrides");
function normalizeDesignBuilderOverrideState(input) {
  if (!input || typeof input !== "object" || Array.isArray(input)) {
    return createEmptyOverrideState();
  }
  const record = input;
  if ("token" in record || "component" in record) {
    return {
      token: normalizeTokenOverrides(record.token),
      component: normalizeComponentOverrides(record.component)
    };
  }
  const tokenOverrides = normalizeTokenOverrides(record);
  if (Object.keys(tokenOverrides).length === Object.keys(record).length) {
    return {
      token: tokenOverrides,
      component: {}
    };
  }
  return {
    token: {},
    component: normalizeComponentOverrides(record)
  };
}
__name(normalizeDesignBuilderOverrideState, "normalizeDesignBuilderOverrideState");
function hasOverrideStateData(state) {
  return Object.keys(state.token).length > 0 || Object.keys(state.component).length > 0;
}
__name(hasOverrideStateData, "hasOverrideStateData");
const browserLocalStorageAdapter = {
  getItem(key) {
    return localStorage.getItem(key);
  },
  setItem(key, value) {
    localStorage.setItem(key, value);
  },
  removeItem(key) {
    localStorage.removeItem(key);
  }
};
const STORAGE_KEY = "design-builder-overrides";
const LEGACY_TOKEN_STORAGE_KEY = "design-tokens-overrides";
const PRESETS_KEY = "design-tokens-presets";
const ACTIVE_PRESET_KEY = "design-tokens-active-preset";
const _TokenOverrideLocalStorageStore = class _TokenOverrideLocalStorageStore {
  key;
  storage;
  constructor(key = STORAGE_KEY, storage = browserLocalStorageAdapter) {
    this.key = key;
    this.storage = storage;
  }
  load() {
    try {
      const raw = this.storage.getItem(this.key);
      if (raw) {
        return normalizeDesignBuilderOverrideState(JSON.parse(raw)).token;
      }
      const legacyRaw = this.storage.getItem(LEGACY_TOKEN_STORAGE_KEY);
      if (!legacyRaw) {
        return {};
      }
      return normalizeDesignBuilderOverrideState(JSON.parse(legacyRaw)).token;
    } catch {
      return {};
    }
  }
  save(overrides) {
    const filtered = normalizeDesignBuilderOverrideState({ token: overrides }).token;
    const nextState = this.loadState();
    nextState.token = filtered;
    this.saveState(nextState);
  }
  clear() {
    const nextState = this.loadState();
    nextState.token = {};
    this.saveState(nextState);
  }
  loadState() {
    const fallbackState = createEmptyOverrideState();
    try {
      const raw = this.storage.getItem(this.key);
      if (raw) {
        return normalizeDesignBuilderOverrideState(JSON.parse(raw));
      }
    } catch {
    }
    try {
      const legacyRaw = this.storage.getItem(LEGACY_TOKEN_STORAGE_KEY);
      if (legacyRaw) {
        fallbackState.token = normalizeTokenOverrides(JSON.parse(legacyRaw));
      }
    } catch {
    }
    try {
      const legacyComponentRaw = this.storage.getItem(LEGACY_COMPONENT_STORAGE_KEY);
      if (legacyComponentRaw) {
        fallbackState.component = normalizeDesignBuilderOverrideState(JSON.parse(legacyComponentRaw)).component;
      }
    } catch {
    }
    return fallbackState;
  }
  saveState(state) {
    if (!hasOverrideStateData(state)) {
      this.storage.removeItem(this.key);
      return;
    }
    this.storage.setItem(this.key, JSON.stringify(state));
  }
};
__name(_TokenOverrideLocalStorageStore, "TokenOverrideLocalStorageStore");
let TokenOverrideLocalStorageStore = _TokenOverrideLocalStorageStore;
const COMPONENT_STORAGE_KEY = "design-builder-overrides";
const LEGACY_COMPONENT_STORAGE_KEY = "design-tokens-component-overrides";
const COMPONENT_PRESETS_KEY = "design-tokens-component-presets";
const COMPONENT_ACTIVE_PRESET_KEY = "design-tokens-component-active-preset";
const _ComponentOverrideLocalStorageStore = class _ComponentOverrideLocalStorageStore {
  key;
  storage;
  constructor(key = COMPONENT_STORAGE_KEY, storage = browserLocalStorageAdapter) {
    this.key = key;
    this.storage = storage;
  }
  load() {
    try {
      const raw = this.storage.getItem(this.key);
      if (raw) {
        return normalizeDesignBuilderOverrideState(JSON.parse(raw)).component;
      }
      const legacyRaw = this.storage.getItem(LEGACY_COMPONENT_STORAGE_KEY);
      if (!legacyRaw) return {};
      return normalizeComponentOverrides(JSON.parse(legacyRaw));
    } catch {
      return {};
    }
  }
  save(overrides) {
    const nextState = this.loadState();
    nextState.component = this.normalize(overrides);
    this.saveState(nextState);
  }
  normalize(input) {
    return normalizeComponentOverrides(input);
  }
  loadState() {
    const fallbackState = createEmptyOverrideState();
    try {
      const raw = this.storage.getItem(this.key);
      if (raw) {
        return normalizeDesignBuilderOverrideState(JSON.parse(raw));
      }
    } catch {
    }
    try {
      const legacyRaw = this.storage.getItem(LEGACY_COMPONENT_STORAGE_KEY);
      if (legacyRaw) {
        fallbackState.component = normalizeComponentOverrides(JSON.parse(legacyRaw));
      }
    } catch {
    }
    try {
      const legacyTokenRaw = this.storage.getItem(LEGACY_TOKEN_STORAGE_KEY);
      if (legacyTokenRaw) {
        fallbackState.token = normalizeTokenOverrides(JSON.parse(legacyTokenRaw));
      }
    } catch {
    }
    return fallbackState;
  }
  saveState(state) {
    if (!hasOverrideStateData(state)) {
      this.storage.removeItem(this.key);
      return;
    }
    this.storage.setItem(this.key, JSON.stringify(state));
  }
};
__name(_ComponentOverrideLocalStorageStore, "ComponentOverrideLocalStorageStore");
let ComponentOverrideLocalStorageStore = _ComponentOverrideLocalStorageStore;
const DESIGN_BUILDER_STORAGE_ATTRIBUTE = "data-design-builder-storage";
const LOCAL_STORAGE_PERSISTENCE_MODE = "local-storage";
function hasLocalStorageOptIn(element) {
  return element?.getAttribute(DESIGN_BUILDER_STORAGE_ATTRIBUTE) === LOCAL_STORAGE_PERSISTENCE_MODE;
}
__name(hasLocalStorageOptIn, "hasLocalStorageOptIn");
function isLocalStoragePersistenceEnabled(element) {
  if (hasLocalStorageOptIn(element)) {
    return true;
  }
  return hasLocalStorageOptIn(globalThis.document?.documentElement);
}
__name(isLocalStoragePersistenceEnabled, "isLocalStoragePersistenceEnabled");
export {
  ACTIVE_PRESET_KEY as A,
  COMPONENT_ACTIVE_PRESET_KEY as C,
  GENERAL_SCOPE_KEY as G,
  MIN_SPLIT as M,
  NON_CUSTOMIZABLE_COMPONENTS as N,
  PRESETS_KEY as P,
  SPLIT_STORAGE_KEY as S,
  TokenOverrideLocalStorageStore as T,
  normalizeTokenOverrides as a,
  normalizeComponentOverrides as b,
  browserLocalStorageAdapter as c,
  COMPONENT_PRESETS_KEY as d,
  GLOBAL_SCOPE_KEY as e,
  MAX_SPLIT as f,
  ComponentOverrideLocalStorageStore as g,
  hasOverrideStateData as h,
  isLocalStoragePersistenceEnabled as i,
  normalizeDesignBuilderOverrideState as n
};
//# sourceMappingURL=designBuilderStorageOptIn.js.map
