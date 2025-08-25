class GetSortableItems {
    private sortableItems: HTMLElement[];
    private sortableResponsiveItems: HTMLElement[];

    constructor(private setting: HTMLElement, private responsiveSetting: HTMLElement ) {
        this.sortableItems = this.getSortableItems();
        this.sortableResponsiveItems = this.getSortableResponsiveItems();
    }

    public getSortableItems() {
        if (this.sortableItems && this.sortableItems.length > 0) {
            return this.sortableItems;
        }

        this.sortableItems = [...this.setting.querySelectorAll('.kirki-sortable-item')] as HTMLElement[];
        return this.sortableItems;
    }

    public getSortableResponsiveItems() {
        if (this.sortableResponsiveItems && this.sortableResponsiveItems.length > 0) {
            return this.sortableResponsiveItems;
        }

        this.sortableResponsiveItems = [...this.responsiveSetting.querySelectorAll('.kirki-sortable-item')] as HTMLElement[];
        return this.sortableResponsiveItems;
    }
}

export default GetSortableItems;