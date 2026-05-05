import NavigationItem from "./navigation-item";

class Slider {
    private readonly noHoverClass = 'no-hover';

    constructor(
        public sliderElement: HTMLElement,
        public splide: any,
        public navigationItems: { item: HTMLElement, instance: NavigationItem }[],
        public backdropBanner: HTMLElement
    ) {
        this.setListeners();
    }

    private setListeners() {
        this.navigationItems.forEach((navigationItemsPair, index) => {
            navigationItemsPair.item.addEventListener('mouseenter', () => {
                // Removes hover for mobile
                if (!this.allowHover()) {
                    return;
                }

                this.splide.go(index);
            });
        });

        this.splide.on('move', (newIndex: number) => {
            
            this.navigationItems[newIndex].instance.activate();
        });
    }

    private allowHover(): boolean {
        return !this.backdropBanner.classList.contains(this.noHoverClass);
    }
}

export function initializeSlider(navigationItems: { item: HTMLElement, instance: NavigationItem }[], backdropBanner: HTMLElement) {
    document.addEventListener('slider:ready', (slider: any) => {
        if (!slider.detail?.sliderElement || !slider.detail?.splide) {
            return;
        }

        if (!slider.detail.sliderElement.dataset.jsBackdropBannerSlider) {
            return;
        }

        new Slider(slider.detail.sliderElement, slider.detail.splide, navigationItems, backdropBanner);
    });
}