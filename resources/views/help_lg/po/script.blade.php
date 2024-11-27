<script>
    var requestFunGetPOFilter = true;
    function funcPOModalHelp(e){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var formData = {}
        formData.form_type = form_modal_type;
        if(!emptyArr.includes($('#supplier_id').val())){
            formData.supplier_id = $('#supplier_id').val()
            formData.open_modal = true
        }

        var url = '{{action('Common\ModalLgHelpController@poHelp')}}';
        console.log(formData);
        $('#kt_modal_xl').modal('show').find('.modal-content').load(url,formData);
    }
    $(document).on('click','.po_reset_all_filter',function(e){
        e.preventDefault();
        $(document).find('.prod_head input[type="text"]').val("");
        $(document).find('.prod_head select').val("").trigger('change');
       // $(document).find('.prod_head input[name="radioDate"]:first').prop('checked',true)

        var arr = funGetPOFilterValues();
        arr.ajax_req =  true;
        var validate = true;

        funGetPOCustomFilter(arr,validate,requestFunGetPOFilter);
    })
    $(document).on('keyup','#po_modal_filter_global_search',function(e){
        e.preventDefault();
        var thix = $(this);
        var arr = funGetPOFilterValues();
        var validate = true;
        arr.pressKeyup = true;
        var notAllowKeyCode = [113, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46];
        if(!notAllowKeyCode.includes(e.keyCode)){
            funGetPOCustomFilter(arr,validate,requestFunGetPOFilter);
        }
    })
    $(document).on('change','#po_modal_filter_supplier_id',function(){
        var thix = $(this);
        var arr = funGetPOFilterValues();
        var validate = true;
        funGetPOCustomFilter(arr,validate,requestFunGetPOFilter);
    });
    $(document).on('focusin','#po_modal_filter_supplier_id',function(e){
        console.log('supplier fin');
        $('.table_lgModal>tbody>tr').removeClass('selected_tr');
    });

    var xhr = false;
    function funGetPOCustomFilter(arr,validate,requestFunGetPOFilter){
        console.log(arr);
        if(arr.hasOwnProperty('pressKeyup') && xhr !== false){
            if(arr.pressKeyup){
                xhr.abort();
            }
        }
        if(validate && requestFunGetPOFilter){
            var formData = {};
            if(arr['supplier_id'] != undefined){
                formData.supplier_id = arr['supplier_id'];
            }
            if(arr['status'] != undefined){
                formData.status = arr['status'];
            }
            if(arr['global_search'] != undefined){
                formData.global_search = arr['global_search'];
            }
            if(arr['ajax_req'] != undefined){
                formData.ajax_req = arr['ajax_req'];
            }

            requestFunGetPOFilter = false;
            var spinner = '<div class="kt-spinner kt-spinner--sm kt-spinner--success kt-spinner-center" style="width: 24.5px;height: 17px;"></div>';
            $('table.table_lgModal').find('tbody.erp_form__grid_body').html(spinner);
            formData.form_type = form_modal_type;
            console.log(arr);
            console.log(formData);
            var url = '{{action('Common\ModalLgHelpController@poHelp')}}';
            xhr = $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType	: 'json',
                data        : formData,
                success: function(response,data) {
                    // console.log(response);
                    if(response.status == 'success'){
                        $('table.table_lgModal').find('tbody.erp_form__grid_body').html("");
                        var list = response.data.list;
                        var len = list.length;
                        var arr = {
                            list : list,
                            len : len,
                        }
                        funSetPOCustomFilter(arr);
                    }else{
                        toastr.error(response.message);
                    }
                    requestFunGetPOFilter = true;

                },
                error: function(response,status) {
                    requestFunGetPOFilter = true;

                }
            });
        }
    }
    function funGetPOFilterValues(){
        var supplier_id = $(document).find('.prod_head #po_modal_filter_supplier_id').find('option:selected').val();
        var status = $(document).find('.prod_head input[name="radioStatus"]:checked').val()
        var global_search = $(document).find('.prod_head #po_modal_filter_global_search').val();
        return {
            supplier_id: supplier_id,
            status: status,
            global_search: global_search,
        };
    }

    $(document).on('click','.table_lgModal>tbody.erp_form__grid_body>tr>td',function(){
        var thix = $(this);
        // add PO Code
        var tr = thix.parents('tr');
        var purchase_order = tr.find('td.code').text();
        var purchase_order_id = tr.attr('data-po_id');
        var supplier_name = tr.find('td.supplier_name').text();
        var supplier_id = tr.attr('data-supplier_id');

        $('#grn_form').find('.erp_form___block').find('#purchase_order').val(purchase_order);
        $('#grn_form').find('.erp_form___block').find('#purchase_order_id').val(purchase_order_id);
        $('#grn_form').find('.erp_form___block').find('#supplier_name').val(supplier_name);
        $('#grn_form').find('.erp_form___block').find('#supplier_id').val(supplier_id);
        closeModal();
    });

    $(document).on('keyup', function(e){
        if($('.table_lgModal').length == 1
            && $('.select2-container--open').length == 0
        ){
            var tr = $('.table_lgModal>tbody>tr.selected_tr');
            var scroll = false;
            if(e.keyCode == 38){ // keyup
                if($('.table_lgModal>tbody>tr.selected_tr').length == 0
                    || $('.table_lgModal>tbody>tr:first-child').hasClass('selected_tr')){
                    $('.table_lgModal>tbody>tr:first-child').removeClass('selected_tr');
                    $('.table_lgModal>tbody>tr:last-child').addClass('selected_tr');
                }else{
                    var tr_index = $('.table_lgModal>tbody>tr.selected_tr').index();
                    $('.table_lgModal>tbody>tr:eq('+tr_index+')').removeClass('selected_tr');
                    tr_index = tr_index - 1;
                    $('.table_lgModal>tbody>tr:eq('+tr_index+')').addClass('selected_tr');
                }
                scroll = true;
                $('.table_lgModal>tbody>tr:eq('+tr_index+')').find('.addCheckedProduct').focus();
            }
            if(e.keyCode == 40){ //keydown
                if($('.table_lgModal>tbody>tr.selected_tr').length == 0
                    || $('.table_lgModal>tbody>tr:last-child').hasClass('selected_tr')){
                    $('.table_lgModal>tbody>tr:last-child').removeClass('selected_tr');
                    $('.table_lgModal>tbody>tr:first-child').addClass('selected_tr');
                }else{
                    var tr_index = $('.table_lgModal>tbody>tr.selected_tr').index();
                    $('.table_lgModal>tbody>tr:eq('+tr_index+')').removeClass('selected_tr');
                    tr_index = tr_index + 1;
                    $('.table_lgModal>tbody>tr:eq('+tr_index+')').addClass('selected_tr');
                }
                scroll = true;
                $('.table_lgModal>tbody>tr:eq('+tr_index+')').find('.addCheckedProduct').focus();
            }
            if(scroll){
                var tr_index = $('.table_lgModal>tbody>tr.selected_tr').index();
                var total_tr_height = 0;
                for(var i=0;i<tr_index;i++){
                    var tr_height = $('.table_lgModal>tbody>tr:eq('+i+')').height();
                    total_tr_height += parseFloat(tr_height);
                }
                $(document).find('#product_filters').animate({scrollTop: total_tr_height - 100}, 1);
            }
           // console.log($("#po_modal_filter_global_search").is(":focus"));
            if(tr && e.keyCode == 13 && !$("#po_modal_filter_global_search").is(":focus")) { //Enter

                // add PO Code
            }
        }
    })

    /* close popup func */
    $(document).on('click','.prod_help__close',function(){
        closeModal();
    })
    $('#kt_modal_xl').on('hidden.bs.modal', function () {
        funcGridThResize([]);
    });

