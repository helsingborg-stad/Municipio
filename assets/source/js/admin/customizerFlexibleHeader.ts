import HiddenSetting from "./flexibleHeader/hiddenSetting";
import { addEditIconToSortables } from "./flexibleHeader/helpers/addSortableIcons";
import { getSettingsKeys } from "./flexibleHeader/helpers/getSettingsKeys";
import { FlexibleHeaderFieldKeys } from "./flexibleHeader/interfaces";
import GetSortableItems from "./flexibleHeader/helpers/getSortableItems";
import { initializeEdit } from "./flexibleHeader/edit";
import { SettingsStorage } from "./flexibleHeader/interfaces";
import Storage from "./flexibleHeader/storage";

declare const flexibleHeader: any;

wp.customize.bind('ready', function() {
    if (!flexibleHeader) {
        return;
    }

    const { flexibleAreaNames, hiddenName, responsiveNameKey, kirkiAttributeName }: FlexibleHeaderFieldKeys  = getSettingsKeys();
    const hiddenSettingInstance = new HiddenSetting(flexibleHeader.hiddenValue, hiddenName, kirkiAttributeName);

    // Using interval since the fields might not be loaded yet.
    const intervalId = setInterval(() => {
        if (!hiddenSettingInstance.getHiddenSettingField()) {
            return;
        } else {
            clearInterval(intervalId);
        }

        let currentValue: SettingsStorage = hiddenSettingInstance.getHiddenFieldValue() ?? {};
        const storageInstance = new Storage(flexibleAreaNames, currentValue, hiddenSettingInstance);

        flexibleAreaNames.forEach(areaKey => {
            const setting = document.querySelector(`[${kirkiAttributeName}="${areaKey}"]`) as HTMLElement;
            const responsiveSetting = document.querySelector(`[${kirkiAttributeName}="${areaKey}${responsiveNameKey}"]`) as HTMLElement;

            if (!setting || !responsiveSetting) {
                return;
            }
            
            const getSortableItemsInstance = new GetSortableItems(
                setting, 
                responsiveSetting
            );

            addEditIconToSortables(setting);

            initializeEdit(
                storageInstance, 
                getSortableItemsInstance.getSortableItems(), 
                areaKey,
                flexibleHeader.lang
            );
        });
    }, 1000);
});