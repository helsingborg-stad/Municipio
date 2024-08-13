import { defaultEditValues } from './helpers/defaultEditValues';
import { getDropdownHtml } from './helpers/html';
import { Settings } from './interfaces';
import Storage from './storage';

class Edit {
    localSettings: Settings = {};
    constructor(
        private storageInstance: Storage,
        private dropdownElement: HTMLElement,
        private settingsElements: HTMLSelectElement[],
        private editButton: HTMLElement,
        settings: Settings,
        private defaultSettings: Settings,
        private sortableItemKey: string,
        private areaKey: string
    ) {
        this.localSettings = settings;
        this.setEditButtonListener();
        this.setSettingsListeners();
    }

    private setSettingsListeners() {
        let wasUpdated: boolean[] = [];
        this.settingsElements.forEach(settingElement => {
            const key = settingElement.getAttribute('data-js-flexible-setting');

            if (!key) { return;}

            wasUpdated.push(this.validateValue(settingElement, key));
            
            settingElement.addEventListener('change', (e: Event) => {
                const select = e.target as HTMLSelectElement;

                if (select) {
                    this.updateValue(key, (e.target as HTMLSelectElement)?.value);
                    this.storageInstance.updateStorage(this.areaKey, this.localSettings, this.sortableItemKey);
                }
            });
        });

        if (wasUpdated.includes(true)) {
            this.storageInstance.updateStorage(this.areaKey, this.localSettings, this.sortableItemKey);
        }
    }

    private updateValue(key: string, value: string) {
        this.localSettings[key] = value;
    }

    private validateValue(settingElement: HTMLSelectElement, key: string): boolean {
        if (this.localSettings.hasOwnProperty(key) || !this.defaultSettings[key]) {
            return false;
        }

        settingElement.value = this.defaultSettings[key];
        this.updateValue(key, this.defaultSettings[key]);

        return true;
    }

    private setEditButtonListener() {
        this.editButton.addEventListener('click', () => {
            if (this.dropdownElement.classList.contains('is-active')) {
                this.dropdownElement.classList.remove('is-active');
                this.editButton.classList.add('dashicons-edit');
                this.editButton.classList.remove('dashicons-no');
            } else {
                this.dropdownElement.classList.add('is-active');
                this.editButton.classList.remove('dashicons-edit');
                this.editButton.classList.add('dashicons-no');
            }
        });
    }
}

export default Edit;

export function initializeEdit(
    storageInstance: Storage,
    sortableItems: HTMLElement[], 
    areaKey: string,
    translations: any
): void {
    sortableItems.forEach(sortableItem => {
        const sortableItemKey = sortableItem.getAttribute('data-value');
        let storage = storageInstance.getStorage();

        if (!sortableItemKey || !storage[areaKey]) {
            return;
        }

        if (!storage[areaKey].hasOwnProperty(sortableItemKey)) {
            storage[areaKey][sortableItemKey] = defaultEditValues;
            storageInstance.updateStorage(areaKey, storage[areaKey][sortableItemKey], sortableItemKey);
        }

        sortableItem.insertAdjacentHTML('beforeend', getDropdownHtml(storage[areaKey][sortableItemKey], translations));
        
        const dropdownElement = sortableItem.querySelector('[data-js-flexible-dropdown]') as HTMLElement;
        const editButton = sortableItem.querySelector('[data-js-sortable-edit]') as HTMLElement;
        const dropdownElementSettings = dropdownElement?.querySelectorAll('[data-js-flexible-setting]') as NodeListOf<HTMLSelectElement>;

        if (!dropdownElementSettings || !editButton) {
            return;
        }

        new Edit(
            storageInstance,
            dropdownElement, 
            [...dropdownElementSettings], 
            editButton, 
            storage[areaKey][sortableItemKey], 
            defaultEditValues,
            sortableItemKey,
            areaKey
        );
    });
}