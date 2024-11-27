@extends('layouts.layout')
@section('title', 'Re-Order Stock Analysis')

@section('pageCSS')
@endsection
@section('content')
@php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    if($case == 'new'){
        $date =  date('d-m-Y');
        $as_on_date =  date('d-m-Y');
        $from_date =  date('d-m-Y');
        $to_date =  date('d-m-Y');
        $satement_date =  date('d-m-Y');
        $dtls = [];
        $bank_acco = [];
        $withoutvendor='';
        $withinventory='';
    }
@endphp
{{--@permission($data['permission'])--}}
<!--begin::Form-->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
            @include('elements.page_header',['page_data' => $data['page_data']])
        </div>
        <div class="kt-portlet__body">
            <div class="row form-group-block">
                <div class="col-lg-4">
                    <div class="row">
                        <label class="col-lg-3 erp-col-form-label">Branch:</label>
                        <div class="col-lg-8">
                            <div class="erp-select2">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control kt-select2 erp-form-control-sm" id="branch_name" name="branch_ids">
                                        @foreach($data['branches'] as $branch)
                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="kt-checkbox-list">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                    <input type="checkbox" id="withinventory" {{$withinventory=='1'?'checked':""}} name="withinventory">
                                    With Inventory
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <label class="col-lg-6 erp-col-form-label">Make Purchase Order: </label>
                        <div class="col-lg-6">
                            <div class="kt-radio-list">
                                <label class="kt-radio kt-radio--bold kt-radio--brand">
                                    <input type="radio" id="make_pruc_order" value="makepurchaseorder" name="make_pruc_order" checked>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group-block row">
                <div class="col-lg-4">
                    <div class="row">
                        <label class="col-lg-4 erp-col-form-label">Vendor Name:<span class="required">*</span></label>
                        <div class="col-lg-7">
                            <div class="erp_form___block">
                                <div class="input-group open-modal-group">
                                    <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                    </div>
                                    <input type="text" value="{{isset($supplier_code)?$supplier_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" id="supplier_name" name="supplier_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                    <input type="hidden" id="supplier_id" name="supplier_id" value="{{isset($supplier_id)?$supplier_id:''}}"/>
                                    <div class="input-group-append">
                                            <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                            <i class="la la-search"></i>
                                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <label class="col-lg-4 erp-col-form-label">&nbsp;</label>
                        <div class="col-lg-8">
                            <button type="button" class="moveIndex btn btn-sm btn-danger" id="remove_data">Un Link Supplier</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="kt-checkbox-list">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                    <input type="checkbox" id="withoutvendor" {{$withoutvendor=='1'?'checked':""}} name="withoutvendor">
                                    Without Vendor
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9" style="background-color:#FFF4DE">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="col-lg-8 erp-col-form-label"><strong>Stock Filter:-</strong></label>
                                </div>
                                <div class="col-lg-1">
                                    <label class="col-lg-2 erp-col-form-label">ALL</label>
                                </div>
                                <div class="col-lg-1">
                                    <div class="kt-radio-list">
                                        <label class="kt-radio kt-radio--bold kt-radio--brand moveIndex">
                                            <input type="radio" id="stock_filter_all" value="all" name="stock_filter">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="col-lg-8 erp-col-form-label">Excess Stock</label>
                                </div>
                                <div class="col-lg-1">
                                    <div class="kt-radio-list">
                                        <label class="kt-radio kt-radio--bold kt-radio--brand">
                                            <input type="radio" id="stock_filter_excess" value="excess" name="stock_filter">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label class="col-lg-8 erp-col-form-label">Shortage</label>
                                </div>
                                <div class="col-lg-1">
                                    <div class="kt-radio-list">
                                        <label class="kt-radio kt-radio--bold kt-radio--brand">
                                            <input type="radio" id="stock_filter_shortage" value="shortage" name="stock_filter" checked>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group-block row">
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="col-lg-12 erp-col-form-label">
                                <strong>Stock Data Aging From Date: <span id="Data_Aging"></span></strong>
                            </label>
                            <input type="hidden"  name="StockDataAging" value="{{isset($StockDataAging)?$StockDataAging:""}}"  id="StockDataAging"/>
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-lg-2">
                                <input type="text" name="Days0" id="Days0" value="{{isset($Days)?$Days:"0"}}" class="moveIndex" style="width:45px;" onblur="changeDate();">
                            </div>
                            <div class="col-lg-2">
                                <input type="text" name="Days1" id="Days1" value="{{isset($Days)?$Days:"0"}}" class="moveIndex" style="width:45px;" onblur="changeDate();">
                            </div>
                            <div class="col-lg-2">
                                <input type="text" name="Days2" id="Days2" value="{{isset($Days)?$Days:"0"}}" class="moveIndex" style="width:45px;" onblur="changeDate();">
                            </div>
                            <div class="col-lg-2">
                                <input type="text" name="Days3" id="Days3" value="{{isset($Days)?$Days:"0"}}" class="moveIndex" style="width:45px;" onblur="changeDate();">
                            </div>
                            <div class="col-lg-2">
                                <input type="text" name="Days4" id="Days4" value="{{isset($Days)?$Days:"0"}}" class="moveIndex" style="width:45px;" onblur="changeDate();">
                            </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-8">
                            <label class="col-lg-8 erp-col-form-label"><strong>As On Date (To Date)</strong></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group date">
                                <input type="text" name="as_on_date" id="as_on_date" class="moveIndex form-control erp-form-control-sm moveIndex c-date-p kt_datepicker_bcs" readonly value="{{isset($as_on_date)?$as_on_date:''}}"/>
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
                        <div class="col-lg-8">
                            <label class="col-lg-8 erp-col-form-label">&nbsp;</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="col-lg-8 erp-col-form-label">Lead Days: </label>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" id="leaddays" value="{{isset($leaddays)?$leaddays:"30"}}" name="leaddays" class="moveIndex">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2">
                    <button type="button" class="moveIndex btn btn-sm btn-primary" id="get_data">Get Data</button>
                    <div style="font-size: 9px;color: red;"><br></div>
                </div>
                <div class="col-lg-8 text-right"> </div>
                <div class="col-lg-2 text-right">
                    <div class="data_entry_header">
                        <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide
                        </div>
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                <i class="flaticon-more" style="color: #666666;"></i>
                            </button>
                            @php
                                $headings = ['Check All','Sr No','Barcode','Item Name','Packing','Last PI Vendor','Last PI Rate','Last PI Qty','Last PI Date',
                                            'Sale Last PI','Sale Days after Last PI','Last Sale Date','Last Audit Date','ReOrder Amount','ReOrder Qty','Suggested Stock','Current Stock',
                                            'Expiry Date','Stock In From Branch','Days With Stock','Stock Status','Per Days' ,'1 Days','2 Days','3 Days','4 Days','5 Days',
                                            'Aging Grand Total', 'Branch Name','Top Level Category','Unit Name','Requested Branch Qty'];
                            @endphp
                            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                @foreach ($headings as $key => $heading)
                                    <li>
                                        <label>
                                            <input value="{{ $key }}" name="{{ trim($key) }}"
                                                   type="checkbox" checked> {{ $heading }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                table#data_re_order_stock {
                    border-bottom: 2px solid #a5a5a5;
                }
                table#data_re_order_stock th {
                    border: 0px solid #cecece;
                    background: #ececec;
                    font-size: 12px;
                    font-weight: 500 !important;
                    text-align: center;
                    padding: 12px 3px !important;
                    font-family: Roboto;
                }
                table#data_re_order_stock td {
                    font-size: 12px;
                    font-weight: 400;
                    padding: 5px 3px !important;
                    border: 1px solid #1e1e1e;
                }
                table#data_re_order_stock tr:nth-child(even)>td {
                    background: #fbfbfb;
                    border-bottom: 2px solid #dadada;
                }
                table#data_re_order_stock tr:nth-child(even)>td input {
                    background: #fbfbfb;
                }
                .pd_bank_recon_input{
                    width: 100%;
                    border: none;
                }
                .pd_bank_recon_input_open{
                    width: 100%;
                    border: 1px solid #ececec;
                    border-radius: 3px;
                }
                .pd_bank_recon_input:focus{
                    outline: 0;
                }
            </style>
            <div class="form-group-block">
                <div class="erp_form___block">
                    <div class="table-scroll form_input__block">
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="data_re_order_stock" class="table table_column_switch table_pit_list erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                        <tr>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Check All</div>
                                                <input type="checkbox" name="chkAll" id="chkAll" onChange="CheckAll()" >
                                             </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Sr.</div>
                                                <div class="erp_form__grid_th_input"></div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Barcode
                                                </div>
                                                <input type="text" class="filter_barcode" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Item Name
                                                </div>
                                                <input type="text" class="filter_itemname" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Last PI Date
                                                </div>
                                                <input type="text" class="filter_last_pi_date" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Last PI Qty
                                                </div>
                                                <input type="text" class="filter_last_pi_qty" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Aging Grand Total
                                                </div>
                                                <input type="text" class="filter_aging_grand_total" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Current Stock
                                                </div>
                                                <input type="text" class="filter_curr_stock" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    ReOrder Qty
                                                </div>
                                                <input type="text" class="filter_reorder_qty" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Last PI Vendor
                                                </div>
                                                <input type="text" class="filter_last_pi_vendor" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Last PI Rate
                                                </div>
                                                <input type="text" class="filter_last_pi_rate" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Sale Last PI
                                                </div>
                                                <input type="text" class="filter_sale_last_pi" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Sale Days after Last PI
                                                </div>
                                                <input type="text" class="filter_sale_day_last_pi" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Last Sale Date
                                                </div>
                                                <input type="text" class="filter_last_sale_date" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Last Audit Date
                                                </div>
                                                <input type="text" class="filter_last_audit_date" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    ReOrder Amount
                                                </div>
                                                <input type="text" class="filter_reorder_amount" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Suggested Stock
                                                </div>
                                                <input type="text" class="filter_suggested_stock" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Expiry Date
                                                </div>
                                                <input type="text" class="filter_expiry_date" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Stock In From Branch
                                                </div>
                                                <input type="text" class="filter_stock_in" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Days With Stock
                                                </div>
                                                <input type="text" class="filter_day_with_stock" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Stock Status
                                                </div>
                                                <input type="text" class="filter_stock_status" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Per Days
                                                </div>
                                                <input type="text" class="filter_per_day" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    <input readonly type="text" id="day0" style="text-align:center;background: #ececec;width:10%;border:none;"> Days
                                                </div>
                                                <input type="text" class="filter_1_day" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    <input readonly type="text" id="day1" style="text-align:center;background: #ececec;width:10%;border:none;"> Days
                                                </div>
                                                <input type="text" class="filter_2_day" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    <input readonly type="text" id="day2" style="text-align:center;background: #ececec;width:10%;border:none;"> Days
                                                </div>
                                                <input type="text" class="filter_3_day" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    <input readonly type="text" id="day3" style="text-align:center;background: #ececec;width:10%;border:none;"> Days
                                                </div>
                                                <input type="text" class="filter_4_day" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    <input readonly type="text" id="day4" style="text-align:center;background: #ececec;width:10%;border:none;"> Days
                                                </div>
                                                <input type="text" class="filter_5_day" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Branch Name
                                                </div>
                                                <input type="text" class="filter_company_shortname" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Top Level Category
                                                </div>
                                                <input type="text" class="filter_top_lavel" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Unit Name
                                                </div>
                                                <input type="text" class="filter_unit_name" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Requested Branch Qty
                                                </div>
                                                <input type="text" class="filter_req_branch_qty" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Package
                                                </div>
                                                <input type="text" class="filter_package" style="width: 100%;">
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Action
                                                </div>
                                                <div class="erp_form__grid_th_input">
                                                    <button type="button" class="btn btn-sm btn-danger" id="header_input_clear_data" style="padding: 3px;"><i class="la la-times"></i></button>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Form-->
{{--@endpermission--}}
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    
    @include('partial_script.po_header_calc');
    <script>
        var onPageSpinner = "<div class='kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center'> <span>loading..</span></div>";

        $(document).on('click','#go_date',function(){
            if($(document).find('#data_re_order_stock>tbody>tr').length != 0){
                var reconciled_date = $('.reconciled_date').val();
                $(document).find('#data_re_order_stock>tbody>tr').each(function() {
                    var check = $(this).find('td input.marked');
                    if(check.prop('checked')){
                        $(this).find('td .cleared_date_input').val(reconciled_date);
                    }
                });
            }
        });
        var xhrGetDataStatus = true;
        $(document).on('click','#get_data',function(){
            var validate = true;
        
            var aging_days0 = $('#Days0').val();
            var aging_days1 = $('#Days1').val();
            var aging_days2 = $('#Days2').val();
            var aging_days3 = $('#Days3').val();
            var aging_days4 = $('#Days4').val();

            if(aging_days0 != "" && aging_days0 != 0){
                document.getElementById('day0').value = aging_days0;
            }
            if(aging_days1 != "" && aging_days1 != 0){
                document.getElementById('day1').value = aging_days1;
            }
            if(aging_days2 != "" && aging_days2 != 0){
                document.getElementById('day2').value = aging_days2;
            }
            if(aging_days3 != "" && aging_days3 != 0){
                document.getElementById('day3').value = aging_days3;
            }
            if(aging_days4 != "" && aging_days4 != 0){
                document.getElementById('day4').value = aging_days4;
            }

            var radioButtonGroup = document.getElementsByName("stock_filter");
            var checkedRadio = Array.from(radioButtonGroup).find(
                (radio) => radio.checked
            );

            var supplier_id = $('#supplier_id').val();
            var from_date = $('#StockDataAging').val();
            var to_date = $('#as_on_date').val();
            var branch_name = $('#branch_name').val();
            var leaddays = $('#leaddays').val();
            var stock_filter = checkedRadio.value;

            if(valueEmpty(branch_name)){
                toastr.error("Branch is required");
                validate = false;
                return true;
            }
            if(valueEmpty(supplier_id)){
                toastr.error("Vendor Name is required");
                validate = false;
                return true;
            }
            if(valueEmpty(leaddays)){
                toastr.error("Lead Days is required");
                validate = false;
                return true;
            }
            if(validate && xhrGetDataStatus){
                xhrGetDataStatus = false;
                $('#data_re_order_stock>tbody').html(onPageSpinner);
                var formData = {
                    supplier_id : supplier_id,
                    from_date : from_date,
                    to_date : to_date,
                    branch_name : branch_name,
                    leaddays : leaddays,
                    aging_days0 : aging_days0,
                    aging_days1 : aging_days1,
                    aging_days2 : aging_days2,
                    aging_days3 : aging_days3,
                    aging_days4 : aging_days4,
                    stock_filter : stock_filter,
                }
                var url = '{{action('Purchase\ReOrderStockController@getAccData')}}';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : "POST",
                    url         :  url,
                    dataType	: 'json',
                    data        : formData,
                    beforeSend: function( xhr ) {
                        $('#data_re_order_stock').addClass('pointerEventsNone');
                    },
                    success: function(response,data) {
                        if(response['status'] == 'success'){
                            var data = response['data']['items'];
                            
                            var len = data.length;
                            var tbody = $('#data_re_order_stock>tbody');
                            var rows = '';
                            var voucher_balance = 0;

                            for(var i=0;i<len;i++){
                                //console.log(data[i]);
                                var td = '';
                                var index = i+1;

                                td += '<td><input readonly type="hidden" data-id="product_id" name="product_id'+index+'" class="product_id" value='+data[i]['product_id']+'>'
                                    +'<input type="checkbox" value="'+data[i]['product_barcode_barcode']+'" id="chk_barcodes'+index+'" class="checkedInv" name="chk_barcodes[]">'+
                                '</td>';
                                td += '<td class="text-center">'+index+'</td>';
                                td += '<td class="text-center">'+data[i]['product_barcode_barcode']+'</td>';
                                td += '<td class="text-left" data-id="pd_product_name">'+data[i]['product_name']+'<input readonly type="hidden" data-id="product_name" name="product_name'+index+'" class="product_name" value='+data[i]['product_name']+'><input readonly type="hidden" data-id="pd_barcode" name="pd_barcode'+index+'" class="pd_barcode" value='+data[i]['product_barcode_barcode']+'><input readonly type="hidden" data-id="product_barcode_id" name="product_barcode_id'+index+'" class="product_barcode_id" value='+data[i]['product_barcode_id']+'></td>';
                                td += '<td class="text-center">'+data[i]['grn_date']+'</td>';
                                td += '<td class="text-center">'+data[i]['tbl_purc_grn_dtl_quantity']+'</td>';
                                td += '<td class="text-right">'+data[i]['aging_grand_total']+'</td>';
                                td += '<td class="text-center">'+data[i]['current_stock']+'</td>';
                                td += '<td class="text-center"><input type="text" id="reorderqty'+index+'" name="reorderqty'+index+'" class="re-order-qty tblGridCal_qty tb_moveIndex" value='+data[i]['re_order_qty']+'></td>';
                                td += '<td class="text-left">'+data[i]['supplier_name']+'</td>';
                                td += '<td class="text-center"><input readonly type="text" class="tblGridCal_rate" style="border:none;" value='+data[i]['tbl_purc_grn_dtl_net_tp']+'></td>';
                        /*10*/  td += '<td class="text-center">'+data[i]['sale_last_pi']+'</td>';
                                td += '<td class="text-center">'+data[i]['sale_days_after_last_pi']+'</td>';
                                td += '<td class="text-center">'+data[i]['last_sale_date']+'</td>';
                                td += '<td class="text-center">'+data[i]['last_audit_date']+'</td>';
                                td += '<td class="text-center"><input readonly type="text" class="tblGridCal_cost_amount" style="border:none;" value='+data[i]['reorder_amount']+'></td>';
                                td += '<td class="text-center">'+data[i]['re_order_suggest']+'</td>';
                                td += '<td class="text-center">'+data[i]['expiry_date']+'</td>';
                                td += '<td class="text-center">'+data[i]['stock_in_from_branch']+'</td>';
                                td += '<td class="text-center">'+data[i]['days_with_stock']+'</td>';
                        /*20*/  td += '<td class="text-center">'+data[i]['stock_status']+'</td>';
                                td += '<td class="text-center">'+data[i]['per_day']+'</td>';
                                td += '<td class="text-center">'+data[i]['qty1']+'</td>';
                                td += '<td class="text-center">'+data[i]['qty2']+'</td>';
                                td += '<td class="text-center">'+data[i]['qty3']+'</td>';
                                td += '<td class="text-center">'+data[i]['qty4']+'</td>';
                                td += '<td class="text-center">'+data[i]['qty5']+'</td>';
                                td += '<td class="text-left">'+data[i]['comp_branch']+'</td>';
                                td += '<td class="text-left">'+data[i]['group_item_parent_name']+'</td>';
                                td += '<td class="text-center">'+data[i]['uom_name']+'</td>';
                                td += '<td class="text-center"></td>';
                                td += '<td class="text-left">'+data[i]['package_name']+'</td>';
                                var tr = '<tr>'+td+'</tr>';
                                rows += tr;
                            }
                            if(len == 0){
                                rows = '<tr><td class="text-center" colspan="33">No Data Found...</td></tr>'
                            }

                            rows += '<tr><td><input value='+index+' type="hidden" id="gridcounter"></td></tr>'

                            tbody.html(rows);
                            date_inputmask();

                        }else{
                            toastr.error(response.message);
                            $('#data_re_order_stock>tbody').html("No Data Found...");
                        }
                        xhrGetDataStatus = true;
                        $('#data_re_order_stock').removeClass('pointerEventsNone');
                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                        xhrGetDataStatus = true;
                        $('#data_re_order_stock').removeClass('pointerEventsNone');
                        $('#data_re_order_stock>tbody').html("No Data Found...");
                    }
                });
            }
        });

        
        var xhrGetDataStatus2 = true;
        $(document).on('click','#remove_data',function(){
            
            var supplier_id = $('#supplier_id').val();
            
            if(valueEmpty(supplier_id)){
                toastr.error("Vendor Name is required");
                validate2 = false;
                return true;
            }

            var text = "Are you want to sure un linked these barcode.";
            if (confirm(text) == true) 
            {
                var validate2 = true;
                
                var barcodes = [];
                $(document).find('input[name="chk_barcodes[]"]:checked').each(function(){
                    var thix = $(this)
                    var obj = {
                        'barcodes' : thix.val(),
                    }
                    barcodes.push(obj);
                })

                if(valueEmpty(barcodes) || barcodes.length == 0){
                    toastr.error("Check minimum one barcode is checked");
                    validate2 = false;
                    return true;
                }


                //if(validate2 && xhrGetDataStatus2){
                    xhrGetDataStatus2 = false;
                    var formData = {
                        supplier_id : supplier_id,
                        barcodes : barcodes,
                    }
                    var url = '{{action('Purchase\ReOrderStockController@delSupData')}}';
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type        : "POST",
                        url         :  url,
                        dataType	: 'json',
                        data        : formData,
                        beforeSend: function( xhr ) {
                            $('.kt-container').addClass('pointerEventsNone');
                        },
                        success: function(response,data) {
                            if(response['status'] == 'success'){
                                window.location.href = '/re-order-stock/form'
                            }else{
                                toastr.error(response.message);
                            }
                            xhrGernerate = true;
                            $('.kt-container').removeClass('pointerEventsNone');
                        },
                        error: function(response,status) {
                            toastr.error(response.responseJSON.message);
                            xhrGernerate = true;
                            $('.kt-container').removeClass('pointerEventsNone');
                        }
                    });
                //}
            }
        });
        


        var xhrGerneratePO = true;
        $(document).on('click','#btn-update-entry',function(){
            var validate = true;
            var barcodes = [];
            $(document).find('input[name="chk_barcodes[]"]:checked').each(function(){
                var thix = $(this)
                var obj = {
                    'barcode' : thix.val(),
                    'qty' : thix.parents('tr').find('.re-order-qty').val(),
                    'supplier_name' : $('#supplier_name').val(),
                    'supplier_id' : $('#supplier_id').val(),
                }
                barcodes.push(obj);
            })

            if(valueEmpty(barcodes) || barcodes.length == 0){
                toastr.error("Check minimum one barcode is checked");
                validate = false;
                return true;
            }
            if(validate && xhrGerneratePO){
                xhrGerneratePO = false;
                var formData = {
                    barcodes : barcodes,
                }
                var url = '{{action('Purchase\ReOrderStockController@generatePurchaseOrder')}}';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : "POST",
                    url         :  url,
                    dataType	: 'json',
                    data        : formData,
                    beforeSend: function( xhr ) {
                        $('.kt-container').addClass('pointerEventsNone');
                    },
                    success: function(response,data) {
                        if(response['status'] == 'success'){
                             window.location.href = '/purchase-order/form'
                        }else{
                            toastr.error(response.message);
                        }
                        xhrGerneratePO = true;
                        $('.kt-container').removeClass('pointerEventsNone');
                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                        xhrGerneratePO = true;
                        $('.kt-container').removeClass('pointerEventsNone');
                    }
                });
            }
        });


        var arrows;
        if (KTUtil.isRTL()) {
            arrows = {
                leftArrow: '<i class="la la-angle-right"></i>',
                rightArrow: '<i class="la la-angle-left"></i>'
            }
        } else {
            arrows = {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        }
        $('.kt_datepicker_bcs, .kt_datepicker_bcs_validate').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows
        });
        function date_inputmask(){
            $(".date_inputmask").inputmask("99-99-9999", {
                "mask": "99-99-9999",
                "placeholder": "dd-mm-yyyy",
                autoUnmask: true
            });
        }
        date_inputmask();
        $(document).on('click','.marked',function(){
            if($(this).is(':checked')){
                var cheque_date = $('.reconciled_date').val();
                $(this).parents('tr').find('.cleared_date_input').val(cheque_date);
            }else{
                $(this).parents('tr').find('.cleared_date_input').val('');
            }
        });


        $(document).on('click','#header_input_clear_data',function(){
            $("#data_re_order_stock>thead input").val("");
            $('#data_re_order_stock>thead input').each(function(){
                var val = $(this).val();
                var index = $(this).parent('th').index();
                var arr = {
                    index : index,
                    val : val
                }
                funFilterDataRow1(arr);
            })
        })
        $(document).on('keyup','#data_re_order_stock>thead input',function(){
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
            table = document.getElementById("data_re_order_stock");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[td_index];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        function convert(str) {
            var date = new Date(str),
            mnth = ("0" + (date.getMonth() + 1)).slice(-2),
            day = ("0" + date.getDate()).slice(-2);
            return [day, mnth, date.getFullYear()].join("-");
        }

        function changeDate()
        {
            var Days ="";
            var StockDataAging;
            var as_on_date = $('#as_on_date').val();
            var row = as_on_date.split("-");
            var day = row[0];
            var month = row[1];
            var year = row[2];
            var NewDate = year+'-'+month+'-'+day;
            var result = new Date(NewDate);

            var array = [];

            for (var i = 0; i < 5; i++)
            {
                pushDays = document.getElementById("Days"+i).value;
                if(pushDays>0){
                    array.push(pushDays);
                }
            }

            console.log(array+' => '+array.length);

            for (k = 0; k < array.length; k++) {
                getDays = document.getElementById("Days"+k).value;
                if(getDays>0){
                    Days = array[array.length-1];
                }
            }
            StockDataAging = result.setDate(result.getDate() - Days);
            document.getElementById("Data_Aging").innerHTML = convert(StockDataAging);
            $('#StockDataAging').val(convert(StockDataAging));
        }
        function CheckAll()
        {
            if(document.getElementById('chkAll').checked == true)
            {
                for (i = 1; i <= document.getElementById('gridcounter').value; i++)
                document.getElementById('chk_barcodes'+i).checked = true ;
            }
            else
            {
                for (i = 1; i <= document.getElementById('gridcounter').value; i++)
                document.getElementById('chk_barcodes'+i).checked = false ;
            }
        }


        $(document).on('keyup','.re-order-qty',function(e){
            var tr = $(this).parents('tr');
            var val = $(this).val();
            val = funcNumberFloat(val);

            var keycodeNo = e.which;

            if(parseFloat(val) != 0){
                if(keycodeNo != 13){
                    tr.find('.checkedInv').prop('checked',true);
                    tr.find('.checkedInv2').prop('checked',true);
                }
            }else{
                tr.find('.checkedInv').prop('checked',false);
                tr.find('.checkedInv2').prop('checked',false);
            }
        });
    </script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <style>
        table#data_re_order_stock input {
            width: 70px !important;
        }
    </style>
    @include('partial_script.table_column_switch')
@endsection

