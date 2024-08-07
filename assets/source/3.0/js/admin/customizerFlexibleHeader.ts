interface FlexibleHeaderSortPair {
    setting: HTMLElement;
    responsive: HTMLElement;
}

interface StructuredItems {
    [key: string]: HTMLElement;
}

class FlexibleHeaderResponsive {
    private mainItems: StructuredItems;
    private responsiveItems: StructuredItems;
    constructor(private pair: FlexibleHeaderSortPair) {
        const [mainItems, responsiveItems] = this.getAllSortableItems();

        this.mainItems = mainItems;
        this.responsiveItems = responsiveItems;

        if (this.mainItems, this.responsiveItems) {
            this.handleHidden();
            this.setMainItemsListeners();
        }
    }

    private setMainItemsListeners() {
        for (const key in this.mainItems) {
            const visibilityElement = this.mainItems[key].querySelector('.visibility');

            if (!visibilityElement || !this.responsiveItems[key]) {
                continue;
            }

            visibilityElement.addEventListener('click', () => {
                if (this.mainItems[key].classList.contains('invisible')) {
                    this.hide(this.responsiveItems[key]);
                } else {
                    this.show(this.responsiveItems[key]);
                }
            });
        }
    }

    private hide(item: HTMLElement) {
        item.style.display = 'none';

        if (!item.classList.contains('invisible')) {
            (item.querySelector('.visibility') as HTMLElement)?.click();
        }
    }

    private show(item: HTMLElement) {
        item.style.display = 'list-item';

        if (item.classList.contains('invisible')) {
            (item.querySelector('.visibility') as HTMLElement)?.click();
        }
    }

    private handleHidden() {
        for (const key in this.mainItems) {
            if (!this.responsiveItems[key]) {
                continue;
            }

            this.removeVisibilityIconButton(this.responsiveItems[key]);

            if (!this.mainItems[key].classList.contains('invisible')) {
                this.show(this.responsiveItems[key]);
                continue;
            }
            console.log("HELLO");
            this.hide(this.responsiveItems[key]);
        }
    }

    private removeVisibilityIconButton(item: HTMLElement) {
        const visibilityElement = item.querySelector('.visibility');

        if (visibilityElement) {
            (visibilityElement as HTMLElement).style.display = 'none';
        }
    }

    private getAllSortableItems() {
        const mainItems       = this.structureSortableItems([...this.pair.setting.querySelectorAll('.kirki-sortable-item')] as HTMLElement[]);
        const responsiveItems = this.structureSortableItems([...this.pair.responsive.querySelectorAll('.kirki-sortable-item')] as HTMLElement[]);

        return [mainItems, responsiveItems];
    }

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

wp.customize.bind('ready', function() {
    const customizerControls = document.querySelector('#customize-theme-controls');
    const flexibleAreaNames = [
        'header_sortable_section_logotype',
        'header_sortable_section_main_upper',
        'header_sortable_section_main_lower'
    ];

    if (!customizerControls) {
        return;
    }

    const responsiveNameKey = '_responsive';
    const kirkiAttributeName = 'data-kirki-setting';

    flexibleAreaNames.forEach(name => {
        const setting = customizerControls.querySelector(`[${kirkiAttributeName}="${name}"]`);
        const responsiveSetting = customizerControls.querySelector(`[${kirkiAttributeName}="${name}${responsiveNameKey}"]`);

        if (!setting || !responsiveSetting) {
            return;
        }

        new FlexibleHeaderResponsive({setting: setting as HTMLElement, responsive: responsiveSetting as HTMLElement});
    });
});