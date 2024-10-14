export class CollapsibleSearchForm {
    constructor(triggerButtonSelector, searchFormSelector, closeButtonSelector, inputSelector) {
        this.triggerButton = document.querySelector(triggerButtonSelector);
        this.searchForm = document.querySelector(searchFormSelector);
        this.closeButton = document.querySelector(closeButtonSelector);
        this.searchInput = document.querySelector(inputSelector);
        this.lastFocusedElement = null;

        // Custom event declarations
        this.searchFormOpened = new CustomEvent('searchFormOpened');
        this.searchFormClosed = new CustomEvent('searchFormClosed');

        // Bind methods to this instance
        this.openSearchForm = this.openSearchForm.bind(this);
        this.closeSearchForm = this.closeSearchForm.bind(this);
        this.outsideClickListener = this.outsideClickListener.bind(this);
        this.trapTabKey = this.trapTabKey.bind(this);

        // Initialize event listeners
        this.initializeEvents();
    }

    // Initialize event listeners
    initializeEvents() {
        // Event listener for opening the form
        this.triggerButton.addEventListener('click', this.openSearchForm);

        // Event listener for closing the form via the close button
        this.closeButton.addEventListener('click', this.closeSearchForm);
    }

    // Function to open the search form
    openSearchForm() {
        this.lastFocusedElement = document.activeElement;
        this.searchForm.classList.remove('closing');
        this.searchForm.setAttribute('aria-hidden', 'false');
        this.triggerButton.setAttribute('aria-expanded', 'true');
        this.searchForm.classList.add('open');

        setTimeout(() => {
            this.searchInput.focus(); // Focus the search input after opening
        }, 500);

        // Emit custom event for form opened
        document.dispatchEvent(this.searchFormOpened);

        // Add event listener for closing the form on outside click
        document.addEventListener('click', this.outsideClickListener);
        document.addEventListener('keydown', this.trapTabKey); // Add focus trap
    }

    // Function to close the search form
    closeSearchForm() {
        this.searchInput.blur();
        this.searchForm.classList.remove('open');
        this.searchForm.setAttribute('aria-hidden', 'true');
        this.triggerButton.setAttribute('aria-expanded', 'false');
        this.searchForm.classList.add('closing');

        // Wait for the transition to finish before fully hiding the form
        setTimeout(() => {
            this.searchForm.classList.remove('closing'); // Clean up classes
            if (this.lastFocusedElement) {
                this.lastFocusedElement.focus(); // Restore focus to the last focused element
            }
        }, 500); // Match the CSS transition duration (0.5s)

        // Emit custom event for form closed
        document.dispatchEvent(this.searchFormClosed);

        // Remove event listeners after closing
        document.removeEventListener('click', this.outsideClickListener);
        document.removeEventListener('keydown', this.trapTabKey); // Remove focus trap
    }

    // Close the form if clicking outside of it
    outsideClickListener(event) {
        // Check if the click is outside the search form and trigger button
        if (!this.searchForm.contains(event.target) && !this.triggerButton.contains(event.target)) {
            this.closeSearchForm();
        }
    }

    // Trap focus inside the search form
    trapTabKey(event) {
        const focusableElements = this.searchForm.querySelectorAll('input, button'); // All focusable elements inside the form
        const firstElement = focusableElements[0]; // First element (input field)
        const lastElement = focusableElements[focusableElements.length - 1]; // Last element (close button)

        if (event.key === 'Tab') {
            if (event.shiftKey) {
                // Shift + Tab pressed, move focus backward
                if (document.activeElement === firstElement) {
                    event.preventDefault();
                    lastElement.focus(); // Move focus to the last element
                }
            } else {
                // Tab pressed, move focus forward
                if (document.activeElement === lastElement) {
                    event.preventDefault();
                    firstElement.focus(); // Move focus to the first element
                }
            }
        } else if (event.key === 'Escape') {
            // Close the form when Escape is pressed
            this.closeSearchForm();
        }
    }
}

// Function to initialize the collapsible search form
export function initializeCollapsibleSearch() {
    const collapsibleSearchElement = document.querySelector('.collapsible-search-form');
    if (collapsibleSearchElement) {
        new CollapsibleSearchForm(
            '.collapsible-search-form__trigger-button', 
            '.collapsible-search-form__form', 
            '.collapsible-search-form__close-button', 
            '.collapsible-search-form input'
        );
    }
}