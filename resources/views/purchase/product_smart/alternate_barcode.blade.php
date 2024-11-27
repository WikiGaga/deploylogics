@extends('layouts.template')
@section('title', 'Alternate Barcode')

@section('pageCSS')
    <style>
        .gridDelBtn {
            padding: 0 0 0 5px !important;
        }
        .erp_form__grid_body>tr>td{
            padding: 5px !important;
        }
    </style>
@endsection

@section('content')

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <!--begin::Form-->
            <div class="kt-portlet__body">
                <div class="form-group-block row">
                    <div class="col-lg-3">
                        <label class="erp-col-form-label">Barcode:</label>
                        <div class="erp_form___block">
                            <div class="input-group open-modal-group">
                                <div class="input-group-prepend">
                                        <span class="input-group-text btn-minus-selected-data">
                                            <i class="la la-minus-circle"></i>
                                        </span>
                                </div>
                                <input type="text" id="tp_barcode" name="tp_barcode" data-url="{{action('Common\DataTableController@inlineHelpOpen','productTPHelp')}}" class="open_inline__help pd_barcode moveIndex form-control erp-form-control-sm" placeholder="Enter Here">
                                <!--<input type="text" id="pd_barcode" class="pd_barcode form-control open_inline__help erp-form-control-sm medium_text" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">-->
                                <input type="hidden" id="product_id">
                                <input type="hidden" id="barcode_id">
                                <div class="input-group-append">
                                    <span class="input-group-text btn-open-mob-help" id="getAlternateBarcodeProduct">
                                       GO
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="erp-col-form-label">Product Name:</label>
                        <input type="text" id="product_name" value="" class="form-control erp-form-control-sm readonly" readonly>
                        {{--<div><span style="color:#3e99ec;">UOM:</span> <span id="uom">PCS</span></div>
                        <div><span style="color:#3e99ec;">Packing:</span> <span id="packing">1</span></div>--}}
                    </div>

                    <div class="col-lg-2">
                        <label class="erp-col-form-label">UOM:</label>
                        <input type="text" id="uom" value="" class="form-control erp-form-control-sm readonly" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Sale Rate:</label>
                        <input type="text" id="sale_rate" value="" class="form-control erp-form-control-sm readonly" readonly>
                    </div>

                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Cost Rate:</label>
                        <input type="text" id="cost_rate" value="" class="form-control erp-form-control-sm readonly" readonly>
                    </div>

                </div>

                <div class="form-group-block row">
                    <div class="col-lg-3">
                        <label class="erp-col-form-label">Alternate Barcode:</label>
                        <input type="text" id="alternate_barcode" value="" class="form-control erp-form-control-sm medium_text" autocomplete="off">
                    </div>
                    <div class="col-lg-3">
                        <label class="erp-col-form-label">Packing:</label>
                        <input type="text" id="packing" value="" class="form-control erp-form-control-sm">
                    </div>
                    <div class="col-lg-2">
                        <button id="alternate_barcode_save" class="btn btn-sm btn-primary" style="position: absolute;bottom: 0;">Save</button>
                    </div>
                </div>
                <div class="row mt-4" >
                    <div class="col-lg-12">
                        <h5>Alternate Barcode Detail</h5>
                        <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                            <thead class="erp_form__grid_header">
                                <tr>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sr.</div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Barcode</div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Product Name</div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Packing</div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">UOM</div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sale Rate</div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Cost Rate</div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="erp_form__grid_body">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end::Form-->
        </div>
    </div>


@endsection
@section('pageJS')


