export default class BackgroundImage {
    private currentImage: string | null = null;
    private currentPosition: string | null = null;
    private startImage: string | null = null;
    private startPosition: string | null = null;

    constructor(
        private frontLayer: HTMLElement,
        private backLayer: HTMLElement
    ) {
        this.startImage = this.frontLayer.dataset.jsStartImage || null;
        this.startPosition = this.frontLayer.style.backgroundPosition || null;

        // no swapping needed
        [this.frontLayer, this.backLayer].forEach(layer => {
            layer.addEventListener('transitionend', (event) => {
                if (event.propertyName !== 'opacity') return;
                if (layer.style.opacity === '0') {
                    layer.style.opacity = '0';
                }
            });
        });
    }

    public showImage(url: string, backgroundPosition = '50% 50%') {
        if (this.currentImage === url && this.currentPosition === backgroundPosition) return;
        if (
            this.currentImage === null
            && this.startImage === url
            && this.startPosition === backgroundPosition
        ) {
            return;
        }

        const hiddenLayer = this.frontLayer.style.opacity === '0' ? this.frontLayer : this.backLayer;
        const visibleLayer = hiddenLayer === this.frontLayer ? this.backLayer : this.frontLayer;

        hiddenLayer.style.backgroundImage = `url(${url})`;
        hiddenLayer.style.backgroundPosition = backgroundPosition;
        hiddenLayer.style.opacity = '1';
        visibleLayer.style.opacity = '0';

        this.currentImage = url;
        this.currentPosition = backgroundPosition;
    }
}