export default class BackgroundImage {
    private currentImage: string | null = null;

    constructor(
        private frontLayer: HTMLElement,
        private backLayer: HTMLElement
    ) {
        // no swapping needed
        [this.frontLayer, this.backLayer].forEach(layer => {
            layer.addEventListener('transitionend', (event) => {
                if (event.propertyName !== 'opacity') return;
                // ensure the hidden layer is always fully transparent
                if (layer.style.opacity === '0') {
                    layer.style.opacity = '0';
                }
            });
        });
    }

    public showImage(url: string) {
        if (this.currentImage === url) return;

        // Determine hidden and visible layers dynamically
        const hiddenLayer = this.frontLayer.style.opacity === '0' ? this.frontLayer : this.backLayer;
        const visibleLayer = hiddenLayer === this.frontLayer ? this.backLayer : this.frontLayer;

        hiddenLayer.style.backgroundImage = `url(${url})`;
        hiddenLayer.style.opacity = '1';
        visibleLayer.style.opacity = '0';

        this.currentImage = url;
    }
}