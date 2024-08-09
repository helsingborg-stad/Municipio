import Sortable from "./flexibleHeader/sortable";
import HiddenSetting from "./flexibleHeader/hiddenSetting";
import { addEditIconToSortables } from "./flexibleHeader/helpers/addSortableIcons";
import { getSettingsKeys } from "./flexibleHeader/helpers/getSettingsKeys";
import { FlexibleHeaderFieldKeys } from "./flexibleHeader/interfaces";
import GetSortableItems from "./flexibleHeader/helpers/getSortableItems";
import { initializeEdit } from "./flexibleHeader/edit";
import Storage from "./flexibleHeader/storage";

declare const hiddenSettingSavedValue: any;

// Gets all the sortable settings
wp.customize.bind('ready', function() {
    const { flexibleAreaNames, hiddenName, responsiveNameKey, kirkiAttributeName }: FlexibleHeaderFieldKeys  = getSettingsKeys();

    const hiddenSettingInstance = new HiddenSetting(hiddenSettingSavedValue, hiddenName, kirkiAttributeName);

    // const storageInstance = new Storage(hiddenSettingInstance);

    let editInstances = [];

    // Initialize per area
    flexibleAreaNames.forEach(key => {
        const setting = document.querySelector(`[${kirkiAttributeName}="${key}"]`) as HTMLElement;
        const responsiveSetting = document.querySelector(`[${kirkiAttributeName}="${key}${responsiveNameKey}"]`) as HTMLElement;

        if (!setting || !responsiveSetting) {
            return;
        }

        const getSortableItemsInstance = new GetSortableItems(setting, responsiveSetting);
        addEditIconToSortables(setting);
        new Sortable(getSortableItemsInstance.getSortableItems(), getSortableItemsInstance.getSortableResponsiveItems());
        initializeEdit(getSortableItemsInstance.getSortableItems(), hiddenSettingInstance, key);
    });
});