</script>
<script>
    function jsDate(patt,dval){
        patt = patt.replace(" ", "");
        const d = new Date(dval);
        let day = d.getDate();
        day = (day < 10)? '0'+day.toString() :day ;

        let month = parseInt(d.getMonth()) + 1 ;
        month = (month < 10)? '0'+month.toString() :month ;

        let year = d.getFullYear();

        const pattren = patt.split("-");
        var pattrenDate = '';
        var len = pattren.length;
        for(var i = 0; i < len; i++){
            if('d' == pattren[i]){ pattrenDate += day; }
            if('m' == pattren[i]){ pattrenDate += month; }
            if('y' == pattren[i]){ pattrenDate += year; }
            if((len-1) != i){ pattrenDate += '-'; }
        }
        return pattrenDate;
    }
    function funSetPOCustomFilter(arr){
        var len = arr['len'];
        var list = arr['list'];
        for (var i =0;i<len;i++){
            var row = list[i];
            var po_grn_status = "";
            if(!valueEmpty(row['po_grn_status'])){
                po_grn_status = row['po_grn_status'].toLowerCase();
            }

            var purchase_order_total_amount = !valueEmpty(row['purchase_order_total_amount'])?parseFloat(row['purchase_order_total_amount']).toFixed(3):"";

            var newTr = '<tr data-po_id="'+row['purchase_order_id']+'" data-supplier_id="'+row['supplier_id']+'">' +
                '<td class="code">'+(!valueEmpty(row['purchase_order_code'])?row['purchase_order_code']:"")+'</td>' +
                '<td>'+jsDate('d-m-y',row['created_at'])+'</td>' +
                '<td class="supplier_name">'+(!valueEmpty(row['supplier_name'])?row['supplier_name']:"")+'</td>' +
                '<td ="total_amount text-right">'+purchase_order_total_amount+'</td>' +
                '<td>'+po_grn_status+'</td>' +
                '</tr>';

            $('table.table_lgModal').find('tbody.erp_form__grid_body').append(newTr);
        }
    }
</script>
