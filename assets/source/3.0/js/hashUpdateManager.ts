export class HashUpdateManager {
  private static currentHash: string | null = null;
  private static offset: number = 0;

  /**
   * Initialize with optional offset
   */
  public static init(offset = 0): void {
    this.offset = offset;

    document.addEventListener('DOMContentLoaded', () => {
      const elements = document.querySelectorAll<HTMLElement>('[data-update-hash-when-focused]');
      if (!elements.length) return;

      this.handleIntersect();

      window.addEventListener('scroll', () => this.handleIntersect());
      window.addEventListener('resize', () => this.handleIntersect());
    });
  }

  private static handleIntersect(): void {
    const elements = document.querySelectorAll<HTMLElement>('[data-update-hash-when-focused]');
    if (elements.length === 0) return;

    let candidate: HTMLElement | null = null;
    let candidateDistance = -Infinity;

    elements.forEach(el => {
      const rect = el.getBoundingClientRect();
      const top = rect.top;

      // Use the configurable offset here
      if (top <= this.offset && top > candidateDistance) {
        candidate = el;
        candidateDistance = top;
      }
    });

    if (!candidate) candidate = elements[0];

    const newHash = candidate.dataset.updateHashValue ?? candidate.id;

    if (!newHash || this.currentHash === newHash) return;

    this.currentHash = newHash;
    history.replaceState(null, '', `#${newHash}`);
    window.dispatchEvent(new HashChangeEvent('hashchange'));
  }
}

export function initializeHashUpdateManager(offset = 0): void {
  HashUpdateManager.init(offset);
}