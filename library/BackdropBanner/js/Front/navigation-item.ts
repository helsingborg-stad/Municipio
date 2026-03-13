import type BackgroundImage from "./background-image";

export default class NavigationItem {
    private imageUrlAttribute = "data-js-backdrop-banner-image-url";
    private imageUrl: string | null;

    constructor(
        private navItem: Element,
        private backgroundImage: BackgroundImage,
    ) {
        this.imageUrl = this.navItem.getAttribute(this.imageUrlAttribute);

        if (this.imageUrl) {
            const img = new Image();
            img.src = this.imageUrl;
            this.setListener();
        }
    }

    private setListener(): void {
        this.navItem.addEventListener("mouseenter", () => {
            if (!this.imageUrl) return;
            this.backgroundImage.showImage(this.imageUrl);
        });
    }
}
