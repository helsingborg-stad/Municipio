import type BackgroundImage from "./background-image";

export default class NavigationItem {
    private imageUrlAttribute = "data-js-backdrop-banner-image-url";
    private navigationItemAttribute = "data-js-backdrop-banner-navigation-item";
    private activeClass = "is-active";
    private imageUrl: string | null;

    constructor(
        private navItem: Element,
        private backgroundImage: BackgroundImage,
    ) {
        this.imageUrl = this.navItem.getAttribute(this.imageUrlAttribute);
        this.setListener();

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
        });

        this.navItem.classList.add(this.activeClass);
    }
}