@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    
    <script>
        $(document).on('click','.btn-minus-selected-data',function(e){
            $('input').val("");
            $('.erp_form__grid_body').html("");
        });
        $(document).on('click','#getAlternateBarcodeProduct',function(){
            var barcode = $("#tp_barcode").val();
            getProductDetail(barcode);
        });
        
        $(document).on('keyup','#tp_barcode',function(e){
            e.preventDefault();
            var barcode = $(this).val();
            if(e.keyCode == 13){
                $('#inLineHelp').remove();
                getProductDetail(barcode);
               // emptyProductInputFields()
            }
            if(valueEmpty(barcode)){
               // emptyProductInputFields();
            }
        });
        function emptyProductInputFields(){
            $('#product_name').val('');
            $('#uom').val('');
            $('#sale_rate').val('');
            $('#cost_rate').val('');
            $('#product_id').val('');
            $('#barcode_id').val('');
            $('#alternate_barcode').val('');
            $('#packing').val('');
        }
        var requestSend = true;
        function getProductDetail(barcode){
            var validate = true;
            if(barcode == undefined || barcode == "" || !barcode){
                validate = false;
            }
            if(!requestSend){
                toastr.error('Barcode fetching data already.');
            }
            if(validate && requestSend){
                requestSend = false;
                var spinner = '<div class="kt-spinner kt-spinner--sm kt-spinner--success kt-spinner-center" style="width: 18px;"></div>';
                $('#getAlternateBarcodeProduct').html(spinner);
                var formData = {
                    barcode : barcode
                };
                var url = '{{action('Purchase\ProductSmartController@getAlternateBarcodeProduct')}}';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    success: function(response,data) {
                        console.log(response);
                        if(response.status == 'success'){
                            // $('#tp_barcode').val('');
                            var current = response.data.current;
                            $('#tp_barcode').val(current.product_barcode_barcode);
                            $('#product_name').val(current.product_name);
                            $('#uom').val(current.uom_name);
                            $('#sale_rate').val(current.sale_rate);
                            $('#cost_rate').val(current.product_barcode_cost_rate);
                            $('#product_id').val(current.product_id);
                            $('#barcode_id').val(current.product_barcode_id);
                            $('#alternate_barcode').val(current.product_barcode_barcode);
                            $('#packing').val(current.product_barcode_packing);

                            var tbody = $('tbody.erp_form__grid_body');
                            if(tbody.length > 0){
                                tbody.html('');
                            };
                            var list =response.data.list;
                            console.log(list);
                            
                            for (let i = 0; i < list.length; i++) {
                                var tr_length = tbody.find('tr').length;
                                var sr_new = tr_length + 1;
                                var newTr = "<tr>";
                                newTr += "<td class='text-center'>"+sr_new+"</td>";
                                newTr += "<td>"+list[i].product_barcode_barcode+"</td>";
                                newTr += "<td>"+list[i].product_name+"</td>";
                                newTr += "<td>"+list[i].product_barcode_packing+"</td>";
                                newTr += "<td>"+list[i].uom_name+"</td>";
                                newTr += "<td>"+list[i].sale_rate+"</td>";
                                newTr += "<td>"+list[i].product_barcode_cost_rate+"</td>";
                                newTr += '<td class="text-center"><button type="button" data-id="'+list[i].product_barcode_id+'" class="btn btn-danger btn-sm gridDelBtn delRow"><i class="la la-trash"></i></button></td>';
                                newTr += "</tr>";
                                tbody.append(newTr);
                            }
                            $('#alternate_barcode').val("");
                            $('#packing').val("");
                        }else{
                            toastr.error(response.message);
                        }
                        $('#getAlternateBarcodeProduct').html('GO');
                        requestSend = true;
                    },
                    error: function(response,status) {
                        $('#getAlternateBarcodeProduct').html('GO');
                        requestSend = true;
                    }
                });
            }
        }
    </script>

    <script>
        requestSendNewBarcode = true;
        $(document).on('click','#alternate_barcode_save',function(){
            var validate = true;
            var tbody = $('tbody.erp_form__grid_body');
            var tr_length = tbody.find('tr').length;
            var sr_new = tr_length + 1;
            var alternate_barcode = $('#alternate_barcode').val();
            var product_name = $('#product_name').val();
            var product_id = $('#product_id').val();
            var barcode_id = $('#barcode_id').val();
            var uom = $('#uom').val();
            var packing = $('#packing').val();
            var sale_rate = $('#sale_rate').val();
            var cost_rate = $('#cost_rate').val();

            if(valueEmpty(alternate_barcode)){
                toastr.error('Barcode is required.');
                validate = false;
                return false;
            }
            if(valueEmpty(packing)){
                toastr.error('Packing is required.');
                validate = false;
                return false;
            }
            if(valueEmpty(product_id) || valueEmpty(barcode_id)){
                toastr.error('Base Product is required.');
                validate = false;
                return false;
            }
            if(validate && requestSendNewBarcode){
                requestSendNewBarcode = false;
                var spinner = '<div class="kt-spinner kt-spinner--sm kt-spinner--success kt-spinner-center" style="width: 24.5px;height: 17px;"></div>';
                $("#alternate_barcode_save").html(spinner);
                var formData = {
                    product_id : product_id,
                    barcode_id : barcode_id,
                    barcode : alternate_barcode,
                    packing : packing,
                };
                var url = '{{action('Purchase\ProductSmartController@storeAlternateBarcode')}}';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    success: function(response,data) {
                        console.log(response);
                        if(response.status == 'success'){
                            var data = response.data;
                            var newTr = "<tr>";
                            newTr += "<td class='text-center'>"+sr_new+"</td>";
                            newTr += "<td>"+alternate_barcode+"</td>";
                            newTr += "<td>"+product_name+"</td>";
                            newTr += "<td>"+packing+"</td>";
                            newTr += "<td>"+uom+"</td>";
                            newTr += "<td>"+sale_rate+"</td>";
                            newTr += "<td>"+cost_rate+"</td>";
                            newTr += '<td class="text-center"><button type="button" data-id="'+data.product_barcode_id+'" class="btn btn-danger btn-sm gridDelBtn delRow"><i class="la la-trash"></i></button></td>';
                            newTr += "</tr>";
                            tbody.append(newTr);
                            $('#alternate_barcode').val("");
                            $('#packing').val("");
                        }else{
                            toastr.error(response.message);
                        }
                        requestSendNewBarcode = true;
                        $("#alternate_barcode_save").html("Save");
                    },
                    error: function(response,status) {
                        requestSendNewBarcode = true;
                        $("#alternate_barcode_save").html("Save");
                    }
                });
            }
        })
        requestRemoveBarcode = true;
        $(document).on('click','.delRow',function(){
            var thix = $(this);
            var val = thix.attr('data-id');
            var validate = true;
            if(valueEmpty(val)){
                toastr.error('Barcode is required.');
                validate = false;
                return false;
            }
            if(validate && requestRemoveBarcode){
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then(function(result) {
                    if (result.value) {
                        requestRemoveBarcode = false;
                        var formData = {
                            barcode : val,
                        };
                        var url = '{{action('Purchase\ProductSmartController@removeAlternateBarcode')}}';
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: url,
                            dataType	: 'json',
                            data        : formData,
                            success: function(response,data) {
                                console.log(response);
                                if(response.status == 'success'){
                                    thix.parents('tr').remove();
                                    var sr_no = 1;
                                    $('tbody.erp_form__grid_body>tr').each(function(){
                                        $(this).find('td:first-child').html(sr_no);
                                        sr_no = sr_no + 1;
                                    });
                                    toastr.success(response.message);
                                }else{
                                    toastr.error(response.message);
                                }
                                requestRemoveBarcode = true;
                            },
                            error: function(response,status) {

                                requestRemoveBarcode = true;
                            }
                        });
                    }
                });
            }
        })
    </script>
@endsection
