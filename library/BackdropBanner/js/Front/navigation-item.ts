import type BackgroundImage from "./background-image";

export default class NavigationItem {
    private imageUrlAttribute = "data-js-backdrop-banner-image-url";
    private navigationItemAttribute = "data-js-backdrop-banner-navigation-item";
    private activeClass = "is-active";
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
        this.setListener();
        this.focalPointX = Number(this.navItem.getAttribute(this.focalPointXAttribute) ?? 0.5);
        this.focalPointY = Number(this.navItem.getAttribute(this.focalPointYAttribute) ?? 0.5);

        if (this.imageUrl) {
            const img = new Image();
            img.src = this.imageUrl;
        }

        if (this.navItem.classList.contains(this.activeClass)) {
            this.activate();
        }
    }

    private setListener(): void {
        this.navItem.addEventListener("mouseenter", () => {
            this.activate();
        });

        this.navItem.addEventListener("focusin", () => {
            this.activate();
        });
    }

    private activate(): void {
        this.setActiveClass();

        if (!this.imageUrl) {
            return;
        }

        this.backgroundImage.showImage(this.imageUrl);
    }

    private setActiveClass(): void {
        const banner = this.navItem.closest("[data-js-backdrop-banner]");
        const navigationItems = banner?.querySelectorAll<HTMLElement>(
            `[${this.navigationItemAttribute}]`,
        );

        navigationItems?.forEach((item) => {
            item.classList.remove(this.activeClass);
            if (!this.imageUrl) return;
            this.backgroundImage.showImage(
                this.imageUrl,
                `${this.focalPointX * 100}% ${this.focalPointY * 100}%`,
            );
        });

        this.navItem.classList.add(this.activeClass);
    }
}
