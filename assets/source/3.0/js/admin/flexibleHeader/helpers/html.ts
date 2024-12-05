import { Settings } from "../interfaces";

// HTML for the edit item.
export function getMenuIconHtml() {
    return '<i class="dashicons dashicons-edit" style="float:right;margin-right:4px;cursor:pointer;" data-js-sortable-edit></i>';
}

// HTML for the dropdown.
export function getDropdownHtml(settings: Settings, translations: any) {
    return `
        <div class="flexible-header-edit" data-js-flexible-dropdown>
            <div class="flexible-header-settings">
                <h3>${translations.alignment ?? 'Alignment'}</h3>
                <select data-js-flexible-setting="align">
                    <option value="left" ${settings.align === 'left' ? 'selected' : ''}>${translations.left ?? 'Left'}</option>
                    <option value="center" ${settings.align === 'center' ? 'selected' : ''}>${translations.center ?? 'Center'}</option>
                    <option value="right" ${settings.align === 'right' ? 'selected' : ''}>${translations.right ?? 'Right'}</option>
                </select>

                <h3>${translations.margin ?? 'Margin'}</h3>
                <select data-js-flexible-setting="margin">
                    <option value="none" ${settings.margin === 'none' ? 'selected' : ''}>${translations.none ?? 'None'}</option>
                    <option value="both" ${settings.margin === 'both' ? 'selected' : ''}>${translations.both ?? 'Both'}</option>
                    <option value="left" ${settings.margin === 'left' ? 'selected' : ''}>${translations.left ?? 'Left'}</option>
                    <option value="right" ${settings.margin === 'right' ? 'selected' : ''}>${translations.right ?? 'Right'}</option>
                </select>
            </div>
        </div>
    `;
}