function validateNumber(event) {
    event = (event) ? event : window.event;
    var charCode = (event.which) ? event.which : event.keyCode;
    var val = String.fromCharCode(charCode);
    var validateNum = ['1','2','3','4','5','6','7','8','9','0','.'];
    if(!validateNum.includes(val)) {
        return false;
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
});

