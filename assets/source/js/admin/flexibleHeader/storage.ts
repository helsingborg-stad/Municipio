import HiddenSetting from "./hiddenSetting";
import { SettingsStorage, Settings, SortableItemsStorage } from "./interfaces";

class Storage {
    localSettingsStorage: SettingsStorage = {};
    constructor(private flexibleAreaNames: string[], currentValue: SettingsStorage, private hiddenSettingInstance: HiddenSetting) {
        this.localSettingsStorage = currentValue;
        this.checkDefaultKeysExists();
    }

    // Get current storage
    public getStorage() {
        return this.localSettingsStorage;
    }

    // update stored data
    public updateStorage(areaKey: string, value: Settings|SortableItemsStorage, sortableItemKey: string|null = null) {
        if (sortableItemKey) {
            this.localSettingsStorage[areaKey][sortableItemKey] = value as Settings;
        } else {
            this.localSettingsStorage[areaKey] = value as SortableItemsStorage;
        }

        this.hiddenSettingInstance.setHiddenFieldValue(this.localSettingsStorage);
    }

    // check that the default setting keys exists in the storage.
    private checkDefaultKeysExists(): void {
        let wasUpdated = false;
        this.flexibleAreaNames.forEach(areaKey => {
            if (!this.localSettingsStorage.hasOwnProperty(areaKey)) {
                this.localSettingsStorage[areaKey] = {};
                wasUpdated = true;
            }
        });

        if (wasUpdated) {
            this.hiddenSettingInstance.setHiddenFieldValue(this.localSettingsStorage);
        }
    }
}

export default Storage;