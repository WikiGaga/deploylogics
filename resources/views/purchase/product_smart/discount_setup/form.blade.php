@extends('layouts.layout')
@section('title', 'Product Discount Setup')

@section('pageCSS')
    <style>
        .erp_form__grid_body td, .erp_form__grid_header th {
            border: 1px solid #a0b0cc !important;
        }
    </style>

    <link href="/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />

@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $reward_point_display = 'none';
        $reward_point_readonly = '';
        $is_with_member_display = 'none';
        $is_all_member = true;
        $is_with_member = false;
        $is_without_member = false;
        $discount_setup_member_list = [];
        if($case == 'new'){
            $code = $data['discount_setup_code'];
        }
        if($case == 'edit'){
            $id = $data['current']->discount_setup_id;
            $code = $data['current']->discount_setup_code;
            $title = $data['current']->discount_setup_title;
            $start_date =  date('d-m-Y H:i', strtotime(trim(str_replace('/','-',$data['current']->start_date))));
            $end_date =  date('d-m-Y H:i', strtotime(trim(str_replace('/','-',$data['current']->end_date))));
            $branch_id = $data['current']->branch_id;
            $sale_type_id = $data['current']->sale_type;
            $discount = $data['current']->discount_type;
            $promotion = $data['current']->promotion_type;
            if($promotion == 'reward_point'){
                $reward_point_display = '';
                $reward_point_readonly = 'readonly';
            }
            $amount_for_point = $data['current']->amount_for_point;
            $point_quantity = $data['current']->point_quantity;
            $discount_qty = $data['current']->discount_qty;
            $discount_perc = $data['current']->discount_perc;
            $flat_discount_qty = $data['current']->flat_discount_qty;
            $flat_discount_amount = $data['current']->flat_discount_amount;
            $remarks = $data['current']->remarks;
            $slab_base = $data['current']->slab_base;
            $is_active = $data['current']->is_active;
            $is_with_member = ($data['current']->is_with_member == 1)?true:false;
            $is_without_member = ($data['current']->is_without_member == 1)?true:false;
            $is_all_member = false;
            if($is_with_member){
                $is_with_member_display = '';
            }
            if($is_with_member && $is_without_member){
                $is_with_member = false;
                $is_without_member = false;
                $is_all_member = true;
                $is_with_member_display = 'none';
            }

            $dtls = (isset($data['dtl']) && $data['dtl'] != null)?$data['dtl']:"" ;
            $selected_product_list = [];
            $selected_group_item_list = [];
            foreach($dtls as $dtl){
                if($dtl->discount_setup_type == 'group_item'){
                    $selected_group_item_list[] = $dtl->group_item_id;
                }
                if($dtl->discount_setup_type == 'product'){
                    $selected_product_list[] = $dtl;
                }
            }

            $discount_setup_membership = (isset($data['current']->discount_setup_membership) && $data['current']->discount_setup_membership != null )?$data['current']->discount_setup_membership:[];
            $discount_setup_member_list = [];
            foreach($discount_setup_membership as $discount_setup_member){
                $discount_setup_member_list[] = $discount_setup_member->membership_type_id;
            }
            $scheme_branches = (isset($data['current']->scheme_branches) && $data['current']->scheme_branches != null )?$data['current']->scheme_branches:[];
            $selectedBranches = [];
            foreach($scheme_branches as $scheme_branch){
                $selectedBranches[] = $scheme_branch->branch_id;
            }
        }
    @endphp
    @php $id = isset($data['current']->discount_setup_id)?$data['current']->discount_setup_id:'';  @endphp
    <form class="discount_setup_form kt-form" method="post" action="{{ action('Purchase\ProductDiscountController@storeProductDiscountSetup',$id) }}">
        <input type="hidden" value='product_discount_setup' id="form_type">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <!--begin::Form-->
                <div class="kt-portlet__body">
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
                    <div class="row">
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Title: <span class="required">*</span></label>
                            @if($case == 'new')
                                <input type="text" name="discount_setup_title" value="{{ $code }}" class="form-control erp-form-control-sm">
                            @else
                                <input type="text" name="discount_setup_title" value="{{ isset($title) ? $title:'' }}" class="form-control erp-form-control-sm">
                            @endif
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Start Date: <span class="required">*</span></label>
                            <div class="input-group date">
                                <input type="text" class="form-control erp-form-control-sm " value="{{ isset($start_date) ? $start_date:date('d-m-Y H:i') }}" id="start_date" name="start_date">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-clock-o glyphicon-th"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">End Date: <span class="required">*</span></label>
                            <div class="input-group date">
                                <input type="text" class="form-control erp-form-control-sm " value="{{ isset($end_date) ? $end_date:date('d-m-Y H:i') }}" id="end_date" name="end_date">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-clock-o glyphicon-th"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Is Active Status:</label>
                            <div class="kt-checkbox-list">
                                <label class="kt-checkbox kt-checkbox--success">
                                    @if ($case == 'edit')
                                        <input type="checkbox" id="is_active" name="is_active" {{ $is_active==1?'checked':'' }}>
                                    @else
                                        <input type="checkbox" id="is_active" name="is_active">
                                    @endif
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Branch: <span class="required">*</span></label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="branch_id" name="branch_id[]" multiple>
                                    <option value="all" >All</option>
                                    @foreach($data['branch'] as $branch)
                                    @if ($case == 'edit')
                                        @if(in_array($branch->branch_id,$selectedBranches))
                                            <option value="{{$branch->branch_id}}" selected>{{$branch->branch_name}}</option>
                                        @else
                                            <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
                                        @endif
                                    @else
                                        <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Sale Type: <span class="required">*</span></label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="sale_type" name="sale_type">
                                    @foreach($data['sale_type'] as $sale_type)
                                    @if ($case == 'edit')
                                        <option value="{{$sale_type->constants_key}}" {{ ($sale_type_id == $sale_type->constants_key)?'selected':'' }} >{{$sale_type->constants_value}}</option>
                                    @else
                                        <option value="{{$sale_type->constants_key}}">{{$sale_type->constants_value}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Discount Type: <span class="required">*</span></label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="discount_type" name="discount_type">
                                    @foreach($data['discount_type'] as $discount_type)
                                    @if ($case == 'edit')
                                        <option value="{{$discount_type->constants_key}}" {{ ($discount == $discount_type->constants_key)?'selected':'' }} >{{$discount_type->constants_value}}</option>
                                    @else
                                        <option value="{{$discount_type->constants_key}}">{{$discount_type->constants_value}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Promotion Type: <span class="required">*</span></label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="promotion_type" name="promotion_type">
                                    @foreach($data['promotion_type'] as $promotion_type)
                                    @if ($case == 'edit')
                                    <option value="{{$promotion_type->constants_key}}" {{ ($promotion == $promotion_type->constants_key)?'selected':'' }} >{{$promotion_type->constants_value}}</option>
                                    @else
                                    <option value="{{$promotion_type->constants_key}}">{{$promotion_type->constants_value}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="reward_point" class="form-group-block row" style="display:{{$reward_point_display}};">
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Amount for Point:</label>
                            <input type="text" name="amount_for_point" id="amount_for_point" value="{{ isset($amount_for_point)?$amount_for_point:'' }}" class="form-control erp-form-control-sm validNumber validOnlyNumber">
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Point Quantity:</label>
                            <input type="text" name="point_quantity" id="point_quantity" value="{{ isset($point_quantity)?$point_quantity:'' }}" class="form-control erp-form-control-sm validNumber validOnlyNumber">
                        </div>
                    </div>
                    <div id="cash_discount" class="form-group-block row">
                        <div class="col-lg-2">
                            <label class="erp-col-form-label">Discount Qty:</label>
                            <input type="text" name="discount_qty" id="discount_qty" value="{{ isset($discount_qty)?$discount_qty:'' }}" class="form-control erp-form-control-sm validNumber validOnlyNumber  {{$reward_point_readonly}}">
                        </div>
                        <div class="col-lg-2">
                            <label class="erp-col-form-label">Discount %:</label>
                            <input type="text" name="discount_perc" id="discount_perc" value="{{ isset($discount_perc)?$discount_perc:'' }}" class="form-control erp-form-control-sm validNumber validOnlyNumber {{$reward_point_readonly}}">
                        </div>
                        <div class="col-lg-2">
                            <label class="erp-col-form-label">Flat Discount Rate:</label>
                            <input type="text" name="flat_discount_qty" id="flat_discount_qty" value="{{ isset($flat_discount_qty)?$flat_discount_qty:'' }}" class="form-control erp-form-control-sm validNumber validOnlyNumber {{$reward_point_readonly}}">
                        </div>
                        <div class="col-lg-2">
                            <label class="erp-col-form-label">Flat Discount Amount:</label>
                            <input type="text" name="flat_discount_amount" id="flat_discount_amount" value="{{ isset($flat_discount_amount)?$flat_discount_amount:'' }}" class="form-control erp-form-control-sm validNumber validOnlyNumber {{$reward_point_readonly}}">
                        </div>
                        <div class="col-lg-2">
                            <label class="erp-col-form-label">Slab Base:</label>
                            <div class="kt-checkbox-list">
                                <label class="kt-checkbox kt-checkbox--success">
                                    @if ($case == 'edit')
                                        <input type="checkbox" id="slab_base" name="slab_base" {{ $slab_base==1?'checked':'' }}>
                                    @else
                                        <input type="checkbox" id="slab_base" name="slab_base">
                                    @endif
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="category_scheme_type" class="form-group-block row">
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Category Scheme Type: <span class="required">*</span></label>
                            <div class="kt-checkbox-list">
                                <label class="kt-checkbox kt-checkbox--success">
                                    <input type="checkbox" id="is_with_member" name="is_with_member" {{$is_with_member?"checked":""}}> Is With Member
                                    <span></span>
                                </label>
                                <label class="kt-checkbox kt-checkbox--success">
                                    <input type="checkbox" id="is_without_member" name="is_without_member" {{$is_without_member?"checked":""}}> Is Without Member
                                    <span></span>
                                </label>
                                <label class="kt-checkbox kt-checkbox--danger">
                                    <input type="checkbox" id="is_all_member" name="is_all_member" {{$is_all_member?"checked":""}}> All Members
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div id="membership_type_block" style="display: {{$is_with_member_display}}">
                                <label class="erp-col-form-label">Membership Type: <span class="required">*</span></label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="membership_type" name="membership_type[]" multiple>
                                        @foreach($data['membership_type'] as $membership_type)
                                            <option value="{{$membership_type->membership_type_id}}" {{in_array($membership_type->membership_type_id,$discount_setup_member_list)?"selected":""}}>{{$membership_type->membership_type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="erp-col-form-label">Remarks:</label>
                                <textarea name="discount_setup_remarks" class="form-control erp-form-control-sm" cols="4">{!! isset($remarks)?$remarks:'' !!}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-block row mt-4">
                        <ul class="green_nav nav nav-tabs col-lg-12" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active slected_product_ds" data-toggle="tab" href="#slected_product_ds" role="tab">Product</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link selected_group_item" data-toggle="tab" href="#selected_group_item" role="tab">Group Item</a>
                            </li>
                        </ul>
                        <div class="tab-content col-lg-12">
                            <div class="tab-pane active slected_product_ds_content" id="slected_product_ds" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <button type="button" id="getListOfProduct" class="btn btn-sm btn-primary">Product help</button>
                                        <div style="font-size: 9px;color: red;">(Click Here or Press F4)</div>
                                    </div>
                                </div>
                                <div class="form-group-block row">
                                    <div class="col-lg-12">
                                        <div class="erp_form___block">
                                            <div class="table-scroll form_input__block">
                                                <table class="table table_pit_list erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                    <thead class="erp_form__grid_header">
                                                    <tr>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Sr.</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                                <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                                <input id="product_barcode_id" readonly type="hidden"  class="product_barcode_id form-control erp-form-control-sm">
                                                                <input id="uom_id" readonly type="hidden"  class="uom_id form-control erp-form-control-sm">
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
                                                                <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}"   data-url_popup="{{ action('Common\DataTableController@helpOpen', 'productHelp') }}">
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
                                                                <select id="pd_uom" class="pd_uom tb_moveIndex form-control erp-form-control-sm">
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
                                                            <div class="erp_form__grid_th_title">Current TP</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input readonly id="current_tp" type="text" class="current_tp validNumber validOnlyNumber form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">MRP</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input readonly id="mrp" type="text" class="mrp validNumber validOnlyNumber form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Sale Rate</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input readonly id="sale_rate" type="text" class="sale_rate validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">GP Rate</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input readonly id="gp_rate" type="text" class="gp_rate fc_rate validNumber form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">GP %</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input readonly id="gp_perc" type="text" class="gp_perc validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Disc Amt</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input readonly id="disc_amt" type="text" class="disc_amt validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Disc Price</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input readonly id="disc_price" type="text" class="disc_price validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">After Disc.GP Amount</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input readonly id="after_disc_gp_amt" type="text"class="after_disc_gp_amt validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">After Disc.GP %</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input readonly id="after_disc_gp_perc" type="text" class="after_disc_gp_perc validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col">
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
                                                         @if(isset($selected_product_list))
                                                            @foreach($selected_product_list as $dtl)
                                                                @php
                                                                    $i = $loop->iteration;
                                                                @endphp
                                                                <tr>
                                                                    <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                                        <input type="text" value="{{$i}}" name="pd[{{$i}}][sr_no]"  class="sr_count form-control erp-form-control-sm handle" readonly>
                                                                        <input type="hidden" name="pd[{{$i}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                                        <input type="hidden" name="pd[{{$i}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                                        <input type="hidden" name="pd[{{$i}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                                    </td>
                                                                    <td><input type="text" data-id="pd_barcode" name="pd[{{$i}}][pd_barcode]" value="{{isset($dtl->product_barcode_barcode)?$dtl->product_barcode_barcode:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                                    <td><input type="text" data-id="product_name" name="pd[{{$i}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                                                    <td>
                                                                        <select class="pd_uom form-control erp-form-control-sm field_readonly " data-id="pd_uom" name="pd[{{$i}}][pd_uom]">
                                                                            <option value="{{isset($dtl->uom_id)?$dtl->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" data-id="pd_packing" name="pd[{{$i}}][pd_packing]" value="{{isset($dtl->packing)?$dtl->packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <input readonly type="text" name="pd[{{ $i }}][current_tp]" data-id="current_tp" value="{{ isset($dtl->cost_rate)?$dtl->cost_rate:'' }}" class="current_tp validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly type="text" name="pd[{{$i}}][mrp]" data-id="mrp"  value="{{isset($dtl->mrp)?$dtl->mrp:''}}" class="mrp form-control erp-form-control-sm validNumber" >
                                                                    </td>
                                                                    <td>
                                                                        <input readonly type="text" name="pd[{{$i}}][sale_rate]" data-id="sale_rate"  value="{{isset($dtl->sale_rate)?$dtl->sale_rate:''}}" class="sale_rate form-control erp-form-control-sm validNumber">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly type="text" name="pd[{{$i}}][gp_rate]" data-id="gp_rate"  value="{{isset($dtl->gp_amount)?$dtl->gp_amount:''}}" class="gp_rate form-control erp-form-control-sm validNumber">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly type="text" name="pd[{{$i}}][gp_perc]" data-id="gp_perc"  value="{{isset($dtl->gp_perc)?$dtl->gp_perc:''}}" class="gp_perc form-control erp-form-control-sm validNumber">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly type="text" name="pd[{{$i}}][disc_amt]" data-id="disc_amt"  value="{{isset($dtl->disc_amount)?$dtl->disc_amount:''}}" class="disc_amt form-control erp-form-control-sm validNumber">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly type="text" name="pd[{{$i}}][disc_price]" data-id="disc_price"  value="{{isset($dtl->disc_perc)?$dtl->disc_perc:''}}" class="disc_price form-control erp-form-control-sm validNumber">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly type="text" name="pd[{{$i}}][after_disc_gp_amt]" data-id="after_disc_gp_amt"  value="{{isset($dtl->after_disc_gp_amount)?$dtl->after_disc_gp_amount:''}}" class="after_disc_gp_amt form-control erp-form-control-sm validNumber">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly type="text" name="pd[{{$i}}][after_disc_gp_perc]" data-id="after_disc_gp_perc"  value="{{isset($dtl->after_disc_gp_perc)?$dtl->after_disc_gp_perc:''}}" class="after_disc_gp_perc form-control erp-form-control-sm validNumber">
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="btn-group btn-group btn-group-sm" role="group">
                                                                            <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane selected_group_item_content" id="selected_group_item" role="tabpanel">
                                <style>
                                    span.first_level_name {
                                        margin-left: 35px;
                                        cursor: pointer;
                                    }
                                    .checkbox_block:hover {
                                        background: #f0f8ff;
                                    }
                                    .checkbox_block_selected{
                                        background: #dcdcdc;
                                    }
                                    span.selected_count {
                                        font-weight: 800;
                                    }
                                </style>
                                <div class="row" id="group_item_container">
                                    <div class="col-lg-6">
                                        <div id="first_level_block" class="kt-checkbox-list">
                                        @foreach($data['group_item'] as $group_item)
                                            <div class="checkbox_block">
                                                <label class="kt-checkbox kt-checkbox--success first_level_checkbox">
                                                    <input type="checkbox" class="first_level_group" id="{{$group_item->group_item_id}}" autocomplete="off">
                                                    <span></span>
                                                </label>
                                                <span class="first_level_name">{{$group_item->group_item_name}} (<span class="selected_count">0</span> / {{count($group_item->last_level)}})</span>
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="child-checkbox-list">
                                            @foreach($data['group_item'] as $child_group_items)
                                                <div class="kt-checkbox-list" id="{{$child_group_items->group_item_id}}" style="display:none;">
                                                @foreach($child_group_items->last_level as $child_group_item)
                                                    <label class="kt-checkbox kt-checkbox--success last_level_checkbox">
                                                        @if($case == 'edit')
                                                        <input type="checkbox" class="last_group_item_id" id="{{$child_group_item->group_item_id}}" value="{{$child_group_item->group_item_id}}" name="group_item_id[]" {{in_array($child_group_item->group_item_id,$selected_group_item_list)?"checked":""}}> {{$child_group_item->group_item_name}}
                                                        <span></span>
                                                        @else
                                                            <input type="checkbox" class="last_group_item_id" id="{{$child_group_item->group_item_id}}" value="{{$child_group_item->group_item_id}}" name="group_item_id[]"> {{$child_group_item->group_item_name}}
                                                            <span></span>
                                                        @endif
                                                    </label>
                                                @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Form-->
            </div>
        </div>

    </form>
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script>

        $(document).on('click','.first_level_name',function(){
            var thix = $(this);
            var checkbox_block = thix.parents('.checkbox_block');
            var data_id = checkbox_block.find('input').attr('id');
            var group_item_container = thix.parents('#group_item_container');
            if(!checkbox_block.hasClass('checkbox_block_selected')){
                group_item_container.find('#first_level_block').find('.checkbox_block').removeClass('checkbox_block_selected');
                group_item_container.find('#child-checkbox-list').find('.kt-checkbox-list').hide();
                checkbox_block.addClass('checkbox_block_selected');
                var child_list = group_item_container.find('#child-checkbox-list').find('#'+data_id);
                child_list.show();
            }else{
                group_item_container.find('#first_level_block').find('.checkbox_block').removeClass('checkbox_block_selected');
                group_item_container.find('#child-checkbox-list').find('.kt-checkbox-list').hide();
            }
        });
        $(document).on('click','.first_level_group',function(){
            var thix = $(this);
            var data_id = thix.attr('id');
            var checkbox_block = thix.parents('.checkbox_block');
            var group_item_container = thix.parents('#group_item_container');
            var child_list = group_item_container.find('#child-checkbox-list').find('#'+data_id)
            if(thix.prop('checked')) {
                child_list.find('input').prop('checked',true);
                var length = child_list.find('input').length;
                checkbox_block.find('.selected_count').html(length);
            } else {
                child_list.find('input').prop('checked',false);
                checkbox_block.find('.selected_count').html(0);
            }

        });

        $(document).on('click','.last_group_item_id',function(){
            var thix = $(this);
            var kt_checkbox_list = thix.parents('.kt-checkbox-list');
            var data_id = kt_checkbox_list.attr('id');
            var total_selected_count = 0;
            var total_length = kt_checkbox_list.find('input').length;
            kt_checkbox_list.find('input').each(function(){
                if($(this).prop('checked')){
                    total_selected_count += 1;
                }
            })
            var group_item_container = thix.parents('#group_item_container');
            var parent_list = group_item_container.find('#first_level_block').find('#'+data_id);
            var checkbox_block = parent_list.parents('.checkbox_block');
            if(total_length == total_selected_count) {
                parent_list.prop('checked',true);
                checkbox_block.find('.selected_count').html(total_selected_count);
            } else {
                parent_list.prop('checked',false);
                checkbox_block.find('.selected_count').html(total_selected_count);
            }
        });
        function funcFoundSelectedCheckedItems(){
            $('#child-checkbox-list>.kt-checkbox-list').each(function(){
                var thix = $(this);
                var data_id = thix.attr('id');
                var total_input = 0;
                var selected_input = 0;
                thix.find('.last_level_checkbox').each(function(){
                    total_input += 1;
                    if($(this).find('input.last_group_item_id').prop('checked')){
                        selected_input += 1;
                    }
                })
                var checkbox_block = $('#first_level_block').find('#'+data_id).parents('.checkbox_block');
                checkbox_block.find('.selected_count').html(selected_input);
                if(total_input == selected_input){
                    $('#first_level_block').find('#'+data_id).prop('checked','true');
                }
            })
        }
        funcFoundSelectedCheckedItems();
        var KTFormWidgets = function () {
            // Private functions
            var validator;
            var formId = $( ".discount_setup_form" )
            $.validator.addMethod("valueNotEquals", function(value, element, arg){
                return arg !== value;
            }, "This field is required");
            var initValidation = function () {
                validator = formId.validate({
                    // define validation rules
                    //  debug: true,
                    rules: {

                    },

                    //display error alert on form submit
                    invalidHandler: function(event, validator) {
                        var alert = $('#kt_form_1_msg');
                        alert.removeClass('kt--hide').show();
                        KTUtil.scrollTo('m_form_1_msg', -200);
                    },
                    beforeSend: function(form) {

                    },
                    submitHandler: function (form) {
                        $("form").find(":submit").prop('disabled', true);
                        //form[0].submit(); // submit the form
                        var formData = new FormData(form);
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url         : form.action,
                            type        : form.method,
                            dataType	: 'json',
                            data        : formData,
                            cache       : false,
                            contentType : false,
                            processData : false,
                            success: function(response,status) {
                                if(response.status == 'success'){
                                    toastr.success(response.message);
                                    setTimeout(function () {
                                        $("form").find(":submit").prop('disabled', false);
                                    }, 2000);
                                    if(response.data.form == 'new'){
                                        window.location.href = response.data.redirect;
                                    }else{
                                        $('.new-row').removeClass('new-row');
                                    }
                                }else{
                                    toastr.error(response.message);
                                    setTimeout(function () {
                                        $("form").find(":submit").prop('disabled', false);
                                    }, 2000);
                                }
                            },
                            error: function(response,status) {
                                // console.log(response.responseJSON);
                                toastr.error(response.responseJSON.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                            },
                        });
                    }
                });
            }

            return {
                // public functions
                init: function() {
                    initValidation();
                }
            };
        }();

        jQuery(document).ready(function() {
            KTFormWidgets.init();
        });

        $(document).on('change','#promotion_type',function(){
            var val = $(this).find('option:selected').val();
            if(val == 'reward_point'){
                $('#reward_point').show();
                $('#cash_discount').find('input').addClass('readonly');
            }else{
                $('#reward_point').hide();
                $('#cash_discount').find('input').removeClass('readonly');
            }
        })
        $(document).on('change','#is_with_member',function(){
            var val = $(this).prop('checked');
            if(val) {
                $('#membership_type_block').show();
                $('#is_all_member').prop('checked',false)
            } else {
                $('#membership_type_block').hide();
            }
        })
        $(document).on('change','#is_without_member',function(){
            var val = $(this).prop('checked');
            if(val) {
                $('#is_all_member').prop('checked',false)
            }
        })
        $(document).on('change','#is_all_member',function(){
            var val = $(this).prop('checked');
            if(val) {
                $('#is_with_member').prop('checked',false)
                $('#is_without_member').prop('checked',false)
                $('#membership_type_block').hide();
            }
        })
    </script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode open_inline__help',
                'message':'Enter Barcode',
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
                'id': 'current_tp',
                'fieldClass': 'current_tp validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'mrp',
                'fieldClass': 'mrp validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'sale_rate',
                'fieldClass': 'sale_rate validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'gp_rate',
                'fieldClass': 'gp_rate validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'gp_perc',
                'fieldClass': 'gp_perc validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'disc_amt',
                'fieldClass': 'disc_amt validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'disc_price',
                'fieldClass': 'disc_price validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'after_disc_gp_amt',
                'fieldClass': 'after_disc_gp_amt validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'after_disc_gp_perc',
                'fieldClass': 'after_disc_gp_perc validNumber validOnlyFloatNumber',
                'readonly':true
            },
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
        var remain_req = 0; // variable use start from in funcAddSelectedProductToFormGrid()
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script>
        var emptyArr = ["",undefined,'NaN',NaN,null,"0",0];
        $(document).on('keyup blur','#discount_perc',function(){
            var val = $(this).val();
            if(!emptyArr.includes(val)){
                $(document).find('#flat_discount_qty').val("")
                $(document).find('#flat_discount_amount').val("")
            }
        })
        $(document).on('keyup blur','#flat_discount_qty',function(){
            var val = $(this).val();
            if(!emptyArr.includes(val)){
                $(document).find('#flat_discount_amount').val("")
                $(document).find('#discount_perc').val("")
            }
        })
        $(document).on('keyup blur','#flat_discount_amount',function(){
            var val = $(this).val();
            if(!emptyArr.includes(val)){
                $(document).find('#flat_discount_qty').val("")
                $(document).find('#discount_perc').val("")
            }
        })
        var dateToday = new Date();
        $('#start_date, #start_date_validate').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'dd-mm-yyyy hh:ii',
            todayBtn: true,
            minDate: new Date()
        });
        $('#end_date, #end_date_validate').datetimepicker({
            format: 'dd-mm-yyyy hh:ii',
            minDate: 0,
            todayHighlight: true,
            autoclose: true,
            todayBtn: true,
        });

        $(document).on('keyup','#discount_perc',function(){
            var discount_perc = $(this).val();
            if(emptyArr.includes(discount_perc)){
                discount_perc = 0;
                $('.erp_form__grid_body > tr').each(function(){
                    var tr = $(this);

                    var disc_amt = 0;
                    tr.find('.disc_amt').val(funcNumberFloat(disc_amt));

                    var disc_price = 0;
                    tr.find('.disc_price').val(funcNumberFloat(disc_price));

                    var after_disc_gp_amt = 0;
                    tr.find('.after_disc_gp_amt').val(funcNumberFloat(after_disc_gp_amt));

                    var after_disc_gp_perc = 0;
                    tr.find('.after_disc_gp_perc').val(funcNumberFloat(after_disc_gp_perc));
                })
            }else{
                $('.erp_form__grid_body > tr').each(function(){
                    var tr = $(this);

                    var current_tp = tr.find('.current_tp').val();
                    var sale_rate = tr.find('.sale_rate').val();
                    var gp_rate = tr.find('.gp_rate').val();

                    var disc_amt = parseFloat(sale_rate) / 100 * parseFloat(discount_perc);
                    tr.find('.disc_amt').val(funcNumberFloat(disc_amt));

                    var disc_price = parseFloat(sale_rate) - parseFloat(disc_amt);
                    tr.find('.disc_price').val(funcNumberFloat(disc_price));

                    var after_disc_gp_amt = parseFloat(gp_rate) - parseFloat(disc_amt);
                    tr.find('.after_disc_gp_amt').val(funcNumberFloat(after_disc_gp_amt));

                    var after_disc_gp_perc = parseFloat(gp_rate) / parseFloat(current_tp) * 100;
                    if(valueEmpty(after_disc_gp_perc)){
                        after_disc_gp_perc = 0;
                    }
                    tr.find('.after_disc_gp_perc').val(funcNumberFloat(after_disc_gp_perc));

                })
            }
        })
        function funcAfterAddRow(){}
    </script>


    <script>
        var form_modal_type = 'product_discount_setup';
    </script>
    @include('purchase.product_smart.product_modal_help.script')
    <script>
        function funcAddSelectedProductToFormGrid(tr){
            var cloneTr = tr.clone();
            var data_product_barcode = $(cloneTr).attr('data-product_barcode');
            var addProd = true;
            $('table.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
                var thix = $(this);
                var pd_barcode = thix.find('input[data-id="pd_barcode"]').val();
                if(pd_barcode == data_product_barcode){
                 //   toastr.error("Product already added");
                    addProd = false;
                }
            })
            if(addProd){
                remain_req += 1;
                cd("remain_req1: " + remain_req);
                $('table.table_pit_list>thead.erp_form__grid_header>tr').find('#pd_barcode').val(data_product_barcode);
                var trTh = $('table.table_pit_list>thead.erp_form__grid_header>tr').find('#pd_barcode').parents('tr');
                var formData = {
                    form_type : form_modal_type,
                    val : data_product_barcode,
                    autoClick : true
                }
                get_barcode_detail(13, trTh, form_modal_type, formData);
            }
        }

        function funSetProductCustomFilter(arr){
            var len = arr['len'];
            var product = arr['product'];
            for (var i =0;i<len;i++){
                var row = product[i];
                var newTr = "<tr  data-product_barcode='"+row['product_barcode_barcode']+"'>";
                newTr += "<td>"+(!valueEmpty(row['product_barcode_barcode'])?row['product_barcode_barcode']:"")+"</td>";
                newTr += "<td>"+(!valueEmpty(row['product_name'])?row['product_name']:"")+"</td>";
                newTr += "<td>"+(!valueEmpty(row['uom_name'])?row['uom_name']:"")+"</td>";
                newTr += "<td>"+(!valueEmpty(row['product_barcode_packing'])?row['product_barcode_packing']:"")+"</td>";
                newTr += "<td class='text-right'>"+(!valueEmpty(row['net_tp'])?parseFloat(row['net_tp']).toFixed(3):"")+"</td>";
                newTr += "<td class='text-right'>"+(!valueEmpty(row['sale_rate'])?parseFloat(row['sale_rate']).toFixed(3):"")+"</td>";
                newTr += '<td class="text-center">\n' +
                    '     <div style="position: relative;top: -5px;">\n' +
                    '       <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">\n' +
                    '           <input type="checkbox" class="addCheckedProduct" data-id="add_prod">\n' +
                    '               <span></span>\n' +
                    '        </label>\n' +
                    '     </div></td>';
                newTr += "</tr>";

                $('table.table_pitModal').find('tbody.erp_form__grid_body').append(newTr);
            }
        }

        function funcSrReInit(){
            var sr_no = 1;
            $('.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
                $(this).find('td:first-child').html(sr_no);
                var allInput = $(this).find('input');
                var len = allInput.length
                for(v=0;v<len;v++){
                    var dataId = $(allInput[v]).attr('data-id');
                    var newNameVal = "pd["+sr_no+"]["+dataId+"]"
                    $(allInput[v]).attr('name',newNameVal);
                }
                sr_no = sr_no + 1;
            });
        }
        $(document).on('click','.addCheckedProductAll',function(){
            if($(this).prop('checked')) {
                $('table.table_pitModal>tbody>tr').each(function(){
                    var thix = $(this);
                    thix.find('.addCheckedProduct').prop('checked',true)
                    funcAddSelectedProductToFormGrid(thix);
                })
            }
        });
    </script>
@endsection

