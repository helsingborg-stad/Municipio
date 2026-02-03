class CollapsibleSearchForm {
    private lastFocusedElement: HTMLElement | null = null;
    private firstFocusableElement: HTMLElement | null = null;
    private lastFocusableElement: HTMLElement | null = null;
    private isOpen: boolean = false;

    constructor(
        private container: HTMLElement,
        private triggerButton: HTMLElement, 
        private searchForm: HTMLElement, 
        private closeButton: HTMLElement, 
        private searchInput: HTMLElement
    ) {
        const [firstElement, lastElement] = this.getFocusableElements();
        this.firstFocusableElement = firstElement;
        this.lastFocusableElement = lastElement;

        // Initialize event listeners
        if (this.firstFocusableElement && this.lastFocusableElement) {
            this.initializeEvents();
        }
    }

    // Initialize event listeners once
    private initializeEvents(): void {
        // Always keep these listeners active
        document.addEventListener('click', this.globalClickListener);
        document.addEventListener('keydown', this.globalKeydownListener);

        this.triggerButton.addEventListener('click', this.toggleSearchForm); // Toggle form open/close
        this.closeButton.addEventListener('click', this.closeSearchForm);
    }

    // Function to toggle the search form
    private toggleSearchForm = (e: Event): void => {
        if (this.isOpen) {
            this.closeSearchForm();
        } else {
            this.openSearchForm();
        }
    }

    // Function to open the search form
    private openSearchForm(): void {
        this.isOpen = true;
        this.handleIsOpen();

        setTimeout(() => {
            this.searchInput.focus(); // Focus the search input after opening the search form
        }, 500);
    }

    // Function to close the search form
    private closeSearchForm = (): void => {
        if (!this.isOpen) return;

        this.isOpen = false;
        this.handleIsOpen();

        setTimeout(() => {
            this.container.classList.remove('is-closing'); // Clean up classes
            this.lastFocusedElement?.focus(); // Restore focus to the last focused element
        }, 500);
    }

    private handleIsOpen(): void {
        if (this.isOpen) {
            this.lastFocusedElement = document.activeElement as HTMLElement;
            this.container.classList.remove('is-closing');
            this.searchForm.setAttribute('aria-hidden', 'false');
            this.searchForm.classList.remove('u-visibility--hidden');
            this.triggerButton.setAttribute('aria-expanded', 'true');
            this.container.classList.add('is-open');
        } else {
            this.searchForm.classList.add('u-visibility--hidden');
            this.searchInput.blur();
            this.container.classList.remove('is-open');
            this.searchForm.setAttribute('aria-hidden', 'true');
            this.triggerButton.setAttribute('aria-expanded', 'false');
            this.container.classList.add('is-closing');
        }
    }

    // Global click listener to detect outside clicks
    private globalClickListener = (event: MouseEvent): void => {
        const target = event.target as Node;
        if (this.isOpen && !this.searchForm.contains(target) && !this.triggerButton.contains(target)) {
            this.closeSearchForm();
        }
    }

    // Global keydown listener for Escape and Tab key handling
    private globalKeydownListener = (event: KeyboardEvent): void => {
        if (!this.isOpen) return;

        if (event.key === 'Escape') {
            this.closeSearchForm();
        } else if (event.key === 'Tab') {
            this.trapTabKey(event);
        }
    }

    // Trap focus inside the search form
    private trapTabKey(event: KeyboardEvent): void {
        if (event.shiftKey) {
            if (document.activeElement === this.firstFocusableElement) {
                event.preventDefault();
                this.lastFocusableElement?.focus(); // Move focus to the last element
            }
        } else {
            if (document.activeElement === this.lastFocusableElement) {
                event.preventDefault();
                this.firstFocusableElement?.focus(); // Move focus to the first element
            }
        }
    }

    // Get first and last focusable elements
    private getFocusableElements(): [HTMLElement | null, HTMLElement | null] {
        const focusableElements = this.searchForm.querySelectorAll<HTMLElement>('input, button');
        return focusableElements.length
            ? [focusableElements[0], focusableElements[focusableElements.length - 1]]
            : [null, null];
    }
}

// Function to initialize the collapsible search form
export function initializeCollapsibleSearch(): void {
    document.addEventListener('DOMContentLoaded', () => {
        const collapsibleSearchElements = document.querySelectorAll('.collapsible-search-form');

        collapsibleSearchElements.forEach((collapsibleSearchElement) => {
            const triggerButton = collapsibleSearchElement.querySelector('.collapsible-search-form__trigger-button');
            const searchForm = collapsibleSearchElement.querySelector('.collapsible-search-form__form');
            const closeButton = collapsibleSearchElement.querySelector('.collapsible-search-form__close-button');
            const searchInput = collapsibleSearchElement.querySelector('.collapsible-search-form input');

            if (triggerButton && searchForm && closeButton && searchInput) {
                new CollapsibleSearchForm(
                    collapsibleSearchElement as HTMLElement,
                    triggerButton as HTMLElement, 
                    searchForm as HTMLElement, 
                    closeButton as HTMLElement, 
                    searchInput as HTMLElement
                );
            }
        });
    });
}
