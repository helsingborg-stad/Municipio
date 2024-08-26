import { StructuredItems, SortPair } from './interfaces';

class Sortable {
    private sortableItems: StructuredItems;
    private sortableResponsiveItems: StructuredItems;
    
    constructor(sortableItems: HTMLElement[], sortableResponsiveItems: HTMLElement[]) {
        this.sortableItems = this.structureSortableItems(sortableItems);
        this.sortableResponsiveItems = this.structureSortableItems(sortableResponsiveItems);

        if (this.sortableItems, this.sortableResponsiveItems) {
            this.handleHidden();
            this.setMainItemsListeners();
        }
    }

    // listen to the visitibility of the main items.
    private setMainItemsListeners() {
        for (const key in this.sortableItems) {
            const visibilityElement = this.sortableItems[key].querySelector('.visibility');

            if (!visibilityElement || !this.sortableResponsiveItems[key]) {
                continue;
            }

            visibilityElement.addEventListener('click', () => {
                if (this.sortableItems[key].classList.contains('invisible')) {
                    this.hide(this.sortableResponsiveItems[key]);
                } else {
                    this.show(this.sortableResponsiveItems[key]);
                }
            });
        }
    }

    // Hide the responsive items if the main items are hidden.
    private hide(item: HTMLElement) {
        item.style.display = 'none';

        if (!item.classList.contains('invisible')) {
            (item.querySelector('.visibility') as HTMLElement)?.click();
        }
    }

    // Show the responsive items if the main items are shown
    private show(item: HTMLElement) {
        item.style.display = 'list-item';

        if (item.classList.contains('invisible')) {
            (item.querySelector('.visibility') as HTMLElement)?.click();
        }
    }

    // Handle hidden elements from start
    private handleHidden() {
        for (const key in this.sortableItems) {
            if (!this.sortableResponsiveItems[key]) {
                continue;
            }

            this.removeVisibilityIconButton(this.sortableResponsiveItems[key]);

            if (!this.sortableItems[key].classList.contains('invisible')) {
                this.show(this.sortableResponsiveItems[key]);
                continue;
            }
            
            this.hide(this.sortableResponsiveItems[key]);
        }
    }

    // Remove the visibility icon button from the responsive items
    private removeVisibilityIconButton(item: HTMLElement) {
        const visibilityElement = item.querySelector('.visibility');

        if (visibilityElement) {
            (visibilityElement as HTMLElement).style.display = 'none';
        }
    }

    // Structure the sortable items correctly
    private structureSortableItems(items: HTMLElement[]) {
        let structuredItems = {} as StructuredItems;

        items.forEach(item => {
            if (!item.getAttribute('data-value')) {
                return;
            }

            const key = item.getAttribute('data-value') as string;

            structuredItems[key] = item;
        });

        return structuredItems;
    }
}

export default Sortable;