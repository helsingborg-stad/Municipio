import HiddenSetting from './hiddenSetting';
import { defaultEditValues } from './helpers/defaultEditValues';
import { getDropdownHtml } from './helpers/html';

class Edit {
    constructor(
        private sortableItem: HTMLElement,
        private hiddenSettingInstance: HiddenSetting,
        private settingKey: string,
        private sortableItemKey: string
    ) {
        const editButton = this.sortableItem.querySelector('[data-js-sortable-edit]');

        if (editButton) {
            this.setEditListener(editButton as HTMLElement);
        }
    }

    private setEditListener(editButton: HTMLElement) {
        editButton.addEventListener('click', () => {
            console.log("click");
        });
    }
}

export function initializeEdit(
    sortableItems: HTMLElement[], 
    hiddenSettingInstance: HiddenSetting, 
    settingKey: string,
): Edit[] {
    let instancesInitialized: Edit[] = [];
    sortableItems.forEach(sortableItem => {
        const sortableItemKey = sortableItem.getAttribute('data-value');
        if (!sortableItemKey) {
            return;
        }

        // if (!storageInstance.getSortableStorage(settingKey, sortableItemKey)) {
        //     storageInstance.setValues(settingKey, sortableItemKey, defaultEditValues);
        // }

        // sortableItem.insertAdjacentHTML('beforeend', getDropdownHtml());

        const editInstance = new Edit(sortableItem, hiddenSettingInstance, settingKey, sortableItemKey);
        instancesInitialized.push(editInstance);
    });

    return instancesInitialized;
}