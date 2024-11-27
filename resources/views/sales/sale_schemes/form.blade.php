@extends('layouts.layout')
@section('title', 'Offer Schemes')

@section('pageCSS')
    <link href="/assets/css/pages/wizard/wizard-1.css" rel="stylesheet" type="text/css" />
    <style>
        .erp-page--actions [type="submit"]{
            display: none;
        }
        .kt-wizard-v1 .kt-wizard-v1__nav .kt-wizard-v1__nav-items .kt-wizard-v1__nav-item[data-ktwizard-state="done"] .kt-wizard-v1__nav-body .kt-wizard-v1__nav-label{
            color: #74788d!important;
        }
    </style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['document_code'];
            $step = 2;
        }
        if($case == 'edit'){
            $code = $data['document_code'];
            $id = $data['current']->scheme_id;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->entry_date))));
            $start_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->start_date))));
            $end_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->end_date))));
            $name = $data['current']->scheme_name;
            $is_active = $data['current']->is_active;
            $remarks = $data['current']->remarks;
            $availProducts = isset($data['current']->schemeAvail) ? $data['current']->schemeAvail : [];
            $schemeSlab = isset($data['current']->schemeSlab) ? $data['current']->schemeSlab : [];
            $schemeSlabDtl = isset($data['current']->schemeSlabDtl) ? $data['current']->schemeSlabDtl : [];
            $step = 2;
            $schemeBranches = isset($data['current']->schemeBranches) ? $data['current']->schemeBranches : [];
        }
        
    @endphp
    @permission($data['permission'])
    <!-- begin:: Content -->
    <form class="kt-form" id="kt_form" method="post" action="{{ action('Sales\SaleSchemesController@store', isset($id)?$id:"") }}">
    @csrf
        <input type="hidden" id="form_type" value="sales_scheme">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="erp-page--title">
                                            {{ $code }}
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
                                            <input type="text" name="scheme_date" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{ isset($date) ? $date : date('d-m-Y') }}" id="kt_datepicker_3" autofocus="" autocomplete="off" aria-invalid="false">
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
                                    <label class="col-lg-6 erp-col-form-label">Scheme Name: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <input type="text" name="scheme_name" value="{{ isset($name) ? $name:'' }}" class="moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Status:</label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @if($case == 'new')
                                                    <input type="checkbox" name="is_active" checked>
                                                    <span></span>
                                                @else
                                                    <input type="checkbox" name="is_active" @if(isset($is_active) && $is_active == 'YES') checked @endif >
                                                    <span></span>
                                                @endif
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Start Date: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                    <div class="input-group date">
                                            <input type="text" name="scheme_start_date" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{ isset($start_date) ? $start_date : date('d-m-Y') }}" id="kt_datepicker_3" autofocus="" autocomplete="off" aria-invalid="false">
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
                                    <label class="col-lg-6 erp-col-form-label">End Date: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            <input type="text" name="scheme_end_date" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{ isset($end_date) ? $end_date : date('d-m-Y') }}" id="kt_datepicker_3" autofocus="" autocomplete="off" aria-invalid="false">
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
                                    <label class="col-lg-6 erp-col-form-label">Default Discount:</label>
                                    <div class="col-lg-6">
                                        <input type="number" name="scheme_default_discount" class="moveIndex form-control erp-form-control-sm" min="0" value="0" id="scheme_default_discount" autofocus="" autocomplete="off" aria-invalid="false">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-2 erp-col-form-label">Remarks:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="scheme_remarks" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{ isset($remarks) ? $remarks : '' }}" id="scheme_remarks" autofocus="" autocomplete="off" aria-invalid="false">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-2 erp-col-form-label">Branches:</label>
                                    <div class="col-lg-10">
                                        <div class="erp-select2 form-group">
                                            <select class="form-control kt-select2 erp-form-control-sm tag-select2" multiple  id="scheme_slab_branches" name="scheme_slab_branches[]">
                                                <option value="0">Select Branches</option>
                                                @if(isset($schemeBranches))
                                                    @php $col = []; @endphp
                                                    @foreach($schemeBranches as $branch)
                                                        @php array_push($col,$branch->branch_id); @endphp
                                                    @endforeach
                                                    @foreach($data['branch'] as $branch)
                                                        <option value="{{$branch->branch_id}}" {{ (in_array($branch->branch_id, $col)) ? 'selected' : '' }}>{{$branch->branch_name}}</option>
                                                    @endforeach
                                                @else
                                                    @foreach($data['branch'] as $branch)
                                                        <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-grid kt-wizard-v1 kt-wizard-v1--white" id="kt_wizard_v1" data-ktwizard-state="step-first">
                            <div class="kt-grid__item">

                                <!--begin: Form Wizard Nav -->
                                <div class="kt-wizard-v1__nav mb-5">
            
                                    <!--doc: Remove "kt-wizard-v3__nav-items--clickable" class and also set 'clickableSteps: false' in the JS init to disable manually clicking step titles -->
                                    <div class="kt-wizard-v1__nav-items kt-wizard-v1__nav-items--clickable">
                                        <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step" data-ktwizard-state="current">
                                            <div class="kt-wizard-v1__nav-body">
                                                <div class="kt-wizard-v1__nav-label">
                                                    <h3 class="wizard-title d-inline">1.</h3>
                                                    <h4 class="d-inline">Direct Discount Items</h4> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
                                            <div class="kt-wizard-v1__nav-body">
                                                <div class="kt-wizard-v1__nav-label">
                                                    <h3 class="wizard-title d-inline">2.</h3>
                                                    <h4 class="d-inline">Slabs & Coupon Offer</h4> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--end: Form Wizard Nav -->
                            </div>
                            <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v1__wrapper d-block">
                                <!--begin: Form Wizard Form-->
                                    <!--begin: Form Wizard Step 1-->
                                        <div class="kt-wizard-v1__content" data-ktwizard-type="step-content" data-ktwizard-state="current">
                                            <div class="kt-form__section kt-form__section--first">
                                                <div class="kt-wizard-v1__form">
                                                    <div class="form-group-block">
                                                        <div class="erp_form___block">
                                                            <div class="table-scroll form_input__block">
                                                                <table class="table erp_form__grid table-resizable dtr-inline product_table">
                                                                    <thead class="erp_form__grid_header">
                                                                        <tr id="erp_form_grid_header_row">
                                                                            <th scope="col" style="width: 3%!important;">
                                                                                <div class="erp_form__grid_th_title">Sr.</div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                                                    <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                                                    <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" style="width: 10.25%;">
                                                                                <div class="erp_form__grid_th_title">
                                                                                    Type
                                                                                </div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <select id="pd_scheme_type" class="pd_scheme_type tb_moveIndex form-control erp-form-control-sm">
                                                                                        <option value="Product">Product</option>
                                                                                        <option value="Product Group">Product Group</option>
                                                                                    </select>
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" style="width: 15.25%;">
                                                                                <div class="erp_form__grid_th_title">
                                                                                    Barcode / Group ID
                                                                                    <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                                                        <i class="la la-barcode"></i>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" style="width: 36.25%;">
                                                                                <div class="erp_form__grid_th_title">Product Name / Group Name</div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" style="width: 10.25%;">
                                                                                <div class="erp_form__grid_th_title">UOM</div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <select id="pd_uom" class="pd_uom tb_moveIndex form-control erp-form-control-sm">
                                                                                        <option value="">Select</option>
                                                                                    </select>
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" style="width: 8.25%;">
                                                                                <div class="erp_form__grid_th_title">Disc %</div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <input id="dis_perc" type="text" class="tblGridCal_discount_perc grid_discount_perc tb_moveIndex sldtl_disc_per avail_disc_perc validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" style="width: 8.25%;">
                                                                                <div class="erp_form__grid_th_title">Disc Amt</div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <input id="dis_amount" type="text" tabindex="-1" class="tblGridCal_discount_amount grid_discount_amount validNumber avail_disc_amount validOnlyFloatNumber form-control erp-form-control-sm">
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col"  style="width: 5.25%;">
                                                                                <div class="erp_form__grid_th_title">FOC Qty</div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <input id="foc_qty" type="text" class="validNumber grid_foc_qty validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" width="48">
                                                                                <div class="erp_form__grid_th_title">Action</div>
                                                                                <div class="erp_form__grid_th_btn">
                                                                                    <button type="button" data-type="product" data-prefix="pd" id="pdAddData" class="addData tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                                                        <i class="la la-plus"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="erp_form__grid_body">
                                                                        @if($case == 'edit')
                                                                            @if(isset($availProducts))
                                                                                @foreach($availProducts as $product)
                                                                                    <tr class="new-row">
                                                                                        <td class="handle">
                                                                                            <i class="fa fa-arrows-alt-v handle"></i>
                                                                                            <input type="text" value="{{ $loop->iteration }}" name="pd[{{ $loop->iteration }}][sr_no]" title="1" class="form-control erp-form-control-sm handle" readonly="" autocomplete="off">
                                                                                            <input type="hidden" name="pd[{{ $loop->iteration }}][product_id]" data-id="product_id" value="{{ $product->product->product_id ?? 0 }}" class="product_id form-control erp-form-control-sm" readonly="" autocomplete="off">
                                                                                            <input type="hidden" name="pd[{{ $loop->iteration }}][product_barcode_id]" data-id="product_barcode_id" value="{{ $product->barcode->product_barcode_id ?? 0 }}" class="product_barcode_id form-control erp-form-control-sm" readonly="" autocomplete="off">
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="erp-select2">
                                                                                                @php $type = ($product->group_item_id == "") ? 'Product' : 'Product Group';  @endphp
                                                                                                <input type="text" name="pd[{{ $loop->iteration }}][pd_scheme_type]" data-id="pd_scheme_type" value="{{ $type }}" title="{{ $type }}" class="form-control erp-form-control-sm pd_scheme_type field_readonly" autocomplete="off">
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" name="pd[{{ $loop->iteration }}][pd_barcode]" data-id="pd_barcode" value="{{ $product->barcode->product_barcode_barcode ?? $product->group_item_id }}" title="{{ $product->barcode->product_barcode_barcode ?? $product->group_item_id }}" class="form-control field_readonly erp-form-control-sm pd_barcode tb_moveIndex open_inline__help" autocomplete="off">
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" name="pd[{{ $loop->iteration }}][product_name]" data-id="product_name" value="{{ $product->product->product_name ?? $product->group_name }}" title="{{ $product->product->product_name ?? $product->group_name }}" class="form-control erp-form-control-sm product_name field_readonly" autocomplete="off">
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="erp-select2">
                                                                                                <select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd[{{ $loop->iteration }}][pd_uom]">
                                                                                                    <option value="{{ $product->barcode->uom->uom_id ?? 0 }}">{{ $product->barcode->uom->uom_name ?? 'Group' }}</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" name="pd[{{$loop->iteration}}][dis_perc]" data-id="dis_perc"  value="{{number_format($product->disc_perc,2)}}" class="tblGridCal_discount_perc tb_moveIndex avail_disc_perc form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" name="pd[{{$loop->iteration}}][dis_amount]" data-id="dis_amount" tabindex="-1"  value="{{number_format($product->disc,3)}}" class="tblGridCal_discount_amount avail_disc_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" name="pd[{{$loop->iteration}}][foc_qty]" data-id="foc_qty"  value="{{ $product->foc_qty }}" class="tb_moveIndex foc_qty form-control erp-form-control-sm validNumber">
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <div class="btn-group btn-group btn-group-sm" role="group">
                                                                                                <button type="button" class="btn btn-danger gridBtn delData">
                                                                                                    <i class="la la-trash"></i>
                                                                                                </button>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            @endif
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <!--end: Form Wizard Step 1-->
                                    <!--begin: Form Wizard Step 2-->
                                        <div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
                                            <div class="kt-form__section kt-form__section--second">
                                                <div class="kt-wizard-v1__form">
                                                    <div class="form-group-block">
                                                        <div class="erp_form___block">
                                                            <div class="form_input__block">
                                                                <div id="kt_repeater_slab">
                                                                    <div class="form-group-block row">
                                                                        @if($case == 'edit')
                                                                            @include('sales.sale_schemes.partials.edit')
                                                                        @else
                                                                            @include('sales.sale_schemes.partials.new')
                                                                        @endif
                                                                    </div>
                                                                    <div class="row mb-5">
                                                                        <div class="col-lg-12 text-right">
                                                                            <div class="btn-group" role="group" aria-label="First group">
                                                                                <button type="button" data-repeater-create="scheme_slab_data" class="btn btn-success btn-sm"><i class="la la-plus"></i></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--begin: Form Actions -->
                                        <div class="kt-form__actions d-flex justify-content-between">
                                            <button class="btn btn-secondary btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-prev">
                                                Previous
                                            </button>
                                            <button class="btn btn-success btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-submit">
                                                @if($case == 'new') Save @else Update @endif
                                            </button>
                                            <button class="btn btn-brand btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-next">
                                                Next Step
                                            </button>
                                        </div>
                                    <!--end: Form Wizard Step 2 -->
                                <!--end: Form Wizard Form-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end: Form Actions -->
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')
    <script>
        var ACTIVE_STEP = '{{ $step }}';
        var PRODUCT_HELP_URL = "{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}"
    </script>
    <script src="{{ asset('js/pages/js/sales_scheme.js') }}" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
