class Listing {
    private isExpanded = false;
    private tabbedAfterExpanded = false;
    public constructor(
        private expandButton: HTMLElement,
        private expandableWrapper: HTMLElement,
        private expandableWrapperFirstChild: HTMLElement,
    ) {
        this.setupExpandButtonListeners();
    }

    private setupExpandButtonListeners() {
        this.expandButton.addEventListener('keydown', (event: KeyboardEvent) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                this.expandButton.click();
            }

            if (event.key === 'Tab' && this.isExpanded && !this.tabbedAfterExpanded) {
                event.preventDefault();
                this.tabbedAfterExpanded = true;
                this.expandableWrapperFirstChild.focus();
            }
        });

        this.expandButton.addEventListener('click', () => {
            this.expandableWrapper.toggleAttribute('inert');
            this.isExpanded = !this.isExpanded;
            this.tabbedAfterExpanded = false;
        });
    }
}

export function initListing() {
    const expandableWrapperAttribute = 'data-js-mod-menu-expandable-wrapper';

    const modMenuItemsWithExpand = document.querySelectorAll(`[data-js-mod-menu-item]:has([${expandableWrapperAttribute}])`);

    modMenuItemsWithExpand.forEach(item => {
        const expandableWrapper = item.querySelector(`[${expandableWrapperAttribute}]`) as HTMLElement;
        const expand = item.querySelector('[data-js-mod-menu-expand-button]') as HTMLElement;
        const expandableWrapperFirstChild = expandableWrapper.firstElementChild as HTMLElement;

        if (expand && expandableWrapper && expandableWrapperFirstChild) {
            new Listing(expand, expandableWrapper, expandableWrapperFirstChild);
        }
    });
}