import BackgroundImage from './background-image';
import NavigationItem from './navigation-item';

document.querySelectorAll<HTMLElement>('[data-js-backdrop-banner]').forEach((backdropBanner) => {
    const navigationItems = backdropBanner.querySelectorAll<HTMLElement>('[data-js-backdrop-banner-navigation-item]');
    const backdropBannerImageFront = backdropBanner.querySelector<HTMLElement>('[data-js-backdrop-banner-image-front]');
    const backdropBannerImageBack = backdropBanner.querySelector<HTMLElement>('[data-js-backdrop-banner-image-back]');

    if (!backdropBannerImageFront || !backdropBannerImageBack) return;

    const backgroundImage = new BackgroundImage(backdropBannerImageFront, backdropBannerImageBack);
    navigationItems.forEach(navItem => {
        new NavigationItem(navItem, backgroundImage);
    });
});