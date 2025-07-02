/**
 * Hash Update Manager
 *
 * Watches elements with `data-update-hash-when-focused` and updates
 * the URL hash when they become focal (e.g., visible in viewport).
 */
export class HashUpdateManager {
  private static throttleTimeoutId: number | null = null;
  private static throttleDelay = 50;

  /**
   * Initialize hash update behavior based on focus/visibility of elements
   */
  public static init(): void {
    document.addEventListener('DOMContentLoaded', () => {
      const elements = document.querySelectorAll<HTMLElement>('[data-update-hash-when-focused]');
     
      if (!elements.length) {
        return;
      }

      // Allow override of throttle delay
      const customDelay = elements[0].dataset.updateHashThrottle;
      if (customDelay) {
        const parsed = parseInt(customDelay, 10);
        if (!isNaN(parsed)) this.throttleDelay = parsed;
      }

      const onScrollOrResize = this.debounce(() => {
        this.updateHashBasedOnFocus(elements);
      }, this.throttleDelay);

      window.addEventListener('scroll', onScrollOrResize, { passive: true });
      window.addEventListener('resize', onScrollOrResize);

      // Initial run
      this.updateHashBasedOnFocus(elements);
    });
  }

  /**
   * Debounce wrapper to limit function execution
   */
  private static debounce(fn: () => void, delay: number): () => void {
    let timeoutId: number | null = null;
    return () => {
      if (timeoutId !== null) {
        clearTimeout(timeoutId);
      }
      timeoutId = window.setTimeout(() => {
        fn();
        timeoutId = null;
      }, delay);
    };
  }

  /**
   * Check which element is currently most visible / focal and update hash accordingly
   */
  private static updateHashBasedOnFocus(elements: NodeListOf<HTMLElement>): void {
    const focalElement = Array.from(elements).reduce<HTMLElement | null>((best, el) => {
      return this.getVisibilityRatio(el) > (best ? this.getVisibilityRatio(best) : 0) ? el : best;
    }, null);

    if (!focalElement) return;

    const desiredHash = focalElement.dataset.updateHashValue ?? focalElement.id;
    if (!desiredHash) return;

    const currentHash = decodeURIComponent(location.hash.replace(/^#/, ''));
    if (currentHash !== desiredHash) {
      const scrollX = window.scrollX;
      const scrollY = window.scrollY;
      location.hash = desiredHash;
      window.scrollTo(scrollX, scrollY); // Prevent jump by restoring scroll position
    } else {
      window.dispatchEvent(new HashChangeEvent('hashchange'));
    }
  }

  /**
   * Calculate how much of the element is visible in viewport (0 to 1)
   */
  private static getVisibilityRatio(el: HTMLElement): number {
    const rect = el.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;

    if (rect.bottom <= 0 || rect.top >= windowHeight) {
      return 0;
    }

    const visibleTop = Math.max(rect.top, 0);
    const visibleBottom = Math.min(rect.bottom, windowHeight);
    return (visibleBottom - visibleTop) / rect.height;
  }
}

export function initializeHashUpdateManager(): void {
  HashUpdateManager.init();
  console.log('HashUpdateManager initialized');
}