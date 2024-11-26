class SizeObserver {
  constructor(private element: HTMLElement) {
    const resizeObserver = new ResizeObserver(entries => {
      for (const entry of entries) {
        const { width, height } = entry.contentRect;
        element.setAttribute('data-js-sizeobserver-width', `${width}`);
        element.setAttribute('data-js-sizeobserver-height', `${height}`);
      }
    });

    resizeObserver.observe(element);
  }
}

// Initializing the SizeObserver class
export function initializeSizeObserver(): void {
  document.querySelectorAll('[data-js-sizeobserver]').forEach((element) => {
      new SizeObserver(element as HTMLElement);
  });
}