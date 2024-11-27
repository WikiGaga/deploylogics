$('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
    var val = $(this).val();
    $('.ErpForm>thead>tr').find('th:eq('+val+')').toggle();
    $('.ErpForm>thead>tr').find('td:eq('+val+')').toggle();
    $('.ErpForm>tbody>tr').find('td:eq('+val+')').toggle();
    $('.ErpForm>thead>tr').find('td:eq('+val+')>input').toggleClass('moveIndex');
    $('.ErpForm>tbody>tr').find('td:eq('+val+')>input').toggleClass('moveIndex');
    hiddenFiledsCount()
});
function hiddenFiledsCount(){
    var count = 0;
    var hiddenFiled = [];
    $('.dropdown-menu>li').each(function(){
        if(!$(this).find('label>input').is(':checked')){
            count += 1;
            hiddenFiled.push($(this).find('label>input').val());
        }
    });
    setCookie(hiddenFieldsFormName, '');
    setCookie(hiddenFieldsFormName, hiddenFiled);
    $('.hiddenFiledsCount>span').html(count);
}

function setCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue+"; expires=Sat, 31 Dec 2023 00:00:00 GMT";
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function updateHiddenFields(){
    if($( window ).width() <= 1024){
        if(getCookie(hiddenFieldsFormName) == ''){
            var salesForm = MobHiddenTd;
        }else{
            var salesForm = getCookie(hiddenFieldsFormName);
        }
    }else{
        var salesForm = getCookie(hiddenFieldsFormName);
    }
    if(salesForm != ''){
        var salesForm = salesForm.split(",")
        $('.hiddenFiledsCount>span').html(salesForm.length);
        for(var i=0;i < salesForm.length; i++){
            $('.dropdown-menu>li').each(function(){
                if($(this).find('label>input').val() == salesForm[i]){
                    $(this).find('label>input').prop('checked',false)
                    $('.ErpForm>thead>tr').find('th:eq('+salesForm[i]+')').hide();
                    $('.ErpForm>thead>tr').find('td:eq('+salesForm[i]+')').hide();
                    $('.ErpForm>thead>tr').find('td:eq('+salesForm[i]+')>input').removeClass('moveIndex');
                    $('.ErpForm>tbody>tr').each(function(){
                        $(this).find('td:eq('+salesForm[i]+')').hide();
                        $(this).find('td:eq('+salesForm[i]+')>input').removeClass('moveIndex');
                    });
                }
            });
        }
    }
}
updateHiddenFields();
