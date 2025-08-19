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

export interface Settings {
    [key: string]: string;
}

export interface SortableItemsStorage {
    [key: string]: Settings;
}

export interface SettingsStorage {
    [key: string]: SortableItemsStorage;
}
