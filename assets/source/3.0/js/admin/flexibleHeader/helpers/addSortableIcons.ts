import { getMenuIconHtml } from "./html";

export function addEditIconToSortables(setting: HTMLElement) {
    const menuIcons = setting.querySelectorAll('.dashicons.dashicons-menu');
    if (menuIcons && menuIcons.length > 0) {
        [...menuIcons].forEach(menuIcon => {
            menuIcon.insertAdjacentHTML('afterend', getMenuIconHtml());
        });
    }
}