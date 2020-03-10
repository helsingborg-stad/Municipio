/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./assets/source/mce-js/mce-print-break.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/source/mce-js/mce-print-break.js":
/*!*************************************************!*\
  !*** ./assets/source/mce-js/mce-print-break.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function () {\n  tinymce.PluginManager.add('print_break', function (editor, url) {\n    editor.addButton('printbreak', {\n      text: '',\n      icon: 'wp_page',\n      context: 'insert',\n      tooltip: 'Print Break',\n      onclick: function onclick(e) {\n        editor.execCommand('Print_Break');\n      }\n    });\n    editor.addCommand('Print_Break', function () {\n      var parent;\n      var html;\n      var tag = 'printbreak';\n      var title = 'Print Break';\n      var classname = 'wp-print-break-tag mce-wp-' + tag;\n      var dom = editor.dom;\n      var node = editor.selection.getNode();\n      html = '<img src=\"' + tinymce.Env.transparentSrc + '\" alt=\"\" title=\"' + title + '\" class=\"' + classname + '\" ' + 'data-mce-resize=\"false\" data-mce-placeholder=\"1\" data-wp-more=\"printbreak\" />'; // Most common case\n\n      if (node.nodeName === 'BODY' || node.nodeName === 'P' && node.parentNode.nodeName === 'BODY') {\n        editor.insertContent(html);\n        return;\n      } // Get the top level parent node\n\n\n      parent = dom.getParent(node, function (found) {\n        if (found.parentNode && found.parentNode.nodeName === 'BODY') {\n          return true;\n        }\n\n        return false;\n      }, editor.getBody());\n\n      if (parent) {\n        if (parent.nodeName === 'P') {\n          parent.appendChild(dom.create('p', null, html).firstChild);\n        } else {\n          dom.insertAfter(dom.create('p', null, html), parent);\n        }\n\n        editor.nodeChanged();\n      }\n    });\n    editor.on('BeforeSetContent', function (event) {\n      var title;\n\n      if (event.content) {\n        if (event.content.indexOf('<!--printbreak-->') !== -1) {\n          title = 'Print Break';\n          event.content = event.content.replace(/<!--printbreak-->/g, '<img src=\"' + tinymce.Env.transparentSrc + '\" class=\"wp-print-break-tag mce-wp-printbreak\" ' + 'alt=\"\" title=\"' + title + '\" data-wp-more=\"printbreak\" data-mce-resize=\"false\" data-mce-placeholder=\"1\" />');\n        }\n      }\n    });\n    editor.on('PostProcess', function (event) {\n      if (event.get) {\n        event.content = event.content.replace(/<img[^>]+>/g, function (image) {\n          var match,\n              string,\n              moretext = '';\n\n          if (image.indexOf('data-wp-more=\"printbreak\"') !== -1) {\n            string = '<!--printbreak-->';\n          }\n\n          return string || image;\n        });\n      }\n    });\n  });\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvc291cmNlL21jZS1qcy9tY2UtcHJpbnQtYnJlYWsuanM/MmYxNiJdLCJuYW1lcyI6WyJ0aW55bWNlIiwiUGx1Z2luTWFuYWdlciIsImFkZCIsImVkaXRvciIsInVybCIsImFkZEJ1dHRvbiIsInRleHQiLCJpY29uIiwiY29udGV4dCIsInRvb2x0aXAiLCJvbmNsaWNrIiwiZSIsImV4ZWNDb21tYW5kIiwiYWRkQ29tbWFuZCIsInBhcmVudCIsImh0bWwiLCJ0YWciLCJ0aXRsZSIsImNsYXNzbmFtZSIsImRvbSIsIm5vZGUiLCJzZWxlY3Rpb24iLCJnZXROb2RlIiwiRW52IiwidHJhbnNwYXJlbnRTcmMiLCJub2RlTmFtZSIsInBhcmVudE5vZGUiLCJpbnNlcnRDb250ZW50IiwiZ2V0UGFyZW50IiwiZm91bmQiLCJnZXRCb2R5IiwiYXBwZW5kQ2hpbGQiLCJjcmVhdGUiLCJmaXJzdENoaWxkIiwiaW5zZXJ0QWZ0ZXIiLCJub2RlQ2hhbmdlZCIsIm9uIiwiZXZlbnQiLCJjb250ZW50IiwiaW5kZXhPZiIsInJlcGxhY2UiLCJnZXQiLCJpbWFnZSIsIm1hdGNoIiwic3RyaW5nIiwibW9yZXRleHQiXSwibWFwcGluZ3MiOiJBQUFBLENBQUMsWUFBVztBQUNSQSxTQUFPLENBQUNDLGFBQVIsQ0FBc0JDLEdBQXRCLENBQTBCLGFBQTFCLEVBQXlDLFVBQVNDLE1BQVQsRUFBaUJDLEdBQWpCLEVBQXNCO0FBQzNERCxVQUFNLENBQUNFLFNBQVAsQ0FBaUIsWUFBakIsRUFBK0I7QUFDM0JDLFVBQUksRUFBRSxFQURxQjtBQUUzQkMsVUFBSSxFQUFFLFNBRnFCO0FBRzNCQyxhQUFPLEVBQUUsUUFIa0I7QUFJM0JDLGFBQU8sRUFBRSxhQUprQjtBQUszQkMsYUFBTyxFQUFFLGlCQUFTQyxDQUFULEVBQVk7QUFDakJSLGNBQU0sQ0FBQ1MsV0FBUCxDQUFtQixhQUFuQjtBQUNIO0FBUDBCLEtBQS9CO0FBVUFULFVBQU0sQ0FBQ1UsVUFBUCxD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QUFkLE1BQStDLENBQUMsQ0FBckQsRUFBeUQ7QUFDckRLLGtCQUFNLEdBQUcsbUJBQVQ7QUFDSDs7QUFFRCxpQkFBT0EsTUFBTSxJQUFJRixLQUFqQjtBQUNILFNBVmUsQ0FBaEI7QUFXSDtBQUNKLEtBZEQ7QUFlSCxHQS9FRDtBQWdGSCxDQWpGRCIsImZpbGUiOiIuL2Fzc2V0cy9zb3VyY2UvbWNlLWpzL21jZS1wcmludC1icmVhay5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbigpIHtcbiAgICB0aW55bWNlLlBsdWdpbk1hbmFnZXIuYWRkKCdwcmludF9icmVhaycsIGZ1bmN0aW9uKGVkaXRvciwgdXJsKSB7XG4gICAgICAgIGVkaXRvci5hZGRCdXR0b24oJ3ByaW50YnJlYWsnLCB7XG4gICAgICAgICAgICB0ZXh0OiAnJyxcbiAgICAgICAgICAgIGljb246ICd3cF9wYWdlJyxcbiAgICAgICAgICAgIGNvbnRleHQ6ICdpbnNlcnQnLFxuICAgICAgICAgICAgdG9vbHRpcDogJ1ByaW50IEJyZWFrJyxcbiAgICAgICAgICAgIG9uY2xpY2s6IGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICBlZGl0b3IuZXhlY0NvbW1hbmQoJ1ByaW50X0JyZWFrJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGVkaXRvci5hZGRDb21tYW5kKCdQcmludF9CcmVhaycsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgdmFyIHBhcmVudDtcbiAgICAgICAgICAgIHZhciBodG1sO1xuXG4gICAgICAgICAgICB2YXIgdGFnID0gJ3ByaW50YnJlYWsnO1xuICAgICAgICAgICAgdmFyIHRpdGxlID0gJ1ByaW50IEJyZWFrJztcbiAgICAgICAgICAgIHZhciBjbGFzc25hbWUgPSAnd3AtcHJpbnQtYnJlYWstdGFnIG1jZS13cC0nICsgdGFnO1xuICAgICAgICAgICAgdmFyIGRvbSA9IGVkaXRvci5kb207XG4gICAgICAgICAgICB2YXIgbm9kZSA9IGVkaXRvci5zZWxlY3Rpb24uZ2V0Tm9kZSgpO1xuXG4gICAgICAgICAgICBodG1sID0gJzxpbWcgc3JjPVwiJyArIHRpbnltY2UuRW52LnRyYW5zcGFyZW50U3JjICsgJ1wiIGFsdD1cIlwiIHRpdGxlPVwiJyArIHRpdGxlICsgJ1wiIGNsYXNzPVwiJyArIGNsYXNzbmFtZSArICdcIiAnICtcbiAgICAgICAgICAgICAgICAnZGF0YS1tY2UtcmVzaXplPVwiZmFsc2VcIiBkYXRhLW1jZS1wbGFjZWhvbGRlcj1cIjFcIiBkYXRhLXdwLW1vcmU9XCJwcmludGJyZWFrXCIgLz4nO1xuXG4gICAgICAgICAgICAvLyBNb3N0IGNvbW1vbiBjYXNlXG4gICAgICAgICAgICBpZiAobm9kZS5ub2RlTmFtZSA9PT0gJ0JPRFknIHx8IChub2RlLm5vZGVOYW1lID09PSAnUCcgJiYgbm9kZS5wYXJlbnROb2RlLm5vZGVOYW1lID09PSAnQk9EWScpKSB7XG4gICAgICAgICAgICAgICAgZWRpdG9yLmluc2VydENvbnRlbnQoaHRtbCk7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBHZXQgdGhlIHRvcCBsZXZlbCBwYXJlbnQgbm9kZVxuICAgICAgICAgICAgcGFyZW50ID0gZG9tLmdldFBhcmVudChub2RlLCBmdW5jdGlvbihmb3VuZCkge1xuICAgICAgICAgICAgICAgIGlmIChmb3VuZC5wYXJlbnROb2RlICYmIGZvdW5kLnBhcmVudE5vZGUubm9kZU5hbWUgPT09ICdCT0RZJykge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9LCBlZGl0b3IuZ2V0Qm9keSgpKTtcblxuICAgICAgICAgICAgaWYgKHBhcmVudCkge1xuICAgICAgICAgICAgICAgIGlmIChwYXJlbnQubm9kZU5hbWUgPT09ICdQJykge1xuICAgICAgICAgICAgICAgICAgICBwYXJlbnQuYXBwZW5kQ2hpbGQoZG9tLmNyZWF0ZSgncCcsIG51bGwsIGh0bWwpLmZpcnN0Q2hpbGQpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGRvbS5pbnNlcnRBZnRlciggZG9tLmNyZWF0ZSgncCcsIG51bGwsIGh0bWwpLCBwYXJlbnQpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGVkaXRvci5ub2RlQ2hhbmdlZCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICBlZGl0b3Iub24oICdCZWZvcmVTZXRDb250ZW50JywgZnVuY3Rpb24oIGV2ZW50ICkge1xuICAgICAgICAgICAgdmFyIHRpdGxlO1xuXG4gICAgICAgICAgICBpZiAoIGV2ZW50LmNvbnRlbnQgKSB7XG4gICAgICAgICAgICAgICAgaWYgKCBldmVudC5jb250ZW50LmluZGV4T2YoICc8IS0tcHJpbnRicmVhay0tPicgKSAhPT0gLTEgKSB7XG4gICAgICAgICAgICAgICAgICAgIHRpdGxlID0gJ1ByaW50IEJyZWFrJztcblxuICAgICAgICAgICAgICAgICAgICBldmVudC5jb250ZW50ID0gZXZlbnQuY29udGVudC5yZXBsYWNlKCAvPCEtLXByaW50YnJlYWstLT4vZyxcbiAgICAgICAgICAgICAgICAgICAgICAgICc8aW1nIHNyYz1cIicgKyB0aW55bWNlLkVudi50cmFuc3BhcmVudFNyYyArICdcIiBjbGFzcz1cIndwLXByaW50LWJyZWFrLXRhZyBtY2Utd3AtcHJpbnRicmVha1wiICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICdhbHQ9XCJcIiB0aXRsZT1cIicgKyB0aXRsZSArICdcIiBkYXRhLXdwLW1vcmU9XCJwcmludGJyZWFrXCIgZGF0YS1tY2UtcmVzaXplPVwiZmFsc2VcIiBkYXRhLW1jZS1wbGFjZWhvbGRlcj1cIjFcIiAvPicgKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGVkaXRvci5vbiggJ1Bvc3RQcm9jZXNzJywgZnVuY3Rpb24oIGV2ZW50ICkge1xuICAgICAgICAgICAgaWYgKCBldmVudC5nZXQgKSB7XG4gICAgICAgICAgICAgICAgZXZlbnQuY29udGVudCA9IGV2ZW50LmNvbnRlbnQucmVwbGFjZSgvPGltZ1tePl0rPi9nLCBmdW5jdGlvbiggaW1hZ2UgKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBtYXRjaCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0cmluZyxcbiAgICAgICAgICAgICAgICAgICAgICAgIG1vcmV0ZXh0ID0gJyc7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCBpbWFnZS5pbmRleE9mKCdkYXRhLXdwLW1vcmU9XCJwcmludGJyZWFrXCInKSAhPT0gLTEgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzdHJpbmcgPSAnPCEtLXByaW50YnJlYWstLT4nO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHN0cmluZyB8fCBpbWFnZTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSk7XG59KSgpO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./assets/source/mce-js/mce-print-break.js\n");

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0Esa0RBQTBDLGdDQUFnQztBQUMxRTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGdFQUF3RCxrQkFBa0I7QUFDMUU7QUFDQSx5REFBaUQsY0FBYztBQUMvRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaURBQXlDLGlDQUFpQztBQUMxRSx3SEFBZ0gsbUJBQW1CLEVBQUU7QUFDckk7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7O0FBR0E7QUFDQSIsImZpbGUiOiJqcy9tY2UtcHJpbnQtYnJlYWsuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBnZXR0ZXIgfSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uciA9IGZ1bmN0aW9uKGV4cG9ydHMpIHtcbiBcdFx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG4gXHRcdH1cbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbiBcdH07XG5cbiBcdC8vIGNyZWF0ZSBhIGZha2UgbmFtZXNwYWNlIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDE6IHZhbHVlIGlzIGEgbW9kdWxlIGlkLCByZXF1aXJlIGl0XG4gXHQvLyBtb2RlICYgMjogbWVyZ2UgYWxsIHByb3BlcnRpZXMgb2YgdmFsdWUgaW50byB0aGUgbnNcbiBcdC8vIG1vZGUgJiA0OiByZXR1cm4gdmFsdWUgd2hlbiBhbHJlYWR5IG5zIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDh8MTogYmVoYXZlIGxpa2UgcmVxdWlyZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy50ID0gZnVuY3Rpb24odmFsdWUsIG1vZGUpIHtcbiBcdFx0aWYobW9kZSAmIDEpIHZhbHVlID0gX193ZWJwYWNrX3JlcXVpcmVfXyh2YWx1ZSk7XG4gXHRcdGlmKG1vZGUgJiA4KSByZXR1cm4gdmFsdWU7XG4gXHRcdGlmKChtb2RlICYgNCkgJiYgdHlwZW9mIHZhbHVlID09PSAnb2JqZWN0JyAmJiB2YWx1ZSAmJiB2YWx1ZS5fX2VzTW9kdWxlKSByZXR1cm4gdmFsdWU7XG4gXHRcdHZhciBucyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18ucihucyk7XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShucywgJ2RlZmF1bHQnLCB7IGVudW1lcmFibGU6IHRydWUsIHZhbHVlOiB2YWx1ZSB9KTtcbiBcdFx0aWYobW9kZSAmIDIgJiYgdHlwZW9mIHZhbHVlICE9ICdzdHJpbmcnKSBmb3IodmFyIGtleSBpbiB2YWx1ZSkgX193ZWJwYWNrX3JlcXVpcmVfXy5kKG5zLCBrZXksIGZ1bmN0aW9uKGtleSkgeyByZXR1cm4gdmFsdWVba2V5XTsgfS5iaW5kKG51bGwsIGtleSkpO1xuIFx0XHRyZXR1cm4gbnM7XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gXCIuL2Fzc2V0cy9zb3VyY2UvbWNlLWpzL21jZS1wcmludC1icmVhay5qc1wiKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=