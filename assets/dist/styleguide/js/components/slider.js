var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { V as VideoControls } from "../../video.js";
const AUTOSLIDE = "data-js-slider__autoslide";
const PAUSE_TOGGLE = "c-slider__autoslide-toggle";
const IS_PAUSED = "c-slider--is-paused";
const SLIDER_ITEM = "c-slider__item";
const SLIDE_BY_PAGE = "data-js-slider-slide-by-page";
const SLIDER_READY_EVENT = "slider:ready";
const _Slider = class _Slider {
  sliderElement;
  track;
  slides;
  prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  autoplayDelay;
  autoplayTimer;
  scrollAnimationFrame;
  constructor(slider) {
    this.sliderElement = slider;
    this.track = this.sliderElement.querySelector("[data-js-slider-track]");
    this.slides = Array.from(this.sliderElement.querySelectorAll(`[data-js-slider-slide], .${SLIDER_ITEM}`));
    this.autoplayDelay = this.getAutoplayDelay();
    this.setTrackLabel();
    this.lazyloadVideo();
    this.addVideoControls();
    if (!this.track || this.slides.length < 2) {
      this.sliderElement.querySelector(".c-slider__arrows")?.remove();
      this.dispatchReadyEvent();
      return;
    }
    this.updateSlideMotionFromScroll();
    this.updateNavigation();
    this.setupNavigation();
    this.setupAutoplay();
    this.dispatchReadyEvent();
  }
  getAutoplayDelay() {
    const delay = Number.parseInt(this.sliderElement.getAttribute(AUTOSLIDE) ?? "0", 10);
    return Number.isFinite(delay) && delay > 0 ? delay * 1e3 : 0;
  }
  setupNavigation() {
    const buttons = this.getNavigationButtons();
    buttons.previous?.addEventListener("click", () => this.goBy(-this.getNavigationStep(), true));
    buttons.next?.addEventListener("click", () => this.goBy(this.getNavigationStep(), true));
    this.track?.addEventListener("scroll", () => this.handleScroll(), { passive: true });
    this.track?.addEventListener("keydown", (event) => this.handleKeydown(event));
  }
  handleScroll() {
    if (this.scrollAnimationFrame !== void 0) return;
    this.scrollAnimationFrame = window.requestAnimationFrame(() => {
      this.updateSlideMotionFromScroll();
      this.updateNavigation();
      this.scrollAnimationFrame = void 0;
    });
  }
  /**
   * Mirrors Minimal Slider's scroll-driven fade: a slide's opacity reflects
   * the proportion currently visible in the viewport.
   */
  updateSlideMotionFromScroll() {
    if (!this.track || this.prefersReducedMotion.matches) {
      this.slides.forEach((slide) => {
        slide.style.removeProperty("--c-slider--slide-progress");
      });
      return;
    }
    const viewportLeft = this.track.scrollLeft;
    const viewportRight = viewportLeft + this.track.clientWidth;
    this.slides.forEach((slide) => {
      const slideLeft = slide.offsetLeft;
      const slideWidth = Math.max(1, slide.offsetWidth);
      const slideRight = slideLeft + slideWidth;
      const visibleWidth = Math.max(0, Math.min(slideRight, viewportRight) - Math.max(slideLeft, viewportLeft));
      slide.style.setProperty("--c-slider--slide-progress", (visibleWidth / slideWidth).toFixed(3));
    });
  }
  handleKeydown(event) {
    switch (event.key) {
      case "ArrowLeft":
        event.preventDefault();
        this.goBy(-this.getNavigationStep(), true);
        break;
      case "ArrowRight":
        event.preventDefault();
        this.goBy(this.getNavigationStep(), true);
        break;
      case "Home":
        event.preventDefault();
        this.goTo(0, true);
        break;
      case "End":
        event.preventDefault();
        this.goTo(this.getLastStartIndex(), true);
        break;
    }
  }
  goBy(offset, announce = false) {
    this.goTo(this.getCurrentIndex() + offset, announce);
  }
  goTo(index, announce = false) {
    const targetIndex = Math.max(0, Math.min(index, this.getLastStartIndex()));
    const target = this.slides[targetIndex];
    if (announce) this.announceSlide(targetIndex);
    if (!this.track || !target) return;
    this.track.scrollTo({
      left: target.offsetLeft,
      behavior: this.prefersReducedMotion.matches ? "auto" : "smooth"
    });
  }
  getCurrentIndex() {
    if (!this.track) return 0;
    return this.slides.reduce((currentIndex, slide, index) => {
      const currentDistance = Math.abs(this.slides[currentIndex].offsetLeft - this.track.scrollLeft);
      const nextDistance = Math.abs(slide.offsetLeft - this.track.scrollLeft);
      return nextDistance < currentDistance ? index : currentIndex;
    }, 0);
  }
  getNavigationStep() {
    if (this.sliderElement.getAttribute(SLIDE_BY_PAGE) !== "true") return 1;
    const inner = this.sliderElement.querySelector("[data-js-slider-inner]");
    const firstSlide = this.slides[0];
    if (!this.track || !inner || !firstSlide) return 1;
    const gap = Number.parseFloat(getComputedStyle(inner).columnGap) || 0;
    const columns = Math.round((this.track.clientWidth + gap) / (firstSlide.offsetWidth + gap));
    return Math.max(1, columns);
  }
  getLastStartIndex() {
    return this.slides.length - (this.sliderElement.getAttribute(SLIDE_BY_PAGE) === "true" ? this.getNavigationStep() : 1);
  }
  getNavigationButtons() {
    const selector = this.sliderElement.getAttribute("data-js-slider-buttons");
    const container = selector ? document.getElementById(selector) : this.sliderElement;
    return {
      previous: container?.querySelector("[data-js-slider-prev]") ?? null,
      next: container?.querySelector("[data-js-slider-next]") ?? null
    };
  }
  updateNavigation() {
    if (!this.track) return;
    const { previous, next } = this.getNavigationButtons();
    const atStart = this.track.scrollLeft <= 1;
    const atEnd = this.track.scrollLeft >= this.track.scrollWidth - this.track.clientWidth - 1;
    this.setButtonState(previous, atStart);
    this.setButtonState(next, atEnd);
    const currentSlide = this.getCurrentIndex() + 1;
    this.slides.forEach((slide, index) => {
      slide.setAttribute("aria-label", `${index + 1} of ${this.slides.length}`);
    });
    this.track.setAttribute("aria-label", `Slider, slide ${currentSlide} of ${this.slides.length}`);
  }
  announceSlide(startIndex) {
    const status = this.sliderElement.querySelector("[data-js-slider-status]");
    if (!status) return;
    const endIndex = Math.min(this.slides.length - 1, startIndex + this.getNavigationStep() - 1);
    status.textContent = startIndex === endIndex ? `Slide ${startIndex + 1} of ${this.slides.length}` : `Slides ${startIndex + 1} to ${endIndex + 1} of ${this.slides.length}`;
  }
  setButtonState(button, disabled) {
    if (!button) return;
    button.disabled = disabled;
    button.setAttribute("aria-disabled", String(disabled));
  }
  setupAutoplay() {
    const toggle = this.sliderElement.querySelector(`.${PAUSE_TOGGLE}`);
    if (!this.autoplayDelay || !toggle) return;
    toggle.setAttribute("aria-label", "Pause slider");
    toggle.addEventListener("click", () => this.toggleAutoplay());
    this.sliderElement.addEventListener("mouseenter", () => this.stopAutoplay());
    this.sliderElement.addEventListener("mouseleave", () => this.startAutoplay());
    this.sliderElement.addEventListener("focusin", () => this.stopAutoplay());
    this.sliderElement.addEventListener("focusout", () => this.startAutoplay());
    if (!this.prefersReducedMotion.matches && !this.sliderElement.classList.contains(IS_PAUSED)) {
      this.startAutoplay();
    }
  }
  toggleAutoplay() {
    const paused = !this.sliderElement.classList.contains(IS_PAUSED);
    this.sliderElement.classList.toggle(IS_PAUSED, paused);
    const toggle = this.sliderElement.querySelector(`.${PAUSE_TOGGLE}`);
    toggle?.setAttribute("aria-label", paused ? "Play slider" : "Pause slider");
    this.announceAutoplay(paused);
    this.sliderElement.querySelectorAll("video").forEach((video) => {
      if (paused) {
        video.pause();
      } else {
        void video.play();
      }
    });
    paused ? this.stopAutoplay() : this.startAutoplay();
  }
  announceAutoplay(paused) {
    const status = this.sliderElement.querySelector("[data-js-slider-status]");
    if (status) status.textContent = paused ? "Autoplay paused" : "Autoplay playing";
  }
  startAutoplay() {
    if (!this.autoplayDelay || this.prefersReducedMotion.matches || this.sliderElement.classList.contains(IS_PAUSED) || this.autoplayTimer) return;
    this.autoplayTimer = window.setInterval(() => {
      const atEnd = this.track && this.track.scrollLeft >= this.track.scrollWidth - this.track.clientWidth - 1;
      this.goTo(atEnd ? 0 : this.getCurrentIndex() + this.getNavigationStep());
    }, this.autoplayDelay);
  }
  stopAutoplay() {
    if (this.autoplayTimer === void 0) return;
    window.clearInterval(this.autoplayTimer);
    this.autoplayTimer = void 0;
  }
  lazyloadVideo() {
    this.slides.forEach((slide) => {
      const sourceUrl = slide.getAttribute("data-js-slider-video");
      const source = slide.querySelector("video source");
      if (sourceUrl && source) {
        source.src = sourceUrl;
        source.closest("video")?.load();
      }
    });
  }
  addVideoControls() {
    this.slides.forEach((slide) => {
      if (slide.querySelector("video")) new VideoControls(slide);
    });
  }
  setTrackLabel() {
    this.track?.setAttribute("aria-label", this.sliderElement.getAttribute("aria-label") ?? "Slider");
  }
  dispatchReadyEvent() {
    document.dispatchEvent(new CustomEvent(SLIDER_READY_EVENT, {
      bubbles: true,
      detail: { sliderElement: this.sliderElement, slider: this }
    }));
  }
};
__name(_Slider, "Slider");
let Slider = _Slider;
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".c-slider").forEach((slider) => {
      new Slider(slider);
    });
  });
}
__name(init, "init");
init();
//# sourceMappingURL=slider.js.map
