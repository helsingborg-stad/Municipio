var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _Image = class _Image {
  constructor() {
    this.image = null;
    this.container = null;
    this.imgAttr = null;
    this.imgCss = null;
  }
  /**
   * Init
   * @return void
   */
  initImage(imageData) {
    this.container = imageData.elementContainer;
    this.imgAttr = imageData.attrList;
    this.imgCss = imageData.classList;
    this.image = document.createElement("img");
    this.appendImage();
  }
  /**
   * Setting Image Attributes
   * @return void
   */
  setAttr() {
    if (this.imgAttr.src) {
      for (const [key, value] of Object.entries(this.imgAttr)) {
        this.image.setAttribute(`${key}`, value);
      }
    }
  }
  /**
   * Adding CSS classes
   * @return void
   */
  setCSSClasses() {
    if (this.imgCss.length > 0) {
      for (const cssClass of this.imgCss) {
        this.image.classList.add(cssClass);
      }
    }
  }
  /**
   * Append image to container
   * @param img
   */
  appendImage() {
    this.setAttr();
    this.container.appendChild(this.image);
    this.setCSSClasses();
  }
};
__name(_Image, "Image");
let Image = _Image;
const _Gallery = class _Gallery {
  /**
   * @param {string|null} modalId
   */
  constructor(modalId = null) {
    this.imageDataSet = [];
    this.imageData = null;
    this.modalImg = "";
    this.modalId = modalId;
    this.container = null;
    this.isEnabled = false;
    this.imageTransitionTimeoutId = null;
    this.imageTransitionAnimationFrameId = null;
    this.imageTransitionId = 0;
    this.Image = new Image();
    this.handleContainerClickBound = (event) => this.handleContainerClick(event);
    this.handleKeyboardNavigationBound = (event) => this.handleKeyboardNavigation(event);
  }
  /**
   * Init Modal Image in Gallery
   * @param modalId
   * @param modalImage
   */
  initImage(modalId, modalImage) {
    this.modalId = modalId;
    this.modalImg = modalImage;
    this.enableGallery(modalId);
    if (!this.container) {
      return;
    }
    this.imageDataSet = this.collectImageDataSet(this.modalId);
    this.imageData = this.getImageDataByUrl(this.modalImg) || this.imageDataSet[0] || null;
    if (!this.imageData) {
      return;
    }
    this.createImg(this.container, this.imageData);
  }
  /**
   * Collect all image metadata connected to a specific modal id.
   * @param {string} modalId
   * @returns {Array<{image: string, imageStep: string, imageCaption: string}>}
   */
  collectImageDataSet(modalId) {
    const imageDataSet = [];
    const imageTriggers = document.querySelectorAll(`[data-open="${modalId}"][data-large-img]`);
    for (const trigger of imageTriggers) {
      const image = trigger.getAttribute("data-large-img");
      const imageStep = trigger.getAttribute("data-stepping") || String(imageDataSet.length);
      const imageCaption = trigger.getAttribute("data-caption") || "";
      if (image) {
        imageDataSet.push({
          image,
          imageStep,
          imageCaption
        });
      }
    }
    return imageDataSet;
  }
  /**
   * Get image metadata by full image url.
   * @param {string} imageUrl
   * @returns {{image: string, imageStep: string, imageCaption: string}|null}
   */
  getImageDataByUrl(imageUrl) {
    for (const imageData of this.imageDataSet) {
      if (imageData.image === imageUrl) {
        return imageData;
      }
    }
    return null;
  }
  /**
   * Enable Gallery
   * Next, Previous image by click or keys
   */
  enableGallery(modalId = this.modalId) {
    if (modalId) {
      this.modalId = modalId;
    }
    if (!this.modalId) {
      return;
    }
    this.container = document.getElementById(this.modalId);
    if (!this.container) {
      return;
    }
    if (!this.isEnabled) {
      this.container.addEventListener("click", this.handleContainerClickBound);
      document.addEventListener("keyup", this.handleKeyboardNavigationBound);
      this.isEnabled = true;
    }
  }
  /**
   * Next & previous Image
   * @param string nav
   * @returns {*}
   */
  cycleImage(nav = "prev") {
    if (!this.container || !this.imageData || this.imageDataSet.length === 0) {
      return null;
    }
    const currentIndex = this.getCurrentImageIndex();
    const nextIndex = nav === "next" ? (currentIndex + 1) % this.imageDataSet.length : (currentIndex - 1 + this.imageDataSet.length) % this.imageDataSet.length;
    const nextImageData = this.imageDataSet[nextIndex];
    this.createImg(this.container, nextImageData);
    return nextImageData;
  }
  /**
   * Create Image in modal
   * @param containerId
   * @param imgSrc
   */
  createImg(containerId, imgSrc) {
    const container = containerId?.querySelector(".c-image");
    const containerModalContent = container;
    this.imageData = imgSrc;
    if (!container || !containerModalContent || !imgSrc) {
      return;
    }
    const imageElement = container.querySelector(".c-image__image");
    if (!imageElement) {
      container.innerHTML = "";
      container.classList.remove("c-image--is-placeholder");
      this.Image.initImage({
        elementContainer: container,
        attrList: {
          src: imgSrc.image,
          "data-step": imgSrc.imageStep,
          "data-caption": imgSrc.imageCaption
        },
        classList: ["c-image__image", "c-image__image--is-visible"]
      });
    } else {
      this.transitionImage(imageElement, imgSrc, () => {
        this.imageCaption(containerModalContent, imgSrc);
        this.updateImageCounter(this.container);
      });
      return;
    }
    this.imageCaption(containerModalContent, imgSrc);
    this.updateImageCounter(this.container);
  }
  /**
   * Animate image replacement with fade-out and fade-in.
   * @param {HTMLImageElement} imageElement
   * @param {{image: string, imageStep: string, imageCaption: string}} imgSrc
   * @param {Function|null} onImageChanged
   */
  transitionImage(imageElement, imgSrc, onImageChanged = null) {
    if (!(imageElement instanceof HTMLImageElement)) {
      return;
    }
    this.imageTransitionId += 1;
    const currentTransitionId = this.imageTransitionId;
    if (this.imageTransitionTimeoutId) {
      window.clearTimeout(this.imageTransitionTimeoutId);
      this.imageTransitionTimeoutId = null;
    }
    if (this.imageTransitionAnimationFrameId) {
      window.cancelAnimationFrame(this.imageTransitionAnimationFrameId);
      this.imageTransitionAnimationFrameId = null;
    }
    const transitionDuration = this.getImageTransitionDuration();
    imageElement.classList.add("c-image__image--is-transitioning");
    imageElement.classList.remove("c-image__image--is-visible");
    this.imageTransitionTimeoutId = window.setTimeout(() => {
      if (currentTransitionId !== this.imageTransitionId) {
        return;
      }
      const preloadImage = new window.Image();
      const applyImage = /* @__PURE__ */ __name(() => {
        if (currentTransitionId !== this.imageTransitionId) {
          return;
        }
        imageElement.src = imgSrc.image;
        imageElement.setAttribute("data-step", imgSrc.imageStep);
        imageElement.setAttribute("data-caption", imgSrc.imageCaption || "");
        if (typeof onImageChanged === "function") {
          onImageChanged();
        }
        const revealImage = /* @__PURE__ */ __name(() => {
          this.imageTransitionAnimationFrameId = window.requestAnimationFrame(() => {
            if (currentTransitionId !== this.imageTransitionId) {
              return;
            }
            imageElement.classList.add("c-image__image--is-visible");
          });
          this.imageTransitionTimeoutId = window.setTimeout(() => {
            if (currentTransitionId !== this.imageTransitionId) {
              return;
            }
            imageElement.classList.remove("c-image__image--is-transitioning");
            this.imageTransitionTimeoutId = null;
          }, transitionDuration);
        }, "revealImage");
        if (typeof imageElement.decode === "function") {
          imageElement.decode().catch(() => void 0).finally(() => {
            if (currentTransitionId !== this.imageTransitionId) {
              return;
            }
            revealImage();
          });
          return;
        }
        revealImage();
      }, "applyImage");
      preloadImage.addEventListener("load", applyImage, { once: true });
      preloadImage.addEventListener("error", applyImage, { once: true });
      preloadImage.src = imgSrc.image;
    }, transitionDuration);
  }
  /**
   * Image transition duration in milliseconds.
   * @returns {number}
   */
  getImageTransitionDuration() {
    return 180;
  }
  /**
   * Setting image caption
   * @param containerModalContent
   * @param imgSrc
   */
  imageCaption(containerModalContent, imgSrc) {
    const existingCaption = containerModalContent.querySelector(".c-image__caption");
    if (existingCaption !== null) {
      existingCaption.remove();
    }
    if (imgSrc.imageCaption) {
      containerModalContent.insertAdjacentHTML("beforeend", '<figcaption class="c-image__caption">' + imgSrc.imageCaption + "</figcaption>");
    }
  }
  /**
   * Update gallery counter (current / total).
   * @param {HTMLElement} container
   */
  updateImageCounter(container) {
    if (!container) {
      return;
    }
    const counterContainer = this.getOrCreateCounterContainer(container);
    if (!counterContainer || !this.imageDataSet.length) {
      return;
    }
    const currentIndex = this.getCurrentImageIndex() + 1;
    const totalImages = this.imageDataSet.length;
    counterContainer.setAttribute("aria-live", "polite");
    counterContainer.textContent = `${currentIndex}/${totalImages}`;
  }
  /**
   * Find existing counter container or create one in modal content.
   * @param {HTMLElement} container
   * @returns {HTMLElement|null}
   */
  getOrCreateCounterContainer(container) {
    const existingCounterContainer = container.querySelector(".c-modal__counter");
    if (existingCounterContainer) {
      return existingCounterContainer;
    }
    const modalContent = container.querySelector(".c-modal__content");
    if (!modalContent) {
      return null;
    }
    const counterContainer = document.createElement("div");
    counterContainer.className = "c-modal__counter";
    modalContent.appendChild(counterContainer);
    return counterContainer;
  }
  /**
   * Resolve currently active image index.
   * @returns {number}
   */
  getCurrentImageIndex() {
    if (!this.imageData) {
      return 0;
    }
    const imageStep = parseInt(this.imageData.imageStep, 10);
    if (Number.isInteger(imageStep) && imageStep >= 0 && imageStep < this.imageDataSet.length) {
      return imageStep;
    }
    const imageIndex = this.imageDataSet.findIndex((imageData) => imageData.image === this.imageData.image);
    return imageIndex >= 0 ? imageIndex : 0;
  }
  /**
   * Handle local gallery controls.
   * @param {MouseEvent} event
   */
  handleContainerClick(event) {
    const trigger = event.target instanceof Element ? event.target.closest("[data-next], [data-prev]") : null;
    if (!trigger) {
      return;
    }
    if (trigger.hasAttribute("data-next")) {
      this.imageData = this.cycleImage("next");
      return;
    }
    if (trigger.hasAttribute("data-prev")) {
      this.imageData = this.cycleImage("prev");
    }
  }
  /**
   * Handle keyboard based gallery navigation.
   * @param {KeyboardEvent} event
   */
  handleKeyboardNavigation(event) {
    if (!this.container || !this.isModalOpen()) {
      return;
    }
    if (event.key === "ArrowRight") {
      this.imageData = this.cycleImage("next");
    }
    if (event.key === "ArrowLeft") {
      this.imageData = this.cycleImage("prev");
    }
  }
  /**
   * Check whether current modal is open.
   * @returns {boolean}
   */
  isModalOpen() {
    return Boolean(this.container && this.container.hasAttribute("open"));
  }
};
__name(_Gallery, "Gallery");
let Gallery = _Gallery;
const _Modal = class _Modal {
  modalId;
  openTrigger;
  closeTrigger;
  dialogs;
  galleryInstances;
  handleDocumentClickBound;
  handleReindexBound;
  constructor() {
    this.modalId = null;
    this.openTrigger = document.querySelectorAll("[data-open]");
    this.closeTrigger = document.querySelectorAll("[data-close]");
    this.dialogs = document.querySelectorAll(".c-modal");
    this.galleryInstances = /* @__PURE__ */ new Map();
    this.handleDocumentClickBound = (event) => this.handleDocumentClick(event);
    this.handleReindexBound = () => this.reindexTriggers();
    this.enableModals();
  }
  /**
   * Enable Modal
   */
  enableModals() {
    document.removeEventListener("click", this.handleDocumentClickBound);
    document.addEventListener("click", this.handleDocumentClickBound);
    document.removeEventListener("reindexModals", this.handleReindexBound);
    document.addEventListener("reindexModals", this.handleReindexBound);
    this.attachDialogEvents();
    document.dispatchEvent(new CustomEvent("enableStyleguideModals"));
  }
  /**
   * Programmatically open a modal
   */
  openModal(modalId, largeImgUrl = null) {
    const modal = document.getElementById(modalId);
    if (!modal) {
      console.warn(`Modal with ID "${modalId}" not found.`);
      return;
    }
    this.modalId = modalId;
    if (!modal.hasAttribute("open")) {
      modal.classList.add("c-modal--visible");
      if (typeof modal.showModal === "function") {
        modal.showModal();
      }
    }
    if (largeImgUrl) {
      const galleryInstance = this.getGalleryInstance(modalId);
      galleryInstance.enableGallery(modalId);
      galleryInstance.initImage(modalId, largeImgUrl);
    }
    this.lockScroll();
  }
  /**
   * Handle clicks outside the modal
   */
  handleClickOutside(e) {
    const dialogElement = e.currentTarget;
    const mouseEvent = e;
    if (!dialogElement) {
      return;
    }
    if (this.clickIsOutsideElement(dialogElement, mouseEvent.clientX, mouseEvent.clientY)) {
      dialogElement.close();
    }
  }
  /**
   * Check if a click is outside the modal
   */
  clickIsOutsideElement(element, clientX, clientY) {
    const boundingRect = element.getBoundingClientRect();
    return clientX < boundingRect.left || clientX > boundingRect.right || clientY < boundingRect.top || clientY > boundingRect.bottom;
  }
  /**
   * Lock scroll
   */
  lockScroll() {
    const overflowHidden = "u-overflow--hidden";
    document.body.classList.add(overflowHidden);
  }
  /**
   * Unlock scroll
   */
  unlockScroll() {
    const overflowHidden = "u-overflow--hidden";
    const hasOpenDialogs = Array.from(this.dialogs).some((dialog) => dialog.hasAttribute("open"));
    if (!hasOpenDialogs) {
      document.body.classList.remove(overflowHidden);
    }
  }
  /**
   * Reindex triggers and dialogs
   */
  reindexTriggers() {
    this.openTrigger = document.querySelectorAll("[data-open]");
    this.closeTrigger = document.querySelectorAll("[data-close]");
    this.dialogs = document.querySelectorAll(".c-modal");
    this.attachDialogEvents();
    for (const id of this.galleryInstances.keys()) {
      if (!document.getElementById(id)) {
        this.galleryInstances.delete(id);
      }
    }
  }
  attachDialogEvents() {
    for (const dialog of this.dialogs) {
      if (dialog.dataset.modalBound === "true") {
        continue;
      }
      dialog.addEventListener("close", () => {
        dialog.classList.remove("c-modal--visible");
        this.unlockScroll();
      });
      dialog.addEventListener("click", (event) => this.handleClickOutside(event));
      dialog.dataset.modalBound = "true";
    }
  }
  handleDocumentClick(event) {
    const trigger = event.target?.closest("[data-open], [data-close]");
    if (!trigger) {
      return;
    }
    const openModalId = trigger.getAttribute("data-open");
    if (openModalId) {
      this.openModal(openModalId, trigger.getAttribute("data-large-img"));
      return;
    }
    const closeTrigger = trigger.getAttribute("data-close");
    if (closeTrigger !== null) {
      const closestDialog = trigger.closest("dialog");
      if (closestDialog?.hasAttribute("open")) {
        event.stopPropagation();
        closestDialog.close();
      }
    }
  }
  getGalleryInstance(modalId) {
    const existingGalleryInstance = this.galleryInstances.get(modalId);
    if (existingGalleryInstance) {
      return existingGalleryInstance;
    }
    const galleryInstance = new Gallery(modalId);
    this.galleryInstances.set(modalId, galleryInstance);
    return galleryInstance;
  }
};
__name(_Modal, "Modal");
let Modal = _Modal;
function initializeModal() {
  new Modal();
}
__name(initializeModal, "initializeModal");
if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initializeModal);
else initializeModal();
//# sourceMappingURL=modal.js.map
