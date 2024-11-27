@extends('layouts.layout')
@section('title', 'Stock Audit Adjustment')

@section('pageCSS')
@endsection

@section('content')
    <!--begin::Form-->
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code = $data['stock_code'];
                $date =  date('d-m-Y');
                $id = '';
                if($data['stock_code_type'] == 'sa'){
                    $rate_type  = 'item_cost_rate';
                }else{
                    $rate_type  = 'item_sale_rate';
                }
                
            }
            if($case == 'edit'){
                $id = $data['current']->stock_id;
                $code = $data['current']->stock_code;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->stock_date))));
                $storeid = $data['current']->stock_store_from_id;
                $remarks = $data['current']->stock_remarks;
                $rate_type = $data['current']->stock_rate_type;
                $rate_perc = $data['current']->stock_rate_perc;
                $stock_location_id = $data['current']->stock_location_id;
                $dtls = isset($data['current_audit'])? $data['current_audit'] :[];
            }
            
            $type =$data['form_type'];
            $form_type = $data['stock_code_type'];

    @endphp
    <form id="stock_adjustment_form" class="stock_form kt-form" method="post" action="{{ action('Inventory\StockAuditController@store', $id) }}">
    @csrf
    <input type="hidden" name="stock_code_type" value='{{$data['stock_code_type']}}' id="form_type">
    <input type="hidden" name="stock_menu_id" value='{{$data['stock_menu_id']}}'>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    {{isset($code)?$code:""}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="start_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
                                    <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label text-center">Store:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2" id="store_id" name="store">
                                        <option value="0">Select</option>
                                        @php $storeid = isset($storeid)?$storeid:'' @endphp
                                        @foreach($data['store'] as $store)
                                            @if($case == 'new' && $store->store_default_value == 1)
                                                @php $storeid = $store->store_id @endphp
                                            @endif
                                            <option value="{{$store->store_id}}" {{$store->store_id == $storeid?'selected':''}}>{{$store->store_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label text-center">Stock Location:</label>
                            <div class="col-lg-6">
                                <div class="erp-select2 display_stock_location">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2 stock_location_id" name="stock_location_id">
                                        <option value="0">Select</option>
                                        @if($case == 'edit')
                                            @foreach($data['display_location'] as $display_location)
                                                <option value="{{$display_location->display_location_id}}" {{$display_location->display_location_id == $stock_location_id?'selected':''}}>{{$display_location->display_location_name_string}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display: none;" class="row form-group-block">
                    <div class="col-lg-8">
                        <div class="row">
                            <label class="col-lg-3 erp-col-form-label">Rate Type <span class="required">*</span></label>
                            <div class="col-lg-9">
                                <div class="ChangeRateBlock input-group erp-select2-sm">
                                    @php $rate_type = isset($rate_type)?$rate_type:''; @endphp
                                    <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rate_type" name="rate_type">
                                        <option value="0">Select</option>
                                        @foreach($data['rate_types'] as $key => $value)
                                            <option value="{{$key}}" {{$rate_type == $key ? "selected" :""}}>{{$value}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="la la-plus"></i>
                                            </span>
                                    </div>
                                    <input type="text" id="rate_perc" name="rate_perc" value="{{isset($rate_perc)?$rate_perc:''}}" class="moveIndex form-control erp-form-control-sm validNumber">
                                    <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-percent"></i>
                                            </span>
                                        <span class="input-group-text group-input-btn" id="changeGridItemRate" title="Change Rate Apply">
                                                <i class="la la-refresh"></i>
                                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-3">
                                <label class="col-lg-12 erp-col-form-label"><strong>Stock Summary</strong></label>
                            </div>
                            <div class="col-lg-3" style="background-color:#d85555;font-size:14;font-weight:bold;color: aliceblue;">
                                <label class="col-lg-8 erp-col-form-label">Shortage</label>
                                &nbsp;&nbsp;&nbsp; <span id="Shortage"></span>
                            </div>
                            <div class="col-lg-3" style="background-color:#0ea73c;font-size:14;font-weight:bold;color: aliceblue;">
                                <label class="col-lg-8 erp-col-form-label">Excess Stock</label>
                                &nbsp;&nbsp;&nbsp; <span id="Excess"></span>
                            </div>
                            <div class="col-lg-3" style="background-color:#2d88c5;font-size:14;font-weight:bold;color: aliceblue;">
                                <label class="col-lg-8 erp-col-form-label">Balance Stock</label>
                                &nbsp;&nbsp;&nbsp; <span id="Balance"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-right">
                        <div class="data_entry_header">
                            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                    <i class="flaticon-more" style="color: #666666;"></i>
                                </button>
                                @php
                                    $headings = ['Check','Sr No','Barcode','Product Name','Unit Name','Package', 'Category', 'Top Level Category',
                                                'Cost Rate','Physical Stock Qty', 'Stock Qty','djustment Qty'];
                                @endphp
                                <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                    @foreach($headings as $key=>$heading)
                                        <li >
                                            <label>
                                                <input value="{{$key}}" type="checkbox" checked> {{$heading}}
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @include('layouts.pageSettingBtn')
                        </div>
                    </div>
                </div>
                <div class="tab-pane selected_product_ds_content" id="selected_product_ds" role="tabpanel">
                    <div class="form-group-block row">
                        <div class="erp_data__grid_fixed_header">
                            <table class="table table_pit_list erp_form__grid erp_data__grid_fixed_header data_grid__last_th_sticky erp_data__inline_filter">
                                <thead>
                                    <tr>
                                        <th scope="col" width="4%">
                                            <div class="erp_form__grid_th_title">Check</div>
                                        </th>
                                        <th scope="col" width="4%">
                                            <div class="erp_form__grid_th_title">Sr No</div>
                                        </th>
                                        <th scope="col" width="10%">
                                            <div class="erp_form__grid_th_title">
                                                Barcode
                                            </div>
                                            <input type="text" class="filter_barcode" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="25%">
                                            <div class="erp_form__grid_th_title">
                                                Product Name
                                            </div>
                                            <input type="text" class="filter_itemname" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="4%">
                                            <div class="erp_form__grid_th_title">
                                                Unit Name
                                            </div>
                                            <input type="text" class="filter_unit_name" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="4%">
                                            <div class="erp_form__grid_th_title">
                                                Package
                                            </div>
                                            <input type="text" class="filter_package" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="8%">
                                            <div class="erp_form__grid_th_title">
                                                Category
                                            </div>
                                            <input type="text" class="filter_category" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="8%">
                                            <div class="erp_form__grid_th_title">
                                                Top Level Category
                                            </div>
                                            <input type="text" class="filter_top_category" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="7%">
                                            <div class="erp_form__grid_th_title">
                                                Physical Stock Qty
                                            </div>
                                            <input type="text" class="filter_curr_stock" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="7%">
                                            <div class="erp_form__grid_th_title">
                                                Stock Qty
                                            </div>
                                            <input type="text" class="filter_curr_stock" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="7%">
                                            <div class="erp_form__grid_th_title">
                                                Adjustment Qty
                                            </div>
                                            <input type="text" class="filter_reorder_qty" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="5%">
                                            <div class="erp_form__grid_th_title">
                                                Cost Rate
                                            </div>
                                            <input type="text" class="filter_cost_rate" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="5%">
                                            <div class="erp_form__grid_th_title">
                                                Cost Amount
                                            </div>
                                            <input type="text" class="filter_cost_amount" style="width: 100%;">
                                        </th>
                                        <th scope="col" width="7%">
                                            <div class="erp_form__grid_th_title">
                                                Action
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <button type="button" class="btn btn-sm btn-danger" id="header_input_clear_data" style="padding: 3px;"><i class="la la-times"></i></button>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="erp_form__grid_body">
                                    @if(isset($dtls))
                                        @php
                                            $k=0;
                                            $j=0;
                                            $x=0;
                                        @endphp
                                        @foreach($dtls as $dtl)
                                            @php
                                                if($dtl->stock_dtl_quantity < 0){
                                                    $k++;
                                                }
                                                if($dtl->stock_dtl_quantity > 0){
                                                    $j++;
                                                }
                                                if($dtl->stock_dtl_quantity == 0){
                                                    $x++;
                                                }
                                            @endphp
                                            <tr>
                                                <td align="center">
                                                    <input type="checkbox" value="pd[{{$loop->iteration}}]['product_barcode_barcode']" id="pd[{{$loop->iteration}}][chk_barcodes]" class="checkedInv" name="pd[{{$loop->iteration}}][chk_barcodes]">
                                                    <input type='hidden' name="pd[{{$loop->iteration}}][pd_barcode]" data-id='pd_barcode' value='{{isset($dtl->product_barcode_barcode)?$dtl->product_barcode_barcode:""}}' title='{{isset($dtl->product_barcode_barcode)?$dtl->product_barcode_barcode:""}}' class='pd_barcode form-control erp-form-control-sm' readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom_id)?$dtl->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{ $loop->iteration }}][product_id]"   data-id="product_id"  value="{{ isset($dtl->product_id) ? $dtl->product_id : '' }}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                </td>
                                                <td align="center">
                                                    <input type='text' name="pd[{{$loop->iteration}}][sr_no]" data-id='sr_no' value='{{$loop->iteration}}' title='{{$loop->iteration}}' class='sr_no form-control erp-form-control-sm' readonly>
                                                </td>
                                                <td align="center">
                                                    {{isset($dtl->product_barcode_barcode)?$dtl->product_barcode_barcode:""}}
                                                </td>
                                                <td align="left">
                                                    {{isset($dtl->product_name)?$dtl->product_name:""}}
                                                    <input type='hidden' name="pd[{{$loop->iteration}}][product_name]" data-id='product_name' value='{{isset($dtl->product_name)?$dtl->product_name:""}}' title='{{isset($dtl->product_name)?$dtl->product_name:""}}' class='pd_product_name form-control erp-form-control-sm' readonly>
                                                </td>
                                                <td align="center">
                                                    <input type='text' data-id='product_name' value='{{isset($dtl->uom_id)?$dtl->uom_id:""}}' title='{{isset($dtl->uom_id)?$dtl->uom_id:""}}' class='pd_product_name form-control erp-form-control-sm' readonly>
                                                </td>
                                                <td align="center">
                                                    <input type="text" data-id="product_barcode_packing" name="pd[{{$loop->iteration}}][product_barcode_packing]" value="{{isset($dtl->product_barcode_packing)?$dtl->product_barcode_packing:""}}" class="product_barcode_packing form-control erp-form-control-sm" readonly>
                                                </td>
                                                <td align="left">
                                                    <input type="text" data-id="group_item_name" name="pd[{{$loop->iteration}}][group_item_name]" value="{{isset($dtl->group_item_name)?$dtl->group_item_name:""}}" class="group_item_name form-control erp-form-control-sm" readonly>
                                                </td>
                                                <td align="left">
                                                    <input type="text" data-id="group_item_parent_name" name="pd[{{$loop->iteration}}][group_item_parent_name]" value="{{isset($dtl->group_item_parent_name)?$dtl->group_item_parent_name:""}}" class="group_item_parent_name form-control erp-form-control-sm" readonly>
                                                </td>
                                                <td align="center">
                                                    <input type="text" data-id="physical_quantity" name="pd[{{$loop->iteration}}][physical_quantity]" value="{{$dtl->stock_dtl_physical_quantity}}" class="tblGridPhysicalQty tb_moveIndex form-control erp-form-control-sm validNumber">
                                                </td>
                                                <td>
                                                    <input type="text" data-id="stock_quantity" name="pd[{{$loop->iteration}}][stock_quantity]" value="{{$dtl->stock_dtl_stock_quantity}}" class="  tblGrid_stockQty form-control erp-form-control-sm validNumber" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->stock_dtl_quantity}}" class="tblGridAdjustment validNumber validOnlyNumber  form-control erp-form-control-sm" readonly>
                                                </td>
                                                <td align="center">
                                                    <input type="text" data-id="cost_rate" name="pd[{{$loop->iteration}}][cost_rate]" value="{{isset($dtl->cost_rate)?$dtl->cost_rate:""}}" class="cost_rate tb_moveIndex form-control erp-form-control-sm">
                                                </td>
                                                <td align="center">
                                                    <input type="text" data-id="cost_amount" name="pd[{{$loop->iteration}}][cost_amount]" value="{{isset($dtl->cost_amount)?$dtl->cost_amount:""}}" class="cost_amount form-control erp-form-control-sm" readonly>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                        <input type="hidden" data-id="short" id="short" value="{{$k}}" readonly>
                                        <input type="hidden" data-id="excess" id="excess" value="{{$j}}" readonly>
                                        <input type="hidden" data-id="bal" id="bal" value="{{$x}}" readonly>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <label class="col-lg-2 erp-col-form-label">Notes:</label>
                    <div class="col-lg-10">
                        <textarea type="text" rows="2" name="stock_remarks" class="moveIndex form-control erp-form-control-sm">{{isset($remarks)?$remarks:""}}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/stock.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
   <script>
        var formcase = '{{$case}}';
    </script>
   <script>
        $(document).ready(function(){

            var short = $('#short').val();
            var excess = $('#excess').val();
            var bal = $('#bal').val();

            short = (short == '' || short == undefined) ? 0 : short;
            excess = (excess == '' || excess == undefined) ? 0 : excess;
            bal = (bal == '' || bal == undefined) ? 0 : bal;

            document.getElementById('Shortage').innerText = '('+short+')';
            document.getElementById('Excess').innerText = '('+excess+')';
            document.getElementById('Balance').innerText = '('+bal+')';
        });

        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            {
                'id':'product_name',
                'fieldClass':'product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id','pd_barcode','product_barcode_packing'];
    </script>
    <script>
        @if($case == 'new')
            var storeid = '{{$storeid}}';
            getStockLocations(storeid)
        @endif
        $('#store_id').change(function(event){
            $('#stock_adjustment_form').find('.display_stock_location>.select2').addClass('pointerEventsNone')
            var storeid = $(this).val();
            getStockLocations(storeid)
        });
        function getStockLocations(storeid){
            var formData = {
                store_id : storeid,
            }
            var url = '{{action('Inventory\StockController@getLocationByStore',$type)}}';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type        : 'POST',
                url         : url,
                dataType	: 'json',
                data        : formData,
                success: function(response) {
                    if(response['status'] == 'success'){
                        var option = '<option value="0">Select</option>';
                        response['locations'].forEach(function(row){
                            option += '<option value="'+row.display_location_id+'">'+row.display_location_name_string+'</option>';
                        });
                        $('#stock_adjustment_form').find('.stock_location_id').html(option);
                        $('#stock_adjustment_form').find('.display_stock_location>.select2').removeClass('pointerEventsNone')
                    }
                    if(response['status'] == 'error'){
                        var option = '<option value="0">Select</option>';
                        $('#stock_adjustment_form').find('.stock_location_id').html(option);
                        $('#stock_adjustment_form').find('.display_stock_location>.select2').removeClass('pointerEventsNone')
                    }
                },
            })
        }
        $(document).on('keyup','.tblGridPhysicalQty',function(){
            var thix = $(this);
            var tr = thix.parents('tr');
            var physical_quantity = thix.val();
            if(physical_quantity == '' || physical_quantity == undefined || physical_quantity == null){
                physical_quantity = 0;
            }
            var store_quantity = tr.find('.tblGrid_stockQty').val();
            if(store_quantity == '' || store_quantity == undefined || store_quantity == null){
                store_quantity = 0;
            }
            var adj = 0;
            if(parseFloat(physical_quantity) !== NaN && parseFloat(store_quantity) !== NaN ){
                var adj = parseFloat(physical_quantity) - parseFloat(store_quantity);
            }
            tr.find('.tblGridAdjustment').val(adj);

            var cost_rate = tr.find('.cost_rate').val();
            if(cost_rate == '' || cost_rate == undefined || cost_rate == null){
                cost_rate = 0;
            }
            var cost_amount = parseFloat(cost_rate) * parseFloat(adj);
            tr.find('.cost_amount').val(cost_amount);

        });

    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('js/pages/js/product-inline-ajax-detail.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/change-grid-item-rate.js') }}" type="text/javascript"></script>

    <script>
        $(document).on('click','#header_input_clear_data',function(){
            $("#data_bank_reconciliation>thead input").val("");
            $('#data_bank_reconciliation>thead input').each(function(){
                var val = $(this).val();
                var index = $(this).parent('th').index();
                var arr = {
                    index : index,
                    val : val
                }
                funFilterDataRow1(arr);
            })
        })


        $(document).on('keyup','.erp_data__inline_filter>thead input',function(){
            var val = $(this).val();
            var index = $(this).parent('th').index();
            var arr = {
                index : index,
                val : val
            }

            funFilterDataRow1(arr);
        })

        function funFilterDataRow1(arr) {
            var input, filter, table, tr, td, i, txtValue;
            input = arr.val;
            var td_index = arr.index;
            filter = input;
            var id = $('.erp_data__inline_filter').attr('id');
            
            if(id == undefined){
                id = "table_filter";
                $('.erp_data__inline_filter').attr('id',id);
            }
            table = document.getElementById(id);
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[td_index];
                    
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    console.log(txtValue.indexOf(filter));
                    if (txtValue.indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
        $('#tb_analysis_detail').click(function(){
            var pd_barcode = $('#pd_barcode').val();
            var product_barcode_id = $('#product_barcode_id').val();
            var product_id = $('#product_id').val();
            if(!valueEmpty(pd_barcode) && !valueEmpty(product_barcode_id) && !valueEmpty(product_id)){
                localStorage.setItem("product_barcode_barcode", pd_barcode);
                localStorage.setItem("product_barcode_id", product_barcode_id);
                localStorage.setItem("product_id", product_id);
                localStorage.setItem("inv_type", "grn");
                window.open('/smart-product/tp-analysis','_blank');
            }else{
                toastr.error("Please select barcode");
            }
        });

        $('#tb_product_detail').click(function(){
            var pd_barcode = $('#pd_barcode').val();
            var product_barcode_id = $('#product_barcode_id').val();
            var product_id = $('#product_id').val();
            if(!valueEmpty(pd_barcode) && !valueEmpty(product_barcode_id) && !valueEmpty(product_id)){
                var data_url = '/common/get-product-detail/get-product/'+product_id;
                $('#kt_modal_md').modal('show').find('.modal-content').load(data_url);
                $('.modal-dialog').draggable({
                    handle: ".prod_head"
                });
            }else{
                toastr.error("Please select barcode");
            }
        });
    </script>
    @include('partial_script.table_column_switch')
@endsection
