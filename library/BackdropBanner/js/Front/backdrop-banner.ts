
console.log("Backdrop banner script loaded");
const backdropBanners = document.querySelectorAll(".wp-block-municipio-backdrop-banner");
backdropBanners.forEach((backdropBanner) => {
    const imageUrl = backdropBanner.getAttribute("data-image-url");
    if (imageUrl) {
        console.log("Setting backdrop banner image:", imageUrl);
    }
});