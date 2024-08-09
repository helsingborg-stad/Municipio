import { StorageInterface, SettingsStorage, SortableItemsStorage, SortableItemStorageValue } from './interfaces';
import HiddenSetting from './hiddenSetting';

class Storage implements StorageInterface {
    storageObject: SettingsStorage;
    constructor(hiddenSettingsInstance: HiddenSetting) {
        this.storageObject = hiddenSettingsInstance.getCurrentHiddenFieldValue();
    }

    public setValue(settingsKey: string, sortableKey: string = "", propertyKey: string, value: string): void {
        if (!this.storageObject[settingsKey]) {
            this.storageObject[settingsKey] = {};
        }

        if (!this.storageObject[settingsKey][sortableKey]) {
            this.storageObject[settingsKey][sortableKey] = {};
        }

        this.storageObject[settingsKey][sortableKey][propertyKey] = value; 
    }

    public setValues(settingsKey: string, sortableKey: string, values: SortableItemStorageValue): void {
        if (!this.storageObject[settingsKey]) {
            this.storageObject[settingsKey] = {};
        }

        this.storageObject[settingsKey][sortableKey] = values;
    }

    public getSettingStorage(settingsKey: string): SortableItemsStorage|null {
        if (this.storageObject[settingsKey]) {
            return this.storageObject[settingsKey];
        }

        return null;
    }

    public getSortableStorage(settingsKey: string, sortableKey: string): SortableItemStorageValue|null {
        if (this.storageObject[settingsKey] && this.storageObject[settingsKey][sortableKey]) {
            return this.storageObject[settingsKey][sortableKey];
        }

        return null;
    }

    public getValues(): SettingsStorage {
        return this.storageObject;
    }
}

export default Storage;