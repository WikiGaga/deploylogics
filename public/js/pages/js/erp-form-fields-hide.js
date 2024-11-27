$('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
    var val = $(this).val();
    $('.erp_form__grid>thead>tr').find('th:eq('+val+')').toggle();
    $('.erp_form__grid>tbody>tr').find('td:eq('+val+')').toggle();
    giveWidthToTh();
    $('.erp_form__grid>thead>tr').find('th:eq('+val+') input').toggleClass('tb_moveIndex');
    $('.erp_form__grid>thead>tr').find('th:eq('+val+') select').toggleClass('tb_moveIndex');
    $('.erp_form__grid>tbody>tr').find('td:eq('+val+')>input').toggleClass('tb_moveIndex');
    $('.erp_form__grid>tbody>tr').find('td:eq('+val+')>select').toggleClass('tb_moveIndex');
    hiddenFiledsCount();
});
function giveWidthToTh(){
    var visibleColLen = $('.erp_form__grid>thead>tr>th').filter(":visible").length;
    $('.table-scroll>.JCLRgrips').css({'width':Math.round($('.table-scroll').width())+'px'})
    var c = 0;
    $('.erp_form__grid>thead>tr>th').each(function(){
        if($(this).css('display') != 'none'){
            c++;
        }else{
            $(this).css({'width':''})
        }
    });
    $('.erp_form__grid>thead>tr>th').each(function(){
        var index = $(this).index();
        if(index == 0){
            $(this).css({'width':'35px'});
        }
        if(index == ($('.erp_form__grid>thead>tr>th').length-1)){
            $(this).css({'width':'48px'});
        }
        var w = ($('.table-scroll').width()-35-48)/(c-2);
        if($(this).css('display') != 'none' && index != 0 && index != ($('.erp_form__grid>thead>tr>th').length-1)){
            $(this).css({'width':Math.round(w)+'px'})
        }
        if(index <= (visibleColLen-2)){
            var left = 35+(w*index);
            $('.table-scroll>.JCLRgrips>.JCLRgrip:eq('+index+')').css({'left':Math.round(left)+'px'})
        }else{
            $('.table-scroll>.JCLRgrips>.JCLRgrip:eq('+index+')').css({'left':''})
        }
    });
}
function hiddenFiledsCount(){
    var count = 0;
    var hiddenFiled = [];
    $('.dropdown-menu>li').each(function(){
        if(!$(this).find('label>input').is(':checked')){
            // console.log($(this).find('label>input').val());
            if($(this).find('label>input').val() !== undefined){
                count += 1;
                hiddenFiled.push($(this).find('label>input').val());
            }
           // hiddenFiled.push($(this).find('label>input').val());
        }
    });

    if (findCookie(hiddenFieldsFormName)) {
        delCookie(hiddenFieldsFormName);
        setCookie(hiddenFieldsFormName, hiddenFiled);
    } else {
        setCookie(hiddenFieldsFormName, hiddenFiled);
    }
    $('.hiddenFiledsCount>span').html(count);
}
function setCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue+"; Path=/; expires=Sat, 31 Dec 2022 00:00:00 GMT";
}
function delCookie(name) {
    let cookies = document.cookie.split(";");
    cookies.forEach(function(cookie) {
        cookie = cookie.split(/=(.+)/);
        if (cookie[0] == name) {
            document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }
    });
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
function findCookie(name) {
    let cookies = document.cookie;
    let cookiestore = {};

    cookies = cookies.split(";");

    if (cookies[0] == "" && cookies[0][0] == undefined) {
        return undefined;
    }

    cookies.forEach(function(cookie) {
        cookie = cookie.split(/=(.+)/);
        if (cookie[0].substr(0, 1) == ' ') {
            cookie[0] = cookie[0].substr(1);
        }
        cookiestore[cookie[0]] = cookie[1];
    });

    return (name !== '' ? cookiestore[name] : cookiestore);
}
function updateHiddenFields(){
    if($( window ).width() <= 1024){
        if (typeof hiddenFieldsFormName !== 'undefined'){
            if(getCookie(hiddenFieldsFormName) == ''){
                var salesForm = MobHiddenTd;
            }else{
                var salesForm = getCookie(hiddenFieldsFormName);
            }
        }else{
            var salesForm = '';
        }
    }else{
        if (typeof hiddenFieldsFormName !== 'undefined'){
            var salesForm = getCookie(hiddenFieldsFormName);
        }else{
            var salesForm = '';
        }
    }
    if(salesForm != ''){
        var salesFormArry = salesForm.split(",")
        var salesForm = [];
        salesFormArry.forEach(function(item, index){
            if(item !== "" && item !== undefined){
                salesForm.push(item)
            }
        })
        setCookie(hiddenFieldsFormName, salesForm);
        $('.hiddenFiledsCount>span').html(salesForm.length);
        for(var i=0;i < salesForm.length; i++){
            $('.dropdown-menu>li').each(function(){
                if($(this).find('label>input').val() == salesForm[i]){
                    $(this).find('label>input').prop('checked',false)
                    $('.erp_form__grid>thead>tr').find('th:eq('+salesForm[i]+')').hide();
                    $('.erp_form__grid>thead>tr').find('th:eq('+salesForm[i]+') input').removeClass('tb_moveIndex');
                    $('.erp_form__grid>thead>tr').find('th:eq('+salesForm[i]+') select').removeClass('tb_moveIndex');
                    $('.erp_form__grid>tbody>tr').each(function(){
                        $(this).find('td:eq('+salesForm[i]+')').hide();
                        $(this).find('td:eq('+salesForm[i]+')>input').removeClass('tb_moveIndex');
                        $(this).find('td:eq('+salesForm[i]+')>select').removeClass('tb_moveIndex');
                    });
                }
            });
        }
    }
    giveWidthToTh();
}
updateHiddenFields();

function emptyCookie() {
    let cookies = document.cookie.split(";");
    var arrCookies = [];
    // get all cookies name add into array
    cookies.forEach(function(cookie) {
        cookie = cookie.split(/=(.+)/);
        arrCookies.push(cookie[0].trim());
    });
    // get duplicate name into array
    var duplicateArr = [];
    arrCookies.sort();
    arrCookies.forEach(function (value, index, arr){
        let first_index = arr.indexOf(value);
        let last_index = arr.lastIndexOf(value);
         if(first_index !== last_index){
             if(!duplicateArr.includes(value)){
                 duplicateArr.push(value);
            }
         }
    });
   // duplicate cookie remove
    path = location.pathname.replace('/form','');
    for(var j=0; j < duplicateArr.length; j++){
        cookies.forEach(function(cookie) {
            cookie = cookie.split(/=(.+)/);
            var val = cookie[0].trim();
            if (val == duplicateArr[j].trim()) {
                document.cookie = cookie[0] +'=; path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                document.cookie = cookie[0] +'=; path='+location.pathname+'; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                document.cookie = cookie[0] +'=; path='+path+'; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }
        });
    }
    // if any cookie has value empty then remove it
    cookies.forEach(function(cookie) {
        // delete duplicate cookies
        cookie = cookie.split(/=(.+)/);
        if (cookie[1] == undefined && cookie[0] !=  ' XSRF-TOKEN') {
            document.cookie = cookie[0] +'=; path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            document.cookie = cookie[0] +'=; path='+location.pathname+'; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            document.cookie = cookie[0] +'=; path='+path+'; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }
    });
}
emptyCookie();
