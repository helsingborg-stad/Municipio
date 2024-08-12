import Sortable from "./flexibleHeader/sortable";
import HiddenSetting from "./flexibleHeader/hiddenSetting";
import { addEditIconToSortables } from "./flexibleHeader/helpers/addSortableIcons";
import { getSettingsKeys } from "./flexibleHeader/helpers/getSettingsKeys";
import { FlexibleHeaderFieldKeys } from "./flexibleHeader/interfaces";
import GetSortableItems from "./flexibleHeader/helpers/getSortableItems";
import { initializeEdit } from "./flexibleHeader/edit";
import Edit from "./flexibleHeader/edit";

declare const hiddenSettingSavedValue: any;

wp.customize.bind('ready', function() {
    const { flexibleAreaNames, hiddenName, responsiveNameKey, kirkiAttributeName }: FlexibleHeaderFieldKeys  = getSettingsKeys();

    const hiddenSettingInstance = new HiddenSetting(hiddenSettingSavedValue, hiddenName, kirkiAttributeName);
    
    let store = hiddenSettingInstance.getHiddenFieldSavedValues();
    let editInstances = [];

    flexibleAreaNames.forEach(key => {
        const setting = document.querySelector(`[${kirkiAttributeName}="${key}"]`) as HTMLElement;
        const responsiveSetting = document.querySelector(`[${kirkiAttributeName}="${key}${responsiveNameKey}"]`) as HTMLElement;

        if (!setting || !responsiveSetting) {
            return;
        }

        if (!store.hasOwnProperty(key)) {
            store[key] = {};
        }

        const getSortableItemsInstance = new GetSortableItems(setting, responsiveSetting);
        addEditIconToSortables(setting);
        new Sortable(getSortableItemsInstance.getSortableItems(), getSortableItemsInstance.getSortableResponsiveItems());
        const editInstancesFromSetting = initializeEdit(getSortableItemsInstance.getSortableItems(), hiddenSettingInstance, store[key]);
        editInstances.push({[key]: editInstancesFromSetting});
    });
});