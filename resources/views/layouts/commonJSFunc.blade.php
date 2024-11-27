<script>
    $(".date_inputmask").inputmask("99-99-9999", {
        "mask": "99-99-9999",
        "placeholder": "dd-mm-yyyy",
        autoUnmask: false
    });

    $(document).ready(function(){
        var listing = '{{ Request::segment(2) }}';
        if(getCookie(listing + '_listing_search')){
            $('#generalSearch').val(getCookie(listing + '_listing_search'));
        }
    });

    $(document).on('keyup' , '#generalSearch' , function(e){
        var val = $(this).val();
        var listing = '{{ Request::segment(2) }}';
        if(getCookie(listing + '_listing_search')){
            deleteCookie(listing + '_listing_search');
            setCookie(listing + '_listing_search' , val);
        }else{
            setCookie(listing + '_listing_search' , val);
        }
        if(val == ""){
            deleteCookie(listing + '_listing_search');
        }
    });

    //Calculate The Budget
    function calculateBudget(trueOnZero = false){
        var budget = $('#donater_budget').val();
        if(budget == ""){return false;}else{ budget = parseFloat(budget); }
        var remaningBudget = budget;
        $(document).find('.coupon-container').each((el)=>{
            var containerPrice,qty,value = 0;
            var element = $('.coupon-container')[el];
            qty = element.querySelector('.coupon_qty').value;
            if(qty == ""){ qty = 0;}
            value = element.querySelector('.coupon_value').value;
            if(value == ""){ value = 0;}
            containerPrice = parseFloat(parseFloat(qty)*parseFloat(value));
            remaningBudget = parseFloat(remaningBudget-containerPrice);
        });
        $('#remaning_budget').html('').html(remaningBudget);
        $('#remaning_budget_input').val(remaningBudget);
        if(remaningBudget == 0){
            $('.generateBenificery').removeClass('d-none');
        }else{
            $('.generateBenificery').addClass('d-none');
        }
        if(trueOnZero){
            if(remaningBudget == 0){
                return true;
            }
        }else{
            if(remaningBudget <= 0){
                return false;
            }
        }
        return true;
    }

    $(document).on('blur', ".date_inputmask,input[data-id='expiry_date']" , function validatedate(e) {
        var inputText = $(this);
        var thsid = inputText.attr('id');
        var classname = inputText.attr('class');
        var classstr = classname.split(" ");
        var ret = true;
        var dateformat = /^(0?[1-9]|[12][0-9]|3[0-9])[\/\-](\d{2})[\/\-]\d{4}$/;
        var date = inputText.val();
        // Create list of days of a month [assume there is no leap year by default]
        var ListofDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        if (date.trim() != "") {
            if (date.trim().match(dateformat)) {
                // document.form1.text1.focus();
                //Test which seperator is used '/' or '-'
                var opera1 = date.split('/');
                var opera2 = date.split('-');
                lopera1 = opera1.length;
                lopera2 = opera2.length;
                // Extract the string into month, date and year
                if (lopera1 > 1) {
                    var pdate = date.split('/');
                } else if (lopera2 > 1) {
                    var pdate = date.split('-');
                }
                var dd = parseInt(pdate[0]);
                var mm = parseInt(pdate[1]);
                var yy = parseInt(pdate[2]);
                var month=12,day=31;

                if (mm == 1 || mm > 2) {
                    (mm > ListofDays.length) ? month = 12 : month = mm;
                    (dd > ListofDays[month - 1]) ? day = ListofDays[month - 1] : day = dd;

                    (day <= 9) ? day = ('0' + day).slice(-2) : day;
                    (month <= 9) ? month = ('0' + month).slice(-2) : month;

                    var fixdate = [day,month,yy].join('-');
                    inputText.val(fixdate);
                }
                if (mm == 2) {
                    var lyear = false;
                    if ((!(yy % 4) && yy % 100) || !(yy % 400)) {
                        lyear = true;
                    }
                    if ((lyear == false) && (dd >= 29)) {
                        month = mm;
                        (dd > ListofDays[month - 1]) ? day = ListofDays[month - 1] : day = dd;

                        (day <= 9) ? day = ('0' + day).slice(-2) : day;
                        (month <= 9) ? month = ('0' + month).slice(-2) : month;

                        var fixdate = [day,month,yy].join('-');
                        inputText.val(fixdate);
                    }
                    if ((lyear == true) && (dd > 29)) {

                        month = mm;
                        (dd > ListofDays[month - 1]) ? day = 29 : day = dd;

                        (day <= 9) ? day = ('0' + day).slice(-2) : day;
                        (month <= 9) ? month = ('0' + month).slice(-2) : month;

                        var fixdate = [day,month,2099].join('-');
                        inputText.val(fixdate);
                    }
                }
            } else {
                var opera1 = date.split('/');
                var opera2 = date.split('-');
                var pdate = opera2;
                lopera1 = opera1.length;
                lopera2 = opera2.length;
                // Extract the string into month, date and year
                if (lopera1 > 1) {
                    pdate = date.split('/');
                } else if (lopera2 > 1) {
                    pdate = date.split('-');
                }
                var dd = parseInt(pdate[0]);
                var mm = parseInt(pdate[1]);
                var yy = parseInt(pdate[2]);
                var month=12,day=31;

                (mm > ListofDays.length) ? month = 12 : month = mm;
                (dd > ListofDays[month - 1]) ? day = ListofDays[month - 1] : day = dd;

                (day <= 9) ? day = ('0' + day).slice(-2) : day;
                (month <= 9) ? month = ('0' + month).slice(-2) : month;
                var currentYear = new Date().getFullYear();
                var fixdate = [day,month,currentYear].join('-');
                inputText.val(fixdate);
            }
        }
    });

    function add_zero(your_number, length) {
        var num = '' + your_number;
        while (num.length < length) {
            num = '0' + num;
        }
        return num;
    }


    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "2000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    /* Modal Function */

    /*$('.open-modal').on('click', function(e){
        e.preventDefault();
        $('#kt_modal_KTDatatable_local').modal('show').find('.modal-content').load($(this).attr('href'));
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });
    });*/
    function openModal(data_url){
        $('#kt_modal_KTDatatable_local').modal('show').find('.modal-content').load(data_url);
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });
    }
    function closeModal(){
        $('.modal').find('.modal-content').empty();
        $('.modal').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
        $('.modal').modal('hide');
    }
    $(".modal").on('click', '.close', function (e) {
        $('#kt_modal_md').find('.modal-content').empty();
        $('#kt_modal_md').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
        $('#kt_modal_KTDatatable_local').find('.modal-content').empty();
        $('#kt_modal_KTDatatable_local').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
    });

    /*$(".getSubMenu").on('click', function (e) {
        var old_url =  window.location.href;
        var new_url = $(this).find('a.kt-menu__link').attr('href');
        var type = old_url.slice(-4);
        if(type == 'form'){
            var isValid = '';
            $( "form" ).find('input').each(function() {
                if ($(this).val() != "") {
                    isValid = true;
                }
            });
            if(isValid == true){
                e.preventDefault();
                swal.fire({
                    title: 'Some Fields are Fill',
                    text: "Do You Want To Redirect",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ok'
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = new_url;
                    }
                });
            }
        }
    });
    */

    /* select menu */
    setNavigation();
    function setNavigation() {
        var path = window.location.pathname;
        var pathArr = path.split("/");
/*        if(pathArr.length == 3 && (pathArr[2] == 'form' || pathArr[2] == 'new')){
            var currentPage = 'form';
        }
        if(pathArr.length == 3 && pathArr[1] == 'listing'){
            var currentPage = pathArr[2];
        }
        if(pathArr.length == 4){
            if(pathArr[2] == 'form' || pathArr[2] == 'edit'){
                var currentPage = 'form';
            }
            if(pathArr[1] == 'reports'){
                var currentPage = 'reports';
            }
        }
        if(pathArr.length == 5){
            if(pathArr[3] == 'form'){
                var currentPage = 'form';
            }
        }

        // get page name
        if(currentPage == 'form' && pathArr.length == 5){
            var currentPath = pathArr[1]+'/'+pathArr[2];
        }
        if(currentPage == 'form' && pathArr.length == 4){
            var currentPath = pathArr[1];
        }
        if(currentPage == 'reports' && pathArr.length == 4){
            var currentPath = pathArr[2]+'/'+pathArr[3];
        }
        if(currentPage == 'form' && pathArr.length == 3){
            var currentPath = pathArr[1];
        }
        if(pathArr[1] == 'home'){
            var currentPath = pathArr[1];
        }*/


        if(pathArr.length == 3 && pathArr[1] == 'listing'){
            var currentPath = pathArr[2];
        }
        if(pathArr.length == 3 && (pathArr[2] == 'form' || pathArr[2] == 'new')){
            var currentPath = pathArr[1];
        }
        if(pathArr.length == 3 && pathArr[1] == 'accounts'){
            var currentPath = pathArr[1]+'/'+pathArr[2];
        }

        if(pathArr.length == 4 && (pathArr[2] == 'form' || pathArr[2] == 'edit')){
            var currentPath = pathArr[1];
        }
        if(pathArr.length == 4 && pathArr[3] == 'form'){
            var currentPath = pathArr[1]+'/'+pathArr[2];
        }
        if(pathArr.length == 4 && (pathArr[1] == 'listing' || pathArr[1] == 'reports')){
            var currentPath = pathArr[2]+'/'+pathArr[3];
        }
        if(pathArr.length == 5 && pathArr[3] == 'form'){
            var currentPath = pathArr[1]+'/'+pathArr[2];
        }
        if(pathArr[1] == 'home'){
            var currentPath = pathArr[1];
        }
       // console.log(currentPath +' == '+ pathArr.length);
        $('.getSubMenu>a.kt-menu__link').each(function(){
            var url = $(this).attr('href');
            var li = $(this).parents('li');
            var ul = li.parents('ul');
            var dv = ul.parents('div');
            var P_li = dv.parents('li');
            var urlArr = url.split("/");
            if(urlArr.length == 4){
                var currentUrl = urlArr[2]+'/'+urlArr[3];
            }
            if(urlArr.length == 4 && urlArr[1] == 'reports'){
                var currentUrl = urlArr[2]+'/'+urlArr[3];
            }
            if(urlArr.length == 3){
                var currentUrl = urlArr[2];
            }
            if(urlArr.length == 3 && urlArr[2] == 'form'){
                var currentUrl = urlArr[1];
            }
            if(urlArr.length == 3 && urlArr[1] == 'accounts'){
                var currentUrl = pathArr[1]+'/'+pathArr[2];
            }
            if(urlArr.length == 2){
                var currentUrl = urlArr[1];
            }
          //  console.log(currentPath +' == '+ currentUrl);
            if(currentPath == currentUrl){
                P_li.addClass('kt-menu__item__active kt-menu__item--open');
                li.addClass('kt-menu__item__active kt-menu__item--open kt-menu__item--here');
                return false;
            }
        })
    }

    /* all input autocomplete off */
    $('input').attr('autocomplete', 'off');


    /* sidebar minimize */
    $('#kt_aside_toggler').click(function(){
        if($('body').hasClass('kt-aside--minimize')){
            sessionStorage.setItem("sidebar_toggle", "kt-aside--minimize");
        }else{
            sessionStorage.setItem("sidebar_toggle", "");
        }
    });
    /* $('body').addClass(sessionStorage.getItem("sidebar_toggle")); */
    $(function() {
        var $contextMenu = $("#contextMenu");
        $("body").on("contextmenu", 'td[data-id="pd_product_name"],input[data-id="pd_product_name"],input[data-id="product_name"]', function(e) {
            var thix = $(this);
            var val = thix.val();
            var product_id = thix.parents('tr').find('td:first-child>.product_id').val();
            var pd_barcode = thix.parents('tr').find('.pd_barcode').val();
            var pd_barcode_id = thix.parents('tr').find('.product_barcode_id').val();

            $("#contextMenu li a").attr('data-id',product_id);
            $("#contextMenu li a").attr('data-val',val);
            $("#contextMenu li a").attr('data-barcode',pd_barcode);
            $("#contextMenu li a").attr('data-barcode_id',pd_barcode_id);
            $("#contextMenu li.product_card a").attr('href','/product/view/'+product_id);

            localStorage.setItem("product_barcode_barcode", pd_barcode);
            localStorage.setItem("product_barcode_id", pd_barcode_id);
            localStorage.setItem("product_id", product_id);
            $("#contextMenu li.tp_analysis a").attr('href','/smart-product/tp-analysis');

            $contextMenu.css({display: "block",left: e.pageX,top: e.pageY});
            return false;
        });
        $('html').click(function() {
            $("#contextMenu li a").attr('data-id',"");
            $("#contextMenu li a").attr('data-val',"");
            $contextMenu.hide();
        });
        $("#contextMenu li.product a,.search_product_dtl>a,.product_card_detail").click(function(e){
            var thix = $(this);
            if($('#form_type').val() != 'barcode_price_tag'){
                var product_id = thix.attr('data-id');
                var data_url = '/common/get-product-detail/get-product/'+product_id;
                $('#kt_modal_md').modal('show').find('.modal-content').load(data_url);
                $('.modal-dialog').draggable({
                    handle: ".prod_head"
                });
            }
        });
        $("#contextMenu li.item_stock_ledger a").click(function(e){
            var thix = $(this);
            var product_val = thix.attr('data-val');
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var mm2 = String(today.getMonth()).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            var from_date = dd + '-' + mm2 + '-' + yyyy;
            var to_date = dd + '-' + mm + '-' + yyyy;
            var grn_date = $('input[name="grn_date"]').val();
            var formData = {
                report_branch_ids : {{auth()->user()->branch_id}},
                product_id : product_val,
                date_from : from_date,
                date_to : to_date,
                report_case : 'item_stock_ledger',
                report_type :  "static",
                form_file_type : "report"
            }
            console.log(formData);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url         : '{{ action('Report\UserReportsController@staticStore', ['static','static','item_stock_ledger']) }}',
                type        : 'POST',
                dataType	: 'json',
                data        : formData,
                beforeSend: function( xhr ) {
                    $('#progressBar').html('<div class="progress"><div class="progress-bar progress-bar-success progress-bar-animated progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div>');
                    var min = Math.ceil(25);
                    var max = Math.floor(95);
                    var n = Math.floor(Math.random() * (max - min + 1)) + min;
                    $('.progress-bar').animate({width: n+"%"}, 100);
                },
                success: function(response) {
                    console.log(response);
                    $('.progress-bar').animate({width: "100%"}, 100);
                    setTimeout(function(){
                        setTimeout(function(){
                            if(response.status == 'success'){
                                $('#progressBar').html('');
                                toastr.success(response.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                                var win = window.open(response['data']['url'], "report");
                                win.location.reload();
                                //  window.location.href = response['data']['redirect'];
                            }else{
                                toastr.error(response.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                                $('#progressBar').html('');
                            }
                        }, 100);
                    }, 500);
                }
            });


        });
        $("#contextMenu li.product_activity a,.product_card_activity_report").click(function(e){
            var data_url = '/report/criteria-list';
            var thix = $(this);
            var product_id = thix.attr('data-id');
            var product_val = thix.attr('data-val');
            var barcode_val = thix.attr('data-barcode');
            var data = {
                'title':'Product Activity Report',
                'product_id':product_id,
                'code_val':barcode_val,
                'name_val':product_val,
                'btn_id':'generate_report_popup',
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });
            $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,data);
            $('.modal-dialog').draggable({
                handle: ".modal-header"
            });
        });
        $(document).on('click',"#generate_report_popup",function(e){
            var thix = $(this);


            var product_id = thix.parents('.modal-content').find('input[name="product_id"]').val();
            var from_date = thix.parents('.modal-content').find('input[name="date_from"]').val();
            var to_date = thix.parents('.modal-content').find('input[name="date_to"]').val();
            var stores = thix.parents('.modal-content').find('.store').select2().val();
            var all_document_type = thix.parents('.modal-content').find('.all_document_type').select2().val();
            var month_wise_check = thix.parents('.modal-content').find('input[name="month_wise"]').prop('checked');
            var formData = {
                report_branch_ids : [{{auth()->user()->branch_id}}],
                product_id : product_id,
                date_from : from_date,
                store   : stores,
                date_to : to_date,
                all_document_type : all_document_type,
                report_case : 'product_activity',
                report_type :  "static",
                form_file_type : "report"
            }
            if(month_wise_check){
                formData.month_wise = 'on';
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url         : '{{ action('Report\UserReportsController@staticStore', ['static','static','product_activity']) }}',
                type        : 'POST',
                dataType	: 'json',
                data        : formData,
                beforeSend: function( xhr ) {
                    $('#progressBar').html('<div class="progress"><div class="progress-bar progress-bar-success progress-bar-animated progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div>');
                    var min = Math.ceil(25);
                    var max = Math.floor(95);
                    var n = Math.floor(Math.random() * (max - min + 1)) + min;
                    $('.progress-bar').animate({width: n+"%"}, 100);
                },
                success: function(response) {
                    console.log(response);
                    $('.progress-bar').animate({width: "100%"}, 100);
                    setTimeout(function(){
                        setTimeout(function(){
                            if(response.status == 'success'){
                                $('#progressBar').html('');
                                toastr.success(response.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                                var win = window.open(response['data']['url'], "_blank");
                                win.location.reload();
                                //  window.location.href = response['data']['redirect'];
                            }else{
                                toastr.error(response.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                                $('#progressBar').html('');
                            }
                        }, 100);
                    }, 500);
                }
            });


        });
    });

    $(function() {
        var $contextRateMenu = $("#contextRateMenu");
        var rateThix = "";
        $("body").on("contextmenu", ".tblGridCal_rate, .tblGridCal_purc_rate", function(e) {
            $contextRateMenu.html('<div class="kt-spinner kt-spinner--sm kt-spinner--warning" style="top:10px;left:40%"></div>');
            var thix = $(this);
            rateThix = $(this);
            var val = thix.val();
            if($('#form_type').val() == 'st'){
                if(thix.attr('data-id') == 'rate' || thix.attr('id') == 'rate'){
                    return false;
                }
            }
          //  debugger
            var product_id = thix.parents('tr').find('.product_id').val();
            var product_barcode_id = thix.parents('tr').find('.product_barcode_id').val();

            if(product_id != "" && product_barcode_id != ""){
                var formData = {
                    'product_id': product_id,
                    'product_barcode_id': product_barcode_id,
                };
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url         : '{{ action('Purchase\BarcodeController@getBarcodeRate') }}',
                    type        : 'POST',
                    dataType	: 'json',
                    data        : formData,
                    success: function(response) {
                        if(response.status == 'success'){
                            var rate = response['data'].rates;
                            var rateLen = rate.length;
                            var sale_rates = response['data'].sale_rates;
                            var sale_ratesLen = sale_rates.length;
                            var tr = "";
                            for(var i=0;rateLen>i;i++){
                                tr += "<tr>";
                                tr += '<td class="right_rate_list_title">'+rate[i]['name']+':</td><td class="right_rate_list_val">'+parseFloat(rate[i]['rate']).toFixed(3)+'</td>';
                                tr += "</tr>";
                            }
                            tr += "<tr><td colspan='2'><hr style='margin-top: 1px;margin-bottom: 1px'></td></tr>";
                            for(var i=0;sale_ratesLen>i;i++){
                                tr += "<tr>";
                                tr += '<td class="right_rate_list_title">'+sale_rates[i]['name']+':</td><td class="right_rate_list_val">'+parseFloat(sale_rates[i]['rate']).toFixed(3)+'</td>';
                                tr += "</tr>";
                            }
                            $contextRateMenu.css({width:"auto"});
                            var table = '<table class="dropdown-menu right_rate_list"  style="display:block;position:static;margin-bottom:5px; min-width:unset;padding: unset;"><tbody>'+tr+'</tbody></table>';
                            $contextRateMenu.html(table);
                        }
                    }
                });
                $contextRateMenu.css({"z-index":999,display: "block",left: e.pageX,top: e.pageY});
                return false;
            }
        });
        $('html').click(function() {
            $contextRateMenu.hide();
            $contextRateMenu.css({width:"100px"});
        });
        $(document).on('click','#contextRateMenu tr',function(){
            var val = $(this).find('.right_rate_list_val').text();
            rateThix.val(parseFloat(val).toFixed(3));
            console.log("val: "+val);
            var tr = rateThix.parents('tr');
            if($('#form_type').val() == 'grn'){
                changeRateColor(rateThix)
            }
            rateThix = "";
            fcRate(tr);
            amountCalc(tr);
            discount(tr);
            vat(tr);
            grossAmount(tr);
            totalAllGrossAmount();
        })
    });

    //code for voucher
    $(function() {
        var $contextChartMenu = $("#contextChartMenu");
        var $contextCopyDataMenu = $("#contextCopyDataMenu");
        $("body").on("contextmenu", 'input[data-id="account_name"]', function(e) {
            var thix = $(this);
            var val = thix.val();
            var account_id = thix.parents('tr').find('td:first-child>.account_id').val();
            var account_code = thix.parents('tr').find('.acc_code').val();
            $("#contextChartMenu li a").attr('data-id',account_id);
            $("#contextChartMenu li a").attr('data-val',val);
            $("#contextChartMenu li a").attr('data-barcode',account_code);
            //$("#contextMenu li.product_card a").attr('href','/product/edit/'+product_id);

            $contextChartMenu.css({display: "block",left: e.pageX,top: e.pageY});
            return false;
        });
        $('html').click(function() {
            $("#contextChartMenu li a").attr('data-id',"");
            $("#contextChartMenu li a").attr('data-val',"");
            $contextChartMenu.hide();
            $contextCopyDataMenu.hide();
        });
        $("#contextChartMenu li.accounting_ledger a").click(function(e){
            var data_url = '/report/criteria-list';
            var thix = $(this);
            var account_id = thix.attr('data-id');
            var account_name = thix.attr('data-val');
            var account_barcode = thix.attr('data-barcode');
            var data = {
                'title':'Accounting Ledger',
                'account_id':account_id,
                'name_val':account_name,
                'code_val':account_barcode,
                'btn_id':'generate_account_report',
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });
            $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,data);
            $('.modal-dialog').draggable({
                handle: ".modal-header"
            });
        });
        $(document).on('click' , '.branch-item' , function(e){
            e.preventDefault();
            var thix = $(this);
            var branch = thix.data('id');
            var formData = {
                branches : branch
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url         : '{{ action('HomeController@branchStore') }}',
                type        : 'POST',
                data        : formData,
                beforeSend : function(){
                    $('body').addClass('pointerEventsNone');
                },
                success: function(response,status) {
                    $('body').removeClass('pointerEventsNone');
                    if(response.status == 'success'){
                        toastr.success('Branch Switch Successfully');
                        if(response.data.form == 'edit'){
                            window.location.href = response.data.redirect;
                        }else{
                            location.reload();
                        }
                    }else{
                        toastr.error(response.message);
                    }
                },
                error: function(response,status) {
                    // console.log(response.responseJSON);
                    $('body').removeClass('pointerEventsNone');
                    toastr.error(response.responseJSON.message);
                },
            });
        });
        $(document).on('click',"#generate_account_report",function(e){
            var thix = $(this);
            var account_id = thix.parents('.modal-content').find('input[name="account_id"]').val();
            var from_date = thix.parents('.modal-content').find('input[name="date_from"]').val();
            var to_date = thix.parents('.modal-content').find('input[name="date_to"]').val();
            var formData = {
                report_branch_ids : [{{auth()->user()->branch_id}}],
                chart_account : account_id,
                date_from : from_date,
                date_to : to_date,
                report_case : 'accounting_ledger',
                report_type :  "static",
                form_file_type : "report",
                accounting_ledger_ob_toggle: 'on'
            }
            console.log(formData);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url         : '{{ action('Report\UserReportsController@staticStore', ['static','static','accounting_ledger']) }}',
                type        : 'POST',
                dataType	: 'json',
                data        : formData,
                beforeSend: function( xhr ) {
                    $('#progressBar').html('<div class="progress"><div class="progress-bar progress-bar-success progress-bar-animated progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div>');
                    var min = Math.ceil(25);
                    var max = Math.floor(95);
                    var n = Math.floor(Math.random() * (max - min + 1)) + min;
                    $('.progress-bar').animate({width: n+"%"}, 100);
                },
                success: function(response) {
                    console.log(response);
                    $('.progress-bar').animate({width: "100%"}, 100);
                    setTimeout(function(){
                        setTimeout(function(){
                            if(response.status == 'success'){
                                $('#progressBar').html('');
                                toastr.success(response.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                                var win = window.open(response['data']['url'], "report");
                                win.location.reload();
                                //  window.location.href = response['data']['redirect'];
                            }else{
                                toastr.error(response.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                                $('#progressBar').html('');
                            }
                        }, 100);
                    }, 500);
                }
            });
        });

        $(document).on('contextmenu' , '.erp_form__grid_header .erp_form__grid_th_title' , function(e){
            e.preventDefault();
            var thix = $(this);
            $contextCopyDataMenu.css({display: "block",left: e.pageX,top: e.pageY});
            return false;
        });

    });
    /* click escape close modal */
    $(document).keydown(function(event) {
        if (event.keyCode == 27) {
            $(document).find('.erp_form__grid #pd_barcode').val('');
            $(document).find('#inLineHelp').remove();
            $('.modal').find('.modal-content').empty();
            $('.modal').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
            $('.modal').modal('hide');
        }
    });
   $(document).on('click','.kt-header__topbar-item--user,.kt-header__topbar-item--user>div,.kt-header__topbar-item--user>div>div, .kt-header__topbar-item--user>div>div>span,.kt-header__topbar-item--user>div>div>img ',function(){
        var thix = $(this);
        thix.parents('#kt_header').addClass('zindex100');
    });
    $(document).on('click',function(e){
        $('#kt_header').removeClass('zindex100');
        if($(e.target).parent('a').parent('li.header_change_zindex.kt-menu__item--hover').length == 1){
            $('#kt_header').addClass('zindex100');
        }
    })
    $('body').removeClass('pointerEventsNone');

    $(document).on('click','#log_print',function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var formData = {
            title : $('#document_title').val(),
            document_id : $('#document_id').val(),
            document_name : $('#document_name').val(),
            prefix_url : $('#prefix_url').val()
        }
        var data_url = '/log-print-modal';
        $('#kt_modal_lg').modal('show').find('.modal-content').load(data_url,formData);
    })

    // Copy Grid Data
    $(document).on('click' , '.copy_grid__data a' , function(e){
        var formType =  $('#form_type').val();
        if(formType == '' || typeof formType == 'undefined'){
            toastr.error("This Page Does'nt Support Copy & Paste Feature");
        }else{
            localStorage.removeItem('grid_data__' + formType);
            var gridData =  $('tbody.erp_form__grid_body').html();

            localStorage.setItem('grid_data__' + formType, gridData);
            toastr.success('Data Copied!');
        }
    });
    // Paste Grid Data
    $(document).on('click' , '.paste_grid__data a' , function(e){
        var formType =  $('#form_type').val();
        if(formType == '' || typeof(formType) == 'undefined'){
            toastr.error("This Page Does'nt Support Copy & Paste Feature");
        }else{
            if (localStorage.getItem('grid_data__' + formType) === null) {
                toastr.error("Nothing To Paste!");
            }else{
                var gridData = localStorage.getItem('grid_data__' + formType);
                $('tbody.erp_form__grid_body').html(gridData);

                // Calculate Accounts Grid
                if(typeof TotalAmount === 'function'){
                    TotalAmount();
                }
                // Dont Touch This (Lmao) Its Working
                if(typeof gridCalcByRow === 'function'){
                    $('.erp_form__grid_body tr').each(el => {
                    var tr = $('.erp_form__grid_body tr')[el];
                        gridCalcByRow(tr);
                    });
                }
                // =============== //
                updateKeys();
                toastr.success('Data Pasted!');
            }
        }
    });

    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
    // Please note that a cookie value is encoded,
    // so getCookie uses a built-in decodeURIComponent function to decode it.
    function setCookie(name, value, options) {
        if (!options) {
            options = {};
        }

        options = Object.assign({}, {path: '/'}, options);

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString();
        }

        var updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

        for (var optionKey in options) {
            if (!options.hasOwnProperty(optionKey)) {
                continue;
            }
            updatedCookie += "; " + optionKey;
            var optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += "=" + optionValue;
            }
        }

        document.cookie = updatedCookie;
    }
    // To delete a cookie, we can call it with a negative expiration date:
    function deleteCookie(name) {
        setCookie(name, "", {
            'max-age': -1
        })
    }

    function toFixedNoRounding(num , n) {
        if(num == null || num == "" || num == undefined || isNaN(num)) { num = 0; }
        const reg = new RegExp("^-?\\d+(?:\\.\\d{0," + n + "})?", "g")
        const a = num.toString().match(reg)[0];
        const dot = a.indexOf(".");
        if (dot === -1) { // integer, insert decimal dot and pad up zeros
            return a + "." + "0".repeat(n);
        }
        const b = n - (a.length - dot) + 1;
        return b > 0 ? (a + "0".repeat(b)) : a;
    }

    function js__number_format(num) {
        var m = Number((Math.abs(num) * 1000).toPrecision(15)); // 1000 Means 3 Decimals
        m =  Math.round(m) / 1000 * Math.sign(num); // 1000 Means 3 Decimals
        return toFixedNoRounding(m , 3);
    }

    $('#modal_customer_city').on('change',function(e){
            var city_id = $(this).val();
            if(city_id != "0"){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : '{{ route("getAreaByCityId") }}',
                    method : 'POST',
                    data : {"city_id" : city_id},
                    async : false,
                    beforeSend : function(){
                        $('body').addClass('pointerEventsNone');
                    },
                    success : function(response,status){
                        $('body').removeClass('pointerEventsNone');
                        $('#modal_customer_area').html('');
                        if(response.status == 'success'){
                            var areas = response.data;
                            var option = '';
                            option += '<option value="0">Select</option>';
                            areas.forEach((el) => {
                                option += '<option value="'+ el.area_id +'">'+el.area_name+'</option>';
                            });
                            $('#modal_customer_area').append(option);
                        }else{
                            toastr.error('No Areas In This City');
                        }
                    },
                    error: function(response,status) {
                        $('body').removeClass('pointerEventsNone');
                        toastr.error(response.responseJSON.message);
                    },
                });
            }
        });
        $('#modal_add_customer').on('click' , function(e){
            e.preventDefault();
            var required_fields = ['modal_customer_name','modal_customer_arabic_name','modal_customer_phone','modal_customer_city','modal_customer_area'];
            var valid = true;
            required_fields.forEach((el) => {
                if($('#'+el).val() == "" || $('#'+el).val() == "0" || $('#'+el).val() == undefined){
                    valid = false;
                }
            });
            if(valid){
                var formData = $('#modal_create_customer').serializeArray();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url         : '{{ route("storeCustomer") }}',
                    type        : 'POST',
                    dataType	: 'json',
                    data        : formData,
                    beforeSend: function( xhr ) {
                        $('body').addClass('pointerEventsNone');
                    },
                    success: function(response) {
                        $('body').removeClass('pointerEventsNone');
                        if(response.status == 'success'){
                            // Get the Data Arrays
                            var customer = response.data.customer;
                            var cities = response.data.city;
                            var areas = response.data.area;

                            var customer_city_id = customer.city_id;
                            var customer_area_id = customer.region_id;
                            // Setting The Values
                            $('#customer_name').val(customer.customer_name);
                            $('#customer_id').val(customer.customer_id);
                            $('#sales_mobile_no').val(customer.customer_phone_1);
                            $('#customer_address').val(customer.customer_address);

                            $('#sales_order_city_id').val(customer_city_id);
                            $('#sales_order_city_id').trigger('change');

                            $('#sales_order_area').val(customer_area_id);

                            $('#customer-code-no').html('').html(customer.customer_code);

                            $('#addCustomerModal').modal('hide');

                            $('#modal_create_customer').find("input[type=text], select").val("");
                            $('#modal_customer_city').select2("val" , "0");
                            $('#modal_customer_area').select2("val" , "0");
                        }
                    },
                    error: function(response,status) {
                        $('body').removeClass('pointerEventsNone');
                        toastr.error(response.responseJSON.message);
                    }
                });
            }else{
                toastr.error("Please Enter Required Fields.");
            }
        });
        
        $(document).on('click','#upload_documents',function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var title = $('.erp-page--title').text();
            var formData = {
                form_id : $('#form_id').val(),
                form_type : $('#form_type').val(),
                menu_id : $('#menu_id').val(),
                form_code : title.trim(),
            }
            var data_url = '/upload-document';
            $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,formData);
        });
</script>
