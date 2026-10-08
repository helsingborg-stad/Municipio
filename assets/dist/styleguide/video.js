var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _VideoControls = class _VideoControls {
  constructor(videoElement) {
    this.PLAYER = videoElement;
    this.videoInteractions();
  }
  videoInteractions() {
    const btn = this.PLAYER.querySelector("[js-video-control]");
    if (btn) {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (this.getVideoState() === "playing" || !this.getVideoState()) {
          this.pauseVideo();
        } else {
          this.playVideo();
        }
      });
    }
  }
  getVideoState() {
    return this.PLAYER.getAttribute("js-video-control");
  }
  pauseVideo() {
    this.PLAYER.setAttribute("js-video-control", "paused");
    this.PLAYER.querySelector("video").pause();
  }
  playVideo() {
    this.PLAYER.setAttribute("js-video-control", "playing");
    this.PLAYER.querySelector("video").play();
  }
};
__name(_VideoControls, "VideoControls");
let VideoControls = _VideoControls;
export {
  VideoControls as V
};
//# sourceMappingURL=video.js.map
