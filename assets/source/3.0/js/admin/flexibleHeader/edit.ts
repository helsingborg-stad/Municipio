import HiddenSetting from './hiddenSetting';
import { defaultEditValues } from './helpers/defaultEditValues';
import { getDropdownHtml } from './helpers/html';

class Edit {
    constructor(
        private sortableItem: HTMLElement,
        private dropdownElement: HTMLElement,
        private editButton: HTMLElement,
        private hiddenSettingInstance: HiddenSetting,
        private sortableItemStore: any,
    ) {
        this.setEditButtonListener();
    }

    private setEditButtonListener() {
        this.editButton.addEventListener('click', () => {
            this.dropdownElement.style.display = 'block';
        });
    }
}

export default Edit;

export function initializeEdit(
    sortableItems: HTMLElement[], 
    hiddenSettingInstance: HiddenSetting,
    settingStore: any
): Edit[] {
    let instancesInitialized: Edit[] = [];
    sortableItems.forEach(sortableItem => {
        const sortableItemKey = sortableItem.getAttribute('data-value');
        if (!sortableItemKey) {
            return;
        }

        if (!settingStore.hasOwnProperty(sortableItemKey)) {
            settingStore[sortableItemKey] = defaultEditValues;
        }

        sortableItem.insertAdjacentHTML('beforeend', getDropdownHtml(settingStore));
        
        const dropdownElement = sortableItem.querySelector('[data-js-flexible-dropdown]') as HTMLElement;
        const editButton = sortableItem.querySelector('[data-js-sortable-edit]') as HTMLElement;

        if (!dropdownElement || !editButton) {
            return;
        }

        const editInstance = new Edit(sortableItem, dropdownElement, editButton, hiddenSettingInstance, settingStore[sortableItemKey]);
        instancesInitialized.push(editInstance);
    });

    return instancesInitialized;
}