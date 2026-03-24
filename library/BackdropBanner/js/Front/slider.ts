import NavigationItem from "./navigation-item";

class Slider {
    constructor(
        public sliderElement: HTMLElement,
        public splide: any,
        public navigationItems: { item: HTMLElement, instance: NavigationItem }[],
    ) {
        this.setListeners();
    }

    private setListeners() {
        this.navigationItems.forEach((navigationItemsPair, index) => {
            navigationItemsPair.item.addEventListener('mouseenter', () => {
                this.splide.go(index);
            });
        });

        this.splide.on('move', (newIndex: number) => {
            this.navigationItems[newIndex].instance.activate();
        });
    }
}

export function initializeSlider(navigationItems: { item: HTMLElement, instance: NavigationItem }[]) {
    document.addEventListener('slider:ready', (slider: any) => {
        if (!slider.detail?.sliderElement || !slider.detail?.splide) {
            return;
        }

        if (!slider.detail.sliderElement.dataset.jsBackdropBannerSlider) {
            return;
        }

        new Slider(slider.detail.sliderElement, slider.detail.splide, navigationItems);
    });
}