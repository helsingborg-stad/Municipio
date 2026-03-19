class Slider {
    constructor(
        public sliderElement: HTMLElement,
        public splide: any,
        public navigationItems: NodeListOf<HTMLElement>
    ) {
        this.setListeners();
    }

    private setListeners() {
        this.navigationItems.forEach((item, index) => {
            item.addEventListener('mouseenter', () => {
                this.splide.go(index);
            });
        });

        this.splide.on('move', (newIndex: number) => {
            console.log('slide changed', newIndex);
        });
    }
}

export function initializeSlider(navigationItems: NodeListOf<HTMLElement>) {
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