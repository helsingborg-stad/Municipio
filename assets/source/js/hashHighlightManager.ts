/**
 * Highlight elements based on URL hash matching a data attribute.
 */
export class HashHighlightManager {
  private static lastManualHashChange = 0;

  /**
   * Initialize the hash highlight system.
   * Finds all elements with `data-highlight-on-hash-match` and toggles
   * the class specified in `data-highlight-on-hash-match-class` when the URL hash matches.
   */
  public static init(): void {
    document.addEventListener('DOMContentLoaded', () => {
      const items = document.querySelectorAll<HTMLElement>('[data-highlight-on-hash-match]');

      if (!items.length) return;

      const updateHighlights = (): void => {
        const currentHash = decodeURIComponent(location.hash.replace(/^#/, ''));
        items.forEach(item => {
          const targetHash = item.dataset.highlightOnHashMatch;
          const activeClass = item.dataset.highlightOnHashMatchClass ?? 'is-current';
          const shouldBeActive = targetHash === currentHash;
          item.classList.toggle(activeClass, shouldBeActive);
        });
      };
      window.addEventListener('hashchange', updateHighlights);
      updateHighlights(); // initial check
    });
  }
}

export function initializeHashHighlightManager(): void {
  HashHighlightManager.init();
}