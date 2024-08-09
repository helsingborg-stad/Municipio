export interface SortPair {
    setting: HTMLElement;
    responsive: HTMLElement;
}

export interface StructuredItems {
    [key: string]: HTMLElement;
}

export interface FlexibleHeaderFieldKeys {
    flexibleAreaNames: string[];
    hiddenName: string;
    responsiveNameKey: string;
    kirkiAttributeName: string;
}

export interface StorageInterface {
    setValue(settingsKey: string, sortableKey: string, propertyKey: string, value: string): void;
    setValues(settingsKey: string, sortableKey: string, values: SortableItemStorageValue): void;
    getValues(): SettingsStorage;
    getSettingStorage(settingsKey: string): SortableItemsStorage|null;
    getSortableStorage(settingsKey: string, sortableKey: string): SortableItemStorageValue|null;
}

export interface SortableItemStorageValue {
    [key: string]: string;
}

export interface SortableItemsStorage {
    [key: string]: SortableItemStorageValue;
}

export interface SettingsStorage {
    [key: string]: SortableItemsStorage;
}
