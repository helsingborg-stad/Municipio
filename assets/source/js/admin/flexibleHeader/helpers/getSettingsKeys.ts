import { FlexibleHeaderFieldKeys } from "../interfaces";

export function getSettingsKeys(): FlexibleHeaderFieldKeys {
    return {
        flexibleAreaNames: [
            'header_sortable_section_main_upper',
            'header_sortable_section_main_lower'
        ],
        hiddenName: 'header_sortable_hidden_storage',
        responsiveNameKey: '_responsive',
        kirkiAttributeName: 'data-kirki-setting'
    }
}