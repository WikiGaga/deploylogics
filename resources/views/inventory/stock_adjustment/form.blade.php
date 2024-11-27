@extends('layouts.layout')
@section('title', 'Stock Adjustment')

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
                $dtls = isset($data['current']->stock_dtls)? $data['current']->stock_dtls :[];

            }

            $type =$data['form_type'];
            $form_type = $data['stock_code_type'];
    @endphp
    @permission($data['permission'])
    <form id="stock_adjustment_form" class="stock_form kt-form" method="post" action="{{ action('Inventory\StockController@store', [$type,$id]) }}">
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
                                    <input type="text" name="stock_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
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
                <div class="row form-group-block">
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
                    <div class="col-lg-12 text-right">
                        <div class="data_entry_header">
                            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                    <i class="flaticon-more" style="color: #666666;"></i>
                                </button>
                                @php
                                    $headings = ['Sr No','Barcode','Product Name','UOM','Packing', 'Rate', 'Amount', 'Physical Stock Qty', 'Stock Qty',
                                                  'djustment Qty','Batch No', 'Production Date', 'Expiry Date'];
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
                <div class="form-group-block">
                    <div class="erp_form___block">
                        <div class="table-scroll form_input__block">
                            <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                <thead class="erp_form__grid_header">
                                <tr>
                                    <th scope="col" width="35px">
                                        <div class="erp_form__grid_th_title">Sr.</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                            <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                            <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                            <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">
                                            Barcode
                                            <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                <i class="la la-barcode"></i>
                                            </button>
                                        </div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Product Name</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">UOM</div>
                                        <div class="erp_form__grid_th_input">
                                            <select id="pd_uom" class="pd_uom form-control erp-form-control-sm">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Packing</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="rate" type="text" class="tblGridCal_rate validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Amount</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="amount" readonly type="text" class="tblGridCal_amount stock_amount validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Physical Stock Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="physical_quantity" type="text" class=" tblGridPhysicalQty validNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Stock Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="stock_quantity" type="text" class="tblGrid_stockQty pd_store_stock validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>

                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Adjustment Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="quantity" type="text" class="tblGridAdjustment validNumber validOnlyNumber form-control erp-form-control-sm" readonly>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Batch No</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="batch_no" type="text" class=" form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Production Date</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="production_date" value="" title="{{ date('d-m-Y') }}"  type="text"  class="date_inputmask form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Expiry Date</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="expiry_date" value="" title="{{ date('d-m-Y') }}"  type="text"  class="date_inputmask expiry-date tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col" width="48">
                                        <div class="erp_form__grid_th_title">Action</div>
                                        <div class="erp_form__grid_th_btn">
                                            <button type="button" id="addData" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                <i class="la la-plus"></i>
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="erp_form__grid_body">
                                @if(isset($dtls))
                                    @foreach($dtls as $dtl)
                                        <tr>
                                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]" class="form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                            </td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                            <td>
                                                <select class="pd_uom field_readonly form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][uom]">
                                                    <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" data-id="pd_packing" name="pd[{{$loop->iteration}}][pd_packing]" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{number_format($dtl->stock_dtl_rate,2)}}" class="tblGridCal_rate form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" data-id="amount" name="pd[{{$loop->iteration}}][amount]" value="{{number_format($dtl->stock_dtl_amount,3)}}" class="tblGridCal_amount stock_amount form-control erp-form-control-sm validNumber" readonly></td>
                                            <td><input type="text" data-id="physical_quantity" name="pd[{{$loop->iteration}}][physical_quantity]" value="{{$dtl->stock_dtl_physical_quantity}}" class="tblGridPhysicalQty tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" data-id="stock_quantity" name="pd[{{$loop->iteration}}][stock_quantity]" value="{{$dtl->stock_dtl_stock_quantity}}" class="  tblGrid_stockQty form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->stock_dtl_quantity}}" class="tblGridAdjustment validNumber validOnlyNumber  form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="batch_no" name="pd[{{$loop->iteration}}][batch_no]" value="{{isset($dtl->stock_dtl_batch_no)?$dtl->stock_dtl_batch_no:""}}" class=" form-control erp-form-control-sm"></td>
                                            @php $prod_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_production_date)))); @endphp
                                            <td><input type="text" name="pd[{{ $loop->iteration }}][production_date]" data-id="production_date" value="{{ $prod_date == '01-01-1970' ? '' : $prod_date }}" title="{{ $prod_date == '01-01-1970' ? '' : $prod_date }}" class="date_inputmask form-control form-control-sm" /> </td>
                                            @php $expiry_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_expiry_date)))); @endphp
                                            <td><input type="text" name="pd[{{ $loop->iteration }}][expiry_date]"  data-id="expiry_date" value="{{ $expiry_date == '01-01-1970' ? '' : $expiry_date }}" title="{{ $expiry_date == '01-01-1970' ? '' : $expiry_date }}" class="date_inputmask tb_moveIndex form-control form-control-sm" /> </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                                <tbody class="erp_form__grid_body_total">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="total_grid_amount">
                                            <input value="0.000" readonly type="text" class="tblGridCal_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-md-10 col-lg-2 text-right">
                        <table class="tableTotal" style="width: 100%;">
                            <tbody>
                            <tr>
                                <td><div class="t_total_label">Total:</div></td>
                                <td><span class="t_stock_gross_total t_total">0</span></td>
                            </tr>
                            </tbody>
                        </table>
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
                <!--end::Form-->
    @endpermission
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
        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode tb_moveIndex open_inline__help',
                'require':true,
                'readonly':true
                //  'data-url' : productHelpUrl
            },
            {
                'id':'product_name',
                'fieldClass':'product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
            {
                'id':'pd_uom',
                'fieldClass':'pd_uom field_readonly',
                'type':'select'
            },
            {
                'id':'pd_packing',
                'fieldClass':'pd_packing',
                'readonly':true
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate validNumber'

            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount stock_amount validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id':'physical_quantity',
                'fieldClass':'validNumber tb_moveIndex tblGridPhysicalQty'
            },
            {
                'id':'stock_quantity',
                'fieldClass':'validNumber tblGrid_stockQty',
                'readonly':true
            },

            {
                'id':'quantity',
                'fieldClass':'tblGridAdjustment validNumber validOnlyNumber ',
                'readonly':true
            },
            {
                'id':'batch_no',
                'fieldClass':''
            },
            {
                'id':'production_date',
                'fieldClass':'date_inputmask'
            },
            {
                'id':'expiry_date',
                'fieldClass':'date_inputmask tb_moveIndex expiry-date'
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
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
        })
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('js/pages/js/product-inline-ajax-detail.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/change-grid-item-rate.js') }}" type="text/javascript"></script>


    @include('partial_script.table_column_switch')
@endsection
