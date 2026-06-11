@extends('layouts.layout')
@section('title', 'Stock Receiving')

@section('pageCSS')
@endsection

@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $user_branches = isset($data['user_branches']) ? $data['user_branches'] : [];

        if($case == 'view'){
            $case = 'edit';
        }
        if($case == 'new'){
            $code = $data['stock_code'];
            $date =  date('d-m-Y');
            $id = '';
            $store = \App\Models\TblDefiStore::where('branch_id',auth()->user()->branch_id)->where('store_default_value',1)->first();
            $store_to = isset($store->store_id)?$store->store_id:"";
        }
        if($case == 'edit'){
            $id = $data['current']->stock_id;
            $code = $data['current']->stock_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->stock_date))));
            $store_from = $data['current']->stock_store_from_id;
            $store_to = $data['current']->stock_store_to_id;
            $branch_from = $data['current']->stock_branch_from_id;
            $branch_to = $data['current']->stock_branch_to_id;
            $remarks = $data['current']->stock_remarks;
            $selected_rate_type = $data['current']->stock_rate_type;
            $rate_perc = $data['current']->stock_rate_perc;
            $stock_from_id = $data['current']->stock_request_id;
            if(isset($stock_from_id)){
                $stock_transfer = \App\Models\TblInveStock::where('stock_id',(int)$stock_from_id)->first();
                $stock_transfer_code = $stock_transfer->stock_code;
            }
            $dtls = isset($data['current']->stock_dtls)? $data['current']->stock_dtls :[];
        }
        $type =$data['form_type'];
        $form_type = $data['stock_code_type'];
    @endphp
    @permission($data['permission'])
    <form id="stock_transfer_receiving" class="stock_form kt-form" method="post" action="{{ action('Inventory\StockController@store', [$type,$id]) }}">
        @csrf
        <input type="hidden" name="stock_code_type" value='{{$data['stock_code_type']}}' id="form_type">
        <input type="hidden" name="stock_menu_id" value='{{$data['stock_menu_id']}}'>
        <input type="hidden" name="branch" value="{{auth()->user()->branch_id}}">
        <input type="hidden" id="stock_id" name="stock_id" value="{{ isset($id) ? $id : '' }}">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky {{ (isset($staging_data) && $staging_data['has_staging']) ? 'has-staging' : '' }}">
                    @include('elements.page_header', ['page_data' => $data['page_data']])
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
                                <label class="col-lg-6 erp-col-form-label">Store:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="moveIndex form-control erp-form-control-sm kt-select2" id="store_id" name="store_to">
                                            <option value="0">Select</option>
                                            @php $store_to = isset($store_to)?$store_to:'' @endphp
                                            @foreach($data['store'] as $store)
                                                <option value="{{$store->store_id}}" data-branch="{{ $store->branch_id }}" {{$store->store_id == $store_to?'selected':''}}>{{$store->store_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                         @include('layouts.branchSelect')
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-12">
                            <div class="row">
                                <label class="col-lg-2 erp-col-form-label">Stock From:</label>
                                <div class="col-lg-6">
                                    <div class="erp_form___block">
                                        <div class="input-group open-modal-group">
                                            <div class="input-group-prepend">
                                                @if($case == 'new')
                                                    <span class="input-group-text btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                                @endif
                                            </div>
                                            @if($case == 'new')
                                                <input type="text" value="{{isset($stock_transfer_code)?$stock_transfer_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','stockTransferHelp')}}" id="stock_transfer_code" name="stock_transfer_code" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                            @else
                                                <input type="text" value="{{isset($stock_transfer_code)?$stock_transfer_code:''}}" id="stock_transfer_code" name="stock_transfer_code" class="readonly form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                            @endif
                                            <input type="hidden" id="stock_from_id" name="stock_from_id" value="{{isset($stock_from_id)?$stock_from_id:''}}"/>
                                            <input type="hidden" id="store" name="store" value="{{isset($store_from)?$store_from:''}}"/>
                                            <input type="hidden" id="branch_from_id" name="branch_from_id" value="{{isset($branch_from)?$branch_from:''}}"/>
                                            <div class="input-group-append">
                                            <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                <i class="la la-search"></i>
                                            </span>
                                                @if($case == 'new')
                                                    <span class="input-group-text group-input-btn" id="getStockTransferData">
                                                GO
                                            </span>
                                                @endif
                                                <span class="input-group-text group-input-btn" id="getStockTransferPrintData">
                                                <i class="la la-print"></i>
                                            </span>
                                            </div>
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
                                        $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Demand Qty','Stock Transfer Qty','Qty','Rate','Amount','Disc Percent','Disc Amt','VAT Percent','VAT Amt','Gross Amt'];
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
                                            {{--<div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                                <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                            </div>--}}
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">
                                                Barcode
                                                {{--<button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                    <i class="la la-barcode"></i>
                                                </button>--}}
                                            </div>
                                            {{--<div class="erp_form__grid_th_input">
                                                <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">
                                            </div>--}}
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Product Name</div>
                                            {{--<div class="erp_form__grid_th_input">
                                                <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                            </div>--}}
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">UOM</div>
                                            {{--<div class="erp_form__grid_th_input">
                                                <select id="pd_uom" class="pd_uom tb_moveIndex form-control erp-form-control-sm">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>--}}
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Packing</div>
                                            {{--<div class="erp_form__grid_th_input">
                                                <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                                            </div>--}}
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Demand Qty</div>
                                            {{--<div class="erp_form__grid_th_input">
                                                <input id="demand_qty" readonly type="text" class="demand_qty tb_moveIndex validNumber validOnlyNumber form-control erp-form-control-sm">
                                            </div>--}}
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Stock Transfer Qty</div>
                                            {{--<div class="erp_form__grid_th_input">
                                                <input id="stock_transfer_qty" readonly type="text" class="stock_transfer_qty tb_moveIndex validNumber validOnlyNumber form-control erp-form-control-sm">
                                            </div>--}}
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Qty</div>
                                            {{--<div class="erp_form__grid_th_input">
                                                <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>--}}
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Rate</div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Amount</div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Disc Percent</div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Disc Amt</div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">VAT Percent</div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">VAT Amt</div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Gross Amt</div>
                                        </th>
                                        <th scope="col" width="48">
                                            <div class="erp_form__grid_th_title">Action</div>
                                            {{--<div class="erp_form__grid_th_btn">
                                                <button type="button" id="addData" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </div>--}}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body">
                                    @if(isset($dtls))
                                        @foreach($dtls as $dtl)
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][stock_dtl_id]" data-id="stock_dtl_id" value="{{$dtl->stock_dtl_id}}" class="stock_dtl_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                </td>
                                                <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{isset($dtl->barcode) && isset($dtl->barcode->product_barcode_barcode) ? $dtl->barcode->product_barcode_barcode : ''}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product) && isset($dtl->product->product_name) ? $dtl->product->product_name : ''}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                                <td>
                                                    <select class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][uom]">
                                                        <option value="{{isset($dtl->uom) && isset($dtl->uom->uom_id) ? $dtl->uom->uom_id : ''}}">{{isset($dtl->uom) && isset($dtl->uom->uom_name) ? $dtl->uom->uom_name : ''}}</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" data-id="pd_packing" name="pd[{{$loop->iteration}}][pd_packing]" value="{{isset($dtl->barcode) && isset($dtl->barcode->product_barcode_packing) ? $dtl->barcode->product_barcode_packing : ''}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="demand_qty" name="pd[{{$loop->iteration}}][demand_qty]" value="{{number_format($dtl->stock_dtl_demand_quantity)}}" class="demand_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                <td><input type="text" data-id="stock_transfer_qty" name="pd[{{$loop->iteration}}][stock_transfer_qty]" value="{{number_format($dtl->stock_dtl_stock_transfer_qty)}}" class="stock_transfer_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{number_format($dtl->stock_dtl_quantity)}}" class="tblGridCal_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{isset($dtl->stock_dtl_rate) && $dtl->stock_dtl_rate != '' ? number_format($dtl->stock_dtl_rate,3,'.','') : (isset($dtl->stock_dtl_purc_rate) && $dtl->stock_dtl_purc_rate != '' ? number_format($dtl->stock_dtl_purc_rate,3,'.','') : '')}}" class="tblGridCal_rate form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                <td><input type="text" data-id="amount" name="pd[{{$loop->iteration}}][amount]" value="{{isset($dtl->stock_dtl_amount) && $dtl->stock_dtl_amount != '' ? number_format($dtl->stock_dtl_amount,3,'.','') : ''}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                <td><input type="text" data-id="dis_perc" name="pd[{{$loop->iteration}}][dis_perc]" value="{{isset($dtl->stock_dtl_disc_percent) && $dtl->stock_dtl_disc_percent != '' ? number_format($dtl->stock_dtl_disc_percent,2,'.','') : ''}}" class="tblGridCal_discount_perc form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                <td><input type="text" data-id="dis_amount" name="pd[{{$loop->iteration}}][dis_amount]" value="{{isset($dtl->stock_dtl_disc_amount) && $dtl->stock_dtl_disc_amount != '' ? number_format($dtl->stock_dtl_disc_amount,3,'.','') : ''}}" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                <td><input type="text" data-id="vat_perc" name="pd[{{$loop->iteration}}][vat_perc]" value="{{isset($dtl->stock_dtl_vat_percent) && $dtl->stock_dtl_vat_percent != '' ? number_format($dtl->stock_dtl_vat_percent,2,'.','') : ''}}" class="tblGridCal_vat_perc form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                <td><input type="text" data-id="vat_amount" name="pd[{{$loop->iteration}}][vat_amount]" value="{{isset($dtl->stock_dtl_vat_amount) && $dtl->stock_dtl_vat_amount != '' ? number_format($dtl->stock_dtl_vat_amount,3,'.','') : ''}}" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                <td><input type="text" data-id="gross_amount" name="pd[{{$loop->iteration}}][gross_amount]" value="{{isset($dtl->stock_dtl_total_amount) && $dtl->stock_dtl_total_amount != '' ? number_format($dtl->stock_dtl_total_amount,3,'.','') : ''}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                <td class="text-center">
                                                    {{--<div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                    </div>--}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
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
                                    <td><span class="t_gross_total t_total">0</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- @include('inventory.stock_transfer.summary_total') --}}
                    <div class="row form-group-block">
                        <label class="col-lg-2 erp-col-form-label">Notes:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="2" name="stock_remarks" class="moveIndex form-control erp-form-control-sm">{{isset($remarks)?$remarks:""}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('staging_activity.auto_include')
    </form>
    <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    @include('partial_script.po_header_calc');
    <script src="{{ asset('js/pages/js/stock.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var formcase = '{{$case}}';
    </script>
    <script>

        $(document).ready(function() {
            // 1. Keep a clone copy of all original store options in memory
            var $storeSelect = $('#store_id');
            var $allStoreOptions = $storeSelect.find('option').clone();

            // Function to filter stores based on selected branch
            function filterStores() {
                var selectedBranchId = $('#new_branch_id').val();
                
                // Reset the select element
                $storeSelect.html('');

                // Re-add the placeholder first if it exists
                var $placeholder = $allStoreOptions.filter(function() { return !$(this).attr('data-branch'); });
                $storeSelect.append($placeholder);

                // Find options matching the selected branch and append them
                var $matchingOptions = $allStoreOptions.filter(function() {
                    return $(this).attr('data-branch') == selectedBranchId;
                });
                
                $storeSelect.append($matchingOptions);

                // CRITICAL: Trigger Select2 change to refresh its UI display
                $storeSelect.trigger('change.select2');
            }

            // 2. Run immediately on page load to handle initial or saved state
            filterStores();

            // 3. Run whenever the branch dropdown changes
            $('#new_branch_id').on('change', function() {
                filterStores();
            });
        });

        var type = "{{ $type }}";
        function voucher_posted(){
            var stock_id = $('#stock_id').val();
            if(!stock_id){
                toastr.error('Stock id not found');
                return;
            }
            $.ajax({
                headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'POST',
                url: '/stock/' + type + '/post',
                dataType: 'json',
                data: { stock_id: stock_id },
                success: function(response, textStatus, xhr) {
                    erpDocumentAjaxDone(response, xhr, { successMsg: 'Successfully Posted.' });
                },
                error: function(xhr) {
                    erpDocumentAjaxDone(null, xhr, { errorMsg: 'Unable to post.' });
                }
            });
        }

        function voucher_unposted(){
            var stock_id = $('#stock_id').val();
            if(!stock_id){
                toastr.error('Stock id not found');
                return;
            }
            $.ajax({
                headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'POST',
                url: '/stock/' + type + '/unposted',
                dataType: 'json',
                data: { data: [stock_id] },
                success: function(response, textStatus, xhr) {
                    erpDocumentAjaxDone(response, xhr, { successMsg: 'Successfully Un-Posted.' });
                },
                error: function(xhr) {
                    erpDocumentAjaxDone(null, xhr, { errorMsg: 'Unable to unpost.' });
                }
            });
        }

        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode open_inline__help',
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
                'id':'demand_qty',
                'fieldClass':'demand_qty validNumber validOnlyNumber',
                'readonly':true
            },
            {
                'id':'stock_transfer_qty',
                'fieldClass':'stock_transfer_qty validNumber validOnlyNumber',
                'readonly':true
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty validNumber validOnlyNumber',
                'readonly':true
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate validNumber validOnlyNumber',
                'readonly':true
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount validNumber validOnlyNumber',
                'readonly':true
            },
            {
                'id':'dis_perc',
                'fieldClass':'tblGridCal_discount_perc validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id':'dis_amount',
                'fieldClass':'tblGridCal_discount_amount validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id':'vat_perc',
                'fieldClass':'tblGridCal_vat_perc validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id':'vat_amount',
                'fieldClass':'tblGridCal_vat_amount validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true
            },
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];

        $(".date_inputmask").inputmask("99-99-9999", {
            "mask": "99-99-9999",
            "placeholder": "dd-mm-yyyy",
            autoUnmask: true
        });
        $('#getStockTransferData').click(function(){
            var thix = $(this);
            var val = thix.parents('.input-group').find('input#stock_from_id').val();
            if(val){
                swal.fire({
                    title: 'Alert!',
                    text: "Are You Sure To Get Data!",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then(function(result) {
                    if (result.value) {
                        var formData = {
                            stock_id : val,
                        };
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type        : 'POST',
                            url         : '/stock/890/get-stock-transfer-dtl-data',
                            dataType	: 'json',
                            data        : formData,
                            success: function(response) {
                                if(response['status'] == 'success'){
                                    toastr.success(response.message);
                                    var stock = response.data.stock.stock_dtls;
                                    var tr = "";
                                    var iteration = $('.erp_form__grid_body').find('tr').length + 1;
                                    for(var i=0;i < stock.length;i++){
                                        var stocki = stock[i];
                                        var expiry_date = stocki['stock_dtl_expiry_date'];
                                        var d = new Date(expiry_date);
                                        var day =   (parseInt(d.getDate()) < 10) ? "0" + (d.getDate()).toString() : d.getDate();
                                        var month = (parseInt(d.getMonth()) < 10) ? "0" + (d.getMonth() + 1).toString() : (d.getMonth() + 1);
                                        var year = d.getFullYear();
                                        var stock_dtl_expiry_date =  day +'-'+ month +'-'+ year;
                                        tr += '<tr>\n' +
                                            '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>\n' +
                                            '<input type="text" value="'+iteration+'" name="pd['+iteration+'][sr_no]"  class="form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+iteration+'][stock_dtl_id]" data-id="stock_dtl_id" value="'+stocki['stock_dtl_id']+'" class="stock_dtl_id form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+iteration+'][product_id]" data-id="product_id" value="'+stocki['product_id']+'" class="product_id form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+iteration+'][uom_id]" data-id="uom_id" value="'+stocki['uom_id']+'" class="uom_id form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+iteration+'][product_barcode_id]" data-id="product_barcode_id" value="'+stocki['product_barcode_id']+'" class="product_barcode_id form-control erp-form-control-sm handle" readonly>\n' +
                                            '</td>\n' +
                                            '<td><input type="text" data-id="pd_barcode" name="pd['+iteration+'][pd_barcode]" value="'+stocki['product_barcode_barcode']+'" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>\n' +
                                            '<td><input type="text" data-id="product_name" name="pd['+iteration+'][product_name]" value="'+stocki['product']['product_name']+'" class="product_name form-control erp-form-control-sm" readonly></td>\n' +
                                            '<td>\n' +
                                            '<select class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm" data-id="pd_uom" name="pd['+iteration+'][uom]">\n' +
                                            '<option value="'+stocki['uom_id']+'">'+stocki['uom']['uom_name']+'</option>\n' +
                                            '</select>\n' +
                                            '</td>\n' +
                                            '<td><input type="text" data-id="pd_packing" name="pd['+iteration+'][pd_packing]" value="'+stocki['stock_dtl_packing']+'" class="pd_packing form-control erp-form-control-sm" readonly></td>\n' +
                                            '<td><input type="text" data-id="demand_qty" name="pd['+iteration+'][demand_qty]" value="'+notEmptyZero(stocki['stock_dtl_demand_quantity'])+'" class="demand_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="stock_transfer_qty" name="pd['+iteration+'][stock_transfer_qty]" value="'+notEmptyZero(stocki['stock_dtl_quantity'])+'" class="stock_transfer_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="quantity" name="pd['+iteration+'][quantity]" value="'+notEmptyZero(stocki['stock_dtl_quantity'])+'" class="tblGridCal_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="rate" name="pd['+iteration+'][rate]" value="'+notNullEmpty(stocki['stock_dtl_rate'],3)+'" class="tblGridCal_rate form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="amount" name="pd['+iteration+'][amount]" value="'+notNullEmpty(stocki['stock_dtl_amount'],3)+'" class="tblGridCal_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="dis_perc" name="pd['+iteration+'][dis_perc]" value="'+notNullEmpty(stocki['stock_dtl_disc_percent'],2)+'" class="tblGridCal_discount_perc form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="dis_amount" name="pd['+iteration+'][dis_amount]" value="'+notNullEmpty(stocki['stock_dtl_disc_amount'],3)+'" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="vat_perc" name="pd['+iteration+'][vat_perc]" value="'+notNullEmpty(stocki['stock_dtl_vat_percent'],2)+'" class="tblGridCal_vat_perc form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="vat_amount" name="pd['+iteration+'][vat_amount]" value="'+notNullEmpty(stocki['stock_dtl_vat_amount'],3)+'" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="gross_amount" name="pd['+iteration+'][gross_amount]" value="'+notNullEmpty(stocki['stock_dtl_total_amount'],3)+'" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>\n' +
                                            '<td class="text-center">\n' +

                                            '</td>\n' +
                                            '</tr>';
                                        iteration += 1;
                                    }
                                    $('.erp_form__grid_body').html(tr);
                                    allCalcFunc();
                                    $('.OnlyEnterAllow').keypress(OnlyEnterAllow);
                                    $('input').attr('autocomplete', 'off');
                                    dataDelete();
                                    updateHiddenFields()

                                    $(".date_inputmask").inputmask("99-99-9999", {
                                        "mask": "99-99-9999",
                                        "placeholder": "dd-mm-yyyy",
                                        autoUnmask: true
                                    });
                                }
                            }
                        });
                    }
                });

            }else{
                toastr.error("Select first Stock Code");
            }
        });

        $(document).on('click','#getStockTransferPrintData',function(){
            var thix = $(this);
            var val = thix.parents('.open-modal-group').find('#stock_from_id').val();
            if(val){
                window.open("/stock/stock-transfer/from-stock-print/" + val,'_blank');
            }else{
                alert('first select stock from')
            }

        })


    </script>
    @yield('summary_total_pageJS')
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script>

        setInterval(function(){
            var t_gross_total = 0;
            $('.erp_form__grid_body>tr').each(function(){
                var thix = $(this);
                var amount = funcNumberFloat(thix.find('.tblGridCal_amount').val());
                t_gross_total += parseFloat(amount);
            })
            $('.t_gross_total').text(parseFloat(t_gross_total).toFixed(3));
        },100)
    </script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
