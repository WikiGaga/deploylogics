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
              clickableSteps: false // allow step clicking
          });
    
          // Validation before going to next page
            wizard.on('beforeNext', function(wizardObj) {
                if (validator.form() !== true) {
                    wizardObj.stop(); // don't go to the next step
                }

                var selectGroup = $('input[name="selectedGroup"]:checked').val();
                var url = GET_GROUP_CONTACTS_URL;
                url = url.replace(':id', selectGroup);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    method: 'POST',
                    data: {},
                    success: function(response){
                        if(response.status == 'success'){
                            var contacts = response.data.contacts;
                            var html = '';
                            $.each( contacts , function(key , value){
                                html += '<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success w-100" data-search="'+ value.cnt_name.toLowerCase() + ' ' + value.phone_no +'" data-id="'+ value.phone_no +'" id="contact_'+ value.phone_no +'">' +
                                    '<input type="checkbox" class="addInGroupCheckbox" ';
                                    if(value.groups.length > 0){
                                        var group = value.groups[0];
                                        if(group.grp_id == selectGroup){
                                         html += 'checked';
                                        } 
                                     }    
                                html+= ' name="selectedContacts[]" value="'+ value.phone_no +'"> '+ value.cnt_name +' ('+ value.phone_no +') ' +
                                    '<span></span>' + 
                                '</label>';
                            });
                            $('.searchContactToAddGroup_container').html('').html(html);
                            $('#selectAllContacts input').removeAttr('disabled');
                        }
                    },
                    error: function(xhrResponse){
                        toastr.error(xhrResponse.responseJSON.message);
                    }
                });
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
                  selectedGroup: {
                      required: true
                  },
              },
    
              // Display error
              invalidHandler: function(event, validator) {
                  KTUtil.scrollTop();
              },
    
              // Submit valid form
              submitHandler: function(form) {
    
              }
          });
        }
    
        var initSubmit = function() {
            var btn = formEl.find('[data-ktwizard-type="action-groupcontact-submit"]');
            btn.on('click', function(e) {
                e.preventDefault();

                if (validator.form()) {
                    // FormData object 
                    var formData = new FormData(document.getElementById('kt_contact__group_form'));
                    // See: src\js\framework\base\app.js
                    KTApp.progress(btn);
                    // KTApp.block(formEl);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url         : formEl.attr('action'),
                        type        : formEl.attr('method'),
                        dataType	: 'json',
                        data        : formData,
                        cache       : false,
                        contentType : false,
                        processData : false,
                        success: function(response,status) {
                            KTApp.unprogress(btn);
                            // KTApp.unblock(formEl);
                            if(response.status == 'success'){
                                toastr.success(response.message);
                                prevBtn.trigger('click');
                                $('.searchContactToAddGroup_container').html('').html('<div class="text-center my-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
                                
                            }else{
                                KTApp.unprogress(btn);
                                // KTApp.unblock(formEl);
                                toastr.error(response.message);
                            }
                        }
                    });
                }
            });
        }
        
        function groupModalHandeling(){
            $("#createWhatsAppGroup").on("hidden.bs.modal", function () {
                var prevBtn = formEl.find('[data-ktwizard-type="action-prev"]');
                prevBtn.trigger('click');
                $('.searchContactToAddGroup_container').html('').html('<div class="text-center my-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
                $('#inputSelectAllContacts').prop('checked' , false);
                $('#inputSelectAllContacts').prop('disabled' , true);
            });
        }

        function initCustomFunctions(){
            $(document).on('change' , '#inputSelectAllContacts' , function(){
                if($(this).is(":checked")){
                    $('input.addInGroupCheckbox').prop("checked" , true);
                }else{
                    $('input.addInGroupCheckbox').prop('checked' , false);
                }
            });
        }
      return {
          // public functions
          init: function() {
              wizardEl = KTUtil.get('kt_wizard_v2');
              formEl = $('#kt_contact__group_form');
    
              initWizard();
              initValidation();
              initSubmit();
              groupModalHandeling();
              initCustomFunctions();
          }
      };
    }();
    
    jQuery(document).ready(function() {
      KTWizard2.init();
    });
    
    
    //# sourceURL=webpack:///../src/assets/js/pages/custom/wizard/wizard-2.js?
    
    /***/ })
    
    /******/ });