@extends('layouts.layout')
@section('title', 'Stock Transfer')

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
            }
            if($case == 'edit'){
                $id = $data['current']->stock_id;
                $code = $data['current']->stock_code;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->stock_date))));
                $store_id = $data['current']->stock_store_from_id;
                $store_to = $data['current']->stock_store_to_id;
                $branch_id = $data['current']->stock_branch_from_id;
                $branch_to = $data['current']->stock_branch_to_id;
                $remarks = $data['current']->stock_remarks;
                $stock_from_id = $data['current']->stock_request_id;
                $selected_rate_type = $data['current']->stock_rate_type;
                $rate_perc = $data['current']->stock_rate_perc;
                $sales_type = $data['current']->sales_sales_type;
                $grn_from_code = $data['current']->ref_grn_code ?? '';
                $grn_from_id = $data['current']->ref_grn_id ?? '';
                //dd($stock_from_id);
                if(isset($stock_from_id)){
                    $demand = \App\Models\TblPurcDemand::where('demand_id',(int)$stock_from_id)->first();
                    $stock_from_code = $demand->demand_no;
                }
                $dtls = isset($data['current']->stock_dtls)? $data['current']->stock_dtls :[];
            }
            $type =$data['form_type'];
            $form_type = $data['stock_code_type'];
    @endphp
    @permission($data['permission'])
    <form id="stock_transfer_form" class="stock_form kt-form" method="post" action="{{ action('Inventory\StockController@store', [$type,$id]) }}">
    @csrf
    <input type="hidden" name="stock_code_type" value='{{$data['stock_code_type']}}' id="form_type">
    <input type="hidden" name="stock_menu_id" value='{{$data['stock_menu_id']}}'>
    <input type="hidden" name="branch" value="{{auth()->user()->branch_id}}">
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row form-group-block">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    {{isset($code)?$code:""}}
                                </div>
                            </div>
                            @if($case == 'edit')
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        @if($data['current']->stock_receive_status == 1)
                                             <span style="background: #ff5722;color: #fff;padding: 0px 30px;">This stock have been received to other branch</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
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
                            <label class="col-lg-6 erp-col-form-label text-center">To Transfer Branch:</label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2" name="branch_to">
                                        <option value="0">Select</option>
                                        @php $transfer_to = isset($branch_to)?$branch_to:'' @endphp
                                        @foreach($data['branch'] as $branch)
                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id == $transfer_to?'selected':''}}>{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Store From:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2" name="store">
                                        @php $storeid = isset($store_id)?$store_id:'' @endphp
                                        @foreach($data['store'] as $store)
                                            <option value="{{$store->store_id}}" {{$store->store_id == $storeid?'selected':''}}>{{$store->store_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Sales Type:</label>
                            <div class="col-lg-6">
                                <div class="erp-select2 form-group">
                                    <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sales_sales_type" name="sales_sales_type">
                                        @if($case == 'edit')
                                            @php $select_type = isset($sales_type)?$sales_type:""; @endphp
                                            @foreach($data['payment_type'] as $payment_type)
                                                <option value="{{$payment_type->payment_type_id}}" {{$select_type == $payment_type->payment_type_id?"selected":""}}>{{$payment_type->payment_type_name}}</option>
                                            @endforeach
                                        @else
                                            @foreach($data['payment_type'] as $payment_type)
                                                <option value="{{$payment_type->payment_type_id}}" {{ $payment_type->payment_type_id == 2?"selected":""}}>{{$payment_type->payment_type_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <label class="col-lg-3 erp-col-form-label text-center">Rate Type </label>
                            <div class="col-lg-9">
                                <div class="input-group erp-select2-sm">
                                    <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rate_type" name="rate_type">
                                        <option value="0">Select</option>
                                        @if($case == 'edit')
                                            @php $selected_rate_type = isset($selected_rate_type)?$selected_rate_type:''; @endphp
                                            @foreach($data['rate_types'] as $key=>$rate_type)
                                                <option value="{{$key}}" {{$selected_rate_type==$key?"selected":""}}>{{$rate_type}}</option>
                                            @endforeach
                                        @else
                                            @foreach($data['rate_types'] as $key=>$rate_type)
                                                <option value="{{$key}}" {{$key=='item_last_net_tp'?"selected":""}}>{{$rate_type}}</option>
                                            @endforeach
                                        @endif
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--<div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Stock Request From:</label>
                            <div class="col-lg-8">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                        </div>
                                        <input type="text" value="{{isset($stock_from_code)?$stock_from_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','stockRequestHelp')}}" id="stock_from_code" name="stock_from_code" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="hidden" id="stock_from_id" name="stock_from_id" value="{{isset($stock_from_id)?$stock_from_id:''}}"/>
                                        <div class="input-group-append">
                                                    <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                    <i class="la la-search"></i>
                                                    </span>
                                            <span class="input-group-text group-input-btn" id="getStockRequestData">
                                                    GO
                                                    </span>
                                            <a href="javascript:;" class="input-group-text group-input-btn" id="requestLink">
                                                <i class="la la-print"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-2 erp-col-form-label">GRN No:</label>
                            <div class="col-lg-10">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                        </div>
                                        <input type="text" value="{{isset($grn_from_code)?$grn_from_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','grnHelp')}}" id="ref_grn_code" name="grn_code" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter GRN">
                                        <input type="hidden" id="ref_grn_id" name="grn_id" value="{{isset($grn_from_id)?$grn_from_id:''}}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text group-input-btn" id="getGRNRequestData">
                                                GO
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="row form-group-block">
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">GRN No:</label>
                            <div class="col-lg-8">
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
                                            <input type="text" value="{{isset($grn_from_code)?$grn_from_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','grnHelp')}}" id="ref_grn_code" name="grn_code" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        @else
                                            <input type="text" value="{{isset($grn_from_code)?$grn_from_code:''}}" id="ref_grn_code" name="grn_code" class="readonly form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        @endif
                                        <input type="hidden" id="ref_grn_id" name="grn_id" value="{{isset($grn_from_id)?$grn_from_id:''}}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                <i class="la la-search"></i>
                                            </span>
                                            @if($case == 'new')
                                                <span class="input-group-text group-input-btn" id="getGRNRequestData">
                                                    GO
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2"></div>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <div class="input-group-prepend"><button type="button" class="btn btn-sm btn-label-danger btn-bold" id="tb_product_detail" style="padding: 0 15px;font-weight: 500;">Stock</button></div>
                            <input type="text" class="form-control erp-form-control-sm" value="0" id="current_product_stock" readonly style="font-size: 18px;background: rgba(253, 57, 122, 0.1);color: #fd397a;font-weight: 500;text-align: center;">
                            <div class="input-group-append"><button type="button" class="btn btn-sm btn-label-success btn-bold" id="tb_analysis_detail" style="padding: 0 15px;">TP Analysis</button></div>
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
                                    $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Sys Qty','Demand Qty','Qty',
                                    'Sale Rate','MRP','Net TP','Adj Rate','Purc Rate','Amount'];
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
                            {{--<div class="" style="display: inline-block">
                                <button type="button" style="width: 30px;height: 30px;" title="Barcode Print" data-toggle="tooltip" class="btn btn-brand btn-elevate btn-circle btn-icon" id="generatePriceTags">
                                    <i class="la la-barcode"></i>
                                </button>
                            </div>--}}
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
                                           
                                            <input id="grn_qty" readonly type="hidden" class="tblGridCal_grn_qty form-control erp-form-control-sm">
                                            <input id="dis_perc" readonly type="hidden" class="tblGridCal_discount_perc form-control erp-form-control-sm">
                                            <input id="dis_amount" readonly type="hidden" class="tblGridCal_discount_amount form-control erp-form-control-sm">
                                            <input id="after_dis_amount" readonly type="hidden" class="tblGridCal_after_discount_amount form-control erp-form-control-sm">
                                            <input id="gst_perc" readonly type="hidden" class="gst_perc form-control erp-form-control-sm">
                                            <input id="gst_amount" readonly type="hidden" class="gst_amount form-control erp-form-control-sm">
                                            <input id="fed_perc" readonly type="hidden" class="tblGridCal_fed_perc form-control erp-form-control-sm">
                                            <input id="fed_amount" readonly type="hidden" class="tblGridCal_fed_amount form-control erp-form-control-sm">
                                            <input id="spec_disc_perc" readonly type="hidden" class="tblGridCal_spec_disc_perc form-control erp-form-control-sm">
                                            <input id="spec_disc_amount" readonly type="hidden" class="tblGridCal_spec_disc_amount form-control erp-form-control-sm">
                                            <input id="gross_amount" readonly type="hidden" class="tblGridCal_gross_amount form-control erp-form-control-sm">
                                            <input id="net_amount" readonly type="hidden" class="tblGridCal_net_amount form-control erp-form-control-sm">
                                            <input id="unit_price" readonly type="hidden" class="tblGridCal_unit_price form-control erp-form-control-sm">
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
                                        <div class="erp_form__grid_th_title">Sys Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="sys_qty" readonly type="text" class="tblGridCal_sys_qty validNumber validOnlyNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Demand Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="demand_qty" readonly type="text" class="demand_qty tb_moveIndex validNumber validOnlyNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sale Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="rate" data-id="rate" readonly type="text" class="tblGridCal_rate validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">MRP</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="mrp" data-id="mrp" type="text" class="mrp validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Net TP</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="ex_net_tp" data-id="ex_net_tp" type="text" class="tblGridCal_ex_net_tp validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Adj Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="adjrate" data-id="adjrate" type="text" class="tblGridCal_adjrate validNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Purc Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="purc_rate" type="text" class="tblGridCal_purc_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Amount</div>
                                        <div class="erp_form__grid_th_input">
                                            <input readonly id="amount" type="text" class="tblGridCal_amount stock_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
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
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                
                                                <input type="hidden" name="pd[{{$loop->iteration}}][grn_qty]" data-id="grn_qty" value="{{isset($dtl->grn_qty)?$dtl->grn_qty:""}}" class="tblGridCal_grn_qty form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][dis_perc]" data-id="dis_perc" value="{{isset($dtl->grn_disc_per)?$dtl->grn_disc_per:""}}" class="tblGridCal_discount_perc form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][dis_amount]" data-id="dis_amount" value="{{isset($dtl->grn_disc_amount)?$dtl->grn_disc_amount:""}}" class="tblGridCal_discount_amount _barcode_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][after_dis_amount]" data-id="after_dis_amount" value="{{isset($dtl->grn_after_disc_amount)?$dtl->grn_after_disc_amount:""}}" class="tblGridCal_after_discount_amount form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][gst_perc]" data-id="gst_perc" value="{{isset($dtl->grn_gst_per)?$dtl->grn_gst_per:""}}" class="gst_perc form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][gst_amount]" data-id="gst_amount" value="{{isset($dtl->grn_gst_amount)?$dtl->grn_gst_amount:""}}" class="gst_amount form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][fed_perc]" data-id="fed_perc" value="{{isset($dtl->grn_fed_per)?$dtl->grn_fed_per:""}}" class="tblGridCal_fed_perc form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][fed_amount]" data-id="fed_amount" value="{{isset($dtl->grn_fed_amount)?$dtl->grn_fed_amount:""}}" class="tblGridCal_fed_amount form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][spec_disc_perc]" data-id="spec_disc_perc" value="{{isset($dtl->grn_spec_disc_per)?$dtl->grn_spec_disc_per:""}}" class="tblGridCal_spec_disc_perc form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][spec_disc_amount]" data-id="spec_disc_amount" value="{{isset($dtl->grn_spec_disc_amount)?$dtl->grn_spec_disc_amount:""}}" class="tblGridCal_spec_disc_amount form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount" value="{{isset($dtl->grn_gross_amount)?$dtl->grn_gross_amount:""}}" class="tblGridCal_gross_amount form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][net_amount]" data-id="net_amount" value="{{isset($dtl->grm_net_amount)?$dtl->grm_net_amount:""}}" class="tblGridCal_net_amount form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][unit_price]" data-id="unit_price" value="{{isset($dtl->grn_rate)?$dtl->grn_rate:""}}" class="tblGridCal_unit_price form-control erp-form-control-sm handle" readonly>
                                            </td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                            <td>
                                                <select class="pd_uom field_readonly form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][uom]">
                                                    <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" data-id="pd_packing" name="pd[{{$loop->iteration}}][pd_packing]" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="sys_qty" name="pd[{{$loop->iteration}}][sys_qty]" value="{{$dtl->stock_dtl_sys_quantity}}" class="tblGridCal_sys_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                            <td><input type="text" data-id="demand_qty" name="pd[{{$loop->iteration}}][demand_qty]" value="{{$dtl->stock_dtl_demand_quantity}}" class="demand_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                            <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->stock_dtl_quantity}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" readonly data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{number_format($dtl->stock_dtl_rate,3,'.','')}}" class="tblGridCal_rate form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" data-id="mrp" name="pd[{{$loop->iteration}}][mrp]" value="{{number_format($dtl->mrp,3,'.','')}}" class="mrp form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" data-id="ex_net_tp" name="pd[{{$loop->iteration}}][ex_net_tp]" value="{{number_format($dtl->stock_dtl_ex_net_tp,3,'.','')}}" class="tblGridCal_ex_net_tp tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" data-id="adjrate" name="pd[{{$loop->iteration}}][adjrate]" value="{{number_format($dtl->stock_dtl_adjrate,3,'.','')}}" class="tblGridCal_adjrate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" data-id="purc_rate" name="pd[{{$loop->iteration}}][purc_rate]" value="{{number_format($dtl->stock_dtl_purc_rate,3,'.','')}}" class="tblGridCal_purc_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" readonly data-id="amount" name="pd[{{$loop->iteration}}][amount]" value="{{number_format($dtl->stock_dtl_amount,3,'.','')}}" class="tblGridCal_amount readonly form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
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
                <div class="row">
                    <div class="offset-md-10 col-lg-2 text-right">
                        <table class="tableTotal" style="width: 100%;">
                            <tbody>
                            <tr>
                                <td><div class="t_total_label">Total:</div></td>
                                <td><strong><span class="t_gross_total">0</span></strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @include('inventory.stock_transfer.summary_total')

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
    @endpermission();
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    @include('partial_script.po_header_calc');
    <script src="{{ asset('js/pages/js/stock.js?v='.time()) }}" type="text/javascript"></script>
    {{-- <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script> --}}
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        $(document).on('click' , '#getGRNRequestData' , function(e){
            validate = true
            var grn_id = $('#ref_grn_id').val();
            if(valueEmpty(grn_id)){
                toastr.error('Please Select GRN No. First');
                validate = false;
                return false;
            }
            if(validate){
                var disabledElement = $('table.erp_form__grid');
                var url = '/stock/890/get-grn-dtl-data';
                var formData = {
                    grn_id : grn_id,
                    rate_type : $('#rate_type').val(),
                    rate_perc : $('#rate_perc').val(),
                };
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    url : url,
                    data        : formData,
                    beforeSend : function(){
                        disabledElement.addClass('pointerEventsNone');
                    },
                    success : function(response){
                        if(response.status == 'success'){
                            $('tbody.erp_form__grid_body').html('');
                            if(!valueEmpty(response.data['grn'])){
                                // console.log(response.data['grn']);
                                var grns = response.data['grn'].grn_dtl;
                                var tr = '';
                                var total_length = $('tbody.erp_form__grid_body tr').length;
                                for(var p=0; p < grns.length; p++ ){
                                    total_length++;
                                    var row = grns[p];
                                    tr += '<tr class="new-row">'+
                                        '<td class="handle">'+
                                            '<i class="fa fa-arrows-alt-v handle"></i>'+
                                            '<input type="text" value="'+total_length+'" name="pd['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly="" autocomplete="off" aria-invalid="false">'+
                                            '<input type="hidden" name="pd['+total_length+'][product_id]" data-id="product_id" value="'+row.product_id+'" class="product_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                            '<input type="hidden" name="pd['+total_length+'][product_barcode_id]" data-id="product_barcode_id" value="'+row.product_barcode_id+'" class="product_barcode_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                            '<input type="hidden" name="pd['+total_length+'][uom_id]" data-id="uom_id" value="'+row.barcode.uom.uom_id+'" class="uom_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                            '<input type="hidden" name="pd['+total_length+'][grn_qty]" data-id="grn_qty" value="'+row.grn_qty+'" class="tblGridCal_grn_qty form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][dis_perc]" data-id="dis_perc" value="'+row.dis_perc+'" class="tblGridCal_discount_perc form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][dis_amount]" data-id="dis_amount" value="'+row.dis_amount+'" class="tblGridCal_discount_amount form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][after_dis_amount]" data-id="after_dis_amount" value="'+row.after_dis_amount+'" class="tblGridCal_after_discount_amount form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][gst_perc]" data-id="gst_perc" value="'+row.gst_perc+'" class="tblGridCal_gst_perc form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][gst_amount]" data-id="gst_amount" value="'+row.gst_amount+'" class="tblGridCal_gst_amount form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][fed_perc]" data-id="fed_perc" value="'+row.fed_perc+'" class="tblGridCal_fed_perc form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][fed_amount]" data-id="fed_amount" value="'+row.fed_amount+'" class="tblGridCal_fed_amount form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][spec_disc_perc]" data-id="spec_disc_perc" value="'+row.spec_disc_perc+'" class="tblGridCal_spec_disc_perc form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][spec_disc_amount]" data-id="spec_disc_amount" value="'+row.spec_disc_amount+'" class="tblGridCal_spec_disc_amount form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][gross_amount]" data-id="gross_amount" value="'+row.gross_amount+'" class="tblGridCal_gross_amount form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][net_amount]" data-id="net_amount" value="'+row.net_amount+'" class="tblGridCal_net_amount form-control erp-form-control-sm handle" readonly>\n' +
                                            '<input type="hidden" name="pd['+total_length+'][unit_price]" data-id="unit_price" value="'+row.unit_price+'" class="tblGridCal_unit_price form-control erp-form-control-sm handle" readonly>\n' +
                                            
                                        '</td>'+
                                        '<td>'+
                                            '<input type="text" name="pd['+total_length+'][pd_barcode]" data-id="pd_barcode" data-url="" value="'+ row.barcode.product_barcode_barcode +'" title="'+row.barcode.product_barcode_barcode+'" class="form-control erp-form-control-sm pd_barcode tb_moveIndex open_inline__help" readonly="" autocomplete="off">'+
                                        '</td>'+
                                        '<td>'+
                                            '<input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" data-url="" value="'+ row.product.product_name +'" class="form-control erp-form-control-sm product_name" readonly="" autocomplete="off">'+
                                        '</td>'+
                                        '<td>' +
                                            '<div class="erp-select2">' +
                                                '<select class="pd_uom field_readonly form-control erp-form-control-sm">' +
                                                    '<option value="'+row.barcode.uom.uom_id+'">'+row.barcode.uom.uom_name+'</option>' +
                                                '</select>' +
                                            '</div>' +
                                        '</td>'+
                                        '<td><input readonly data-id="pd_packing" name="pd['+total_length+'][pd_packing]" value="'+ row.barcode.product_barcode_packing +'" type="text" class="pd_packing form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="sys_qty" name="pd['+total_length+'][sys_qty]" value="'+ row.tbl_purc_grn_dtl_sys_quantity +'" type="text" class="tblGridCal_sys_qty form-control erp-form-control-sm validNumber validOnlyNumber "></td>'+
                                        '<td><input readonly data-id="demand_qty" name="pd['+total_length+'][demand_qty]" value="" type="text" class="demand_qty form-control erp-form-control-sm validNumber validOnlyNumber "></td>'+
                                        '<td><input  data-id="quantity" name="pd['+total_length+'][quantity]" value="'+ row.tbl_purc_grn_dtl_quantity +'" type="text" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="rate" name="pd['+total_length+'][rate]" value="'+ row.tbl_purc_grn_dtl_sale_rate +'" type="text" class="tblGridCal_rate form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input data-id="mrp" name="pd['+total_length+'][mrp]" value="'+ row.tbl_purc_grn_dtl_mrp +'" type="text" class="mrp form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input  data-id="ex_net_tp" name="pd['+total_length+'][ex_net_tp]" value="'+ row.tbl_purc_grn_dtl_net_tp +'" type="text" class="tblGridCal_ex_net_tp tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input  data-id="adjrate" name="pd['+total_length+'][adjrate]" value="" type="text" class="tblGridCal_adjrate  tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input  data-id="purc_rate" name="pd['+total_length+'][purc_rate]" value="'+ row.tbl_purc_grn_dtl_net_tp +'" type="text" class="tblGridCal_purc_rate tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="amount" name="pd['+total_length+'][amount]" value="'+ row.tbl_purc_grn_dtl_gross_amount +'" type="text" class="tblGridCal_amount form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>'+

                                        '<td class="text-center">'+
                                        '<div class="btn-group btn-group btn-group-sm" role="group">'+
                                        '<button type="button" class="btn btn-danger gridBtn delData">'+
                                        '<i class="la la-trash"></i>'+
                                        '</button>'+
                                        '</div>'+
                                        '</td>'+
                                        '</tr>';
                                }
                                $('tbody.erp_form__grid_body').append(tr);
                            }
                            toastr.success(response.message);
                        }else{
                            toastr.error(response.message);
                        }
                        disabledElement.removeClass('pointerEventsNone');
                    },
                    error : function(xhr,response){
                        disabledElement.removeClass('pointerEventsNone');
                        toastr.error('Something went wrong!');
                    }
                });
            }
        });

        function funcAfterAddRow(){

        }
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
                'id':'sys_qty',
                'fieldClass':'tblGridCal_sys_qty tb_moveIndex validNumber validOnlyNumber',
                'readonly':true
            },
            {
                'id':'demand_qty',
                'fieldClass':'demand_qty tb_moveIndex validNumber validOnlyNumber',
                'readonly':true
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty validNumber validOnlyNumber tb_moveIndex'
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate validNumber',
                'readonly':true
            },
            {
                'id':'mrp',
                'fieldClass':'mrp validNumber',
            },
            {
                'id':'ex_net_tp',
                'fieldClass':'tblGridCal_ex_net_tp tb_moveIndex validNumber'
            },
            {
                'id':'adjrate',
                'fieldClass':'tblGridCal_adjrate tb_moveIndex validNumber'
            },
            {
                'id':'purc_rate',
                'fieldClass':'tblGridCal_purc_rate tb_moveIndex validNumber'
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber',
                'readonly':true
            },
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id','grn_qty','dis_perc','dis_amount','after_dis_amount','gst_perc','gst_amount','fed_perc','fed_amount','spec_disc_perc','spec_disc_amount','gross_amount','net_amount','unit_price'];

        $(document).on('keyup','.tblGridCal_purc_rate',function(){
            //var thix = $(this);
            //var val = thix.val();
            //var qty = thix.parents('tr').find('.tblGridCal_qty').val();
            //var amount = parseFloat(qty) * parseFloat(val);
            //thix.parents('tr').find('.tblGridCal_amount').val(parseFloat(amount).toFixed(3));
        });
        $(".date_inputmask").inputmask("99-99-9999", {
            "mask": "99-99-9999",
            "placeholder": "dd-mm-yyyy",
            autoUnmask: true
        });
        $(document).on("blur",".tblGridCal_adjrate",function(){
            var thix = $(this);
            var adjrate = parseFloat(thix.val()).toFixed(3);
            var ex_net_tp = thix.parents('tr').find('.tblGridCal_ex_net_tp').val();
            if(valueEmpty(adjrate)){
                adjrate = 0;
            }
            var purc_rate = parseFloat(ex_net_tp) + parseFloat(adjrate);
            thix.parents('tr').find('.tblGridCal_purc_rate').val(parseFloat(purc_rate).toFixed(3));
        });
        $(document).on("blur",".tblGridCal_purc_rate",function(){
            var thix = $(this);
            var purc_rate = parseFloat(thix.val()).toFixed(3)
            if(valueEmpty(purc_rate)){
                purc_rate = "";
            }
            thix.val(purc_rate)
        });
        $('#getStockRequestData').click(function(){
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
                            rate_type : $('#rate_type').val(),
                            rate_perc : $('#rate_perc').val(),
                        };
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type        : 'POST',
                            url         : '/stock/890/get-stock-request-dtl-data',
                            dataType	: 'json',
                            data        : formData,
                            success: function(response) {
                                if(response['status'] == 'success'){
                                    toastr.success(response.message);
                                    var stock = response.data.stock.dtls;
                                    var rate_data = response.data.rate;
                                    var tr = "";
                                    var iteration = $('.erp_form__grid_body').find('tr').length + 1;
                                    for(var i=0;i < stock.length;i++){
                                        var stocki = stock[i];
                                        var qty = stocki['demand_dtl_demand_quantity'];
                                        var rate = rate_data[i]['rate'];
                                        var ex_net_tp = notNullEmpty(rate_data[i]['purc_rate'],3);
                                        var adjrate = notNullEmpty(0,3);
                                        var purc_rate = notNullEmpty(rate_data[i]['purc_rate'],3);
                                        //var purc_rate = notNullEmpty(rate_data[i]['purc_rate'],3);
                                        var cost_rate = notNullEmpty(rate_data[i]['cost_rate'],3);
                                        var vatPerc = rate_data[i]['vat_purc'];
                                        var amount = parseFloat(purc_rate) * parseFloat(qty);
                                        var vat_amt = (parseFloat(amount)/100)*parseFloat(vatPerc);
                                        var gross_amount = parseFloat(amount) + parseFloat(vat_amt);
                                        tr += '<tr>\n' +
                                            '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>\n' +
                                                '<input type="text" value="'+iteration+'" name="pd['+iteration+'][sr_no]"  class="form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][product_id]" data-id="product_id" value="'+stocki['product_id']+'" class="product_id form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][uom_id]" data-id="uom_id" value="'+stocki['uom']['uom_id']+'" class="uom_id form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][product_barcode_id]" data-id="product_barcode_id" value="'+stocki['product_barcode_id']+'" class="product_barcode_id form-control erp-form-control-sm handle" readonly>\n' +
                                                
                                                '<input type="hidden" name="pd['+iteration+'][dis_perc]" data-id="dis_perc" value="'+stocki['dis_perc']+'" class="tblGridCal_discount_perc form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][dis_amount]" data-id="dis_amount" value="'+stocki['dis_amount']+'" class="tblGridCal_discount_amount form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][after_dis_amount]" data-id="after_dis_amount" value="'+stocki['after_dis_amount']+'" class="tblGridCal_after_discount_amount form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][gst_perc]" data-id="gst_perc" value="'+stocki['gst_perc']+'" class="tblGridCal_gst_perc form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][gst_amount]" data-id="gst_amount" value="'+stocki['gst_amount']+'" class="tblGridCal_gst_amount form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][fed_perc]" data-id="fed_perc" value="'+stocki['fed_perc']+'" class="tblGridCal_fed_perc form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][fed_amount]" data-id="fed_amount" value="'+stocki['fed_amount']+'" class="tblGridCal_fed_amount form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][spec_disc_perc]" data-id="spec_disc_perc" value="'+stocki['spec_disc_perc']+'" class="tblGridCal_spec_disc_perc form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][spec_disc_amount]" data-id="spec_disc_amount" value="'+stocki['spec_disc_amount']+'" class="tblGridCal_spec_disc_amount form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][gross_amount]" data-id="gross_amount" value="'+stocki['gross_amount']+'" class="tblGridCal_gross_amount form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][net_amount]" data-id="net_amount" value="'+stocki['net_amount']+'" class="tblGridCal_net_amount form-control erp-form-control-sm handle" readonly>\n' +
                                                '<input type="hidden" name="pd['+iteration+'][unit_price]" data-id="unit_price" value="'+stocki['unit_price']+'" class="tblGridCal_unit_price form-control erp-form-control-sm handle" readonly>\n' +
                                            '</td>\n' +
                                            '<td><input type="text" data-id="pd_barcode" name="pd['+iteration+'][pd_barcode]" value="'+stocki['product_barcode_barcode']+'" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>\n' +
                                            '<td><input type="text" data-id="product_name" name="pd['+iteration+'][product_name]" value="'+stocki['product']['product_name']+'" class="product_name form-control erp-form-control-sm" readonly></td>\n' +
                                            '<td>\n' +
                                                '<select class="pd_uom field_readonly form-control erp-form-control-sm" data-id="pd_uom" name="pd['+iteration+'][uom]">\n' +
                                                '<option value="'+stocki['uom']['uom_id']+'">'+stocki['uom']['uom_name']+'</option>\n' +
                                                '</select>\n' +
                                            '</td>\n' +
                                            '<td><input type="text" data-id="pd_packing" name="pd['+iteration+'][pd_packing]" value="'+stocki['demand_dtl_packing']+'" class="pd_packing form-control erp-form-control-sm" readonly></td>\n' +
                                            '<td><input type="text" data-id="demand_qty" name="pd['+iteration+'][demand_qty]" value="'+qty+'" class="demand_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td><input type="text" data-id="quantity" name="pd['+iteration+'][quantity]" value="'+qty+'" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>\n' +
                                            '<td><input type="text" readonly data-id="rate" name="pd['+iteration+'][rate]" value="'+notNullEmpty(rate,3)+'" class="tblGridCal_rate form-control erp-form-control-sm validNumber" ></td>\n' +
                                            '<td><input type="text" data-id="ex_net_tp" name="pd['+iteration+'][ex_net_tp]" value="'+notNullEmpty(ex_net_tp,3)+'" class="tblGridCal_ex_net_tp tb_moveIndex form-control erp-form-control-sm validNumber" ></td>\n' +
                                            '<td><input type="text" data-id="adjrate" name="pd['+iteration+'][adjrate]" value="'+notNullEmpty(adjrate,3)+'" class="tblGridCal_adjrate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>\n' +
                                            '<td><input type="text" data-id="purc_rate" name="pd['+iteration+'][purc_rate]" value="'+notNullEmpty(purc_rate,3)+'" class="tblGridCal_purc_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>\n' +
                                            '<td><input type="text" data-id="amount" name="pd['+iteration+'][amount]" value="'+notNullEmpty(amount,3)+'" class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>\n' +
                                            '<td class="text-center">\n' +
                                                '<div class="btn-group btn-group btn-group-sm" role="group">\n' +
                                                    '<button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>\n' +
                                                '</div>\n' +
                                            '</td>\n' +
                                            '</tr>';
                                        iteration += 1;
                                    }
                                    $('.erp_form__grid_body').append(tr);
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

        $('.btn-minus-selected-data').click(function(){
             $(this).parents('.input-group').find('a#requestLink').attr('href','javascript:;').removeAttr('target');
        })
        //change sale rate
        $('.sale_rate_barcode').click(function() {

            var barcodeData = {};
            barcodeData.data = [];
              var sale_rate = $('.erp_form__grid>thead.erp_form__grid_header>tr').find('#product_barcode_id').val()
            if (sale_rate) {
                barcodeData.data.push(sale_rate);
            }
            if ($('.erp_form__grid>tbody.erp_form__grid_body>tr').length > 0) {
            $('.erp_form__grid>tbody.erp_form__grid_body>tr').each(function() {
                var thix = $(this)
                var barcode = thix.find('input[data-id="product_barcode_id"]').val();
                barcodeData.data.push(barcode);
            });
            }
            if (barcodeData.data.length > 0) {
            var url = '/grn/barcode-sale-price';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: barcodeData,
                success: function(response, data) {
                    if(response['status'] == 'success'){
                    toastr.success('Sale Rate Updated');
                    var products = response.product_barcode;
                    products.forEach(element => {
                        var parent = $('input[value="' + element.product_barcode_id + '"]').parents('tr');
                        var value = parseFloat(element.product_barcode_sale_rate_rate).toFixed(3);
                        parent.find('input[data-id="rate"]').val(value);
                    });
                    }
                },
                error: function(response, status) {
                }

            });
            }
        });

        $('#generatePriceTags').click(function() {

            var formData = {};
            formData.data = [];
            $('.erp_form__grid>tbody.erp_form__grid_body>tr').each(function() {
                var thix = $(this)
                var tr = {
                    'barcode_id': thix.find('input[data-id="product_barcode_id"]').val(),
                    'qty': thix.find('input[data-id="quantity"]').val(),
                }
                formData.data.push(tr);
            });
            console.log(formData);
            var url = '/grn/barcode-price-tag';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    var win = window.open(url, "generateBarcodeTagsST");
                    win.location.reload();
                    if (response) {
                    }
                },
                error: function(response, status) {}
            });
        })

        function getDateFunc(dt){
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            const daysNames = ["Sun", "Mon", "Tue", "Wed", "Thr", "Fri", "Sat"];
            var d = new Date(dt);
            var date = "";
            date += daysNames[d.getDay()]+" ";
            date += d.getDate()+" ";
            date += monthNames[d.getMonth()]+" ";
            date += d.getFullYear();
            return date;
        }

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
        <script>
            $(document).on('keyup blur' , '.overall_vat_perc, .tblGridCal_qty, .tblGridCal_adjrate, .tblGridCal_purc_rate',function(e){
                //Cost according to quantity
                var thix = this;
                var tr = $(this).parents('tr');
                funcHeaderCalc(tr,thix);
                if (typeof allGridTotal !== 'undefined'){ // func make on GRN form
                    allGridTotal();
                }
            });
            function funcHeaderCalc(tr,thix = null){
                var cListArr = [];
                if(!valueEmpty(thix)){
                    var cList = thix.classList;
                    for(var i=0;i<cList.length;i++){
                        cListArr.push(cList[i]);
                    }
                }
                //debugger
                var qty = tr.find('.tblGridCal_qty').val();
                var purc = tr.find('.tblGridCal_purc_rate').val();
                var purc_amount = funcCalcNumberFloat(qty) * funcCalcNumberFloat(purc);
                tr.find('.tblGridCal_amount').val(funcNumberFloat(purc_amount));
            }
        </script>
    
    @yield('summary_total_pageJS')
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