@endsection

@section('customJS')
    <script>
        var arr_text_FieldProduct = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'pd_scheme_type',
                'fieldClass':'pd_scheme_type field_readonly',
                'type':'select',
                'convertType' : 'input'
            },
            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode field_readonly tb_moveIndex open_inline__help',
              //  'data-url' : productHelpUrl
            },
            {
                'id':'product_name',
                'fieldClass':'product_name field_readonly',
                'require' : true,
                'message':'Enter Product Detail',
            },
            {
                'id':'pd_uom',
                'fieldClass':'pd_uom field_readonly',
                'type':'select',
            },
            {
                'id':'dis_perc',
                'fieldClass':'tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'dis_amount',
                'fieldClass':'tblGridCal_discount_amount validNumber validOnlyFloatNumber'
            },
            {
                'id':'foc_qty',
                'fieldClass':'tb_moveIndex validNumber foc_qty'
            },
        ];
        var arr_hidden_field_product = ['product_id','product_barcode_id'];

        // var arr_text_FieldSlab = [
        //     // keys = id, fieldClass, readonly(boolean), require(boolean)
        //     {
        //         'id':'sl_slab_name',
        //         'fieldClass':'sl_slab_name',
        //         'type':'text',
        //         'require' : true,
        //         'message':'Enter Slab Name',
        //     },
        //     {
        //         'id':'sl_min_sale',
        //         'fieldClass':'sl_min_sale tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber',
        //         'require' : true,
        //         'message':'Enter Min Sale',
        //       //  'data-url' : productHelpUrl
        //     },
        //     {
        //         'id':'sl_max_sale',
        //         'fieldClass':'sl_max_sale tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber',
        //         'require' : true,
        //         'message':'Enter Max Sale',
        //     },
        //     {
        //         'id':'sl_disc_per',
        //         'fieldClass':'sl_disc_per tb_moveIndex field_readonly validNumber validOnlyFloatNumber form-control erp-form-control-sm',
        //         'type':'text'
        //     },
        //     {
        //         'id':'sl_disc',
        //         'fieldClass':'sl_disc tb_moveIndex validNumber field_readonly validOnlyFloatNumber form-control erp-form-control-sm',
        //         'type':'text'
        //     },
        //     {
        //         'id':'sl_expiry_days',
        //         'fieldClass':'sl_expiry_days tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm',
        //         'type':'text'
        //     },
        //     {
        //         'id':'sl_expiry_date',
        //         'fieldClass':'date_inputmask tb_moveIndex sl-expiry-date'
        //     }
        // ];
        // var arr_hidden_field_slab = [];

        var arr_text_FieldSlabDtl = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'sldtl_type',
                'fieldClass':'sldtl_type field_readonly',
                'type':'select',
                'convertType' : 'input'
            },
            {
                'id':'sldtl_barcode',
                'require' : true,
                'message' : 'Enter Product Detail',
                'fieldClass':'sldtl_barcode tb_moveIndex open_inline__help'
            },
            {
                'id':'sldtl_product_name',
                'fieldClass':'sldtl_product_name',
                'require' : true,
                'message':'Enter Product Detail',
            },
            {
                'id':'sldtl_disc_per',
                'fieldClass':'sldtl_disc_per tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm',
                'type':'text'
            },
            {
                'id':'sldtl_disc',
                'fieldClass':'sldtl_disc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm',
                'type':'text'
            },
            {
                'id':'sldtl_foc_qty',
                'fieldClass':'sldtl_foc_qty tb_moveIndex validNumber form-control erp-form-control-sm',
                'type':'text'
            },
        ];
        var arr_hidden_field_slab_dtl = ['sldtl_product_id','sldtl_product_barcode_id'];

        // Custom JS
        $(document).on('keyup', 
            '.slab_disc,.slab_disc_per',
            function(e){
            var thix = $(this);
            var field = thix.attr('id');
            if(field == 'slab_disc_per'){
                thix.closest('.kt-margin-b-10.slab.p-3.border').find('.slab_disc').val('0');
            }else if( field == 'slab_disc'){
                thix.closest('.kt-margin-b-10.slab.p-3.border').find('.slab_disc_per').val('0');
            }else{
                thix.closest('.kt-margin-b-10.slab.p-3.border').find('.slab_disc').val('0');
                thix.closest('.kt-margin-b-10.slab.p-3.border').find('.slab_disc_per').val('0');
            }
        });
        $(document).on('keyup', 
            '.slab_disc,.slab_disc_per',
            function(e){
            var thix = $(this);
            var field = thix.attr('id');
            if(field == 'slab_disc_per'){
                thix.closest('.kt-margin-b-10.slab.p-3.border').find('.slab_disc').val('0');
            }else if( field == 'slab_disc'){
                thix.closest('.kt-margin-b-10.slab.p-3.border').find('.slab_disc_per').val('0');
            }else{
                thix.closest('.kt-margin-b-10.slab.p-3.border').find('.slab_disc').val('0');
                thix.closest('.kt-margin-b-10.slab.p-3.border').find('.slab_disc_per').val('0');
            }
        });
        $(document).on('keyup', 
            '.avail_disc_perc,.avail_disc_amount',
            function(e){
                var thix = $(this);
                var field = thix.attr('id');
                if(field == 'dis_perc' || thix.data('id') == 'dis_perc'){
                    thix.closest('tr').find('.avail_disc_amount').val('0');
                }else if(field == 'dis_amount' || thix.data('id') == 'dis_amount'){
                    thix.closest('tr').find('.avail_disc_perc').val('0');
                }else{
                    thix.closest('tr').find('.avail_disc_amount').val('0');
                    thix.closest('tr').find('.avail_disc_perc').val('0');
                }
            }
        );
        $(document).on('keyup', 
            '#sldtl_disc_per,#sldtl_disc,[data-id="sldtl_disc_per"],[data-id="sldtl_disc"]',
            function(e){
            var thix = $(this);
            var field = thix.attr('id');
            if(field == 'sldtl_disc_per'){
                thix.parents('.table.erp_form__grid').find('#sldtl_disc').val('0');
            }else if( field == 'sldtl_disc'){
                thix.parents('.table.erp_form__grid').find('#sldtl_disc_per').val('0');
            }else{
                thix.parents('.table.erp_form__grid').find('#sldtl_disc').val('0');
                thix.parents('.table.erp_form__grid').find('#sldtl_disc_per').val('0');
            }
        });
        $(document).on('change' , '#pd_scheme_type' , function(){
            var thix = $(this);
            if(thix.val() == "Product"){
                $("#pd_barcode").attr('data-url' , PRODUCT_HELP_URL);
            }
            if(thix.val() == "Product Group"){
                $("#pd_barcode").attr('data-url' , "{{action('Common\DataTableController@inlineHelpOpen','groupHelp')}}");
            }
        });
    </script>

    <script src="{{ asset('js/pages/js/add-row-repeated_scheme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/pages/crud/forms/widgets/form-repeater.js') }}" type="text/javascript"></script>
@endsection
