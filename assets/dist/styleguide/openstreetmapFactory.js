var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
const sheet = '.openstreetmap{overflow:hidden;z-index:2}.openstreetmap .openstreetmap__search{background-color:#fff;border:2px solid rgba(0,0,0,.2);border-radius:4px;margin-left:auto;margin-right:auto;margin-top:10px;max-width:calc(100% - 7.5rem);padding:0 1rem;position:relative;width:25rem;z-index:9}@media (max-width:768px){.openstreetmap .openstreetmap__search{padding:0 .5rem}}.openstreetmap .openstreetmap__search .openstreetmap__search-container{position:relative}.openstreetmap .openstreetmap__search .openstreetmap__search-reset{color:#7c7c7c;cursor:pointer;display:none;padding:.5rem;position:absolute;right:0;top:50%;transform:translate(.5rem,-50%);transition:font-size .2s ease-in-out}.openstreetmap .openstreetmap__search .openstreetmap__search-reset:hover{font-size:110%}.openstreetmap .openstreetmap__search .openstreetmap__search-spinner{animation:rotation 1s linear infinite;border:2px solid #7c7c7c;border-bottom-color:transparent;border-radius:50%;box-sizing:border-box;display:none;height:12px;position:absolute;right:1rem;top:calc(50% - 6px);width:12px}@keyframes rotation{0%{transform:rotate(0deg)}to{transform:rotate(1turn)}}.openstreetmap .openstreetmap__search input{border:none;height:2rem;padding-right:2rem;width:100%}.openstreetmap .openstreetmap__search input:focus-visible{box-shadow:none;outline:unset;outline-offset:unset}.openstreetmap .openstreetmap__search ul{border-top:1px solid #c6c6c6;list-style-type:none;margin:0;max-height:16rem;overflow-y:auto;padding:.5rem 0;width:100%}.openstreetmap .openstreetmap__search ul li{cursor:pointer;padding-left:2px}.openstreetmap .openstreetmap__search ul:empty{background-color:transparent;border:none;padding:0}.openstreetmap .leaflet-control-container{pointer-events:none}.openstreetmap .leaflet-control-container>*{pointer-events:auto}.openstreetmap .leaflet-popup-content{width:100%}.openstreetmap .leaflet-control-attribution{background-color:hsla(0,0%,100%,.4)!important}.openstreetmap .leaflet-control-attribution,.openstreetmap .leaflet-control-attribution>*{color:0!important}.openstreetmap .marker-cluster-small{background-color:hsla(91,60%,72%,.6)}.openstreetmap .marker-cluster-small div{background-color:rgba(110,204,57,.6)}.openstreetmap .marker-cluster-medium{background-color:rgba(241,211,87,.6)}.openstreetmap .marker-cluster-medium div{background-color:rgba(240,194,12,.6)}.openstreetmap .marker-cluster-large{background-color:hsla(18,97%,72%,.6)}.openstreetmap .marker-cluster-large div{background-color:rgba(241,128,23,.6)}.openstreetmap .leaflet-oldie .marker-cluster-small{background-color:#b5e28c}.openstreetmap .leaflet-oldie .marker-cluster-small div{background-color:#6ecc39}.openstreetmap .leaflet-oldie .marker-cluster-medium{background-color:#f1d357}.openstreetmap .leaflet-oldie .marker-cluster-medium div{background-color:#f0c20c}.openstreetmap .leaflet-oldie .marker-cluster-large{background-color:#fd9c73}.openstreetmap .leaflet-oldie .marker-cluster-large div{background-color:#f18017}.openstreetmap .marker-cluster{background-clip:padding-box;border-radius:20px}.openstreetmap .marker-cluster div{border-radius:15px;font:12px Helvetica Neue,Arial,Helvetica,sans-serif;height:30px;margin-left:5px;margin-top:5px;text-align:center;width:30px}.openstreetmap .marker-cluster span{line-height:30px}.openstreetmap .leaflet-image-layer,.openstreetmap .leaflet-layer,.openstreetmap .leaflet-marker-icon,.openstreetmap .leaflet-marker-shadow,.openstreetmap .leaflet-pane,.openstreetmap .leaflet-pane>canvas,.openstreetmap .leaflet-pane>svg,.openstreetmap .leaflet-tile,.openstreetmap .leaflet-tile-container,.openstreetmap .leaflet-zoom-box{left:0;position:absolute;top:0}.openstreetmap .leaflet-container{overflow:hidden}.openstreetmap .leaflet-marker-icon,.openstreetmap .leaflet-marker-shadow,.openstreetmap .leaflet-tile{-webkit-user-drag:none;-webkit-user-select:none;-moz-user-select:none;user-select:none}.openstreetmap .leaflet-tile::selection{background:transparent}.openstreetmap .leaflet-safari .leaflet-tile{image-rendering:-webkit-optimize-contrast}.openstreetmap .leaflet-safari .leaflet-tile-container{height:1600px;-webkit-transform-origin:0 0;width:1600px}.openstreetmap .leaflet-marker-icon,.openstreetmap .leaflet-marker-shadow{display:block}.openstreetmap .leaflet-container .leaflet-overlay-pane svg{max-height:none!important;max-width:none!important}.openstreetmap .leaflet-container .leaflet-marker-pane img,.openstreetmap .leaflet-container .leaflet-shadow-pane img,.openstreetmap .leaflet-container .leaflet-tile,.openstreetmap .leaflet-container .leaflet-tile-pane img,.openstreetmap .leaflet-container img.leaflet-image-layer{max-height:none!important;max-width:none!important;padding:0;width:auto}.openstreetmap .leaflet-container img.leaflet-tile{mix-blend-mode:plus-lighter}.openstreetmap .leaflet-container.leaflet-touch-zoom{-ms-touch-action:pan-x pan-y;touch-action:pan-x pan-y}.openstreetmap .leaflet-container.leaflet-touch-drag{-ms-touch-action:pinch-zoom;touch-action:none;touch-action:pinch-zoom}.openstreetmap .leaflet-container.leaflet-touch-drag.leaflet-touch-zoom{-ms-touch-action:none;touch-action:none}.openstreetmap .leaflet-container{-webkit-tap-highlight-color:transparent}.openstreetmap .leaflet-container a{-webkit-tap-highlight-color:rgba(51,181,229,.4)}.openstreetmap .leaflet-tile{filter:inherit;visibility:hidden}.openstreetmap .leaflet-tile-loaded{visibility:inherit}.openstreetmap .leaflet-zoom-box{-moz-box-sizing:border-box;box-sizing:border-box;height:0;width:0;z-index:8}.openstreetmap .leaflet-overlay-pane svg{-moz-user-select:none}.openstreetmap .leaflet-pane{z-index:4}.openstreetmap .leaflet-tile-pane{z-index:2}.openstreetmap .leaflet-overlay-pane{z-index:4}.openstreetmap .leaflet-shadow-pane{z-index:5}.openstreetmap .leaflet-marker-pane,.openstreetmap .leaflet-tooltip-pane{z-index:6}.openstreetmap .leaflet-popup-pane{z-index:7}.openstreetmap .leaflet-map-pane canvas{z-index:1}.openstreetmap .leaflet-map-pane svg{z-index:2}.openstreetmap .leaflet-vml-shape{height:1px;width:1px}.openstreetmap .lvml{behavior:url(#default#VML);display:inline-block;position:absolute}.openstreetmap .leaflet-control{pointer-events:visiblePainted;pointer-events:auto;position:relative;z-index:8}.openstreetmap .leaflet-bottom,.openstreetmap .leaflet-top{pointer-events:none;position:absolute;z-index:9}.openstreetmap .leaflet-top{top:0}.openstreetmap .leaflet-right{right:0}.openstreetmap .leaflet-bottom{bottom:0}.openstreetmap .leaflet-left{left:0}.openstreetmap .leaflet-control{clear:both;float:left}.openstreetmap .leaflet-right .leaflet-control{float:right}.openstreetmap .leaflet-top .leaflet-control{margin-top:10px}.openstreetmap .leaflet-bottom .leaflet-control{margin-bottom:10px}.openstreetmap .leaflet-left .leaflet-control{margin-left:10px}.openstreetmap .leaflet-right .leaflet-control{margin-right:10px}.openstreetmap .leaflet-fade-anim .leaflet-popup{opacity:0;-webkit-transition:opacity .2s linear;-moz-transition:opacity .2s linear;transition:opacity .2s linear}.openstreetmap .leaflet-fade-anim .leaflet-map-pane .leaflet-popup{opacity:1}.openstreetmap .leaflet-zoom-animated{-webkit-transform-origin:0 0;-ms-transform-origin:0 0;transform-origin:0 0}.openstreetmap svg.leaflet-zoom-animated{will-change:transform}.openstreetmap .leaflet-zoom-anim .leaflet-zoom-animated{-webkit-transition:-webkit-transform .25s cubic-bezier(0,0,.25,1);-moz-transition:-moz-transform .25s cubic-bezier(0,0,.25,1);transition:transform .25s cubic-bezier(0,0,.25,1)}.openstreetmap .leaflet-pan-anim .leaflet-tile,.openstreetmap .leaflet-zoom-anim .leaflet-tile{-webkit-transition:none;-moz-transition:none;transition:none}.openstreetmap .leaflet-zoom-anim .leaflet-zoom-hide{visibility:hidden}.openstreetmap .leaflet-interactive{cursor:pointer}.openstreetmap .leaflet-grab{cursor:-webkit-grab;cursor:-moz-grab;cursor:grab}.openstreetmap .leaflet-crosshair,.openstreetmap .leaflet-crosshair .leaflet-interactive{cursor:crosshair}.openstreetmap .leaflet-control,.openstreetmap .leaflet-popup-pane{cursor:auto}.openstreetmap .leaflet-dragging .leaflet-grab,.openstreetmap .leaflet-dragging .leaflet-grab .leaflet-interactive,.openstreetmap .leaflet-dragging .leaflet-marker-draggable{cursor:move;cursor:-webkit-grabbing;cursor:-moz-grabbing;cursor:grabbing}.openstreetmap .leaflet-image-layer,.openstreetmap .leaflet-marker-icon,.openstreetmap .leaflet-marker-shadow,.openstreetmap .leaflet-pane>svg path,.openstreetmap .leaflet-tile-container{pointer-events:none}.openstreetmap .leaflet-image-layer.leaflet-interactive,.openstreetmap .leaflet-marker-icon.leaflet-interactive,.openstreetmap .leaflet-pane>svg path.leaflet-interactive,.openstreetmap svg.leaflet-image-layer.leaflet-interactive path{pointer-events:visiblePainted;pointer-events:auto}.openstreetmap .leaflet-container{background:#ddd;outline-offset:1px}.openstreetmap .leaflet-container a{color:#0078a8}.openstreetmap .leaflet-zoom-box{background:hsla(0,0%,100%,.5);border:2px dotted #38f}.openstreetmap .leaflet-container{font-family:Helvetica Neue,Arial,Helvetica,sans-serif;font-size:12px;font-size:.75rem;line-height:1.5}.openstreetmap .leaflet-bar{border-radius:4px;box-shadow:0 1px 5px rgba(0,0,0,.65)}.openstreetmap .leaflet-bar a{background-color:#fff;border-bottom:1px solid #ccc;color:#000;display:block;height:26px;line-height:26px;text-align:center;text-decoration:none;width:26px}.openstreetmap .leaflet-bar a,.openstreetmap .leaflet-control-layers-toggle{background-position:50% 50%;background-repeat:no-repeat;display:block}.openstreetmap .leaflet-bar a:focus,.openstreetmap .leaflet-bar a:hover{background-color:#f4f4f4}.openstreetmap .leaflet-bar a:first-child{border-top-left-radius:4px;border-top-right-radius:4px}.openstreetmap .leaflet-bar a:last-child{border-bottom:none;border-bottom-left-radius:4px;border-bottom-right-radius:4px}.openstreetmap .leaflet-bar a.leaflet-disabled{background-color:#f4f4f4;color:#bbb;cursor:default}.openstreetmap .leaflet-touch .leaflet-bar a{height:30px;line-height:30px;width:30px}.openstreetmap .leaflet-touch .leaflet-bar a:first-child{border-top-left-radius:2px;border-top-right-radius:2px}.openstreetmap .leaflet-touch .leaflet-bar a:last-child{border-bottom-left-radius:2px;border-bottom-right-radius:2px}.openstreetmap .leaflet-control-zoom-in,.openstreetmap .leaflet-control-zoom-out{font:700 18px Lucida Console,Monaco,monospace;text-indent:1px}.openstreetmap .leaflet-touch .leaflet-control-zoom-in,.openstreetmap .leaflet-touch .leaflet-control-zoom-out{font-size:22px}.openstreetmap .leaflet-control-layers{background:#fff;border-radius:5px;box-shadow:0 1px 5px rgba(0,0,0,.4)}.openstreetmap .leaflet-touch .leaflet-control-layers-toggle{height:44px;width:44px}.openstreetmap .leaflet-control-layers .leaflet-control-layers-list,.openstreetmap .leaflet-control-layers-expanded .leaflet-control-layers-toggle{display:none}.openstreetmap .leaflet-control-layers-expanded .leaflet-control-layers-list{display:block;position:relative}.openstreetmap .leaflet-control-layers-expanded{background:#fff;color:#333;padding:6px 10px 6px 6px}.openstreetmap .leaflet-control-layers-scrollbar{overflow-x:hidden;overflow-y:scroll;padding-right:5px}.openstreetmap .leaflet-control-layers-selector{margin-top:2px;position:relative;top:1px}.openstreetmap .leaflet-control-layers label{display:block;font-size:13px;font-size:1.08333em}.openstreetmap .leaflet-control-layers-separator{border-top:1px solid #ddd;height:0;margin:5px -10px 5px -6px}.openstreetmap .leaflet-container .leaflet-control-attribution{background:#fff;background:hsla(0,0%,100%,.8);margin:0}.openstreetmap .leaflet-control-attribution,.openstreetmap .leaflet-control-scale-line{color:#333;line-height:1.4;padding:0 5px}.openstreetmap .leaflet-control-attribution a{text-decoration:none}.openstreetmap .leaflet-control-attribution a:focus,.openstreetmap .leaflet-control-attribution a:hover{text-decoration:underline}.openstreetmap .leaflet-attribution-flag{display:inline!important;height:.6669em;vertical-align:baseline!important;width:1em}.openstreetmap .leaflet-left .leaflet-control-scale{margin-left:5px}.openstreetmap .leaflet-bottom .leaflet-control-scale{margin-bottom:5px}.openstreetmap .leaflet-control-scale-line{background:hsla(0,0%,100%,.8);border:2px solid #777;border-top:none;-moz-box-sizing:border-box;box-sizing:border-box;line-height:1.1;padding:2px 5px 1px;text-shadow:1px 1px #fff;white-space:nowrap}.openstreetmap .leaflet-control-scale-line:not(:first-child){border-bottom:none;border-top:2px solid #777;margin-top:-2px}.openstreetmap .leaflet-control-scale-line:not(:first-child):not(:last-child){border-bottom:2px solid #777}.openstreetmap .leaflet-touch .leaflet-bar,.openstreetmap .leaflet-touch .leaflet-control-attribution,.openstreetmap .leaflet-touch .leaflet-control-layers{box-shadow:none}.openstreetmap .leaflet-touch .leaflet-bar,.openstreetmap .leaflet-touch .leaflet-control-layers{background-clip:padding-box;border:2px solid rgba(0,0,0,.2)}.openstreetmap .leaflet-popup{margin-bottom:20px;position:absolute;text-align:center}.openstreetmap .leaflet-popup-content-wrapper{border-radius:12px;padding:1px;text-align:left}.openstreetmap .leaflet-popup-content{font-size:13px;font-size:1.08333em;line-height:1.3;margin:13px 24px 13px 20px;min-height:1px}.openstreetmap .leaflet-popup-content p{margin:1.3em 0}.openstreetmap .leaflet-popup-tip-container{height:20px;left:50%;margin-left:-20px;margin-top:-1px;overflow:hidden;pointer-events:none;position:absolute;width:40px}.openstreetmap .leaflet-popup-tip{height:17px;margin:-10px auto 0;padding:1px;pointer-events:auto;-webkit-transform:rotate(45deg);-moz-transform:rotate(45deg);-ms-transform:rotate(45deg);transform:rotate(45deg);width:17px}.openstreetmap .leaflet-popup-content-wrapper,.openstreetmap .leaflet-popup-tip{background:#fff;box-shadow:0 3px 14px rgba(0,0,0,.4);color:#333}.openstreetmap .leaflet-container a.leaflet-popup-close-button{background:transparent;border:none;color:#757575;font:16px/24px Tahoma,Verdana,sans-serif;height:24px;position:absolute;right:0;text-align:center;text-decoration:none;top:0;width:24px}.openstreetmap .leaflet-container a.leaflet-popup-close-button:focus,.openstreetmap .leaflet-container a.leaflet-popup-close-button:hover{color:#585858}.openstreetmap .leaflet-popup-scrolled{overflow:auto}.openstreetmap .leaflet-oldie .leaflet-popup-content-wrapper{-ms-zoom:1}.openstreetmap .leaflet-oldie .leaflet-popup-tip{-ms-filter:"progid:DXImageTransform.Microsoft.Matrix(M11=0.70710678, M12=0.70710678, M21=-0.70710678, M22=0.70710678)";filter:progid:DXImageTransform.Microsoft.Matrix(M11=0.70710678,M12=0.70710678,M21=-0.70710678,M22=0.70710678);margin:0 auto;width:24px}.openstreetmap .leaflet-oldie .leaflet-control-layers,.openstreetmap .leaflet-oldie .leaflet-control-zoom,.openstreetmap .leaflet-oldie .leaflet-popup-content-wrapper,.openstreetmap .leaflet-oldie .leaflet-popup-tip{border:1px solid #999}.openstreetmap .leaflet-tooltip{background-color:#fff;border:1px solid #fff;border-radius:3px;box-shadow:0 1px 3px rgba(0,0,0,.4);color:#222;padding:6px;pointer-events:none;position:absolute;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;white-space:nowrap}.openstreetmap .leaflet-tooltip.leaflet-interactive{cursor:pointer;pointer-events:auto}.openstreetmap .leaflet-tooltip-bottom:before,.openstreetmap .leaflet-tooltip-left:before,.openstreetmap .leaflet-tooltip-right:before,.openstreetmap .leaflet-tooltip-top:before{background:transparent;border:6px solid transparent;content:"";pointer-events:none;position:absolute}.openstreetmap .leaflet-tooltip-bottom{margin-top:6px}.openstreetmap .leaflet-tooltip-top{margin-top:-6px}.openstreetmap .leaflet-tooltip-bottom:before,.openstreetmap .leaflet-tooltip-top:before{left:50%;margin-left:-6px}.openstreetmap .leaflet-tooltip-top:before{border-top-color:#fff;bottom:0;margin-bottom:-12px}.openstreetmap .leaflet-tooltip-bottom:before{border-bottom-color:#fff;margin-left:-6px;margin-top:-12px;top:0}.openstreetmap .leaflet-tooltip-left{margin-left:-6px}.openstreetmap .leaflet-tooltip-right{margin-left:6px}.openstreetmap .leaflet-tooltip-left:before,.openstreetmap .leaflet-tooltip-right:before{margin-top:-6px;top:50%}.openstreetmap .leaflet-tooltip-left:before{border-left-color:#fff;margin-right:-12px;right:0}.openstreetmap .leaflet-tooltip-right:before{border-right-color:#fff;left:0;margin-left:-12px}@media print{.openstreetmap .leaflet-control{-webkit-print-color-adjust:exact;print-color-adjust:exact}}\n/*# sourceMappingURL=main.css?inline.map */';
var dist = {};
var types = {};
var hasRequiredTypes;
function requireTypes() {
  if (hasRequiredTypes) return types;
  hasRequiredTypes = 1;
  Object.defineProperty(types, "__esModule", { value: true });
  return types;
}
__name(requireTypes, "requireTypes");
var eventListenerInterface = {};
var hasRequiredEventListenerInterface;
function requireEventListenerInterface() {
  if (hasRequiredEventListenerInterface) return eventListenerInterface;
  hasRequiredEventListenerInterface = 1;
  Object.defineProperty(eventListenerInterface, "__esModule", { value: true });
  return eventListenerInterface;
}
__name(requireEventListenerInterface, "requireEventListenerInterface");
var addableInterface = {};
var hasRequiredAddableInterface;
function requireAddableInterface() {
  if (hasRequiredAddableInterface) return addableInterface;
  hasRequiredAddableInterface = 1;
  Object.defineProperty(addableInterface, "__esModule", { value: true });
  return addableInterface;
}
__name(requireAddableInterface, "requireAddableInterface");
var addToInterface = {};
var hasRequiredAddToInterface;
function requireAddToInterface() {
  if (hasRequiredAddToInterface) return addToInterface;
  hasRequiredAddToInterface = 1;
  Object.defineProperty(addToInterface, "__esModule", { value: true });
  return addToInterface;
}
__name(requireAddToInterface, "requireAddToInterface");
var TilesHelper = {};
var hasRequiredTilesHelper;
function requireTilesHelper() {
  var _a;
  if (hasRequiredTilesHelper) return TilesHelper;
  hasRequiredTilesHelper = 1;
  Object.defineProperty(TilesHelper, "__esModule", { value: true });
  TilesHelper.TilesHelper = void 0;
  let TilesHelper$1 = (_a = class {
    getDefaultTiles(style) {
      switch (style) {
        case "dark":
          return {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            url: "https://tile.openstreetmap.org/{z}/{x}/{y}.png"
          };
        case "pale":
          return {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Tiles style by <a href="https://www.hotosm.org/" target="_blank">Humanitarian OpenStreetMap Team</a> hosted by <a href="https://openstreetmap.fr/" target="_blank">OpenStreetMap France</a>',
            url: "https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png"
          };
        case "color":
          return {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Tiles courtesy of <a href="https://www.openstreetmap.cat" target="_blank">Breton OpenStreetMap Team</a>',
            url: "https://tile.openstreetmap.bzh/ca/{z}/{x}/{y}.png"
          };
        case "default":
        default:
          return {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            url: "https://tile.openstreetmap.org/{z}/{x}/{y}.png"
          };
      }
    }
  }, __name(_a, "TilesHelper"), _a);
  TilesHelper.TilesHelper = TilesHelper$1;
  return TilesHelper;
}
__name(requireTilesHelper, "requireTilesHelper");
var TilesHelperInterface = {};
var hasRequiredTilesHelperInterface;
function requireTilesHelperInterface() {
  if (hasRequiredTilesHelperInterface) return TilesHelperInterface;
  hasRequiredTilesHelperInterface = 1;
  Object.defineProperty(TilesHelperInterface, "__esModule", { value: true });
  return TilesHelperInterface;
}
__name(requireTilesHelperInterface, "requireTilesHelperInterface");
var createMap = {};
var leafletSrc$1 = { exports: {} };
var leafletSrc = leafletSrc$1.exports;
var hasRequiredLeafletSrc;
function requireLeafletSrc() {
  if (hasRequiredLeafletSrc) return leafletSrc$1.exports;
  hasRequiredLeafletSrc = 1;
  (function(module, exports) {
    (function(global, factory) {
      factory(exports);
    })(leafletSrc, (function(exports2) {
      var version = "1.9.4";
      function extend(dest) {
        var i, j, len, src;
        for (j = 1, len = arguments.length; j < len; j++) {
          src = arguments[j];
          for (i in src) {
            dest[i] = src[i];
          }
        }
        return dest;
      }
      __name(extend, "extend");
      var create$2 = Object.create || /* @__PURE__ */ (function() {
        function F() {
        }
        __name(F, "F");
        return function(proto) {
          F.prototype = proto;
          return new F();
        };
      })();
      function bind(fn, obj) {
        var slice = Array.prototype.slice;
        if (fn.bind) {
          return fn.bind.apply(fn, slice.call(arguments, 1));
        }
        var args = slice.call(arguments, 2);
        return function() {
          return fn.apply(obj, args.length ? args.concat(slice.call(arguments)) : arguments);
        };
      }
      __name(bind, "bind");
      var lastId = 0;
      function stamp(obj) {
        if (!("_leaflet_id" in obj)) {
          obj["_leaflet_id"] = ++lastId;
        }
        return obj._leaflet_id;
      }
      __name(stamp, "stamp");
      function throttle(fn, time, context) {
        var lock, args, wrapperFn, later;
        later = /* @__PURE__ */ __name(function() {
          lock = false;
          if (args) {
            wrapperFn.apply(context, args);
            args = false;
          }
        }, "later");
        wrapperFn = /* @__PURE__ */ __name(function() {
          if (lock) {
            args = arguments;
          } else {
            fn.apply(context, arguments);
            setTimeout(later, time);
            lock = true;
          }
        }, "wrapperFn");
        return wrapperFn;
      }
      __name(throttle, "throttle");
      function wrapNum(x, range, includeMax) {
        var max = range[1], min = range[0], d = max - min;
        return x === max && includeMax ? x : ((x - min) % d + d) % d + min;
      }
      __name(wrapNum, "wrapNum");
      function falseFn() {
        return false;
      }
      __name(falseFn, "falseFn");
      function formatNum(num, precision) {
        if (precision === false) {
          return num;
        }
        var pow = Math.pow(10, precision === void 0 ? 6 : precision);
        return Math.round(num * pow) / pow;
      }
      __name(formatNum, "formatNum");
      function trim(str) {
        return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g, "");
      }
      __name(trim, "trim");
      function splitWords(str) {
        return trim(str).split(/\s+/);
      }
      __name(splitWords, "splitWords");
      function setOptions(obj, options) {
        if (!Object.prototype.hasOwnProperty.call(obj, "options")) {
          obj.options = obj.options ? create$2(obj.options) : {};
        }
        for (var i in options) {
          obj.options[i] = options[i];
        }
        return obj.options;
      }
      __name(setOptions, "setOptions");
      function getParamString(obj, existingUrl, uppercase) {
        var params = [];
        for (var i in obj) {
          params.push(encodeURIComponent(uppercase ? i.toUpperCase() : i) + "=" + encodeURIComponent(obj[i]));
        }
        return (!existingUrl || existingUrl.indexOf("?") === -1 ? "?" : "&") + params.join("&");
      }
      __name(getParamString, "getParamString");
      var templateRe = /\{ *([\w_ -]+) *\}/g;
      function template(str, data) {
        return str.replace(templateRe, function(str2, key) {
          var value = data[key];
          if (value === void 0) {
            throw new Error("No value provided for variable " + str2);
          } else if (typeof value === "function") {
            value = value(data);
          }
          return value;
        });
      }
      __name(template, "template");
      var isArray = Array.isArray || function(obj) {
        return Object.prototype.toString.call(obj) === "[object Array]";
      };
      function indexOf(array, el) {
        for (var i = 0; i < array.length; i++) {
          if (array[i] === el) {
            return i;
          }
        }
        return -1;
      }
      __name(indexOf, "indexOf");
      var emptyImageUrl = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
      function getPrefixed(name) {
        return window["webkit" + name] || window["moz" + name] || window["ms" + name];
      }
      __name(getPrefixed, "getPrefixed");
      var lastTime = 0;
      function timeoutDefer(fn) {
        var time = +/* @__PURE__ */ new Date(), timeToCall = Math.max(0, 16 - (time - lastTime));
        lastTime = time + timeToCall;
        return window.setTimeout(fn, timeToCall);
      }
      __name(timeoutDefer, "timeoutDefer");
      var requestFn = window.requestAnimationFrame || getPrefixed("RequestAnimationFrame") || timeoutDefer;
      var cancelFn = window.cancelAnimationFrame || getPrefixed("CancelAnimationFrame") || getPrefixed("CancelRequestAnimationFrame") || function(id) {
        window.clearTimeout(id);
      };
      function requestAnimFrame(fn, context, immediate) {
        if (immediate && requestFn === timeoutDefer) {
          fn.call(context);
        } else {
          return requestFn.call(window, bind(fn, context));
        }
      }
      __name(requestAnimFrame, "requestAnimFrame");
      function cancelAnimFrame(id) {
        if (id) {
          cancelFn.call(window, id);
        }
      }
      __name(cancelAnimFrame, "cancelAnimFrame");
      var Util = {
        __proto__: null,
        extend,
        create: create$2,
        bind,
        get lastId() {
          return lastId;
        },
        stamp,
        throttle,
        wrapNum,
        falseFn,
        formatNum,
        trim,
        splitWords,
        setOptions,
        getParamString,
        template,
        isArray,
        indexOf,
        emptyImageUrl,
        requestFn,
        cancelFn,
        requestAnimFrame,
        cancelAnimFrame
      };
      function Class() {
      }
      __name(Class, "Class");
      Class.extend = function(props) {
        var NewClass = /* @__PURE__ */ __name(function() {
          setOptions(this);
          if (this.initialize) {
            this.initialize.apply(this, arguments);
          }
          this.callInitHooks();
        }, "NewClass");
        var parentProto = NewClass.__super__ = this.prototype;
        var proto = create$2(parentProto);
        proto.constructor = NewClass;
        NewClass.prototype = proto;
        for (var i in this) {
          if (Object.prototype.hasOwnProperty.call(this, i) && i !== "prototype" && i !== "__super__") {
            NewClass[i] = this[i];
          }
        }
        if (props.statics) {
          extend(NewClass, props.statics);
        }
        if (props.includes) {
          checkDeprecatedMixinEvents(props.includes);
          extend.apply(null, [proto].concat(props.includes));
        }
        extend(proto, props);
        delete proto.statics;
        delete proto.includes;
        if (proto.options) {
          proto.options = parentProto.options ? create$2(parentProto.options) : {};
          extend(proto.options, props.options);
        }
        proto._initHooks = [];
        proto.callInitHooks = function() {
          if (this._initHooksCalled) {
            return;
          }
          if (parentProto.callInitHooks) {
            parentProto.callInitHooks.call(this);
          }
          this._initHooksCalled = true;
          for (var i2 = 0, len = proto._initHooks.length; i2 < len; i2++) {
            proto._initHooks[i2].call(this);
          }
        };
        return NewClass;
      };
      Class.include = function(props) {
        var parentOptions = this.prototype.options;
        extend(this.prototype, props);
        if (props.options) {
          this.prototype.options = parentOptions;
          this.mergeOptions(props.options);
        }
        return this;
      };
      Class.mergeOptions = function(options) {
        extend(this.prototype.options, options);
        return this;
      };
      Class.addInitHook = function(fn) {
        var args = Array.prototype.slice.call(arguments, 1);
        var init = typeof fn === "function" ? fn : function() {
          this[fn].apply(this, args);
        };
        this.prototype._initHooks = this.prototype._initHooks || [];
        this.prototype._initHooks.push(init);
        return this;
      };
      function checkDeprecatedMixinEvents(includes) {
        if (typeof L === "undefined" || !L || !L.Mixin) {
          return;
        }
        includes = isArray(includes) ? includes : [includes];
        for (var i = 0; i < includes.length; i++) {
          if (includes[i] === L.Mixin.Events) {
            console.warn("Deprecated include of L.Mixin.Events: this property will be removed in future releases, please inherit from L.Evented instead.", new Error().stack);
          }
        }
      }
      __name(checkDeprecatedMixinEvents, "checkDeprecatedMixinEvents");
      var Events = {
        /* @method on(type: String, fn: Function, context?: Object): this
         * Adds a listener function (`fn`) to a particular event type of the object. You can optionally specify the context of the listener (object the this keyword will point to). You can also pass several space-separated types (e.g. `'click dblclick'`).
         *
         * @alternative
         * @method on(eventMap: Object): this
         * Adds a set of type/listener pairs, e.g. `{click: onClick, mousemove: onMouseMove}`
         */
        on: /* @__PURE__ */ __name(function(types2, fn, context) {
          if (typeof types2 === "object") {
            for (var type in types2) {
              this._on(type, types2[type], fn);
            }
          } else {
            types2 = splitWords(types2);
            for (var i = 0, len = types2.length; i < len; i++) {
              this._on(types2[i], fn, context);
            }
          }
          return this;
        }, "on"),
        /* @method off(type: String, fn?: Function, context?: Object): this
         * Removes a previously added listener function. If no function is specified, it will remove all the listeners of that particular event from the object. Note that if you passed a custom context to `on`, you must pass the same context to `off` in order to remove the listener.
         *
         * @alternative
         * @method off(eventMap: Object): this
         * Removes a set of type/listener pairs.
         *
         * @alternative
         * @method off: this
         * Removes all listeners to all events on the object. This includes implicitly attached events.
         */
        off: /* @__PURE__ */ __name(function(types2, fn, context) {
          if (!arguments.length) {
            delete this._events;
          } else if (typeof types2 === "object") {
            for (var type in types2) {
              this._off(type, types2[type], fn);
            }
          } else {
            types2 = splitWords(types2);
            var removeAll = arguments.length === 1;
            for (var i = 0, len = types2.length; i < len; i++) {
              if (removeAll) {
                this._off(types2[i]);
              } else {
                this._off(types2[i], fn, context);
              }
            }
          }
          return this;
        }, "off"),
        // attach listener (without syntactic sugar now)
        _on: /* @__PURE__ */ __name(function(type, fn, context, _once) {
          if (typeof fn !== "function") {
            console.warn("wrong listener type: " + typeof fn);
            return;
          }
          if (this._listens(type, fn, context) !== false) {
            return;
          }
          if (context === this) {
            context = void 0;
          }
          var newListener = { fn, ctx: context };
          if (_once) {
            newListener.once = true;
          }
          this._events = this._events || {};
          this._events[type] = this._events[type] || [];
          this._events[type].push(newListener);
        }, "_on"),
        _off: /* @__PURE__ */ __name(function(type, fn, context) {
          var listeners, i, len;
          if (!this._events) {
            return;
          }
          listeners = this._events[type];
          if (!listeners) {
            return;
          }
          if (arguments.length === 1) {
            if (this._firingCount) {
              for (i = 0, len = listeners.length; i < len; i++) {
                listeners[i].fn = falseFn;
              }
            }
            delete this._events[type];
            return;
          }
          if (typeof fn !== "function") {
            console.warn("wrong listener type: " + typeof fn);
            return;
          }
          var index2 = this._listens(type, fn, context);
          if (index2 !== false) {
            var listener = listeners[index2];
            if (this._firingCount) {
              listener.fn = falseFn;
              this._events[type] = listeners = listeners.slice();
            }
            listeners.splice(index2, 1);
          }
        }, "_off"),
        // @method fire(type: String, data?: Object, propagate?: Boolean): this
        // Fires an event of the specified type. You can optionally provide a data
        // object — the first argument of the listener function will contain its
        // properties. The event can optionally be propagated to event parents.
        fire: /* @__PURE__ */ __name(function(type, data, propagate) {
          if (!this.listens(type, propagate)) {
            return this;
          }
          var event = extend({}, data, {
            type,
            target: this,
            sourceTarget: data && data.sourceTarget || this
          });
          if (this._events) {
            var listeners = this._events[type];
            if (listeners) {
              this._firingCount = this._firingCount + 1 || 1;
              for (var i = 0, len = listeners.length; i < len; i++) {
                var l = listeners[i];
                var fn = l.fn;
                if (l.once) {
                  this.off(type, fn, l.ctx);
                }
                fn.call(l.ctx || this, event);
              }
              this._firingCount--;
            }
          }
          if (propagate) {
            this._propagateEvent(event);
          }
          return this;
        }, "fire"),
        // @method listens(type: String, propagate?: Boolean): Boolean
        // @method listens(type: String, fn: Function, context?: Object, propagate?: Boolean): Boolean
        // Returns `true` if a particular event type has any listeners attached to it.
        // The verification can optionally be propagated, it will return `true` if parents have the listener attached to it.
        listens: /* @__PURE__ */ __name(function(type, fn, context, propagate) {
          if (typeof type !== "string") {
            console.warn('"string" type argument expected');
          }
          var _fn = fn;
          if (typeof fn !== "function") {
            propagate = !!fn;
            _fn = void 0;
            context = void 0;
          }
          var listeners = this._events && this._events[type];
          if (listeners && listeners.length) {
            if (this._listens(type, _fn, context) !== false) {
              return true;
            }
          }
          if (propagate) {
            for (var id in this._eventParents) {
              if (this._eventParents[id].listens(type, fn, context, propagate)) {
                return true;
              }
            }
          }
          return false;
        }, "listens"),
        // returns the index (number) or false
        _listens: /* @__PURE__ */ __name(function(type, fn, context) {
          if (!this._events) {
            return false;
          }
          var listeners = this._events[type] || [];
          if (!fn) {
            return !!listeners.length;
          }
          if (context === this) {
            context = void 0;
          }
          for (var i = 0, len = listeners.length; i < len; i++) {
            if (listeners[i].fn === fn && listeners[i].ctx === context) {
              return i;
            }
          }
          return false;
        }, "_listens"),
        // @method once(…): this
        // Behaves as [`on(…)`](#evented-on), except the listener will only get fired once and then removed.
        once: /* @__PURE__ */ __name(function(types2, fn, context) {
          if (typeof types2 === "object") {
            for (var type in types2) {
              this._on(type, types2[type], fn, true);
            }
          } else {
            types2 = splitWords(types2);
            for (var i = 0, len = types2.length; i < len; i++) {
              this._on(types2[i], fn, context, true);
            }
          }
          return this;
        }, "once"),
        // @method addEventParent(obj: Evented): this
        // Adds an event parent - an `Evented` that will receive propagated events
        addEventParent: /* @__PURE__ */ __name(function(obj) {
          this._eventParents = this._eventParents || {};
          this._eventParents[stamp(obj)] = obj;
          return this;
        }, "addEventParent"),
        // @method removeEventParent(obj: Evented): this
        // Removes an event parent, so it will stop receiving propagated events
        removeEventParent: /* @__PURE__ */ __name(function(obj) {
          if (this._eventParents) {
            delete this._eventParents[stamp(obj)];
          }
          return this;
        }, "removeEventParent"),
        _propagateEvent: /* @__PURE__ */ __name(function(e) {
          for (var id in this._eventParents) {
            this._eventParents[id].fire(e.type, extend({
              layer: e.target,
              propagatedFrom: e.target
            }, e), true);
          }
        }, "_propagateEvent")
      };
      Events.addEventListener = Events.on;
      Events.removeEventListener = Events.clearAllEventListeners = Events.off;
      Events.addOneTimeEventListener = Events.once;
      Events.fireEvent = Events.fire;
      Events.hasEventListeners = Events.listens;
      var Evented = Class.extend(Events);
      function Point(x, y, round) {
        this.x = round ? Math.round(x) : x;
        this.y = round ? Math.round(y) : y;
      }
      __name(Point, "Point");
      var trunc = Math.trunc || function(v) {
        return v > 0 ? Math.floor(v) : Math.ceil(v);
      };
      Point.prototype = {
        // @method clone(): Point
        // Returns a copy of the current point.
        clone: /* @__PURE__ */ __name(function() {
          return new Point(this.x, this.y);
        }, "clone"),
        // @method add(otherPoint: Point): Point
        // Returns the result of addition of the current and the given points.
        add: /* @__PURE__ */ __name(function(point) {
          return this.clone()._add(toPoint(point));
        }, "add"),
        _add: /* @__PURE__ */ __name(function(point) {
          this.x += point.x;
          this.y += point.y;
          return this;
        }, "_add"),
        // @method subtract(otherPoint: Point): Point
        // Returns the result of subtraction of the given point from the current.
        subtract: /* @__PURE__ */ __name(function(point) {
          return this.clone()._subtract(toPoint(point));
        }, "subtract"),
        _subtract: /* @__PURE__ */ __name(function(point) {
          this.x -= point.x;
          this.y -= point.y;
          return this;
        }, "_subtract"),
        // @method divideBy(num: Number): Point
        // Returns the result of division of the current point by the given number.
        divideBy: /* @__PURE__ */ __name(function(num) {
          return this.clone()._divideBy(num);
        }, "divideBy"),
        _divideBy: /* @__PURE__ */ __name(function(num) {
          this.x /= num;
          this.y /= num;
          return this;
        }, "_divideBy"),
        // @method multiplyBy(num: Number): Point
        // Returns the result of multiplication of the current point by the given number.
        multiplyBy: /* @__PURE__ */ __name(function(num) {
          return this.clone()._multiplyBy(num);
        }, "multiplyBy"),
        _multiplyBy: /* @__PURE__ */ __name(function(num) {
          this.x *= num;
          this.y *= num;
          return this;
        }, "_multiplyBy"),
        // @method scaleBy(scale: Point): Point
        // Multiply each coordinate of the current point by each coordinate of
        // `scale`. In linear algebra terms, multiply the point by the
        // [scaling matrix](https://en.wikipedia.org/wiki/Scaling_%28geometry%29#Matrix_representation)
        // defined by `scale`.
        scaleBy: /* @__PURE__ */ __name(function(point) {
          return new Point(this.x * point.x, this.y * point.y);
        }, "scaleBy"),
        // @method unscaleBy(scale: Point): Point
        // Inverse of `scaleBy`. Divide each coordinate of the current point by
        // each coordinate of `scale`.
        unscaleBy: /* @__PURE__ */ __name(function(point) {
          return new Point(this.x / point.x, this.y / point.y);
        }, "unscaleBy"),
        // @method round(): Point
        // Returns a copy of the current point with rounded coordinates.
        round: /* @__PURE__ */ __name(function() {
          return this.clone()._round();
        }, "round"),
        _round: /* @__PURE__ */ __name(function() {
          this.x = Math.round(this.x);
          this.y = Math.round(this.y);
          return this;
        }, "_round"),
        // @method floor(): Point
        // Returns a copy of the current point with floored coordinates (rounded down).
        floor: /* @__PURE__ */ __name(function() {
          return this.clone()._floor();
        }, "floor"),
        _floor: /* @__PURE__ */ __name(function() {
          this.x = Math.floor(this.x);
          this.y = Math.floor(this.y);
          return this;
        }, "_floor"),
        // @method ceil(): Point
        // Returns a copy of the current point with ceiled coordinates (rounded up).
        ceil: /* @__PURE__ */ __name(function() {
          return this.clone()._ceil();
        }, "ceil"),
        _ceil: /* @__PURE__ */ __name(function() {
          this.x = Math.ceil(this.x);
          this.y = Math.ceil(this.y);
          return this;
        }, "_ceil"),
        // @method trunc(): Point
        // Returns a copy of the current point with truncated coordinates (rounded towards zero).
        trunc: /* @__PURE__ */ __name(function() {
          return this.clone()._trunc();
        }, "trunc"),
        _trunc: /* @__PURE__ */ __name(function() {
          this.x = trunc(this.x);
          this.y = trunc(this.y);
          return this;
        }, "_trunc"),
        // @method distanceTo(otherPoint: Point): Number
        // Returns the cartesian distance between the current and the given points.
        distanceTo: /* @__PURE__ */ __name(function(point) {
          point = toPoint(point);
          var x = point.x - this.x, y = point.y - this.y;
          return Math.sqrt(x * x + y * y);
        }, "distanceTo"),
        // @method equals(otherPoint: Point): Boolean
        // Returns `true` if the given point has the same coordinates.
        equals: /* @__PURE__ */ __name(function(point) {
          point = toPoint(point);
          return point.x === this.x && point.y === this.y;
        }, "equals"),
        // @method contains(otherPoint: Point): Boolean
        // Returns `true` if both coordinates of the given point are less than the corresponding current point coordinates (in absolute values).
        contains: /* @__PURE__ */ __name(function(point) {
          point = toPoint(point);
          return Math.abs(point.x) <= Math.abs(this.x) && Math.abs(point.y) <= Math.abs(this.y);
        }, "contains"),
        // @method toString(): String
        // Returns a string representation of the point for debugging purposes.
        toString: /* @__PURE__ */ __name(function() {
          return "Point(" + formatNum(this.x) + ", " + formatNum(this.y) + ")";
        }, "toString")
      };
      function toPoint(x, y, round) {
        if (x instanceof Point) {
          return x;
        }
        if (isArray(x)) {
          return new Point(x[0], x[1]);
        }
        if (x === void 0 || x === null) {
          return x;
        }
        if (typeof x === "object" && "x" in x && "y" in x) {
          return new Point(x.x, x.y);
        }
        return new Point(x, y, round);
      }
      __name(toPoint, "toPoint");
      function Bounds(a, b) {
        if (!a) {
          return;
        }
        var points = b ? [a, b] : a;
        for (var i = 0, len = points.length; i < len; i++) {
          this.extend(points[i]);
        }
      }
      __name(Bounds, "Bounds");
      Bounds.prototype = {
        // @method extend(point: Point): this
        // Extends the bounds to contain the given point.
        // @alternative
        // @method extend(otherBounds: Bounds): this
        // Extend the bounds to contain the given bounds
        extend: /* @__PURE__ */ __name(function(obj) {
          var min2, max2;
          if (!obj) {
            return this;
          }
          if (obj instanceof Point || typeof obj[0] === "number" || "x" in obj) {
            min2 = max2 = toPoint(obj);
          } else {
            obj = toBounds(obj);
            min2 = obj.min;
            max2 = obj.max;
            if (!min2 || !max2) {
              return this;
            }
          }
          if (!this.min && !this.max) {
            this.min = min2.clone();
            this.max = max2.clone();
          } else {
            this.min.x = Math.min(min2.x, this.min.x);
            this.max.x = Math.max(max2.x, this.max.x);
            this.min.y = Math.min(min2.y, this.min.y);
            this.max.y = Math.max(max2.y, this.max.y);
          }
          return this;
        }, "extend"),
        // @method getCenter(round?: Boolean): Point
        // Returns the center point of the bounds.
        getCenter: /* @__PURE__ */ __name(function(round) {
          return toPoint(
            (this.min.x + this.max.x) / 2,
            (this.min.y + this.max.y) / 2,
            round
          );
        }, "getCenter"),
        // @method getBottomLeft(): Point
        // Returns the bottom-left point of the bounds.
        getBottomLeft: /* @__PURE__ */ __name(function() {
          return toPoint(this.min.x, this.max.y);
        }, "getBottomLeft"),
        // @method getTopRight(): Point
        // Returns the top-right point of the bounds.
        getTopRight: /* @__PURE__ */ __name(function() {
          return toPoint(this.max.x, this.min.y);
        }, "getTopRight"),
        // @method getTopLeft(): Point
        // Returns the top-left point of the bounds (i.e. [`this.min`](#bounds-min)).
        getTopLeft: /* @__PURE__ */ __name(function() {
          return this.min;
        }, "getTopLeft"),
        // @method getBottomRight(): Point
        // Returns the bottom-right point of the bounds (i.e. [`this.max`](#bounds-max)).
        getBottomRight: /* @__PURE__ */ __name(function() {
          return this.max;
        }, "getBottomRight"),
        // @method getSize(): Point
        // Returns the size of the given bounds
        getSize: /* @__PURE__ */ __name(function() {
          return this.max.subtract(this.min);
        }, "getSize"),
        // @method contains(otherBounds: Bounds): Boolean
        // Returns `true` if the rectangle contains the given one.
        // @alternative
        // @method contains(point: Point): Boolean
        // Returns `true` if the rectangle contains the given point.
        contains: /* @__PURE__ */ __name(function(obj) {
          var min, max;
          if (typeof obj[0] === "number" || obj instanceof Point) {
            obj = toPoint(obj);
          } else {
            obj = toBounds(obj);
          }
          if (obj instanceof Bounds) {
            min = obj.min;
            max = obj.max;
          } else {
            min = max = obj;
          }
          return min.x >= this.min.x && max.x <= this.max.x && min.y >= this.min.y && max.y <= this.max.y;
        }, "contains"),
        // @method intersects(otherBounds: Bounds): Boolean
        // Returns `true` if the rectangle intersects the given bounds. Two bounds
        // intersect if they have at least one point in common.
        intersects: /* @__PURE__ */ __name(function(bounds) {
          bounds = toBounds(bounds);
          var min = this.min, max = this.max, min2 = bounds.min, max2 = bounds.max, xIntersects = max2.x >= min.x && min2.x <= max.x, yIntersects = max2.y >= min.y && min2.y <= max.y;
          return xIntersects && yIntersects;
        }, "intersects"),
        // @method overlaps(otherBounds: Bounds): Boolean
        // Returns `true` if the rectangle overlaps the given bounds. Two bounds
        // overlap if their intersection is an area.
        overlaps: /* @__PURE__ */ __name(function(bounds) {
          bounds = toBounds(bounds);
          var min = this.min, max = this.max, min2 = bounds.min, max2 = bounds.max, xOverlaps = max2.x > min.x && min2.x < max.x, yOverlaps = max2.y > min.y && min2.y < max.y;
          return xOverlaps && yOverlaps;
        }, "overlaps"),
        // @method isValid(): Boolean
        // Returns `true` if the bounds are properly initialized.
        isValid: /* @__PURE__ */ __name(function() {
          return !!(this.min && this.max);
        }, "isValid"),
        // @method pad(bufferRatio: Number): Bounds
        // Returns bounds created by extending or retracting the current bounds by a given ratio in each direction.
        // For example, a ratio of 0.5 extends the bounds by 50% in each direction.
        // Negative values will retract the bounds.
        pad: /* @__PURE__ */ __name(function(bufferRatio) {
          var min = this.min, max = this.max, heightBuffer = Math.abs(min.x - max.x) * bufferRatio, widthBuffer = Math.abs(min.y - max.y) * bufferRatio;
          return toBounds(
            toPoint(min.x - heightBuffer, min.y - widthBuffer),
            toPoint(max.x + heightBuffer, max.y + widthBuffer)
          );
        }, "pad"),
        // @method equals(otherBounds: Bounds): Boolean
        // Returns `true` if the rectangle is equivalent to the given bounds.
        equals: /* @__PURE__ */ __name(function(bounds) {
          if (!bounds) {
            return false;
          }
          bounds = toBounds(bounds);
          return this.min.equals(bounds.getTopLeft()) && this.max.equals(bounds.getBottomRight());
        }, "equals")
      };
      function toBounds(a, b) {
        if (!a || a instanceof Bounds) {
          return a;
        }
        return new Bounds(a, b);
      }
      __name(toBounds, "toBounds");
      function LatLngBounds(corner1, corner2) {
        if (!corner1) {
          return;
        }
        var latlngs = corner2 ? [corner1, corner2] : corner1;
        for (var i = 0, len = latlngs.length; i < len; i++) {
          this.extend(latlngs[i]);
        }
      }
      __name(LatLngBounds, "LatLngBounds");
      LatLngBounds.prototype = {
        // @method extend(latlng: LatLng): this
        // Extend the bounds to contain the given point
        // @alternative
        // @method extend(otherBounds: LatLngBounds): this
        // Extend the bounds to contain the given bounds
        extend: /* @__PURE__ */ __name(function(obj) {
          var sw = this._southWest, ne = this._northEast, sw2, ne2;
          if (obj instanceof LatLng) {
            sw2 = obj;
            ne2 = obj;
          } else if (obj instanceof LatLngBounds) {
            sw2 = obj._southWest;
            ne2 = obj._northEast;
            if (!sw2 || !ne2) {
              return this;
            }
          } else {
            return obj ? this.extend(toLatLng(obj) || toLatLngBounds(obj)) : this;
          }
          if (!sw && !ne) {
            this._southWest = new LatLng(sw2.lat, sw2.lng);
            this._northEast = new LatLng(ne2.lat, ne2.lng);
          } else {
            sw.lat = Math.min(sw2.lat, sw.lat);
            sw.lng = Math.min(sw2.lng, sw.lng);
            ne.lat = Math.max(ne2.lat, ne.lat);
            ne.lng = Math.max(ne2.lng, ne.lng);
          }
          return this;
        }, "extend"),
        // @method pad(bufferRatio: Number): LatLngBounds
        // Returns bounds created by extending or retracting the current bounds by a given ratio in each direction.
        // For example, a ratio of 0.5 extends the bounds by 50% in each direction.
        // Negative values will retract the bounds.
        pad: /* @__PURE__ */ __name(function(bufferRatio) {
          var sw = this._southWest, ne = this._northEast, heightBuffer = Math.abs(sw.lat - ne.lat) * bufferRatio, widthBuffer = Math.abs(sw.lng - ne.lng) * bufferRatio;
          return new LatLngBounds(
            new LatLng(sw.lat - heightBuffer, sw.lng - widthBuffer),
            new LatLng(ne.lat + heightBuffer, ne.lng + widthBuffer)
          );
        }, "pad"),
        // @method getCenter(): LatLng
        // Returns the center point of the bounds.
        getCenter: /* @__PURE__ */ __name(function() {
          return new LatLng(
            (this._southWest.lat + this._northEast.lat) / 2,
            (this._southWest.lng + this._northEast.lng) / 2
          );
        }, "getCenter"),
        // @method getSouthWest(): LatLng
        // Returns the south-west point of the bounds.
        getSouthWest: /* @__PURE__ */ __name(function() {
          return this._southWest;
        }, "getSouthWest"),
        // @method getNorthEast(): LatLng
        // Returns the north-east point of the bounds.
        getNorthEast: /* @__PURE__ */ __name(function() {
          return this._northEast;
        }, "getNorthEast"),
        // @method getNorthWest(): LatLng
        // Returns the north-west point of the bounds.
        getNorthWest: /* @__PURE__ */ __name(function() {
          return new LatLng(this.getNorth(), this.getWest());
        }, "getNorthWest"),
        // @method getSouthEast(): LatLng
        // Returns the south-east point of the bounds.
        getSouthEast: /* @__PURE__ */ __name(function() {
          return new LatLng(this.getSouth(), this.getEast());
        }, "getSouthEast"),
        // @method getWest(): Number
        // Returns the west longitude of the bounds
        getWest: /* @__PURE__ */ __name(function() {
          return this._southWest.lng;
        }, "getWest"),
        // @method getSouth(): Number
        // Returns the south latitude of the bounds
        getSouth: /* @__PURE__ */ __name(function() {
          return this._southWest.lat;
        }, "getSouth"),
        // @method getEast(): Number
        // Returns the east longitude of the bounds
        getEast: /* @__PURE__ */ __name(function() {
          return this._northEast.lng;
        }, "getEast"),
        // @method getNorth(): Number
        // Returns the north latitude of the bounds
        getNorth: /* @__PURE__ */ __name(function() {
          return this._northEast.lat;
        }, "getNorth"),
        // @method contains(otherBounds: LatLngBounds): Boolean
        // Returns `true` if the rectangle contains the given one.
        // @alternative
        // @method contains (latlng: LatLng): Boolean
        // Returns `true` if the rectangle contains the given point.
        contains: /* @__PURE__ */ __name(function(obj) {
          if (typeof obj[0] === "number" || obj instanceof LatLng || "lat" in obj) {
            obj = toLatLng(obj);
          } else {
            obj = toLatLngBounds(obj);
          }
          var sw = this._southWest, ne = this._northEast, sw2, ne2;
          if (obj instanceof LatLngBounds) {
            sw2 = obj.getSouthWest();
            ne2 = obj.getNorthEast();
          } else {
            sw2 = ne2 = obj;
          }
          return sw2.lat >= sw.lat && ne2.lat <= ne.lat && sw2.lng >= sw.lng && ne2.lng <= ne.lng;
        }, "contains"),
        // @method intersects(otherBounds: LatLngBounds): Boolean
        // Returns `true` if the rectangle intersects the given bounds. Two bounds intersect if they have at least one point in common.
        intersects: /* @__PURE__ */ __name(function(bounds) {
          bounds = toLatLngBounds(bounds);
          var sw = this._southWest, ne = this._northEast, sw2 = bounds.getSouthWest(), ne2 = bounds.getNorthEast(), latIntersects = ne2.lat >= sw.lat && sw2.lat <= ne.lat, lngIntersects = ne2.lng >= sw.lng && sw2.lng <= ne.lng;
          return latIntersects && lngIntersects;
        }, "intersects"),
        // @method overlaps(otherBounds: LatLngBounds): Boolean
        // Returns `true` if the rectangle overlaps the given bounds. Two bounds overlap if their intersection is an area.
        overlaps: /* @__PURE__ */ __name(function(bounds) {
          bounds = toLatLngBounds(bounds);
          var sw = this._southWest, ne = this._northEast, sw2 = bounds.getSouthWest(), ne2 = bounds.getNorthEast(), latOverlaps = ne2.lat > sw.lat && sw2.lat < ne.lat, lngOverlaps = ne2.lng > sw.lng && sw2.lng < ne.lng;
          return latOverlaps && lngOverlaps;
        }, "overlaps"),
        // @method toBBoxString(): String
        // Returns a string with bounding box coordinates in a 'southwest_lng,southwest_lat,northeast_lng,northeast_lat' format. Useful for sending requests to web services that return geo data.
        toBBoxString: /* @__PURE__ */ __name(function() {
          return [this.getWest(), this.getSouth(), this.getEast(), this.getNorth()].join(",");
        }, "toBBoxString"),
        // @method equals(otherBounds: LatLngBounds, maxMargin?: Number): Boolean
        // Returns `true` if the rectangle is equivalent (within a small margin of error) to the given bounds. The margin of error can be overridden by setting `maxMargin` to a small number.
        equals: /* @__PURE__ */ __name(function(bounds, maxMargin) {
          if (!bounds) {
            return false;
          }
          bounds = toLatLngBounds(bounds);
          return this._southWest.equals(bounds.getSouthWest(), maxMargin) && this._northEast.equals(bounds.getNorthEast(), maxMargin);
        }, "equals"),
        // @method isValid(): Boolean
        // Returns `true` if the bounds are properly initialized.
        isValid: /* @__PURE__ */ __name(function() {
          return !!(this._southWest && this._northEast);
        }, "isValid")
      };
      function toLatLngBounds(a, b) {
        if (a instanceof LatLngBounds) {
          return a;
        }
        return new LatLngBounds(a, b);
      }
      __name(toLatLngBounds, "toLatLngBounds");
      function LatLng(lat, lng, alt) {
        if (isNaN(lat) || isNaN(lng)) {
          throw new Error("Invalid LatLng object: (" + lat + ", " + lng + ")");
        }
        this.lat = +lat;
        this.lng = +lng;
        if (alt !== void 0) {
          this.alt = +alt;
        }
      }
      __name(LatLng, "LatLng");
      LatLng.prototype = {
        // @method equals(otherLatLng: LatLng, maxMargin?: Number): Boolean
        // Returns `true` if the given `LatLng` point is at the same position (within a small margin of error). The margin of error can be overridden by setting `maxMargin` to a small number.
        equals: /* @__PURE__ */ __name(function(obj, maxMargin) {
          if (!obj) {
            return false;
          }
          obj = toLatLng(obj);
          var margin = Math.max(
            Math.abs(this.lat - obj.lat),
            Math.abs(this.lng - obj.lng)
          );
          return margin <= (maxMargin === void 0 ? 1e-9 : maxMargin);
        }, "equals"),
        // @method toString(): String
        // Returns a string representation of the point (for debugging purposes).
        toString: /* @__PURE__ */ __name(function(precision) {
          return "LatLng(" + formatNum(this.lat, precision) + ", " + formatNum(this.lng, precision) + ")";
        }, "toString"),
        // @method distanceTo(otherLatLng: LatLng): Number
        // Returns the distance (in meters) to the given `LatLng` calculated using the [Spherical Law of Cosines](https://en.wikipedia.org/wiki/Spherical_law_of_cosines).
        distanceTo: /* @__PURE__ */ __name(function(other) {
          return Earth.distance(this, toLatLng(other));
        }, "distanceTo"),
        // @method wrap(): LatLng
        // Returns a new `LatLng` object with the longitude wrapped so it's always between -180 and +180 degrees.
        wrap: /* @__PURE__ */ __name(function() {
          return Earth.wrapLatLng(this);
        }, "wrap"),
        // @method toBounds(sizeInMeters: Number): LatLngBounds
        // Returns a new `LatLngBounds` object in which each boundary is `sizeInMeters/2` meters apart from the `LatLng`.
        toBounds: /* @__PURE__ */ __name(function(sizeInMeters) {
          var latAccuracy = 180 * sizeInMeters / 40075017, lngAccuracy = latAccuracy / Math.cos(Math.PI / 180 * this.lat);
          return toLatLngBounds(
            [this.lat - latAccuracy, this.lng - lngAccuracy],
            [this.lat + latAccuracy, this.lng + lngAccuracy]
          );
        }, "toBounds"),
        clone: /* @__PURE__ */ __name(function() {
          return new LatLng(this.lat, this.lng, this.alt);
        }, "clone")
      };
      function toLatLng(a, b, c) {
        if (a instanceof LatLng) {
          return a;
        }
        if (isArray(a) && typeof a[0] !== "object") {
          if (a.length === 3) {
            return new LatLng(a[0], a[1], a[2]);
          }
          if (a.length === 2) {
            return new LatLng(a[0], a[1]);
          }
          return null;
        }
        if (a === void 0 || a === null) {
          return a;
        }
        if (typeof a === "object" && "lat" in a) {
          return new LatLng(a.lat, "lng" in a ? a.lng : a.lon, a.alt);
        }
        if (b === void 0) {
          return null;
        }
        return new LatLng(a, b, c);
      }
      __name(toLatLng, "toLatLng");
      var CRS = {
        // @method latLngToPoint(latlng: LatLng, zoom: Number): Point
        // Projects geographical coordinates into pixel coordinates for a given zoom.
        latLngToPoint: /* @__PURE__ */ __name(function(latlng, zoom2) {
          var projectedPoint = this.projection.project(latlng), scale2 = this.scale(zoom2);
          return this.transformation._transform(projectedPoint, scale2);
        }, "latLngToPoint"),
        // @method pointToLatLng(point: Point, zoom: Number): LatLng
        // The inverse of `latLngToPoint`. Projects pixel coordinates on a given
        // zoom into geographical coordinates.
        pointToLatLng: /* @__PURE__ */ __name(function(point, zoom2) {
          var scale2 = this.scale(zoom2), untransformedPoint = this.transformation.untransform(point, scale2);
          return this.projection.unproject(untransformedPoint);
        }, "pointToLatLng"),
        // @method project(latlng: LatLng): Point
        // Projects geographical coordinates into coordinates in units accepted for
        // this CRS (e.g. meters for EPSG:3857, for passing it to WMS services).
        project: /* @__PURE__ */ __name(function(latlng) {
          return this.projection.project(latlng);
        }, "project"),
        // @method unproject(point: Point): LatLng
        // Given a projected coordinate returns the corresponding LatLng.
        // The inverse of `project`.
        unproject: /* @__PURE__ */ __name(function(point) {
          return this.projection.unproject(point);
        }, "unproject"),
        // @method scale(zoom: Number): Number
        // Returns the scale used when transforming projected coordinates into
        // pixel coordinates for a particular zoom. For example, it returns
        // `256 * 2^zoom` for Mercator-based CRS.
        scale: /* @__PURE__ */ __name(function(zoom2) {
          return 256 * Math.pow(2, zoom2);
        }, "scale"),
        // @method zoom(scale: Number): Number
        // Inverse of `scale()`, returns the zoom level corresponding to a scale
        // factor of `scale`.
        zoom: /* @__PURE__ */ __name(function(scale2) {
          return Math.log(scale2 / 256) / Math.LN2;
        }, "zoom"),
        // @method getProjectedBounds(zoom: Number): Bounds
        // Returns the projection's bounds scaled and transformed for the provided `zoom`.
        getProjectedBounds: /* @__PURE__ */ __name(function(zoom2) {
          if (this.infinite) {
            return null;
          }
          var b = this.projection.bounds, s = this.scale(zoom2), min = this.transformation.transform(b.min, s), max = this.transformation.transform(b.max, s);
          return new Bounds(min, max);
        }, "getProjectedBounds"),
        // @method distance(latlng1: LatLng, latlng2: LatLng): Number
        // Returns the distance between two geographical coordinates.
        // @property code: String
        // Standard code name of the CRS passed into WMS services (e.g. `'EPSG:3857'`)
        //
        // @property wrapLng: Number[]
        // An array of two numbers defining whether the longitude (horizontal) coordinate
        // axis wraps around a given range and how. Defaults to `[-180, 180]` in most
        // geographical CRSs. If `undefined`, the longitude axis does not wrap around.
        //
        // @property wrapLat: Number[]
        // Like `wrapLng`, but for the latitude (vertical) axis.
        // wrapLng: [min, max],
        // wrapLat: [min, max],
        // @property infinite: Boolean
        // If true, the coordinate space will be unbounded (infinite in both axes)
        infinite: false,
        // @method wrapLatLng(latlng: LatLng): LatLng
        // Returns a `LatLng` where lat and lng has been wrapped according to the
        // CRS's `wrapLat` and `wrapLng` properties, if they are outside the CRS's bounds.
        wrapLatLng: /* @__PURE__ */ __name(function(latlng) {
          var lng = this.wrapLng ? wrapNum(latlng.lng, this.wrapLng, true) : latlng.lng, lat = this.wrapLat ? wrapNum(latlng.lat, this.wrapLat, true) : latlng.lat, alt = latlng.alt;
          return new LatLng(lat, lng, alt);
        }, "wrapLatLng"),
        // @method wrapLatLngBounds(bounds: LatLngBounds): LatLngBounds
        // Returns a `LatLngBounds` with the same size as the given one, ensuring
        // that its center is within the CRS's bounds.
        // Only accepts actual `L.LatLngBounds` instances, not arrays.
        wrapLatLngBounds: /* @__PURE__ */ __name(function(bounds) {
          var center = bounds.getCenter(), newCenter = this.wrapLatLng(center), latShift = center.lat - newCenter.lat, lngShift = center.lng - newCenter.lng;
          if (latShift === 0 && lngShift === 0) {
            return bounds;
          }
          var sw = bounds.getSouthWest(), ne = bounds.getNorthEast(), newSw = new LatLng(sw.lat - latShift, sw.lng - lngShift), newNe = new LatLng(ne.lat - latShift, ne.lng - lngShift);
          return new LatLngBounds(newSw, newNe);
        }, "wrapLatLngBounds")
      };
      var Earth = extend({}, CRS, {
        wrapLng: [-180, 180],
        // Mean Earth Radius, as recommended for use by
        // the International Union of Geodesy and Geophysics,
        // see https://rosettacode.org/wiki/Haversine_formula
        R: 6371e3,
        // distance between two geographical points using spherical law of cosines approximation
        distance: /* @__PURE__ */ __name(function(latlng1, latlng2) {
          var rad = Math.PI / 180, lat1 = latlng1.lat * rad, lat2 = latlng2.lat * rad, sinDLat = Math.sin((latlng2.lat - latlng1.lat) * rad / 2), sinDLon = Math.sin((latlng2.lng - latlng1.lng) * rad / 2), a = sinDLat * sinDLat + Math.cos(lat1) * Math.cos(lat2) * sinDLon * sinDLon, c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
          return this.R * c;
        }, "distance")
      });
      var earthRadius = 6378137;
      var SphericalMercator = {
        R: earthRadius,
        MAX_LATITUDE: 85.0511287798,
        project: /* @__PURE__ */ __name(function(latlng) {
          var d = Math.PI / 180, max = this.MAX_LATITUDE, lat = Math.max(Math.min(max, latlng.lat), -max), sin = Math.sin(lat * d);
          return new Point(
            this.R * latlng.lng * d,
            this.R * Math.log((1 + sin) / (1 - sin)) / 2
          );
        }, "project"),
        unproject: /* @__PURE__ */ __name(function(point) {
          var d = 180 / Math.PI;
          return new LatLng(
            (2 * Math.atan(Math.exp(point.y / this.R)) - Math.PI / 2) * d,
            point.x * d / this.R
          );
        }, "unproject"),
        bounds: (function() {
          var d = earthRadius * Math.PI;
          return new Bounds([-d, -d], [d, d]);
        })()
      };
      function Transformation(a, b, c, d) {
        if (isArray(a)) {
          this._a = a[0];
          this._b = a[1];
          this._c = a[2];
          this._d = a[3];
          return;
        }
        this._a = a;
        this._b = b;
        this._c = c;
        this._d = d;
      }
      __name(Transformation, "Transformation");
      Transformation.prototype = {
        // @method transform(point: Point, scale?: Number): Point
        // Returns a transformed point, optionally multiplied by the given scale.
        // Only accepts actual `L.Point` instances, not arrays.
        transform: /* @__PURE__ */ __name(function(point, scale2) {
          return this._transform(point.clone(), scale2);
        }, "transform"),
        // destructive transform (faster)
        _transform: /* @__PURE__ */ __name(function(point, scale2) {
          scale2 = scale2 || 1;
          point.x = scale2 * (this._a * point.x + this._b);
          point.y = scale2 * (this._c * point.y + this._d);
          return point;
        }, "_transform"),
        // @method untransform(point: Point, scale?: Number): Point
        // Returns the reverse transformation of the given point, optionally divided
        // by the given scale. Only accepts actual `L.Point` instances, not arrays.
        untransform: /* @__PURE__ */ __name(function(point, scale2) {
          scale2 = scale2 || 1;
          return new Point(
            (point.x / scale2 - this._b) / this._a,
            (point.y / scale2 - this._d) / this._c
          );
        }, "untransform")
      };
      function toTransformation(a, b, c, d) {
        return new Transformation(a, b, c, d);
      }
      __name(toTransformation, "toTransformation");
      var EPSG3857 = extend({}, Earth, {
        code: "EPSG:3857",
        projection: SphericalMercator,
        transformation: (function() {
          var scale2 = 0.5 / (Math.PI * SphericalMercator.R);
          return toTransformation(scale2, 0.5, -scale2, 0.5);
        })()
      });
      var EPSG900913 = extend({}, EPSG3857, {
        code: "EPSG:900913"
      });
      function svgCreate(name) {
        return document.createElementNS("http://www.w3.org/2000/svg", name);
      }
      __name(svgCreate, "svgCreate");
      function pointsToPath(rings, closed) {
        var str = "", i, j, len, len2, points, p;
        for (i = 0, len = rings.length; i < len; i++) {
          points = rings[i];
          for (j = 0, len2 = points.length; j < len2; j++) {
            p = points[j];
            str += (j ? "L" : "M") + p.x + " " + p.y;
          }
          str += closed ? Browser.svg ? "z" : "x" : "";
        }
        return str || "M0 0";
      }
      __name(pointsToPath, "pointsToPath");
      var style = document.documentElement.style;
      var ie = "ActiveXObject" in window;
      var ielt9 = ie && !document.addEventListener;
      var edge = "msLaunchUri" in navigator && !("documentMode" in document);
      var webkit = userAgentContains("webkit");
      var android = userAgentContains("android");
      var android23 = userAgentContains("android 2") || userAgentContains("android 3");
      var webkitVer = parseInt(/WebKit\/([0-9]+)|$/.exec(navigator.userAgent)[1], 10);
      var androidStock = android && userAgentContains("Google") && webkitVer < 537 && !("AudioNode" in window);
      var opera = !!window.opera;
      var chrome = !edge && userAgentContains("chrome");
      var gecko = userAgentContains("gecko") && !webkit && !opera && !ie;
      var safari = !chrome && userAgentContains("safari");
      var phantom = userAgentContains("phantom");
      var opera12 = "OTransition" in style;
      var win = navigator.platform.indexOf("Win") === 0;
      var ie3d = ie && "transition" in style;
      var webkit3d = "WebKitCSSMatrix" in window && "m11" in new window.WebKitCSSMatrix() && !android23;
      var gecko3d = "MozPerspective" in style;
      var any3d = !window.L_DISABLE_3D && (ie3d || webkit3d || gecko3d) && !opera12 && !phantom;
      var mobile = typeof orientation !== "undefined" || userAgentContains("mobile");
      var mobileWebkit = mobile && webkit;
      var mobileWebkit3d = mobile && webkit3d;
      var msPointer = !window.PointerEvent && window.MSPointerEvent;
      var pointer = !!(window.PointerEvent || msPointer);
      var touchNative = "ontouchstart" in window || !!window.TouchEvent;
      var touch = !window.L_NO_TOUCH && (touchNative || pointer);
      var mobileOpera = mobile && opera;
      var mobileGecko = mobile && gecko;
      var retina = (window.devicePixelRatio || window.screen.deviceXDPI / window.screen.logicalXDPI) > 1;
      var passiveEvents = (function() {
        var supportsPassiveOption = false;
        try {
          var opts = Object.defineProperty({}, "passive", {
            get: /* @__PURE__ */ __name(function() {
              supportsPassiveOption = true;
            }, "get")
          });
          window.addEventListener("testPassiveEventSupport", falseFn, opts);
          window.removeEventListener("testPassiveEventSupport", falseFn, opts);
        } catch (e) {
        }
        return supportsPassiveOption;
      })();
      var canvas$1 = (function() {
        return !!document.createElement("canvas").getContext;
      })();
      var svg$1 = !!(document.createElementNS && svgCreate("svg").createSVGRect);
      var inlineSvg = !!svg$1 && (function() {
        var div = document.createElement("div");
        div.innerHTML = "<svg/>";
        return (div.firstChild && div.firstChild.namespaceURI) === "http://www.w3.org/2000/svg";
      })();
      var vml = !svg$1 && (function() {
        try {
          var div = document.createElement("div");
          div.innerHTML = '<v:shape adj="1"/>';
          var shape = div.firstChild;
          shape.style.behavior = "url(#default#VML)";
          return shape && typeof shape.adj === "object";
        } catch (e) {
          return false;
        }
      })();
      var mac = navigator.platform.indexOf("Mac") === 0;
      var linux = navigator.platform.indexOf("Linux") === 0;
      function userAgentContains(str) {
        return navigator.userAgent.toLowerCase().indexOf(str) >= 0;
      }
      __name(userAgentContains, "userAgentContains");
      var Browser = {
        ie,
        ielt9,
        edge,
        webkit,
        android,
        android23,
        androidStock,
        opera,
        chrome,
        gecko,
        safari,
        phantom,
        opera12,
        win,
        ie3d,
        webkit3d,
        gecko3d,
        any3d,
        mobile,
        mobileWebkit,
        mobileWebkit3d,
        msPointer,
        pointer,
        touch,
        touchNative,
        mobileOpera,
        mobileGecko,
        retina,
        passiveEvents,
        canvas: canvas$1,
        svg: svg$1,
        vml,
        inlineSvg,
        mac,
        linux
      };
      var POINTER_DOWN = Browser.msPointer ? "MSPointerDown" : "pointerdown";
      var POINTER_MOVE = Browser.msPointer ? "MSPointerMove" : "pointermove";
      var POINTER_UP = Browser.msPointer ? "MSPointerUp" : "pointerup";
      var POINTER_CANCEL = Browser.msPointer ? "MSPointerCancel" : "pointercancel";
      var pEvent = {
        touchstart: POINTER_DOWN,
        touchmove: POINTER_MOVE,
        touchend: POINTER_UP,
        touchcancel: POINTER_CANCEL
      };
      var handle = {
        touchstart: _onPointerStart,
        touchmove: _handlePointer,
        touchend: _handlePointer,
        touchcancel: _handlePointer
      };
      var _pointers = {};
      var _pointerDocListener = false;
      function addPointerListener(obj, type, handler) {
        if (type === "touchstart") {
          _addPointerDocListener();
        }
        if (!handle[type]) {
          console.warn("wrong event specified:", type);
          return falseFn;
        }
        handler = handle[type].bind(this, handler);
        obj.addEventListener(pEvent[type], handler, false);
        return handler;
      }
      __name(addPointerListener, "addPointerListener");
      function removePointerListener(obj, type, handler) {
        if (!pEvent[type]) {
          console.warn("wrong event specified:", type);
          return;
        }
        obj.removeEventListener(pEvent[type], handler, false);
      }
      __name(removePointerListener, "removePointerListener");
      function _globalPointerDown(e) {
        _pointers[e.pointerId] = e;
      }
      __name(_globalPointerDown, "_globalPointerDown");
      function _globalPointerMove(e) {
        if (_pointers[e.pointerId]) {
          _pointers[e.pointerId] = e;
        }
      }
      __name(_globalPointerMove, "_globalPointerMove");
      function _globalPointerUp(e) {
        delete _pointers[e.pointerId];
      }
      __name(_globalPointerUp, "_globalPointerUp");
      function _addPointerDocListener() {
        if (!_pointerDocListener) {
          document.addEventListener(POINTER_DOWN, _globalPointerDown, true);
          document.addEventListener(POINTER_MOVE, _globalPointerMove, true);
          document.addEventListener(POINTER_UP, _globalPointerUp, true);
          document.addEventListener(POINTER_CANCEL, _globalPointerUp, true);
          _pointerDocListener = true;
        }
      }
      __name(_addPointerDocListener, "_addPointerDocListener");
      function _handlePointer(handler, e) {
        if (e.pointerType === (e.MSPOINTER_TYPE_MOUSE || "mouse")) {
          return;
        }
        e.touches = [];
        for (var i in _pointers) {
          e.touches.push(_pointers[i]);
        }
        e.changedTouches = [e];
        handler(e);
      }
      __name(_handlePointer, "_handlePointer");
      function _onPointerStart(handler, e) {
        if (e.MSPOINTER_TYPE_TOUCH && e.pointerType === e.MSPOINTER_TYPE_TOUCH) {
          preventDefault(e);
        }
        _handlePointer(handler, e);
      }
      __name(_onPointerStart, "_onPointerStart");
      function makeDblclick(event) {
        var newEvent = {}, prop, i;
        for (i in event) {
          prop = event[i];
          newEvent[i] = prop && prop.bind ? prop.bind(event) : prop;
        }
        event = newEvent;
        newEvent.type = "dblclick";
        newEvent.detail = 2;
        newEvent.isTrusted = false;
        newEvent._simulated = true;
        return newEvent;
      }
      __name(makeDblclick, "makeDblclick");
      var delay = 200;
      function addDoubleTapListener(obj, handler) {
        obj.addEventListener("dblclick", handler);
        var last = 0, detail;
        function simDblclick(e) {
          if (e.detail !== 1) {
            detail = e.detail;
            return;
          }
          if (e.pointerType === "mouse" || e.sourceCapabilities && !e.sourceCapabilities.firesTouchEvents) {
            return;
          }
          var path = getPropagationPath(e);
          if (path.some(function(el) {
            return el instanceof HTMLLabelElement && el.attributes.for;
          }) && !path.some(function(el) {
            return el instanceof HTMLInputElement || el instanceof HTMLSelectElement;
          })) {
            return;
          }
          var now = Date.now();
          if (now - last <= delay) {
            detail++;
            if (detail === 2) {
              handler(makeDblclick(e));
            }
          } else {
            detail = 1;
          }
          last = now;
        }
        __name(simDblclick, "simDblclick");
        obj.addEventListener("click", simDblclick);
        return {
          dblclick: handler,
          simDblclick
        };
      }
      __name(addDoubleTapListener, "addDoubleTapListener");
      function removeDoubleTapListener(obj, handlers) {
        obj.removeEventListener("dblclick", handlers.dblclick);
        obj.removeEventListener("click", handlers.simDblclick);
      }
      __name(removeDoubleTapListener, "removeDoubleTapListener");
      var TRANSFORM = testProp(
        ["transform", "webkitTransform", "OTransform", "MozTransform", "msTransform"]
      );
      var TRANSITION = testProp(
        ["webkitTransition", "transition", "OTransition", "MozTransition", "msTransition"]
      );
      var TRANSITION_END = TRANSITION === "webkitTransition" || TRANSITION === "OTransition" ? TRANSITION + "End" : "transitionend";
      function get(id) {
        return typeof id === "string" ? document.getElementById(id) : id;
      }
      __name(get, "get");
      function getStyle(el, style2) {
        var value = el.style[style2] || el.currentStyle && el.currentStyle[style2];
        if ((!value || value === "auto") && document.defaultView) {
          var css = document.defaultView.getComputedStyle(el, null);
          value = css ? css[style2] : null;
        }
        return value === "auto" ? null : value;
      }
      __name(getStyle, "getStyle");
      function create$1(tagName, className, container) {
        var el = document.createElement(tagName);
        el.className = className || "";
        if (container) {
          container.appendChild(el);
        }
        return el;
      }
      __name(create$1, "create$1");
      function remove(el) {
        var parent = el.parentNode;
        if (parent) {
          parent.removeChild(el);
        }
      }
      __name(remove, "remove");
      function empty(el) {
        while (el.firstChild) {
          el.removeChild(el.firstChild);
        }
      }
      __name(empty, "empty");
      function toFront(el) {
        var parent = el.parentNode;
        if (parent && parent.lastChild !== el) {
          parent.appendChild(el);
        }
      }
      __name(toFront, "toFront");
      function toBack(el) {
        var parent = el.parentNode;
        if (parent && parent.firstChild !== el) {
          parent.insertBefore(el, parent.firstChild);
        }
      }
      __name(toBack, "toBack");
      function hasClass(el, name) {
        if (el.classList !== void 0) {
          return el.classList.contains(name);
        }
        var className = getClass(el);
        return className.length > 0 && new RegExp("(^|\\s)" + name + "(\\s|$)").test(className);
      }
      __name(hasClass, "hasClass");
      function addClass(el, name) {
        if (el.classList !== void 0) {
          var classes = splitWords(name);
          for (var i = 0, len = classes.length; i < len; i++) {
            el.classList.add(classes[i]);
          }
        } else if (!hasClass(el, name)) {
          var className = getClass(el);
          setClass(el, (className ? className + " " : "") + name);
        }
      }
      __name(addClass, "addClass");
      function removeClass(el, name) {
        if (el.classList !== void 0) {
          el.classList.remove(name);
        } else {
          setClass(el, trim((" " + getClass(el) + " ").replace(" " + name + " ", " ")));
        }
      }
      __name(removeClass, "removeClass");
      function setClass(el, name) {
        if (el.className.baseVal === void 0) {
          el.className = name;
        } else {
          el.className.baseVal = name;
        }
      }
      __name(setClass, "setClass");
      function getClass(el) {
        if (el.correspondingElement) {
          el = el.correspondingElement;
        }
        return el.className.baseVal === void 0 ? el.className : el.className.baseVal;
      }
      __name(getClass, "getClass");
      function setOpacity(el, value) {
        if ("opacity" in el.style) {
          el.style.opacity = value;
        } else if ("filter" in el.style) {
          _setOpacityIE(el, value);
        }
      }
      __name(setOpacity, "setOpacity");
      function _setOpacityIE(el, value) {
        var filter = false, filterName = "DXImageTransform.Microsoft.Alpha";
        try {
          filter = el.filters.item(filterName);
        } catch (e) {
          if (value === 1) {
            return;
          }
        }
        value = Math.round(value * 100);
        if (filter) {
          filter.Enabled = value !== 100;
          filter.Opacity = value;
        } else {
          el.style.filter += " progid:" + filterName + "(opacity=" + value + ")";
        }
      }
      __name(_setOpacityIE, "_setOpacityIE");
      function testProp(props) {
        var style2 = document.documentElement.style;
        for (var i = 0; i < props.length; i++) {
          if (props[i] in style2) {
            return props[i];
          }
        }
        return false;
      }
      __name(testProp, "testProp");
      function setTransform(el, offset, scale2) {
        var pos = offset || new Point(0, 0);
        el.style[TRANSFORM] = (Browser.ie3d ? "translate(" + pos.x + "px," + pos.y + "px)" : "translate3d(" + pos.x + "px," + pos.y + "px,0)") + (scale2 ? " scale(" + scale2 + ")" : "");
      }
      __name(setTransform, "setTransform");
      function setPosition(el, point) {
        el._leaflet_pos = point;
        if (Browser.any3d) {
          setTransform(el, point);
        } else {
          el.style.left = point.x + "px";
          el.style.top = point.y + "px";
        }
      }
      __name(setPosition, "setPosition");
      function getPosition(el) {
        return el._leaflet_pos || new Point(0, 0);
      }
      __name(getPosition, "getPosition");
      var disableTextSelection;
      var enableTextSelection;
      var _userSelect;
      if ("onselectstart" in document) {
        disableTextSelection = /* @__PURE__ */ __name(function() {
          on(window, "selectstart", preventDefault);
        }, "disableTextSelection");
        enableTextSelection = /* @__PURE__ */ __name(function() {
          off(window, "selectstart", preventDefault);
        }, "enableTextSelection");
      } else {
        var userSelectProperty = testProp(
          ["userSelect", "WebkitUserSelect", "OUserSelect", "MozUserSelect", "msUserSelect"]
        );
        disableTextSelection = /* @__PURE__ */ __name(function() {
          if (userSelectProperty) {
            var style2 = document.documentElement.style;
            _userSelect = style2[userSelectProperty];
            style2[userSelectProperty] = "none";
          }
        }, "disableTextSelection");
        enableTextSelection = /* @__PURE__ */ __name(function() {
          if (userSelectProperty) {
            document.documentElement.style[userSelectProperty] = _userSelect;
            _userSelect = void 0;
          }
        }, "enableTextSelection");
      }
      function disableImageDrag() {
        on(window, "dragstart", preventDefault);
      }
      __name(disableImageDrag, "disableImageDrag");
      function enableImageDrag() {
        off(window, "dragstart", preventDefault);
      }
      __name(enableImageDrag, "enableImageDrag");
      var _outlineElement, _outlineStyle;
      function preventOutline(element) {
        while (element.tabIndex === -1) {
          element = element.parentNode;
        }
        if (!element.style) {
          return;
        }
        restoreOutline();
        _outlineElement = element;
        _outlineStyle = element.style.outlineStyle;
        element.style.outlineStyle = "none";
        on(window, "keydown", restoreOutline);
      }
      __name(preventOutline, "preventOutline");
      function restoreOutline() {
        if (!_outlineElement) {
          return;
        }
        _outlineElement.style.outlineStyle = _outlineStyle;
        _outlineElement = void 0;
        _outlineStyle = void 0;
        off(window, "keydown", restoreOutline);
      }
      __name(restoreOutline, "restoreOutline");
      function getSizedParentNode(element) {
        do {
          element = element.parentNode;
        } while ((!element.offsetWidth || !element.offsetHeight) && element !== document.body);
        return element;
      }
      __name(getSizedParentNode, "getSizedParentNode");
      function getScale(element) {
        var rect = element.getBoundingClientRect();
        return {
          x: rect.width / element.offsetWidth || 1,
          y: rect.height / element.offsetHeight || 1,
          boundingClientRect: rect
        };
      }
      __name(getScale, "getScale");
      var DomUtil = {
        __proto__: null,
        TRANSFORM,
        TRANSITION,
        TRANSITION_END,
        get,
        getStyle,
        create: create$1,
        remove,
        empty,
        toFront,
        toBack,
        hasClass,
        addClass,
        removeClass,
        setClass,
        getClass,
        setOpacity,
        testProp,
        setTransform,
        setPosition,
        getPosition,
        get disableTextSelection() {
          return disableTextSelection;
        },
        get enableTextSelection() {
          return enableTextSelection;
        },
        disableImageDrag,
        enableImageDrag,
        preventOutline,
        restoreOutline,
        getSizedParentNode,
        getScale
      };
      function on(obj, types2, fn, context) {
        if (types2 && typeof types2 === "object") {
          for (var type in types2) {
            addOne(obj, type, types2[type], fn);
          }
        } else {
          types2 = splitWords(types2);
          for (var i = 0, len = types2.length; i < len; i++) {
            addOne(obj, types2[i], fn, context);
          }
        }
        return this;
      }
      __name(on, "on");
      var eventsKey = "_leaflet_events";
      function off(obj, types2, fn, context) {
        if (arguments.length === 1) {
          batchRemove(obj);
          delete obj[eventsKey];
        } else if (types2 && typeof types2 === "object") {
          for (var type in types2) {
            removeOne(obj, type, types2[type], fn);
          }
        } else {
          types2 = splitWords(types2);
          if (arguments.length === 2) {
            batchRemove(obj, function(type2) {
              return indexOf(types2, type2) !== -1;
            });
          } else {
            for (var i = 0, len = types2.length; i < len; i++) {
              removeOne(obj, types2[i], fn, context);
            }
          }
        }
        return this;
      }
      __name(off, "off");
      function batchRemove(obj, filterFn) {
        for (var id in obj[eventsKey]) {
          var type = id.split(/\d/)[0];
          if (!filterFn || filterFn(type)) {
            removeOne(obj, type, null, null, id);
          }
        }
      }
      __name(batchRemove, "batchRemove");
      var mouseSubst = {
        mouseenter: "mouseover",
        mouseleave: "mouseout",
        wheel: !("onwheel" in window) && "mousewheel"
      };
      function addOne(obj, type, fn, context) {
        var id = type + stamp(fn) + (context ? "_" + stamp(context) : "");
        if (obj[eventsKey] && obj[eventsKey][id]) {
          return this;
        }
        var handler = /* @__PURE__ */ __name(function(e) {
          return fn.call(context || obj, e || window.event);
        }, "handler");
        var originalHandler = handler;
        if (!Browser.touchNative && Browser.pointer && type.indexOf("touch") === 0) {
          handler = addPointerListener(obj, type, handler);
        } else if (Browser.touch && type === "dblclick") {
          handler = addDoubleTapListener(obj, handler);
        } else if ("addEventListener" in obj) {
          if (type === "touchstart" || type === "touchmove" || type === "wheel" || type === "mousewheel") {
            obj.addEventListener(mouseSubst[type] || type, handler, Browser.passiveEvents ? { passive: false } : false);
          } else if (type === "mouseenter" || type === "mouseleave") {
            handler = /* @__PURE__ */ __name(function(e) {
              e = e || window.event;
              if (isExternalTarget(obj, e)) {
                originalHandler(e);
              }
            }, "handler");
            obj.addEventListener(mouseSubst[type], handler, false);
          } else {
            obj.addEventListener(type, originalHandler, false);
          }
        } else {
          obj.attachEvent("on" + type, handler);
        }
        obj[eventsKey] = obj[eventsKey] || {};
        obj[eventsKey][id] = handler;
      }
      __name(addOne, "addOne");
      function removeOne(obj, type, fn, context, id) {
        id = id || type + stamp(fn) + (context ? "_" + stamp(context) : "");
        var handler = obj[eventsKey] && obj[eventsKey][id];
        if (!handler) {
          return this;
        }
        if (!Browser.touchNative && Browser.pointer && type.indexOf("touch") === 0) {
          removePointerListener(obj, type, handler);
        } else if (Browser.touch && type === "dblclick") {
          removeDoubleTapListener(obj, handler);
        } else if ("removeEventListener" in obj) {
          obj.removeEventListener(mouseSubst[type] || type, handler, false);
        } else {
          obj.detachEvent("on" + type, handler);
        }
        obj[eventsKey][id] = null;
      }
      __name(removeOne, "removeOne");
      function stopPropagation(e) {
        if (e.stopPropagation) {
          e.stopPropagation();
        } else if (e.originalEvent) {
          e.originalEvent._stopped = true;
        } else {
          e.cancelBubble = true;
        }
        return this;
      }
      __name(stopPropagation, "stopPropagation");
      function disableScrollPropagation(el) {
        addOne(el, "wheel", stopPropagation);
        return this;
      }
      __name(disableScrollPropagation, "disableScrollPropagation");
      function disableClickPropagation(el) {
        on(el, "mousedown touchstart dblclick contextmenu", stopPropagation);
        el["_leaflet_disable_click"] = true;
        return this;
      }
      __name(disableClickPropagation, "disableClickPropagation");
      function preventDefault(e) {
        if (e.preventDefault) {
          e.preventDefault();
        } else {
          e.returnValue = false;
        }
        return this;
      }
      __name(preventDefault, "preventDefault");
      function stop(e) {
        preventDefault(e);
        stopPropagation(e);
        return this;
      }
      __name(stop, "stop");
      function getPropagationPath(ev) {
        if (ev.composedPath) {
          return ev.composedPath();
        }
        var path = [];
        var el = ev.target;
        while (el) {
          path.push(el);
          el = el.parentNode;
        }
        return path;
      }
      __name(getPropagationPath, "getPropagationPath");
      function getMousePosition(e, container) {
        if (!container) {
          return new Point(e.clientX, e.clientY);
        }
        var scale2 = getScale(container), offset = scale2.boundingClientRect;
        return new Point(
          // offset.left/top values are in page scale (like clientX/Y),
          // whereas clientLeft/Top (border width) values are the original values (before CSS scale applies).
          (e.clientX - offset.left) / scale2.x - container.clientLeft,
          (e.clientY - offset.top) / scale2.y - container.clientTop
        );
      }
      __name(getMousePosition, "getMousePosition");
      var wheelPxFactor = Browser.linux && Browser.chrome ? window.devicePixelRatio : Browser.mac ? window.devicePixelRatio * 3 : window.devicePixelRatio > 0 ? 2 * window.devicePixelRatio : 1;
      function getWheelDelta(e) {
        return Browser.edge ? e.wheelDeltaY / 2 : (
          // Don't trust window-geometry-based delta
          e.deltaY && e.deltaMode === 0 ? -e.deltaY / wheelPxFactor : (
            // Pixels
            e.deltaY && e.deltaMode === 1 ? -e.deltaY * 20 : (
              // Lines
              e.deltaY && e.deltaMode === 2 ? -e.deltaY * 60 : (
                // Pages
                e.deltaX || e.deltaZ ? 0 : (
                  // Skip horizontal/depth wheel events
                  e.wheelDelta ? (e.wheelDeltaY || e.wheelDelta) / 2 : (
                    // Legacy IE pixels
                    e.detail && Math.abs(e.detail) < 32765 ? -e.detail * 20 : (
                      // Legacy Moz lines
                      e.detail ? e.detail / -32765 * 60 : (
                        // Legacy Moz pages
                        0
                      )
                    )
                  )
                )
              )
            )
          )
        );
      }
      __name(getWheelDelta, "getWheelDelta");
      function isExternalTarget(el, e) {
        var related = e.relatedTarget;
        if (!related) {
          return true;
        }
        try {
          while (related && related !== el) {
            related = related.parentNode;
          }
        } catch (err) {
          return false;
        }
        return related !== el;
      }
      __name(isExternalTarget, "isExternalTarget");
      var DomEvent = {
        __proto__: null,
        on,
        off,
        stopPropagation,
        disableScrollPropagation,
        disableClickPropagation,
        preventDefault,
        stop,
        getPropagationPath,
        getMousePosition,
        getWheelDelta,
        isExternalTarget,
        addListener: on,
        removeListener: off
      };
      var PosAnimation = Evented.extend({
        // @method run(el: HTMLElement, newPos: Point, duration?: Number, easeLinearity?: Number)
        // Run an animation of a given element to a new position, optionally setting
        // duration in seconds (`0.25` by default) and easing linearity factor (3rd
        // argument of the [cubic bezier curve](https://cubic-bezier.com/#0,0,.5,1),
        // `0.5` by default).
        run: /* @__PURE__ */ __name(function(el, newPos, duration, easeLinearity) {
          this.stop();
          this._el = el;
          this._inProgress = true;
          this._duration = duration || 0.25;
          this._easeOutPower = 1 / Math.max(easeLinearity || 0.5, 0.2);
          this._startPos = getPosition(el);
          this._offset = newPos.subtract(this._startPos);
          this._startTime = +/* @__PURE__ */ new Date();
          this.fire("start");
          this._animate();
        }, "run"),
        // @method stop()
        // Stops the animation (if currently running).
        stop: /* @__PURE__ */ __name(function() {
          if (!this._inProgress) {
            return;
          }
          this._step(true);
          this._complete();
        }, "stop"),
        _animate: /* @__PURE__ */ __name(function() {
          this._animId = requestAnimFrame(this._animate, this);
          this._step();
        }, "_animate"),
        _step: /* @__PURE__ */ __name(function(round) {
          var elapsed = +/* @__PURE__ */ new Date() - this._startTime, duration = this._duration * 1e3;
          if (elapsed < duration) {
            this._runFrame(this._easeOut(elapsed / duration), round);
          } else {
            this._runFrame(1);
            this._complete();
          }
        }, "_step"),
        _runFrame: /* @__PURE__ */ __name(function(progress, round) {
          var pos = this._startPos.add(this._offset.multiplyBy(progress));
          if (round) {
            pos._round();
          }
          setPosition(this._el, pos);
          this.fire("step");
        }, "_runFrame"),
        _complete: /* @__PURE__ */ __name(function() {
          cancelAnimFrame(this._animId);
          this._inProgress = false;
          this.fire("end");
        }, "_complete"),
        _easeOut: /* @__PURE__ */ __name(function(t) {
          return 1 - Math.pow(1 - t, this._easeOutPower);
        }, "_easeOut")
      });
      var Map = Evented.extend({
        options: {
          // @section Map State Options
          // @option crs: CRS = L.CRS.EPSG3857
          // The [Coordinate Reference System](#crs) to use. Don't change this if you're not
          // sure what it means.
          crs: EPSG3857,
          // @option center: LatLng = undefined
          // Initial geographic center of the map
          center: void 0,
          // @option zoom: Number = undefined
          // Initial map zoom level
          zoom: void 0,
          // @option minZoom: Number = *
          // Minimum zoom level of the map.
          // If not specified and at least one `GridLayer` or `TileLayer` is in the map,
          // the lowest of their `minZoom` options will be used instead.
          minZoom: void 0,
          // @option maxZoom: Number = *
          // Maximum zoom level of the map.
          // If not specified and at least one `GridLayer` or `TileLayer` is in the map,
          // the highest of their `maxZoom` options will be used instead.
          maxZoom: void 0,
          // @option layers: Layer[] = []
          // Array of layers that will be added to the map initially
          layers: [],
          // @option maxBounds: LatLngBounds = null
          // When this option is set, the map restricts the view to the given
          // geographical bounds, bouncing the user back if the user tries to pan
          // outside the view. To set the restriction dynamically, use
          // [`setMaxBounds`](#map-setmaxbounds) method.
          maxBounds: void 0,
          // @option renderer: Renderer = *
          // The default method for drawing vector layers on the map. `L.SVG`
          // or `L.Canvas` by default depending on browser support.
          renderer: void 0,
          // @section Animation Options
          // @option zoomAnimation: Boolean = true
          // Whether the map zoom animation is enabled. By default it's enabled
          // in all browsers that support CSS3 Transitions except Android.
          zoomAnimation: true,
          // @option zoomAnimationThreshold: Number = 4
          // Won't animate zoom if the zoom difference exceeds this value.
          zoomAnimationThreshold: 4,
          // @option fadeAnimation: Boolean = true
          // Whether the tile fade animation is enabled. By default it's enabled
          // in all browsers that support CSS3 Transitions except Android.
          fadeAnimation: true,
          // @option markerZoomAnimation: Boolean = true
          // Whether markers animate their zoom with the zoom animation, if disabled
          // they will disappear for the length of the animation. By default it's
          // enabled in all browsers that support CSS3 Transitions except Android.
          markerZoomAnimation: true,
          // @option transform3DLimit: Number = 2^23
          // Defines the maximum size of a CSS translation transform. The default
          // value should not be changed unless a web browser positions layers in
          // the wrong place after doing a large `panBy`.
          transform3DLimit: 8388608,
          // Precision limit of a 32-bit float
          // @section Interaction Options
          // @option zoomSnap: Number = 1
          // Forces the map's zoom level to always be a multiple of this, particularly
          // right after a [`fitBounds()`](#map-fitbounds) or a pinch-zoom.
          // By default, the zoom level snaps to the nearest integer; lower values
          // (e.g. `0.5` or `0.1`) allow for greater granularity. A value of `0`
          // means the zoom level will not be snapped after `fitBounds` or a pinch-zoom.
          zoomSnap: 1,
          // @option zoomDelta: Number = 1
          // Controls how much the map's zoom level will change after a
          // [`zoomIn()`](#map-zoomin), [`zoomOut()`](#map-zoomout), pressing `+`
          // or `-` on the keyboard, or using the [zoom controls](#control-zoom).
          // Values smaller than `1` (e.g. `0.5`) allow for greater granularity.
          zoomDelta: 1,
          // @option trackResize: Boolean = true
          // Whether the map automatically handles browser window resize to update itself.
          trackResize: true
        },
        initialize: /* @__PURE__ */ __name(function(id, options) {
          options = setOptions(this, options);
          this._handlers = [];
          this._layers = {};
          this._zoomBoundLayers = {};
          this._sizeChanged = true;
          this._initContainer(id);
          this._initLayout();
          this._onResize = bind(this._onResize, this);
          this._initEvents();
          if (options.maxBounds) {
            this.setMaxBounds(options.maxBounds);
          }
          if (options.zoom !== void 0) {
            this._zoom = this._limitZoom(options.zoom);
          }
          if (options.center && options.zoom !== void 0) {
            this.setView(toLatLng(options.center), options.zoom, { reset: true });
          }
          this.callInitHooks();
          this._zoomAnimated = TRANSITION && Browser.any3d && !Browser.mobileOpera && this.options.zoomAnimation;
          if (this._zoomAnimated) {
            this._createAnimProxy();
            on(this._proxy, TRANSITION_END, this._catchTransitionEnd, this);
          }
          this._addLayers(this.options.layers);
        }, "initialize"),
        // @section Methods for modifying map state
        // @method setView(center: LatLng, zoom: Number, options?: Zoom/pan options): this
        // Sets the view of the map (geographical center and zoom) with the given
        // animation options.
        setView: /* @__PURE__ */ __name(function(center, zoom2, options) {
          zoom2 = zoom2 === void 0 ? this._zoom : this._limitZoom(zoom2);
          center = this._limitCenter(toLatLng(center), zoom2, this.options.maxBounds);
          options = options || {};
          this._stop();
          if (this._loaded && !options.reset && options !== true) {
            if (options.animate !== void 0) {
              options.zoom = extend({ animate: options.animate }, options.zoom);
              options.pan = extend({ animate: options.animate, duration: options.duration }, options.pan);
            }
            var moved = this._zoom !== zoom2 ? this._tryAnimatedZoom && this._tryAnimatedZoom(center, zoom2, options.zoom) : this._tryAnimatedPan(center, options.pan);
            if (moved) {
              clearTimeout(this._sizeTimer);
              return this;
            }
          }
          this._resetView(center, zoom2, options.pan && options.pan.noMoveStart);
          return this;
        }, "setView"),
        // @method setZoom(zoom: Number, options?: Zoom/pan options): this
        // Sets the zoom of the map.
        setZoom: /* @__PURE__ */ __name(function(zoom2, options) {
          if (!this._loaded) {
            this._zoom = zoom2;
            return this;
          }
          return this.setView(this.getCenter(), zoom2, { zoom: options });
        }, "setZoom"),
        // @method zoomIn(delta?: Number, options?: Zoom options): this
        // Increases the zoom of the map by `delta` ([`zoomDelta`](#map-zoomdelta) by default).
        zoomIn: /* @__PURE__ */ __name(function(delta, options) {
          delta = delta || (Browser.any3d ? this.options.zoomDelta : 1);
          return this.setZoom(this._zoom + delta, options);
        }, "zoomIn"),
        // @method zoomOut(delta?: Number, options?: Zoom options): this
        // Decreases the zoom of the map by `delta` ([`zoomDelta`](#map-zoomdelta) by default).
        zoomOut: /* @__PURE__ */ __name(function(delta, options) {
          delta = delta || (Browser.any3d ? this.options.zoomDelta : 1);
          return this.setZoom(this._zoom - delta, options);
        }, "zoomOut"),
        // @method setZoomAround(latlng: LatLng, zoom: Number, options: Zoom options): this
        // Zooms the map while keeping a specified geographical point on the map
        // stationary (e.g. used internally for scroll zoom and double-click zoom).
        // @alternative
        // @method setZoomAround(offset: Point, zoom: Number, options: Zoom options): this
        // Zooms the map while keeping a specified pixel on the map (relative to the top-left corner) stationary.
        setZoomAround: /* @__PURE__ */ __name(function(latlng, zoom2, options) {
          var scale2 = this.getZoomScale(zoom2), viewHalf = this.getSize().divideBy(2), containerPoint = latlng instanceof Point ? latlng : this.latLngToContainerPoint(latlng), centerOffset = containerPoint.subtract(viewHalf).multiplyBy(1 - 1 / scale2), newCenter = this.containerPointToLatLng(viewHalf.add(centerOffset));
          return this.setView(newCenter, zoom2, { zoom: options });
        }, "setZoomAround"),
        _getBoundsCenterZoom: /* @__PURE__ */ __name(function(bounds, options) {
          options = options || {};
          bounds = bounds.getBounds ? bounds.getBounds() : toLatLngBounds(bounds);
          var paddingTL = toPoint(options.paddingTopLeft || options.padding || [0, 0]), paddingBR = toPoint(options.paddingBottomRight || options.padding || [0, 0]), zoom2 = this.getBoundsZoom(bounds, false, paddingTL.add(paddingBR));
          zoom2 = typeof options.maxZoom === "number" ? Math.min(options.maxZoom, zoom2) : zoom2;
          if (zoom2 === Infinity) {
            return {
              center: bounds.getCenter(),
              zoom: zoom2
            };
          }
          var paddingOffset = paddingBR.subtract(paddingTL).divideBy(2), swPoint = this.project(bounds.getSouthWest(), zoom2), nePoint = this.project(bounds.getNorthEast(), zoom2), center = this.unproject(swPoint.add(nePoint).divideBy(2).add(paddingOffset), zoom2);
          return {
            center,
            zoom: zoom2
          };
        }, "_getBoundsCenterZoom"),
        // @method fitBounds(bounds: LatLngBounds, options?: fitBounds options): this
        // Sets a map view that contains the given geographical bounds with the
        // maximum zoom level possible.
        fitBounds: /* @__PURE__ */ __name(function(bounds, options) {
          bounds = toLatLngBounds(bounds);
          if (!bounds.isValid()) {
            throw new Error("Bounds are not valid.");
          }
          var target = this._getBoundsCenterZoom(bounds, options);
          return this.setView(target.center, target.zoom, options);
        }, "fitBounds"),
        // @method fitWorld(options?: fitBounds options): this
        // Sets a map view that mostly contains the whole world with the maximum
        // zoom level possible.
        fitWorld: /* @__PURE__ */ __name(function(options) {
          return this.fitBounds([[-90, -180], [90, 180]], options);
        }, "fitWorld"),
        // @method panTo(latlng: LatLng, options?: Pan options): this
        // Pans the map to a given center.
        panTo: /* @__PURE__ */ __name(function(center, options) {
          return this.setView(center, this._zoom, { pan: options });
        }, "panTo"),
        // @method panBy(offset: Point, options?: Pan options): this
        // Pans the map by a given number of pixels (animated).
        panBy: /* @__PURE__ */ __name(function(offset, options) {
          offset = toPoint(offset).round();
          options = options || {};
          if (!offset.x && !offset.y) {
            return this.fire("moveend");
          }
          if (options.animate !== true && !this.getSize().contains(offset)) {
            this._resetView(this.unproject(this.project(this.getCenter()).add(offset)), this.getZoom());
            return this;
          }
          if (!this._panAnim) {
            this._panAnim = new PosAnimation();
            this._panAnim.on({
              "step": this._onPanTransitionStep,
              "end": this._onPanTransitionEnd
            }, this);
          }
          if (!options.noMoveStart) {
            this.fire("movestart");
          }
          if (options.animate !== false) {
            addClass(this._mapPane, "leaflet-pan-anim");
            var newPos = this._getMapPanePos().subtract(offset).round();
            this._panAnim.run(this._mapPane, newPos, options.duration || 0.25, options.easeLinearity);
          } else {
            this._rawPanBy(offset);
            this.fire("move").fire("moveend");
          }
          return this;
        }, "panBy"),
        // @method flyTo(latlng: LatLng, zoom?: Number, options?: Zoom/pan options): this
        // Sets the view of the map (geographical center and zoom) performing a smooth
        // pan-zoom animation.
        flyTo: /* @__PURE__ */ __name(function(targetCenter, targetZoom, options) {
          options = options || {};
          if (options.animate === false || !Browser.any3d) {
            return this.setView(targetCenter, targetZoom, options);
          }
          this._stop();
          var from = this.project(this.getCenter()), to = this.project(targetCenter), size = this.getSize(), startZoom = this._zoom;
          targetCenter = toLatLng(targetCenter);
          targetZoom = targetZoom === void 0 ? startZoom : targetZoom;
          var w0 = Math.max(size.x, size.y), w1 = w0 * this.getZoomScale(startZoom, targetZoom), u1 = to.distanceTo(from) || 1, rho = 1.42, rho2 = rho * rho;
          function r(i) {
            var s1 = i ? -1 : 1, s2 = i ? w1 : w0, t1 = w1 * w1 - w0 * w0 + s1 * rho2 * rho2 * u1 * u1, b1 = 2 * s2 * rho2 * u1, b = t1 / b1, sq = Math.sqrt(b * b + 1) - b;
            var log = sq < 1e-9 ? -18 : Math.log(sq);
            return log;
          }
          __name(r, "r");
          function sinh(n) {
            return (Math.exp(n) - Math.exp(-n)) / 2;
          }
          __name(sinh, "sinh");
          function cosh(n) {
            return (Math.exp(n) + Math.exp(-n)) / 2;
          }
          __name(cosh, "cosh");
          function tanh(n) {
            return sinh(n) / cosh(n);
          }
          __name(tanh, "tanh");
          var r0 = r(0);
          function w(s) {
            return w0 * (cosh(r0) / cosh(r0 + rho * s));
          }
          __name(w, "w");
          function u(s) {
            return w0 * (cosh(r0) * tanh(r0 + rho * s) - sinh(r0)) / rho2;
          }
          __name(u, "u");
          function easeOut(t) {
            return 1 - Math.pow(1 - t, 1.5);
          }
          __name(easeOut, "easeOut");
          var start = Date.now(), S = (r(1) - r0) / rho, duration = options.duration ? 1e3 * options.duration : 1e3 * S * 0.8;
          function frame() {
            var t = (Date.now() - start) / duration, s = easeOut(t) * S;
            if (t <= 1) {
              this._flyToFrame = requestAnimFrame(frame, this);
              this._move(
                this.unproject(from.add(to.subtract(from).multiplyBy(u(s) / u1)), startZoom),
                this.getScaleZoom(w0 / w(s), startZoom),
                { flyTo: true }
              );
            } else {
              this._move(targetCenter, targetZoom)._moveEnd(true);
            }
          }
          __name(frame, "frame");
          this._moveStart(true, options.noMoveStart);
          frame.call(this);
          return this;
        }, "flyTo"),
        // @method flyToBounds(bounds: LatLngBounds, options?: fitBounds options): this
        // Sets the view of the map with a smooth animation like [`flyTo`](#map-flyto),
        // but takes a bounds parameter like [`fitBounds`](#map-fitbounds).
        flyToBounds: /* @__PURE__ */ __name(function(bounds, options) {
          var target = this._getBoundsCenterZoom(bounds, options);
          return this.flyTo(target.center, target.zoom, options);
        }, "flyToBounds"),
        // @method setMaxBounds(bounds: LatLngBounds): this
        // Restricts the map view to the given bounds (see the [maxBounds](#map-maxbounds) option).
        setMaxBounds: /* @__PURE__ */ __name(function(bounds) {
          bounds = toLatLngBounds(bounds);
          if (this.listens("moveend", this._panInsideMaxBounds)) {
            this.off("moveend", this._panInsideMaxBounds);
          }
          if (!bounds.isValid()) {
            this.options.maxBounds = null;
            return this;
          }
          this.options.maxBounds = bounds;
          if (this._loaded) {
            this._panInsideMaxBounds();
          }
          return this.on("moveend", this._panInsideMaxBounds);
        }, "setMaxBounds"),
        // @method setMinZoom(zoom: Number): this
        // Sets the lower limit for the available zoom levels (see the [minZoom](#map-minzoom) option).
        setMinZoom: /* @__PURE__ */ __name(function(zoom2) {
          var oldZoom = this.options.minZoom;
          this.options.minZoom = zoom2;
          if (this._loaded && oldZoom !== zoom2) {
            this.fire("zoomlevelschange");
            if (this.getZoom() < this.options.minZoom) {
              return this.setZoom(zoom2);
            }
          }
          return this;
        }, "setMinZoom"),
        // @method setMaxZoom(zoom: Number): this
        // Sets the upper limit for the available zoom levels (see the [maxZoom](#map-maxzoom) option).
        setMaxZoom: /* @__PURE__ */ __name(function(zoom2) {
          var oldZoom = this.options.maxZoom;
          this.options.maxZoom = zoom2;
          if (this._loaded && oldZoom !== zoom2) {
            this.fire("zoomlevelschange");
            if (this.getZoom() > this.options.maxZoom) {
              return this.setZoom(zoom2);
            }
          }
          return this;
        }, "setMaxZoom"),
        // @method panInsideBounds(bounds: LatLngBounds, options?: Pan options): this
        // Pans the map to the closest view that would lie inside the given bounds (if it's not already), controlling the animation using the options specific, if any.
        panInsideBounds: /* @__PURE__ */ __name(function(bounds, options) {
          this._enforcingBounds = true;
          var center = this.getCenter(), newCenter = this._limitCenter(center, this._zoom, toLatLngBounds(bounds));
          if (!center.equals(newCenter)) {
            this.panTo(newCenter, options);
          }
          this._enforcingBounds = false;
          return this;
        }, "panInsideBounds"),
        // @method panInside(latlng: LatLng, options?: padding options): this
        // Pans the map the minimum amount to make the `latlng` visible. Use
        // padding options to fit the display to more restricted bounds.
        // If `latlng` is already within the (optionally padded) display bounds,
        // the map will not be panned.
        panInside: /* @__PURE__ */ __name(function(latlng, options) {
          options = options || {};
          var paddingTL = toPoint(options.paddingTopLeft || options.padding || [0, 0]), paddingBR = toPoint(options.paddingBottomRight || options.padding || [0, 0]), pixelCenter = this.project(this.getCenter()), pixelPoint = this.project(latlng), pixelBounds = this.getPixelBounds(), paddedBounds = toBounds([pixelBounds.min.add(paddingTL), pixelBounds.max.subtract(paddingBR)]), paddedSize = paddedBounds.getSize();
          if (!paddedBounds.contains(pixelPoint)) {
            this._enforcingBounds = true;
            var centerOffset = pixelPoint.subtract(paddedBounds.getCenter());
            var offset = paddedBounds.extend(pixelPoint).getSize().subtract(paddedSize);
            pixelCenter.x += centerOffset.x < 0 ? -offset.x : offset.x;
            pixelCenter.y += centerOffset.y < 0 ? -offset.y : offset.y;
            this.panTo(this.unproject(pixelCenter), options);
            this._enforcingBounds = false;
          }
          return this;
        }, "panInside"),
        // @method invalidateSize(options: Zoom/pan options): this
        // Checks if the map container size changed and updates the map if so —
        // call it after you've changed the map size dynamically, also animating
        // pan by default. If `options.pan` is `false`, panning will not occur.
        // If `options.debounceMoveend` is `true`, it will delay `moveend` event so
        // that it doesn't happen often even if the method is called many
        // times in a row.
        // @alternative
        // @method invalidateSize(animate: Boolean): this
        // Checks if the map container size changed and updates the map if so —
        // call it after you've changed the map size dynamically, also animating
        // pan by default.
        invalidateSize: /* @__PURE__ */ __name(function(options) {
          if (!this._loaded) {
            return this;
          }
          options = extend({
            animate: false,
            pan: true
          }, options === true ? { animate: true } : options);
          var oldSize = this.getSize();
          this._sizeChanged = true;
          this._lastCenter = null;
          var newSize = this.getSize(), oldCenter = oldSize.divideBy(2).round(), newCenter = newSize.divideBy(2).round(), offset = oldCenter.subtract(newCenter);
          if (!offset.x && !offset.y) {
            return this;
          }
          if (options.animate && options.pan) {
            this.panBy(offset);
          } else {
            if (options.pan) {
              this._rawPanBy(offset);
            }
            this.fire("move");
            if (options.debounceMoveend) {
              clearTimeout(this._sizeTimer);
              this._sizeTimer = setTimeout(bind(this.fire, this, "moveend"), 200);
            } else {
              this.fire("moveend");
            }
          }
          return this.fire("resize", {
            oldSize,
            newSize
          });
        }, "invalidateSize"),
        // @section Methods for modifying map state
        // @method stop(): this
        // Stops the currently running `panTo` or `flyTo` animation, if any.
        stop: /* @__PURE__ */ __name(function() {
          this.setZoom(this._limitZoom(this._zoom));
          if (!this.options.zoomSnap) {
            this.fire("viewreset");
          }
          return this._stop();
        }, "stop"),
        // @section Geolocation methods
        // @method locate(options?: Locate options): this
        // Tries to locate the user using the Geolocation API, firing a [`locationfound`](#map-locationfound)
        // event with location data on success or a [`locationerror`](#map-locationerror) event on failure,
        // and optionally sets the map view to the user's location with respect to
        // detection accuracy (or to the world view if geolocation failed).
        // Note that, if your page doesn't use HTTPS, this method will fail in
        // modern browsers ([Chrome 50 and newer](https://sites.google.com/a/chromium.org/dev/Home/chromium-security/deprecating-powerful-features-on-insecure-origins))
        // See `Locate options` for more details.
        locate: /* @__PURE__ */ __name(function(options) {
          options = this._locateOptions = extend({
            timeout: 1e4,
            watch: false
            // setView: false
            // maxZoom: <Number>
            // maximumAge: 0
            // enableHighAccuracy: false
          }, options);
          if (!("geolocation" in navigator)) {
            this._handleGeolocationError({
              code: 0,
              message: "Geolocation not supported."
            });
            return this;
          }
          var onResponse = bind(this._handleGeolocationResponse, this), onError = bind(this._handleGeolocationError, this);
          if (options.watch) {
            this._locationWatchId = navigator.geolocation.watchPosition(onResponse, onError, options);
          } else {
            navigator.geolocation.getCurrentPosition(onResponse, onError, options);
          }
          return this;
        }, "locate"),
        // @method stopLocate(): this
        // Stops watching location previously initiated by `map.locate({watch: true})`
        // and aborts resetting the map view if map.locate was called with
        // `{setView: true}`.
        stopLocate: /* @__PURE__ */ __name(function() {
          if (navigator.geolocation && navigator.geolocation.clearWatch) {
            navigator.geolocation.clearWatch(this._locationWatchId);
          }
          if (this._locateOptions) {
            this._locateOptions.setView = false;
          }
          return this;
        }, "stopLocate"),
        _handleGeolocationError: /* @__PURE__ */ __name(function(error) {
          if (!this._container._leaflet_id) {
            return;
          }
          var c = error.code, message = error.message || (c === 1 ? "permission denied" : c === 2 ? "position unavailable" : "timeout");
          if (this._locateOptions.setView && !this._loaded) {
            this.fitWorld();
          }
          this.fire("locationerror", {
            code: c,
            message: "Geolocation error: " + message + "."
          });
        }, "_handleGeolocationError"),
        _handleGeolocationResponse: /* @__PURE__ */ __name(function(pos) {
          if (!this._container._leaflet_id) {
            return;
          }
          var lat = pos.coords.latitude, lng = pos.coords.longitude, latlng = new LatLng(lat, lng), bounds = latlng.toBounds(pos.coords.accuracy * 2), options = this._locateOptions;
          if (options.setView) {
            var zoom2 = this.getBoundsZoom(bounds);
            this.setView(latlng, options.maxZoom ? Math.min(zoom2, options.maxZoom) : zoom2);
          }
          var data = {
            latlng,
            bounds,
            timestamp: pos.timestamp
          };
          for (var i in pos.coords) {
            if (typeof pos.coords[i] === "number") {
              data[i] = pos.coords[i];
            }
          }
          this.fire("locationfound", data);
        }, "_handleGeolocationResponse"),
        // TODO Appropriate docs section?
        // @section Other Methods
        // @method addHandler(name: String, HandlerClass: Function): this
        // Adds a new `Handler` to the map, given its name and constructor function.
        addHandler: /* @__PURE__ */ __name(function(name, HandlerClass) {
          if (!HandlerClass) {
            return this;
          }
          var handler = this[name] = new HandlerClass(this);
          this._handlers.push(handler);
          if (this.options[name]) {
            handler.enable();
          }
          return this;
        }, "addHandler"),
        // @method remove(): this
        // Destroys the map and clears all related event listeners.
        remove: /* @__PURE__ */ __name(function() {
          this._initEvents(true);
          if (this.options.maxBounds) {
            this.off("moveend", this._panInsideMaxBounds);
          }
          if (this._containerId !== this._container._leaflet_id) {
            throw new Error("Map container is being reused by another instance");
          }
          try {
            delete this._container._leaflet_id;
            delete this._containerId;
          } catch (e) {
            this._container._leaflet_id = void 0;
            this._containerId = void 0;
          }
          if (this._locationWatchId !== void 0) {
            this.stopLocate();
          }
          this._stop();
          remove(this._mapPane);
          if (this._clearControlPos) {
            this._clearControlPos();
          }
          if (this._resizeRequest) {
            cancelAnimFrame(this._resizeRequest);
            this._resizeRequest = null;
          }
          this._clearHandlers();
          if (this._loaded) {
            this.fire("unload");
          }
          var i;
          for (i in this._layers) {
            this._layers[i].remove();
          }
          for (i in this._panes) {
            remove(this._panes[i]);
          }
          this._layers = [];
          this._panes = [];
          delete this._mapPane;
          delete this._renderer;
          return this;
        }, "remove"),
        // @section Other Methods
        // @method createPane(name: String, container?: HTMLElement): HTMLElement
        // Creates a new [map pane](#map-pane) with the given name if it doesn't exist already,
        // then returns it. The pane is created as a child of `container`, or
        // as a child of the main map pane if not set.
        createPane: /* @__PURE__ */ __name(function(name, container) {
          var className = "leaflet-pane" + (name ? " leaflet-" + name.replace("Pane", "") + "-pane" : ""), pane = create$1("div", className, container || this._mapPane);
          if (name) {
            this._panes[name] = pane;
          }
          return pane;
        }, "createPane"),
        // @section Methods for Getting Map State
        // @method getCenter(): LatLng
        // Returns the geographical center of the map view
        getCenter: /* @__PURE__ */ __name(function() {
          this._checkIfLoaded();
          if (this._lastCenter && !this._moved()) {
            return this._lastCenter.clone();
          }
          return this.layerPointToLatLng(this._getCenterLayerPoint());
        }, "getCenter"),
        // @method getZoom(): Number
        // Returns the current zoom level of the map view
        getZoom: /* @__PURE__ */ __name(function() {
          return this._zoom;
        }, "getZoom"),
        // @method getBounds(): LatLngBounds
        // Returns the geographical bounds visible in the current map view
        getBounds: /* @__PURE__ */ __name(function() {
          var bounds = this.getPixelBounds(), sw = this.unproject(bounds.getBottomLeft()), ne = this.unproject(bounds.getTopRight());
          return new LatLngBounds(sw, ne);
        }, "getBounds"),
        // @method getMinZoom(): Number
        // Returns the minimum zoom level of the map (if set in the `minZoom` option of the map or of any layers), or `0` by default.
        getMinZoom: /* @__PURE__ */ __name(function() {
          return this.options.minZoom === void 0 ? this._layersMinZoom || 0 : this.options.minZoom;
        }, "getMinZoom"),
        // @method getMaxZoom(): Number
        // Returns the maximum zoom level of the map (if set in the `maxZoom` option of the map or of any layers).
        getMaxZoom: /* @__PURE__ */ __name(function() {
          return this.options.maxZoom === void 0 ? this._layersMaxZoom === void 0 ? Infinity : this._layersMaxZoom : this.options.maxZoom;
        }, "getMaxZoom"),
        // @method getBoundsZoom(bounds: LatLngBounds, inside?: Boolean, padding?: Point): Number
        // Returns the maximum zoom level on which the given bounds fit to the map
        // view in its entirety. If `inside` (optional) is set to `true`, the method
        // instead returns the minimum zoom level on which the map view fits into
        // the given bounds in its entirety.
        getBoundsZoom: /* @__PURE__ */ __name(function(bounds, inside, padding) {
          bounds = toLatLngBounds(bounds);
          padding = toPoint(padding || [0, 0]);
          var zoom2 = this.getZoom() || 0, min = this.getMinZoom(), max = this.getMaxZoom(), nw = bounds.getNorthWest(), se = bounds.getSouthEast(), size = this.getSize().subtract(padding), boundsSize = toBounds(this.project(se, zoom2), this.project(nw, zoom2)).getSize(), snap = Browser.any3d ? this.options.zoomSnap : 1, scalex = size.x / boundsSize.x, scaley = size.y / boundsSize.y, scale2 = inside ? Math.max(scalex, scaley) : Math.min(scalex, scaley);
          zoom2 = this.getScaleZoom(scale2, zoom2);
          if (snap) {
            zoom2 = Math.round(zoom2 / (snap / 100)) * (snap / 100);
            zoom2 = inside ? Math.ceil(zoom2 / snap) * snap : Math.floor(zoom2 / snap) * snap;
          }
          return Math.max(min, Math.min(max, zoom2));
        }, "getBoundsZoom"),
        // @method getSize(): Point
        // Returns the current size of the map container (in pixels).
        getSize: /* @__PURE__ */ __name(function() {
          if (!this._size || this._sizeChanged) {
            this._size = new Point(
              this._container.clientWidth || 0,
              this._container.clientHeight || 0
            );
            this._sizeChanged = false;
          }
          return this._size.clone();
        }, "getSize"),
        // @method getPixelBounds(): Bounds
        // Returns the bounds of the current map view in projected pixel
        // coordinates (sometimes useful in layer and overlay implementations).
        getPixelBounds: /* @__PURE__ */ __name(function(center, zoom2) {
          var topLeftPoint = this._getTopLeftPoint(center, zoom2);
          return new Bounds(topLeftPoint, topLeftPoint.add(this.getSize()));
        }, "getPixelBounds"),
        // TODO: Check semantics - isn't the pixel origin the 0,0 coord relative to
        // the map pane? "left point of the map layer" can be confusing, specially
        // since there can be negative offsets.
        // @method getPixelOrigin(): Point
        // Returns the projected pixel coordinates of the top left point of
        // the map layer (useful in custom layer and overlay implementations).
        getPixelOrigin: /* @__PURE__ */ __name(function() {
          this._checkIfLoaded();
          return this._pixelOrigin;
        }, "getPixelOrigin"),
        // @method getPixelWorldBounds(zoom?: Number): Bounds
        // Returns the world's bounds in pixel coordinates for zoom level `zoom`.
        // If `zoom` is omitted, the map's current zoom level is used.
        getPixelWorldBounds: /* @__PURE__ */ __name(function(zoom2) {
          return this.options.crs.getProjectedBounds(zoom2 === void 0 ? this.getZoom() : zoom2);
        }, "getPixelWorldBounds"),
        // @section Other Methods
        // @method getPane(pane: String|HTMLElement): HTMLElement
        // Returns a [map pane](#map-pane), given its name or its HTML element (its identity).
        getPane: /* @__PURE__ */ __name(function(pane) {
          return typeof pane === "string" ? this._panes[pane] : pane;
        }, "getPane"),
        // @method getPanes(): Object
        // Returns a plain object containing the names of all [panes](#map-pane) as keys and
        // the panes as values.
        getPanes: /* @__PURE__ */ __name(function() {
          return this._panes;
        }, "getPanes"),
        // @method getContainer: HTMLElement
        // Returns the HTML element that contains the map.
        getContainer: /* @__PURE__ */ __name(function() {
          return this._container;
        }, "getContainer"),
        // @section Conversion Methods
        // @method getZoomScale(toZoom: Number, fromZoom: Number): Number
        // Returns the scale factor to be applied to a map transition from zoom level
        // `fromZoom` to `toZoom`. Used internally to help with zoom animations.
        getZoomScale: /* @__PURE__ */ __name(function(toZoom, fromZoom) {
          var crs = this.options.crs;
          fromZoom = fromZoom === void 0 ? this._zoom : fromZoom;
          return crs.scale(toZoom) / crs.scale(fromZoom);
        }, "getZoomScale"),
        // @method getScaleZoom(scale: Number, fromZoom: Number): Number
        // Returns the zoom level that the map would end up at, if it is at `fromZoom`
        // level and everything is scaled by a factor of `scale`. Inverse of
        // [`getZoomScale`](#map-getZoomScale).
        getScaleZoom: /* @__PURE__ */ __name(function(scale2, fromZoom) {
          var crs = this.options.crs;
          fromZoom = fromZoom === void 0 ? this._zoom : fromZoom;
          var zoom2 = crs.zoom(scale2 * crs.scale(fromZoom));
          return isNaN(zoom2) ? Infinity : zoom2;
        }, "getScaleZoom"),
        // @method project(latlng: LatLng, zoom: Number): Point
        // Projects a geographical coordinate `LatLng` according to the projection
        // of the map's CRS, then scales it according to `zoom` and the CRS's
        // `Transformation`. The result is pixel coordinate relative to
        // the CRS origin.
        project: /* @__PURE__ */ __name(function(latlng, zoom2) {
          zoom2 = zoom2 === void 0 ? this._zoom : zoom2;
          return this.options.crs.latLngToPoint(toLatLng(latlng), zoom2);
        }, "project"),
        // @method unproject(point: Point, zoom: Number): LatLng
        // Inverse of [`project`](#map-project).
        unproject: /* @__PURE__ */ __name(function(point, zoom2) {
          zoom2 = zoom2 === void 0 ? this._zoom : zoom2;
          return this.options.crs.pointToLatLng(toPoint(point), zoom2);
        }, "unproject"),
        // @method layerPointToLatLng(point: Point): LatLng
        // Given a pixel coordinate relative to the [origin pixel](#map-getpixelorigin),
        // returns the corresponding geographical coordinate (for the current zoom level).
        layerPointToLatLng: /* @__PURE__ */ __name(function(point) {
          var projectedPoint = toPoint(point).add(this.getPixelOrigin());
          return this.unproject(projectedPoint);
        }, "layerPointToLatLng"),
        // @method latLngToLayerPoint(latlng: LatLng): Point
        // Given a geographical coordinate, returns the corresponding pixel coordinate
        // relative to the [origin pixel](#map-getpixelorigin).
        latLngToLayerPoint: /* @__PURE__ */ __name(function(latlng) {
          var projectedPoint = this.project(toLatLng(latlng))._round();
          return projectedPoint._subtract(this.getPixelOrigin());
        }, "latLngToLayerPoint"),
        // @method wrapLatLng(latlng: LatLng): LatLng
        // Returns a `LatLng` where `lat` and `lng` has been wrapped according to the
        // map's CRS's `wrapLat` and `wrapLng` properties, if they are outside the
        // CRS's bounds.
        // By default this means longitude is wrapped around the dateline so its
        // value is between -180 and +180 degrees.
        wrapLatLng: /* @__PURE__ */ __name(function(latlng) {
          return this.options.crs.wrapLatLng(toLatLng(latlng));
        }, "wrapLatLng"),
        // @method wrapLatLngBounds(bounds: LatLngBounds): LatLngBounds
        // Returns a `LatLngBounds` with the same size as the given one, ensuring that
        // its center is within the CRS's bounds.
        // By default this means the center longitude is wrapped around the dateline so its
        // value is between -180 and +180 degrees, and the majority of the bounds
        // overlaps the CRS's bounds.
        wrapLatLngBounds: /* @__PURE__ */ __name(function(latlng) {
          return this.options.crs.wrapLatLngBounds(toLatLngBounds(latlng));
        }, "wrapLatLngBounds"),
        // @method distance(latlng1: LatLng, latlng2: LatLng): Number
        // Returns the distance between two geographical coordinates according to
        // the map's CRS. By default this measures distance in meters.
        distance: /* @__PURE__ */ __name(function(latlng1, latlng2) {
          return this.options.crs.distance(toLatLng(latlng1), toLatLng(latlng2));
        }, "distance"),
        // @method containerPointToLayerPoint(point: Point): Point
        // Given a pixel coordinate relative to the map container, returns the corresponding
        // pixel coordinate relative to the [origin pixel](#map-getpixelorigin).
        containerPointToLayerPoint: /* @__PURE__ */ __name(function(point) {
          return toPoint(point).subtract(this._getMapPanePos());
        }, "containerPointToLayerPoint"),
        // @method layerPointToContainerPoint(point: Point): Point
        // Given a pixel coordinate relative to the [origin pixel](#map-getpixelorigin),
        // returns the corresponding pixel coordinate relative to the map container.
        layerPointToContainerPoint: /* @__PURE__ */ __name(function(point) {
          return toPoint(point).add(this._getMapPanePos());
        }, "layerPointToContainerPoint"),
        // @method containerPointToLatLng(point: Point): LatLng
        // Given a pixel coordinate relative to the map container, returns
        // the corresponding geographical coordinate (for the current zoom level).
        containerPointToLatLng: /* @__PURE__ */ __name(function(point) {
          var layerPoint = this.containerPointToLayerPoint(toPoint(point));
          return this.layerPointToLatLng(layerPoint);
        }, "containerPointToLatLng"),
        // @method latLngToContainerPoint(latlng: LatLng): Point
        // Given a geographical coordinate, returns the corresponding pixel coordinate
        // relative to the map container.
        latLngToContainerPoint: /* @__PURE__ */ __name(function(latlng) {
          return this.layerPointToContainerPoint(this.latLngToLayerPoint(toLatLng(latlng)));
        }, "latLngToContainerPoint"),
        // @method mouseEventToContainerPoint(ev: MouseEvent): Point
        // Given a MouseEvent object, returns the pixel coordinate relative to the
        // map container where the event took place.
        mouseEventToContainerPoint: /* @__PURE__ */ __name(function(e) {
          return getMousePosition(e, this._container);
        }, "mouseEventToContainerPoint"),
        // @method mouseEventToLayerPoint(ev: MouseEvent): Point
        // Given a MouseEvent object, returns the pixel coordinate relative to
        // the [origin pixel](#map-getpixelorigin) where the event took place.
        mouseEventToLayerPoint: /* @__PURE__ */ __name(function(e) {
          return this.containerPointToLayerPoint(this.mouseEventToContainerPoint(e));
        }, "mouseEventToLayerPoint"),
        // @method mouseEventToLatLng(ev: MouseEvent): LatLng
        // Given a MouseEvent object, returns geographical coordinate where the
        // event took place.
        mouseEventToLatLng: /* @__PURE__ */ __name(function(e) {
          return this.layerPointToLatLng(this.mouseEventToLayerPoint(e));
        }, "mouseEventToLatLng"),
        // map initialization methods
        _initContainer: /* @__PURE__ */ __name(function(id) {
          var container = this._container = get(id);
          if (!container) {
            throw new Error("Map container not found.");
          } else if (container._leaflet_id) {
            throw new Error("Map container is already initialized.");
          }
          on(container, "scroll", this._onScroll, this);
          this._containerId = stamp(container);
        }, "_initContainer"),
        _initLayout: /* @__PURE__ */ __name(function() {
          var container = this._container;
          this._fadeAnimated = this.options.fadeAnimation && Browser.any3d;
          addClass(container, "leaflet-container" + (Browser.touch ? " leaflet-touch" : "") + (Browser.retina ? " leaflet-retina" : "") + (Browser.ielt9 ? " leaflet-oldie" : "") + (Browser.safari ? " leaflet-safari" : "") + (this._fadeAnimated ? " leaflet-fade-anim" : ""));
          var position = getStyle(container, "position");
          if (position !== "absolute" && position !== "relative" && position !== "fixed" && position !== "sticky") {
            container.style.position = "relative";
          }
          this._initPanes();
          if (this._initControlPos) {
            this._initControlPos();
          }
        }, "_initLayout"),
        _initPanes: /* @__PURE__ */ __name(function() {
          var panes = this._panes = {};
          this._paneRenderers = {};
          this._mapPane = this.createPane("mapPane", this._container);
          setPosition(this._mapPane, new Point(0, 0));
          this.createPane("tilePane");
          this.createPane("overlayPane");
          this.createPane("shadowPane");
          this.createPane("markerPane");
          this.createPane("tooltipPane");
          this.createPane("popupPane");
          if (!this.options.markerZoomAnimation) {
            addClass(panes.markerPane, "leaflet-zoom-hide");
            addClass(panes.shadowPane, "leaflet-zoom-hide");
          }
        }, "_initPanes"),
        // private methods that modify map state
        // @section Map state change events
        _resetView: /* @__PURE__ */ __name(function(center, zoom2, noMoveStart) {
          setPosition(this._mapPane, new Point(0, 0));
          var loading = !this._loaded;
          this._loaded = true;
          zoom2 = this._limitZoom(zoom2);
          this.fire("viewprereset");
          var zoomChanged = this._zoom !== zoom2;
          this._moveStart(zoomChanged, noMoveStart)._move(center, zoom2)._moveEnd(zoomChanged);
          this.fire("viewreset");
          if (loading) {
            this.fire("load");
          }
        }, "_resetView"),
        _moveStart: /* @__PURE__ */ __name(function(zoomChanged, noMoveStart) {
          if (zoomChanged) {
            this.fire("zoomstart");
          }
          if (!noMoveStart) {
            this.fire("movestart");
          }
          return this;
        }, "_moveStart"),
        _move: /* @__PURE__ */ __name(function(center, zoom2, data, supressEvent) {
          if (zoom2 === void 0) {
            zoom2 = this._zoom;
          }
          var zoomChanged = this._zoom !== zoom2;
          this._zoom = zoom2;
          this._lastCenter = center;
          this._pixelOrigin = this._getNewPixelOrigin(center);
          if (!supressEvent) {
            if (zoomChanged || data && data.pinch) {
              this.fire("zoom", data);
            }
            this.fire("move", data);
          } else if (data && data.pinch) {
            this.fire("zoom", data);
          }
          return this;
        }, "_move"),
        _moveEnd: /* @__PURE__ */ __name(function(zoomChanged) {
          if (zoomChanged) {
            this.fire("zoomend");
          }
          return this.fire("moveend");
        }, "_moveEnd"),
        _stop: /* @__PURE__ */ __name(function() {
          cancelAnimFrame(this._flyToFrame);
          if (this._panAnim) {
            this._panAnim.stop();
          }
          return this;
        }, "_stop"),
        _rawPanBy: /* @__PURE__ */ __name(function(offset) {
          setPosition(this._mapPane, this._getMapPanePos().subtract(offset));
        }, "_rawPanBy"),
        _getZoomSpan: /* @__PURE__ */ __name(function() {
          return this.getMaxZoom() - this.getMinZoom();
        }, "_getZoomSpan"),
        _panInsideMaxBounds: /* @__PURE__ */ __name(function() {
          if (!this._enforcingBounds) {
            this.panInsideBounds(this.options.maxBounds);
          }
        }, "_panInsideMaxBounds"),
        _checkIfLoaded: /* @__PURE__ */ __name(function() {
          if (!this._loaded) {
            throw new Error("Set map center and zoom first.");
          }
        }, "_checkIfLoaded"),
        // DOM event handling
        // @section Interaction events
        _initEvents: /* @__PURE__ */ __name(function(remove2) {
          this._targets = {};
          this._targets[stamp(this._container)] = this;
          var onOff = remove2 ? off : on;
          onOff(this._container, "click dblclick mousedown mouseup mouseover mouseout mousemove contextmenu keypress keydown keyup", this._handleDOMEvent, this);
          if (this.options.trackResize) {
            onOff(window, "resize", this._onResize, this);
          }
          if (Browser.any3d && this.options.transform3DLimit) {
            (remove2 ? this.off : this.on).call(this, "moveend", this._onMoveEnd);
          }
        }, "_initEvents"),
        _onResize: /* @__PURE__ */ __name(function() {
          cancelAnimFrame(this._resizeRequest);
          this._resizeRequest = requestAnimFrame(
            function() {
              this.invalidateSize({ debounceMoveend: true });
            },
            this
          );
        }, "_onResize"),
        _onScroll: /* @__PURE__ */ __name(function() {
          this._container.scrollTop = 0;
          this._container.scrollLeft = 0;
        }, "_onScroll"),
        _onMoveEnd: /* @__PURE__ */ __name(function() {
          var pos = this._getMapPanePos();
          if (Math.max(Math.abs(pos.x), Math.abs(pos.y)) >= this.options.transform3DLimit) {
            this._resetView(this.getCenter(), this.getZoom());
          }
        }, "_onMoveEnd"),
        _findEventTargets: /* @__PURE__ */ __name(function(e, type) {
          var targets = [], target, isHover = type === "mouseout" || type === "mouseover", src = e.target || e.srcElement, dragging = false;
          while (src) {
            target = this._targets[stamp(src)];
            if (target && (type === "click" || type === "preclick") && this._draggableMoved(target)) {
              dragging = true;
              break;
            }
            if (target && target.listens(type, true)) {
              if (isHover && !isExternalTarget(src, e)) {
                break;
              }
              targets.push(target);
              if (isHover) {
                break;
              }
            }
            if (src === this._container) {
              break;
            }
            src = src.parentNode;
          }
          if (!targets.length && !dragging && !isHover && this.listens(type, true)) {
            targets = [this];
          }
          return targets;
        }, "_findEventTargets"),
        _isClickDisabled: /* @__PURE__ */ __name(function(el) {
          while (el && el !== this._container) {
            if (el["_leaflet_disable_click"]) {
              return true;
            }
            el = el.parentNode;
          }
        }, "_isClickDisabled"),
        _handleDOMEvent: /* @__PURE__ */ __name(function(e) {
          var el = e.target || e.srcElement;
          if (!this._loaded || el["_leaflet_disable_events"] || e.type === "click" && this._isClickDisabled(el)) {
            return;
          }
          var type = e.type;
          if (type === "mousedown") {
            preventOutline(el);
          }
          this._fireDOMEvent(e, type);
        }, "_handleDOMEvent"),
        _mouseEvents: ["click", "dblclick", "mouseover", "mouseout", "contextmenu"],
        _fireDOMEvent: /* @__PURE__ */ __name(function(e, type, canvasTargets) {
          if (e.type === "click") {
            var synth = extend({}, e);
            synth.type = "preclick";
            this._fireDOMEvent(synth, synth.type, canvasTargets);
          }
          var targets = this._findEventTargets(e, type);
          if (canvasTargets) {
            var filtered = [];
            for (var i = 0; i < canvasTargets.length; i++) {
              if (canvasTargets[i].listens(type, true)) {
                filtered.push(canvasTargets[i]);
              }
            }
            targets = filtered.concat(targets);
          }
          if (!targets.length) {
            return;
          }
          if (type === "contextmenu") {
            preventDefault(e);
          }
          var target = targets[0];
          var data = {
            originalEvent: e
          };
          if (e.type !== "keypress" && e.type !== "keydown" && e.type !== "keyup") {
            var isMarker = target.getLatLng && (!target._radius || target._radius <= 10);
            data.containerPoint = isMarker ? this.latLngToContainerPoint(target.getLatLng()) : this.mouseEventToContainerPoint(e);
            data.layerPoint = this.containerPointToLayerPoint(data.containerPoint);
            data.latlng = isMarker ? target.getLatLng() : this.layerPointToLatLng(data.layerPoint);
          }
          for (i = 0; i < targets.length; i++) {
            targets[i].fire(type, data, true);
            if (data.originalEvent._stopped || targets[i].options.bubblingMouseEvents === false && indexOf(this._mouseEvents, type) !== -1) {
              return;
            }
          }
        }, "_fireDOMEvent"),
        _draggableMoved: /* @__PURE__ */ __name(function(obj) {
          obj = obj.dragging && obj.dragging.enabled() ? obj : this;
          return obj.dragging && obj.dragging.moved() || this.boxZoom && this.boxZoom.moved();
        }, "_draggableMoved"),
        _clearHandlers: /* @__PURE__ */ __name(function() {
          for (var i = 0, len = this._handlers.length; i < len; i++) {
            this._handlers[i].disable();
          }
        }, "_clearHandlers"),
        // @section Other Methods
        // @method whenReady(fn: Function, context?: Object): this
        // Runs the given function `fn` when the map gets initialized with
        // a view (center and zoom) and at least one layer, or immediately
        // if it's already initialized, optionally passing a function context.
        whenReady: /* @__PURE__ */ __name(function(callback, context) {
          if (this._loaded) {
            callback.call(context || this, { target: this });
          } else {
            this.on("load", callback, context);
          }
          return this;
        }, "whenReady"),
        // private methods for getting map state
        _getMapPanePos: /* @__PURE__ */ __name(function() {
          return getPosition(this._mapPane) || new Point(0, 0);
        }, "_getMapPanePos"),
        _moved: /* @__PURE__ */ __name(function() {
          var pos = this._getMapPanePos();
          return pos && !pos.equals([0, 0]);
        }, "_moved"),
        _getTopLeftPoint: /* @__PURE__ */ __name(function(center, zoom2) {
          var pixelOrigin = center && zoom2 !== void 0 ? this._getNewPixelOrigin(center, zoom2) : this.getPixelOrigin();
          return pixelOrigin.subtract(this._getMapPanePos());
        }, "_getTopLeftPoint"),
        _getNewPixelOrigin: /* @__PURE__ */ __name(function(center, zoom2) {
          var viewHalf = this.getSize()._divideBy(2);
          return this.project(center, zoom2)._subtract(viewHalf)._add(this._getMapPanePos())._round();
        }, "_getNewPixelOrigin"),
        _latLngToNewLayerPoint: /* @__PURE__ */ __name(function(latlng, zoom2, center) {
          var topLeft = this._getNewPixelOrigin(center, zoom2);
          return this.project(latlng, zoom2)._subtract(topLeft);
        }, "_latLngToNewLayerPoint"),
        _latLngBoundsToNewLayerBounds: /* @__PURE__ */ __name(function(latLngBounds, zoom2, center) {
          var topLeft = this._getNewPixelOrigin(center, zoom2);
          return toBounds([
            this.project(latLngBounds.getSouthWest(), zoom2)._subtract(topLeft),
            this.project(latLngBounds.getNorthWest(), zoom2)._subtract(topLeft),
            this.project(latLngBounds.getSouthEast(), zoom2)._subtract(topLeft),
            this.project(latLngBounds.getNorthEast(), zoom2)._subtract(topLeft)
          ]);
        }, "_latLngBoundsToNewLayerBounds"),
        // layer point of the current center
        _getCenterLayerPoint: /* @__PURE__ */ __name(function() {
          return this.containerPointToLayerPoint(this.getSize()._divideBy(2));
        }, "_getCenterLayerPoint"),
        // offset of the specified place to the current center in pixels
        _getCenterOffset: /* @__PURE__ */ __name(function(latlng) {
          return this.latLngToLayerPoint(latlng).subtract(this._getCenterLayerPoint());
        }, "_getCenterOffset"),
        // adjust center for view to get inside bounds
        _limitCenter: /* @__PURE__ */ __name(function(center, zoom2, bounds) {
          if (!bounds) {
            return center;
          }
          var centerPoint = this.project(center, zoom2), viewHalf = this.getSize().divideBy(2), viewBounds = new Bounds(centerPoint.subtract(viewHalf), centerPoint.add(viewHalf)), offset = this._getBoundsOffset(viewBounds, bounds, zoom2);
          if (Math.abs(offset.x) <= 1 && Math.abs(offset.y) <= 1) {
            return center;
          }
          return this.unproject(centerPoint.add(offset), zoom2);
        }, "_limitCenter"),
        // adjust offset for view to get inside bounds
        _limitOffset: /* @__PURE__ */ __name(function(offset, bounds) {
          if (!bounds) {
            return offset;
          }
          var viewBounds = this.getPixelBounds(), newBounds = new Bounds(viewBounds.min.add(offset), viewBounds.max.add(offset));
          return offset.add(this._getBoundsOffset(newBounds, bounds));
        }, "_limitOffset"),
        // returns offset needed for pxBounds to get inside maxBounds at a specified zoom
        _getBoundsOffset: /* @__PURE__ */ __name(function(pxBounds, maxBounds, zoom2) {
          var projectedMaxBounds = toBounds(
            this.project(maxBounds.getNorthEast(), zoom2),
            this.project(maxBounds.getSouthWest(), zoom2)
          ), minOffset = projectedMaxBounds.min.subtract(pxBounds.min), maxOffset = projectedMaxBounds.max.subtract(pxBounds.max), dx = this._rebound(minOffset.x, -maxOffset.x), dy = this._rebound(minOffset.y, -maxOffset.y);
          return new Point(dx, dy);
        }, "_getBoundsOffset"),
        _rebound: /* @__PURE__ */ __name(function(left, right) {
          return left + right > 0 ? Math.round(left - right) / 2 : Math.max(0, Math.ceil(left)) - Math.max(0, Math.floor(right));
        }, "_rebound"),
        _limitZoom: /* @__PURE__ */ __name(function(zoom2) {
          var min = this.getMinZoom(), max = this.getMaxZoom(), snap = Browser.any3d ? this.options.zoomSnap : 1;
          if (snap) {
            zoom2 = Math.round(zoom2 / snap) * snap;
          }
          return Math.max(min, Math.min(max, zoom2));
        }, "_limitZoom"),
        _onPanTransitionStep: /* @__PURE__ */ __name(function() {
          this.fire("move");
        }, "_onPanTransitionStep"),
        _onPanTransitionEnd: /* @__PURE__ */ __name(function() {
          removeClass(this._mapPane, "leaflet-pan-anim");
          this.fire("moveend");
        }, "_onPanTransitionEnd"),
        _tryAnimatedPan: /* @__PURE__ */ __name(function(center, options) {
          var offset = this._getCenterOffset(center)._trunc();
          if ((options && options.animate) !== true && !this.getSize().contains(offset)) {
            return false;
          }
          this.panBy(offset, options);
          return true;
        }, "_tryAnimatedPan"),
        _createAnimProxy: /* @__PURE__ */ __name(function() {
          var proxy = this._proxy = create$1("div", "leaflet-proxy leaflet-zoom-animated");
          this._panes.mapPane.appendChild(proxy);
          this.on("zoomanim", function(e) {
            var prop = TRANSFORM, transform = this._proxy.style[prop];
            setTransform(this._proxy, this.project(e.center, e.zoom), this.getZoomScale(e.zoom, 1));
            if (transform === this._proxy.style[prop] && this._animatingZoom) {
              this._onZoomTransitionEnd();
            }
          }, this);
          this.on("load moveend", this._animMoveEnd, this);
          this._on("unload", this._destroyAnimProxy, this);
        }, "_createAnimProxy"),
        _destroyAnimProxy: /* @__PURE__ */ __name(function() {
          remove(this._proxy);
          this.off("load moveend", this._animMoveEnd, this);
          delete this._proxy;
        }, "_destroyAnimProxy"),
        _animMoveEnd: /* @__PURE__ */ __name(function() {
          var c = this.getCenter(), z = this.getZoom();
          setTransform(this._proxy, this.project(c, z), this.getZoomScale(z, 1));
        }, "_animMoveEnd"),
        _catchTransitionEnd: /* @__PURE__ */ __name(function(e) {
          if (this._animatingZoom && e.propertyName.indexOf("transform") >= 0) {
            this._onZoomTransitionEnd();
          }
        }, "_catchTransitionEnd"),
        _nothingToAnimate: /* @__PURE__ */ __name(function() {
          return !this._container.getElementsByClassName("leaflet-zoom-animated").length;
        }, "_nothingToAnimate"),
        _tryAnimatedZoom: /* @__PURE__ */ __name(function(center, zoom2, options) {
          if (this._animatingZoom) {
            return true;
          }
          options = options || {};
          if (!this._zoomAnimated || options.animate === false || this._nothingToAnimate() || Math.abs(zoom2 - this._zoom) > this.options.zoomAnimationThreshold) {
            return false;
          }
          var scale2 = this.getZoomScale(zoom2), offset = this._getCenterOffset(center)._divideBy(1 - 1 / scale2);
          if (options.animate !== true && !this.getSize().contains(offset)) {
            return false;
          }
          requestAnimFrame(function() {
            this._moveStart(true, options.noMoveStart || false)._animateZoom(center, zoom2, true);
          }, this);
          return true;
        }, "_tryAnimatedZoom"),
        _animateZoom: /* @__PURE__ */ __name(function(center, zoom2, startAnim, noUpdate) {
          if (!this._mapPane) {
            return;
          }
          if (startAnim) {
            this._animatingZoom = true;
            this._animateToCenter = center;
            this._animateToZoom = zoom2;
            addClass(this._mapPane, "leaflet-zoom-anim");
          }
          this.fire("zoomanim", {
            center,
            zoom: zoom2,
            noUpdate
          });
          if (!this._tempFireZoomEvent) {
            this._tempFireZoomEvent = this._zoom !== this._animateToZoom;
          }
          this._move(this._animateToCenter, this._animateToZoom, void 0, true);
          setTimeout(bind(this._onZoomTransitionEnd, this), 250);
        }, "_animateZoom"),
        _onZoomTransitionEnd: /* @__PURE__ */ __name(function() {
          if (!this._animatingZoom) {
            return;
          }
          if (this._mapPane) {
            removeClass(this._mapPane, "leaflet-zoom-anim");
          }
          this._animatingZoom = false;
          this._move(this._animateToCenter, this._animateToZoom, void 0, true);
          if (this._tempFireZoomEvent) {
            this.fire("zoom");
          }
          delete this._tempFireZoomEvent;
          this.fire("move");
          this._moveEnd(true);
        }, "_onZoomTransitionEnd")
      });
      function createMap2(id, options) {
        return new Map(id, options);
      }
      __name(createMap2, "createMap");
      var Control = Class.extend({
        // @section
        // @aka Control Options
        options: {
          // @option position: String = 'topright'
          // The position of the control (one of the map corners). Possible values are `'topleft'`,
          // `'topright'`, `'bottomleft'` or `'bottomright'`
          position: "topright"
        },
        initialize: /* @__PURE__ */ __name(function(options) {
          setOptions(this, options);
        }, "initialize"),
        /* @section
         * Classes extending L.Control will inherit the following methods:
         *
         * @method getPosition: string
         * Returns the position of the control.
         */
        getPosition: /* @__PURE__ */ __name(function() {
          return this.options.position;
        }, "getPosition"),
        // @method setPosition(position: string): this
        // Sets the position of the control.
        setPosition: /* @__PURE__ */ __name(function(position) {
          var map2 = this._map;
          if (map2) {
            map2.removeControl(this);
          }
          this.options.position = position;
          if (map2) {
            map2.addControl(this);
          }
          return this;
        }, "setPosition"),
        // @method getContainer: HTMLElement
        // Returns the HTMLElement that contains the control.
        getContainer: /* @__PURE__ */ __name(function() {
          return this._container;
        }, "getContainer"),
        // @method addTo(map: Map): this
        // Adds the control to the given map.
        addTo: /* @__PURE__ */ __name(function(map2) {
          this.remove();
          this._map = map2;
          var container = this._container = this.onAdd(map2), pos = this.getPosition(), corner = map2._controlCorners[pos];
          addClass(container, "leaflet-control");
          if (pos.indexOf("bottom") !== -1) {
            corner.insertBefore(container, corner.firstChild);
          } else {
            corner.appendChild(container);
          }
          this._map.on("unload", this.remove, this);
          return this;
        }, "addTo"),
        // @method remove: this
        // Removes the control from the map it is currently active on.
        remove: /* @__PURE__ */ __name(function() {
          if (!this._map) {
            return this;
          }
          remove(this._container);
          if (this.onRemove) {
            this.onRemove(this._map);
          }
          this._map.off("unload", this.remove, this);
          this._map = null;
          return this;
        }, "remove"),
        _refocusOnMap: /* @__PURE__ */ __name(function(e) {
          if (this._map && e && e.screenX > 0 && e.screenY > 0) {
            this._map.getContainer().focus();
          }
        }, "_refocusOnMap")
      });
      var control = /* @__PURE__ */ __name(function(options) {
        return new Control(options);
      }, "control");
      Map.include({
        // @method addControl(control: Control): this
        // Adds the given control to the map
        addControl: /* @__PURE__ */ __name(function(control2) {
          control2.addTo(this);
          return this;
        }, "addControl"),
        // @method removeControl(control: Control): this
        // Removes the given control from the map
        removeControl: /* @__PURE__ */ __name(function(control2) {
          control2.remove();
          return this;
        }, "removeControl"),
        _initControlPos: /* @__PURE__ */ __name(function() {
          var corners = this._controlCorners = {}, l = "leaflet-", container = this._controlContainer = create$1("div", l + "control-container", this._container);
          function createCorner(vSide, hSide) {
            var className = l + vSide + " " + l + hSide;
            corners[vSide + hSide] = create$1("div", className, container);
          }
          __name(createCorner, "createCorner");
          createCorner("top", "left");
          createCorner("top", "right");
          createCorner("bottom", "left");
          createCorner("bottom", "right");
        }, "_initControlPos"),
        _clearControlPos: /* @__PURE__ */ __name(function() {
          for (var i in this._controlCorners) {
            remove(this._controlCorners[i]);
          }
          remove(this._controlContainer);
          delete this._controlCorners;
          delete this._controlContainer;
        }, "_clearControlPos")
      });
      var Layers = Control.extend({
        // @section
        // @aka Control.Layers options
        options: {
          // @option collapsed: Boolean = true
          // If `true`, the control will be collapsed into an icon and expanded on mouse hover, touch, or keyboard activation.
          collapsed: true,
          position: "topright",
          // @option autoZIndex: Boolean = true
          // If `true`, the control will assign zIndexes in increasing order to all of its layers so that the order is preserved when switching them on/off.
          autoZIndex: true,
          // @option hideSingleBase: Boolean = false
          // If `true`, the base layers in the control will be hidden when there is only one.
          hideSingleBase: false,
          // @option sortLayers: Boolean = false
          // Whether to sort the layers. When `false`, layers will keep the order
          // in which they were added to the control.
          sortLayers: false,
          // @option sortFunction: Function = *
          // A [compare function](https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Array/sort)
          // that will be used for sorting the layers, when `sortLayers` is `true`.
          // The function receives both the `L.Layer` instances and their names, as in
          // `sortFunction(layerA, layerB, nameA, nameB)`.
          // By default, it sorts layers alphabetically by their name.
          sortFunction: /* @__PURE__ */ __name(function(layerA, layerB, nameA, nameB) {
            return nameA < nameB ? -1 : nameB < nameA ? 1 : 0;
          }, "sortFunction")
        },
        initialize: /* @__PURE__ */ __name(function(baseLayers, overlays, options) {
          setOptions(this, options);
          this._layerControlInputs = [];
          this._layers = [];
          this._lastZIndex = 0;
          this._handlingClick = false;
          this._preventClick = false;
          for (var i in baseLayers) {
            this._addLayer(baseLayers[i], i);
          }
          for (i in overlays) {
            this._addLayer(overlays[i], i, true);
          }
        }, "initialize"),
        onAdd: /* @__PURE__ */ __name(function(map2) {
          this._initLayout();
          this._update();
          this._map = map2;
          map2.on("zoomend", this._checkDisabledLayers, this);
          for (var i = 0; i < this._layers.length; i++) {
            this._layers[i].layer.on("add remove", this._onLayerChange, this);
          }
          return this._container;
        }, "onAdd"),
        addTo: /* @__PURE__ */ __name(function(map2) {
          Control.prototype.addTo.call(this, map2);
          return this._expandIfNotCollapsed();
        }, "addTo"),
        onRemove: /* @__PURE__ */ __name(function() {
          this._map.off("zoomend", this._checkDisabledLayers, this);
          for (var i = 0; i < this._layers.length; i++) {
            this._layers[i].layer.off("add remove", this._onLayerChange, this);
          }
        }, "onRemove"),
        // @method addBaseLayer(layer: Layer, name: String): this
        // Adds a base layer (radio button entry) with the given name to the control.
        addBaseLayer: /* @__PURE__ */ __name(function(layer, name) {
          this._addLayer(layer, name);
          return this._map ? this._update() : this;
        }, "addBaseLayer"),
        // @method addOverlay(layer: Layer, name: String): this
        // Adds an overlay (checkbox entry) with the given name to the control.
        addOverlay: /* @__PURE__ */ __name(function(layer, name) {
          this._addLayer(layer, name, true);
          return this._map ? this._update() : this;
        }, "addOverlay"),
        // @method removeLayer(layer: Layer): this
        // Remove the given layer from the control.
        removeLayer: /* @__PURE__ */ __name(function(layer) {
          layer.off("add remove", this._onLayerChange, this);
          var obj = this._getLayer(stamp(layer));
          if (obj) {
            this._layers.splice(this._layers.indexOf(obj), 1);
          }
          return this._map ? this._update() : this;
        }, "removeLayer"),
        // @method expand(): this
        // Expand the control container if collapsed.
        expand: /* @__PURE__ */ __name(function() {
          addClass(this._container, "leaflet-control-layers-expanded");
          this._section.style.height = null;
          var acceptableHeight = this._map.getSize().y - (this._container.offsetTop + 50);
          if (acceptableHeight < this._section.clientHeight) {
            addClass(this._section, "leaflet-control-layers-scrollbar");
            this._section.style.height = acceptableHeight + "px";
          } else {
            removeClass(this._section, "leaflet-control-layers-scrollbar");
          }
          this._checkDisabledLayers();
          return this;
        }, "expand"),
        // @method collapse(): this
        // Collapse the control container if expanded.
        collapse: /* @__PURE__ */ __name(function() {
          removeClass(this._container, "leaflet-control-layers-expanded");
          return this;
        }, "collapse"),
        _initLayout: /* @__PURE__ */ __name(function() {
          var className = "leaflet-control-layers", container = this._container = create$1("div", className), collapsed = this.options.collapsed;
          container.setAttribute("aria-haspopup", true);
          disableClickPropagation(container);
          disableScrollPropagation(container);
          var section = this._section = create$1("section", className + "-list");
          if (collapsed) {
            this._map.on("click", this.collapse, this);
            on(container, {
              mouseenter: this._expandSafely,
              mouseleave: this.collapse
            }, this);
          }
          var link = this._layersLink = create$1("a", className + "-toggle", container);
          link.href = "#";
          link.title = "Layers";
          link.setAttribute("role", "button");
          on(link, {
            keydown: /* @__PURE__ */ __name(function(e) {
              if (e.keyCode === 13) {
                this._expandSafely();
              }
            }, "keydown"),
            // Certain screen readers intercept the key event and instead send a click event
            click: /* @__PURE__ */ __name(function(e) {
              preventDefault(e);
              this._expandSafely();
            }, "click")
          }, this);
          if (!collapsed) {
            this.expand();
          }
          this._baseLayersList = create$1("div", className + "-base", section);
          this._separator = create$1("div", className + "-separator", section);
          this._overlaysList = create$1("div", className + "-overlays", section);
          container.appendChild(section);
        }, "_initLayout"),
        _getLayer: /* @__PURE__ */ __name(function(id) {
          for (var i = 0; i < this._layers.length; i++) {
            if (this._layers[i] && stamp(this._layers[i].layer) === id) {
              return this._layers[i];
            }
          }
        }, "_getLayer"),
        _addLayer: /* @__PURE__ */ __name(function(layer, name, overlay) {
          if (this._map) {
            layer.on("add remove", this._onLayerChange, this);
          }
          this._layers.push({
            layer,
            name,
            overlay
          });
          if (this.options.sortLayers) {
            this._layers.sort(bind(function(a, b) {
              return this.options.sortFunction(a.layer, b.layer, a.name, b.name);
            }, this));
          }
          if (this.options.autoZIndex && layer.setZIndex) {
            this._lastZIndex++;
            layer.setZIndex(this._lastZIndex);
          }
          this._expandIfNotCollapsed();
        }, "_addLayer"),
        _update: /* @__PURE__ */ __name(function() {
          if (!this._container) {
            return this;
          }
          empty(this._baseLayersList);
          empty(this._overlaysList);
          this._layerControlInputs = [];
          var baseLayersPresent, overlaysPresent, i, obj, baseLayersCount = 0;
          for (i = 0; i < this._layers.length; i++) {
            obj = this._layers[i];
            this._addItem(obj);
            overlaysPresent = overlaysPresent || obj.overlay;
            baseLayersPresent = baseLayersPresent || !obj.overlay;
            baseLayersCount += !obj.overlay ? 1 : 0;
          }
          if (this.options.hideSingleBase) {
            baseLayersPresent = baseLayersPresent && baseLayersCount > 1;
            this._baseLayersList.style.display = baseLayersPresent ? "" : "none";
          }
          this._separator.style.display = overlaysPresent && baseLayersPresent ? "" : "none";
          return this;
        }, "_update"),
        _onLayerChange: /* @__PURE__ */ __name(function(e) {
          if (!this._handlingClick) {
            this._update();
          }
          var obj = this._getLayer(stamp(e.target));
          var type = obj.overlay ? e.type === "add" ? "overlayadd" : "overlayremove" : e.type === "add" ? "baselayerchange" : null;
          if (type) {
            this._map.fire(type, obj);
          }
        }, "_onLayerChange"),
        // IE7 bugs out if you create a radio dynamically, so you have to do it this hacky way (see https://stackoverflow.com/a/119079)
        _createRadioElement: /* @__PURE__ */ __name(function(name, checked) {
          var radioHtml = '<input type="radio" class="leaflet-control-layers-selector" name="' + name + '"' + (checked ? ' checked="checked"' : "") + "/>";
          var radioFragment = document.createElement("div");
          radioFragment.innerHTML = radioHtml;
          return radioFragment.firstChild;
        }, "_createRadioElement"),
        _addItem: /* @__PURE__ */ __name(function(obj) {
          var label = document.createElement("label"), checked = this._map.hasLayer(obj.layer), input;
          if (obj.overlay) {
            input = document.createElement("input");
            input.type = "checkbox";
            input.className = "leaflet-control-layers-selector";
            input.defaultChecked = checked;
          } else {
            input = this._createRadioElement("leaflet-base-layers_" + stamp(this), checked);
          }
          this._layerControlInputs.push(input);
          input.layerId = stamp(obj.layer);
          on(input, "click", this._onInputClick, this);
          var name = document.createElement("span");
          name.innerHTML = " " + obj.name;
          var holder = document.createElement("span");
          label.appendChild(holder);
          holder.appendChild(input);
          holder.appendChild(name);
          var container = obj.overlay ? this._overlaysList : this._baseLayersList;
          container.appendChild(label);
          this._checkDisabledLayers();
          return label;
        }, "_addItem"),
        _onInputClick: /* @__PURE__ */ __name(function() {
          if (this._preventClick) {
            return;
          }
          var inputs = this._layerControlInputs, input, layer;
          var addedLayers = [], removedLayers = [];
          this._handlingClick = true;
          for (var i = inputs.length - 1; i >= 0; i--) {
            input = inputs[i];
            layer = this._getLayer(input.layerId).layer;
            if (input.checked) {
              addedLayers.push(layer);
            } else if (!input.checked) {
              removedLayers.push(layer);
            }
          }
          for (i = 0; i < removedLayers.length; i++) {
            if (this._map.hasLayer(removedLayers[i])) {
              this._map.removeLayer(removedLayers[i]);
            }
          }
          for (i = 0; i < addedLayers.length; i++) {
            if (!this._map.hasLayer(addedLayers[i])) {
              this._map.addLayer(addedLayers[i]);
            }
          }
          this._handlingClick = false;
          this._refocusOnMap();
        }, "_onInputClick"),
        _checkDisabledLayers: /* @__PURE__ */ __name(function() {
          var inputs = this._layerControlInputs, input, layer, zoom2 = this._map.getZoom();
          for (var i = inputs.length - 1; i >= 0; i--) {
            input = inputs[i];
            layer = this._getLayer(input.layerId).layer;
            input.disabled = layer.options.minZoom !== void 0 && zoom2 < layer.options.minZoom || layer.options.maxZoom !== void 0 && zoom2 > layer.options.maxZoom;
          }
        }, "_checkDisabledLayers"),
        _expandIfNotCollapsed: /* @__PURE__ */ __name(function() {
          if (this._map && !this.options.collapsed) {
            this.expand();
          }
          return this;
        }, "_expandIfNotCollapsed"),
        _expandSafely: /* @__PURE__ */ __name(function() {
          var section = this._section;
          this._preventClick = true;
          on(section, "click", preventDefault);
          this.expand();
          var that = this;
          setTimeout(function() {
            off(section, "click", preventDefault);
            that._preventClick = false;
          });
        }, "_expandSafely")
      });
      var layers = /* @__PURE__ */ __name(function(baseLayers, overlays, options) {
        return new Layers(baseLayers, overlays, options);
      }, "layers");
      var Zoom = Control.extend({
        // @section
        // @aka Control.Zoom options
        options: {
          position: "topleft",
          // @option zoomInText: String = '<span aria-hidden="true">+</span>'
          // The text set on the 'zoom in' button.
          zoomInText: '<span aria-hidden="true">+</span>',
          // @option zoomInTitle: String = 'Zoom in'
          // The title set on the 'zoom in' button.
          zoomInTitle: "Zoom in",
          // @option zoomOutText: String = '<span aria-hidden="true">&#x2212;</span>'
          // The text set on the 'zoom out' button.
          zoomOutText: '<span aria-hidden="true">&#x2212;</span>',
          // @option zoomOutTitle: String = 'Zoom out'
          // The title set on the 'zoom out' button.
          zoomOutTitle: "Zoom out"
        },
        onAdd: /* @__PURE__ */ __name(function(map2) {
          var zoomName = "leaflet-control-zoom", container = create$1("div", zoomName + " leaflet-bar"), options = this.options;
          this._zoomInButton = this._createButton(
            options.zoomInText,
            options.zoomInTitle,
            zoomName + "-in",
            container,
            this._zoomIn
          );
          this._zoomOutButton = this._createButton(
            options.zoomOutText,
            options.zoomOutTitle,
            zoomName + "-out",
            container,
            this._zoomOut
          );
          this._updateDisabled();
          map2.on("zoomend zoomlevelschange", this._updateDisabled, this);
          return container;
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function(map2) {
          map2.off("zoomend zoomlevelschange", this._updateDisabled, this);
        }, "onRemove"),
        disable: /* @__PURE__ */ __name(function() {
          this._disabled = true;
          this._updateDisabled();
          return this;
        }, "disable"),
        enable: /* @__PURE__ */ __name(function() {
          this._disabled = false;
          this._updateDisabled();
          return this;
        }, "enable"),
        _zoomIn: /* @__PURE__ */ __name(function(e) {
          if (!this._disabled && this._map._zoom < this._map.getMaxZoom()) {
            this._map.zoomIn(this._map.options.zoomDelta * (e.shiftKey ? 3 : 1));
          }
        }, "_zoomIn"),
        _zoomOut: /* @__PURE__ */ __name(function(e) {
          if (!this._disabled && this._map._zoom > this._map.getMinZoom()) {
            this._map.zoomOut(this._map.options.zoomDelta * (e.shiftKey ? 3 : 1));
          }
        }, "_zoomOut"),
        _createButton: /* @__PURE__ */ __name(function(html, title, className, container, fn) {
          var link = create$1("a", className, container);
          link.innerHTML = html;
          link.href = "#";
          link.title = title;
          link.setAttribute("role", "button");
          link.setAttribute("aria-label", title);
          disableClickPropagation(link);
          on(link, "click", stop);
          on(link, "click", fn, this);
          on(link, "click", this._refocusOnMap, this);
          return link;
        }, "_createButton"),
        _updateDisabled: /* @__PURE__ */ __name(function() {
          var map2 = this._map, className = "leaflet-disabled";
          removeClass(this._zoomInButton, className);
          removeClass(this._zoomOutButton, className);
          this._zoomInButton.setAttribute("aria-disabled", "false");
          this._zoomOutButton.setAttribute("aria-disabled", "false");
          if (this._disabled || map2._zoom === map2.getMinZoom()) {
            addClass(this._zoomOutButton, className);
            this._zoomOutButton.setAttribute("aria-disabled", "true");
          }
          if (this._disabled || map2._zoom === map2.getMaxZoom()) {
            addClass(this._zoomInButton, className);
            this._zoomInButton.setAttribute("aria-disabled", "true");
          }
        }, "_updateDisabled")
      });
      Map.mergeOptions({
        zoomControl: true
      });
      Map.addInitHook(function() {
        if (this.options.zoomControl) {
          this.zoomControl = new Zoom();
          this.addControl(this.zoomControl);
        }
      });
      var zoom = /* @__PURE__ */ __name(function(options) {
        return new Zoom(options);
      }, "zoom");
      var Scale = Control.extend({
        // @section
        // @aka Control.Scale options
        options: {
          position: "bottomleft",
          // @option maxWidth: Number = 100
          // Maximum width of the control in pixels. The width is set dynamically to show round values (e.g. 100, 200, 500).
          maxWidth: 100,
          // @option metric: Boolean = True
          // Whether to show the metric scale line (m/km).
          metric: true,
          // @option imperial: Boolean = True
          // Whether to show the imperial scale line (mi/ft).
          imperial: true
          // @option updateWhenIdle: Boolean = false
          // If `true`, the control is updated on [`moveend`](#map-moveend), otherwise it's always up-to-date (updated on [`move`](#map-move)).
        },
        onAdd: /* @__PURE__ */ __name(function(map2) {
          var className = "leaflet-control-scale", container = create$1("div", className), options = this.options;
          this._addScales(options, className + "-line", container);
          map2.on(options.updateWhenIdle ? "moveend" : "move", this._update, this);
          map2.whenReady(this._update, this);
          return container;
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function(map2) {
          map2.off(this.options.updateWhenIdle ? "moveend" : "move", this._update, this);
        }, "onRemove"),
        _addScales: /* @__PURE__ */ __name(function(options, className, container) {
          if (options.metric) {
            this._mScale = create$1("div", className, container);
          }
          if (options.imperial) {
            this._iScale = create$1("div", className, container);
          }
        }, "_addScales"),
        _update: /* @__PURE__ */ __name(function() {
          var map2 = this._map, y = map2.getSize().y / 2;
          var maxMeters = map2.distance(
            map2.containerPointToLatLng([0, y]),
            map2.containerPointToLatLng([this.options.maxWidth, y])
          );
          this._updateScales(maxMeters);
        }, "_update"),
        _updateScales: /* @__PURE__ */ __name(function(maxMeters) {
          if (this.options.metric && maxMeters) {
            this._updateMetric(maxMeters);
          }
          if (this.options.imperial && maxMeters) {
            this._updateImperial(maxMeters);
          }
        }, "_updateScales"),
        _updateMetric: /* @__PURE__ */ __name(function(maxMeters) {
          var meters = this._getRoundNum(maxMeters), label = meters < 1e3 ? meters + " m" : meters / 1e3 + " km";
          this._updateScale(this._mScale, label, meters / maxMeters);
        }, "_updateMetric"),
        _updateImperial: /* @__PURE__ */ __name(function(maxMeters) {
          var maxFeet = maxMeters * 3.2808399, maxMiles, miles, feet;
          if (maxFeet > 5280) {
            maxMiles = maxFeet / 5280;
            miles = this._getRoundNum(maxMiles);
            this._updateScale(this._iScale, miles + " mi", miles / maxMiles);
          } else {
            feet = this._getRoundNum(maxFeet);
            this._updateScale(this._iScale, feet + " ft", feet / maxFeet);
          }
        }, "_updateImperial"),
        _updateScale: /* @__PURE__ */ __name(function(scale2, text, ratio) {
          scale2.style.width = Math.round(this.options.maxWidth * ratio) + "px";
          scale2.innerHTML = text;
        }, "_updateScale"),
        _getRoundNum: /* @__PURE__ */ __name(function(num) {
          var pow10 = Math.pow(10, (Math.floor(num) + "").length - 1), d = num / pow10;
          d = d >= 10 ? 10 : d >= 5 ? 5 : d >= 3 ? 3 : d >= 2 ? 2 : 1;
          return pow10 * d;
        }, "_getRoundNum")
      });
      var scale = /* @__PURE__ */ __name(function(options) {
        return new Scale(options);
      }, "scale");
      var ukrainianFlag = '<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" class="leaflet-attribution-flag"><path fill="#4C7BE1" d="M0 0h12v4H0z"/><path fill="#FFD500" d="M0 4h12v3H0z"/><path fill="#E0BC00" d="M0 7h12v1H0z"/></svg>';
      var Attribution = Control.extend({
        // @section
        // @aka Control.Attribution options
        options: {
          position: "bottomright",
          // @option prefix: String|false = 'Leaflet'
          // The HTML text shown before the attributions. Pass `false` to disable.
          prefix: '<a href="https://leafletjs.com" title="A JavaScript library for interactive maps">' + (Browser.inlineSvg ? ukrainianFlag + " " : "") + "Leaflet</a>"
        },
        initialize: /* @__PURE__ */ __name(function(options) {
          setOptions(this, options);
          this._attributions = {};
        }, "initialize"),
        onAdd: /* @__PURE__ */ __name(function(map2) {
          map2.attributionControl = this;
          this._container = create$1("div", "leaflet-control-attribution");
          disableClickPropagation(this._container);
          for (var i in map2._layers) {
            if (map2._layers[i].getAttribution) {
              this.addAttribution(map2._layers[i].getAttribution());
            }
          }
          this._update();
          map2.on("layeradd", this._addAttribution, this);
          return this._container;
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function(map2) {
          map2.off("layeradd", this._addAttribution, this);
        }, "onRemove"),
        _addAttribution: /* @__PURE__ */ __name(function(ev) {
          if (ev.layer.getAttribution) {
            this.addAttribution(ev.layer.getAttribution());
            ev.layer.once("remove", function() {
              this.removeAttribution(ev.layer.getAttribution());
            }, this);
          }
        }, "_addAttribution"),
        // @method setPrefix(prefix: String|false): this
        // The HTML text shown before the attributions. Pass `false` to disable.
        setPrefix: /* @__PURE__ */ __name(function(prefix) {
          this.options.prefix = prefix;
          this._update();
          return this;
        }, "setPrefix"),
        // @method addAttribution(text: String): this
        // Adds an attribution text (e.g. `'&copy; OpenStreetMap contributors'`).
        addAttribution: /* @__PURE__ */ __name(function(text) {
          if (!text) {
            return this;
          }
          if (!this._attributions[text]) {
            this._attributions[text] = 0;
          }
          this._attributions[text]++;
          this._update();
          return this;
        }, "addAttribution"),
        // @method removeAttribution(text: String): this
        // Removes an attribution text.
        removeAttribution: /* @__PURE__ */ __name(function(text) {
          if (!text) {
            return this;
          }
          if (this._attributions[text]) {
            this._attributions[text]--;
            this._update();
          }
          return this;
        }, "removeAttribution"),
        _update: /* @__PURE__ */ __name(function() {
          if (!this._map) {
            return;
          }
          var attribs = [];
          for (var i in this._attributions) {
            if (this._attributions[i]) {
              attribs.push(i);
            }
          }
          var prefixAndAttribs = [];
          if (this.options.prefix) {
            prefixAndAttribs.push(this.options.prefix);
          }
          if (attribs.length) {
            prefixAndAttribs.push(attribs.join(", "));
          }
          this._container.innerHTML = prefixAndAttribs.join(' <span aria-hidden="true">|</span> ');
        }, "_update")
      });
      Map.mergeOptions({
        attributionControl: true
      });
      Map.addInitHook(function() {
        if (this.options.attributionControl) {
          new Attribution().addTo(this);
        }
      });
      var attribution2 = /* @__PURE__ */ __name(function(options) {
        return new Attribution(options);
      }, "attribution");
      Control.Layers = Layers;
      Control.Zoom = Zoom;
      Control.Scale = Scale;
      Control.Attribution = Attribution;
      control.layers = layers;
      control.zoom = zoom;
      control.scale = scale;
      control.attribution = attribution2;
      var Handler = Class.extend({
        initialize: /* @__PURE__ */ __name(function(map2) {
          this._map = map2;
        }, "initialize"),
        // @method enable(): this
        // Enables the handler
        enable: /* @__PURE__ */ __name(function() {
          if (this._enabled) {
            return this;
          }
          this._enabled = true;
          this.addHooks();
          return this;
        }, "enable"),
        // @method disable(): this
        // Disables the handler
        disable: /* @__PURE__ */ __name(function() {
          if (!this._enabled) {
            return this;
          }
          this._enabled = false;
          this.removeHooks();
          return this;
        }, "disable"),
        // @method enabled(): Boolean
        // Returns `true` if the handler is enabled
        enabled: /* @__PURE__ */ __name(function() {
          return !!this._enabled;
        }, "enabled")
        // @section Extension methods
        // Classes inheriting from `Handler` must implement the two following methods:
        // @method addHooks()
        // Called when the handler is enabled, should add event hooks.
        // @method removeHooks()
        // Called when the handler is disabled, should remove the event hooks added previously.
      });
      Handler.addTo = function(map2, name) {
        map2.addHandler(name, this);
        return this;
      };
      var Mixin = { Events };
      var START = Browser.touch ? "touchstart mousedown" : "mousedown";
      var Draggable = Evented.extend({
        options: {
          // @section
          // @aka Draggable options
          // @option clickTolerance: Number = 3
          // The max number of pixels a user can shift the mouse pointer during a click
          // for it to be considered a valid click (as opposed to a mouse drag).
          clickTolerance: 3
        },
        // @constructor L.Draggable(el: HTMLElement, dragHandle?: HTMLElement, preventOutline?: Boolean, options?: Draggable options)
        // Creates a `Draggable` object for moving `el` when you start dragging the `dragHandle` element (equals `el` itself by default).
        initialize: /* @__PURE__ */ __name(function(element, dragStartTarget, preventOutline2, options) {
          setOptions(this, options);
          this._element = element;
          this._dragStartTarget = dragStartTarget || element;
          this._preventOutline = preventOutline2;
        }, "initialize"),
        // @method enable()
        // Enables the dragging ability
        enable: /* @__PURE__ */ __name(function() {
          if (this._enabled) {
            return;
          }
          on(this._dragStartTarget, START, this._onDown, this);
          this._enabled = true;
        }, "enable"),
        // @method disable()
        // Disables the dragging ability
        disable: /* @__PURE__ */ __name(function() {
          if (!this._enabled) {
            return;
          }
          if (Draggable._dragging === this) {
            this.finishDrag(true);
          }
          off(this._dragStartTarget, START, this._onDown, this);
          this._enabled = false;
          this._moved = false;
        }, "disable"),
        _onDown: /* @__PURE__ */ __name(function(e) {
          if (!this._enabled) {
            return;
          }
          this._moved = false;
          if (hasClass(this._element, "leaflet-zoom-anim")) {
            return;
          }
          if (e.touches && e.touches.length !== 1) {
            if (Draggable._dragging === this) {
              this.finishDrag();
            }
            return;
          }
          if (Draggable._dragging || e.shiftKey || e.which !== 1 && e.button !== 1 && !e.touches) {
            return;
          }
          Draggable._dragging = this;
          if (this._preventOutline) {
            preventOutline(this._element);
          }
          disableImageDrag();
          disableTextSelection();
          if (this._moving) {
            return;
          }
          this.fire("down");
          var first = e.touches ? e.touches[0] : e, sizedParent = getSizedParentNode(this._element);
          this._startPoint = new Point(first.clientX, first.clientY);
          this._startPos = getPosition(this._element);
          this._parentScale = getScale(sizedParent);
          var mouseevent = e.type === "mousedown";
          on(document, mouseevent ? "mousemove" : "touchmove", this._onMove, this);
          on(document, mouseevent ? "mouseup" : "touchend touchcancel", this._onUp, this);
        }, "_onDown"),
        _onMove: /* @__PURE__ */ __name(function(e) {
          if (!this._enabled) {
            return;
          }
          if (e.touches && e.touches.length > 1) {
            this._moved = true;
            return;
          }
          var first = e.touches && e.touches.length === 1 ? e.touches[0] : e, offset = new Point(first.clientX, first.clientY)._subtract(this._startPoint);
          if (!offset.x && !offset.y) {
            return;
          }
          if (Math.abs(offset.x) + Math.abs(offset.y) < this.options.clickTolerance) {
            return;
          }
          offset.x /= this._parentScale.x;
          offset.y /= this._parentScale.y;
          preventDefault(e);
          if (!this._moved) {
            this.fire("dragstart");
            this._moved = true;
            addClass(document.body, "leaflet-dragging");
            this._lastTarget = e.target || e.srcElement;
            if (window.SVGElementInstance && this._lastTarget instanceof window.SVGElementInstance) {
              this._lastTarget = this._lastTarget.correspondingUseElement;
            }
            addClass(this._lastTarget, "leaflet-drag-target");
          }
          this._newPos = this._startPos.add(offset);
          this._moving = true;
          this._lastEvent = e;
          this._updatePosition();
        }, "_onMove"),
        _updatePosition: /* @__PURE__ */ __name(function() {
          var e = { originalEvent: this._lastEvent };
          this.fire("predrag", e);
          setPosition(this._element, this._newPos);
          this.fire("drag", e);
        }, "_updatePosition"),
        _onUp: /* @__PURE__ */ __name(function() {
          if (!this._enabled) {
            return;
          }
          this.finishDrag();
        }, "_onUp"),
        finishDrag: /* @__PURE__ */ __name(function(noInertia) {
          removeClass(document.body, "leaflet-dragging");
          if (this._lastTarget) {
            removeClass(this._lastTarget, "leaflet-drag-target");
            this._lastTarget = null;
          }
          off(document, "mousemove touchmove", this._onMove, this);
          off(document, "mouseup touchend touchcancel", this._onUp, this);
          enableImageDrag();
          enableTextSelection();
          var fireDragend = this._moved && this._moving;
          this._moving = false;
          Draggable._dragging = false;
          if (fireDragend) {
            this.fire("dragend", {
              noInertia,
              distance: this._newPos.distanceTo(this._startPos)
            });
          }
        }, "finishDrag")
      });
      function clipPolygon(points, bounds, round) {
        var clippedPoints, edges = [1, 4, 2, 8], i, j, k, a, b, len, edge2, p;
        for (i = 0, len = points.length; i < len; i++) {
          points[i]._code = _getBitCode(points[i], bounds);
        }
        for (k = 0; k < 4; k++) {
          edge2 = edges[k];
          clippedPoints = [];
          for (i = 0, len = points.length, j = len - 1; i < len; j = i++) {
            a = points[i];
            b = points[j];
            if (!(a._code & edge2)) {
              if (b._code & edge2) {
                p = _getEdgeIntersection(b, a, edge2, bounds, round);
                p._code = _getBitCode(p, bounds);
                clippedPoints.push(p);
              }
              clippedPoints.push(a);
            } else if (!(b._code & edge2)) {
              p = _getEdgeIntersection(b, a, edge2, bounds, round);
              p._code = _getBitCode(p, bounds);
              clippedPoints.push(p);
            }
          }
          points = clippedPoints;
        }
        return points;
      }
      __name(clipPolygon, "clipPolygon");
      function polygonCenter(latlngs, crs) {
        var i, j, p1, p2, f, area, x, y, center;
        if (!latlngs || latlngs.length === 0) {
          throw new Error("latlngs not passed");
        }
        if (!isFlat(latlngs)) {
          console.warn("latlngs are not flat! Only the first ring will be used");
          latlngs = latlngs[0];
        }
        var centroidLatLng = toLatLng([0, 0]);
        var bounds = toLatLngBounds(latlngs);
        var areaBounds = bounds.getNorthWest().distanceTo(bounds.getSouthWest()) * bounds.getNorthEast().distanceTo(bounds.getNorthWest());
        if (areaBounds < 1700) {
          centroidLatLng = centroid(latlngs);
        }
        var len = latlngs.length;
        var points = [];
        for (i = 0; i < len; i++) {
          var latlng = toLatLng(latlngs[i]);
          points.push(crs.project(toLatLng([latlng.lat - centroidLatLng.lat, latlng.lng - centroidLatLng.lng])));
        }
        area = x = y = 0;
        for (i = 0, j = len - 1; i < len; j = i++) {
          p1 = points[i];
          p2 = points[j];
          f = p1.y * p2.x - p2.y * p1.x;
          x += (p1.x + p2.x) * f;
          y += (p1.y + p2.y) * f;
          area += f * 3;
        }
        if (area === 0) {
          center = points[0];
        } else {
          center = [x / area, y / area];
        }
        var latlngCenter = crs.unproject(toPoint(center));
        return toLatLng([latlngCenter.lat + centroidLatLng.lat, latlngCenter.lng + centroidLatLng.lng]);
      }
      __name(polygonCenter, "polygonCenter");
      function centroid(coords) {
        var latSum = 0;
        var lngSum = 0;
        var len = 0;
        for (var i = 0; i < coords.length; i++) {
          var latlng = toLatLng(coords[i]);
          latSum += latlng.lat;
          lngSum += latlng.lng;
          len++;
        }
        return toLatLng([latSum / len, lngSum / len]);
      }
      __name(centroid, "centroid");
      var PolyUtil = {
        __proto__: null,
        clipPolygon,
        polygonCenter,
        centroid
      };
      function simplify(points, tolerance) {
        if (!tolerance || !points.length) {
          return points.slice();
        }
        var sqTolerance = tolerance * tolerance;
        points = _reducePoints(points, sqTolerance);
        points = _simplifyDP(points, sqTolerance);
        return points;
      }
      __name(simplify, "simplify");
      function pointToSegmentDistance(p, p1, p2) {
        return Math.sqrt(_sqClosestPointOnSegment(p, p1, p2, true));
      }
      __name(pointToSegmentDistance, "pointToSegmentDistance");
      function closestPointOnSegment(p, p1, p2) {
        return _sqClosestPointOnSegment(p, p1, p2);
      }
      __name(closestPointOnSegment, "closestPointOnSegment");
      function _simplifyDP(points, sqTolerance) {
        var len = points.length, ArrayConstructor = typeof Uint8Array !== "undefined" ? Uint8Array : Array, markers = new ArrayConstructor(len);
        markers[0] = markers[len - 1] = 1;
        _simplifyDPStep(points, markers, sqTolerance, 0, len - 1);
        var i, newPoints = [];
        for (i = 0; i < len; i++) {
          if (markers[i]) {
            newPoints.push(points[i]);
          }
        }
        return newPoints;
      }
      __name(_simplifyDP, "_simplifyDP");
      function _simplifyDPStep(points, markers, sqTolerance, first, last) {
        var maxSqDist = 0, index2, i, sqDist;
        for (i = first + 1; i <= last - 1; i++) {
          sqDist = _sqClosestPointOnSegment(points[i], points[first], points[last], true);
          if (sqDist > maxSqDist) {
            index2 = i;
            maxSqDist = sqDist;
          }
        }
        if (maxSqDist > sqTolerance) {
          markers[index2] = 1;
          _simplifyDPStep(points, markers, sqTolerance, first, index2);
          _simplifyDPStep(points, markers, sqTolerance, index2, last);
        }
      }
      __name(_simplifyDPStep, "_simplifyDPStep");
      function _reducePoints(points, sqTolerance) {
        var reducedPoints = [points[0]];
        for (var i = 1, prev = 0, len = points.length; i < len; i++) {
          if (_sqDist(points[i], points[prev]) > sqTolerance) {
            reducedPoints.push(points[i]);
            prev = i;
          }
        }
        if (prev < len - 1) {
          reducedPoints.push(points[len - 1]);
        }
        return reducedPoints;
      }
      __name(_reducePoints, "_reducePoints");
      var _lastCode;
      function clipSegment(a, b, bounds, useLastCode, round) {
        var codeA = useLastCode ? _lastCode : _getBitCode(a, bounds), codeB = _getBitCode(b, bounds), codeOut, p, newCode;
        _lastCode = codeB;
        while (true) {
          if (!(codeA | codeB)) {
            return [a, b];
          }
          if (codeA & codeB) {
            return false;
          }
          codeOut = codeA || codeB;
          p = _getEdgeIntersection(a, b, codeOut, bounds, round);
          newCode = _getBitCode(p, bounds);
          if (codeOut === codeA) {
            a = p;
            codeA = newCode;
          } else {
            b = p;
            codeB = newCode;
          }
        }
      }
      __name(clipSegment, "clipSegment");
      function _getEdgeIntersection(a, b, code, bounds, round) {
        var dx = b.x - a.x, dy = b.y - a.y, min = bounds.min, max = bounds.max, x, y;
        if (code & 8) {
          x = a.x + dx * (max.y - a.y) / dy;
          y = max.y;
        } else if (code & 4) {
          x = a.x + dx * (min.y - a.y) / dy;
          y = min.y;
        } else if (code & 2) {
          x = max.x;
          y = a.y + dy * (max.x - a.x) / dx;
        } else if (code & 1) {
          x = min.x;
          y = a.y + dy * (min.x - a.x) / dx;
        }
        return new Point(x, y, round);
      }
      __name(_getEdgeIntersection, "_getEdgeIntersection");
      function _getBitCode(p, bounds) {
        var code = 0;
        if (p.x < bounds.min.x) {
          code |= 1;
        } else if (p.x > bounds.max.x) {
          code |= 2;
        }
        if (p.y < bounds.min.y) {
          code |= 4;
        } else if (p.y > bounds.max.y) {
          code |= 8;
        }
        return code;
      }
      __name(_getBitCode, "_getBitCode");
      function _sqDist(p1, p2) {
        var dx = p2.x - p1.x, dy = p2.y - p1.y;
        return dx * dx + dy * dy;
      }
      __name(_sqDist, "_sqDist");
      function _sqClosestPointOnSegment(p, p1, p2, sqDist) {
        var x = p1.x, y = p1.y, dx = p2.x - x, dy = p2.y - y, dot = dx * dx + dy * dy, t;
        if (dot > 0) {
          t = ((p.x - x) * dx + (p.y - y) * dy) / dot;
          if (t > 1) {
            x = p2.x;
            y = p2.y;
          } else if (t > 0) {
            x += dx * t;
            y += dy * t;
          }
        }
        dx = p.x - x;
        dy = p.y - y;
        return sqDist ? dx * dx + dy * dy : new Point(x, y);
      }
      __name(_sqClosestPointOnSegment, "_sqClosestPointOnSegment");
      function isFlat(latlngs) {
        return !isArray(latlngs[0]) || typeof latlngs[0][0] !== "object" && typeof latlngs[0][0] !== "undefined";
      }
      __name(isFlat, "isFlat");
      function _flat(latlngs) {
        console.warn("Deprecated use of _flat, please use L.LineUtil.isFlat instead.");
        return isFlat(latlngs);
      }
      __name(_flat, "_flat");
      function polylineCenter(latlngs, crs) {
        var i, halfDist, segDist, dist2, p1, p2, ratio, center;
        if (!latlngs || latlngs.length === 0) {
          throw new Error("latlngs not passed");
        }
        if (!isFlat(latlngs)) {
          console.warn("latlngs are not flat! Only the first ring will be used");
          latlngs = latlngs[0];
        }
        var centroidLatLng = toLatLng([0, 0]);
        var bounds = toLatLngBounds(latlngs);
        var areaBounds = bounds.getNorthWest().distanceTo(bounds.getSouthWest()) * bounds.getNorthEast().distanceTo(bounds.getNorthWest());
        if (areaBounds < 1700) {
          centroidLatLng = centroid(latlngs);
        }
        var len = latlngs.length;
        var points = [];
        for (i = 0; i < len; i++) {
          var latlng = toLatLng(latlngs[i]);
          points.push(crs.project(toLatLng([latlng.lat - centroidLatLng.lat, latlng.lng - centroidLatLng.lng])));
        }
        for (i = 0, halfDist = 0; i < len - 1; i++) {
          halfDist += points[i].distanceTo(points[i + 1]) / 2;
        }
        if (halfDist === 0) {
          center = points[0];
        } else {
          for (i = 0, dist2 = 0; i < len - 1; i++) {
            p1 = points[i];
            p2 = points[i + 1];
            segDist = p1.distanceTo(p2);
            dist2 += segDist;
            if (dist2 > halfDist) {
              ratio = (dist2 - halfDist) / segDist;
              center = [
                p2.x - ratio * (p2.x - p1.x),
                p2.y - ratio * (p2.y - p1.y)
              ];
              break;
            }
          }
        }
        var latlngCenter = crs.unproject(toPoint(center));
        return toLatLng([latlngCenter.lat + centroidLatLng.lat, latlngCenter.lng + centroidLatLng.lng]);
      }
      __name(polylineCenter, "polylineCenter");
      var LineUtil = {
        __proto__: null,
        simplify,
        pointToSegmentDistance,
        closestPointOnSegment,
        clipSegment,
        _getEdgeIntersection,
        _getBitCode,
        _sqClosestPointOnSegment,
        isFlat,
        _flat,
        polylineCenter
      };
      var LonLat = {
        project: /* @__PURE__ */ __name(function(latlng) {
          return new Point(latlng.lng, latlng.lat);
        }, "project"),
        unproject: /* @__PURE__ */ __name(function(point) {
          return new LatLng(point.y, point.x);
        }, "unproject"),
        bounds: new Bounds([-180, -90], [180, 90])
      };
      var Mercator = {
        R: 6378137,
        R_MINOR: 6356752314245179e-9,
        bounds: new Bounds([-2003750834279e-5, -1549657073972e-5], [2003750834279e-5, 1876465623138e-5]),
        project: /* @__PURE__ */ __name(function(latlng) {
          var d = Math.PI / 180, r = this.R, y = latlng.lat * d, tmp = this.R_MINOR / r, e = Math.sqrt(1 - tmp * tmp), con = e * Math.sin(y);
          var ts = Math.tan(Math.PI / 4 - y / 2) / Math.pow((1 - con) / (1 + con), e / 2);
          y = -r * Math.log(Math.max(ts, 1e-10));
          return new Point(latlng.lng * d * r, y);
        }, "project"),
        unproject: /* @__PURE__ */ __name(function(point) {
          var d = 180 / Math.PI, r = this.R, tmp = this.R_MINOR / r, e = Math.sqrt(1 - tmp * tmp), ts = Math.exp(-point.y / r), phi = Math.PI / 2 - 2 * Math.atan(ts);
          for (var i = 0, dphi = 0.1, con; i < 15 && Math.abs(dphi) > 1e-7; i++) {
            con = e * Math.sin(phi);
            con = Math.pow((1 - con) / (1 + con), e / 2);
            dphi = Math.PI / 2 - 2 * Math.atan(ts * con) - phi;
            phi += dphi;
          }
          return new LatLng(phi * d, point.x * d / r);
        }, "unproject")
      };
      var index = {
        __proto__: null,
        LonLat,
        Mercator,
        SphericalMercator
      };
      var EPSG3395 = extend({}, Earth, {
        code: "EPSG:3395",
        projection: Mercator,
        transformation: (function() {
          var scale2 = 0.5 / (Math.PI * Mercator.R);
          return toTransformation(scale2, 0.5, -scale2, 0.5);
        })()
      });
      var EPSG4326 = extend({}, Earth, {
        code: "EPSG:4326",
        projection: LonLat,
        transformation: toTransformation(1 / 180, 1, -1 / 180, 0.5)
      });
      var Simple = extend({}, CRS, {
        projection: LonLat,
        transformation: toTransformation(1, 0, -1, 0),
        scale: /* @__PURE__ */ __name(function(zoom2) {
          return Math.pow(2, zoom2);
        }, "scale"),
        zoom: /* @__PURE__ */ __name(function(scale2) {
          return Math.log(scale2) / Math.LN2;
        }, "zoom"),
        distance: /* @__PURE__ */ __name(function(latlng1, latlng2) {
          var dx = latlng2.lng - latlng1.lng, dy = latlng2.lat - latlng1.lat;
          return Math.sqrt(dx * dx + dy * dy);
        }, "distance"),
        infinite: true
      });
      CRS.Earth = Earth;
      CRS.EPSG3395 = EPSG3395;
      CRS.EPSG3857 = EPSG3857;
      CRS.EPSG900913 = EPSG900913;
      CRS.EPSG4326 = EPSG4326;
      CRS.Simple = Simple;
      var Layer = Evented.extend({
        // Classes extending `L.Layer` will inherit the following options:
        options: {
          // @option pane: String = 'overlayPane'
          // By default the layer will be added to the map's [overlay pane](#map-overlaypane). Overriding this option will cause the layer to be placed on another pane by default.
          pane: "overlayPane",
          // @option attribution: String = null
          // String to be shown in the attribution control, e.g. "© OpenStreetMap contributors". It describes the layer data and is often a legal obligation towards copyright holders and tile providers.
          attribution: null,
          bubblingMouseEvents: true
        },
        /* @section
         * Classes extending `L.Layer` will inherit the following methods:
         *
         * @method addTo(map: Map|LayerGroup): this
         * Adds the layer to the given map or layer group.
         */
        addTo: /* @__PURE__ */ __name(function(map2) {
          map2.addLayer(this);
          return this;
        }, "addTo"),
        // @method remove: this
        // Removes the layer from the map it is currently active on.
        remove: /* @__PURE__ */ __name(function() {
          return this.removeFrom(this._map || this._mapToAdd);
        }, "remove"),
        // @method removeFrom(map: Map): this
        // Removes the layer from the given map
        //
        // @alternative
        // @method removeFrom(group: LayerGroup): this
        // Removes the layer from the given `LayerGroup`
        removeFrom: /* @__PURE__ */ __name(function(obj) {
          if (obj) {
            obj.removeLayer(this);
          }
          return this;
        }, "removeFrom"),
        // @method getPane(name? : String): HTMLElement
        // Returns the `HTMLElement` representing the named pane on the map. If `name` is omitted, returns the pane for this layer.
        getPane: /* @__PURE__ */ __name(function(name) {
          return this._map.getPane(name ? this.options[name] || name : this.options.pane);
        }, "getPane"),
        addInteractiveTarget: /* @__PURE__ */ __name(function(targetEl) {
          this._map._targets[stamp(targetEl)] = this;
          return this;
        }, "addInteractiveTarget"),
        removeInteractiveTarget: /* @__PURE__ */ __name(function(targetEl) {
          delete this._map._targets[stamp(targetEl)];
          return this;
        }, "removeInteractiveTarget"),
        // @method getAttribution: String
        // Used by the `attribution control`, returns the [attribution option](#gridlayer-attribution).
        getAttribution: /* @__PURE__ */ __name(function() {
          return this.options.attribution;
        }, "getAttribution"),
        _layerAdd: /* @__PURE__ */ __name(function(e) {
          var map2 = e.target;
          if (!map2.hasLayer(this)) {
            return;
          }
          this._map = map2;
          this._zoomAnimated = map2._zoomAnimated;
          if (this.getEvents) {
            var events = this.getEvents();
            map2.on(events, this);
            this.once("remove", function() {
              map2.off(events, this);
            }, this);
          }
          this.onAdd(map2);
          this.fire("add");
          map2.fire("layeradd", { layer: this });
        }, "_layerAdd")
      });
      Map.include({
        // @method addLayer(layer: Layer): this
        // Adds the given layer to the map
        addLayer: /* @__PURE__ */ __name(function(layer) {
          if (!layer._layerAdd) {
            throw new Error("The provided object is not a Layer.");
          }
          var id = stamp(layer);
          if (this._layers[id]) {
            return this;
          }
          this._layers[id] = layer;
          layer._mapToAdd = this;
          if (layer.beforeAdd) {
            layer.beforeAdd(this);
          }
          this.whenReady(layer._layerAdd, layer);
          return this;
        }, "addLayer"),
        // @method removeLayer(layer: Layer): this
        // Removes the given layer from the map.
        removeLayer: /* @__PURE__ */ __name(function(layer) {
          var id = stamp(layer);
          if (!this._layers[id]) {
            return this;
          }
          if (this._loaded) {
            layer.onRemove(this);
          }
          delete this._layers[id];
          if (this._loaded) {
            this.fire("layerremove", { layer });
            layer.fire("remove");
          }
          layer._map = layer._mapToAdd = null;
          return this;
        }, "removeLayer"),
        // @method hasLayer(layer: Layer): Boolean
        // Returns `true` if the given layer is currently added to the map
        hasLayer: /* @__PURE__ */ __name(function(layer) {
          return stamp(layer) in this._layers;
        }, "hasLayer"),
        /* @method eachLayer(fn: Function, context?: Object): this
         * Iterates over the layers of the map, optionally specifying context of the iterator function.
         * ```
         * map.eachLayer(function(layer){
         *     layer.bindPopup('Hello');
         * });
         * ```
         */
        eachLayer: /* @__PURE__ */ __name(function(method, context) {
          for (var i in this._layers) {
            method.call(context, this._layers[i]);
          }
          return this;
        }, "eachLayer"),
        _addLayers: /* @__PURE__ */ __name(function(layers2) {
          layers2 = layers2 ? isArray(layers2) ? layers2 : [layers2] : [];
          for (var i = 0, len = layers2.length; i < len; i++) {
            this.addLayer(layers2[i]);
          }
        }, "_addLayers"),
        _addZoomLimit: /* @__PURE__ */ __name(function(layer) {
          if (!isNaN(layer.options.maxZoom) || !isNaN(layer.options.minZoom)) {
            this._zoomBoundLayers[stamp(layer)] = layer;
            this._updateZoomLevels();
          }
        }, "_addZoomLimit"),
        _removeZoomLimit: /* @__PURE__ */ __name(function(layer) {
          var id = stamp(layer);
          if (this._zoomBoundLayers[id]) {
            delete this._zoomBoundLayers[id];
            this._updateZoomLevels();
          }
        }, "_removeZoomLimit"),
        _updateZoomLevels: /* @__PURE__ */ __name(function() {
          var minZoom = Infinity, maxZoom = -Infinity, oldZoomSpan = this._getZoomSpan();
          for (var i in this._zoomBoundLayers) {
            var options = this._zoomBoundLayers[i].options;
            minZoom = options.minZoom === void 0 ? minZoom : Math.min(minZoom, options.minZoom);
            maxZoom = options.maxZoom === void 0 ? maxZoom : Math.max(maxZoom, options.maxZoom);
          }
          this._layersMaxZoom = maxZoom === -Infinity ? void 0 : maxZoom;
          this._layersMinZoom = minZoom === Infinity ? void 0 : minZoom;
          if (oldZoomSpan !== this._getZoomSpan()) {
            this.fire("zoomlevelschange");
          }
          if (this.options.maxZoom === void 0 && this._layersMaxZoom && this.getZoom() > this._layersMaxZoom) {
            this.setZoom(this._layersMaxZoom);
          }
          if (this.options.minZoom === void 0 && this._layersMinZoom && this.getZoom() < this._layersMinZoom) {
            this.setZoom(this._layersMinZoom);
          }
        }, "_updateZoomLevels")
      });
      var LayerGroup = Layer.extend({
        initialize: /* @__PURE__ */ __name(function(layers2, options) {
          setOptions(this, options);
          this._layers = {};
          var i, len;
          if (layers2) {
            for (i = 0, len = layers2.length; i < len; i++) {
              this.addLayer(layers2[i]);
            }
          }
        }, "initialize"),
        // @method addLayer(layer: Layer): this
        // Adds the given layer to the group.
        addLayer: /* @__PURE__ */ __name(function(layer) {
          var id = this.getLayerId(layer);
          this._layers[id] = layer;
          if (this._map) {
            this._map.addLayer(layer);
          }
          return this;
        }, "addLayer"),
        // @method removeLayer(layer: Layer): this
        // Removes the given layer from the group.
        // @alternative
        // @method removeLayer(id: Number): this
        // Removes the layer with the given internal ID from the group.
        removeLayer: /* @__PURE__ */ __name(function(layer) {
          var id = layer in this._layers ? layer : this.getLayerId(layer);
          if (this._map && this._layers[id]) {
            this._map.removeLayer(this._layers[id]);
          }
          delete this._layers[id];
          return this;
        }, "removeLayer"),
        // @method hasLayer(layer: Layer): Boolean
        // Returns `true` if the given layer is currently added to the group.
        // @alternative
        // @method hasLayer(id: Number): Boolean
        // Returns `true` if the given internal ID is currently added to the group.
        hasLayer: /* @__PURE__ */ __name(function(layer) {
          var layerId = typeof layer === "number" ? layer : this.getLayerId(layer);
          return layerId in this._layers;
        }, "hasLayer"),
        // @method clearLayers(): this
        // Removes all the layers from the group.
        clearLayers: /* @__PURE__ */ __name(function() {
          return this.eachLayer(this.removeLayer, this);
        }, "clearLayers"),
        // @method invoke(methodName: String, …): this
        // Calls `methodName` on every layer contained in this group, passing any
        // additional parameters. Has no effect if the layers contained do not
        // implement `methodName`.
        invoke: /* @__PURE__ */ __name(function(methodName) {
          var args = Array.prototype.slice.call(arguments, 1), i, layer;
          for (i in this._layers) {
            layer = this._layers[i];
            if (layer[methodName]) {
              layer[methodName].apply(layer, args);
            }
          }
          return this;
        }, "invoke"),
        onAdd: /* @__PURE__ */ __name(function(map2) {
          this.eachLayer(map2.addLayer, map2);
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function(map2) {
          this.eachLayer(map2.removeLayer, map2);
        }, "onRemove"),
        // @method eachLayer(fn: Function, context?: Object): this
        // Iterates over the layers of the group, optionally specifying context of the iterator function.
        // ```js
        // group.eachLayer(function (layer) {
        // 	layer.bindPopup('Hello');
        // });
        // ```
        eachLayer: /* @__PURE__ */ __name(function(method, context) {
          for (var i in this._layers) {
            method.call(context, this._layers[i]);
          }
          return this;
        }, "eachLayer"),
        // @method getLayer(id: Number): Layer
        // Returns the layer with the given internal ID.
        getLayer: /* @__PURE__ */ __name(function(id) {
          return this._layers[id];
        }, "getLayer"),
        // @method getLayers(): Layer[]
        // Returns an array of all the layers added to the group.
        getLayers: /* @__PURE__ */ __name(function() {
          var layers2 = [];
          this.eachLayer(layers2.push, layers2);
          return layers2;
        }, "getLayers"),
        // @method setZIndex(zIndex: Number): this
        // Calls `setZIndex` on every layer contained in this group, passing the z-index.
        setZIndex: /* @__PURE__ */ __name(function(zIndex) {
          return this.invoke("setZIndex", zIndex);
        }, "setZIndex"),
        // @method getLayerId(layer: Layer): Number
        // Returns the internal ID for a layer
        getLayerId: /* @__PURE__ */ __name(function(layer) {
          return stamp(layer);
        }, "getLayerId")
      });
      var layerGroup2 = /* @__PURE__ */ __name(function(layers2, options) {
        return new LayerGroup(layers2, options);
      }, "layerGroup");
      var FeatureGroup = LayerGroup.extend({
        addLayer: /* @__PURE__ */ __name(function(layer) {
          if (this.hasLayer(layer)) {
            return this;
          }
          layer.addEventParent(this);
          LayerGroup.prototype.addLayer.call(this, layer);
          return this.fire("layeradd", { layer });
        }, "addLayer"),
        removeLayer: /* @__PURE__ */ __name(function(layer) {
          if (!this.hasLayer(layer)) {
            return this;
          }
          if (layer in this._layers) {
            layer = this._layers[layer];
          }
          layer.removeEventParent(this);
          LayerGroup.prototype.removeLayer.call(this, layer);
          return this.fire("layerremove", { layer });
        }, "removeLayer"),
        // @method setStyle(style: Path options): this
        // Sets the given path options to each layer of the group that has a `setStyle` method.
        setStyle: /* @__PURE__ */ __name(function(style2) {
          return this.invoke("setStyle", style2);
        }, "setStyle"),
        // @method bringToFront(): this
        // Brings the layer group to the top of all other layers
        bringToFront: /* @__PURE__ */ __name(function() {
          return this.invoke("bringToFront");
        }, "bringToFront"),
        // @method bringToBack(): this
        // Brings the layer group to the back of all other layers
        bringToBack: /* @__PURE__ */ __name(function() {
          return this.invoke("bringToBack");
        }, "bringToBack"),
        // @method getBounds(): LatLngBounds
        // Returns the LatLngBounds of the Feature Group (created from bounds and coordinates of its children).
        getBounds: /* @__PURE__ */ __name(function() {
          var bounds = new LatLngBounds();
          for (var id in this._layers) {
            var layer = this._layers[id];
            bounds.extend(layer.getBounds ? layer.getBounds() : layer.getLatLng());
          }
          return bounds;
        }, "getBounds")
      });
      var featureGroup = /* @__PURE__ */ __name(function(layers2, options) {
        return new FeatureGroup(layers2, options);
      }, "featureGroup");
      var Icon = Class.extend({
        /* @section
         * @aka Icon options
         *
         * @option iconUrl: String = null
         * **(required)** The URL to the icon image (absolute or relative to your script path).
         *
         * @option iconRetinaUrl: String = null
         * The URL to a retina sized version of the icon image (absolute or relative to your
         * script path). Used for Retina screen devices.
         *
         * @option iconSize: Point = null
         * Size of the icon image in pixels.
         *
         * @option iconAnchor: Point = null
         * The coordinates of the "tip" of the icon (relative to its top left corner). The icon
         * will be aligned so that this point is at the marker's geographical location. Centered
         * by default if size is specified, also can be set in CSS with negative margins.
         *
         * @option popupAnchor: Point = [0, 0]
         * The coordinates of the point from which popups will "open", relative to the icon anchor.
         *
         * @option tooltipAnchor: Point = [0, 0]
         * The coordinates of the point from which tooltips will "open", relative to the icon anchor.
         *
         * @option shadowUrl: String = null
         * The URL to the icon shadow image. If not specified, no shadow image will be created.
         *
         * @option shadowRetinaUrl: String = null
         *
         * @option shadowSize: Point = null
         * Size of the shadow image in pixels.
         *
         * @option shadowAnchor: Point = null
         * The coordinates of the "tip" of the shadow (relative to its top left corner) (the same
         * as iconAnchor if not specified).
         *
         * @option className: String = ''
         * A custom class name to assign to both icon and shadow images. Empty by default.
         */
        options: {
          popupAnchor: [0, 0],
          tooltipAnchor: [0, 0],
          // @option crossOrigin: Boolean|String = false
          // Whether the crossOrigin attribute will be added to the tiles.
          // If a String is provided, all tiles will have their crossOrigin attribute set to the String provided. This is needed if you want to access tile pixel data.
          // Refer to [CORS Settings](https://developer.mozilla.org/en-US/docs/Web/HTML/CORS_settings_attributes) for valid String values.
          crossOrigin: false
        },
        initialize: /* @__PURE__ */ __name(function(options) {
          setOptions(this, options);
        }, "initialize"),
        // @method createIcon(oldIcon?: HTMLElement): HTMLElement
        // Called internally when the icon has to be shown, returns a `<img>` HTML element
        // styled according to the options.
        createIcon: /* @__PURE__ */ __name(function(oldIcon) {
          return this._createIcon("icon", oldIcon);
        }, "createIcon"),
        // @method createShadow(oldIcon?: HTMLElement): HTMLElement
        // As `createIcon`, but for the shadow beneath it.
        createShadow: /* @__PURE__ */ __name(function(oldIcon) {
          return this._createIcon("shadow", oldIcon);
        }, "createShadow"),
        _createIcon: /* @__PURE__ */ __name(function(name, oldIcon) {
          var src = this._getIconUrl(name);
          if (!src) {
            if (name === "icon") {
              throw new Error("iconUrl not set in Icon options (see the docs).");
            }
            return null;
          }
          var img = this._createImg(src, oldIcon && oldIcon.tagName === "IMG" ? oldIcon : null);
          this._setIconStyles(img, name);
          if (this.options.crossOrigin || this.options.crossOrigin === "") {
            img.crossOrigin = this.options.crossOrigin === true ? "" : this.options.crossOrigin;
          }
          return img;
        }, "_createIcon"),
        _setIconStyles: /* @__PURE__ */ __name(function(img, name) {
          var options = this.options;
          var sizeOption = options[name + "Size"];
          if (typeof sizeOption === "number") {
            sizeOption = [sizeOption, sizeOption];
          }
          var size = toPoint(sizeOption), anchor = toPoint(name === "shadow" && options.shadowAnchor || options.iconAnchor || size && size.divideBy(2, true));
          img.className = "leaflet-marker-" + name + " " + (options.className || "");
          if (anchor) {
            img.style.marginLeft = -anchor.x + "px";
            img.style.marginTop = -anchor.y + "px";
          }
          if (size) {
            img.style.width = size.x + "px";
            img.style.height = size.y + "px";
          }
        }, "_setIconStyles"),
        _createImg: /* @__PURE__ */ __name(function(src, el) {
          el = el || document.createElement("img");
          el.src = src;
          return el;
        }, "_createImg"),
        _getIconUrl: /* @__PURE__ */ __name(function(name) {
          return Browser.retina && this.options[name + "RetinaUrl"] || this.options[name + "Url"];
        }, "_getIconUrl")
      });
      function icon(options) {
        return new Icon(options);
      }
      __name(icon, "icon");
      var IconDefault = Icon.extend({
        options: {
          iconUrl: "marker-icon.png",
          iconRetinaUrl: "marker-icon-2x.png",
          shadowUrl: "marker-shadow.png",
          iconSize: [25, 41],
          iconAnchor: [12, 41],
          popupAnchor: [1, -34],
          tooltipAnchor: [16, -28],
          shadowSize: [41, 41]
        },
        _getIconUrl: /* @__PURE__ */ __name(function(name) {
          if (typeof IconDefault.imagePath !== "string") {
            IconDefault.imagePath = this._detectIconPath();
          }
          return (this.options.imagePath || IconDefault.imagePath) + Icon.prototype._getIconUrl.call(this, name);
        }, "_getIconUrl"),
        _stripUrl: /* @__PURE__ */ __name(function(path) {
          var strip = /* @__PURE__ */ __name(function(str, re, idx) {
            var match = re.exec(str);
            return match && match[idx];
          }, "strip");
          path = strip(path, /^url\((['"])?(.+)\1\)$/, 2);
          return path && strip(path, /^(.*)marker-icon\.png$/, 1);
        }, "_stripUrl"),
        _detectIconPath: /* @__PURE__ */ __name(function() {
          var el = create$1("div", "leaflet-default-icon-path", document.body);
          var path = getStyle(el, "background-image") || getStyle(el, "backgroundImage");
          document.body.removeChild(el);
          path = this._stripUrl(path);
          if (path) {
            return path;
          }
          var link = document.querySelector('link[href$="leaflet.css"]');
          if (!link) {
            return "";
          }
          return link.href.substring(0, link.href.length - "leaflet.css".length - 1);
        }, "_detectIconPath")
      });
      var MarkerDrag = Handler.extend({
        initialize: /* @__PURE__ */ __name(function(marker3) {
          this._marker = marker3;
        }, "initialize"),
        addHooks: /* @__PURE__ */ __name(function() {
          var icon2 = this._marker._icon;
          if (!this._draggable) {
            this._draggable = new Draggable(icon2, icon2, true);
          }
          this._draggable.on({
            dragstart: this._onDragStart,
            predrag: this._onPreDrag,
            drag: this._onDrag,
            dragend: this._onDragEnd
          }, this).enable();
          addClass(icon2, "leaflet-marker-draggable");
        }, "addHooks"),
        removeHooks: /* @__PURE__ */ __name(function() {
          this._draggable.off({
            dragstart: this._onDragStart,
            predrag: this._onPreDrag,
            drag: this._onDrag,
            dragend: this._onDragEnd
          }, this).disable();
          if (this._marker._icon) {
            removeClass(this._marker._icon, "leaflet-marker-draggable");
          }
        }, "removeHooks"),
        moved: /* @__PURE__ */ __name(function() {
          return this._draggable && this._draggable._moved;
        }, "moved"),
        _adjustPan: /* @__PURE__ */ __name(function(e) {
          var marker3 = this._marker, map2 = marker3._map, speed = this._marker.options.autoPanSpeed, padding = this._marker.options.autoPanPadding, iconPos = getPosition(marker3._icon), bounds = map2.getPixelBounds(), origin = map2.getPixelOrigin();
          var panBounds = toBounds(
            bounds.min._subtract(origin).add(padding),
            bounds.max._subtract(origin).subtract(padding)
          );
          if (!panBounds.contains(iconPos)) {
            var movement = toPoint(
              (Math.max(panBounds.max.x, iconPos.x) - panBounds.max.x) / (bounds.max.x - panBounds.max.x) - (Math.min(panBounds.min.x, iconPos.x) - panBounds.min.x) / (bounds.min.x - panBounds.min.x),
              (Math.max(panBounds.max.y, iconPos.y) - panBounds.max.y) / (bounds.max.y - panBounds.max.y) - (Math.min(panBounds.min.y, iconPos.y) - panBounds.min.y) / (bounds.min.y - panBounds.min.y)
            ).multiplyBy(speed);
            map2.panBy(movement, { animate: false });
            this._draggable._newPos._add(movement);
            this._draggable._startPos._add(movement);
            setPosition(marker3._icon, this._draggable._newPos);
            this._onDrag(e);
            this._panRequest = requestAnimFrame(this._adjustPan.bind(this, e));
          }
        }, "_adjustPan"),
        _onDragStart: /* @__PURE__ */ __name(function() {
          this._oldLatLng = this._marker.getLatLng();
          this._marker.closePopup && this._marker.closePopup();
          this._marker.fire("movestart").fire("dragstart");
        }, "_onDragStart"),
        _onPreDrag: /* @__PURE__ */ __name(function(e) {
          if (this._marker.options.autoPan) {
            cancelAnimFrame(this._panRequest);
            this._panRequest = requestAnimFrame(this._adjustPan.bind(this, e));
          }
        }, "_onPreDrag"),
        _onDrag: /* @__PURE__ */ __name(function(e) {
          var marker3 = this._marker, shadow = marker3._shadow, iconPos = getPosition(marker3._icon), latlng = marker3._map.layerPointToLatLng(iconPos);
          if (shadow) {
            setPosition(shadow, iconPos);
          }
          marker3._latlng = latlng;
          e.latlng = latlng;
          e.oldLatLng = this._oldLatLng;
          marker3.fire("move", e).fire("drag", e);
        }, "_onDrag"),
        _onDragEnd: /* @__PURE__ */ __name(function(e) {
          cancelAnimFrame(this._panRequest);
          delete this._oldLatLng;
          this._marker.fire("moveend").fire("dragend", e);
        }, "_onDragEnd")
      });
      var Marker2 = Layer.extend({
        // @section
        // @aka Marker options
        options: {
          // @option icon: Icon = *
          // Icon instance to use for rendering the marker.
          // See [Icon documentation](#L.Icon) for details on how to customize the marker icon.
          // If not specified, a common instance of `L.Icon.Default` is used.
          icon: new IconDefault(),
          // Option inherited from "Interactive layer" abstract class
          interactive: true,
          // @option keyboard: Boolean = true
          // Whether the marker can be tabbed to with a keyboard and clicked by pressing enter.
          keyboard: true,
          // @option title: String = ''
          // Text for the browser tooltip that appear on marker hover (no tooltip by default).
          // [Useful for accessibility](https://leafletjs.com/examples/accessibility/#markers-must-be-labelled).
          title: "",
          // @option alt: String = 'Marker'
          // Text for the `alt` attribute of the icon image.
          // [Useful for accessibility](https://leafletjs.com/examples/accessibility/#markers-must-be-labelled).
          alt: "Marker",
          // @option zIndexOffset: Number = 0
          // By default, marker images zIndex is set automatically based on its latitude. Use this option if you want to put the marker on top of all others (or below), specifying a high value like `1000` (or high negative value, respectively).
          zIndexOffset: 0,
          // @option opacity: Number = 1.0
          // The opacity of the marker.
          opacity: 1,
          // @option riseOnHover: Boolean = false
          // If `true`, the marker will get on top of others when you hover the mouse over it.
          riseOnHover: false,
          // @option riseOffset: Number = 250
          // The z-index offset used for the `riseOnHover` feature.
          riseOffset: 250,
          // @option pane: String = 'markerPane'
          // `Map pane` where the markers icon will be added.
          pane: "markerPane",
          // @option shadowPane: String = 'shadowPane'
          // `Map pane` where the markers shadow will be added.
          shadowPane: "shadowPane",
          // @option bubblingMouseEvents: Boolean = false
          // When `true`, a mouse event on this marker will trigger the same event on the map
          // (unless [`L.DomEvent.stopPropagation`](#domevent-stoppropagation) is used).
          bubblingMouseEvents: false,
          // @option autoPanOnFocus: Boolean = true
          // When `true`, the map will pan whenever the marker is focused (via
          // e.g. pressing `tab` on the keyboard) to ensure the marker is
          // visible within the map's bounds
          autoPanOnFocus: true,
          // @section Draggable marker options
          // @option draggable: Boolean = false
          // Whether the marker is draggable with mouse/touch or not.
          draggable: false,
          // @option autoPan: Boolean = false
          // Whether to pan the map when dragging this marker near its edge or not.
          autoPan: false,
          // @option autoPanPadding: Point = Point(50, 50)
          // Distance (in pixels to the left/right and to the top/bottom) of the
          // map edge to start panning the map.
          autoPanPadding: [50, 50],
          // @option autoPanSpeed: Number = 10
          // Number of pixels the map should pan by.
          autoPanSpeed: 10
        },
        /* @section
         *
         * In addition to [shared layer methods](#Layer) like `addTo()` and `remove()` and [popup methods](#Popup) like bindPopup() you can also use the following methods:
         */
        initialize: /* @__PURE__ */ __name(function(latlng, options) {
          setOptions(this, options);
          this._latlng = toLatLng(latlng);
        }, "initialize"),
        onAdd: /* @__PURE__ */ __name(function(map2) {
          this._zoomAnimated = this._zoomAnimated && map2.options.markerZoomAnimation;
          if (this._zoomAnimated) {
            map2.on("zoomanim", this._animateZoom, this);
          }
          this._initIcon();
          this.update();
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function(map2) {
          if (this.dragging && this.dragging.enabled()) {
            this.options.draggable = true;
            this.dragging.removeHooks();
          }
          delete this.dragging;
          if (this._zoomAnimated) {
            map2.off("zoomanim", this._animateZoom, this);
          }
          this._removeIcon();
          this._removeShadow();
        }, "onRemove"),
        getEvents: /* @__PURE__ */ __name(function() {
          return {
            zoom: this.update,
            viewreset: this.update
          };
        }, "getEvents"),
        // @method getLatLng: LatLng
        // Returns the current geographical position of the marker.
        getLatLng: /* @__PURE__ */ __name(function() {
          return this._latlng;
        }, "getLatLng"),
        // @method setLatLng(latlng: LatLng): this
        // Changes the marker position to the given point.
        setLatLng: /* @__PURE__ */ __name(function(latlng) {
          var oldLatLng = this._latlng;
          this._latlng = toLatLng(latlng);
          this.update();
          return this.fire("move", { oldLatLng, latlng: this._latlng });
        }, "setLatLng"),
        // @method setZIndexOffset(offset: Number): this
        // Changes the [zIndex offset](#marker-zindexoffset) of the marker.
        setZIndexOffset: /* @__PURE__ */ __name(function(offset) {
          this.options.zIndexOffset = offset;
          return this.update();
        }, "setZIndexOffset"),
        // @method getIcon: Icon
        // Returns the current icon used by the marker
        getIcon: /* @__PURE__ */ __name(function() {
          return this.options.icon;
        }, "getIcon"),
        // @method setIcon(icon: Icon): this
        // Changes the marker icon.
        setIcon: /* @__PURE__ */ __name(function(icon2) {
          this.options.icon = icon2;
          if (this._map) {
            this._initIcon();
            this.update();
          }
          if (this._popup) {
            this.bindPopup(this._popup, this._popup.options);
          }
          return this;
        }, "setIcon"),
        getElement: /* @__PURE__ */ __name(function() {
          return this._icon;
        }, "getElement"),
        update: /* @__PURE__ */ __name(function() {
          if (this._icon && this._map) {
            var pos = this._map.latLngToLayerPoint(this._latlng).round();
            this._setPos(pos);
          }
          return this;
        }, "update"),
        _initIcon: /* @__PURE__ */ __name(function() {
          var options = this.options, classToAdd = "leaflet-zoom-" + (this._zoomAnimated ? "animated" : "hide");
          var icon2 = options.icon.createIcon(this._icon), addIcon = false;
          if (icon2 !== this._icon) {
            if (this._icon) {
              this._removeIcon();
            }
            addIcon = true;
            if (options.title) {
              icon2.title = options.title;
            }
            if (icon2.tagName === "IMG") {
              icon2.alt = options.alt || "";
            }
          }
          addClass(icon2, classToAdd);
          if (options.keyboard) {
            icon2.tabIndex = "0";
            icon2.setAttribute("role", "button");
          }
          this._icon = icon2;
          if (options.riseOnHover) {
            this.on({
              mouseover: this._bringToFront,
              mouseout: this._resetZIndex
            });
          }
          if (this.options.autoPanOnFocus) {
            on(icon2, "focus", this._panOnFocus, this);
          }
          var newShadow = options.icon.createShadow(this._shadow), addShadow = false;
          if (newShadow !== this._shadow) {
            this._removeShadow();
            addShadow = true;
          }
          if (newShadow) {
            addClass(newShadow, classToAdd);
            newShadow.alt = "";
          }
          this._shadow = newShadow;
          if (options.opacity < 1) {
            this._updateOpacity();
          }
          if (addIcon) {
            this.getPane().appendChild(this._icon);
          }
          this._initInteraction();
          if (newShadow && addShadow) {
            this.getPane(options.shadowPane).appendChild(this._shadow);
          }
        }, "_initIcon"),
        _removeIcon: /* @__PURE__ */ __name(function() {
          if (this.options.riseOnHover) {
            this.off({
              mouseover: this._bringToFront,
              mouseout: this._resetZIndex
            });
          }
          if (this.options.autoPanOnFocus) {
            off(this._icon, "focus", this._panOnFocus, this);
          }
          remove(this._icon);
          this.removeInteractiveTarget(this._icon);
          this._icon = null;
        }, "_removeIcon"),
        _removeShadow: /* @__PURE__ */ __name(function() {
          if (this._shadow) {
            remove(this._shadow);
          }
          this._shadow = null;
        }, "_removeShadow"),
        _setPos: /* @__PURE__ */ __name(function(pos) {
          if (this._icon) {
            setPosition(this._icon, pos);
          }
          if (this._shadow) {
            setPosition(this._shadow, pos);
          }
          this._zIndex = pos.y + this.options.zIndexOffset;
          this._resetZIndex();
        }, "_setPos"),
        _updateZIndex: /* @__PURE__ */ __name(function(offset) {
          if (this._icon) {
            this._icon.style.zIndex = this._zIndex + offset;
          }
        }, "_updateZIndex"),
        _animateZoom: /* @__PURE__ */ __name(function(opt) {
          var pos = this._map._latLngToNewLayerPoint(this._latlng, opt.zoom, opt.center).round();
          this._setPos(pos);
        }, "_animateZoom"),
        _initInteraction: /* @__PURE__ */ __name(function() {
          if (!this.options.interactive) {
            return;
          }
          addClass(this._icon, "leaflet-interactive");
          this.addInteractiveTarget(this._icon);
          if (MarkerDrag) {
            var draggable = this.options.draggable;
            if (this.dragging) {
              draggable = this.dragging.enabled();
              this.dragging.disable();
            }
            this.dragging = new MarkerDrag(this);
            if (draggable) {
              this.dragging.enable();
            }
          }
        }, "_initInteraction"),
        // @method setOpacity(opacity: Number): this
        // Changes the opacity of the marker.
        setOpacity: /* @__PURE__ */ __name(function(opacity) {
          this.options.opacity = opacity;
          if (this._map) {
            this._updateOpacity();
          }
          return this;
        }, "setOpacity"),
        _updateOpacity: /* @__PURE__ */ __name(function() {
          var opacity = this.options.opacity;
          if (this._icon) {
            setOpacity(this._icon, opacity);
          }
          if (this._shadow) {
            setOpacity(this._shadow, opacity);
          }
        }, "_updateOpacity"),
        _bringToFront: /* @__PURE__ */ __name(function() {
          this._updateZIndex(this.options.riseOffset);
        }, "_bringToFront"),
        _resetZIndex: /* @__PURE__ */ __name(function() {
          this._updateZIndex(0);
        }, "_resetZIndex"),
        _panOnFocus: /* @__PURE__ */ __name(function() {
          var map2 = this._map;
          if (!map2) {
            return;
          }
          var iconOpts = this.options.icon.options;
          var size = iconOpts.iconSize ? toPoint(iconOpts.iconSize) : toPoint(0, 0);
          var anchor = iconOpts.iconAnchor ? toPoint(iconOpts.iconAnchor) : toPoint(0, 0);
          map2.panInside(this._latlng, {
            paddingTopLeft: anchor,
            paddingBottomRight: size.subtract(anchor)
          });
        }, "_panOnFocus"),
        _getPopupAnchor: /* @__PURE__ */ __name(function() {
          return this.options.icon.options.popupAnchor;
        }, "_getPopupAnchor"),
        _getTooltipAnchor: /* @__PURE__ */ __name(function() {
          return this.options.icon.options.tooltipAnchor;
        }, "_getTooltipAnchor")
      });
      function marker2(latlng, options) {
        return new Marker2(latlng, options);
      }
      __name(marker2, "marker");
      var Path = Layer.extend({
        // @section
        // @aka Path options
        options: {
          // @option stroke: Boolean = true
          // Whether to draw stroke along the path. Set it to `false` to disable borders on polygons or circles.
          stroke: true,
          // @option color: String = '#3388ff'
          // Stroke color
          color: "#3388ff",
          // @option weight: Number = 3
          // Stroke width in pixels
          weight: 3,
          // @option opacity: Number = 1.0
          // Stroke opacity
          opacity: 1,
          // @option lineCap: String= 'round'
          // A string that defines [shape to be used at the end](https://developer.mozilla.org/docs/Web/SVG/Attribute/stroke-linecap) of the stroke.
          lineCap: "round",
          // @option lineJoin: String = 'round'
          // A string that defines [shape to be used at the corners](https://developer.mozilla.org/docs/Web/SVG/Attribute/stroke-linejoin) of the stroke.
          lineJoin: "round",
          // @option dashArray: String = null
          // A string that defines the stroke [dash pattern](https://developer.mozilla.org/docs/Web/SVG/Attribute/stroke-dasharray). Doesn't work on `Canvas`-powered layers in [some old browsers](https://developer.mozilla.org/docs/Web/API/CanvasRenderingContext2D/setLineDash#Browser_compatibility).
          dashArray: null,
          // @option dashOffset: String = null
          // A string that defines the [distance into the dash pattern to start the dash](https://developer.mozilla.org/docs/Web/SVG/Attribute/stroke-dashoffset). Doesn't work on `Canvas`-powered layers in [some old browsers](https://developer.mozilla.org/docs/Web/API/CanvasRenderingContext2D/setLineDash#Browser_compatibility).
          dashOffset: null,
          // @option fill: Boolean = depends
          // Whether to fill the path with color. Set it to `false` to disable filling on polygons or circles.
          fill: false,
          // @option fillColor: String = *
          // Fill color. Defaults to the value of the [`color`](#path-color) option
          fillColor: null,
          // @option fillOpacity: Number = 0.2
          // Fill opacity.
          fillOpacity: 0.2,
          // @option fillRule: String = 'evenodd'
          // A string that defines [how the inside of a shape](https://developer.mozilla.org/docs/Web/SVG/Attribute/fill-rule) is determined.
          fillRule: "evenodd",
          // className: '',
          // Option inherited from "Interactive layer" abstract class
          interactive: true,
          // @option bubblingMouseEvents: Boolean = true
          // When `true`, a mouse event on this path will trigger the same event on the map
          // (unless [`L.DomEvent.stopPropagation`](#domevent-stoppropagation) is used).
          bubblingMouseEvents: true
        },
        beforeAdd: /* @__PURE__ */ __name(function(map2) {
          this._renderer = map2.getRenderer(this);
        }, "beforeAdd"),
        onAdd: /* @__PURE__ */ __name(function() {
          this._renderer._initPath(this);
          this._reset();
          this._renderer._addPath(this);
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function() {
          this._renderer._removePath(this);
        }, "onRemove"),
        // @method redraw(): this
        // Redraws the layer. Sometimes useful after you changed the coordinates that the path uses.
        redraw: /* @__PURE__ */ __name(function() {
          if (this._map) {
            this._renderer._updatePath(this);
          }
          return this;
        }, "redraw"),
        // @method setStyle(style: Path options): this
        // Changes the appearance of a Path based on the options in the `Path options` object.
        setStyle: /* @__PURE__ */ __name(function(style2) {
          setOptions(this, style2);
          if (this._renderer) {
            this._renderer._updateStyle(this);
            if (this.options.stroke && style2 && Object.prototype.hasOwnProperty.call(style2, "weight")) {
              this._updateBounds();
            }
          }
          return this;
        }, "setStyle"),
        // @method bringToFront(): this
        // Brings the layer to the top of all path layers.
        bringToFront: /* @__PURE__ */ __name(function() {
          if (this._renderer) {
            this._renderer._bringToFront(this);
          }
          return this;
        }, "bringToFront"),
        // @method bringToBack(): this
        // Brings the layer to the bottom of all path layers.
        bringToBack: /* @__PURE__ */ __name(function() {
          if (this._renderer) {
            this._renderer._bringToBack(this);
          }
          return this;
        }, "bringToBack"),
        getElement: /* @__PURE__ */ __name(function() {
          return this._path;
        }, "getElement"),
        _reset: /* @__PURE__ */ __name(function() {
          this._project();
          this._update();
        }, "_reset"),
        _clickTolerance: /* @__PURE__ */ __name(function() {
          return (this.options.stroke ? this.options.weight / 2 : 0) + (this._renderer.options.tolerance || 0);
        }, "_clickTolerance")
      });
      var CircleMarker = Path.extend({
        // @section
        // @aka CircleMarker options
        options: {
          fill: true,
          // @option radius: Number = 10
          // Radius of the circle marker, in pixels
          radius: 10
        },
        initialize: /* @__PURE__ */ __name(function(latlng, options) {
          setOptions(this, options);
          this._latlng = toLatLng(latlng);
          this._radius = this.options.radius;
        }, "initialize"),
        // @method setLatLng(latLng: LatLng): this
        // Sets the position of a circle marker to a new location.
        setLatLng: /* @__PURE__ */ __name(function(latlng) {
          var oldLatLng = this._latlng;
          this._latlng = toLatLng(latlng);
          this.redraw();
          return this.fire("move", { oldLatLng, latlng: this._latlng });
        }, "setLatLng"),
        // @method getLatLng(): LatLng
        // Returns the current geographical position of the circle marker
        getLatLng: /* @__PURE__ */ __name(function() {
          return this._latlng;
        }, "getLatLng"),
        // @method setRadius(radius: Number): this
        // Sets the radius of a circle marker. Units are in pixels.
        setRadius: /* @__PURE__ */ __name(function(radius) {
          this.options.radius = this._radius = radius;
          return this.redraw();
        }, "setRadius"),
        // @method getRadius(): Number
        // Returns the current radius of the circle
        getRadius: /* @__PURE__ */ __name(function() {
          return this._radius;
        }, "getRadius"),
        setStyle: /* @__PURE__ */ __name(function(options) {
          var radius = options && options.radius || this._radius;
          Path.prototype.setStyle.call(this, options);
          this.setRadius(radius);
          return this;
        }, "setStyle"),
        _project: /* @__PURE__ */ __name(function() {
          this._point = this._map.latLngToLayerPoint(this._latlng);
          this._updateBounds();
        }, "_project"),
        _updateBounds: /* @__PURE__ */ __name(function() {
          var r = this._radius, r2 = this._radiusY || r, w = this._clickTolerance(), p = [r + w, r2 + w];
          this._pxBounds = new Bounds(this._point.subtract(p), this._point.add(p));
        }, "_updateBounds"),
        _update: /* @__PURE__ */ __name(function() {
          if (this._map) {
            this._updatePath();
          }
        }, "_update"),
        _updatePath: /* @__PURE__ */ __name(function() {
          this._renderer._updateCircle(this);
        }, "_updatePath"),
        _empty: /* @__PURE__ */ __name(function() {
          return this._radius && !this._renderer._bounds.intersects(this._pxBounds);
        }, "_empty"),
        // Needed by the `Canvas` renderer for interactivity
        _containsPoint: /* @__PURE__ */ __name(function(p) {
          return p.distanceTo(this._point) <= this._radius + this._clickTolerance();
        }, "_containsPoint")
      });
      function circleMarker(latlng, options) {
        return new CircleMarker(latlng, options);
      }
      __name(circleMarker, "circleMarker");
      var Circle = CircleMarker.extend({
        initialize: /* @__PURE__ */ __name(function(latlng, options, legacyOptions) {
          if (typeof options === "number") {
            options = extend({}, legacyOptions, { radius: options });
          }
          setOptions(this, options);
          this._latlng = toLatLng(latlng);
          if (isNaN(this.options.radius)) {
            throw new Error("Circle radius cannot be NaN");
          }
          this._mRadius = this.options.radius;
        }, "initialize"),
        // @method setRadius(radius: Number): this
        // Sets the radius of a circle. Units are in meters.
        setRadius: /* @__PURE__ */ __name(function(radius) {
          this._mRadius = radius;
          return this.redraw();
        }, "setRadius"),
        // @method getRadius(): Number
        // Returns the current radius of a circle. Units are in meters.
        getRadius: /* @__PURE__ */ __name(function() {
          return this._mRadius;
        }, "getRadius"),
        // @method getBounds(): LatLngBounds
        // Returns the `LatLngBounds` of the path.
        getBounds: /* @__PURE__ */ __name(function() {
          var half = [this._radius, this._radiusY || this._radius];
          return new LatLngBounds(
            this._map.layerPointToLatLng(this._point.subtract(half)),
            this._map.layerPointToLatLng(this._point.add(half))
          );
        }, "getBounds"),
        setStyle: Path.prototype.setStyle,
        _project: /* @__PURE__ */ __name(function() {
          var lng = this._latlng.lng, lat = this._latlng.lat, map2 = this._map, crs = map2.options.crs;
          if (crs.distance === Earth.distance) {
            var d = Math.PI / 180, latR = this._mRadius / Earth.R / d, top = map2.project([lat + latR, lng]), bottom = map2.project([lat - latR, lng]), p = top.add(bottom).divideBy(2), lat2 = map2.unproject(p).lat, lngR = Math.acos((Math.cos(latR * d) - Math.sin(lat * d) * Math.sin(lat2 * d)) / (Math.cos(lat * d) * Math.cos(lat2 * d))) / d;
            if (isNaN(lngR) || lngR === 0) {
              lngR = latR / Math.cos(Math.PI / 180 * lat);
            }
            this._point = p.subtract(map2.getPixelOrigin());
            this._radius = isNaN(lngR) ? 0 : p.x - map2.project([lat2, lng - lngR]).x;
            this._radiusY = p.y - top.y;
          } else {
            var latlng2 = crs.unproject(crs.project(this._latlng).subtract([this._mRadius, 0]));
            this._point = map2.latLngToLayerPoint(this._latlng);
            this._radius = this._point.x - map2.latLngToLayerPoint(latlng2).x;
          }
          this._updateBounds();
        }, "_project")
      });
      function circle(latlng, options, legacyOptions) {
        return new Circle(latlng, options, legacyOptions);
      }
      __name(circle, "circle");
      var Polyline = Path.extend({
        // @section
        // @aka Polyline options
        options: {
          // @option smoothFactor: Number = 1.0
          // How much to simplify the polyline on each zoom level. More means
          // better performance and smoother look, and less means more accurate representation.
          smoothFactor: 1,
          // @option noClip: Boolean = false
          // Disable polyline clipping.
          noClip: false
        },
        initialize: /* @__PURE__ */ __name(function(latlngs, options) {
          setOptions(this, options);
          this._setLatLngs(latlngs);
        }, "initialize"),
        // @method getLatLngs(): LatLng[]
        // Returns an array of the points in the path, or nested arrays of points in case of multi-polyline.
        getLatLngs: /* @__PURE__ */ __name(function() {
          return this._latlngs;
        }, "getLatLngs"),
        // @method setLatLngs(latlngs: LatLng[]): this
        // Replaces all the points in the polyline with the given array of geographical points.
        setLatLngs: /* @__PURE__ */ __name(function(latlngs) {
          this._setLatLngs(latlngs);
          return this.redraw();
        }, "setLatLngs"),
        // @method isEmpty(): Boolean
        // Returns `true` if the Polyline has no LatLngs.
        isEmpty: /* @__PURE__ */ __name(function() {
          return !this._latlngs.length;
        }, "isEmpty"),
        // @method closestLayerPoint(p: Point): Point
        // Returns the point closest to `p` on the Polyline.
        closestLayerPoint: /* @__PURE__ */ __name(function(p) {
          var minDistance = Infinity, minPoint = null, closest = _sqClosestPointOnSegment, p1, p2;
          for (var j = 0, jLen = this._parts.length; j < jLen; j++) {
            var points = this._parts[j];
            for (var i = 1, len = points.length; i < len; i++) {
              p1 = points[i - 1];
              p2 = points[i];
              var sqDist = closest(p, p1, p2, true);
              if (sqDist < minDistance) {
                minDistance = sqDist;
                minPoint = closest(p, p1, p2);
              }
            }
          }
          if (minPoint) {
            minPoint.distance = Math.sqrt(minDistance);
          }
          return minPoint;
        }, "closestLayerPoint"),
        // @method getCenter(): LatLng
        // Returns the center ([centroid](https://en.wikipedia.org/wiki/Centroid)) of the polyline.
        getCenter: /* @__PURE__ */ __name(function() {
          if (!this._map) {
            throw new Error("Must add layer to map before using getCenter()");
          }
          return polylineCenter(this._defaultShape(), this._map.options.crs);
        }, "getCenter"),
        // @method getBounds(): LatLngBounds
        // Returns the `LatLngBounds` of the path.
        getBounds: /* @__PURE__ */ __name(function() {
          return this._bounds;
        }, "getBounds"),
        // @method addLatLng(latlng: LatLng, latlngs?: LatLng[]): this
        // Adds a given point to the polyline. By default, adds to the first ring of
        // the polyline in case of a multi-polyline, but can be overridden by passing
        // a specific ring as a LatLng array (that you can earlier access with [`getLatLngs`](#polyline-getlatlngs)).
        addLatLng: /* @__PURE__ */ __name(function(latlng, latlngs) {
          latlngs = latlngs || this._defaultShape();
          latlng = toLatLng(latlng);
          latlngs.push(latlng);
          this._bounds.extend(latlng);
          return this.redraw();
        }, "addLatLng"),
        _setLatLngs: /* @__PURE__ */ __name(function(latlngs) {
          this._bounds = new LatLngBounds();
          this._latlngs = this._convertLatLngs(latlngs);
        }, "_setLatLngs"),
        _defaultShape: /* @__PURE__ */ __name(function() {
          return isFlat(this._latlngs) ? this._latlngs : this._latlngs[0];
        }, "_defaultShape"),
        // recursively convert latlngs input into actual LatLng instances; calculate bounds along the way
        _convertLatLngs: /* @__PURE__ */ __name(function(latlngs) {
          var result = [], flat = isFlat(latlngs);
          for (var i = 0, len = latlngs.length; i < len; i++) {
            if (flat) {
              result[i] = toLatLng(latlngs[i]);
              this._bounds.extend(result[i]);
            } else {
              result[i] = this._convertLatLngs(latlngs[i]);
            }
          }
          return result;
        }, "_convertLatLngs"),
        _project: /* @__PURE__ */ __name(function() {
          var pxBounds = new Bounds();
          this._rings = [];
          this._projectLatlngs(this._latlngs, this._rings, pxBounds);
          if (this._bounds.isValid() && pxBounds.isValid()) {
            this._rawPxBounds = pxBounds;
            this._updateBounds();
          }
        }, "_project"),
        _updateBounds: /* @__PURE__ */ __name(function() {
          var w = this._clickTolerance(), p = new Point(w, w);
          if (!this._rawPxBounds) {
            return;
          }
          this._pxBounds = new Bounds([
            this._rawPxBounds.min.subtract(p),
            this._rawPxBounds.max.add(p)
          ]);
        }, "_updateBounds"),
        // recursively turns latlngs into a set of rings with projected coordinates
        _projectLatlngs: /* @__PURE__ */ __name(function(latlngs, result, projectedBounds) {
          var flat = latlngs[0] instanceof LatLng, len = latlngs.length, i, ring;
          if (flat) {
            ring = [];
            for (i = 0; i < len; i++) {
              ring[i] = this._map.latLngToLayerPoint(latlngs[i]);
              projectedBounds.extend(ring[i]);
            }
            result.push(ring);
          } else {
            for (i = 0; i < len; i++) {
              this._projectLatlngs(latlngs[i], result, projectedBounds);
            }
          }
        }, "_projectLatlngs"),
        // clip polyline by renderer bounds so that we have less to render for performance
        _clipPoints: /* @__PURE__ */ __name(function() {
          var bounds = this._renderer._bounds;
          this._parts = [];
          if (!this._pxBounds || !this._pxBounds.intersects(bounds)) {
            return;
          }
          if (this.options.noClip) {
            this._parts = this._rings;
            return;
          }
          var parts = this._parts, i, j, k, len, len2, segment, points;
          for (i = 0, k = 0, len = this._rings.length; i < len; i++) {
            points = this._rings[i];
            for (j = 0, len2 = points.length; j < len2 - 1; j++) {
              segment = clipSegment(points[j], points[j + 1], bounds, j, true);
              if (!segment) {
                continue;
              }
              parts[k] = parts[k] || [];
              parts[k].push(segment[0]);
              if (segment[1] !== points[j + 1] || j === len2 - 2) {
                parts[k].push(segment[1]);
                k++;
              }
            }
          }
        }, "_clipPoints"),
        // simplify each clipped part of the polyline for performance
        _simplifyPoints: /* @__PURE__ */ __name(function() {
          var parts = this._parts, tolerance = this.options.smoothFactor;
          for (var i = 0, len = parts.length; i < len; i++) {
            parts[i] = simplify(parts[i], tolerance);
          }
        }, "_simplifyPoints"),
        _update: /* @__PURE__ */ __name(function() {
          if (!this._map) {
            return;
          }
          this._clipPoints();
          this._simplifyPoints();
          this._updatePath();
        }, "_update"),
        _updatePath: /* @__PURE__ */ __name(function() {
          this._renderer._updatePoly(this);
        }, "_updatePath"),
        // Needed by the `Canvas` renderer for interactivity
        _containsPoint: /* @__PURE__ */ __name(function(p, closed) {
          var i, j, k, len, len2, part, w = this._clickTolerance();
          if (!this._pxBounds || !this._pxBounds.contains(p)) {
            return false;
          }
          for (i = 0, len = this._parts.length; i < len; i++) {
            part = this._parts[i];
            for (j = 0, len2 = part.length, k = len2 - 1; j < len2; k = j++) {
              if (!closed && j === 0) {
                continue;
              }
              if (pointToSegmentDistance(p, part[k], part[j]) <= w) {
                return true;
              }
            }
          }
          return false;
        }, "_containsPoint")
      });
      function polyline(latlngs, options) {
        return new Polyline(latlngs, options);
      }
      __name(polyline, "polyline");
      Polyline._flat = _flat;
      var Polygon = Polyline.extend({
        options: {
          fill: true
        },
        isEmpty: /* @__PURE__ */ __name(function() {
          return !this._latlngs.length || !this._latlngs[0].length;
        }, "isEmpty"),
        // @method getCenter(): LatLng
        // Returns the center ([centroid](http://en.wikipedia.org/wiki/Centroid)) of the Polygon.
        getCenter: /* @__PURE__ */ __name(function() {
          if (!this._map) {
            throw new Error("Must add layer to map before using getCenter()");
          }
          return polygonCenter(this._defaultShape(), this._map.options.crs);
        }, "getCenter"),
        _convertLatLngs: /* @__PURE__ */ __name(function(latlngs) {
          var result = Polyline.prototype._convertLatLngs.call(this, latlngs), len = result.length;
          if (len >= 2 && result[0] instanceof LatLng && result[0].equals(result[len - 1])) {
            result.pop();
          }
          return result;
        }, "_convertLatLngs"),
        _setLatLngs: /* @__PURE__ */ __name(function(latlngs) {
          Polyline.prototype._setLatLngs.call(this, latlngs);
          if (isFlat(this._latlngs)) {
            this._latlngs = [this._latlngs];
          }
        }, "_setLatLngs"),
        _defaultShape: /* @__PURE__ */ __name(function() {
          return isFlat(this._latlngs[0]) ? this._latlngs[0] : this._latlngs[0][0];
        }, "_defaultShape"),
        _clipPoints: /* @__PURE__ */ __name(function() {
          var bounds = this._renderer._bounds, w = this.options.weight, p = new Point(w, w);
          bounds = new Bounds(bounds.min.subtract(p), bounds.max.add(p));
          this._parts = [];
          if (!this._pxBounds || !this._pxBounds.intersects(bounds)) {
            return;
          }
          if (this.options.noClip) {
            this._parts = this._rings;
            return;
          }
          for (var i = 0, len = this._rings.length, clipped; i < len; i++) {
            clipped = clipPolygon(this._rings[i], bounds, true);
            if (clipped.length) {
              this._parts.push(clipped);
            }
          }
        }, "_clipPoints"),
        _updatePath: /* @__PURE__ */ __name(function() {
          this._renderer._updatePoly(this, true);
        }, "_updatePath"),
        // Needed by the `Canvas` renderer for interactivity
        _containsPoint: /* @__PURE__ */ __name(function(p) {
          var inside = false, part, p1, p2, i, j, k, len, len2;
          if (!this._pxBounds || !this._pxBounds.contains(p)) {
            return false;
          }
          for (i = 0, len = this._parts.length; i < len; i++) {
            part = this._parts[i];
            for (j = 0, len2 = part.length, k = len2 - 1; j < len2; k = j++) {
              p1 = part[j];
              p2 = part[k];
              if (p1.y > p.y !== p2.y > p.y && p.x < (p2.x - p1.x) * (p.y - p1.y) / (p2.y - p1.y) + p1.x) {
                inside = !inside;
              }
            }
          }
          return inside || Polyline.prototype._containsPoint.call(this, p, true);
        }, "_containsPoint")
      });
      function polygon(latlngs, options) {
        return new Polygon(latlngs, options);
      }
      __name(polygon, "polygon");
      var GeoJSON = FeatureGroup.extend({
        /* @section
         * @aka GeoJSON options
         *
         * @option pointToLayer: Function = *
         * A `Function` defining how GeoJSON points spawn Leaflet layers. It is internally
         * called when data is added, passing the GeoJSON point feature and its `LatLng`.
         * The default is to spawn a default `Marker`:
         * ```js
         * function(geoJsonPoint, latlng) {
         * 	return L.marker(latlng);
         * }
         * ```
         *
         * @option style: Function = *
         * A `Function` defining the `Path options` for styling GeoJSON lines and polygons,
         * called internally when data is added.
         * The default value is to not override any defaults:
         * ```js
         * function (geoJsonFeature) {
         * 	return {}
         * }
         * ```
         *
         * @option onEachFeature: Function = *
         * A `Function` that will be called once for each created `Feature`, after it has
         * been created and styled. Useful for attaching events and popups to features.
         * The default is to do nothing with the newly created layers:
         * ```js
         * function (feature, layer) {}
         * ```
         *
         * @option filter: Function = *
         * A `Function` that will be used to decide whether to include a feature or not.
         * The default is to include all features:
         * ```js
         * function (geoJsonFeature) {
         * 	return true;
         * }
         * ```
         * Note: dynamically changing the `filter` option will have effect only on newly
         * added data. It will _not_ re-evaluate already included features.
         *
         * @option coordsToLatLng: Function = *
         * A `Function` that will be used for converting GeoJSON coordinates to `LatLng`s.
         * The default is the `coordsToLatLng` static method.
         *
         * @option markersInheritOptions: Boolean = false
         * Whether default Markers for "Point" type Features inherit from group options.
         */
        initialize: /* @__PURE__ */ __name(function(geojson, options) {
          setOptions(this, options);
          this._layers = {};
          if (geojson) {
            this.addData(geojson);
          }
        }, "initialize"),
        // @method addData( <GeoJSON> data ): this
        // Adds a GeoJSON object to the layer.
        addData: /* @__PURE__ */ __name(function(geojson) {
          var features = isArray(geojson) ? geojson : geojson.features, i, len, feature;
          if (features) {
            for (i = 0, len = features.length; i < len; i++) {
              feature = features[i];
              if (feature.geometries || feature.geometry || feature.features || feature.coordinates) {
                this.addData(feature);
              }
            }
            return this;
          }
          var options = this.options;
          if (options.filter && !options.filter(geojson)) {
            return this;
          }
          var layer = geometryToLayer(geojson, options);
          if (!layer) {
            return this;
          }
          layer.feature = asFeature(geojson);
          layer.defaultOptions = layer.options;
          this.resetStyle(layer);
          if (options.onEachFeature) {
            options.onEachFeature(geojson, layer);
          }
          return this.addLayer(layer);
        }, "addData"),
        // @method resetStyle( <Path> layer? ): this
        // Resets the given vector layer's style to the original GeoJSON style, useful for resetting style after hover events.
        // If `layer` is omitted, the style of all features in the current layer is reset.
        resetStyle: /* @__PURE__ */ __name(function(layer) {
          if (layer === void 0) {
            return this.eachLayer(this.resetStyle, this);
          }
          layer.options = extend({}, layer.defaultOptions);
          this._setLayerStyle(layer, this.options.style);
          return this;
        }, "resetStyle"),
        // @method setStyle( <Function> style ): this
        // Changes styles of GeoJSON vector layers with the given style function.
        setStyle: /* @__PURE__ */ __name(function(style2) {
          return this.eachLayer(function(layer) {
            this._setLayerStyle(layer, style2);
          }, this);
        }, "setStyle"),
        _setLayerStyle: /* @__PURE__ */ __name(function(layer, style2) {
          if (layer.setStyle) {
            if (typeof style2 === "function") {
              style2 = style2(layer.feature);
            }
            layer.setStyle(style2);
          }
        }, "_setLayerStyle")
      });
      function geometryToLayer(geojson, options) {
        var geometry = geojson.type === "Feature" ? geojson.geometry : geojson, coords = geometry ? geometry.coordinates : null, layers2 = [], pointToLayer = options && options.pointToLayer, _coordsToLatLng = options && options.coordsToLatLng || coordsToLatLng, latlng, latlngs, i, len;
        if (!coords && !geometry) {
          return null;
        }
        switch (geometry.type) {
          case "Point":
            latlng = _coordsToLatLng(coords);
            return _pointToLayer(pointToLayer, geojson, latlng, options);
          case "MultiPoint":
            for (i = 0, len = coords.length; i < len; i++) {
              latlng = _coordsToLatLng(coords[i]);
              layers2.push(_pointToLayer(pointToLayer, geojson, latlng, options));
            }
            return new FeatureGroup(layers2);
          case "LineString":
          case "MultiLineString":
            latlngs = coordsToLatLngs(coords, geometry.type === "LineString" ? 0 : 1, _coordsToLatLng);
            return new Polyline(latlngs, options);
          case "Polygon":
          case "MultiPolygon":
            latlngs = coordsToLatLngs(coords, geometry.type === "Polygon" ? 1 : 2, _coordsToLatLng);
            return new Polygon(latlngs, options);
          case "GeometryCollection":
            for (i = 0, len = geometry.geometries.length; i < len; i++) {
              var geoLayer = geometryToLayer({
                geometry: geometry.geometries[i],
                type: "Feature",
                properties: geojson.properties
              }, options);
              if (geoLayer) {
                layers2.push(geoLayer);
              }
            }
            return new FeatureGroup(layers2);
          case "FeatureCollection":
            for (i = 0, len = geometry.features.length; i < len; i++) {
              var featureLayer = geometryToLayer(geometry.features[i], options);
              if (featureLayer) {
                layers2.push(featureLayer);
              }
            }
            return new FeatureGroup(layers2);
          default:
            throw new Error("Invalid GeoJSON object.");
        }
      }
      __name(geometryToLayer, "geometryToLayer");
      function _pointToLayer(pointToLayerFn, geojson, latlng, options) {
        return pointToLayerFn ? pointToLayerFn(geojson, latlng) : new Marker2(latlng, options && options.markersInheritOptions && options);
      }
      __name(_pointToLayer, "_pointToLayer");
      function coordsToLatLng(coords) {
        return new LatLng(coords[1], coords[0], coords[2]);
      }
      __name(coordsToLatLng, "coordsToLatLng");
      function coordsToLatLngs(coords, levelsDeep, _coordsToLatLng) {
        var latlngs = [];
        for (var i = 0, len = coords.length, latlng; i < len; i++) {
          latlng = levelsDeep ? coordsToLatLngs(coords[i], levelsDeep - 1, _coordsToLatLng) : (_coordsToLatLng || coordsToLatLng)(coords[i]);
          latlngs.push(latlng);
        }
        return latlngs;
      }
      __name(coordsToLatLngs, "coordsToLatLngs");
      function latLngToCoords(latlng, precision) {
        latlng = toLatLng(latlng);
        return latlng.alt !== void 0 ? [formatNum(latlng.lng, precision), formatNum(latlng.lat, precision), formatNum(latlng.alt, precision)] : [formatNum(latlng.lng, precision), formatNum(latlng.lat, precision)];
      }
      __name(latLngToCoords, "latLngToCoords");
      function latLngsToCoords(latlngs, levelsDeep, closed, precision) {
        var coords = [];
        for (var i = 0, len = latlngs.length; i < len; i++) {
          coords.push(levelsDeep ? latLngsToCoords(latlngs[i], isFlat(latlngs[i]) ? 0 : levelsDeep - 1, closed, precision) : latLngToCoords(latlngs[i], precision));
        }
        if (!levelsDeep && closed && coords.length > 0) {
          coords.push(coords[0].slice());
        }
        return coords;
      }
      __name(latLngsToCoords, "latLngsToCoords");
      function getFeature(layer, newGeometry) {
        return layer.feature ? extend({}, layer.feature, { geometry: newGeometry }) : asFeature(newGeometry);
      }
      __name(getFeature, "getFeature");
      function asFeature(geojson) {
        if (geojson.type === "Feature" || geojson.type === "FeatureCollection") {
          return geojson;
        }
        return {
          type: "Feature",
          properties: {},
          geometry: geojson
        };
      }
      __name(asFeature, "asFeature");
      var PointToGeoJSON = {
        toGeoJSON: /* @__PURE__ */ __name(function(precision) {
          return getFeature(this, {
            type: "Point",
            coordinates: latLngToCoords(this.getLatLng(), precision)
          });
        }, "toGeoJSON")
      };
      Marker2.include(PointToGeoJSON);
      Circle.include(PointToGeoJSON);
      CircleMarker.include(PointToGeoJSON);
      Polyline.include({
        toGeoJSON: /* @__PURE__ */ __name(function(precision) {
          var multi = !isFlat(this._latlngs);
          var coords = latLngsToCoords(this._latlngs, multi ? 1 : 0, false, precision);
          return getFeature(this, {
            type: (multi ? "Multi" : "") + "LineString",
            coordinates: coords
          });
        }, "toGeoJSON")
      });
      Polygon.include({
        toGeoJSON: /* @__PURE__ */ __name(function(precision) {
          var holes = !isFlat(this._latlngs), multi = holes && !isFlat(this._latlngs[0]);
          var coords = latLngsToCoords(this._latlngs, multi ? 2 : holes ? 1 : 0, true, precision);
          if (!holes) {
            coords = [coords];
          }
          return getFeature(this, {
            type: (multi ? "Multi" : "") + "Polygon",
            coordinates: coords
          });
        }, "toGeoJSON")
      });
      LayerGroup.include({
        toMultiPoint: /* @__PURE__ */ __name(function(precision) {
          var coords = [];
          this.eachLayer(function(layer) {
            coords.push(layer.toGeoJSON(precision).geometry.coordinates);
          });
          return getFeature(this, {
            type: "MultiPoint",
            coordinates: coords
          });
        }, "toMultiPoint"),
        // @method toGeoJSON(precision?: Number|false): Object
        // Coordinates values are rounded with [`formatNum`](#util-formatnum) function with given `precision`.
        // Returns a [`GeoJSON`](https://en.wikipedia.org/wiki/GeoJSON) representation of the layer group (as a GeoJSON `FeatureCollection`, `GeometryCollection`, or `MultiPoint`).
        toGeoJSON: /* @__PURE__ */ __name(function(precision) {
          var type = this.feature && this.feature.geometry && this.feature.geometry.type;
          if (type === "MultiPoint") {
            return this.toMultiPoint(precision);
          }
          var isGeometryCollection = type === "GeometryCollection", jsons = [];
          this.eachLayer(function(layer) {
            if (layer.toGeoJSON) {
              var json = layer.toGeoJSON(precision);
              if (isGeometryCollection) {
                jsons.push(json.geometry);
              } else {
                var feature = asFeature(json);
                if (feature.type === "FeatureCollection") {
                  jsons.push.apply(jsons, feature.features);
                } else {
                  jsons.push(feature);
                }
              }
            }
          });
          if (isGeometryCollection) {
            return getFeature(this, {
              geometries: jsons,
              type: "GeometryCollection"
            });
          }
          return {
            type: "FeatureCollection",
            features: jsons
          };
        }, "toGeoJSON")
      });
      function geoJSON(geojson, options) {
        return new GeoJSON(geojson, options);
      }
      __name(geoJSON, "geoJSON");
      var geoJson = geoJSON;
      var ImageOverlay = Layer.extend({
        // @section
        // @aka ImageOverlay options
        options: {
          // @option opacity: Number = 1.0
          // The opacity of the image overlay.
          opacity: 1,
          // @option alt: String = ''
          // Text for the `alt` attribute of the image (useful for accessibility).
          alt: "",
          // @option interactive: Boolean = false
          // If `true`, the image overlay will emit [mouse events](#interactive-layer) when clicked or hovered.
          interactive: false,
          // @option crossOrigin: Boolean|String = false
          // Whether the crossOrigin attribute will be added to the image.
          // If a String is provided, the image will have its crossOrigin attribute set to the String provided. This is needed if you want to access image pixel data.
          // Refer to [CORS Settings](https://developer.mozilla.org/en-US/docs/Web/HTML/CORS_settings_attributes) for valid String values.
          crossOrigin: false,
          // @option errorOverlayUrl: String = ''
          // URL to the overlay image to show in place of the overlay that failed to load.
          errorOverlayUrl: "",
          // @option zIndex: Number = 1
          // The explicit [zIndex](https://developer.mozilla.org/docs/Web/CSS/CSS_Positioning/Understanding_z_index) of the overlay layer.
          zIndex: 1,
          // @option className: String = ''
          // A custom class name to assign to the image. Empty by default.
          className: ""
        },
        initialize: /* @__PURE__ */ __name(function(url, bounds, options) {
          this._url = url;
          this._bounds = toLatLngBounds(bounds);
          setOptions(this, options);
        }, "initialize"),
        onAdd: /* @__PURE__ */ __name(function() {
          if (!this._image) {
            this._initImage();
            if (this.options.opacity < 1) {
              this._updateOpacity();
            }
          }
          if (this.options.interactive) {
            addClass(this._image, "leaflet-interactive");
            this.addInteractiveTarget(this._image);
          }
          this.getPane().appendChild(this._image);
          this._reset();
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function() {
          remove(this._image);
          if (this.options.interactive) {
            this.removeInteractiveTarget(this._image);
          }
        }, "onRemove"),
        // @method setOpacity(opacity: Number): this
        // Sets the opacity of the overlay.
        setOpacity: /* @__PURE__ */ __name(function(opacity) {
          this.options.opacity = opacity;
          if (this._image) {
            this._updateOpacity();
          }
          return this;
        }, "setOpacity"),
        setStyle: /* @__PURE__ */ __name(function(styleOpts) {
          if (styleOpts.opacity) {
            this.setOpacity(styleOpts.opacity);
          }
          return this;
        }, "setStyle"),
        // @method bringToFront(): this
        // Brings the layer to the top of all overlays.
        bringToFront: /* @__PURE__ */ __name(function() {
          if (this._map) {
            toFront(this._image);
          }
          return this;
        }, "bringToFront"),
        // @method bringToBack(): this
        // Brings the layer to the bottom of all overlays.
        bringToBack: /* @__PURE__ */ __name(function() {
          if (this._map) {
            toBack(this._image);
          }
          return this;
        }, "bringToBack"),
        // @method setUrl(url: String): this
        // Changes the URL of the image.
        setUrl: /* @__PURE__ */ __name(function(url) {
          this._url = url;
          if (this._image) {
            this._image.src = url;
          }
          return this;
        }, "setUrl"),
        // @method setBounds(bounds: LatLngBounds): this
        // Update the bounds that this ImageOverlay covers
        setBounds: /* @__PURE__ */ __name(function(bounds) {
          this._bounds = toLatLngBounds(bounds);
          if (this._map) {
            this._reset();
          }
          return this;
        }, "setBounds"),
        getEvents: /* @__PURE__ */ __name(function() {
          var events = {
            zoom: this._reset,
            viewreset: this._reset
          };
          if (this._zoomAnimated) {
            events.zoomanim = this._animateZoom;
          }
          return events;
        }, "getEvents"),
        // @method setZIndex(value: Number): this
        // Changes the [zIndex](#imageoverlay-zindex) of the image overlay.
        setZIndex: /* @__PURE__ */ __name(function(value) {
          this.options.zIndex = value;
          this._updateZIndex();
          return this;
        }, "setZIndex"),
        // @method getBounds(): LatLngBounds
        // Get the bounds that this ImageOverlay covers
        getBounds: /* @__PURE__ */ __name(function() {
          return this._bounds;
        }, "getBounds"),
        // @method getElement(): HTMLElement
        // Returns the instance of [`HTMLImageElement`](https://developer.mozilla.org/docs/Web/API/HTMLImageElement)
        // used by this overlay.
        getElement: /* @__PURE__ */ __name(function() {
          return this._image;
        }, "getElement"),
        _initImage: /* @__PURE__ */ __name(function() {
          var wasElementSupplied = this._url.tagName === "IMG";
          var img = this._image = wasElementSupplied ? this._url : create$1("img");
          addClass(img, "leaflet-image-layer");
          if (this._zoomAnimated) {
            addClass(img, "leaflet-zoom-animated");
          }
          if (this.options.className) {
            addClass(img, this.options.className);
          }
          img.onselectstart = falseFn;
          img.onmousemove = falseFn;
          img.onload = bind(this.fire, this, "load");
          img.onerror = bind(this._overlayOnError, this, "error");
          if (this.options.crossOrigin || this.options.crossOrigin === "") {
            img.crossOrigin = this.options.crossOrigin === true ? "" : this.options.crossOrigin;
          }
          if (this.options.zIndex) {
            this._updateZIndex();
          }
          if (wasElementSupplied) {
            this._url = img.src;
            return;
          }
          img.src = this._url;
          img.alt = this.options.alt;
        }, "_initImage"),
        _animateZoom: /* @__PURE__ */ __name(function(e) {
          var scale2 = this._map.getZoomScale(e.zoom), offset = this._map._latLngBoundsToNewLayerBounds(this._bounds, e.zoom, e.center).min;
          setTransform(this._image, offset, scale2);
        }, "_animateZoom"),
        _reset: /* @__PURE__ */ __name(function() {
          var image = this._image, bounds = new Bounds(
            this._map.latLngToLayerPoint(this._bounds.getNorthWest()),
            this._map.latLngToLayerPoint(this._bounds.getSouthEast())
          ), size = bounds.getSize();
          setPosition(image, bounds.min);
          image.style.width = size.x + "px";
          image.style.height = size.y + "px";
        }, "_reset"),
        _updateOpacity: /* @__PURE__ */ __name(function() {
          setOpacity(this._image, this.options.opacity);
        }, "_updateOpacity"),
        _updateZIndex: /* @__PURE__ */ __name(function() {
          if (this._image && this.options.zIndex !== void 0 && this.options.zIndex !== null) {
            this._image.style.zIndex = this.options.zIndex;
          }
        }, "_updateZIndex"),
        _overlayOnError: /* @__PURE__ */ __name(function() {
          this.fire("error");
          var errorUrl = this.options.errorOverlayUrl;
          if (errorUrl && this._url !== errorUrl) {
            this._url = errorUrl;
            this._image.src = errorUrl;
          }
        }, "_overlayOnError"),
        // @method getCenter(): LatLng
        // Returns the center of the ImageOverlay.
        getCenter: /* @__PURE__ */ __name(function() {
          return this._bounds.getCenter();
        }, "getCenter")
      });
      var imageOverlay2 = /* @__PURE__ */ __name(function(url, bounds, options) {
        return new ImageOverlay(url, bounds, options);
      }, "imageOverlay");
      var VideoOverlay = ImageOverlay.extend({
        // @section
        // @aka VideoOverlay options
        options: {
          // @option autoplay: Boolean = true
          // Whether the video starts playing automatically when loaded.
          // On some browsers autoplay will only work with `muted: true`
          autoplay: true,
          // @option loop: Boolean = true
          // Whether the video will loop back to the beginning when played.
          loop: true,
          // @option keepAspectRatio: Boolean = true
          // Whether the video will save aspect ratio after the projection.
          // Relevant for supported browsers. See [browser compatibility](https://developer.mozilla.org/en-US/docs/Web/CSS/object-fit)
          keepAspectRatio: true,
          // @option muted: Boolean = false
          // Whether the video starts on mute when loaded.
          muted: false,
          // @option playsInline: Boolean = true
          // Mobile browsers will play the video right where it is instead of open it up in fullscreen mode.
          playsInline: true
        },
        _initImage: /* @__PURE__ */ __name(function() {
          var wasElementSupplied = this._url.tagName === "VIDEO";
          var vid = this._image = wasElementSupplied ? this._url : create$1("video");
          addClass(vid, "leaflet-image-layer");
          if (this._zoomAnimated) {
            addClass(vid, "leaflet-zoom-animated");
          }
          if (this.options.className) {
            addClass(vid, this.options.className);
          }
          vid.onselectstart = falseFn;
          vid.onmousemove = falseFn;
          vid.onloadeddata = bind(this.fire, this, "load");
          if (wasElementSupplied) {
            var sourceElements = vid.getElementsByTagName("source");
            var sources = [];
            for (var j = 0; j < sourceElements.length; j++) {
              sources.push(sourceElements[j].src);
            }
            this._url = sourceElements.length > 0 ? sources : [vid.src];
            return;
          }
          if (!isArray(this._url)) {
            this._url = [this._url];
          }
          if (!this.options.keepAspectRatio && Object.prototype.hasOwnProperty.call(vid.style, "objectFit")) {
            vid.style["objectFit"] = "fill";
          }
          vid.autoplay = !!this.options.autoplay;
          vid.loop = !!this.options.loop;
          vid.muted = !!this.options.muted;
          vid.playsInline = !!this.options.playsInline;
          for (var i = 0; i < this._url.length; i++) {
            var source = create$1("source");
            source.src = this._url[i];
            vid.appendChild(source);
          }
        }, "_initImage")
        // @method getElement(): HTMLVideoElement
        // Returns the instance of [`HTMLVideoElement`](https://developer.mozilla.org/docs/Web/API/HTMLVideoElement)
        // used by this overlay.
      });
      function videoOverlay(video, bounds, options) {
        return new VideoOverlay(video, bounds, options);
      }
      __name(videoOverlay, "videoOverlay");
      var SVGOverlay = ImageOverlay.extend({
        _initImage: /* @__PURE__ */ __name(function() {
          var el = this._image = this._url;
          addClass(el, "leaflet-image-layer");
          if (this._zoomAnimated) {
            addClass(el, "leaflet-zoom-animated");
          }
          if (this.options.className) {
            addClass(el, this.options.className);
          }
          el.onselectstart = falseFn;
          el.onmousemove = falseFn;
        }, "_initImage")
        // @method getElement(): SVGElement
        // Returns the instance of [`SVGElement`](https://developer.mozilla.org/docs/Web/API/SVGElement)
        // used by this overlay.
      });
      function svgOverlay(el, bounds, options) {
        return new SVGOverlay(el, bounds, options);
      }
      __name(svgOverlay, "svgOverlay");
      var DivOverlay = Layer.extend({
        // @section
        // @aka DivOverlay options
        options: {
          // @option interactive: Boolean = false
          // If true, the popup/tooltip will listen to the mouse events.
          interactive: false,
          // @option offset: Point = Point(0, 0)
          // The offset of the overlay position.
          offset: [0, 0],
          // @option className: String = ''
          // A custom CSS class name to assign to the overlay.
          className: "",
          // @option pane: String = undefined
          // `Map pane` where the overlay will be added.
          pane: void 0,
          // @option content: String|HTMLElement|Function = ''
          // Sets the HTML content of the overlay while initializing. If a function is passed the source layer will be
          // passed to the function. The function should return a `String` or `HTMLElement` to be used in the overlay.
          content: ""
        },
        initialize: /* @__PURE__ */ __name(function(options, source) {
          if (options && (options instanceof LatLng || isArray(options))) {
            this._latlng = toLatLng(options);
            setOptions(this, source);
          } else {
            setOptions(this, options);
            this._source = source;
          }
          if (this.options.content) {
            this._content = this.options.content;
          }
        }, "initialize"),
        // @method openOn(map: Map): this
        // Adds the overlay to the map.
        // Alternative to `map.openPopup(popup)`/`.openTooltip(tooltip)`.
        openOn: /* @__PURE__ */ __name(function(map2) {
          map2 = arguments.length ? map2 : this._source._map;
          if (!map2.hasLayer(this)) {
            map2.addLayer(this);
          }
          return this;
        }, "openOn"),
        // @method close(): this
        // Closes the overlay.
        // Alternative to `map.closePopup(popup)`/`.closeTooltip(tooltip)`
        // and `layer.closePopup()`/`.closeTooltip()`.
        close: /* @__PURE__ */ __name(function() {
          if (this._map) {
            this._map.removeLayer(this);
          }
          return this;
        }, "close"),
        // @method toggle(layer?: Layer): this
        // Opens or closes the overlay bound to layer depending on its current state.
        // Argument may be omitted only for overlay bound to layer.
        // Alternative to `layer.togglePopup()`/`.toggleTooltip()`.
        toggle: /* @__PURE__ */ __name(function(layer) {
          if (this._map) {
            this.close();
          } else {
            if (arguments.length) {
              this._source = layer;
            } else {
              layer = this._source;
            }
            this._prepareOpen();
            this.openOn(layer._map);
          }
          return this;
        }, "toggle"),
        onAdd: /* @__PURE__ */ __name(function(map2) {
          this._zoomAnimated = map2._zoomAnimated;
          if (!this._container) {
            this._initLayout();
          }
          if (map2._fadeAnimated) {
            setOpacity(this._container, 0);
          }
          clearTimeout(this._removeTimeout);
          this.getPane().appendChild(this._container);
          this.update();
          if (map2._fadeAnimated) {
            setOpacity(this._container, 1);
          }
          this.bringToFront();
          if (this.options.interactive) {
            addClass(this._container, "leaflet-interactive");
            this.addInteractiveTarget(this._container);
          }
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function(map2) {
          if (map2._fadeAnimated) {
            setOpacity(this._container, 0);
            this._removeTimeout = setTimeout(bind(remove, void 0, this._container), 200);
          } else {
            remove(this._container);
          }
          if (this.options.interactive) {
            removeClass(this._container, "leaflet-interactive");
            this.removeInteractiveTarget(this._container);
          }
        }, "onRemove"),
        // @namespace DivOverlay
        // @method getLatLng: LatLng
        // Returns the geographical point of the overlay.
        getLatLng: /* @__PURE__ */ __name(function() {
          return this._latlng;
        }, "getLatLng"),
        // @method setLatLng(latlng: LatLng): this
        // Sets the geographical point where the overlay will open.
        setLatLng: /* @__PURE__ */ __name(function(latlng) {
          this._latlng = toLatLng(latlng);
          if (this._map) {
            this._updatePosition();
            this._adjustPan();
          }
          return this;
        }, "setLatLng"),
        // @method getContent: String|HTMLElement
        // Returns the content of the overlay.
        getContent: /* @__PURE__ */ __name(function() {
          return this._content;
        }, "getContent"),
        // @method setContent(htmlContent: String|HTMLElement|Function): this
        // Sets the HTML content of the overlay. If a function is passed the source layer will be passed to the function.
        // The function should return a `String` or `HTMLElement` to be used in the overlay.
        setContent: /* @__PURE__ */ __name(function(content) {
          this._content = content;
          this.update();
          return this;
        }, "setContent"),
        // @method getElement: String|HTMLElement
        // Returns the HTML container of the overlay.
        getElement: /* @__PURE__ */ __name(function() {
          return this._container;
        }, "getElement"),
        // @method update: null
        // Updates the overlay content, layout and position. Useful for updating the overlay after something inside changed, e.g. image loaded.
        update: /* @__PURE__ */ __name(function() {
          if (!this._map) {
            return;
          }
          this._container.style.visibility = "hidden";
          this._updateContent();
          this._updateLayout();
          this._updatePosition();
          this._container.style.visibility = "";
          this._adjustPan();
        }, "update"),
        getEvents: /* @__PURE__ */ __name(function() {
          var events = {
            zoom: this._updatePosition,
            viewreset: this._updatePosition
          };
          if (this._zoomAnimated) {
            events.zoomanim = this._animateZoom;
          }
          return events;
        }, "getEvents"),
        // @method isOpen: Boolean
        // Returns `true` when the overlay is visible on the map.
        isOpen: /* @__PURE__ */ __name(function() {
          return !!this._map && this._map.hasLayer(this);
        }, "isOpen"),
        // @method bringToFront: this
        // Brings this overlay in front of other overlays (in the same map pane).
        bringToFront: /* @__PURE__ */ __name(function() {
          if (this._map) {
            toFront(this._container);
          }
          return this;
        }, "bringToFront"),
        // @method bringToBack: this
        // Brings this overlay to the back of other overlays (in the same map pane).
        bringToBack: /* @__PURE__ */ __name(function() {
          if (this._map) {
            toBack(this._container);
          }
          return this;
        }, "bringToBack"),
        // prepare bound overlay to open: update latlng pos / content source (for FeatureGroup)
        _prepareOpen: /* @__PURE__ */ __name(function(latlng) {
          var source = this._source;
          if (!source._map) {
            return false;
          }
          if (source instanceof FeatureGroup) {
            source = null;
            var layers2 = this._source._layers;
            for (var id in layers2) {
              if (layers2[id]._map) {
                source = layers2[id];
                break;
              }
            }
            if (!source) {
              return false;
            }
            this._source = source;
          }
          if (!latlng) {
            if (source.getCenter) {
              latlng = source.getCenter();
            } else if (source.getLatLng) {
              latlng = source.getLatLng();
            } else if (source.getBounds) {
              latlng = source.getBounds().getCenter();
            } else {
              throw new Error("Unable to get source layer LatLng.");
            }
          }
          this.setLatLng(latlng);
          if (this._map) {
            this.update();
          }
          return true;
        }, "_prepareOpen"),
        _updateContent: /* @__PURE__ */ __name(function() {
          if (!this._content) {
            return;
          }
          var node = this._contentNode;
          var content = typeof this._content === "function" ? this._content(this._source || this) : this._content;
          if (typeof content === "string") {
            node.innerHTML = content;
          } else {
            while (node.hasChildNodes()) {
              node.removeChild(node.firstChild);
            }
            node.appendChild(content);
          }
          this.fire("contentupdate");
        }, "_updateContent"),
        _updatePosition: /* @__PURE__ */ __name(function() {
          if (!this._map) {
            return;
          }
          var pos = this._map.latLngToLayerPoint(this._latlng), offset = toPoint(this.options.offset), anchor = this._getAnchor();
          if (this._zoomAnimated) {
            setPosition(this._container, pos.add(anchor));
          } else {
            offset = offset.add(pos).add(anchor);
          }
          var bottom = this._containerBottom = -offset.y, left = this._containerLeft = -Math.round(this._containerWidth / 2) + offset.x;
          this._container.style.bottom = bottom + "px";
          this._container.style.left = left + "px";
        }, "_updatePosition"),
        _getAnchor: /* @__PURE__ */ __name(function() {
          return [0, 0];
        }, "_getAnchor")
      });
      Map.include({
        _initOverlay: /* @__PURE__ */ __name(function(OverlayClass, content, latlng, options) {
          var overlay = content;
          if (!(overlay instanceof OverlayClass)) {
            overlay = new OverlayClass(options).setContent(content);
          }
          if (latlng) {
            overlay.setLatLng(latlng);
          }
          return overlay;
        }, "_initOverlay")
      });
      Layer.include({
        _initOverlay: /* @__PURE__ */ __name(function(OverlayClass, old, content, options) {
          var overlay = content;
          if (overlay instanceof OverlayClass) {
            setOptions(overlay, options);
            overlay._source = this;
          } else {
            overlay = old && !options ? old : new OverlayClass(options, this);
            overlay.setContent(content);
          }
          return overlay;
        }, "_initOverlay")
      });
      var Popup = DivOverlay.extend({
        // @section
        // @aka Popup options
        options: {
          // @option pane: String = 'popupPane'
          // `Map pane` where the popup will be added.
          pane: "popupPane",
          // @option offset: Point = Point(0, 7)
          // The offset of the popup position.
          offset: [0, 7],
          // @option maxWidth: Number = 300
          // Max width of the popup, in pixels.
          maxWidth: 300,
          // @option minWidth: Number = 50
          // Min width of the popup, in pixels.
          minWidth: 50,
          // @option maxHeight: Number = null
          // If set, creates a scrollable container of the given height
          // inside a popup if its content exceeds it.
          // The scrollable container can be styled using the
          // `leaflet-popup-scrolled` CSS class selector.
          maxHeight: null,
          // @option autoPan: Boolean = true
          // Set it to `false` if you don't want the map to do panning animation
          // to fit the opened popup.
          autoPan: true,
          // @option autoPanPaddingTopLeft: Point = null
          // The margin between the popup and the top left corner of the map
          // view after autopanning was performed.
          autoPanPaddingTopLeft: null,
          // @option autoPanPaddingBottomRight: Point = null
          // The margin between the popup and the bottom right corner of the map
          // view after autopanning was performed.
          autoPanPaddingBottomRight: null,
          // @option autoPanPadding: Point = Point(5, 5)
          // Equivalent of setting both top left and bottom right autopan padding to the same value.
          autoPanPadding: [5, 5],
          // @option keepInView: Boolean = false
          // Set it to `true` if you want to prevent users from panning the popup
          // off of the screen while it is open.
          keepInView: false,
          // @option closeButton: Boolean = true
          // Controls the presence of a close button in the popup.
          closeButton: true,
          // @option autoClose: Boolean = true
          // Set it to `false` if you want to override the default behavior of
          // the popup closing when another popup is opened.
          autoClose: true,
          // @option closeOnEscapeKey: Boolean = true
          // Set it to `false` if you want to override the default behavior of
          // the ESC key for closing of the popup.
          closeOnEscapeKey: true,
          // @option closeOnClick: Boolean = *
          // Set it if you want to override the default behavior of the popup closing when user clicks
          // on the map. Defaults to the map's [`closePopupOnClick`](#map-closepopuponclick) option.
          // @option className: String = ''
          // A custom CSS class name to assign to the popup.
          className: ""
        },
        // @namespace Popup
        // @method openOn(map: Map): this
        // Alternative to `map.openPopup(popup)`.
        // Adds the popup to the map and closes the previous one.
        openOn: /* @__PURE__ */ __name(function(map2) {
          map2 = arguments.length ? map2 : this._source._map;
          if (!map2.hasLayer(this) && map2._popup && map2._popup.options.autoClose) {
            map2.removeLayer(map2._popup);
          }
          map2._popup = this;
          return DivOverlay.prototype.openOn.call(this, map2);
        }, "openOn"),
        onAdd: /* @__PURE__ */ __name(function(map2) {
          DivOverlay.prototype.onAdd.call(this, map2);
          map2.fire("popupopen", { popup: this });
          if (this._source) {
            this._source.fire("popupopen", { popup: this }, true);
            if (!(this._source instanceof Path)) {
              this._source.on("preclick", stopPropagation);
            }
          }
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function(map2) {
          DivOverlay.prototype.onRemove.call(this, map2);
          map2.fire("popupclose", { popup: this });
          if (this._source) {
            this._source.fire("popupclose", { popup: this }, true);
            if (!(this._source instanceof Path)) {
              this._source.off("preclick", stopPropagation);
            }
          }
        }, "onRemove"),
        getEvents: /* @__PURE__ */ __name(function() {
          var events = DivOverlay.prototype.getEvents.call(this);
          if (this.options.closeOnClick !== void 0 ? this.options.closeOnClick : this._map.options.closePopupOnClick) {
            events.preclick = this.close;
          }
          if (this.options.keepInView) {
            events.moveend = this._adjustPan;
          }
          return events;
        }, "getEvents"),
        _initLayout: /* @__PURE__ */ __name(function() {
          var prefix = "leaflet-popup", container = this._container = create$1(
            "div",
            prefix + " " + (this.options.className || "") + " leaflet-zoom-animated"
          );
          var wrapper = this._wrapper = create$1("div", prefix + "-content-wrapper", container);
          this._contentNode = create$1("div", prefix + "-content", wrapper);
          disableClickPropagation(container);
          disableScrollPropagation(this._contentNode);
          on(container, "contextmenu", stopPropagation);
          this._tipContainer = create$1("div", prefix + "-tip-container", container);
          this._tip = create$1("div", prefix + "-tip", this._tipContainer);
          if (this.options.closeButton) {
            var closeButton = this._closeButton = create$1("a", prefix + "-close-button", container);
            closeButton.setAttribute("role", "button");
            closeButton.setAttribute("aria-label", "Close popup");
            closeButton.href = "#close";
            closeButton.innerHTML = '<span aria-hidden="true">&#215;</span>';
            on(closeButton, "click", function(ev) {
              preventDefault(ev);
              this.close();
            }, this);
          }
        }, "_initLayout"),
        _updateLayout: /* @__PURE__ */ __name(function() {
          var container = this._contentNode, style2 = container.style;
          style2.width = "";
          style2.whiteSpace = "nowrap";
          var width = container.offsetWidth;
          width = Math.min(width, this.options.maxWidth);
          width = Math.max(width, this.options.minWidth);
          style2.width = width + 1 + "px";
          style2.whiteSpace = "";
          style2.height = "";
          var height = container.offsetHeight, maxHeight = this.options.maxHeight, scrolledClass = "leaflet-popup-scrolled";
          if (maxHeight && height > maxHeight) {
            style2.height = maxHeight + "px";
            addClass(container, scrolledClass);
          } else {
            removeClass(container, scrolledClass);
          }
          this._containerWidth = this._container.offsetWidth;
        }, "_updateLayout"),
        _animateZoom: /* @__PURE__ */ __name(function(e) {
          var pos = this._map._latLngToNewLayerPoint(this._latlng, e.zoom, e.center), anchor = this._getAnchor();
          setPosition(this._container, pos.add(anchor));
        }, "_animateZoom"),
        _adjustPan: /* @__PURE__ */ __name(function() {
          if (!this.options.autoPan) {
            return;
          }
          if (this._map._panAnim) {
            this._map._panAnim.stop();
          }
          if (this._autopanning) {
            this._autopanning = false;
            return;
          }
          var map2 = this._map, marginBottom = parseInt(getStyle(this._container, "marginBottom"), 10) || 0, containerHeight = this._container.offsetHeight + marginBottom, containerWidth = this._containerWidth, layerPos = new Point(this._containerLeft, -containerHeight - this._containerBottom);
          layerPos._add(getPosition(this._container));
          var containerPos = map2.layerPointToContainerPoint(layerPos), padding = toPoint(this.options.autoPanPadding), paddingTL = toPoint(this.options.autoPanPaddingTopLeft || padding), paddingBR = toPoint(this.options.autoPanPaddingBottomRight || padding), size = map2.getSize(), dx = 0, dy = 0;
          if (containerPos.x + containerWidth + paddingBR.x > size.x) {
            dx = containerPos.x + containerWidth - size.x + paddingBR.x;
          }
          if (containerPos.x - dx - paddingTL.x < 0) {
            dx = containerPos.x - paddingTL.x;
          }
          if (containerPos.y + containerHeight + paddingBR.y > size.y) {
            dy = containerPos.y + containerHeight - size.y + paddingBR.y;
          }
          if (containerPos.y - dy - paddingTL.y < 0) {
            dy = containerPos.y - paddingTL.y;
          }
          if (dx || dy) {
            if (this.options.keepInView) {
              this._autopanning = true;
            }
            map2.fire("autopanstart").panBy([dx, dy]);
          }
        }, "_adjustPan"),
        _getAnchor: /* @__PURE__ */ __name(function() {
          return toPoint(this._source && this._source._getPopupAnchor ? this._source._getPopupAnchor() : [0, 0]);
        }, "_getAnchor")
      });
      var popup2 = /* @__PURE__ */ __name(function(options, source) {
        return new Popup(options, source);
      }, "popup");
      Map.mergeOptions({
        closePopupOnClick: true
      });
      Map.include({
        // @method openPopup(popup: Popup): this
        // Opens the specified popup while closing the previously opened (to make sure only one is opened at one time for usability).
        // @alternative
        // @method openPopup(content: String|HTMLElement, latlng: LatLng, options?: Popup options): this
        // Creates a popup with the specified content and options and opens it in the given point on a map.
        openPopup: /* @__PURE__ */ __name(function(popup3, latlng, options) {
          this._initOverlay(Popup, popup3, latlng, options).openOn(this);
          return this;
        }, "openPopup"),
        // @method closePopup(popup?: Popup): this
        // Closes the popup previously opened with [openPopup](#map-openpopup) (or the given one).
        closePopup: /* @__PURE__ */ __name(function(popup3) {
          popup3 = arguments.length ? popup3 : this._popup;
          if (popup3) {
            popup3.close();
          }
          return this;
        }, "closePopup")
      });
      Layer.include({
        // @method bindPopup(content: String|HTMLElement|Function|Popup, options?: Popup options): this
        // Binds a popup to the layer with the passed `content` and sets up the
        // necessary event listeners. If a `Function` is passed it will receive
        // the layer as the first argument and should return a `String` or `HTMLElement`.
        bindPopup: /* @__PURE__ */ __name(function(content, options) {
          this._popup = this._initOverlay(Popup, this._popup, content, options);
          if (!this._popupHandlersAdded) {
            this.on({
              click: this._openPopup,
              keypress: this._onKeyPress,
              remove: this.closePopup,
              move: this._movePopup
            });
            this._popupHandlersAdded = true;
          }
          return this;
        }, "bindPopup"),
        // @method unbindPopup(): this
        // Removes the popup previously bound with `bindPopup`.
        unbindPopup: /* @__PURE__ */ __name(function() {
          if (this._popup) {
            this.off({
              click: this._openPopup,
              keypress: this._onKeyPress,
              remove: this.closePopup,
              move: this._movePopup
            });
            this._popupHandlersAdded = false;
            this._popup = null;
          }
          return this;
        }, "unbindPopup"),
        // @method openPopup(latlng?: LatLng): this
        // Opens the bound popup at the specified `latlng` or at the default popup anchor if no `latlng` is passed.
        openPopup: /* @__PURE__ */ __name(function(latlng) {
          if (this._popup) {
            if (!(this instanceof FeatureGroup)) {
              this._popup._source = this;
            }
            if (this._popup._prepareOpen(latlng || this._latlng)) {
              this._popup.openOn(this._map);
            }
          }
          return this;
        }, "openPopup"),
        // @method closePopup(): this
        // Closes the popup bound to this layer if it is open.
        closePopup: /* @__PURE__ */ __name(function() {
          if (this._popup) {
            this._popup.close();
          }
          return this;
        }, "closePopup"),
        // @method togglePopup(): this
        // Opens or closes the popup bound to this layer depending on its current state.
        togglePopup: /* @__PURE__ */ __name(function() {
          if (this._popup) {
            this._popup.toggle(this);
          }
          return this;
        }, "togglePopup"),
        // @method isPopupOpen(): boolean
        // Returns `true` if the popup bound to this layer is currently open.
        isPopupOpen: /* @__PURE__ */ __name(function() {
          return this._popup ? this._popup.isOpen() : false;
        }, "isPopupOpen"),
        // @method setPopupContent(content: String|HTMLElement|Popup): this
        // Sets the content of the popup bound to this layer.
        setPopupContent: /* @__PURE__ */ __name(function(content) {
          if (this._popup) {
            this._popup.setContent(content);
          }
          return this;
        }, "setPopupContent"),
        // @method getPopup(): Popup
        // Returns the popup bound to this layer.
        getPopup: /* @__PURE__ */ __name(function() {
          return this._popup;
        }, "getPopup"),
        _openPopup: /* @__PURE__ */ __name(function(e) {
          if (!this._popup || !this._map) {
            return;
          }
          stop(e);
          var target = e.layer || e.target;
          if (this._popup._source === target && !(target instanceof Path)) {
            if (this._map.hasLayer(this._popup)) {
              this.closePopup();
            } else {
              this.openPopup(e.latlng);
            }
            return;
          }
          this._popup._source = target;
          this.openPopup(e.latlng);
        }, "_openPopup"),
        _movePopup: /* @__PURE__ */ __name(function(e) {
          this._popup.setLatLng(e.latlng);
        }, "_movePopup"),
        _onKeyPress: /* @__PURE__ */ __name(function(e) {
          if (e.originalEvent.keyCode === 13) {
            this._openPopup(e);
          }
        }, "_onKeyPress")
      });
      var Tooltip = DivOverlay.extend({
        // @section
        // @aka Tooltip options
        options: {
          // @option pane: String = 'tooltipPane'
          // `Map pane` where the tooltip will be added.
          pane: "tooltipPane",
          // @option offset: Point = Point(0, 0)
          // Optional offset of the tooltip position.
          offset: [0, 0],
          // @option direction: String = 'auto'
          // Direction where to open the tooltip. Possible values are: `right`, `left`,
          // `top`, `bottom`, `center`, `auto`.
          // `auto` will dynamically switch between `right` and `left` according to the tooltip
          // position on the map.
          direction: "auto",
          // @option permanent: Boolean = false
          // Whether to open the tooltip permanently or only on mouseover.
          permanent: false,
          // @option sticky: Boolean = false
          // If true, the tooltip will follow the mouse instead of being fixed at the feature center.
          sticky: false,
          // @option opacity: Number = 0.9
          // Tooltip container opacity.
          opacity: 0.9
        },
        onAdd: /* @__PURE__ */ __name(function(map2) {
          DivOverlay.prototype.onAdd.call(this, map2);
          this.setOpacity(this.options.opacity);
          map2.fire("tooltipopen", { tooltip: this });
          if (this._source) {
            this.addEventParent(this._source);
            this._source.fire("tooltipopen", { tooltip: this }, true);
          }
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function(map2) {
          DivOverlay.prototype.onRemove.call(this, map2);
          map2.fire("tooltipclose", { tooltip: this });
          if (this._source) {
            this.removeEventParent(this._source);
            this._source.fire("tooltipclose", { tooltip: this }, true);
          }
        }, "onRemove"),
        getEvents: /* @__PURE__ */ __name(function() {
          var events = DivOverlay.prototype.getEvents.call(this);
          if (!this.options.permanent) {
            events.preclick = this.close;
          }
          return events;
        }, "getEvents"),
        _initLayout: /* @__PURE__ */ __name(function() {
          var prefix = "leaflet-tooltip", className = prefix + " " + (this.options.className || "") + " leaflet-zoom-" + (this._zoomAnimated ? "animated" : "hide");
          this._contentNode = this._container = create$1("div", className);
          this._container.setAttribute("role", "tooltip");
          this._container.setAttribute("id", "leaflet-tooltip-" + stamp(this));
        }, "_initLayout"),
        _updateLayout: /* @__PURE__ */ __name(function() {
        }, "_updateLayout"),
        _adjustPan: /* @__PURE__ */ __name(function() {
        }, "_adjustPan"),
        _setPosition: /* @__PURE__ */ __name(function(pos) {
          var subX, subY, map2 = this._map, container = this._container, centerPoint = map2.latLngToContainerPoint(map2.getCenter()), tooltipPoint = map2.layerPointToContainerPoint(pos), direction = this.options.direction, tooltipWidth = container.offsetWidth, tooltipHeight = container.offsetHeight, offset = toPoint(this.options.offset), anchor = this._getAnchor();
          if (direction === "top") {
            subX = tooltipWidth / 2;
            subY = tooltipHeight;
          } else if (direction === "bottom") {
            subX = tooltipWidth / 2;
            subY = 0;
          } else if (direction === "center") {
            subX = tooltipWidth / 2;
            subY = tooltipHeight / 2;
          } else if (direction === "right") {
            subX = 0;
            subY = tooltipHeight / 2;
          } else if (direction === "left") {
            subX = tooltipWidth;
            subY = tooltipHeight / 2;
          } else if (tooltipPoint.x < centerPoint.x) {
            direction = "right";
            subX = 0;
            subY = tooltipHeight / 2;
          } else {
            direction = "left";
            subX = tooltipWidth + (offset.x + anchor.x) * 2;
            subY = tooltipHeight / 2;
          }
          pos = pos.subtract(toPoint(subX, subY, true)).add(offset).add(anchor);
          removeClass(container, "leaflet-tooltip-right");
          removeClass(container, "leaflet-tooltip-left");
          removeClass(container, "leaflet-tooltip-top");
          removeClass(container, "leaflet-tooltip-bottom");
          addClass(container, "leaflet-tooltip-" + direction);
          setPosition(container, pos);
        }, "_setPosition"),
        _updatePosition: /* @__PURE__ */ __name(function() {
          var pos = this._map.latLngToLayerPoint(this._latlng);
          this._setPosition(pos);
        }, "_updatePosition"),
        setOpacity: /* @__PURE__ */ __name(function(opacity) {
          this.options.opacity = opacity;
          if (this._container) {
            setOpacity(this._container, opacity);
          }
        }, "setOpacity"),
        _animateZoom: /* @__PURE__ */ __name(function(e) {
          var pos = this._map._latLngToNewLayerPoint(this._latlng, e.zoom, e.center);
          this._setPosition(pos);
        }, "_animateZoom"),
        _getAnchor: /* @__PURE__ */ __name(function() {
          return toPoint(this._source && this._source._getTooltipAnchor && !this.options.sticky ? this._source._getTooltipAnchor() : [0, 0]);
        }, "_getAnchor")
      });
      var tooltip = /* @__PURE__ */ __name(function(options, source) {
        return new Tooltip(options, source);
      }, "tooltip");
      Map.include({
        // @method openTooltip(tooltip: Tooltip): this
        // Opens the specified tooltip.
        // @alternative
        // @method openTooltip(content: String|HTMLElement, latlng: LatLng, options?: Tooltip options): this
        // Creates a tooltip with the specified content and options and open it.
        openTooltip: /* @__PURE__ */ __name(function(tooltip2, latlng, options) {
          this._initOverlay(Tooltip, tooltip2, latlng, options).openOn(this);
          return this;
        }, "openTooltip"),
        // @method closeTooltip(tooltip: Tooltip): this
        // Closes the tooltip given as parameter.
        closeTooltip: /* @__PURE__ */ __name(function(tooltip2) {
          tooltip2.close();
          return this;
        }, "closeTooltip")
      });
      Layer.include({
        // @method bindTooltip(content: String|HTMLElement|Function|Tooltip, options?: Tooltip options): this
        // Binds a tooltip to the layer with the passed `content` and sets up the
        // necessary event listeners. If a `Function` is passed it will receive
        // the layer as the first argument and should return a `String` or `HTMLElement`.
        bindTooltip: /* @__PURE__ */ __name(function(content, options) {
          if (this._tooltip && this.isTooltipOpen()) {
            this.unbindTooltip();
          }
          this._tooltip = this._initOverlay(Tooltip, this._tooltip, content, options);
          this._initTooltipInteractions();
          if (this._tooltip.options.permanent && this._map && this._map.hasLayer(this)) {
            this.openTooltip();
          }
          return this;
        }, "bindTooltip"),
        // @method unbindTooltip(): this
        // Removes the tooltip previously bound with `bindTooltip`.
        unbindTooltip: /* @__PURE__ */ __name(function() {
          if (this._tooltip) {
            this._initTooltipInteractions(true);
            this.closeTooltip();
            this._tooltip = null;
          }
          return this;
        }, "unbindTooltip"),
        _initTooltipInteractions: /* @__PURE__ */ __name(function(remove2) {
          if (!remove2 && this._tooltipHandlersAdded) {
            return;
          }
          var onOff = remove2 ? "off" : "on", events = {
            remove: this.closeTooltip,
            move: this._moveTooltip
          };
          if (!this._tooltip.options.permanent) {
            events.mouseover = this._openTooltip;
            events.mouseout = this.closeTooltip;
            events.click = this._openTooltip;
            if (this._map) {
              this._addFocusListeners();
            } else {
              events.add = this._addFocusListeners;
            }
          } else {
            events.add = this._openTooltip;
          }
          if (this._tooltip.options.sticky) {
            events.mousemove = this._moveTooltip;
          }
          this[onOff](events);
          this._tooltipHandlersAdded = !remove2;
        }, "_initTooltipInteractions"),
        // @method openTooltip(latlng?: LatLng): this
        // Opens the bound tooltip at the specified `latlng` or at the default tooltip anchor if no `latlng` is passed.
        openTooltip: /* @__PURE__ */ __name(function(latlng) {
          if (this._tooltip) {
            if (!(this instanceof FeatureGroup)) {
              this._tooltip._source = this;
            }
            if (this._tooltip._prepareOpen(latlng)) {
              this._tooltip.openOn(this._map);
              if (this.getElement) {
                this._setAriaDescribedByOnLayer(this);
              } else if (this.eachLayer) {
                this.eachLayer(this._setAriaDescribedByOnLayer, this);
              }
            }
          }
          return this;
        }, "openTooltip"),
        // @method closeTooltip(): this
        // Closes the tooltip bound to this layer if it is open.
        closeTooltip: /* @__PURE__ */ __name(function() {
          if (this._tooltip) {
            return this._tooltip.close();
          }
        }, "closeTooltip"),
        // @method toggleTooltip(): this
        // Opens or closes the tooltip bound to this layer depending on its current state.
        toggleTooltip: /* @__PURE__ */ __name(function() {
          if (this._tooltip) {
            this._tooltip.toggle(this);
          }
          return this;
        }, "toggleTooltip"),
        // @method isTooltipOpen(): boolean
        // Returns `true` if the tooltip bound to this layer is currently open.
        isTooltipOpen: /* @__PURE__ */ __name(function() {
          return this._tooltip.isOpen();
        }, "isTooltipOpen"),
        // @method setTooltipContent(content: String|HTMLElement|Tooltip): this
        // Sets the content of the tooltip bound to this layer.
        setTooltipContent: /* @__PURE__ */ __name(function(content) {
          if (this._tooltip) {
            this._tooltip.setContent(content);
          }
          return this;
        }, "setTooltipContent"),
        // @method getTooltip(): Tooltip
        // Returns the tooltip bound to this layer.
        getTooltip: /* @__PURE__ */ __name(function() {
          return this._tooltip;
        }, "getTooltip"),
        _addFocusListeners: /* @__PURE__ */ __name(function() {
          if (this.getElement) {
            this._addFocusListenersOnLayer(this);
          } else if (this.eachLayer) {
            this.eachLayer(this._addFocusListenersOnLayer, this);
          }
        }, "_addFocusListeners"),
        _addFocusListenersOnLayer: /* @__PURE__ */ __name(function(layer) {
          var el = typeof layer.getElement === "function" && layer.getElement();
          if (el) {
            on(el, "focus", function() {
              this._tooltip._source = layer;
              this.openTooltip();
            }, this);
            on(el, "blur", this.closeTooltip, this);
          }
        }, "_addFocusListenersOnLayer"),
        _setAriaDescribedByOnLayer: /* @__PURE__ */ __name(function(layer) {
          var el = typeof layer.getElement === "function" && layer.getElement();
          if (el) {
            el.setAttribute("aria-describedby", this._tooltip._container.id);
          }
        }, "_setAriaDescribedByOnLayer"),
        _openTooltip: /* @__PURE__ */ __name(function(e) {
          if (!this._tooltip || !this._map) {
            return;
          }
          if (this._map.dragging && this._map.dragging.moving() && !this._openOnceFlag) {
            this._openOnceFlag = true;
            var that = this;
            this._map.once("moveend", function() {
              that._openOnceFlag = false;
              that._openTooltip(e);
            });
            return;
          }
          this._tooltip._source = e.layer || e.target;
          this.openTooltip(this._tooltip.options.sticky ? e.latlng : void 0);
        }, "_openTooltip"),
        _moveTooltip: /* @__PURE__ */ __name(function(e) {
          var latlng = e.latlng, containerPoint, layerPoint;
          if (this._tooltip.options.sticky && e.originalEvent) {
            containerPoint = this._map.mouseEventToContainerPoint(e.originalEvent);
            layerPoint = this._map.containerPointToLayerPoint(containerPoint);
            latlng = this._map.layerPointToLatLng(layerPoint);
          }
          this._tooltip.setLatLng(latlng);
        }, "_moveTooltip")
      });
      var DivIcon = Icon.extend({
        options: {
          // @section
          // @aka DivIcon options
          iconSize: [12, 12],
          // also can be set through CSS
          // iconAnchor: (Point),
          // popupAnchor: (Point),
          // @option html: String|HTMLElement = ''
          // Custom HTML code to put inside the div element, empty by default. Alternatively,
          // an instance of `HTMLElement`.
          html: false,
          // @option bgPos: Point = [0, 0]
          // Optional relative position of the background, in pixels
          bgPos: null,
          className: "leaflet-div-icon"
        },
        createIcon: /* @__PURE__ */ __name(function(oldIcon) {
          var div = oldIcon && oldIcon.tagName === "DIV" ? oldIcon : document.createElement("div"), options = this.options;
          if (options.html instanceof Element) {
            empty(div);
            div.appendChild(options.html);
          } else {
            div.innerHTML = options.html !== false ? options.html : "";
          }
          if (options.bgPos) {
            var bgPos = toPoint(options.bgPos);
            div.style.backgroundPosition = -bgPos.x + "px " + -bgPos.y + "px";
          }
          this._setIconStyles(div, "icon");
          return div;
        }, "createIcon"),
        createShadow: /* @__PURE__ */ __name(function() {
          return null;
        }, "createShadow")
      });
      function divIcon(options) {
        return new DivIcon(options);
      }
      __name(divIcon, "divIcon");
      Icon.Default = IconDefault;
      var GridLayer = Layer.extend({
        // @section
        // @aka GridLayer options
        options: {
          // @option tileSize: Number|Point = 256
          // Width and height of tiles in the grid. Use a number if width and height are equal, or `L.point(width, height)` otherwise.
          tileSize: 256,
          // @option opacity: Number = 1.0
          // Opacity of the tiles. Can be used in the `createTile()` function.
          opacity: 1,
          // @option updateWhenIdle: Boolean = (depends)
          // Load new tiles only when panning ends.
          // `true` by default on mobile browsers, in order to avoid too many requests and keep smooth navigation.
          // `false` otherwise in order to display new tiles _during_ panning, since it is easy to pan outside the
          // [`keepBuffer`](#gridlayer-keepbuffer) option in desktop browsers.
          updateWhenIdle: Browser.mobile,
          // @option updateWhenZooming: Boolean = true
          // By default, a smooth zoom animation (during a [touch zoom](#map-touchzoom) or a [`flyTo()`](#map-flyto)) will update grid layers every integer zoom level. Setting this option to `false` will update the grid layer only when the smooth animation ends.
          updateWhenZooming: true,
          // @option updateInterval: Number = 200
          // Tiles will not update more than once every `updateInterval` milliseconds when panning.
          updateInterval: 200,
          // @option zIndex: Number = 1
          // The explicit zIndex of the tile layer.
          zIndex: 1,
          // @option bounds: LatLngBounds = undefined
          // If set, tiles will only be loaded inside the set `LatLngBounds`.
          bounds: null,
          // @option minZoom: Number = 0
          // The minimum zoom level down to which this layer will be displayed (inclusive).
          minZoom: 0,
          // @option maxZoom: Number = undefined
          // The maximum zoom level up to which this layer will be displayed (inclusive).
          maxZoom: void 0,
          // @option maxNativeZoom: Number = undefined
          // Maximum zoom number the tile source has available. If it is specified,
          // the tiles on all zoom levels higher than `maxNativeZoom` will be loaded
          // from `maxNativeZoom` level and auto-scaled.
          maxNativeZoom: void 0,
          // @option minNativeZoom: Number = undefined
          // Minimum zoom number the tile source has available. If it is specified,
          // the tiles on all zoom levels lower than `minNativeZoom` will be loaded
          // from `minNativeZoom` level and auto-scaled.
          minNativeZoom: void 0,
          // @option noWrap: Boolean = false
          // Whether the layer is wrapped around the antimeridian. If `true`, the
          // GridLayer will only be displayed once at low zoom levels. Has no
          // effect when the [map CRS](#map-crs) doesn't wrap around. Can be used
          // in combination with [`bounds`](#gridlayer-bounds) to prevent requesting
          // tiles outside the CRS limits.
          noWrap: false,
          // @option pane: String = 'tilePane'
          // `Map pane` where the grid layer will be added.
          pane: "tilePane",
          // @option className: String = ''
          // A custom class name to assign to the tile layer. Empty by default.
          className: "",
          // @option keepBuffer: Number = 2
          // When panning the map, keep this many rows and columns of tiles before unloading them.
          keepBuffer: 2
        },
        initialize: /* @__PURE__ */ __name(function(options) {
          setOptions(this, options);
        }, "initialize"),
        onAdd: /* @__PURE__ */ __name(function() {
          this._initContainer();
          this._levels = {};
          this._tiles = {};
          this._resetView();
        }, "onAdd"),
        beforeAdd: /* @__PURE__ */ __name(function(map2) {
          map2._addZoomLimit(this);
        }, "beforeAdd"),
        onRemove: /* @__PURE__ */ __name(function(map2) {
          this._removeAllTiles();
          remove(this._container);
          map2._removeZoomLimit(this);
          this._container = null;
          this._tileZoom = void 0;
        }, "onRemove"),
        // @method bringToFront: this
        // Brings the tile layer to the top of all tile layers.
        bringToFront: /* @__PURE__ */ __name(function() {
          if (this._map) {
            toFront(this._container);
            this._setAutoZIndex(Math.max);
          }
          return this;
        }, "bringToFront"),
        // @method bringToBack: this
        // Brings the tile layer to the bottom of all tile layers.
        bringToBack: /* @__PURE__ */ __name(function() {
          if (this._map) {
            toBack(this._container);
            this._setAutoZIndex(Math.min);
          }
          return this;
        }, "bringToBack"),
        // @method getContainer: HTMLElement
        // Returns the HTML element that contains the tiles for this layer.
        getContainer: /* @__PURE__ */ __name(function() {
          return this._container;
        }, "getContainer"),
        // @method setOpacity(opacity: Number): this
        // Changes the [opacity](#gridlayer-opacity) of the grid layer.
        setOpacity: /* @__PURE__ */ __name(function(opacity) {
          this.options.opacity = opacity;
          this._updateOpacity();
          return this;
        }, "setOpacity"),
        // @method setZIndex(zIndex: Number): this
        // Changes the [zIndex](#gridlayer-zindex) of the grid layer.
        setZIndex: /* @__PURE__ */ __name(function(zIndex) {
          this.options.zIndex = zIndex;
          this._updateZIndex();
          return this;
        }, "setZIndex"),
        // @method isLoading: Boolean
        // Returns `true` if any tile in the grid layer has not finished loading.
        isLoading: /* @__PURE__ */ __name(function() {
          return this._loading;
        }, "isLoading"),
        // @method redraw: this
        // Causes the layer to clear all the tiles and request them again.
        redraw: /* @__PURE__ */ __name(function() {
          if (this._map) {
            this._removeAllTiles();
            var tileZoom = this._clampZoom(this._map.getZoom());
            if (tileZoom !== this._tileZoom) {
              this._tileZoom = tileZoom;
              this._updateLevels();
            }
            this._update();
          }
          return this;
        }, "redraw"),
        getEvents: /* @__PURE__ */ __name(function() {
          var events = {
            viewprereset: this._invalidateAll,
            viewreset: this._resetView,
            zoom: this._resetView,
            moveend: this._onMoveEnd
          };
          if (!this.options.updateWhenIdle) {
            if (!this._onMove) {
              this._onMove = throttle(this._onMoveEnd, this.options.updateInterval, this);
            }
            events.move = this._onMove;
          }
          if (this._zoomAnimated) {
            events.zoomanim = this._animateZoom;
          }
          return events;
        }, "getEvents"),
        // @section Extension methods
        // Layers extending `GridLayer` shall reimplement the following method.
        // @method createTile(coords: Object, done?: Function): HTMLElement
        // Called only internally, must be overridden by classes extending `GridLayer`.
        // Returns the `HTMLElement` corresponding to the given `coords`. If the `done` callback
        // is specified, it must be called when the tile has finished loading and drawing.
        createTile: /* @__PURE__ */ __name(function() {
          return document.createElement("div");
        }, "createTile"),
        // @section
        // @method getTileSize: Point
        // Normalizes the [tileSize option](#gridlayer-tilesize) into a point. Used by the `createTile()` method.
        getTileSize: /* @__PURE__ */ __name(function() {
          var s = this.options.tileSize;
          return s instanceof Point ? s : new Point(s, s);
        }, "getTileSize"),
        _updateZIndex: /* @__PURE__ */ __name(function() {
          if (this._container && this.options.zIndex !== void 0 && this.options.zIndex !== null) {
            this._container.style.zIndex = this.options.zIndex;
          }
        }, "_updateZIndex"),
        _setAutoZIndex: /* @__PURE__ */ __name(function(compare) {
          var layers2 = this.getPane().children, edgeZIndex = -compare(-Infinity, Infinity);
          for (var i = 0, len = layers2.length, zIndex; i < len; i++) {
            zIndex = layers2[i].style.zIndex;
            if (layers2[i] !== this._container && zIndex) {
              edgeZIndex = compare(edgeZIndex, +zIndex);
            }
          }
          if (isFinite(edgeZIndex)) {
            this.options.zIndex = edgeZIndex + compare(-1, 1);
            this._updateZIndex();
          }
        }, "_setAutoZIndex"),
        _updateOpacity: /* @__PURE__ */ __name(function() {
          if (!this._map) {
            return;
          }
          if (Browser.ielt9) {
            return;
          }
          setOpacity(this._container, this.options.opacity);
          var now = +/* @__PURE__ */ new Date(), nextFrame = false, willPrune = false;
          for (var key in this._tiles) {
            var tile = this._tiles[key];
            if (!tile.current || !tile.loaded) {
              continue;
            }
            var fade = Math.min(1, (now - tile.loaded) / 200);
            setOpacity(tile.el, fade);
            if (fade < 1) {
              nextFrame = true;
            } else {
              if (tile.active) {
                willPrune = true;
              } else {
                this._onOpaqueTile(tile);
              }
              tile.active = true;
            }
          }
          if (willPrune && !this._noPrune) {
            this._pruneTiles();
          }
          if (nextFrame) {
            cancelAnimFrame(this._fadeFrame);
            this._fadeFrame = requestAnimFrame(this._updateOpacity, this);
          }
        }, "_updateOpacity"),
        _onOpaqueTile: falseFn,
        _initContainer: /* @__PURE__ */ __name(function() {
          if (this._container) {
            return;
          }
          this._container = create$1("div", "leaflet-layer " + (this.options.className || ""));
          this._updateZIndex();
          if (this.options.opacity < 1) {
            this._updateOpacity();
          }
          this.getPane().appendChild(this._container);
        }, "_initContainer"),
        _updateLevels: /* @__PURE__ */ __name(function() {
          var zoom2 = this._tileZoom, maxZoom = this.options.maxZoom;
          if (zoom2 === void 0) {
            return void 0;
          }
          for (var z in this._levels) {
            z = Number(z);
            if (this._levels[z].el.children.length || z === zoom2) {
              this._levels[z].el.style.zIndex = maxZoom - Math.abs(zoom2 - z);
              this._onUpdateLevel(z);
            } else {
              remove(this._levels[z].el);
              this._removeTilesAtZoom(z);
              this._onRemoveLevel(z);
              delete this._levels[z];
            }
          }
          var level = this._levels[zoom2], map2 = this._map;
          if (!level) {
            level = this._levels[zoom2] = {};
            level.el = create$1("div", "leaflet-tile-container leaflet-zoom-animated", this._container);
            level.el.style.zIndex = maxZoom;
            level.origin = map2.project(map2.unproject(map2.getPixelOrigin()), zoom2).round();
            level.zoom = zoom2;
            this._setZoomTransform(level, map2.getCenter(), map2.getZoom());
            falseFn(level.el.offsetWidth);
            this._onCreateLevel(level);
          }
          this._level = level;
          return level;
        }, "_updateLevels"),
        _onUpdateLevel: falseFn,
        _onRemoveLevel: falseFn,
        _onCreateLevel: falseFn,
        _pruneTiles: /* @__PURE__ */ __name(function() {
          if (!this._map) {
            return;
          }
          var key, tile;
          var zoom2 = this._map.getZoom();
          if (zoom2 > this.options.maxZoom || zoom2 < this.options.minZoom) {
            this._removeAllTiles();
            return;
          }
          for (key in this._tiles) {
            tile = this._tiles[key];
            tile.retain = tile.current;
          }
          for (key in this._tiles) {
            tile = this._tiles[key];
            if (tile.current && !tile.active) {
              var coords = tile.coords;
              if (!this._retainParent(coords.x, coords.y, coords.z, coords.z - 5)) {
                this._retainChildren(coords.x, coords.y, coords.z, coords.z + 2);
              }
            }
          }
          for (key in this._tiles) {
            if (!this._tiles[key].retain) {
              this._removeTile(key);
            }
          }
        }, "_pruneTiles"),
        _removeTilesAtZoom: /* @__PURE__ */ __name(function(zoom2) {
          for (var key in this._tiles) {
            if (this._tiles[key].coords.z !== zoom2) {
              continue;
            }
            this._removeTile(key);
          }
        }, "_removeTilesAtZoom"),
        _removeAllTiles: /* @__PURE__ */ __name(function() {
          for (var key in this._tiles) {
            this._removeTile(key);
          }
        }, "_removeAllTiles"),
        _invalidateAll: /* @__PURE__ */ __name(function() {
          for (var z in this._levels) {
            remove(this._levels[z].el);
            this._onRemoveLevel(Number(z));
            delete this._levels[z];
          }
          this._removeAllTiles();
          this._tileZoom = void 0;
        }, "_invalidateAll"),
        _retainParent: /* @__PURE__ */ __name(function(x, y, z, minZoom) {
          var x2 = Math.floor(x / 2), y2 = Math.floor(y / 2), z2 = z - 1, coords2 = new Point(+x2, +y2);
          coords2.z = +z2;
          var key = this._tileCoordsToKey(coords2), tile = this._tiles[key];
          if (tile && tile.active) {
            tile.retain = true;
            return true;
          } else if (tile && tile.loaded) {
            tile.retain = true;
          }
          if (z2 > minZoom) {
            return this._retainParent(x2, y2, z2, minZoom);
          }
          return false;
        }, "_retainParent"),
        _retainChildren: /* @__PURE__ */ __name(function(x, y, z, maxZoom) {
          for (var i = 2 * x; i < 2 * x + 2; i++) {
            for (var j = 2 * y; j < 2 * y + 2; j++) {
              var coords = new Point(i, j);
              coords.z = z + 1;
              var key = this._tileCoordsToKey(coords), tile = this._tiles[key];
              if (tile && tile.active) {
                tile.retain = true;
                continue;
              } else if (tile && tile.loaded) {
                tile.retain = true;
              }
              if (z + 1 < maxZoom) {
                this._retainChildren(i, j, z + 1, maxZoom);
              }
            }
          }
        }, "_retainChildren"),
        _resetView: /* @__PURE__ */ __name(function(e) {
          var animating = e && (e.pinch || e.flyTo);
          this._setView(this._map.getCenter(), this._map.getZoom(), animating, animating);
        }, "_resetView"),
        _animateZoom: /* @__PURE__ */ __name(function(e) {
          this._setView(e.center, e.zoom, true, e.noUpdate);
        }, "_animateZoom"),
        _clampZoom: /* @__PURE__ */ __name(function(zoom2) {
          var options = this.options;
          if (void 0 !== options.minNativeZoom && zoom2 < options.minNativeZoom) {
            return options.minNativeZoom;
          }
          if (void 0 !== options.maxNativeZoom && options.maxNativeZoom < zoom2) {
            return options.maxNativeZoom;
          }
          return zoom2;
        }, "_clampZoom"),
        _setView: /* @__PURE__ */ __name(function(center, zoom2, noPrune, noUpdate) {
          var tileZoom = Math.round(zoom2);
          if (this.options.maxZoom !== void 0 && tileZoom > this.options.maxZoom || this.options.minZoom !== void 0 && tileZoom < this.options.minZoom) {
            tileZoom = void 0;
          } else {
            tileZoom = this._clampZoom(tileZoom);
          }
          var tileZoomChanged = this.options.updateWhenZooming && tileZoom !== this._tileZoom;
          if (!noUpdate || tileZoomChanged) {
            this._tileZoom = tileZoom;
            if (this._abortLoading) {
              this._abortLoading();
            }
            this._updateLevels();
            this._resetGrid();
            if (tileZoom !== void 0) {
              this._update(center);
            }
            if (!noPrune) {
              this._pruneTiles();
            }
            this._noPrune = !!noPrune;
          }
          this._setZoomTransforms(center, zoom2);
        }, "_setView"),
        _setZoomTransforms: /* @__PURE__ */ __name(function(center, zoom2) {
          for (var i in this._levels) {
            this._setZoomTransform(this._levels[i], center, zoom2);
          }
        }, "_setZoomTransforms"),
        _setZoomTransform: /* @__PURE__ */ __name(function(level, center, zoom2) {
          var scale2 = this._map.getZoomScale(zoom2, level.zoom), translate = level.origin.multiplyBy(scale2).subtract(this._map._getNewPixelOrigin(center, zoom2)).round();
          if (Browser.any3d) {
            setTransform(level.el, translate, scale2);
          } else {
            setPosition(level.el, translate);
          }
        }, "_setZoomTransform"),
        _resetGrid: /* @__PURE__ */ __name(function() {
          var map2 = this._map, crs = map2.options.crs, tileSize = this._tileSize = this.getTileSize(), tileZoom = this._tileZoom;
          var bounds = this._map.getPixelWorldBounds(this._tileZoom);
          if (bounds) {
            this._globalTileRange = this._pxBoundsToTileRange(bounds);
          }
          this._wrapX = crs.wrapLng && !this.options.noWrap && [
            Math.floor(map2.project([0, crs.wrapLng[0]], tileZoom).x / tileSize.x),
            Math.ceil(map2.project([0, crs.wrapLng[1]], tileZoom).x / tileSize.y)
          ];
          this._wrapY = crs.wrapLat && !this.options.noWrap && [
            Math.floor(map2.project([crs.wrapLat[0], 0], tileZoom).y / tileSize.x),
            Math.ceil(map2.project([crs.wrapLat[1], 0], tileZoom).y / tileSize.y)
          ];
        }, "_resetGrid"),
        _onMoveEnd: /* @__PURE__ */ __name(function() {
          if (!this._map || this._map._animatingZoom) {
            return;
          }
          this._update();
        }, "_onMoveEnd"),
        _getTiledPixelBounds: /* @__PURE__ */ __name(function(center) {
          var map2 = this._map, mapZoom = map2._animatingZoom ? Math.max(map2._animateToZoom, map2.getZoom()) : map2.getZoom(), scale2 = map2.getZoomScale(mapZoom, this._tileZoom), pixelCenter = map2.project(center, this._tileZoom).floor(), halfSize = map2.getSize().divideBy(scale2 * 2);
          return new Bounds(pixelCenter.subtract(halfSize), pixelCenter.add(halfSize));
        }, "_getTiledPixelBounds"),
        // Private method to load tiles in the grid's active zoom level according to map bounds
        _update: /* @__PURE__ */ __name(function(center) {
          var map2 = this._map;
          if (!map2) {
            return;
          }
          var zoom2 = this._clampZoom(map2.getZoom());
          if (center === void 0) {
            center = map2.getCenter();
          }
          if (this._tileZoom === void 0) {
            return;
          }
          var pixelBounds = this._getTiledPixelBounds(center), tileRange = this._pxBoundsToTileRange(pixelBounds), tileCenter = tileRange.getCenter(), queue = [], margin = this.options.keepBuffer, noPruneRange = new Bounds(
            tileRange.getBottomLeft().subtract([margin, -margin]),
            tileRange.getTopRight().add([margin, -margin])
          );
          if (!(isFinite(tileRange.min.x) && isFinite(tileRange.min.y) && isFinite(tileRange.max.x) && isFinite(tileRange.max.y))) {
            throw new Error("Attempted to load an infinite number of tiles");
          }
          for (var key in this._tiles) {
            var c = this._tiles[key].coords;
            if (c.z !== this._tileZoom || !noPruneRange.contains(new Point(c.x, c.y))) {
              this._tiles[key].current = false;
            }
          }
          if (Math.abs(zoom2 - this._tileZoom) > 1) {
            this._setView(center, zoom2);
            return;
          }
          for (var j = tileRange.min.y; j <= tileRange.max.y; j++) {
            for (var i = tileRange.min.x; i <= tileRange.max.x; i++) {
              var coords = new Point(i, j);
              coords.z = this._tileZoom;
              if (!this._isValidTile(coords)) {
                continue;
              }
              var tile = this._tiles[this._tileCoordsToKey(coords)];
              if (tile) {
                tile.current = true;
              } else {
                queue.push(coords);
              }
            }
          }
          queue.sort(function(a, b) {
            return a.distanceTo(tileCenter) - b.distanceTo(tileCenter);
          });
          if (queue.length !== 0) {
            if (!this._loading) {
              this._loading = true;
              this.fire("loading");
            }
            var fragment = document.createDocumentFragment();
            for (i = 0; i < queue.length; i++) {
              this._addTile(queue[i], fragment);
            }
            this._level.el.appendChild(fragment);
          }
        }, "_update"),
        _isValidTile: /* @__PURE__ */ __name(function(coords) {
          var crs = this._map.options.crs;
          if (!crs.infinite) {
            var bounds = this._globalTileRange;
            if (!crs.wrapLng && (coords.x < bounds.min.x || coords.x > bounds.max.x) || !crs.wrapLat && (coords.y < bounds.min.y || coords.y > bounds.max.y)) {
              return false;
            }
          }
          if (!this.options.bounds) {
            return true;
          }
          var tileBounds = this._tileCoordsToBounds(coords);
          return toLatLngBounds(this.options.bounds).overlaps(tileBounds);
        }, "_isValidTile"),
        _keyToBounds: /* @__PURE__ */ __name(function(key) {
          return this._tileCoordsToBounds(this._keyToTileCoords(key));
        }, "_keyToBounds"),
        _tileCoordsToNwSe: /* @__PURE__ */ __name(function(coords) {
          var map2 = this._map, tileSize = this.getTileSize(), nwPoint = coords.scaleBy(tileSize), sePoint = nwPoint.add(tileSize), nw = map2.unproject(nwPoint, coords.z), se = map2.unproject(sePoint, coords.z);
          return [nw, se];
        }, "_tileCoordsToNwSe"),
        // converts tile coordinates to its geographical bounds
        _tileCoordsToBounds: /* @__PURE__ */ __name(function(coords) {
          var bp = this._tileCoordsToNwSe(coords), bounds = new LatLngBounds(bp[0], bp[1]);
          if (!this.options.noWrap) {
            bounds = this._map.wrapLatLngBounds(bounds);
          }
          return bounds;
        }, "_tileCoordsToBounds"),
        // converts tile coordinates to key for the tile cache
        _tileCoordsToKey: /* @__PURE__ */ __name(function(coords) {
          return coords.x + ":" + coords.y + ":" + coords.z;
        }, "_tileCoordsToKey"),
        // converts tile cache key to coordinates
        _keyToTileCoords: /* @__PURE__ */ __name(function(key) {
          var k = key.split(":"), coords = new Point(+k[0], +k[1]);
          coords.z = +k[2];
          return coords;
        }, "_keyToTileCoords"),
        _removeTile: /* @__PURE__ */ __name(function(key) {
          var tile = this._tiles[key];
          if (!tile) {
            return;
          }
          remove(tile.el);
          delete this._tiles[key];
          this.fire("tileunload", {
            tile: tile.el,
            coords: this._keyToTileCoords(key)
          });
        }, "_removeTile"),
        _initTile: /* @__PURE__ */ __name(function(tile) {
          addClass(tile, "leaflet-tile");
          var tileSize = this.getTileSize();
          tile.style.width = tileSize.x + "px";
          tile.style.height = tileSize.y + "px";
          tile.onselectstart = falseFn;
          tile.onmousemove = falseFn;
          if (Browser.ielt9 && this.options.opacity < 1) {
            setOpacity(tile, this.options.opacity);
          }
        }, "_initTile"),
        _addTile: /* @__PURE__ */ __name(function(coords, container) {
          var tilePos = this._getTilePos(coords), key = this._tileCoordsToKey(coords);
          var tile = this.createTile(this._wrapCoords(coords), bind(this._tileReady, this, coords));
          this._initTile(tile);
          if (this.createTile.length < 2) {
            requestAnimFrame(bind(this._tileReady, this, coords, null, tile));
          }
          setPosition(tile, tilePos);
          this._tiles[key] = {
            el: tile,
            coords,
            current: true
          };
          container.appendChild(tile);
          this.fire("tileloadstart", {
            tile,
            coords
          });
        }, "_addTile"),
        _tileReady: /* @__PURE__ */ __name(function(coords, err, tile) {
          if (err) {
            this.fire("tileerror", {
              error: err,
              tile,
              coords
            });
          }
          var key = this._tileCoordsToKey(coords);
          tile = this._tiles[key];
          if (!tile) {
            return;
          }
          tile.loaded = +/* @__PURE__ */ new Date();
          if (this._map._fadeAnimated) {
            setOpacity(tile.el, 0);
            cancelAnimFrame(this._fadeFrame);
            this._fadeFrame = requestAnimFrame(this._updateOpacity, this);
          } else {
            tile.active = true;
            this._pruneTiles();
          }
          if (!err) {
            addClass(tile.el, "leaflet-tile-loaded");
            this.fire("tileload", {
              tile: tile.el,
              coords
            });
          }
          if (this._noTilesToLoad()) {
            this._loading = false;
            this.fire("load");
            if (Browser.ielt9 || !this._map._fadeAnimated) {
              requestAnimFrame(this._pruneTiles, this);
            } else {
              setTimeout(bind(this._pruneTiles, this), 250);
            }
          }
        }, "_tileReady"),
        _getTilePos: /* @__PURE__ */ __name(function(coords) {
          return coords.scaleBy(this.getTileSize()).subtract(this._level.origin);
        }, "_getTilePos"),
        _wrapCoords: /* @__PURE__ */ __name(function(coords) {
          var newCoords = new Point(
            this._wrapX ? wrapNum(coords.x, this._wrapX) : coords.x,
            this._wrapY ? wrapNum(coords.y, this._wrapY) : coords.y
          );
          newCoords.z = coords.z;
          return newCoords;
        }, "_wrapCoords"),
        _pxBoundsToTileRange: /* @__PURE__ */ __name(function(bounds) {
          var tileSize = this.getTileSize();
          return new Bounds(
            bounds.min.unscaleBy(tileSize).floor(),
            bounds.max.unscaleBy(tileSize).ceil().subtract([1, 1])
          );
        }, "_pxBoundsToTileRange"),
        _noTilesToLoad: /* @__PURE__ */ __name(function() {
          for (var key in this._tiles) {
            if (!this._tiles[key].loaded) {
              return false;
            }
          }
          return true;
        }, "_noTilesToLoad")
      });
      function gridLayer(options) {
        return new GridLayer(options);
      }
      __name(gridLayer, "gridLayer");
      var TileLayer = GridLayer.extend({
        // @section
        // @aka TileLayer options
        options: {
          // @option minZoom: Number = 0
          // The minimum zoom level down to which this layer will be displayed (inclusive).
          minZoom: 0,
          // @option maxZoom: Number = 18
          // The maximum zoom level up to which this layer will be displayed (inclusive).
          maxZoom: 18,
          // @option subdomains: String|String[] = 'abc'
          // Subdomains of the tile service. Can be passed in the form of one string (where each letter is a subdomain name) or an array of strings.
          subdomains: "abc",
          // @option errorTileUrl: String = ''
          // URL to the tile image to show in place of the tile that failed to load.
          errorTileUrl: "",
          // @option zoomOffset: Number = 0
          // The zoom number used in tile URLs will be offset with this value.
          zoomOffset: 0,
          // @option tms: Boolean = false
          // If `true`, inverses Y axis numbering for tiles (turn this on for [TMS](https://en.wikipedia.org/wiki/Tile_Map_Service) services).
          tms: false,
          // @option zoomReverse: Boolean = false
          // If set to true, the zoom number used in tile URLs will be reversed (`maxZoom - zoom` instead of `zoom`)
          zoomReverse: false,
          // @option detectRetina: Boolean = false
          // If `true` and user is on a retina display, it will request four tiles of half the specified size and a bigger zoom level in place of one to utilize the high resolution.
          detectRetina: false,
          // @option crossOrigin: Boolean|String = false
          // Whether the crossOrigin attribute will be added to the tiles.
          // If a String is provided, all tiles will have their crossOrigin attribute set to the String provided. This is needed if you want to access tile pixel data.
          // Refer to [CORS Settings](https://developer.mozilla.org/en-US/docs/Web/HTML/CORS_settings_attributes) for valid String values.
          crossOrigin: false,
          // @option referrerPolicy: Boolean|String = false
          // Whether the referrerPolicy attribute will be added to the tiles.
          // If a String is provided, all tiles will have their referrerPolicy attribute set to the String provided.
          // This may be needed if your map's rendering context has a strict default but your tile provider expects a valid referrer
          // (e.g. to validate an API token).
          // Refer to [HTMLImageElement.referrerPolicy](https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/referrerPolicy) for valid String values.
          referrerPolicy: false
        },
        initialize: /* @__PURE__ */ __name(function(url, options) {
          this._url = url;
          options = setOptions(this, options);
          if (options.detectRetina && Browser.retina && options.maxZoom > 0) {
            options.tileSize = Math.floor(options.tileSize / 2);
            if (!options.zoomReverse) {
              options.zoomOffset++;
              options.maxZoom = Math.max(options.minZoom, options.maxZoom - 1);
            } else {
              options.zoomOffset--;
              options.minZoom = Math.min(options.maxZoom, options.minZoom + 1);
            }
            options.minZoom = Math.max(0, options.minZoom);
          } else if (!options.zoomReverse) {
            options.maxZoom = Math.max(options.minZoom, options.maxZoom);
          } else {
            options.minZoom = Math.min(options.maxZoom, options.minZoom);
          }
          if (typeof options.subdomains === "string") {
            options.subdomains = options.subdomains.split("");
          }
          this.on("tileunload", this._onTileRemove);
        }, "initialize"),
        // @method setUrl(url: String, noRedraw?: Boolean): this
        // Updates the layer's URL template and redraws it (unless `noRedraw` is set to `true`).
        // If the URL does not change, the layer will not be redrawn unless
        // the noRedraw parameter is set to false.
        setUrl: /* @__PURE__ */ __name(function(url, noRedraw) {
          if (this._url === url && noRedraw === void 0) {
            noRedraw = true;
          }
          this._url = url;
          if (!noRedraw) {
            this.redraw();
          }
          return this;
        }, "setUrl"),
        // @method createTile(coords: Object, done?: Function): HTMLElement
        // Called only internally, overrides GridLayer's [`createTile()`](#gridlayer-createtile)
        // to return an `<img>` HTML element with the appropriate image URL given `coords`. The `done`
        // callback is called when the tile has been loaded.
        createTile: /* @__PURE__ */ __name(function(coords, done) {
          var tile = document.createElement("img");
          on(tile, "load", bind(this._tileOnLoad, this, done, tile));
          on(tile, "error", bind(this._tileOnError, this, done, tile));
          if (this.options.crossOrigin || this.options.crossOrigin === "") {
            tile.crossOrigin = this.options.crossOrigin === true ? "" : this.options.crossOrigin;
          }
          if (typeof this.options.referrerPolicy === "string") {
            tile.referrerPolicy = this.options.referrerPolicy;
          }
          tile.alt = "";
          tile.src = this.getTileUrl(coords);
          return tile;
        }, "createTile"),
        // @section Extension methods
        // @uninheritable
        // Layers extending `TileLayer` might reimplement the following method.
        // @method getTileUrl(coords: Object): String
        // Called only internally, returns the URL for a tile given its coordinates.
        // Classes extending `TileLayer` can override this function to provide custom tile URL naming schemes.
        getTileUrl: /* @__PURE__ */ __name(function(coords) {
          var data = {
            r: Browser.retina ? "@2x" : "",
            s: this._getSubdomain(coords),
            x: coords.x,
            y: coords.y,
            z: this._getZoomForUrl()
          };
          if (this._map && !this._map.options.crs.infinite) {
            var invertedY = this._globalTileRange.max.y - coords.y;
            if (this.options.tms) {
              data["y"] = invertedY;
            }
            data["-y"] = invertedY;
          }
          return template(this._url, extend(data, this.options));
        }, "getTileUrl"),
        _tileOnLoad: /* @__PURE__ */ __name(function(done, tile) {
          if (Browser.ielt9) {
            setTimeout(bind(done, this, null, tile), 0);
          } else {
            done(null, tile);
          }
        }, "_tileOnLoad"),
        _tileOnError: /* @__PURE__ */ __name(function(done, tile, e) {
          var errorUrl = this.options.errorTileUrl;
          if (errorUrl && tile.getAttribute("src") !== errorUrl) {
            tile.src = errorUrl;
          }
          done(e, tile);
        }, "_tileOnError"),
        _onTileRemove: /* @__PURE__ */ __name(function(e) {
          e.tile.onload = null;
        }, "_onTileRemove"),
        _getZoomForUrl: /* @__PURE__ */ __name(function() {
          var zoom2 = this._tileZoom, maxZoom = this.options.maxZoom, zoomReverse = this.options.zoomReverse, zoomOffset = this.options.zoomOffset;
          if (zoomReverse) {
            zoom2 = maxZoom - zoom2;
          }
          return zoom2 + zoomOffset;
        }, "_getZoomForUrl"),
        _getSubdomain: /* @__PURE__ */ __name(function(tilePoint) {
          var index2 = Math.abs(tilePoint.x + tilePoint.y) % this.options.subdomains.length;
          return this.options.subdomains[index2];
        }, "_getSubdomain"),
        // stops loading all tiles in the background layer
        _abortLoading: /* @__PURE__ */ __name(function() {
          var i, tile;
          for (i in this._tiles) {
            if (this._tiles[i].coords.z !== this._tileZoom) {
              tile = this._tiles[i].el;
              tile.onload = falseFn;
              tile.onerror = falseFn;
              if (!tile.complete) {
                tile.src = emptyImageUrl;
                var coords = this._tiles[i].coords;
                remove(tile);
                delete this._tiles[i];
                this.fire("tileabort", {
                  tile,
                  coords
                });
              }
            }
          }
        }, "_abortLoading"),
        _removeTile: /* @__PURE__ */ __name(function(key) {
          var tile = this._tiles[key];
          if (!tile) {
            return;
          }
          tile.el.setAttribute("src", emptyImageUrl);
          return GridLayer.prototype._removeTile.call(this, key);
        }, "_removeTile"),
        _tileReady: /* @__PURE__ */ __name(function(coords, err, tile) {
          if (!this._map || tile && tile.getAttribute("src") === emptyImageUrl) {
            return;
          }
          return GridLayer.prototype._tileReady.call(this, coords, err, tile);
        }, "_tileReady")
      });
      function tileLayer2(url, options) {
        return new TileLayer(url, options);
      }
      __name(tileLayer2, "tileLayer");
      var TileLayerWMS = TileLayer.extend({
        // @section
        // @aka TileLayer.WMS options
        // If any custom options not documented here are used, they will be sent to the
        // WMS server as extra parameters in each request URL. This can be useful for
        // [non-standard vendor WMS parameters](https://docs.geoserver.org/stable/en/user/services/wms/vendor.html).
        defaultWmsParams: {
          service: "WMS",
          request: "GetMap",
          // @option layers: String = ''
          // **(required)** Comma-separated list of WMS layers to show.
          layers: "",
          // @option styles: String = ''
          // Comma-separated list of WMS styles.
          styles: "",
          // @option format: String = 'image/jpeg'
          // WMS image format (use `'image/png'` for layers with transparency).
          format: "image/jpeg",
          // @option transparent: Boolean = false
          // If `true`, the WMS service will return images with transparency.
          transparent: false,
          // @option version: String = '1.1.1'
          // Version of the WMS service to use
          version: "1.1.1"
        },
        options: {
          // @option crs: CRS = null
          // Coordinate Reference System to use for the WMS requests, defaults to
          // map CRS. Don't change this if you're not sure what it means.
          crs: null,
          // @option uppercase: Boolean = false
          // If `true`, WMS request parameter keys will be uppercase.
          uppercase: false
        },
        initialize: /* @__PURE__ */ __name(function(url, options) {
          this._url = url;
          var wmsParams = extend({}, this.defaultWmsParams);
          for (var i in options) {
            if (!(i in this.options)) {
              wmsParams[i] = options[i];
            }
          }
          options = setOptions(this, options);
          var realRetina = options.detectRetina && Browser.retina ? 2 : 1;
          var tileSize = this.getTileSize();
          wmsParams.width = tileSize.x * realRetina;
          wmsParams.height = tileSize.y * realRetina;
          this.wmsParams = wmsParams;
        }, "initialize"),
        onAdd: /* @__PURE__ */ __name(function(map2) {
          this._crs = this.options.crs || map2.options.crs;
          this._wmsVersion = parseFloat(this.wmsParams.version);
          var projectionKey = this._wmsVersion >= 1.3 ? "crs" : "srs";
          this.wmsParams[projectionKey] = this._crs.code;
          TileLayer.prototype.onAdd.call(this, map2);
        }, "onAdd"),
        getTileUrl: /* @__PURE__ */ __name(function(coords) {
          var tileBounds = this._tileCoordsToNwSe(coords), crs = this._crs, bounds = toBounds(crs.project(tileBounds[0]), crs.project(tileBounds[1])), min = bounds.min, max = bounds.max, bbox = (this._wmsVersion >= 1.3 && this._crs === EPSG4326 ? [min.y, min.x, max.y, max.x] : [min.x, min.y, max.x, max.y]).join(","), url = TileLayer.prototype.getTileUrl.call(this, coords);
          return url + getParamString(this.wmsParams, url, this.options.uppercase) + (this.options.uppercase ? "&BBOX=" : "&bbox=") + bbox;
        }, "getTileUrl"),
        // @method setParams(params: Object, noRedraw?: Boolean): this
        // Merges an object with the new parameters and re-requests tiles on the current screen (unless `noRedraw` was set to true).
        setParams: /* @__PURE__ */ __name(function(params, noRedraw) {
          extend(this.wmsParams, params);
          if (!noRedraw) {
            this.redraw();
          }
          return this;
        }, "setParams")
      });
      function tileLayerWMS(url, options) {
        return new TileLayerWMS(url, options);
      }
      __name(tileLayerWMS, "tileLayerWMS");
      TileLayer.WMS = TileLayerWMS;
      tileLayer2.wms = tileLayerWMS;
      var Renderer = Layer.extend({
        // @section
        // @aka Renderer options
        options: {
          // @option padding: Number = 0.1
          // How much to extend the clip area around the map view (relative to its size)
          // e.g. 0.1 would be 10% of map view in each direction
          padding: 0.1
        },
        initialize: /* @__PURE__ */ __name(function(options) {
          setOptions(this, options);
          stamp(this);
          this._layers = this._layers || {};
        }, "initialize"),
        onAdd: /* @__PURE__ */ __name(function() {
          if (!this._container) {
            this._initContainer();
            addClass(this._container, "leaflet-zoom-animated");
          }
          this.getPane().appendChild(this._container);
          this._update();
          this.on("update", this._updatePaths, this);
        }, "onAdd"),
        onRemove: /* @__PURE__ */ __name(function() {
          this.off("update", this._updatePaths, this);
          this._destroyContainer();
        }, "onRemove"),
        getEvents: /* @__PURE__ */ __name(function() {
          var events = {
            viewreset: this._reset,
            zoom: this._onZoom,
            moveend: this._update,
            zoomend: this._onZoomEnd
          };
          if (this._zoomAnimated) {
            events.zoomanim = this._onAnimZoom;
          }
          return events;
        }, "getEvents"),
        _onAnimZoom: /* @__PURE__ */ __name(function(ev) {
          this._updateTransform(ev.center, ev.zoom);
        }, "_onAnimZoom"),
        _onZoom: /* @__PURE__ */ __name(function() {
          this._updateTransform(this._map.getCenter(), this._map.getZoom());
        }, "_onZoom"),
        _updateTransform: /* @__PURE__ */ __name(function(center, zoom2) {
          var scale2 = this._map.getZoomScale(zoom2, this._zoom), viewHalf = this._map.getSize().multiplyBy(0.5 + this.options.padding), currentCenterPoint = this._map.project(this._center, zoom2), topLeftOffset = viewHalf.multiplyBy(-scale2).add(currentCenterPoint).subtract(this._map._getNewPixelOrigin(center, zoom2));
          if (Browser.any3d) {
            setTransform(this._container, topLeftOffset, scale2);
          } else {
            setPosition(this._container, topLeftOffset);
          }
        }, "_updateTransform"),
        _reset: /* @__PURE__ */ __name(function() {
          this._update();
          this._updateTransform(this._center, this._zoom);
          for (var id in this._layers) {
            this._layers[id]._reset();
          }
        }, "_reset"),
        _onZoomEnd: /* @__PURE__ */ __name(function() {
          for (var id in this._layers) {
            this._layers[id]._project();
          }
        }, "_onZoomEnd"),
        _updatePaths: /* @__PURE__ */ __name(function() {
          for (var id in this._layers) {
            this._layers[id]._update();
          }
        }, "_updatePaths"),
        _update: /* @__PURE__ */ __name(function() {
          var p = this.options.padding, size = this._map.getSize(), min = this._map.containerPointToLayerPoint(size.multiplyBy(-p)).round();
          this._bounds = new Bounds(min, min.add(size.multiplyBy(1 + p * 2)).round());
          this._center = this._map.getCenter();
          this._zoom = this._map.getZoom();
        }, "_update")
      });
      var Canvas = Renderer.extend({
        // @section
        // @aka Canvas options
        options: {
          // @option tolerance: Number = 0
          // How much to extend the click tolerance around a path/object on the map.
          tolerance: 0
        },
        getEvents: /* @__PURE__ */ __name(function() {
          var events = Renderer.prototype.getEvents.call(this);
          events.viewprereset = this._onViewPreReset;
          return events;
        }, "getEvents"),
        _onViewPreReset: /* @__PURE__ */ __name(function() {
          this._postponeUpdatePaths = true;
        }, "_onViewPreReset"),
        onAdd: /* @__PURE__ */ __name(function() {
          Renderer.prototype.onAdd.call(this);
          this._draw();
        }, "onAdd"),
        _initContainer: /* @__PURE__ */ __name(function() {
          var container = this._container = document.createElement("canvas");
          on(container, "mousemove", this._onMouseMove, this);
          on(container, "click dblclick mousedown mouseup contextmenu", this._onClick, this);
          on(container, "mouseout", this._handleMouseOut, this);
          container["_leaflet_disable_events"] = true;
          this._ctx = container.getContext("2d");
        }, "_initContainer"),
        _destroyContainer: /* @__PURE__ */ __name(function() {
          cancelAnimFrame(this._redrawRequest);
          delete this._ctx;
          remove(this._container);
          off(this._container);
          delete this._container;
        }, "_destroyContainer"),
        _updatePaths: /* @__PURE__ */ __name(function() {
          if (this._postponeUpdatePaths) {
            return;
          }
          var layer;
          this._redrawBounds = null;
          for (var id in this._layers) {
            layer = this._layers[id];
            layer._update();
          }
          this._redraw();
        }, "_updatePaths"),
        _update: /* @__PURE__ */ __name(function() {
          if (this._map._animatingZoom && this._bounds) {
            return;
          }
          Renderer.prototype._update.call(this);
          var b = this._bounds, container = this._container, size = b.getSize(), m = Browser.retina ? 2 : 1;
          setPosition(container, b.min);
          container.width = m * size.x;
          container.height = m * size.y;
          container.style.width = size.x + "px";
          container.style.height = size.y + "px";
          if (Browser.retina) {
            this._ctx.scale(2, 2);
          }
          this._ctx.translate(-b.min.x, -b.min.y);
          this.fire("update");
        }, "_update"),
        _reset: /* @__PURE__ */ __name(function() {
          Renderer.prototype._reset.call(this);
          if (this._postponeUpdatePaths) {
            this._postponeUpdatePaths = false;
            this._updatePaths();
          }
        }, "_reset"),
        _initPath: /* @__PURE__ */ __name(function(layer) {
          this._updateDashArray(layer);
          this._layers[stamp(layer)] = layer;
          var order = layer._order = {
            layer,
            prev: this._drawLast,
            next: null
          };
          if (this._drawLast) {
            this._drawLast.next = order;
          }
          this._drawLast = order;
          this._drawFirst = this._drawFirst || this._drawLast;
        }, "_initPath"),
        _addPath: /* @__PURE__ */ __name(function(layer) {
          this._requestRedraw(layer);
        }, "_addPath"),
        _removePath: /* @__PURE__ */ __name(function(layer) {
          var order = layer._order;
          var next = order.next;
          var prev = order.prev;
          if (next) {
            next.prev = prev;
          } else {
            this._drawLast = prev;
          }
          if (prev) {
            prev.next = next;
          } else {
            this._drawFirst = next;
          }
          delete layer._order;
          delete this._layers[stamp(layer)];
          this._requestRedraw(layer);
        }, "_removePath"),
        _updatePath: /* @__PURE__ */ __name(function(layer) {
          this._extendRedrawBounds(layer);
          layer._project();
          layer._update();
          this._requestRedraw(layer);
        }, "_updatePath"),
        _updateStyle: /* @__PURE__ */ __name(function(layer) {
          this._updateDashArray(layer);
          this._requestRedraw(layer);
        }, "_updateStyle"),
        _updateDashArray: /* @__PURE__ */ __name(function(layer) {
          if (typeof layer.options.dashArray === "string") {
            var parts = layer.options.dashArray.split(/[, ]+/), dashArray = [], dashValue, i;
            for (i = 0; i < parts.length; i++) {
              dashValue = Number(parts[i]);
              if (isNaN(dashValue)) {
                return;
              }
              dashArray.push(dashValue);
            }
            layer.options._dashArray = dashArray;
          } else {
            layer.options._dashArray = layer.options.dashArray;
          }
        }, "_updateDashArray"),
        _requestRedraw: /* @__PURE__ */ __name(function(layer) {
          if (!this._map) {
            return;
          }
          this._extendRedrawBounds(layer);
          this._redrawRequest = this._redrawRequest || requestAnimFrame(this._redraw, this);
        }, "_requestRedraw"),
        _extendRedrawBounds: /* @__PURE__ */ __name(function(layer) {
          if (layer._pxBounds) {
            var padding = (layer.options.weight || 0) + 1;
            this._redrawBounds = this._redrawBounds || new Bounds();
            this._redrawBounds.extend(layer._pxBounds.min.subtract([padding, padding]));
            this._redrawBounds.extend(layer._pxBounds.max.add([padding, padding]));
          }
        }, "_extendRedrawBounds"),
        _redraw: /* @__PURE__ */ __name(function() {
          this._redrawRequest = null;
          if (this._redrawBounds) {
            this._redrawBounds.min._floor();
            this._redrawBounds.max._ceil();
          }
          this._clear();
          this._draw();
          this._redrawBounds = null;
        }, "_redraw"),
        _clear: /* @__PURE__ */ __name(function() {
          var bounds = this._redrawBounds;
          if (bounds) {
            var size = bounds.getSize();
            this._ctx.clearRect(bounds.min.x, bounds.min.y, size.x, size.y);
          } else {
            this._ctx.save();
            this._ctx.setTransform(1, 0, 0, 1, 0, 0);
            this._ctx.clearRect(0, 0, this._container.width, this._container.height);
            this._ctx.restore();
          }
        }, "_clear"),
        _draw: /* @__PURE__ */ __name(function() {
          var layer, bounds = this._redrawBounds;
          this._ctx.save();
          if (bounds) {
            var size = bounds.getSize();
            this._ctx.beginPath();
            this._ctx.rect(bounds.min.x, bounds.min.y, size.x, size.y);
            this._ctx.clip();
          }
          this._drawing = true;
          for (var order = this._drawFirst; order; order = order.next) {
            layer = order.layer;
            if (!bounds || layer._pxBounds && layer._pxBounds.intersects(bounds)) {
              layer._updatePath();
            }
          }
          this._drawing = false;
          this._ctx.restore();
        }, "_draw"),
        _updatePoly: /* @__PURE__ */ __name(function(layer, closed) {
          if (!this._drawing) {
            return;
          }
          var i, j, len2, p, parts = layer._parts, len = parts.length, ctx = this._ctx;
          if (!len) {
            return;
          }
          ctx.beginPath();
          for (i = 0; i < len; i++) {
            for (j = 0, len2 = parts[i].length; j < len2; j++) {
              p = parts[i][j];
              ctx[j ? "lineTo" : "moveTo"](p.x, p.y);
            }
            if (closed) {
              ctx.closePath();
            }
          }
          this._fillStroke(ctx, layer);
        }, "_updatePoly"),
        _updateCircle: /* @__PURE__ */ __name(function(layer) {
          if (!this._drawing || layer._empty()) {
            return;
          }
          var p = layer._point, ctx = this._ctx, r = Math.max(Math.round(layer._radius), 1), s = (Math.max(Math.round(layer._radiusY), 1) || r) / r;
          if (s !== 1) {
            ctx.save();
            ctx.scale(1, s);
          }
          ctx.beginPath();
          ctx.arc(p.x, p.y / s, r, 0, Math.PI * 2, false);
          if (s !== 1) {
            ctx.restore();
          }
          this._fillStroke(ctx, layer);
        }, "_updateCircle"),
        _fillStroke: /* @__PURE__ */ __name(function(ctx, layer) {
          var options = layer.options;
          if (options.fill) {
            ctx.globalAlpha = options.fillOpacity;
            ctx.fillStyle = options.fillColor || options.color;
            ctx.fill(options.fillRule || "evenodd");
          }
          if (options.stroke && options.weight !== 0) {
            if (ctx.setLineDash) {
              ctx.setLineDash(layer.options && layer.options._dashArray || []);
            }
            ctx.globalAlpha = options.opacity;
            ctx.lineWidth = options.weight;
            ctx.strokeStyle = options.color;
            ctx.lineCap = options.lineCap;
            ctx.lineJoin = options.lineJoin;
            ctx.stroke();
          }
        }, "_fillStroke"),
        // Canvas obviously doesn't have mouse events for individual drawn objects,
        // so we emulate that by calculating what's under the mouse on mousemove/click manually
        _onClick: /* @__PURE__ */ __name(function(e) {
          var point = this._map.mouseEventToLayerPoint(e), layer, clickedLayer;
          for (var order = this._drawFirst; order; order = order.next) {
            layer = order.layer;
            if (layer.options.interactive && layer._containsPoint(point)) {
              if (!(e.type === "click" || e.type === "preclick") || !this._map._draggableMoved(layer)) {
                clickedLayer = layer;
              }
            }
          }
          this._fireEvent(clickedLayer ? [clickedLayer] : false, e);
        }, "_onClick"),
        _onMouseMove: /* @__PURE__ */ __name(function(e) {
          if (!this._map || this._map.dragging.moving() || this._map._animatingZoom) {
            return;
          }
          var point = this._map.mouseEventToLayerPoint(e);
          this._handleMouseHover(e, point);
        }, "_onMouseMove"),
        _handleMouseOut: /* @__PURE__ */ __name(function(e) {
          var layer = this._hoveredLayer;
          if (layer) {
            removeClass(this._container, "leaflet-interactive");
            this._fireEvent([layer], e, "mouseout");
            this._hoveredLayer = null;
            this._mouseHoverThrottled = false;
          }
        }, "_handleMouseOut"),
        _handleMouseHover: /* @__PURE__ */ __name(function(e, point) {
          if (this._mouseHoverThrottled) {
            return;
          }
          var layer, candidateHoveredLayer;
          for (var order = this._drawFirst; order; order = order.next) {
            layer = order.layer;
            if (layer.options.interactive && layer._containsPoint(point)) {
              candidateHoveredLayer = layer;
            }
          }
          if (candidateHoveredLayer !== this._hoveredLayer) {
            this._handleMouseOut(e);
            if (candidateHoveredLayer) {
              addClass(this._container, "leaflet-interactive");
              this._fireEvent([candidateHoveredLayer], e, "mouseover");
              this._hoveredLayer = candidateHoveredLayer;
            }
          }
          this._fireEvent(this._hoveredLayer ? [this._hoveredLayer] : false, e);
          this._mouseHoverThrottled = true;
          setTimeout(bind(function() {
            this._mouseHoverThrottled = false;
          }, this), 32);
        }, "_handleMouseHover"),
        _fireEvent: /* @__PURE__ */ __name(function(layers2, e, type) {
          this._map._fireDOMEvent(e, type || e.type, layers2);
        }, "_fireEvent"),
        _bringToFront: /* @__PURE__ */ __name(function(layer) {
          var order = layer._order;
          if (!order) {
            return;
          }
          var next = order.next;
          var prev = order.prev;
          if (next) {
            next.prev = prev;
          } else {
            return;
          }
          if (prev) {
            prev.next = next;
          } else if (next) {
            this._drawFirst = next;
          }
          order.prev = this._drawLast;
          this._drawLast.next = order;
          order.next = null;
          this._drawLast = order;
          this._requestRedraw(layer);
        }, "_bringToFront"),
        _bringToBack: /* @__PURE__ */ __name(function(layer) {
          var order = layer._order;
          if (!order) {
            return;
          }
          var next = order.next;
          var prev = order.prev;
          if (prev) {
            prev.next = next;
          } else {
            return;
          }
          if (next) {
            next.prev = prev;
          } else if (prev) {
            this._drawLast = prev;
          }
          order.prev = null;
          order.next = this._drawFirst;
          this._drawFirst.prev = order;
          this._drawFirst = order;
          this._requestRedraw(layer);
        }, "_bringToBack")
      });
      function canvas(options) {
        return Browser.canvas ? new Canvas(options) : null;
      }
      __name(canvas, "canvas");
      var vmlCreate = (function() {
        try {
          document.namespaces.add("lvml", "urn:schemas-microsoft-com:vml");
          return function(name) {
            return document.createElement("<lvml:" + name + ' class="lvml">');
          };
        } catch (e) {
        }
        return function(name) {
          return document.createElement("<" + name + ' xmlns="urn:schemas-microsoft.com:vml" class="lvml">');
        };
      })();
      var vmlMixin = {
        _initContainer: /* @__PURE__ */ __name(function() {
          this._container = create$1("div", "leaflet-vml-container");
        }, "_initContainer"),
        _update: /* @__PURE__ */ __name(function() {
          if (this._map._animatingZoom) {
            return;
          }
          Renderer.prototype._update.call(this);
          this.fire("update");
        }, "_update"),
        _initPath: /* @__PURE__ */ __name(function(layer) {
          var container = layer._container = vmlCreate("shape");
          addClass(container, "leaflet-vml-shape " + (this.options.className || ""));
          container.coordsize = "1 1";
          layer._path = vmlCreate("path");
          container.appendChild(layer._path);
          this._updateStyle(layer);
          this._layers[stamp(layer)] = layer;
        }, "_initPath"),
        _addPath: /* @__PURE__ */ __name(function(layer) {
          var container = layer._container;
          this._container.appendChild(container);
          if (layer.options.interactive) {
            layer.addInteractiveTarget(container);
          }
        }, "_addPath"),
        _removePath: /* @__PURE__ */ __name(function(layer) {
          var container = layer._container;
          remove(container);
          layer.removeInteractiveTarget(container);
          delete this._layers[stamp(layer)];
        }, "_removePath"),
        _updateStyle: /* @__PURE__ */ __name(function(layer) {
          var stroke = layer._stroke, fill = layer._fill, options = layer.options, container = layer._container;
          container.stroked = !!options.stroke;
          container.filled = !!options.fill;
          if (options.stroke) {
            if (!stroke) {
              stroke = layer._stroke = vmlCreate("stroke");
            }
            container.appendChild(stroke);
            stroke.weight = options.weight + "px";
            stroke.color = options.color;
            stroke.opacity = options.opacity;
            if (options.dashArray) {
              stroke.dashStyle = isArray(options.dashArray) ? options.dashArray.join(" ") : options.dashArray.replace(/( *, *)/g, " ");
            } else {
              stroke.dashStyle = "";
            }
            stroke.endcap = options.lineCap.replace("butt", "flat");
            stroke.joinstyle = options.lineJoin;
          } else if (stroke) {
            container.removeChild(stroke);
            layer._stroke = null;
          }
          if (options.fill) {
            if (!fill) {
              fill = layer._fill = vmlCreate("fill");
            }
            container.appendChild(fill);
            fill.color = options.fillColor || options.color;
            fill.opacity = options.fillOpacity;
          } else if (fill) {
            container.removeChild(fill);
            layer._fill = null;
          }
        }, "_updateStyle"),
        _updateCircle: /* @__PURE__ */ __name(function(layer) {
          var p = layer._point.round(), r = Math.round(layer._radius), r2 = Math.round(layer._radiusY || r);
          this._setPath(layer, layer._empty() ? "M0 0" : "AL " + p.x + "," + p.y + " " + r + "," + r2 + " 0," + 65535 * 360);
        }, "_updateCircle"),
        _setPath: /* @__PURE__ */ __name(function(layer, path) {
          layer._path.v = path;
        }, "_setPath"),
        _bringToFront: /* @__PURE__ */ __name(function(layer) {
          toFront(layer._container);
        }, "_bringToFront"),
        _bringToBack: /* @__PURE__ */ __name(function(layer) {
          toBack(layer._container);
        }, "_bringToBack")
      };
      var create = Browser.vml ? vmlCreate : svgCreate;
      var SVG = Renderer.extend({
        _initContainer: /* @__PURE__ */ __name(function() {
          this._container = create("svg");
          this._container.setAttribute("pointer-events", "none");
          this._rootGroup = create("g");
          this._container.appendChild(this._rootGroup);
        }, "_initContainer"),
        _destroyContainer: /* @__PURE__ */ __name(function() {
          remove(this._container);
          off(this._container);
          delete this._container;
          delete this._rootGroup;
          delete this._svgSize;
        }, "_destroyContainer"),
        _update: /* @__PURE__ */ __name(function() {
          if (this._map._animatingZoom && this._bounds) {
            return;
          }
          Renderer.prototype._update.call(this);
          var b = this._bounds, size = b.getSize(), container = this._container;
          if (!this._svgSize || !this._svgSize.equals(size)) {
            this._svgSize = size;
            container.setAttribute("width", size.x);
            container.setAttribute("height", size.y);
          }
          setPosition(container, b.min);
          container.setAttribute("viewBox", [b.min.x, b.min.y, size.x, size.y].join(" "));
          this.fire("update");
        }, "_update"),
        // methods below are called by vector layers implementations
        _initPath: /* @__PURE__ */ __name(function(layer) {
          var path = layer._path = create("path");
          if (layer.options.className) {
            addClass(path, layer.options.className);
          }
          if (layer.options.interactive) {
            addClass(path, "leaflet-interactive");
          }
          this._updateStyle(layer);
          this._layers[stamp(layer)] = layer;
        }, "_initPath"),
        _addPath: /* @__PURE__ */ __name(function(layer) {
          if (!this._rootGroup) {
            this._initContainer();
          }
          this._rootGroup.appendChild(layer._path);
          layer.addInteractiveTarget(layer._path);
        }, "_addPath"),
        _removePath: /* @__PURE__ */ __name(function(layer) {
          remove(layer._path);
          layer.removeInteractiveTarget(layer._path);
          delete this._layers[stamp(layer)];
        }, "_removePath"),
        _updatePath: /* @__PURE__ */ __name(function(layer) {
          layer._project();
          layer._update();
        }, "_updatePath"),
        _updateStyle: /* @__PURE__ */ __name(function(layer) {
          var path = layer._path, options = layer.options;
          if (!path) {
            return;
          }
          if (options.stroke) {
            path.setAttribute("stroke", options.color);
            path.setAttribute("stroke-opacity", options.opacity);
            path.setAttribute("stroke-width", options.weight);
            path.setAttribute("stroke-linecap", options.lineCap);
            path.setAttribute("stroke-linejoin", options.lineJoin);
            if (options.dashArray) {
              path.setAttribute("stroke-dasharray", options.dashArray);
            } else {
              path.removeAttribute("stroke-dasharray");
            }
            if (options.dashOffset) {
              path.setAttribute("stroke-dashoffset", options.dashOffset);
            } else {
              path.removeAttribute("stroke-dashoffset");
            }
          } else {
            path.setAttribute("stroke", "none");
          }
          if (options.fill) {
            path.setAttribute("fill", options.fillColor || options.color);
            path.setAttribute("fill-opacity", options.fillOpacity);
            path.setAttribute("fill-rule", options.fillRule || "evenodd");
          } else {
            path.setAttribute("fill", "none");
          }
        }, "_updateStyle"),
        _updatePoly: /* @__PURE__ */ __name(function(layer, closed) {
          this._setPath(layer, pointsToPath(layer._parts, closed));
        }, "_updatePoly"),
        _updateCircle: /* @__PURE__ */ __name(function(layer) {
          var p = layer._point, r = Math.max(Math.round(layer._radius), 1), r2 = Math.max(Math.round(layer._radiusY), 1) || r, arc = "a" + r + "," + r2 + " 0 1,0 ";
          var d = layer._empty() ? "M0 0" : "M" + (p.x - r) + "," + p.y + arc + r * 2 + ",0 " + arc + -r * 2 + ",0 ";
          this._setPath(layer, d);
        }, "_updateCircle"),
        _setPath: /* @__PURE__ */ __name(function(layer, path) {
          layer._path.setAttribute("d", path);
        }, "_setPath"),
        // SVG does not have the concept of zIndex so we resort to changing the DOM order of elements
        _bringToFront: /* @__PURE__ */ __name(function(layer) {
          toFront(layer._path);
        }, "_bringToFront"),
        _bringToBack: /* @__PURE__ */ __name(function(layer) {
          toBack(layer._path);
        }, "_bringToBack")
      });
      if (Browser.vml) {
        SVG.include(vmlMixin);
      }
      function svg(options) {
        return Browser.svg || Browser.vml ? new SVG(options) : null;
      }
      __name(svg, "svg");
      Map.include({
        // @namespace Map; @method getRenderer(layer: Path): Renderer
        // Returns the instance of `Renderer` that should be used to render the given
        // `Path`. It will ensure that the `renderer` options of the map and paths
        // are respected, and that the renderers do exist on the map.
        getRenderer: /* @__PURE__ */ __name(function(layer) {
          var renderer = layer.options.renderer || this._getPaneRenderer(layer.options.pane) || this.options.renderer || this._renderer;
          if (!renderer) {
            renderer = this._renderer = this._createRenderer();
          }
          if (!this.hasLayer(renderer)) {
            this.addLayer(renderer);
          }
          return renderer;
        }, "getRenderer"),
        _getPaneRenderer: /* @__PURE__ */ __name(function(name) {
          if (name === "overlayPane" || name === void 0) {
            return false;
          }
          var renderer = this._paneRenderers[name];
          if (renderer === void 0) {
            renderer = this._createRenderer({ pane: name });
            this._paneRenderers[name] = renderer;
          }
          return renderer;
        }, "_getPaneRenderer"),
        _createRenderer: /* @__PURE__ */ __name(function(options) {
          return this.options.preferCanvas && canvas(options) || svg(options);
        }, "_createRenderer")
      });
      var Rectangle = Polygon.extend({
        initialize: /* @__PURE__ */ __name(function(latLngBounds, options) {
          Polygon.prototype.initialize.call(this, this._boundsToLatLngs(latLngBounds), options);
        }, "initialize"),
        // @method setBounds(latLngBounds: LatLngBounds): this
        // Redraws the rectangle with the passed bounds.
        setBounds: /* @__PURE__ */ __name(function(latLngBounds) {
          return this.setLatLngs(this._boundsToLatLngs(latLngBounds));
        }, "setBounds"),
        _boundsToLatLngs: /* @__PURE__ */ __name(function(latLngBounds) {
          latLngBounds = toLatLngBounds(latLngBounds);
          return [
            latLngBounds.getSouthWest(),
            latLngBounds.getNorthWest(),
            latLngBounds.getNorthEast(),
            latLngBounds.getSouthEast()
          ];
        }, "_boundsToLatLngs")
      });
      function rectangle2(latLngBounds, options) {
        return new Rectangle(latLngBounds, options);
      }
      __name(rectangle2, "rectangle");
      SVG.create = create;
      SVG.pointsToPath = pointsToPath;
      GeoJSON.geometryToLayer = geometryToLayer;
      GeoJSON.coordsToLatLng = coordsToLatLng;
      GeoJSON.coordsToLatLngs = coordsToLatLngs;
      GeoJSON.latLngToCoords = latLngToCoords;
      GeoJSON.latLngsToCoords = latLngsToCoords;
      GeoJSON.getFeature = getFeature;
      GeoJSON.asFeature = asFeature;
      Map.mergeOptions({
        // @option boxZoom: Boolean = true
        // Whether the map can be zoomed to a rectangular area specified by
        // dragging the mouse while pressing the shift key.
        boxZoom: true
      });
      var BoxZoom = Handler.extend({
        initialize: /* @__PURE__ */ __name(function(map2) {
          this._map = map2;
          this._container = map2._container;
          this._pane = map2._panes.overlayPane;
          this._resetStateTimeout = 0;
          map2.on("unload", this._destroy, this);
        }, "initialize"),
        addHooks: /* @__PURE__ */ __name(function() {
          on(this._container, "mousedown", this._onMouseDown, this);
        }, "addHooks"),
        removeHooks: /* @__PURE__ */ __name(function() {
          off(this._container, "mousedown", this._onMouseDown, this);
        }, "removeHooks"),
        moved: /* @__PURE__ */ __name(function() {
          return this._moved;
        }, "moved"),
        _destroy: /* @__PURE__ */ __name(function() {
          remove(this._pane);
          delete this._pane;
        }, "_destroy"),
        _resetState: /* @__PURE__ */ __name(function() {
          this._resetStateTimeout = 0;
          this._moved = false;
        }, "_resetState"),
        _clearDeferredResetState: /* @__PURE__ */ __name(function() {
          if (this._resetStateTimeout !== 0) {
            clearTimeout(this._resetStateTimeout);
            this._resetStateTimeout = 0;
          }
        }, "_clearDeferredResetState"),
        _onMouseDown: /* @__PURE__ */ __name(function(e) {
          if (!e.shiftKey || e.which !== 1 && e.button !== 1) {
            return false;
          }
          this._clearDeferredResetState();
          this._resetState();
          disableTextSelection();
          disableImageDrag();
          this._startPoint = this._map.mouseEventToContainerPoint(e);
          on(document, {
            contextmenu: stop,
            mousemove: this._onMouseMove,
            mouseup: this._onMouseUp,
            keydown: this._onKeyDown
          }, this);
        }, "_onMouseDown"),
        _onMouseMove: /* @__PURE__ */ __name(function(e) {
          if (!this._moved) {
            this._moved = true;
            this._box = create$1("div", "leaflet-zoom-box", this._container);
            addClass(this._container, "leaflet-crosshair");
            this._map.fire("boxzoomstart");
          }
          this._point = this._map.mouseEventToContainerPoint(e);
          var bounds = new Bounds(this._point, this._startPoint), size = bounds.getSize();
          setPosition(this._box, bounds.min);
          this._box.style.width = size.x + "px";
          this._box.style.height = size.y + "px";
        }, "_onMouseMove"),
        _finish: /* @__PURE__ */ __name(function() {
          if (this._moved) {
            remove(this._box);
            removeClass(this._container, "leaflet-crosshair");
          }
          enableTextSelection();
          enableImageDrag();
          off(document, {
            contextmenu: stop,
            mousemove: this._onMouseMove,
            mouseup: this._onMouseUp,
            keydown: this._onKeyDown
          }, this);
        }, "_finish"),
        _onMouseUp: /* @__PURE__ */ __name(function(e) {
          if (e.which !== 1 && e.button !== 1) {
            return;
          }
          this._finish();
          if (!this._moved) {
            return;
          }
          this._clearDeferredResetState();
          this._resetStateTimeout = setTimeout(bind(this._resetState, this), 0);
          var bounds = new LatLngBounds(
            this._map.containerPointToLatLng(this._startPoint),
            this._map.containerPointToLatLng(this._point)
          );
          this._map.fitBounds(bounds).fire("boxzoomend", { boxZoomBounds: bounds });
        }, "_onMouseUp"),
        _onKeyDown: /* @__PURE__ */ __name(function(e) {
          if (e.keyCode === 27) {
            this._finish();
            this._clearDeferredResetState();
            this._resetState();
          }
        }, "_onKeyDown")
      });
      Map.addInitHook("addHandler", "boxZoom", BoxZoom);
      Map.mergeOptions({
        // @option doubleClickZoom: Boolean|String = true
        // Whether the map can be zoomed in by double clicking on it and
        // zoomed out by double clicking while holding shift. If passed
        // `'center'`, double-click zoom will zoom to the center of the
        //  view regardless of where the mouse was.
        doubleClickZoom: true
      });
      var DoubleClickZoom = Handler.extend({
        addHooks: /* @__PURE__ */ __name(function() {
          this._map.on("dblclick", this._onDoubleClick, this);
        }, "addHooks"),
        removeHooks: /* @__PURE__ */ __name(function() {
          this._map.off("dblclick", this._onDoubleClick, this);
        }, "removeHooks"),
        _onDoubleClick: /* @__PURE__ */ __name(function(e) {
          var map2 = this._map, oldZoom = map2.getZoom(), delta = map2.options.zoomDelta, zoom2 = e.originalEvent.shiftKey ? oldZoom - delta : oldZoom + delta;
          if (map2.options.doubleClickZoom === "center") {
            map2.setZoom(zoom2);
          } else {
            map2.setZoomAround(e.containerPoint, zoom2);
          }
        }, "_onDoubleClick")
      });
      Map.addInitHook("addHandler", "doubleClickZoom", DoubleClickZoom);
      Map.mergeOptions({
        // @option dragging: Boolean = true
        // Whether the map is draggable with mouse/touch or not.
        dragging: true,
        // @section Panning Inertia Options
        // @option inertia: Boolean = *
        // If enabled, panning of the map will have an inertia effect where
        // the map builds momentum while dragging and continues moving in
        // the same direction for some time. Feels especially nice on touch
        // devices. Enabled by default.
        inertia: true,
        // @option inertiaDeceleration: Number = 3000
        // The rate with which the inertial movement slows down, in pixels/second².
        inertiaDeceleration: 3400,
        // px/s^2
        // @option inertiaMaxSpeed: Number = Infinity
        // Max speed of the inertial movement, in pixels/second.
        inertiaMaxSpeed: Infinity,
        // px/s
        // @option easeLinearity: Number = 0.2
        easeLinearity: 0.2,
        // TODO refactor, move to CRS
        // @option worldCopyJump: Boolean = false
        // With this option enabled, the map tracks when you pan to another "copy"
        // of the world and seamlessly jumps to the original one so that all overlays
        // like markers and vector layers are still visible.
        worldCopyJump: false,
        // @option maxBoundsViscosity: Number = 0.0
        // If `maxBounds` is set, this option will control how solid the bounds
        // are when dragging the map around. The default value of `0.0` allows the
        // user to drag outside the bounds at normal speed, higher values will
        // slow down map dragging outside bounds, and `1.0` makes the bounds fully
        // solid, preventing the user from dragging outside the bounds.
        maxBoundsViscosity: 0
      });
      var Drag = Handler.extend({
        addHooks: /* @__PURE__ */ __name(function() {
          if (!this._draggable) {
            var map2 = this._map;
            this._draggable = new Draggable(map2._mapPane, map2._container);
            this._draggable.on({
              dragstart: this._onDragStart,
              drag: this._onDrag,
              dragend: this._onDragEnd
            }, this);
            this._draggable.on("predrag", this._onPreDragLimit, this);
            if (map2.options.worldCopyJump) {
              this._draggable.on("predrag", this._onPreDragWrap, this);
              map2.on("zoomend", this._onZoomEnd, this);
              map2.whenReady(this._onZoomEnd, this);
            }
          }
          addClass(this._map._container, "leaflet-grab leaflet-touch-drag");
          this._draggable.enable();
          this._positions = [];
          this._times = [];
        }, "addHooks"),
        removeHooks: /* @__PURE__ */ __name(function() {
          removeClass(this._map._container, "leaflet-grab");
          removeClass(this._map._container, "leaflet-touch-drag");
          this._draggable.disable();
        }, "removeHooks"),
        moved: /* @__PURE__ */ __name(function() {
          return this._draggable && this._draggable._moved;
        }, "moved"),
        moving: /* @__PURE__ */ __name(function() {
          return this._draggable && this._draggable._moving;
        }, "moving"),
        _onDragStart: /* @__PURE__ */ __name(function() {
          var map2 = this._map;
          map2._stop();
          if (this._map.options.maxBounds && this._map.options.maxBoundsViscosity) {
            var bounds = toLatLngBounds(this._map.options.maxBounds);
            this._offsetLimit = toBounds(
              this._map.latLngToContainerPoint(bounds.getNorthWest()).multiplyBy(-1),
              this._map.latLngToContainerPoint(bounds.getSouthEast()).multiplyBy(-1).add(this._map.getSize())
            );
            this._viscosity = Math.min(1, Math.max(0, this._map.options.maxBoundsViscosity));
          } else {
            this._offsetLimit = null;
          }
          map2.fire("movestart").fire("dragstart");
          if (map2.options.inertia) {
            this._positions = [];
            this._times = [];
          }
        }, "_onDragStart"),
        _onDrag: /* @__PURE__ */ __name(function(e) {
          if (this._map.options.inertia) {
            var time = this._lastTime = +/* @__PURE__ */ new Date(), pos = this._lastPos = this._draggable._absPos || this._draggable._newPos;
            this._positions.push(pos);
            this._times.push(time);
            this._prunePositions(time);
          }
          this._map.fire("move", e).fire("drag", e);
        }, "_onDrag"),
        _prunePositions: /* @__PURE__ */ __name(function(time) {
          while (this._positions.length > 1 && time - this._times[0] > 50) {
            this._positions.shift();
            this._times.shift();
          }
        }, "_prunePositions"),
        _onZoomEnd: /* @__PURE__ */ __name(function() {
          var pxCenter = this._map.getSize().divideBy(2), pxWorldCenter = this._map.latLngToLayerPoint([0, 0]);
          this._initialWorldOffset = pxWorldCenter.subtract(pxCenter).x;
          this._worldWidth = this._map.getPixelWorldBounds().getSize().x;
        }, "_onZoomEnd"),
        _viscousLimit: /* @__PURE__ */ __name(function(value, threshold) {
          return value - (value - threshold) * this._viscosity;
        }, "_viscousLimit"),
        _onPreDragLimit: /* @__PURE__ */ __name(function() {
          if (!this._viscosity || !this._offsetLimit) {
            return;
          }
          var offset = this._draggable._newPos.subtract(this._draggable._startPos);
          var limit = this._offsetLimit;
          if (offset.x < limit.min.x) {
            offset.x = this._viscousLimit(offset.x, limit.min.x);
          }
          if (offset.y < limit.min.y) {
            offset.y = this._viscousLimit(offset.y, limit.min.y);
          }
          if (offset.x > limit.max.x) {
            offset.x = this._viscousLimit(offset.x, limit.max.x);
          }
          if (offset.y > limit.max.y) {
            offset.y = this._viscousLimit(offset.y, limit.max.y);
          }
          this._draggable._newPos = this._draggable._startPos.add(offset);
        }, "_onPreDragLimit"),
        _onPreDragWrap: /* @__PURE__ */ __name(function() {
          var worldWidth = this._worldWidth, halfWidth = Math.round(worldWidth / 2), dx = this._initialWorldOffset, x = this._draggable._newPos.x, newX1 = (x - halfWidth + dx) % worldWidth + halfWidth - dx, newX2 = (x + halfWidth + dx) % worldWidth - halfWidth - dx, newX = Math.abs(newX1 + dx) < Math.abs(newX2 + dx) ? newX1 : newX2;
          this._draggable._absPos = this._draggable._newPos.clone();
          this._draggable._newPos.x = newX;
        }, "_onPreDragWrap"),
        _onDragEnd: /* @__PURE__ */ __name(function(e) {
          var map2 = this._map, options = map2.options, noInertia = !options.inertia || e.noInertia || this._times.length < 2;
          map2.fire("dragend", e);
          if (noInertia) {
            map2.fire("moveend");
          } else {
            this._prunePositions(+/* @__PURE__ */ new Date());
            var direction = this._lastPos.subtract(this._positions[0]), duration = (this._lastTime - this._times[0]) / 1e3, ease = options.easeLinearity, speedVector = direction.multiplyBy(ease / duration), speed = speedVector.distanceTo([0, 0]), limitedSpeed = Math.min(options.inertiaMaxSpeed, speed), limitedSpeedVector = speedVector.multiplyBy(limitedSpeed / speed), decelerationDuration = limitedSpeed / (options.inertiaDeceleration * ease), offset = limitedSpeedVector.multiplyBy(-decelerationDuration / 2).round();
            if (!offset.x && !offset.y) {
              map2.fire("moveend");
            } else {
              offset = map2._limitOffset(offset, map2.options.maxBounds);
              requestAnimFrame(function() {
                map2.panBy(offset, {
                  duration: decelerationDuration,
                  easeLinearity: ease,
                  noMoveStart: true,
                  animate: true
                });
              });
            }
          }
        }, "_onDragEnd")
      });
      Map.addInitHook("addHandler", "dragging", Drag);
      Map.mergeOptions({
        // @option keyboard: Boolean = true
        // Makes the map focusable and allows users to navigate the map with keyboard
        // arrows and `+`/`-` keys.
        keyboard: true,
        // @option keyboardPanDelta: Number = 80
        // Amount of pixels to pan when pressing an arrow key.
        keyboardPanDelta: 80
      });
      var Keyboard = Handler.extend({
        keyCodes: {
          left: [37],
          right: [39],
          down: [40],
          up: [38],
          zoomIn: [187, 107, 61, 171],
          zoomOut: [189, 109, 54, 173]
        },
        initialize: /* @__PURE__ */ __name(function(map2) {
          this._map = map2;
          this._setPanDelta(map2.options.keyboardPanDelta);
          this._setZoomDelta(map2.options.zoomDelta);
        }, "initialize"),
        addHooks: /* @__PURE__ */ __name(function() {
          var container = this._map._container;
          if (container.tabIndex <= 0) {
            container.tabIndex = "0";
          }
          on(container, {
            focus: this._onFocus,
            blur: this._onBlur,
            mousedown: this._onMouseDown
          }, this);
          this._map.on({
            focus: this._addHooks,
            blur: this._removeHooks
          }, this);
        }, "addHooks"),
        removeHooks: /* @__PURE__ */ __name(function() {
          this._removeHooks();
          off(this._map._container, {
            focus: this._onFocus,
            blur: this._onBlur,
            mousedown: this._onMouseDown
          }, this);
          this._map.off({
            focus: this._addHooks,
            blur: this._removeHooks
          }, this);
        }, "removeHooks"),
        _onMouseDown: /* @__PURE__ */ __name(function() {
          if (this._focused) {
            return;
          }
          var body = document.body, docEl = document.documentElement, top = body.scrollTop || docEl.scrollTop, left = body.scrollLeft || docEl.scrollLeft;
          this._map._container.focus();
          window.scrollTo(left, top);
        }, "_onMouseDown"),
        _onFocus: /* @__PURE__ */ __name(function() {
          this._focused = true;
          this._map.fire("focus");
        }, "_onFocus"),
        _onBlur: /* @__PURE__ */ __name(function() {
          this._focused = false;
          this._map.fire("blur");
        }, "_onBlur"),
        _setPanDelta: /* @__PURE__ */ __name(function(panDelta) {
          var keys = this._panKeys = {}, codes = this.keyCodes, i, len;
          for (i = 0, len = codes.left.length; i < len; i++) {
            keys[codes.left[i]] = [-1 * panDelta, 0];
          }
          for (i = 0, len = codes.right.length; i < len; i++) {
            keys[codes.right[i]] = [panDelta, 0];
          }
          for (i = 0, len = codes.down.length; i < len; i++) {
            keys[codes.down[i]] = [0, panDelta];
          }
          for (i = 0, len = codes.up.length; i < len; i++) {
            keys[codes.up[i]] = [0, -1 * panDelta];
          }
        }, "_setPanDelta"),
        _setZoomDelta: /* @__PURE__ */ __name(function(zoomDelta) {
          var keys = this._zoomKeys = {}, codes = this.keyCodes, i, len;
          for (i = 0, len = codes.zoomIn.length; i < len; i++) {
            keys[codes.zoomIn[i]] = zoomDelta;
          }
          for (i = 0, len = codes.zoomOut.length; i < len; i++) {
            keys[codes.zoomOut[i]] = -zoomDelta;
          }
        }, "_setZoomDelta"),
        _addHooks: /* @__PURE__ */ __name(function() {
          on(document, "keydown", this._onKeyDown, this);
        }, "_addHooks"),
        _removeHooks: /* @__PURE__ */ __name(function() {
          off(document, "keydown", this._onKeyDown, this);
        }, "_removeHooks"),
        _onKeyDown: /* @__PURE__ */ __name(function(e) {
          if (e.altKey || e.ctrlKey || e.metaKey) {
            return;
          }
          var key = e.keyCode, map2 = this._map, offset;
          if (key in this._panKeys) {
            if (!map2._panAnim || !map2._panAnim._inProgress) {
              offset = this._panKeys[key];
              if (e.shiftKey) {
                offset = toPoint(offset).multiplyBy(3);
              }
              if (map2.options.maxBounds) {
                offset = map2._limitOffset(toPoint(offset), map2.options.maxBounds);
              }
              if (map2.options.worldCopyJump) {
                var newLatLng = map2.wrapLatLng(map2.unproject(map2.project(map2.getCenter()).add(offset)));
                map2.panTo(newLatLng);
              } else {
                map2.panBy(offset);
              }
            }
          } else if (key in this._zoomKeys) {
            map2.setZoom(map2.getZoom() + (e.shiftKey ? 3 : 1) * this._zoomKeys[key]);
          } else if (key === 27 && map2._popup && map2._popup.options.closeOnEscapeKey) {
            map2.closePopup();
          } else {
            return;
          }
          stop(e);
        }, "_onKeyDown")
      });
      Map.addInitHook("addHandler", "keyboard", Keyboard);
      Map.mergeOptions({
        // @section Mouse wheel options
        // @option scrollWheelZoom: Boolean|String = true
        // Whether the map can be zoomed by using the mouse wheel. If passed `'center'`,
        // it will zoom to the center of the view regardless of where the mouse was.
        scrollWheelZoom: true,
        // @option wheelDebounceTime: Number = 40
        // Limits the rate at which a wheel can fire (in milliseconds). By default
        // user can't zoom via wheel more often than once per 40 ms.
        wheelDebounceTime: 40,
        // @option wheelPxPerZoomLevel: Number = 60
        // How many scroll pixels (as reported by [L.DomEvent.getWheelDelta](#domevent-getwheeldelta))
        // mean a change of one full zoom level. Smaller values will make wheel-zooming
        // faster (and vice versa).
        wheelPxPerZoomLevel: 60
      });
      var ScrollWheelZoom = Handler.extend({
        addHooks: /* @__PURE__ */ __name(function() {
          on(this._map._container, "wheel", this._onWheelScroll, this);
          this._delta = 0;
        }, "addHooks"),
        removeHooks: /* @__PURE__ */ __name(function() {
          off(this._map._container, "wheel", this._onWheelScroll, this);
        }, "removeHooks"),
        _onWheelScroll: /* @__PURE__ */ __name(function(e) {
          var delta = getWheelDelta(e);
          var debounce = this._map.options.wheelDebounceTime;
          this._delta += delta;
          this._lastMousePos = this._map.mouseEventToContainerPoint(e);
          if (!this._startTime) {
            this._startTime = +/* @__PURE__ */ new Date();
          }
          var left = Math.max(debounce - (+/* @__PURE__ */ new Date() - this._startTime), 0);
          clearTimeout(this._timer);
          this._timer = setTimeout(bind(this._performZoom, this), left);
          stop(e);
        }, "_onWheelScroll"),
        _performZoom: /* @__PURE__ */ __name(function() {
          var map2 = this._map, zoom2 = map2.getZoom(), snap = this._map.options.zoomSnap || 0;
          map2._stop();
          var d2 = this._delta / (this._map.options.wheelPxPerZoomLevel * 4), d3 = 4 * Math.log(2 / (1 + Math.exp(-Math.abs(d2)))) / Math.LN2, d4 = snap ? Math.ceil(d3 / snap) * snap : d3, delta = map2._limitZoom(zoom2 + (this._delta > 0 ? d4 : -d4)) - zoom2;
          this._delta = 0;
          this._startTime = null;
          if (!delta) {
            return;
          }
          if (map2.options.scrollWheelZoom === "center") {
            map2.setZoom(zoom2 + delta);
          } else {
            map2.setZoomAround(this._lastMousePos, zoom2 + delta);
          }
        }, "_performZoom")
      });
      Map.addInitHook("addHandler", "scrollWheelZoom", ScrollWheelZoom);
      var tapHoldDelay = 600;
      Map.mergeOptions({
        // @section Touch interaction options
        // @option tapHold: Boolean
        // Enables simulation of `contextmenu` event, default is `true` for mobile Safari.
        tapHold: Browser.touchNative && Browser.safari && Browser.mobile,
        // @option tapTolerance: Number = 15
        // The max number of pixels a user can shift his finger during touch
        // for it to be considered a valid tap.
        tapTolerance: 15
      });
      var TapHold = Handler.extend({
        addHooks: /* @__PURE__ */ __name(function() {
          on(this._map._container, "touchstart", this._onDown, this);
        }, "addHooks"),
        removeHooks: /* @__PURE__ */ __name(function() {
          off(this._map._container, "touchstart", this._onDown, this);
        }, "removeHooks"),
        _onDown: /* @__PURE__ */ __name(function(e) {
          clearTimeout(this._holdTimeout);
          if (e.touches.length !== 1) {
            return;
          }
          var first = e.touches[0];
          this._startPos = this._newPos = new Point(first.clientX, first.clientY);
          this._holdTimeout = setTimeout(bind(function() {
            this._cancel();
            if (!this._isTapValid()) {
              return;
            }
            on(document, "touchend", preventDefault);
            on(document, "touchend touchcancel", this._cancelClickPrevent);
            this._simulateEvent("contextmenu", first);
          }, this), tapHoldDelay);
          on(document, "touchend touchcancel contextmenu", this._cancel, this);
          on(document, "touchmove", this._onMove, this);
        }, "_onDown"),
        _cancelClickPrevent: /* @__PURE__ */ __name(function cancelClickPrevent() {
          off(document, "touchend", preventDefault);
          off(document, "touchend touchcancel", cancelClickPrevent);
        }, "cancelClickPrevent"),
        _cancel: /* @__PURE__ */ __name(function() {
          clearTimeout(this._holdTimeout);
          off(document, "touchend touchcancel contextmenu", this._cancel, this);
          off(document, "touchmove", this._onMove, this);
        }, "_cancel"),
        _onMove: /* @__PURE__ */ __name(function(e) {
          var first = e.touches[0];
          this._newPos = new Point(first.clientX, first.clientY);
        }, "_onMove"),
        _isTapValid: /* @__PURE__ */ __name(function() {
          return this._newPos.distanceTo(this._startPos) <= this._map.options.tapTolerance;
        }, "_isTapValid"),
        _simulateEvent: /* @__PURE__ */ __name(function(type, e) {
          var simulatedEvent = new MouseEvent(type, {
            bubbles: true,
            cancelable: true,
            view: window,
            // detail: 1,
            screenX: e.screenX,
            screenY: e.screenY,
            clientX: e.clientX,
            clientY: e.clientY
            // button: 2,
            // buttons: 2
          });
          simulatedEvent._simulated = true;
          e.target.dispatchEvent(simulatedEvent);
        }, "_simulateEvent")
      });
      Map.addInitHook("addHandler", "tapHold", TapHold);
      Map.mergeOptions({
        // @section Touch interaction options
        // @option touchZoom: Boolean|String = *
        // Whether the map can be zoomed by touch-dragging with two fingers. If
        // passed `'center'`, it will zoom to the center of the view regardless of
        // where the touch events (fingers) were. Enabled for touch-capable web
        // browsers.
        touchZoom: Browser.touch,
        // @option bounceAtZoomLimits: Boolean = true
        // Set it to false if you don't want the map to zoom beyond min/max zoom
        // and then bounce back when pinch-zooming.
        bounceAtZoomLimits: true
      });
      var TouchZoom = Handler.extend({
        addHooks: /* @__PURE__ */ __name(function() {
          addClass(this._map._container, "leaflet-touch-zoom");
          on(this._map._container, "touchstart", this._onTouchStart, this);
        }, "addHooks"),
        removeHooks: /* @__PURE__ */ __name(function() {
          removeClass(this._map._container, "leaflet-touch-zoom");
          off(this._map._container, "touchstart", this._onTouchStart, this);
        }, "removeHooks"),
        _onTouchStart: /* @__PURE__ */ __name(function(e) {
          var map2 = this._map;
          if (!e.touches || e.touches.length !== 2 || map2._animatingZoom || this._zooming) {
            return;
          }
          var p1 = map2.mouseEventToContainerPoint(e.touches[0]), p2 = map2.mouseEventToContainerPoint(e.touches[1]);
          this._centerPoint = map2.getSize()._divideBy(2);
          this._startLatLng = map2.containerPointToLatLng(this._centerPoint);
          if (map2.options.touchZoom !== "center") {
            this._pinchStartLatLng = map2.containerPointToLatLng(p1.add(p2)._divideBy(2));
          }
          this._startDist = p1.distanceTo(p2);
          this._startZoom = map2.getZoom();
          this._moved = false;
          this._zooming = true;
          map2._stop();
          on(document, "touchmove", this._onTouchMove, this);
          on(document, "touchend touchcancel", this._onTouchEnd, this);
          preventDefault(e);
        }, "_onTouchStart"),
        _onTouchMove: /* @__PURE__ */ __name(function(e) {
          if (!e.touches || e.touches.length !== 2 || !this._zooming) {
            return;
          }
          var map2 = this._map, p1 = map2.mouseEventToContainerPoint(e.touches[0]), p2 = map2.mouseEventToContainerPoint(e.touches[1]), scale2 = p1.distanceTo(p2) / this._startDist;
          this._zoom = map2.getScaleZoom(scale2, this._startZoom);
          if (!map2.options.bounceAtZoomLimits && (this._zoom < map2.getMinZoom() && scale2 < 1 || this._zoom > map2.getMaxZoom() && scale2 > 1)) {
            this._zoom = map2._limitZoom(this._zoom);
          }
          if (map2.options.touchZoom === "center") {
            this._center = this._startLatLng;
            if (scale2 === 1) {
              return;
            }
          } else {
            var delta = p1._add(p2)._divideBy(2)._subtract(this._centerPoint);
            if (scale2 === 1 && delta.x === 0 && delta.y === 0) {
              return;
            }
            this._center = map2.unproject(map2.project(this._pinchStartLatLng, this._zoom).subtract(delta), this._zoom);
          }
          if (!this._moved) {
            map2._moveStart(true, false);
            this._moved = true;
          }
          cancelAnimFrame(this._animRequest);
          var moveFn = bind(map2._move, map2, this._center, this._zoom, { pinch: true, round: false }, void 0);
          this._animRequest = requestAnimFrame(moveFn, this, true);
          preventDefault(e);
        }, "_onTouchMove"),
        _onTouchEnd: /* @__PURE__ */ __name(function() {
          if (!this._moved || !this._zooming) {
            this._zooming = false;
            return;
          }
          this._zooming = false;
          cancelAnimFrame(this._animRequest);
          off(document, "touchmove", this._onTouchMove, this);
          off(document, "touchend touchcancel", this._onTouchEnd, this);
          if (this._map.options.zoomAnimation) {
            this._map._animateZoom(this._center, this._map._limitZoom(this._zoom), true, this._map.options.zoomSnap);
          } else {
            this._map._resetView(this._center, this._map._limitZoom(this._zoom));
          }
        }, "_onTouchEnd")
      });
      Map.addInitHook("addHandler", "touchZoom", TouchZoom);
      Map.BoxZoom = BoxZoom;
      Map.DoubleClickZoom = DoubleClickZoom;
      Map.Drag = Drag;
      Map.Keyboard = Keyboard;
      Map.ScrollWheelZoom = ScrollWheelZoom;
      Map.TapHold = TapHold;
      Map.TouchZoom = TouchZoom;
      exports2.Bounds = Bounds;
      exports2.Browser = Browser;
      exports2.CRS = CRS;
      exports2.Canvas = Canvas;
      exports2.Circle = Circle;
      exports2.CircleMarker = CircleMarker;
      exports2.Class = Class;
      exports2.Control = Control;
      exports2.DivIcon = DivIcon;
      exports2.DivOverlay = DivOverlay;
      exports2.DomEvent = DomEvent;
      exports2.DomUtil = DomUtil;
      exports2.Draggable = Draggable;
      exports2.Evented = Evented;
      exports2.FeatureGroup = FeatureGroup;
      exports2.GeoJSON = GeoJSON;
      exports2.GridLayer = GridLayer;
      exports2.Handler = Handler;
      exports2.Icon = Icon;
      exports2.ImageOverlay = ImageOverlay;
      exports2.LatLng = LatLng;
      exports2.LatLngBounds = LatLngBounds;
      exports2.Layer = Layer;
      exports2.LayerGroup = LayerGroup;
      exports2.LineUtil = LineUtil;
      exports2.Map = Map;
      exports2.Marker = Marker2;
      exports2.Mixin = Mixin;
      exports2.Path = Path;
      exports2.Point = Point;
      exports2.PolyUtil = PolyUtil;
      exports2.Polygon = Polygon;
      exports2.Polyline = Polyline;
      exports2.Popup = Popup;
      exports2.PosAnimation = PosAnimation;
      exports2.Projection = index;
      exports2.Rectangle = Rectangle;
      exports2.Renderer = Renderer;
      exports2.SVG = SVG;
      exports2.SVGOverlay = SVGOverlay;
      exports2.TileLayer = TileLayer;
      exports2.Tooltip = Tooltip;
      exports2.Transformation = Transformation;
      exports2.Util = Util;
      exports2.VideoOverlay = VideoOverlay;
      exports2.bind = bind;
      exports2.bounds = toBounds;
      exports2.canvas = canvas;
      exports2.circle = circle;
      exports2.circleMarker = circleMarker;
      exports2.control = control;
      exports2.divIcon = divIcon;
      exports2.extend = extend;
      exports2.featureGroup = featureGroup;
      exports2.geoJSON = geoJSON;
      exports2.geoJson = geoJson;
      exports2.gridLayer = gridLayer;
      exports2.icon = icon;
      exports2.imageOverlay = imageOverlay2;
      exports2.latLng = toLatLng;
      exports2.latLngBounds = toLatLngBounds;
      exports2.layerGroup = layerGroup2;
      exports2.map = createMap2;
      exports2.marker = marker2;
      exports2.point = toPoint;
      exports2.polygon = polygon;
      exports2.polyline = polyline;
      exports2.popup = popup2;
      exports2.rectangle = rectangle2;
      exports2.setOptions = setOptions;
      exports2.stamp = stamp;
      exports2.svg = svg;
      exports2.svgOverlay = svgOverlay;
      exports2.tileLayer = tileLayer2;
      exports2.tooltip = tooltip;
      exports2.transformation = toTransformation;
      exports2.version = version;
      exports2.videoOverlay = videoOverlay;
      var oldL = window.L;
      exports2.noConflict = function() {
        window.L = oldL;
        return this;
      };
      window.L = exports2;
    }));
  })(leafletSrc$1, leafletSrc$1.exports);
  return leafletSrc$1.exports;
}
__name(requireLeafletSrc, "requireLeafletSrc");
var map = {};
var hasRequiredMap;
function requireMap() {
  if (hasRequiredMap) return map;
  hasRequiredMap = 1;
  Object.defineProperty(map, "__esModule", { value: true });
  map.Map = void 0;
  const _Map = class _Map {
    constructor(leafletMap) {
      Object.defineProperty(this, "leafletMap", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: leafletMap
      });
      Object.defineProperty(this, "listeners", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: {}
      });
      this.setupListeners();
    }
    addListener(event, callback) {
      if (!this.listeners[event]) {
        this.listeners[event] = [];
      }
      this.listeners[event].push(callback);
      return this;
    }
    flyTo(latlng, zoom = null) {
      this.getMap().flyTo(latlng, zoom !== null && zoom !== void 0 ? zoom : this.getZoom());
      return this;
    }
    getZoom() {
      return this.getMap().getZoom();
    }
    setZoom(zoom) {
      this.getMap().setZoom(zoom);
      return this;
    }
    getCenter() {
      return this.getMap().getCenter();
    }
    setView(latlng, zoom) {
      this.getMap().setView(latlng, zoom);
      return this;
    }
    getMap() {
      return this.leafletMap;
    }
    getAddable() {
      return this.getMap();
    }
    invalidateSize() {
      this.getMap().invalidateSize();
      return this;
    }
    setupListeners() {
      ["click", "dblclick", "mousedown", "mouseup", "mouseover", "mouseout", "mousemove", "contextmenu", "preclick"].forEach((event) => {
        this.getMap().on(event, (e) => {
          var _a;
          (_a = this.listeners[event]) === null || _a === void 0 ? void 0 : _a.forEach((callback) => {
            var _a2, _b;
            callback({
              originalEvent: (_a2 = e === null || e === void 0 ? void 0 : e.originalEvent) !== null && _a2 !== void 0 ? _a2 : null,
              latLng: (_b = e.latlng) !== null && _b !== void 0 ? _b : null
            });
          });
        });
      });
    }
  };
  __name(_Map, "Map");
  let Map = _Map;
  map.Map = Map;
  return map;
}
__name(requireMap, "requireMap");
var hasRequiredCreateMap;
function requireCreateMap() {
  if (hasRequiredCreateMap) return createMap;
  hasRequiredCreateMap = 1;
  var __importDefault = createMap && createMap.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(createMap, "__esModule", { value: true });
  createMap.CreateMap = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const map_1 = requireMap();
  const _CreateMap = class _CreateMap {
    constructor(options) {
      Object.defineProperty(this, "options", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: options
      });
    }
    create() {
      var _a, _b, _c, _d, _e;
      const map2 = leaflet_1.default.map(this.options.id, {
        scrollWheelZoom: (_a = this.options.scrollWheelZoom) !== null && _a !== void 0 ? _a : false,
        keyboard: (_b = this.options.keyboard) !== null && _b !== void 0 ? _b : false,
        center: (_c = this.options.center) !== null && _c !== void 0 ? _c : { lat: 59.32932, lng: 18.06858 },
        zoom: (_d = this.options.zoom) !== null && _d !== void 0 ? _d : 16,
        zoomControl: (_e = this.options.zoomControl) !== null && _e !== void 0 ? _e : true,
        attributionControl: false
      });
      return new map_1.Map(map2);
    }
  };
  __name(_CreateMap, "CreateMap");
  let CreateMap = _CreateMap;
  createMap.CreateMap = CreateMap;
  return createMap;
}
__name(requireCreateMap, "requireCreateMap");
var createMapInterface = {};
var hasRequiredCreateMapInterface;
function requireCreateMapInterface() {
  if (hasRequiredCreateMapInterface) return createMapInterface;
  hasRequiredCreateMapInterface = 1;
  Object.defineProperty(createMapInterface, "__esModule", { value: true });
  return createMapInterface;
}
__name(requireCreateMapInterface, "requireCreateMapInterface");
var mapInterface = {};
var hasRequiredMapInterface;
function requireMapInterface() {
  if (hasRequiredMapInterface) return mapInterface;
  hasRequiredMapInterface = 1;
  Object.defineProperty(mapInterface, "__esModule", { value: true });
  return mapInterface;
}
__name(requireMapInterface, "requireMapInterface");
var createTileLayer = {};
var tileLayer = {};
var hasRequiredTileLayer;
function requireTileLayer() {
  if (hasRequiredTileLayer) return tileLayer;
  hasRequiredTileLayer = 1;
  Object.defineProperty(tileLayer, "__esModule", { value: true });
  tileLayer.TileLayer = void 0;
  const _TileLayer = class _TileLayer {
    constructor(tileLayer2) {
      Object.defineProperty(this, "tileLayer", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: tileLayer2
      });
    }
    addTo(addable) {
      this.getTileLayer().addTo(addable.getAddable());
      return this;
    }
    removeTileLayer() {
      this.getTileLayer().remove();
      return this;
    }
    setUrl(url) {
      this.getTileLayer().setUrl(url);
      return this;
    }
    getTileLayer() {
      return this.tileLayer;
    }
  };
  __name(_TileLayer, "TileLayer");
  let TileLayer = _TileLayer;
  tileLayer.TileLayer = TileLayer;
  return tileLayer;
}
__name(requireTileLayer, "requireTileLayer");
var hasRequiredCreateTileLayer;
function requireCreateTileLayer() {
  if (hasRequiredCreateTileLayer) return createTileLayer;
  hasRequiredCreateTileLayer = 1;
  var __importDefault = createTileLayer && createTileLayer.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(createTileLayer, "__esModule", { value: true });
  createTileLayer.CreateTileLayer = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const tileLayer_1 = requireTileLayer();
  const _CreateTileLayer = class _CreateTileLayer {
    constructor() {
    }
    create(tileLayerOptions = {}) {
      var _a, _b, _c, _d, _e, _f;
      const tileLayer2 = leaflet_1.default.tileLayer((_a = tileLayerOptions.url) !== null && _a !== void 0 ? _a : "", {
        maxZoom: (_b = tileLayerOptions.maxZoom) !== null && _b !== void 0 ? _b : 18,
        minZoom: (_c = tileLayerOptions.minZoom) !== null && _c !== void 0 ? _c : 0,
        tileSize: (_d = tileLayerOptions.tileSize) !== null && _d !== void 0 ? _d : 256,
        opacity: (_e = tileLayerOptions.opacity) !== null && _e !== void 0 ? _e : 1,
        className: (_f = tileLayerOptions.className) !== null && _f !== void 0 ? _f : ""
      });
      return new tileLayer_1.TileLayer(tileLayer2);
    }
  };
  __name(_CreateTileLayer, "CreateTileLayer");
  let CreateTileLayer = _CreateTileLayer;
  createTileLayer.CreateTileLayer = CreateTileLayer;
  return createTileLayer;
}
__name(requireCreateTileLayer, "requireCreateTileLayer");
var createTileLayerInterface = {};
var hasRequiredCreateTileLayerInterface;
function requireCreateTileLayerInterface() {
  if (hasRequiredCreateTileLayerInterface) return createTileLayerInterface;
  hasRequiredCreateTileLayerInterface = 1;
  Object.defineProperty(createTileLayerInterface, "__esModule", { value: true });
  return createTileLayerInterface;
}
__name(requireCreateTileLayerInterface, "requireCreateTileLayerInterface");
var tileLayerInterface = {};
var hasRequiredTileLayerInterface;
function requireTileLayerInterface() {
  if (hasRequiredTileLayerInterface) return tileLayerInterface;
  hasRequiredTileLayerInterface = 1;
  Object.defineProperty(tileLayerInterface, "__esModule", { value: true });
  return tileLayerInterface;
}
__name(requireTileLayerInterface, "requireTileLayerInterface");
var createLayerGroup = {};
var layerGroup = {};
var hasRequiredLayerGroup;
function requireLayerGroup() {
  if (hasRequiredLayerGroup) return layerGroup;
  hasRequiredLayerGroup = 1;
  Object.defineProperty(layerGroup, "__esModule", { value: true });
  layerGroup.LayerGroup = void 0;
  const _LayerGroup = class _LayerGroup {
    constructor(leafletLayer) {
      Object.defineProperty(this, "leafletLayer", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: leafletLayer
      });
    }
    getBindable() {
      return this.getLayerGroup();
    }
    removeLayerGroup() {
      this.getLayerGroup().remove();
      return this;
    }
    // Need to type addable here since Leaflet is missing types for removeFrom()
    // check issue: https://github.com/Leaflet/Leaflet/issues/9209
    removeLayerGroupFrom(addable) {
      this.getLayerGroup().removeFrom(addable.getAddable());
      return this;
    }
    addTo(addable) {
      this.getLayerGroup().addTo(addable.getAddable());
      return this;
    }
    getAddable() {
      return this.getLayerGroup();
    }
    getLayerGroup() {
      return this.leafletLayer;
    }
  };
  __name(_LayerGroup, "LayerGroup");
  let LayerGroup = _LayerGroup;
  layerGroup.LayerGroup = LayerGroup;
  return layerGroup;
}
__name(requireLayerGroup, "requireLayerGroup");
var hasRequiredCreateLayerGroup;
function requireCreateLayerGroup() {
  if (hasRequiredCreateLayerGroup) return createLayerGroup;
  hasRequiredCreateLayerGroup = 1;
  var __importDefault = createLayerGroup && createLayerGroup.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(createLayerGroup, "__esModule", { value: true });
  createLayerGroup.CreateLayerGroup = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const layerGroup_1 = requireLayerGroup();
  const _CreateLayerGroup = class _CreateLayerGroup {
    constructor() {
    }
    create() {
      const layer = leaflet_1.default.layerGroup();
      return new layerGroup_1.LayerGroup(layer);
    }
  };
  __name(_CreateLayerGroup, "CreateLayerGroup");
  let CreateLayerGroup = _CreateLayerGroup;
  createLayerGroup.CreateLayerGroup = CreateLayerGroup;
  return createLayerGroup;
}
__name(requireCreateLayerGroup, "requireCreateLayerGroup");
var createLayerGroupInterface = {};
var hasRequiredCreateLayerGroupInterface;
function requireCreateLayerGroupInterface() {
  if (hasRequiredCreateLayerGroupInterface) return createLayerGroupInterface;
  hasRequiredCreateLayerGroupInterface = 1;
  Object.defineProperty(createLayerGroupInterface, "__esModule", { value: true });
  return createLayerGroupInterface;
}
__name(requireCreateLayerGroupInterface, "requireCreateLayerGroupInterface");
var layerGroupInterface = {};
var hasRequiredLayerGroupInterface;
function requireLayerGroupInterface() {
  if (hasRequiredLayerGroupInterface) return layerGroupInterface;
  hasRequiredLayerGroupInterface = 1;
  Object.defineProperty(layerGroupInterface, "__esModule", { value: true });
  return layerGroupInterface;
}
__name(requireLayerGroupInterface, "requireLayerGroupInterface");
var createRectangle = {};
var rectangle = {};
var hasRequiredRectangle;
function requireRectangle() {
  if (hasRequiredRectangle) return rectangle;
  hasRequiredRectangle = 1;
  var __importDefault = rectangle && rectangle.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(rectangle, "__esModule", { value: true });
  rectangle.Rectangle = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const _Rectangle = class _Rectangle {
    constructor(leafletRectangle) {
      Object.defineProperty(this, "leafletRectangle", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: leafletRectangle
      });
      Object.defineProperty(this, "listeners", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: {}
      });
      this.setupListeners();
    }
    getBindable() {
      return this.getRectangle();
    }
    setPosition(latLngBoundsObject) {
      const southWest = leaflet_1.default.latLng(latLngBoundsObject.southWest.lat, latLngBoundsObject.southWest.lng);
      const northEast = leaflet_1.default.latLng(latLngBoundsObject.northEast.lat, latLngBoundsObject.northEast.lng);
      const bounds = leaflet_1.default.latLngBounds(southWest, northEast);
      this.getRectangle().setBounds(bounds);
      return this;
    }
    getPosition() {
      const bounds = this.getRectangle().getBounds();
      return {
        southWest: {
          lat: bounds.getSouthWest().lat,
          lng: bounds.getSouthWest().lng
        },
        northEast: {
          lat: bounds.getNorthEast().lat,
          lng: bounds.getNorthEast().lng
        }
      };
    }
    addTo(addable) {
      this.getRectangle().addTo(addable.getAddable());
      return this;
    }
    addListener(event, callback) {
      if (!this.listeners[event]) {
        this.listeners[event] = [];
      }
      this.listeners[event].push(callback);
      return this;
    }
    removeRectangle() {
      this.getRectangle().remove();
      return this;
    }
    getRectangle() {
      return this.leafletRectangle;
    }
    setupListeners() {
      ["click", "dblclick", "mousedown", "mouseup", "mouseover", "mouseout", "mousemove", "contextmenu", "preclick", "drag"].forEach((event) => {
        this.getRectangle().on(event, (e) => {
          var _a;
          (_a = this.listeners[event]) === null || _a === void 0 ? void 0 : _a.forEach((callback) => {
            var _a2, _b;
            callback({
              originalEvent: (_a2 = e === null || e === void 0 ? void 0 : e.originalEvent) !== null && _a2 !== void 0 ? _a2 : null,
              latLng: (_b = e.latlng) !== null && _b !== void 0 ? _b : null
            });
          });
        });
      });
    }
  };
  __name(_Rectangle, "Rectangle");
  let Rectangle = _Rectangle;
  rectangle.Rectangle = Rectangle;
  return rectangle;
}
__name(requireRectangle, "requireRectangle");
var hasRequiredCreateRectangle;
function requireCreateRectangle() {
  if (hasRequiredCreateRectangle) return createRectangle;
  hasRequiredCreateRectangle = 1;
  var __importDefault = createRectangle && createRectangle.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(createRectangle, "__esModule", { value: true });
  createRectangle.CreateRectangle = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const rectangle_1 = requireRectangle();
  const _CreateRectangle = class _CreateRectangle {
    create(latLngBounds, rectangleOptions = {}) {
      const rectangle2 = leaflet_1.default.rectangle([
        [latLngBounds.southWest.lat, latLngBounds.southWest.lng],
        [latLngBounds.northEast.lat, latLngBounds.northEast.lng]
      ], rectangleOptions);
      return new rectangle_1.Rectangle(rectangle2);
    }
  };
  __name(_CreateRectangle, "CreateRectangle");
  let CreateRectangle = _CreateRectangle;
  createRectangle.CreateRectangle = CreateRectangle;
  return createRectangle;
}
__name(requireCreateRectangle, "requireCreateRectangle");
var createRectangleInterface = {};
var hasRequiredCreateRectangleInterface;
function requireCreateRectangleInterface() {
  if (hasRequiredCreateRectangleInterface) return createRectangleInterface;
  hasRequiredCreateRectangleInterface = 1;
  Object.defineProperty(createRectangleInterface, "__esModule", { value: true });
  return createRectangleInterface;
}
__name(requireCreateRectangleInterface, "requireCreateRectangleInterface");
var rectangleInterface = {};
var hasRequiredRectangleInterface;
function requireRectangleInterface() {
  if (hasRequiredRectangleInterface) return rectangleInterface;
  hasRequiredRectangleInterface = 1;
  Object.defineProperty(rectangleInterface, "__esModule", { value: true });
  return rectangleInterface;
}
__name(requireRectangleInterface, "requireRectangleInterface");
var createMarker = {};
var marker = {};
var hasRequiredMarker;
function requireMarker() {
  if (hasRequiredMarker) return marker;
  hasRequiredMarker = 1;
  var __importDefault = marker && marker.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(marker, "__esModule", { value: true });
  marker.Marker = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const _Marker2 = class _Marker2 {
    constructor(marker2) {
      Object.defineProperty(this, "marker", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: marker2
      });
      Object.defineProperty(this, "listeners", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: {}
      });
      this.setupListeners();
    }
    addTo(addable) {
      this.getMarker().addTo(addable.getAddable());
      return this;
    }
    getBindable() {
      return this.getMarker();
    }
    addListener(event, callback) {
      if (!this.listeners[event]) {
        this.listeners[event] = [];
      }
      this.listeners[event].push(callback);
      return this;
    }
    setPosition(position) {
      this.marker.setLatLng(position);
      return this;
    }
    getPosition() {
      return this.marker.getLatLng();
    }
    removeMarker() {
      this.getMarker().remove();
      return this;
    }
    getMarker() {
      return this.marker;
    }
    getElement() {
      return this.marker.getElement();
    }
    setIcon(iconOptions) {
      this.marker.setIcon(leaflet_1.default.divIcon(iconOptions));
      return this;
    }
    isPopupOpen() {
      return this.marker.isPopupOpen();
    }
    setupListeners() {
      ["click", "dblclick", "mousedown", "mouseup", "mouseover", "mouseout", "mousemove", "contextmenu", "preclick", "drag", "dragend", "dragstart", "popupopen", "popupclose"].forEach((event) => {
        this.marker.on(event, (e) => {
          var _a;
          (_a = this.listeners[event]) === null || _a === void 0 ? void 0 : _a.forEach((callback) => {
            var _a2, _b;
            callback({
              originalEvent: (_a2 = e === null || e === void 0 ? void 0 : e.originalEvent) !== null && _a2 !== void 0 ? _a2 : null,
              latLng: (_b = e.latlng) !== null && _b !== void 0 ? _b : this.getPosition()
            });
          });
        });
      });
      return this;
    }
  };
  __name(_Marker2, "Marker");
  let Marker2 = _Marker2;
  marker.Marker = Marker2;
  return marker;
}
__name(requireMarker, "requireMarker");
var hasRequiredCreateMarker;
function requireCreateMarker() {
  if (hasRequiredCreateMarker) return createMarker;
  hasRequiredCreateMarker = 1;
  var __importDefault = createMarker && createMarker.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(createMarker, "__esModule", { value: true });
  createMarker.CreateMarker = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const marker_1 = requireMarker();
  const _CreateMarker = class _CreateMarker {
    constructor() {
    }
    create(markerOptions, tooltipOptions = null) {
      var _a, _b, _c, _d, _e, _f, _g, _h, _j;
      const icon = leaflet_1.default.divIcon({
        className: (_a = markerOptions.className) !== null && _a !== void 0 ? _a : "",
        html: markerOptions.html,
        iconSize: (_b = markerOptions.iconSize) !== null && _b !== void 0 ? _b : [24, 24],
        iconAnchor: (_c = markerOptions.iconAnchor) !== null && _c !== void 0 ? _c : [24, 24]
      });
      const marker2 = leaflet_1.default.marker(markerOptions.position, {
        icon,
        draggable: (_d = markerOptions.draggable) !== null && _d !== void 0 ? _d : false
      });
      if (tooltipOptions) {
        marker2.bindPopup(tooltipOptions.content, {
          closeButton: (_e = tooltipOptions.closeButton) !== null && _e !== void 0 ? _e : true,
          maxHeight: (_f = tooltipOptions.maxHeight) !== null && _f !== void 0 ? _f : 100,
          maxWidth: (_g = tooltipOptions.maxWidth) !== null && _g !== void 0 ? _g : 250,
          className: (_h = tooltipOptions.className) !== null && _h !== void 0 ? _h : "",
          offset: (_j = tooltipOptions.offset) !== null && _j !== void 0 ? _j : [0, 7]
        });
      }
      return new marker_1.Marker(marker2);
    }
  };
  __name(_CreateMarker, "CreateMarker");
  let CreateMarker = _CreateMarker;
  createMarker.CreateMarker = CreateMarker;
  return createMarker;
}
__name(requireCreateMarker, "requireCreateMarker");
var createMarkerInterface = {};
var hasRequiredCreateMarkerInterface;
function requireCreateMarkerInterface() {
  if (hasRequiredCreateMarkerInterface) return createMarkerInterface;
  hasRequiredCreateMarkerInterface = 1;
  Object.defineProperty(createMarkerInterface, "__esModule", { value: true });
  return createMarkerInterface;
}
__name(requireCreateMarkerInterface, "requireCreateMarkerInterface");
var markerInterface = {};
var hasRequiredMarkerInterface;
function requireMarkerInterface() {
  if (hasRequiredMarkerInterface) return markerInterface;
  hasRequiredMarkerInterface = 1;
  Object.defineProperty(markerInterface, "__esModule", { value: true });
  return markerInterface;
}
__name(requireMarkerInterface, "requireMarkerInterface");
var createImageOverlay = {};
var imageOverlay = {};
var hasRequiredImageOverlay;
function requireImageOverlay() {
  if (hasRequiredImageOverlay) return imageOverlay;
  hasRequiredImageOverlay = 1;
  var __importDefault = imageOverlay && imageOverlay.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(imageOverlay, "__esModule", { value: true });
  imageOverlay.ImageOverlay = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const _ImageOverlay = class _ImageOverlay {
    constructor(leafletOverlay) {
      Object.defineProperty(this, "leafletOverlay", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: leafletOverlay
      });
      Object.defineProperty(this, "listeners", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: {}
      });
      this.setupListeners();
    }
    getBindable() {
      return this.getImageOverlay();
    }
    addListener(event, callback) {
      if (!this.listeners[event]) {
        this.listeners[event] = [];
      }
      this.listeners[event].push(callback);
      return this;
    }
    addTo(addable) {
      this.getImageOverlay().addTo(addable.getAddable());
      return this;
    }
    setPosition(latLngBounds) {
      const southWest = leaflet_1.default.latLng(latLngBounds.southWest.lat, latLngBounds.southWest.lng);
      const northEast = leaflet_1.default.latLng(latLngBounds.northEast.lat, latLngBounds.northEast.lng);
      const bounds = leaflet_1.default.latLngBounds(southWest, northEast);
      this.getImageOverlay().setBounds(bounds);
      return this;
    }
    getPosition() {
      const bounds = this.getImageOverlay().getBounds();
      return {
        southWest: {
          lat: bounds.getSouthWest().lat,
          lng: bounds.getSouthWest().lng
        },
        northEast: {
          lat: bounds.getNorthEast().lat,
          lng: bounds.getNorthEast().lng
        }
      };
    }
    getCenter() {
      const bounds = this.getImageOverlay().getBounds();
      const center = bounds.getCenter();
      return {
        lat: center.lat,
        lng: center.lng
      };
    }
    setOpacity(opacity) {
      this.getImageOverlay().setOpacity(opacity);
      return this;
    }
    removeImageOverlay() {
      this.getImageOverlay().remove();
      return this;
    }
    getElement() {
      return this.getImageOverlay().getElement();
    }
    getImageOverlay() {
      return this.leafletOverlay;
    }
    setupListeners() {
      ["click", "dblclick", "mousedown", "mouseup", "mouseover", "mouseout", "mousemove"].forEach((event) => {
        this.getImageOverlay().on(event, (e) => {
          var _a;
          (_a = this.listeners[event]) === null || _a === void 0 ? void 0 : _a.forEach((callback) => {
            var _a2, _b;
            callback({
              originalEvent: (_a2 = e === null || e === void 0 ? void 0 : e.originalEvent) !== null && _a2 !== void 0 ? _a2 : null,
              latLng: (_b = e.latlng) !== null && _b !== void 0 ? _b : this.getPosition()
            });
          });
        });
      });
    }
  };
  __name(_ImageOverlay, "ImageOverlay");
  let ImageOverlay = _ImageOverlay;
  imageOverlay.ImageOverlay = ImageOverlay;
  return imageOverlay;
}
__name(requireImageOverlay, "requireImageOverlay");
var hasRequiredCreateImageOverlay;
function requireCreateImageOverlay() {
  if (hasRequiredCreateImageOverlay) return createImageOverlay;
  hasRequiredCreateImageOverlay = 1;
  var __importDefault = createImageOverlay && createImageOverlay.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(createImageOverlay, "__esModule", { value: true });
  createImageOverlay.CreateImageOverlay = void 0;
  const imageOverlay_1 = requireImageOverlay();
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const _CreateImageOverlay = class _CreateImageOverlay {
    create(imageOverlayOptions) {
      const url = imageOverlayOptions.url;
      const latLngBounds = [
        [imageOverlayOptions.bounds.southWest.lat, imageOverlayOptions.bounds.southWest.lng],
        [imageOverlayOptions.bounds.northEast.lat, imageOverlayOptions.bounds.northEast.lng]
      ];
      const imageOverlay2 = leaflet_1.default.imageOverlay(url, latLngBounds, imageOverlayOptions);
      return new imageOverlay_1.ImageOverlay(imageOverlay2);
    }
  };
  __name(_CreateImageOverlay, "CreateImageOverlay");
  let CreateImageOverlay = _CreateImageOverlay;
  createImageOverlay.CreateImageOverlay = CreateImageOverlay;
  return createImageOverlay;
}
__name(requireCreateImageOverlay, "requireCreateImageOverlay");
var createImageOverlayInterface = {};
var hasRequiredCreateImageOverlayInterface;
function requireCreateImageOverlayInterface() {
  if (hasRequiredCreateImageOverlayInterface) return createImageOverlayInterface;
  hasRequiredCreateImageOverlayInterface = 1;
  Object.defineProperty(createImageOverlayInterface, "__esModule", { value: true });
  return createImageOverlayInterface;
}
__name(requireCreateImageOverlayInterface, "requireCreateImageOverlayInterface");
var imageOverlayInterface = {};
var hasRequiredImageOverlayInterface;
function requireImageOverlayInterface() {
  if (hasRequiredImageOverlayInterface) return imageOverlayInterface;
  hasRequiredImageOverlayInterface = 1;
  Object.defineProperty(imageOverlayInterface, "__esModule", { value: true });
  return imageOverlayInterface;
}
__name(requireImageOverlayInterface, "requireImageOverlayInterface");
var createAttribution = {};
var attribution = {};
var hasRequiredAttribution;
function requireAttribution() {
  if (hasRequiredAttribution) return attribution;
  hasRequiredAttribution = 1;
  Object.defineProperty(attribution, "__esModule", { value: true });
  attribution.Attribution = void 0;
  const _Attribution = class _Attribution {
    constructor(leafletAttribution) {
      Object.defineProperty(this, "leafletAttribution", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: leafletAttribution
      });
    }
    addAttribution(attributionText) {
      this.getAttribution().addAttribution(attributionText);
      return this;
    }
    setPrefix(prefix) {
      this.getAttribution().setPrefix(prefix);
      return this;
    }
    removePrefix() {
      this.getAttribution().setPrefix(false);
      return this;
    }
    addTo(mapInterface2) {
      this.getAttribution().addTo(mapInterface2.getAddable());
      return this;
    }
    setPosition(position) {
      this.getAttribution().setPosition(position);
      return this;
    }
    removeAttribution() {
      this.getAttribution().remove();
      return this;
    }
    getAttribution() {
      return this.leafletAttribution;
    }
  };
  __name(_Attribution, "Attribution");
  let Attribution = _Attribution;
  attribution.Attribution = Attribution;
  return attribution;
}
__name(requireAttribution, "requireAttribution");
var hasRequiredCreateAttribution;
function requireCreateAttribution() {
  if (hasRequiredCreateAttribution) return createAttribution;
  hasRequiredCreateAttribution = 1;
  var __importDefault = createAttribution && createAttribution.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(createAttribution, "__esModule", { value: true });
  createAttribution.CreateAttribution = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const attribution_1 = requireAttribution();
  const _CreateAttribution = class _CreateAttribution {
    constructor() {
    }
    create(attributionOptions = {}) {
      var _a, _b;
      const attribution2 = leaflet_1.default.control.attribution({
        position: (_a = attributionOptions.position) !== null && _a !== void 0 ? _a : "bottomleft",
        prefix: (_b = attributionOptions.prefix) !== null && _b !== void 0 ? _b : false
      });
      return new attribution_1.Attribution(attribution2);
    }
  };
  __name(_CreateAttribution, "CreateAttribution");
  let CreateAttribution = _CreateAttribution;
  createAttribution.CreateAttribution = CreateAttribution;
  return createAttribution;
}
__name(requireCreateAttribution, "requireCreateAttribution");
var createAttributionInterface = {};
var hasRequiredCreateAttributionInterface;
function requireCreateAttributionInterface() {
  if (hasRequiredCreateAttributionInterface) return createAttributionInterface;
  hasRequiredCreateAttributionInterface = 1;
  Object.defineProperty(createAttributionInterface, "__esModule", { value: true });
  return createAttributionInterface;
}
__name(requireCreateAttributionInterface, "requireCreateAttributionInterface");
var attributionInterface = {};
var hasRequiredAttributionInterface;
function requireAttributionInterface() {
  if (hasRequiredAttributionInterface) return attributionInterface;
  hasRequiredAttributionInterface = 1;
  Object.defineProperty(attributionInterface, "__esModule", { value: true });
  return attributionInterface;
}
__name(requireAttributionInterface, "requireAttributionInterface");
var createSearch = {};
var api = {};
var hasRequiredApi;
function requireApi() {
  if (hasRequiredApi) return api;
  hasRequiredApi = 1;
  Object.defineProperty(api, "__esModule", { value: true });
  api.SearchApi = void 0;
  const _SearchApi = class _SearchApi {
    constructor() {
      Object.defineProperty(this, "apiUrl", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: null
      });
      Object.defineProperty(this, "searchParam", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: null
      });
      Object.defineProperty(this, "searchListeners", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: []
      });
      Object.defineProperty(this, "searchResults", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: {}
      });
      Object.defineProperty(this, "searching", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: false
      });
    }
    search(question) {
      if (this.apiUrl === null || this.searchParam === null) {
        throw new Error("API URL and search parameter must be set before searching.");
      }
      if (this.searchResults[question]) {
        return Promise.resolve(this.searchResults[question]);
      }
      if (question.length < 1) {
        this.searchListeners.forEach((callback) => callback([]));
        return Promise.resolve([]);
      }
      const url = new URL(this.apiUrl);
      url.searchParams.set(this.searchParam, question);
      this.searching = true;
      return fetch(url.toString()).then((response) => response.json()).then((data) => {
        const modifiedData = this.searchListeners.reduce((currentData, callback) => callback(currentData), data);
        this.searchResults[question] = modifiedData;
        return modifiedData;
      }).catch((error) => {
        console.error("Error:", error);
        return Promise.resolve([]);
      }).finally(() => {
        this.searching = false;
      });
    }
    // Adding listeners gives you a chance to change the response value
    addSearchResponseCallback(callback) {
      this.searchListeners.push(callback);
      return this;
    }
    isSearching() {
      return this.searching;
    }
    setSearchParam(searchParam) {
      this.searchParam = searchParam;
      return this;
    }
    setApiUrl(url) {
      this.apiUrl = new URL(url);
      return this;
    }
  };
  __name(_SearchApi, "SearchApi");
  let SearchApi = _SearchApi;
  api.SearchApi = SearchApi;
  return api;
}
__name(requireApi, "requireApi");
var search = {};
var hasRequiredSearch;
function requireSearch() {
  if (hasRequiredSearch) return search;
  hasRequiredSearch = 1;
  Object.defineProperty(search, "__esModule", { value: true });
  search.Search = void 0;
  const _Search = class _Search {
    constructor(apiInstance, searchUi2) {
      Object.defineProperty(this, "apiInstance", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: apiInstance
      });
      Object.defineProperty(this, "searchUi", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: searchUi2
      });
      Object.defineProperty(this, "apiUrl", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: ""
      });
    }
    addSearchResponseCallback(searchEventCallback) {
      this.apiInstance.addSearchResponseCallback(searchEventCallback);
      return this;
    }
    addListItemListener(searchEventCallback) {
      this.searchUi.addListItemListener(searchEventCallback);
      return this;
    }
    setApiUrl(url) {
      this.apiInstance.setApiUrl(url);
      return this;
    }
    setSearchListItems(items) {
      this.searchUi.setSearchListItems(items);
      return this;
    }
    search(question) {
      return this.apiInstance.search(question);
    }
    setSearchParam(searchParam) {
      this.apiInstance.setSearchParam(searchParam);
      return this;
    }
    addTo(map2) {
      this.searchUi.addTo(map2);
      return this;
    }
    removeSearch() {
      this.searchUi.removeSearch();
      return this;
    }
    getInput() {
      return this.searchUi.getInput();
    }
    getResetButton() {
      return this.searchUi.getResetButton();
    }
    showSpinner() {
      this.searchUi.showSpinner();
      return this;
    }
    hideSpinner() {
      this.searchUi.hideSpinner();
      return this;
    }
    showResetButton() {
      this.searchUi.showResetButton();
      return this;
    }
    hideResetButton() {
      this.searchUi.hideResetButton();
      return this;
    }
    isSearching() {
      return this.apiInstance.isSearching();
    }
    setValue(value) {
      this.searchUi.setValue(value);
      return this;
    }
    getTitleFromPlaceSchema(place) {
      return this.searchUi.getTitleFromPlaceSchema(place);
    }
    getContainer() {
      return this.searchUi.getContainer();
    }
  };
  __name(_Search, "Search");
  let Search = _Search;
  search.Search = Search;
  return search;
}
__name(requireSearch, "requireSearch");
var searchUi = {};
var hasRequiredSearchUi;
function requireSearchUi() {
  if (hasRequiredSearchUi) return searchUi;
  hasRequiredSearchUi = 1;
  var __importDefault = searchUi && searchUi.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(searchUi, "__esModule", { value: true });
  searchUi.SearchUi = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const _SearchUi = class _SearchUi {
    constructor(searchOptions, searchApi) {
      Object.defineProperty(this, "searchOptions", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: searchOptions
      });
      Object.defineProperty(this, "searchApi", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: searchApi
      });
      Object.defineProperty(this, "searchContainer", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: void 0
      });
      Object.defineProperty(this, "list", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: void 0
      });
      Object.defineProperty(this, "input", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: void 0
      });
      Object.defineProperty(this, "resetButton", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: void 0
      });
      Object.defineProperty(this, "spinner", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: void 0
      });
      Object.defineProperty(this, "itemClickListeners", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: []
      });
      Object.defineProperty(this, "currentValue", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: ""
      });
    }
    listenForInput() {
      var _a;
      const debouncedSearch = this.debounce((value) => {
        this.showSpinner();
        this.searchApi.search(value).then((data) => {
          this.hideSpinner();
          this.setSearchListItems(data);
        });
      }, 500);
      (_a = this.getInput()) === null || _a === void 0 ? void 0 : _a.addEventListener("input", (e) => {
        const input = e.target;
        this.currentValue = input.value;
        this.currentValue.length > 0 ? this.showResetButton() : this.hideResetButton();
        debouncedSearch(input.value);
      });
    }
    addListItemListener(searchEventCallback) {
      this.itemClickListeners.push(searchEventCallback);
      return this;
    }
    setSearchListItems(items) {
      var _a;
      const listContainer = this.getList();
      if (!listContainer) {
        throw new Error("List container not found");
      }
      if (items === null) {
        listContainer.innerHTML = "";
        return this;
      } else if (items.length < 1 && this.currentValue.length > 0) {
        listContainer.innerHTML = `<li>${(_a = this.searchOptions.noResultsText) !== null && _a !== void 0 ? _a : "No items found."}</li>`;
      } else {
        listContainer.innerHTML = "";
      }
      items.forEach((item) => {
        const listItem = this.createListItem(item);
        listItem.addEventListener("click", () => {
          this.itemClickListeners.forEach((listener) => listener(item));
          if (this.input) {
            this.input.value = this.getTitleFromPlaceSchema(item);
            this.input.focus();
          }
          this.getList().innerHTML = "";
        });
        listContainer.appendChild(listItem);
      });
      return this;
    }
    createListItem(item) {
      const title = this.getTitleFromPlaceSchema(item);
      const li = document.createElement("li");
      li.innerHTML = `<span>${title}</span>`;
      return li;
    }
    getTitleFromPlaceSchema(item) {
      var _a, _b;
      if (!item.address) {
        return (_a = item.name) !== null && _a !== void 0 ? _a : "";
      }
      if (typeof item.address !== "object") {
        return item.address;
      }
      if ("name" in item.address) {
        return item.address.name;
      }
      const address = [];
      if (item.name) {
        address.push(item.name);
      }
      if ("streetAddress" in item.address) {
        address.push(item.address.streetAddress);
      }
      if ("addressLocality" in item.address) {
        address.push(item.address.addressLocality);
      }
      if ("addressRegion" in item.address) {
        address.push(item.address.addressRegion);
      }
      if ("postalCode" in item.address) {
        address.push(item.address.postalCode);
      }
      if ("addressCountry" in item.address) {
        address.push(item.address.addressCountry);
      }
      return address.length > 0 ? address.join(", ") : (_b = item.name) !== null && _b !== void 0 ? _b : "";
    }
    addTo(map2) {
      var _a, _b, _c;
      const container = document.createElement("div");
      container.className = `openstreetmap__search ${this.searchOptions.className || ""}`;
      container.innerHTML = `
            <div class="openstreetmap__search-container">
                <input type="text" placeholder="${(_a = this.searchOptions.placeholder) !== null && _a !== void 0 ? _a : "Search location..."}" />
                <span class="openstreetmap__search-spinner" data-js-search-spinner="true"></span>
                <span class="openstreetmap__search-reset" data-js-search-reset="true" role="button" aria-label="${(_b = this.searchOptions.resetButtonLabel) !== null && _b !== void 0 ? _b : "Reset search"}">&#10005;</span>
            </div>
            <ul></ul>
        `;
      const controlContainer = (_c = map2.getAddable().getContainer()) === null || _c === void 0 ? void 0 : _c.querySelector(".leaflet-control-container");
      controlContainer === null || controlContainer === void 0 ? void 0 : controlContainer.appendChild(container);
      this.searchContainer = container;
      leaflet_1.default.DomEvent.disableClickPropagation(this.getContainer());
      leaflet_1.default.DomEvent.disableScrollPropagation(this.getContainer());
      this.listenForInput();
      this.listenForResetButton();
      return this;
    }
    removeSearch() {
      var _a;
      (_a = this.getContainer()) === null || _a === void 0 ? void 0 : _a.remove();
      this.searchContainer = void 0;
      this.list = void 0;
      this.input = void 0;
      return this;
    }
    listenForResetButton() {
      var _a;
      (_a = this.getResetButton()) === null || _a === void 0 ? void 0 : _a.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.getResetButton().style.display = "none";
        if (this.getList()) {
          this.getList().innerHTML = "";
        }
        if (this.getInput()) {
          this.getInput().value = "";
          this.getInput().focus();
        }
      });
    }
    hideSpinner() {
      if (!this.getSpinner()) {
        return this;
      }
      this.getSpinner().style.display = "none";
      return this;
    }
    showSpinner() {
      if (!this.getSpinner()) {
        return this;
      }
      this.getSpinner().style.display = "inline-block";
      return this;
    }
    hideResetButton() {
      if (!this.getResetButton()) {
        return this;
      }
      this.getResetButton().style.display = "none";
      return this;
    }
    showResetButton() {
      if (!this.getResetButton()) {
        return this;
      }
      this.getResetButton().style.display = "block";
      return this;
    }
    setValue(value) {
      this.currentValue = value;
      if (this.getInput()) {
        this.getInput().value = value;
      }
      return this;
    }
    getContainer() {
      return this.searchContainer;
    }
    getSpinner() {
      var _a, _b;
      if (!this.spinner) {
        this.spinner = (_b = (_a = this.getContainer()) === null || _a === void 0 ? void 0 : _a.querySelector("[data-js-search-spinner]")) !== null && _b !== void 0 ? _b : void 0;
      }
      return this.spinner;
    }
    getResetButton() {
      var _a, _b;
      if (!this.resetButton) {
        this.resetButton = (_b = (_a = this.getContainer()) === null || _a === void 0 ? void 0 : _a.querySelector("[data-js-search-reset]")) !== null && _b !== void 0 ? _b : void 0;
      }
      return this.resetButton;
    }
    getInput() {
      var _a, _b;
      if (!this.input) {
        this.input = (_b = (_a = this.getContainer()) === null || _a === void 0 ? void 0 : _a.querySelector("input")) !== null && _b !== void 0 ? _b : void 0;
      }
      return this.input;
    }
    getList() {
      var _a, _b;
      if (!this.list) {
        this.list = (_b = (_a = this.getContainer()) === null || _a === void 0 ? void 0 : _a.querySelector("ul")) !== null && _b !== void 0 ? _b : void 0;
      }
      return this.list;
    }
    debounce(func, wait) {
      let timeout;
      return (...args) => {
        clearTimeout(timeout);
        timeout = window.setTimeout(() => func(...args), wait);
      };
    }
  };
  __name(_SearchUi, "SearchUi");
  let SearchUi = _SearchUi;
  searchUi.SearchUi = SearchUi;
  return searchUi;
}
__name(requireSearchUi, "requireSearchUi");
var hasRequiredCreateSearch;
function requireCreateSearch() {
  if (hasRequiredCreateSearch) return createSearch;
  hasRequiredCreateSearch = 1;
  Object.defineProperty(createSearch, "__esModule", { value: true });
  createSearch.CreateSearch = void 0;
  const api_1 = requireApi();
  const search_1 = requireSearch();
  const searchUi_1 = requireSearchUi();
  const _CreateSearch = class _CreateSearch {
    constructor() {
    }
    create(searchOptions = {}) {
      const searchApi = new api_1.SearchApi();
      return new search_1.Search(searchApi, new searchUi_1.SearchUi(searchOptions, searchApi));
    }
  };
  __name(_CreateSearch, "CreateSearch");
  let CreateSearch = _CreateSearch;
  createSearch.CreateSearch = CreateSearch;
  return createSearch;
}
__name(requireCreateSearch, "requireCreateSearch");
var createSearchInterface = {};
var hasRequiredCreateSearchInterface;
function requireCreateSearchInterface() {
  if (hasRequiredCreateSearchInterface) return createSearchInterface;
  hasRequiredCreateSearchInterface = 1;
  Object.defineProperty(createSearchInterface, "__esModule", { value: true });
  return createSearchInterface;
}
__name(requireCreateSearchInterface, "requireCreateSearchInterface");
var searchInterface = {};
var hasRequiredSearchInterface;
function requireSearchInterface() {
  if (hasRequiredSearchInterface) return searchInterface;
  hasRequiredSearchInterface = 1;
  Object.defineProperty(searchInterface, "__esModule", { value: true });
  return searchInterface;
}
__name(requireSearchInterface, "requireSearchInterface");
var createPopup = {};
var popup = {};
var hasRequiredPopup;
function requirePopup() {
  if (hasRequiredPopup) return popup;
  hasRequiredPopup = 1;
  Object.defineProperty(popup, "__esModule", { value: true });
  popup.Popup = void 0;
  const _Popup = class _Popup {
    constructor(popup2) {
      Object.defineProperty(this, "popup", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: popup2
      });
      Object.defineProperty(this, "bindable", {
        enumerable: true,
        configurable: true,
        writable: true,
        value: null
      });
    }
    bindTo(bindable, options = {}) {
      var _a;
      (_a = this.getBindable()) === null || _a === void 0 ? void 0 : _a.getBindable().unbindPopup();
      this.bindable = bindable;
      bindable.getBindable().bindPopup(this.popup, options);
      return this;
    }
    unbind() {
      var _a;
      (_a = this.getBindable()) === null || _a === void 0 ? void 0 : _a.getBindable().unbindPopup();
      this.bindable = null;
      return this;
    }
    getElement() {
      var _a;
      return (_a = this.popup.getElement()) !== null && _a !== void 0 ? _a : null;
    }
    open() {
      var _a;
      (_a = this.getBindable()) === null || _a === void 0 ? void 0 : _a.getBindable().openPopup();
      return this;
    }
    close() {
      var _a;
      (_a = this.getBindable()) === null || _a === void 0 ? void 0 : _a.getBindable().closePopup();
      return this;
    }
    toggle() {
      var _a;
      (_a = this.getBindable()) === null || _a === void 0 ? void 0 : _a.getBindable().togglePopup();
      return this;
    }
    isOpen() {
      var _a, _b;
      return (_b = (_a = this.getBindable()) === null || _a === void 0 ? void 0 : _a.getBindable().isPopupOpen()) !== null && _b !== void 0 ? _b : false;
    }
    setContent(content) {
      var _a;
      (_a = this.getBindable()) === null || _a === void 0 ? void 0 : _a.getBindable().setPopupContent(content);
      return this;
    }
    getBindable() {
      return this.bindable;
    }
  };
  __name(_Popup, "Popup");
  let Popup = _Popup;
  popup.Popup = Popup;
  return popup;
}
__name(requirePopup, "requirePopup");
var hasRequiredCreatePopup;
function requireCreatePopup() {
  if (hasRequiredCreatePopup) return createPopup;
  hasRequiredCreatePopup = 1;
  var __importDefault = createPopup && createPopup.__importDefault || function(mod) {
    return mod && mod.__esModule ? mod : { "default": mod };
  };
  Object.defineProperty(createPopup, "__esModule", { value: true });
  createPopup.CreatePopup = void 0;
  const leaflet_1 = __importDefault(requireLeafletSrc());
  const popup_1 = requirePopup();
  const _CreatePopup = class _CreatePopup {
    create(popupOptions = {}) {
      const popup2 = leaflet_1.default.popup(popupOptions);
      return new popup_1.Popup(popup2);
    }
  };
  __name(_CreatePopup, "CreatePopup");
  let CreatePopup = _CreatePopup;
  createPopup.CreatePopup = CreatePopup;
  return createPopup;
}
__name(requireCreatePopup, "requireCreatePopup");
var createPopupInterface = {};
var hasRequiredCreatePopupInterface;
function requireCreatePopupInterface() {
  if (hasRequiredCreatePopupInterface) return createPopupInterface;
  hasRequiredCreatePopupInterface = 1;
  Object.defineProperty(createPopupInterface, "__esModule", { value: true });
  return createPopupInterface;
}
__name(requireCreatePopupInterface, "requireCreatePopupInterface");
var popupInterface = {};
var hasRequiredPopupInterface;
function requirePopupInterface() {
  if (hasRequiredPopupInterface) return popupInterface;
  hasRequiredPopupInterface = 1;
  Object.defineProperty(popupInterface, "__esModule", { value: true });
  return popupInterface;
}
__name(requirePopupInterface, "requirePopupInterface");
var hasRequiredDist;
function requireDist() {
  if (hasRequiredDist) return dist;
  hasRequiredDist = 1;
  (function(exports) {
    var __createBinding = dist && dist.__createBinding || (Object.create ? (function(o, m, k, k2) {
      if (k2 === void 0) k2 = k;
      var desc = Object.getOwnPropertyDescriptor(m, k);
      if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
        desc = { enumerable: true, get: /* @__PURE__ */ __name(function() {
          return m[k];
        }, "get") };
      }
      Object.defineProperty(o, k2, desc);
    }) : (function(o, m, k, k2) {
      if (k2 === void 0) k2 = k;
      o[k2] = m[k];
    }));
    var __exportStar = dist && dist.__exportStar || function(m, exports2) {
      for (var p in m) if (p !== "default" && !Object.prototype.hasOwnProperty.call(exports2, p)) __createBinding(exports2, m, p);
    };
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.Popup = exports.CreatePopup = exports.Search = exports.CreateSearch = exports.Attribution = exports.CreateAttribution = exports.ImageOverlay = exports.CreateImageOverlay = exports.Marker = exports.CreateMarker = exports.Rectangle = exports.CreateRectangle = exports.LayerGroup = exports.CreateLayerGroup = exports.TileLayer = exports.CreateTileLayer = exports.Map = exports.CreateMap = exports.TilesHelper = void 0;
    __exportStar(requireTypes(), exports);
    __exportStar(requireEventListenerInterface(), exports);
    __exportStar(requireAddableInterface(), exports);
    __exportStar(requireAddToInterface(), exports);
    var TilesHelper_1 = requireTilesHelper();
    Object.defineProperty(exports, "TilesHelper", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return TilesHelper_1.TilesHelper;
    }, "get") });
    __exportStar(requireTilesHelperInterface(), exports);
    var createMap_1 = requireCreateMap();
    Object.defineProperty(exports, "CreateMap", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return createMap_1.CreateMap;
    }, "get") });
    var map_1 = requireMap();
    Object.defineProperty(exports, "Map", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return map_1.Map;
    }, "get") });
    __exportStar(requireCreateMapInterface(), exports);
    __exportStar(requireMapInterface(), exports);
    var createTileLayer_1 = requireCreateTileLayer();
    Object.defineProperty(exports, "CreateTileLayer", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return createTileLayer_1.CreateTileLayer;
    }, "get") });
    var tileLayer_1 = requireTileLayer();
    Object.defineProperty(exports, "TileLayer", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return tileLayer_1.TileLayer;
    }, "get") });
    __exportStar(requireCreateTileLayerInterface(), exports);
    __exportStar(requireTileLayerInterface(), exports);
    var createLayerGroup_1 = requireCreateLayerGroup();
    Object.defineProperty(exports, "CreateLayerGroup", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return createLayerGroup_1.CreateLayerGroup;
    }, "get") });
    var layerGroup_1 = requireLayerGroup();
    Object.defineProperty(exports, "LayerGroup", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return layerGroup_1.LayerGroup;
    }, "get") });
    __exportStar(requireCreateLayerGroupInterface(), exports);
    __exportStar(requireLayerGroupInterface(), exports);
    var createRectangle_1 = requireCreateRectangle();
    Object.defineProperty(exports, "CreateRectangle", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return createRectangle_1.CreateRectangle;
    }, "get") });
    var rectangle_1 = requireRectangle();
    Object.defineProperty(exports, "Rectangle", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return rectangle_1.Rectangle;
    }, "get") });
    __exportStar(requireCreateRectangleInterface(), exports);
    __exportStar(requireRectangleInterface(), exports);
    var createMarker_1 = requireCreateMarker();
    Object.defineProperty(exports, "CreateMarker", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return createMarker_1.CreateMarker;
    }, "get") });
    var marker_1 = requireMarker();
    Object.defineProperty(exports, "Marker", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return marker_1.Marker;
    }, "get") });
    __exportStar(requireCreateMarkerInterface(), exports);
    __exportStar(requireMarkerInterface(), exports);
    var createImageOverlay_1 = requireCreateImageOverlay();
    Object.defineProperty(exports, "CreateImageOverlay", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return createImageOverlay_1.CreateImageOverlay;
    }, "get") });
    var imageOverlay_1 = requireImageOverlay();
    Object.defineProperty(exports, "ImageOverlay", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return imageOverlay_1.ImageOverlay;
    }, "get") });
    __exportStar(requireCreateImageOverlayInterface(), exports);
    __exportStar(requireImageOverlayInterface(), exports);
    var createAttribution_1 = requireCreateAttribution();
    Object.defineProperty(exports, "CreateAttribution", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return createAttribution_1.CreateAttribution;
    }, "get") });
    var attribution_1 = requireAttribution();
    Object.defineProperty(exports, "Attribution", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return attribution_1.Attribution;
    }, "get") });
    __exportStar(requireCreateAttributionInterface(), exports);
    __exportStar(requireAttributionInterface(), exports);
    var createSearch_1 = requireCreateSearch();
    Object.defineProperty(exports, "CreateSearch", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return createSearch_1.CreateSearch;
    }, "get") });
    var search_1 = requireSearch();
    Object.defineProperty(exports, "Search", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return search_1.Search;
    }, "get") });
    __exportStar(requireCreateSearchInterface(), exports);
    __exportStar(requireSearchInterface(), exports);
    var createPopup_1 = requireCreatePopup();
    Object.defineProperty(exports, "CreatePopup", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return createPopup_1.CreatePopup;
    }, "get") });
    var popup_1 = requirePopup();
    Object.defineProperty(exports, "Popup", { enumerable: true, get: /* @__PURE__ */ __name(function() {
      return popup_1.Popup;
    }, "get") });
    __exportStar(requireCreatePopupInterface(), exports);
    __exportStar(requirePopupInterface(), exports);
  })(dist);
  return dist;
}
__name(requireDist, "requireDist");
var distExports = requireDist();
const _Marker = class _Marker {
  /**
   * @param markerCreator - Factory used to create map marker instances.
   * @param popupCreator  - Factory used to create popup instances bound to markers.
   */
  constructor(markerCreator, popupCreator) {
    this.markerCreator = markerCreator;
    this.popupCreator = popupCreator;
  }
  markerCreator;
  popupCreator;
  /**
   * Creates a map marker at the position defined in options. When a content
   * string is provided, a popup is bound to the marker and click listeners
   * are attached to toggle the highlighted icon state.
   *
   * @param options - Configuration for the marker's position, icon, color, and popup content.
   * @returns The created MarkerInterface instance.
   */
  create(options) {
    const marker2 = this.markerCreator.create({
      position: { lat: options.lat, lng: options.lng },
      ...this.getIconOptions(this.getMarkerContent(options))
    });
    if (options.content) {
      const popup2 = this.popupCreator.create();
      popup2.bindTo(marker2);
      popup2.setContent(options.content);
      this.addClickListener(marker2, options);
    }
    return marker2;
  }
  /**
   * Attaches popupopen and popupclose event listeners to the marker so that
   * its icon switches to the highlighted state while the popup is open and
   * reverts to the default state when the popup closes.
   *
   * @param marker  - The marker to attach listeners to.
   * @param options - Marker configuration used to regenerate the icon HTML.
   */
  addClickListener(marker2, options) {
    marker2.addListener("popupopen", () => {
      marker2.setIcon(this.getIconOptions(this.getHighlightedMarkerContent(options)));
    });
    marker2.addListener("popupclose", () => {
      marker2.setIcon(this.getIconOptions(this.getMarkerContent(options)));
    });
  }
  /**
   * Builds an IconOptions object with a fixed 32×32 size and a centred
   * horizontal anchor at [16, 2].
   *
   * @param html - The HTML string to use as the icon's visual content.
   * @returns An IconOptions object ready to pass to the marker creator.
   */
  getIconOptions(html) {
    return {
      html,
      iconSize: [32, 32],
      iconAnchor: [16, 2]
    };
  }
  /**
   * Generates the HTML string for the highlighted (active/open-popup) marker
   * icon, rendered as a white circle with a coloured border and icon.
   *
   * @param options - Marker configuration supplying colour and icon values.
   * @returns An HTML string representing the highlighted marker icon.
   */
  getHighlightedMarkerContent(options) {
    const [color, icon] = this.getColorAndIcon(options);
    return `<span style="background-color: white; border: solid 2px ${color}; color: ${color}; font-size: 20px; padding: 4px; border-radius: 50%;" data-material-symbol="${icon}" class="interactive-map__highlighted-marker material-symbols material-symbols-rounded material-symbols-sharp material-symbols-outlined material-symbols--filled"></span>`;
  }
  /**
   * Generates the HTML string for the default marker icon, rendered as a
   * solid coloured circle with a white icon.
   *
   * @param options - Marker configuration supplying colour and icon values.
   * @returns An HTML string representing the default marker icon.
   */
  getMarkerContent(options) {
    const [color, icon] = this.getColorAndIcon(options);
    return `<span style="background-color: ${color}; border: solid 2px ${color}; color: white; font-size: 20px; padding: 4px; border-radius: 50%;" data-material-symbol="${icon}" class="material-symbols material-symbols-rounded material-symbols-sharp material-symbols-outlined material-symbols--filled"></span>`;
  }
  /**
   * Extracts the colour and icon name from the marker options, falling back
   * to the default colour (#E04A39) and icon (location_on) when not provided.
   *
   * @param options - Marker configuration that may contain colour and icon overrides.
   * @returns A tuple of [color, icon] strings.
   */
  getColorAndIcon(options) {
    const color = options.color ?? "#E04A39";
    const icon = options.icon ?? "location_on";
    return [color, icon];
  }
};
__name(_Marker, "Marker");
let Marker = _Marker;
const _Openstreetmap = class _Openstreetmap {
  /**
   * Creates an Openstreetmap instance and initializes the map.
   *
   * @param openstreetmapArgs - Verified configuration for the map.
   */
  constructor(openstreetmapArgs) {
    this.openstreetmapArgs = openstreetmapArgs;
    this.createMap();
  }
  openstreetmapArgs;
  map;
  /**
   * Creates the map instance, attaches the tile layer, attribution, and
   * renders all configured markers onto the map.
   */
  createMap() {
    this.map = new distExports.CreateMap({
      id: this.openstreetmapArgs.id,
      center: { lat: this.openstreetmapArgs.lat, lng: this.openstreetmapArgs.lng },
      zoom: this.openstreetmapArgs.zoom
    }).create();
    const { url, attribution: attribution2 } = new distExports.TilesHelper().getDefaultTiles(this.openstreetmapArgs.style);
    const markerCreator = new Marker(new distExports.CreateMarker(), new distExports.CreatePopup());
    new distExports.CreateTileLayer().create().setUrl(url).addTo(this.map);
    new distExports.CreateAttribution().create().setPrefix(attribution2).addTo(this.map);
    for (const markerConfig of this.openstreetmapArgs.markers) {
      const marker2 = markerCreator.create(markerConfig);
      marker2.addTo(this.map);
    }
  }
};
__name(_Openstreetmap, "Openstreetmap");
let Openstreetmap = _Openstreetmap;
const _OpenstreetmapFactory = class _OpenstreetmapFactory {
  /**
   * Creates and returns a new Openstreetmap instance after validating the
   * provided arguments and ensuring the required styles are loaded.
   *
   * @param args - Raw map configuration from the consuming component.
   * @returns A new Openstreetmap instance, or undefined if arguments are invalid.
   */
  create(args) {
    const verifiedArgs = this.getAndVerifyArgs(args);
    if (!verifiedArgs) {
      console.warn("Invalid map arguments provided, map creation aborted.");
      return;
    }
    this.ensureStyles();
    return new Openstreetmap(verifiedArgs);
  }
  /**
   * Validates and transforms raw map arguments into a typed OpenstreetmapArgs
   * object. Returns false if any required field fails validation.
   *
   * @param args - Raw map configuration to validate.
   * @returns A verified OpenstreetmapArgs object, or false on validation failure.
   */
  getAndVerifyArgs(args) {
    const id = this.verifyId(args.container, args.id);
    if (!id) {
      return false;
    }
    const zoom = this.getZoom(args.zoom);
    const style = this.getStyle(args.style);
    const markers = this.decodeAndVerifyMarkers(args.markers);
    const verifiedArgs = {
      id,
      lat: Number(args.lat),
      lng: Number(args.lng),
      zoom,
      style,
      markers
    };
    return verifiedArgs;
  }
  /**
   * Parses a JSON-encoded markers string and filters out any entries with
   * missing or non-numeric lat/lng values.
   *
   * @param encodedMarkers - JSON string representing an array of marker objects.
   * @returns An array of valid MarkerConfig objects, or an empty array if none are valid.
   */
  decodeAndVerifyMarkers(encodedMarkers) {
    const markers = JSON.parse(encodedMarkers || "[]");
    if (!Array.isArray(markers)) {
      return [];
    }
    const markerConfigs = [];
    for (const marker2 of markers) {
      const lat = Number(marker2.lat);
      const lng = Number(marker2.lng);
      if (marker2.lat == null || Number.isNaN(lat) || marker2.lng == null || Number.isNaN(lng)) {
        continue;
      }
      markerConfigs.push({
        lat,
        lng,
        icon: marker2.icon || null,
        content: marker2.content || null,
        color: marker2.color || null
      });
    }
    return markerConfigs;
  }
  /**
   * Validates the provided style string against the allowed MapStyle values
   * and falls back to "default" when the value is absent or unrecognised.
   *
   * @param style - Raw style string from map arguments.
   * @returns A valid MapStyle value.
   */
  getStyle(style) {
    const validStyles = ["default", "pale", "dark", "color"];
    if (!style || !validStyles.includes(style)) {
      return "default";
    }
    return style;
  }
  /**
   * Parses the zoom string into a number. Returns 16 as the default zoom
   * level when the value is absent or not a valid number.
   *
   * @param zoom - Raw zoom string from map arguments.
   * @returns A numeric zoom level.
   */
  getZoom(zoom) {
    const parsedZoom = Number(zoom);
    return Number.isNaN(parsedZoom) ? 16 : parsedZoom;
  }
  /**
   * Verifies that an element with the given id exists within the provided
   * container element.
   *
   * @param container - The parent element to search within.
   * @param id - The id attribute value to look for.
   * @returns The id string when the element is found, or false otherwise.
   */
  verifyId(container, id) {
    const mapContainer = container.querySelector(`[id="${id}"]`);
    if (!mapContainer) {
      return false;
    }
    return id;
  }
  /**
   * Injects the OpenStreetMap CSS into the document's adopted stylesheets
   * exactly once per page, guarded by the static `stylesLoaded` flag.
   */
  ensureStyles() {
    if (_OpenstreetmapFactory.stylesLoaded) return;
    _OpenstreetmapFactory.stylesLoaded = true;
    const newSheet = new CSSStyleSheet();
    newSheet.replaceSync(sheet);
    document.adoptedStyleSheets = [...document.adoptedStyleSheets, newSheet];
  }
};
__name(_OpenstreetmapFactory, "OpenstreetmapFactory");
__publicField(_OpenstreetmapFactory, "stylesLoaded", false);
let OpenstreetmapFactory = _OpenstreetmapFactory;
export {
  OpenstreetmapFactory as default
};
//# sourceMappingURL=openstreetmapFactory.js.map
