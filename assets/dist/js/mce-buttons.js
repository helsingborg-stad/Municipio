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
/******/ 	return __webpack_require__(__webpack_require__.s = "./assets/source/mce-js/mce-buttons.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/source/mce-js/mce-buttons.js":
/*!*********************************************!*\
  !*** ./assets/source/mce-js/mce-buttons.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function () {\n  if (typeof tinymce !== 'undefined') {\n    tinymce.PluginManager.add('mce_hbg_buttons', function (editor, url) {\n      editor.addButton('mce_hbg_buttons', {\n        text: 'Button',\n        icon: '',\n        context: 'insert',\n        tooltip: 'Add button',\n        cmd: 'mce_hbg_buttons'\n      });\n      editor.addCommand('mce_hbg_buttons', function () {\n        editor.windowManager.open({\n          title: 'Add button',\n          url: mce_hbg_buttons.themeUrl + '/library/Admin/TinyMce/MceButtons/mce-buttons-template.php',\n          width: 500,\n          height: 420,\n          buttons: [{\n            text: 'Insert',\n            onclick: function onclick(e) {\n              var $iframe = jQuery('.mce-container-body.mce-window-body.mce-abs-layout iframe').contents();\n              var btnClass = $iframe.find('#preview a').attr('class');\n              var btnText = $iframe.find('#btnText').val();\n              var btnLink = $iframe.find('#btnLink').val();\n              var button = '<a href=\"' + btnLink + '\" class=\"' + btnClass + '\">' + btnText + '</a>';\n              editor.insertContent(button);\n              editor.windowManager.close();\n              return true;\n            }\n          }]\n        }, {\n          stylesSheet: mce_hbg_buttons.styleSheet\n        });\n      });\n    });\n  }\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvc291cmNlL21jZS1qcy9tY2UtYnV0dG9ucy5qcz85MGNiIl0sIm5hbWVzIjpbInRpbnltY2UiLCJQbHVnaW5NYW5hZ2VyIiwiYWRkIiwiZWRpdG9yIiwidXJsIiwiYWRkQnV0dG9uIiwidGV4dCIsImljb24iLCJjb250ZXh0IiwidG9vbHRpcCIsImNtZCIsImFkZENvbW1hbmQiLCJ3aW5kb3dNYW5hZ2VyIiwib3BlbiIsInRpdGxlIiwibWNlX2hiZ19idXR0b25zIiwidGhlbWVVcmwiLCJ3aWR0aCIsImhlaWdodCIsImJ1dHRvbnMiLCJvbmNsaWNrIiwiZSIsIiRpZnJhbWUiLCJqUXVlcnkiLCJjb250ZW50cyIsImJ0bkNsYXNzIiwiZmluZCIsImF0dHIiLCJidG5UZXh0IiwidmFsIiwiYnRuTGluayIsImJ1dHRvbiIsImluc2VydENvbnRlbnQiLCJjbG9zZSIsInN0eWxlc1NoZWV0Iiwic3R5bGVTaGVldCJdLCJtYXBwaW5ncyI6IkFBQUEsQ0FBQyxZQUFXO0FBQ1IsTUFBSSxPQUFPQSxPQUFQLEtBQW1CLFdBQXZCLEVBQW9DO0FBQ2hDQSxXQUFPLENBQUNDLGFBQVIsQ0FBc0JDLEdBQXRCLENBQTBCLGlCQUExQixFQUE2QyxVQUFTQyxNQUFULEVBQWlCQyxHQUFqQixFQUFzQjtBQUNuRUQsWUFBTSxDQUFDRSxTQUFQLENBQWlCLGlCQUFqQixFQUFvQztBQUNoQ0MsWUFBSSxFQUFFLFFBRDBCO0FBRWhDQyxZQUFJLEVBQUUsRUFGMEI7QUFHaENDLGVBQU8sRUFBRSxRQUh1QjtBQUloQ0MsZUFBTyxFQUFFLFlBSnVCO0FBS2hDQyxXQUFHLEVBQUU7QUFMMkIsT0FBcEM7QUFRQVAsWUFBTSxDQUFDUSxVQUFQLENBQWtCLGlCQUFsQixFQUFxQyxZQUFXO0FBQzVDUixjQUFNLENBQUNTLGFBQVAsQ0FBcUJDLElBQXJCLENBQTBCO0FBQ3RCQyxlQUFLLEVBQUUsWUFEZTtBQUV0QlYsYUFBRyxFQUFFVyxlQUFlLENBQUNDLFFBQWhCLEdBQTJCLDREQUZWO0FBR3RCQyxlQUFLLEVBQUUsR0FIZTtBQUl0QkMsZ0JBQU0sRUFBRSxHQUpjO0FBS3RCQyxpQkFBTyxFQUFFLENBQ0w7QUFDSWIsZ0JBQUksRUFBRSxRQURWO0FBRUljLG1CQUFPLEVBQUUsaUJBQVNDLENBQVQsRUFBWTtBQUNqQixrQkFBSUMsT0FBTyxHQUFHQyxNQUFNLENBQUMsMkRBQUQsQ0FBTixDQUFvRUMsUUFBcEUsRUFBZDtBQUNBLGtCQUFJQyxRQUFRLEdBQUdILE9BQU8sQ0FBQ0ksSUFBUixDQUFhLFlBQWIsRUFBMkJDLElBQTNCLENBQWdDLE9BQWhDLENBQWY7QUFDQSxrQkFBSUMsT0FBTyxHQUFHTixPQUFPLENBQUNJLElBQVIsQ0FBYSxVQUFiLEVBQXlCRyxHQUF6QixFQUFkO0FBQ0Esa0JBQUlDLE9BQU8sR0FBR1IsT0FBTyxDQUFDSSxJQUFSLENBQWEsVUFBYixFQUF5QkcsR0FBekIsRUFBZDtBQUNBLGtCQUFJRSxNQUFNLEdBQUcsY0FBY0QsT0FBZCxHQUF3QixXQUF4QixHQUFzQ0wsUUFBdEMsR0FBaUQsSUFBakQsR0FBdURHLE9BQXZELEdBQWdFLE1BQTdFO0FBQ0F6QixvQkFBTSxDQUFDNkIsYUFBUCxDQUFxQkQsTUFBckI7QUFDQTVCLG9CQUFNLENBQUNTLGFBQVAsQ0FBcUJxQixLQUFyQjtBQUNBLHFCQUFPLElBQVA7QUFDSDtBQVhMLFdBREs7QUFMYSxTQUExQixFQXFCQTtBQUNJQyxxQkFBVyxFQUFFbkIsZUFBZSxDQUFDb0I7QUFEakMsU0FyQkE7QUF5QkgsT0ExQkQ7QUEyQkgsS0FwQ0c7QUFxQ0g7QUFDSixDQXhDRCIsImZpbGUiOiIuL2Fzc2V0cy9zb3VyY2UvbWNlLWpzL21jZS1idXR0b25zLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uKCkge1xuICAgIGlmICh0eXBlb2YgdGlueW1jZSAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgdGlueW1jZS5QbHVnaW5NYW5hZ2VyLmFkZCgnbWNlX2hiZ19idXR0b25zJywgZnVuY3Rpb24oZWRpdG9yLCB1cmwpIHtcbiAgICAgICAgZWRpdG9yLmFkZEJ1dHRvbignbWNlX2hiZ19idXR0b25zJywge1xuICAgICAgICAgICAgdGV4dDogJ0J1dHRvbicsXG4gICAgICAgICAgICBpY29uOiAnJyxcbiAgICAgICAgICAgIGNvbnRleHQ6ICdpbnNlcnQnLFxuICAgICAgICAgICAgdG9vbHRpcDogJ0FkZCBidXR0b24nLFxuICAgICAgICAgICAgY21kOiAnbWNlX2hiZ19idXR0b25zJ1xuICAgICAgICB9KTtcblxuICAgICAgICBlZGl0b3IuYWRkQ29tbWFuZCgnbWNlX2hiZ19idXR0b25zJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBlZGl0b3Iud2luZG93TWFuYWdlci5vcGVuKHtcbiAgICAgICAgICAgICAgICB0aXRsZTogJ0FkZCBidXR0b24nLFxuICAgICAgICAgICAgICAgIHVybDogbWNlX2hiZ19idXR0b25zLnRoZW1lVXJsICsgJy9saWJyYXJ5L0FkbWluL1RpbnlNY2UvTWNlQnV0dG9ucy9tY2UtYnV0dG9ucy10ZW1wbGF0ZS5waHAnLFxuICAgICAgICAgICAgICAgIHdpZHRoOiA1MDAsXG4gICAgICAgICAgICAgICAgaGVpZ2h0OiA0MjAsXG4gICAgICAgICAgICAgICAgYnV0dG9uczogW1xuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0ZXh0OiAnSW5zZXJ0JyxcbiAgICAgICAgICAgICAgICAgICAgICAgIG9uY2xpY2s6IGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgJGlmcmFtZSA9IGpRdWVyeSgnLm1jZS1jb250YWluZXItYm9keS5tY2Utd2luZG93LWJvZHkubWNlLWFicy1sYXlvdXQgaWZyYW1lJykuY29udGVudHMoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgYnRuQ2xhc3MgPSAkaWZyYW1lLmZpbmQoJyNwcmV2aWV3IGEnKS5hdHRyKCdjbGFzcycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBidG5UZXh0ID0gJGlmcmFtZS5maW5kKCcjYnRuVGV4dCcpLnZhbCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBidG5MaW5rID0gJGlmcmFtZS5maW5kKCcjYnRuTGluaycpLnZhbCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBidXR0b24gPSAnPGEgaHJlZj1cIicgKyBidG5MaW5rICsgJ1wiIGNsYXNzPVwiJyArIGJ0bkNsYXNzICsgJ1wiPicrIGJ0blRleHQgKyc8L2E+JztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlZGl0b3IuaW5zZXJ0Q29udGVudChidXR0b24pO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVkaXRvci53aW5kb3dNYW5hZ2VyLmNsb3NlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBdXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIHN0eWxlc1NoZWV0OiBtY2VfaGJnX2J1dHRvbnMuc3R5bGVTaGVldFxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgKTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG4gICAgfVxufSkoKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./assets/source/mce-js/mce-buttons.js\n");

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0Esa0RBQTBDLGdDQUFnQztBQUMxRTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGdFQUF3RCxrQkFBa0I7QUFDMUU7QUFDQSx5REFBaUQsY0FBYztBQUMvRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaURBQXlDLGlDQUFpQztBQUMxRSx3SEFBZ0gsbUJBQW1CLEVBQUU7QUFDckk7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7O0FBR0E7QUFDQSIsImZpbGUiOiJqcy9tY2UtYnV0dG9ucy5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSBcIi4vYXNzZXRzL3NvdXJjZS9tY2UtanMvbWNlLWJ1dHRvbnMuanNcIik7XG4iXSwic291cmNlUm9vdCI6IiJ9