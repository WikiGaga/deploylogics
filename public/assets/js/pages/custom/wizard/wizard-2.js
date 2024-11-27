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
/******/ 	return __webpack_require__(__webpack_require__.s = "../src/assets/js/pages/custom/wizard/wizard-2.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "../src/assets/js/pages/custom/wizard/wizard-2.js":
/*!********************************************************!*\
  !*** ../src/assets/js/pages/custom/wizard/wizard-2.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Class definition
var KTWizard2 = function() {
  // Base elements
  var wizardEl;
  var formEl;
  var validator;
  var wizard;

  // Private functions
  var initWizard = function() {
      // Initialize form wizard
      wizard = new KTWizard('kt_wizard_v2', {
          startStep: 1, // initial active step number
          clickableSteps: true // allow step clicking
      });

      // Validation before going to next page
      wizard.on('beforeNext', function(wizardObj) {
          if (validator.form() !== true) {
              wizardObj.stop(); // don't go to the next step
          }
      });

      wizard.on('beforePrev', function(wizardObj) {
          if (validator.form() !== true) {
              wizardObj.stop(); // don't go to the next step
          }
      });

      // Change event
      wizard.on('change', function(wizard) {
          KTUtil.scrollTop();
      });
  }

  var initValidation = function() {
      validator = formEl.validate({
          // Validate only visible fields
          ignore: ":hidden",

          // Validation rules
          rules: {
              //= Step 1
              fname: {
                  required: true
              },
              lname: {
                  required: true
              },
              phone: {
                  required: true
              },
              emaul: {
                  required: true,
                  email: true
              },

              //= Step 2
              address1: {
                  required: true
              },
              postcode: {
                  required: true
              },
              city: {
                  required: true
              },
              state: {
                  required: true
              },
              country: {
                  required: true
              },

              //= Step 3
              delivery: {
                  required: true
              },
              packaging: {
                  required: true
              },
              preferreddelivery: {
                  required: true
              },

              //= Step 4
              locaddress1: {
                  required: true
              },
              locpostcode: {
                  required: true
              },
              loccity: {
                  required: true
              },
              locstate: {
                  required: true
              },
              loccountry: {
                  required: true
              },

              //= Step 5
              ccname: {
                  required: true
              },
              ccnumber: {
                  required: true,
                  creditcard: true
              },
              ccmonth: {
                  required: true
              },
              ccyear: {
                  required: true
              },
              cccvv: {
                  required: true,
                  minlength: 2,
                  maxlength: 3
              },
          },

          // Display error
          invalidHandler: function(event, validator) {
              KTUtil.scrollTop();

              swal.fire({
                  "title": "",
                  "text": "There are some errors in your submission. Please correct them.",
                  "type": "error",
                  "confirmButtonClass": "btn btn-secondary"
              });
          },

          // Submit valid form
          submitHandler: function(form) {

          }
      });
  }

  var initSubmit = function() {
      var btn = formEl.find('[data-ktwizard-type="action-submit"]');

      btn.on('click', function(e) {
          e.preventDefault();

          if (validator.form()) {
              // See: src\js\framework\base\app.js
              KTApp.progress(btn);
              //KTApp.block(formEl);

              // See: http://malsup.com/jquery/form/#ajaxSubmit
              formEl.ajaxSubmit({
                  success: function() {
                      KTApp.unprogress(btn);
                      //KTApp.unblock(formEl);

                      swal.fire({
                          "title": "",
                          "text": "The application has been successfully submitted!",
                          "type": "success",
                          "confirmButtonClass": "btn btn-secondary"
                      });
                  }
              });
          }
      });
  }

  return {
      // public functions
      init: function() {
          wizardEl = KTUtil.get('kt_wizard_v2');
          formEl = $('#kt_form');

          initWizard();
          initValidation();
          initSubmit();
      }
  };
}();

jQuery(document).ready(function() {
  KTWizard2.init();
});


//# sourceURL=webpack:///../src/assets/js/pages/custom/wizard/wizard-2.js?

/***/ })

/******/ });