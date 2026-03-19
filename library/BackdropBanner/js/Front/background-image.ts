export default class BackgroundImage {
    private currentImage: string | null = null;
    private startImage: string | null = null;

    constructor(
        private frontLayer: HTMLElement,
        private backLayer: HTMLElement
    ) {
        this.startImage = this.frontLayer.getAttribute('data-js-start-image');

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
        if (this.currentImage === null && this.startImage === url) return;

        const hiddenLayer = this.frontLayer.style.opacity === '0' ? this.frontLayer : this.backLayer;
        const visibleLayer = hiddenLayer === this.frontLayer ? this.backLayer : this.frontLayer;

        hiddenLayer.style.backgroundImage = `url(${url})`;
        hiddenLayer.style.opacity = '1';
        visibleLayer.style.opacity = '0';

        this.currentImage = url;
    }
}