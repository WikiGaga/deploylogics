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
/******/ 	return __webpack_require__(__webpack_require__.s = "../src/assets/js/pages/custom/chat/chat.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "../src/assets/js/pages/custom/chat/chat.js":
/*!**************************************************!*\
  !*** ../src/assets/js/pages/custom/chat/chat.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Class definition
var KTAppChat = function() {
  var chatAsideEl;
  var chatContentEl;

  // Private functions
    var initAside = function() {
      // Mobile offcanvas for mobile mode
      var offcanvas = new KTOffcanvas(chatAsideEl, {
          overlay: true,
          baseClass: 'kt-app__aside',
          closeBy: 'kt_chat_aside_close',
          toggleBy: 'kt_chat_aside_mobile_toggle'
      });

      // User listing 
      var userListEl = KTUtil.find(chatAsideEl, '.kt-scroll');
      if (!userListEl) {
          return;
      }

      // Initialize perfect scrollbar(see:  https://github.com/utatti/perfect-scrollbar) 
      KTUtil.scrollInit(userListEl, {
          mobileNativeScroll: true, // enable native scroll for mobile
          desktopNativeScroll: true, // disable native scroll and use custom scroll for desktop 
          resetHeightOnDestroy: true, // reset css height on scroll feature destroyed
          handleWindowResize: true, // recalculate hight on window resize
          rememberPosition: true, // remember scroll position in cookie
          height: function() { // calculate height
              var height;
              var portletBodyEl = KTUtil.find(chatAsideEl, '.kt-portlet > .kt-portlet__body');
              var widgetEl = KTUtil.find(chatAsideEl, '.kt-widget.kt-widget--users');
              var searchbarEl = KTUtil.find(chatAsideEl, '.kt-searchbar');

              if (KTUtil.isInResponsiveRange('desktop')) {
                  height = KTLayout.getContentHeight();
              } else {
                  height = KTUtil.getViewPort().height;
              }

              if (chatAsideEl) {
                  height = height - parseInt(KTUtil.css(chatAsideEl, 'margin-top')) - parseInt(KTUtil.css(chatAsideEl, 'margin-bottom'));
                  height = height - parseInt(KTUtil.css(chatAsideEl, 'padding-top')) - parseInt(KTUtil.css(chatAsideEl, 'padding-bottom'));
              }

              if (widgetEl) {
                  height = height - parseInt(KTUtil.css(widgetEl, 'margin-top')) - parseInt(KTUtil.css(widgetEl, 'margin-bottom'));
                  height = height - parseInt(KTUtil.css(widgetEl, 'padding-top')) - parseInt(KTUtil.css(widgetEl, 'padding-bottom'));
              }

              if (portletBodyEl) {
                  height = height - parseInt(KTUtil.css(portletBodyEl, 'margin-top')) - parseInt(KTUtil.css(portletBodyEl, 'margin-bottom'));
                  height = height - parseInt(KTUtil.css(portletBodyEl, 'padding-top')) - parseInt(KTUtil.css(portletBodyEl, 'padding-bottom'));
              }

              if (searchbarEl) {
                  height = height - parseInt(KTUtil.css(searchbarEl, 'height'));
                  height = height - parseInt(KTUtil.css(searchbarEl, 'margin-top')) - parseInt(KTUtil.css(searchbarEl, 'margin-bottom'));
              }

              // remove additional space
              height = height - 5;

              return height;
          }
      });
    }

  return {
      // public functions
      init: function() {
          // elements
          chatAsideEl = KTUtil.getByID('kt_chat_aside');

          // init aside and user list
          initAside();

          // init inline chat example
          KTChat.setup(KTUtil.getByID('kt_chat_content'));

          // trigger click to show popup modal chat on page load
          if (KTUtil.getByID('kt_app_chat_launch_btn')) {
              setTimeout(function() {
                  KTUtil.getByID('kt_app_chat_launch_btn').click();
              }, 1000);
          }
      }
  };
}();

KTUtil.ready(function() {
  KTAppChat.init();
});

//# sourceURL=webpack:///../src/assets/js/pages/custom/chat/chat.js?

/***/ })

/******/ });