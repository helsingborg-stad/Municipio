import { SortableItemsStorage } from "../interfaces";

export function getMenuIconHtml() {
    return '<i class="dashicons dashicons-edit" style="float:right;margin-right:4px;cursor:pointer;" data-js-sortable-edit></i>';
}

export function getDropdownHtml(sortableStorage: SortableItemsStorage) {
    return `
        <div style="display:none;width:100%;left:0;height:200px;position:absolute;">
           
        </div>
    `;
}