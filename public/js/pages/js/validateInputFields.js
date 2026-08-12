function sanitizeNumericInput(value) {
    if (value === '' || value === undefined || value === null) {
        return value;
    }
    var strVal = String(value).trim();
    if (strVal.indexOf('.') === 0) {
        strVal = '0' + strVal;
    } else if (strVal.indexOf('-.') === 0) {
        strVal = '-0' + strVal.substring(1);
    }
    value = strVal.replace(/[^\d.-]/g, '');
    var dotIndex = value.indexOf('.');
    if (dotIndex !== -1) {
        value = value.substring(0, dotIndex + 1) + value.substring(dotIndex + 1).replace(/\./g, '');
    }
    if (value.indexOf('.') === 0) {
        value = '0' + value;
    }
    return value;
}

(function($) {
    if (typeof $ !== 'undefined' && $.fn && !$.fn.val._leadingZeroPatched) {
        var originalVal = $.fn.val;
        var patchedVal = function(value) {
            if (arguments.length > 0 && value !== undefined && value !== null) {
                if (this.length) {
                    var strVal = String(value).trim();
                    if (/^\.\d+/.test(strVal) || /^-\.\d+/.test(strVal)) {
                        var shouldSanitize = false;
                        for (var i = 0; i < this.length; i++) {
                            var el = this[i];
                            if (el && el.tagName === 'INPUT') {
                                var inputType = (el.type || 'text').toLowerCase();
                                if (inputType === 'text' || inputType === 'number' || inputType === 'hidden' || inputType === 'tel' || !inputType) {
                                    shouldSanitize = true;
                                    break;
                                }
                            }
                        }
                        if (shouldSanitize) {
                            value = strVal.indexOf('.') === 0 ? ('0' + strVal) : ('-0' + strVal.substring(1));
                        }
                    }
                }
            }
            return originalVal.apply(this, arguments);
        };
        patchedVal._leadingZeroPatched = true;
        $.fn.val = patchedVal;
    }
})(jQuery);

function validateNumber(event) {
    event = (event) ? event : window.event;
    var charCode = (event.which) ? event.which : event.keyCode;
    if (charCode === 8 || charCode === 0 || charCode === 13) {
        return true;
    }
    var val = String.fromCharCode(charCode);
    var validateNum = ['1','2','3','4','5','6','7','8','9','0','.'];
    if(!validateNum.includes(val)) {
        return false;
    }
    if (val === '.') {
        var input = event.target || event.srcElement;
        if (input && input.tagName === 'INPUT') {
            var currentValue = input.value || '';
            var start = typeof input.selectionStart === 'number' ? input.selectionStart : currentValue.length;
            var end = typeof input.selectionEnd === 'number' ? input.selectionEnd : currentValue.length;
            var withoutSelection = currentValue.substring(0, start) + currentValue.substring(end);
            if (withoutSelection.indexOf('.') !== -1) {
                return false;
            }
        }
    }
    return true;
}
function OnlyEnterAllow(event) {
    // 13 = enter
    var key = window.event ? event.keyCode : event.which;
    if (event.keyCode === 13) {
        return true;
    } else {
        return false;
    }
}
//number pattern validator
// var dashCount = 0;
function allowNumberDash(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode == 45) {
        dashCount++;
    }
    if ((charCode > 47 && charCode <58) || (charCode == 45 && dashCount < 2))
    {
        return true;
    }
  return false;
}
//end number pattern validator

function validateOnlyFloatNumber(event) {
    $(".validOnlyFloatNumber").blur(function() {
        if(this.value != "") {
            this.value = parseFloat(this.value).toFixed(3);
        }
    });
    $(".tblGridCal_discount,.tblGridCal_vat_perc,.tblGridCal_rate").blur(function() {
        if(this.value != "") {
            this.value = parseFloat(this.value).toFixed(3);
        }
    });
    $(".debit,.credit").blur(function() {
        if(this.value != "") {
            this.value = parseFloat(this.value).toFixed(3);
        }
    });
};

function setTextLength(){
    $(".short_text").attr('maxlength','20');
    $(".small_text").attr('maxlength','50');
    $(".medium_text").attr('maxlength','100');
    $(".large_text").attr('maxlength','255');
    $(".long_text").attr('maxlength','500');
    $(".double_text").attr('maxlength','800');
    $(".small_no").attr('maxlength','5');
    $(".medium_no").attr('maxlength','8');
    $(".large_no").attr('maxlength','10');
    $(".mob_no").attr('maxlength','15');
}

$(document).ready(function() {
    $('.validNumber,.validNo').keypress(validateNumber);
    $('.OnlyEnterAllow').keypress(OnlyEnterAllow);
    $('.AllowNumberDash').keypress(allowNumberDash);
    $('.validOnlyFloatNumber').keypress(validateOnlyFloatNumber);
    $('.debit').keypress(validateOnlyFloatNumber);
    $('.short_text,.small_text,.medium_text,.large_text,.long_text,.double_text,.small_no,.medium_no,.large_no,.mob_no').keypress(setTextLength);
    $(document).on('input paste change keyup blur', '.validNumber,.validNo,.validOnlyFloatNumber,.debit,.credit,.fcdebit,.fccredit,.vatperc,.vatamt,.netamt,.amount,.deduct_credit,.expense_amount,.expense_perc,.tblGridCal_qty,.tblGridCal_rate,.tblGridCal_amount,.tblGridCal_discount_perc,.tblGridCal_discount_amount,.tblGridCal_vat_perc,.tblGridCal_vat_amount,.tblGridCal_gross_amount,.tblGridSale_rate,.fc_rate', function() {
        var self = this;
        setTimeout(function() {
            var sanitized = sanitizeNumericInput(self.value);
            if (sanitized !== self.value) {
                self.value = sanitized;
            }
        }, 0);
    });
});


