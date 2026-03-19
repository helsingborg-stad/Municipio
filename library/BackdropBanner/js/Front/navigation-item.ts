import type BackgroundImage from "./background-image";

export default class NavigationItem {
    private imageUrlAttribute = "data-js-backdrop-banner-image-url";
    private focalPointXAttribute = "data-js-backdrop-banner-image-focal-x";
    private focalPointYAttribute = "data-js-backdrop-banner-image-focal-y";
    private imageUrl: string | null;
    private focalPointX: number;
    private focalPointY: number;

    constructor(
        private navItem: Element,
        private backgroundImage: BackgroundImage,
    ) {
        this.imageUrl = this.navItem.getAttribute(this.imageUrlAttribute);
        this.focalPointX = Number(this.navItem.getAttribute(this.focalPointXAttribute) ?? 0.5);
        this.focalPointY = Number(this.navItem.getAttribute(this.focalPointYAttribute) ?? 0.5);

        if (this.imageUrl) {
            const img = new Image();
            img.src = this.imageUrl;
            this.setListener();
        }
    }

    private setListener(): void {
        this.navItem.addEventListener("mouseenter", () => {
            if (!this.imageUrl) return;
            this.backgroundImage.showImage(
                this.imageUrl,
                `${this.focalPointX * 100}% ${this.focalPointY * 100}%`,
            );
        });
    }
}
