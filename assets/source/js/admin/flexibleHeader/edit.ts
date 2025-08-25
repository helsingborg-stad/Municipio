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
        private areaKey: string,
        private sortableItem: HTMLElement
    ) {
        this.localSettings = settings;
        this.setEditButtonListener();
        this.setSettingsListeners();
    }

    // Updates the value of a setting.
    private updateValue(key: string, value: string) {
        this.localSettings[key] = value;
    }

    // Validates the value of a setting. And adds default value of possible.
    private validateValue(settingElement: HTMLSelectElement, key: string): boolean {
        if (this.localSettings.hasOwnProperty(key) || !this.defaultSettings[key]) {
            return false;
        }

        settingElement.value = this.defaultSettings[key];
        this.updateValue(key, this.defaultSettings[key]);

        return true;
    }

    // Adds listener and update values in the global storage.
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

        // if changes happened during the validation. Update everything once.
        if (wasUpdated.includes(true)) {
            this.storageInstance.updateStorage(this.areaKey, this.localSettings, this.sortableItemKey);
        }
    }

    // Handle edit button clicks.
    private setEditButtonListener() {
        this.editButton.addEventListener('click', () => {
            if (this.sortableItem.classList.contains('is-active')) {
                this.hideDropdown(this.editButton, this.sortableItem);
            } else {
                this.closeAlreadyOpenDropdowns();
                this.showDropdown();
            }
        });
    }

    private closeAlreadyOpenDropdowns() {
        document.querySelectorAll('.kirki-sortable-item.is-active').forEach((sortableItem) => {
            const editButton = sortableItem.querySelector('[data-js-sortable-edit]') as HTMLElement;
            if (editButton && sortableItem !== this.sortableItem) {
                this.hideDropdown(editButton, sortableItem as HTMLElement);
            }
        });
    }

    private showDropdown() {
        this.sortableItem.classList.add('is-active');
        this.editButton.classList.remove('dashicons-edit');
        this.editButton.classList.add('dashicons-no');
    }

    private hideDropdown(editButton: HTMLElement, sortableItem: HTMLElement) {

        sortableItem.classList.remove('is-active');
        editButton.classList.add('dashicons-edit');
        editButton.classList.remove('dashicons-no');
    }
}

export default Edit;

// Get all needed element and handle each sortable as a separate Edit instance.
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
            areaKey,
            sortableItem
        );
    });
}