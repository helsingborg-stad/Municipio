var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { V as VideoControls } from "../../video.js";
const _Hero = class _Hero {
  heroVideos;
  isReduced;
  constructor() {
    this.heroVideos = document.querySelectorAll(".c-hero--video");
    this.isReduced = window.matchMedia("(prefers-reduced-motion: reduce)");
    this.heroVideos.length && this.handleVideoPause();
  }
  handleVideoPause() {
    this.heroVideos.forEach((heroVideo) => {
      const video = new VideoControls(heroVideo);
      if (this.isReduced && this.isReduced.matches) {
        video.pauseVideo();
      }
    });
  }
};
__name(_Hero, "Hero");
let Hero = _Hero;
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    new Hero();
  });
}
__name(init, "init");
init();
//# sourceMappingURL=hero.js.map
