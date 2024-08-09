import { getMenuIconHtml } from "./html";

export function addEditIconToSortables(setting: HTMLElement) {
    const menuIcons = setting.querySelectorAll('.dashicons.dashicons-menu');
    if (menuIcons && menuIcons.length > 0) {
        const html = '<i class="dashicons dashicons-edit" style="float:right;margin-right:4px;cursor:pointer;" data-js-sortable-edit></i>';

        [...menuIcons].forEach(menuIcon => {
            menuIcon.insertAdjacentHTML('afterend', getMenuIconHtml());
        });
    }
}