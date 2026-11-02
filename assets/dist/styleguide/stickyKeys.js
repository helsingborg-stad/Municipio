var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _StickyKeys = class _StickyKeys {
  constructor() {
    this.keyPressed = false;
    this.timeStamp = false;
    const inputTypes = [
      'input[type="checkbox"]',
      'input[type="email"]',
      'input[type="text"]',
      'input[type="date"]',
      'input[type="search"]',
      'input[type="datetime-local"]',
      'input[type="month"]',
      'input[type="number"]'
    ];
    this.subscribeInput(
      [
        ...document.querySelectorAll(
          inputTypes.join(", ")
        )
      ]
    );
  }
  subscribeInput(targetElements) {
    const arr = [];
    targetElements.forEach((input) => {
      input.addEventListener("keydown", (event) => {
        if (event.code !== "Backspace" && !event.shiftKey && !event.ctrlKey && !event.altKey && !event.metaKey) {
          if (event.repeat) {
            this.handleInput(event, 2e3);
          }
          if (!event.repeat && arr.pop() === event.key) {
            this.handleInput(event, 500);
          }
        }
        arr.push(event.key);
      });
    });
  }
  handleInput(event, delay) {
    if (!this.timeStamp) {
      this.timeStamp = event.timeStamp - 600;
    }
    if (event.timeStamp >= this.timeStamp + delay) {
      this.timeStamp = event.timeStamp;
    } else {
      event.preventDefault();
    }
  }
};
__name(_StickyKeys, "StickyKeys");
let StickyKeys = _StickyKeys;
export {
  StickyKeys as default
};
//# sourceMappingURL=stickyKeys.js.map
