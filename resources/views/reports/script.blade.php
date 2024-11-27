<script>
    function funcProductModalHelp(e){
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

        var url = '{{action('Purchase\ProductSmartController@openModalProductFilter')}}';
        console.log(formData);
        $('#kt_modal_xl').modal('show').find('.modal-content').load(url,formData);
    }
    var requestFunGetProductCustomFilter = true;
    

    $(document).on('keyup','#modal_filter_global_search',function(e){
        e.preventDefault();
        var thix = $(this);
        var arr = funGetFilterValues();
        var validate = true;
        arr.pressKeyup = true;
        var notAllowKeyCode = [113, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46];
        if(!notAllowKeyCode.includes(e.keyCode)){
            funGetProductCustomFilter(arr,validate,requestFunGetProductCustomFilter);
        }
    })
    $(document).on('click','#select_product_group_tree',function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var formData = {

        }
        var url = '{{action('Purchase\ProductSmartController@openModalProductGroup')}}';

        $('#kt_modal_tree').modal('show').find('.modal-content').load(url,formData);
    })
    $(document).on('click','#unselect_product_group',function(){
        $('#select_product_group_name').html("---");
        $('#product_group_id').val("");
        var thix = $(this);
        var arr = funGetFilterValues();
        var validate = true;

        funGetProductCustomFilter(arr,validate,requestFunGetProductCustomFilter);
    })
    $(document).on('click','.clear_all_filter',function(e){
        e.preventDefault();
        $(document).find('.prod_head #modal_filter_global_search').val("");

        var arr = funGetFilterValues();
        arr.ajax_req =  true;
        var validate = true;
        requestFunGetProductCustomFilter = true;
        funGetProductCustomFilter(arr,validate,requestFunGetProductCustomFilter);
    })
    var xhr = false;
    function funGetProductCustomFilter(arr,validate,requestFunGetProductCustomFilter){
        console.log(arr);
        if(arr.hasOwnProperty('pressKeyup') && xhr !== false){
            if(arr.pressKeyup){
                xhr.abort();
            }
        }
        if(validate && requestFunGetProductCustomFilter){
            var formData = {};
            if(arr['global_search'] != undefined){
                formData.global_search = arr['global_search'];
            }
            if(arr['ajax_req'] != undefined){
                formData.ajax_req = arr['ajax_req'];
            }

            requestFunGetProductCustomFilter = false;
            var spinner = '<div class="kt-spinner kt-spinner--sm kt-spinner--success kt-spinner-center" style="width: 24.5px;height: 17px;"></div>';
            $('table.table_pitModal').find('tbody.erp_form__grid_body').html(spinner);
            formData.form_type = form_modal_type;
            console.log(arr);
            console.log(formData);
            var url = '{{action('Purchase\ProductSmartController@openModalProductFilter')}}';
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
                        $('table.table_pitModal').find('tbody.erp_form__grid_body').html("");
                        var product = response.data.product;
                        var len = product.length;
                        var arr = {
                            product : product,
                            len : len,
                        }
                        funSetProductCustomFilter(arr)
                    }else{
                        toastr.error(response.message);
                    }
                    requestFunGetProductCustomFilter = true;

                },
                error: function(response,status) {
                    requestFunGetProductCustomFilter = true;

                }
            });
        }
    }
    function funGetFilterValues(){
        var global_search = $(document).find('.prod_head #modal_filter_global_search').val();
        return {
            global_search: global_search,
        };
    }
    $(document).on('click','.addCheckedProduct',function(){
        var thix = $(this);
        if(thix.is(':checked')){
            var tr = thix.parents('tr');
            funcAddSelectedProductToFormGrid(tr);
        }
    });
    $(document).on('click','.table_pitModal>tbody.erp_form__grid_body>tr>td',function(){
        var thix = $(this);
        if(thix.find('.addCheckedProduct').length == 0){
            var tr = thix.parents('tr');
            funcAddSelectedProductToFormGrid(tr);
        }
    });

    $(document).on('keyup', function(e){
        if($('.table_pitModal').length == 1
            && $('.select2-container--open').length == 0
        ){
            var tr = $('.table_pitModal>tbody>tr.selected_tr');
            var scroll = false;
            if(e.keyCode == 38){ // keyup
                if($('.table_pitModal>tbody>tr.selected_tr').length == 0
                    || $('.table_pitModal>tbody>tr:first-child').hasClass('selected_tr')){
                    $('.table_pitModal>tbody>tr:first-child').removeClass('selected_tr');
                    $('.table_pitModal>tbody>tr:last-child').addClass('selected_tr');
                }else{
                    var tr_index = $('.table_pitModal>tbody>tr.selected_tr').index();
                    $('.table_pitModal>tbody>tr:eq('+tr_index+')').removeClass('selected_tr');
                    tr_index = tr_index - 1;
                    $('.table_pitModal>tbody>tr:eq('+tr_index+')').addClass('selected_tr');
                }
                scroll = true;
                $('.table_pitModal>tbody>tr:eq('+tr_index+')').find('.addCheckedProduct').focus();
            }
            if(e.keyCode == 40){ //keydown
                if($('.table_pitModal>tbody>tr.selected_tr').length == 0
                    || $('.table_pitModal>tbody>tr:last-child').hasClass('selected_tr')){
                    $('.table_pitModal>tbody>tr:last-child').removeClass('selected_tr');
                    $('.table_pitModal>tbody>tr:first-child').addClass('selected_tr');
                }else{
                    var tr_index = $('.table_pitModal>tbody>tr.selected_tr').index();
                    $('.table_pitModal>tbody>tr:eq('+tr_index+')').removeClass('selected_tr');
                    tr_index = tr_index + 1;
                    $('.table_pitModal>tbody>tr:eq('+tr_index+')').addClass('selected_tr');
                }
                scroll = true;
                $('.table_pitModal>tbody>tr:eq('+tr_index+')').find('.addCheckedProduct').focus();
            }
            if(scroll){
                var tr_index = $('.table_pitModal>tbody>tr.selected_tr').index();
                var total_tr_height = 0;
                for(var i=0;i<tr_index;i++){
                    var tr_height = $('.table_pitModal>tbody>tr:eq('+i+')').height();
                    total_tr_height += parseFloat(tr_height);
                }
                $(document).find('#product_filters').animate({scrollTop: total_tr_height - 100}, 1);
            }
            console.log($("#modal_filter_global_search").is(":focus"));
            if(tr && e.keyCode == 13 && !$("#modal_filter_global_search").is(":focus")) { //Enter
                tr.find('.addCheckedProduct').prop('checked',true)
                funcAddSelectedProductToFormGrid(tr);
            }
        }
    })

    /* close popup func */
    $(document).on('click','.prod_help__close',function(){
        closeModal();
    })
    $('#kt_modal_xl').on('hidden.bs.modal', function () {
       // funcGridThResize([]);
    });

</script>
