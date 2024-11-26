class SizeObserver {
  private elements: NodeListOf<HTMLElement>;

  constructor(private selector: string = '[data-js-sizeobserver]') {
      this.elements = document.querySelectorAll<HTMLElement>(selector);

      // Initialize size observers
      this.updateSizes();
      this.initializeEvents();
  }

  // Initialize event listeners
  private initializeEvents(): void {
      window.addEventListener('resize', this.updateSizes);
      document.addEventListener('DOMContentLoaded', this.updateSizes);
  }

  // Update size attributes on all observed elements
  private updateSizes = (): void => {
      this.elements.forEach((element) => {
          const width = element.offsetWidth;
          const height = element.offsetHeight;

          element.setAttribute('data-js-sizeobserver-width', width.toString());
          element.setAttribute('data-js-sizeobserver-height', height.toString());
      });
  };
}

// Function to initialize the size observer
export function initializeSizeObserver(): void {
  new SizeObserver();
}

// Automatically initialize when the module is loaded
initializeSizeObserver();