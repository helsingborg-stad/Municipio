import BackgroundImage from './background-image';
import NavigationItem from './navigation-item';
import { initializeSlider } from './slider';

document.querySelectorAll<HTMLElement>('[data-js-backdrop-banner]').forEach((backdropBanner) => {
    const navigationItems = backdropBanner.querySelectorAll<HTMLElement>('[data-js-backdrop-banner-navigation-item]');
    const backdropBannerImageFront = backdropBanner.querySelector<HTMLElement>('[data-js-backdrop-banner-image-front]');
    const backdropBannerImageBack = backdropBanner.querySelector<HTMLElement>('[data-js-backdrop-banner-image-back]');


    if (!backdropBannerImageFront || !backdropBannerImageBack) return;

    let navigationItemsArray: { item: HTMLElement, instance: NavigationItem }[] = [];
    const backgroundImage = new BackgroundImage(backdropBannerImageFront, backdropBannerImageBack);
    navigationItems.forEach(navItem => {
        const navigationItem = new NavigationItem(navItem, backgroundImage, navigationItems);
        navigationItemsArray.push({ item: navItem, instance: navigationItem });
    });

    initializeSlider(navigationItemsArray);
});