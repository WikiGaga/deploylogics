@extends('layouts.layout')
@section('title', 'Multi Barcode Labels')
@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $type =$data['form_type'];
        if($case == 'new'){
            $mfg_date =  date('d-m-Y');
            $date =  date('d-m-Y');
            $user_id = Auth::user()->id;
            $code = $data['document_code'];

            $id = '';
            $barcode_design = '';
            $supplier_name = '';
            $days = '';

            $dataSessionBarcode = session('dataBarcodeTags');
            if(is_array($dataSessionBarcode) && $dataSessionBarcode != null && $dataSessionBarcode != ''){
                $barcode_design = 'barcode_with_price';
            }

            $dataSessionGRN = session('dataGrnTags');
            if(is_array($dataSessionGRN) && $dataSessionGRN != null && $dataSessionGRN != ''){
                $barcode_design = 'barcode_with_price';
            }

        }
        if($case == 'edit'){
            $id = $data['current']->barcode_labels_id;
            $barcode_design = $data['current']->barcode_design;
            $code = $data['current']->barcode_labels_code;
            $supplier_name = $data['current']->supplier_name;
            $mfg_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->mfg_date))));
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_date))));
            $days = $data['current']->no_of_days;
            $dtls = isset($data['current']->dtl)? $data['current']->dtl:[];
        }
    @endphp
    {{--@ permission($data['permission'])--}}
    <form id="dynamic_barcode_tag_form" class="kt-form" method="post" action="{{ action('BarcodeLabels\BarcodeLabelsController@store',[$type,$id]) }}">
        @csrf
        <input type="hidden" name="barcode_labels_type" value="{{$data['barcode_labels_type']}}">
        <input type="hidden" id="form_type" value="multi_barcode_labels">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="erp-page--title">
                                {{isset($code)?$code:""}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        {{--<div class="col-lg-3">
                            <label class="erp-col-form-label">Branch: <span class="required">*</span></label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="branch_id" name="branch_id">
                                    <option value="0">Select</option>
                                    @foreach($data['branch'] as $branch)
                                        <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>--}}
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Vendor: </label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="supplier_name" name="supplier_name">
                                    <option value="0">Select</option>
                                    <option value="hashim_and_co" {{$supplier_name == 'hashim_and_co'?'selected':''}}>Hashim & Co</option>
                                    <option value="defence_rice" {{$supplier_name == 'defence_rice'?'selected':''}}>Defence Rice</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Barcode Style: </label>
                            <div class="erp-select2">
                                <select class="form-control erp-form-control-sm kt-select2" name="barcode_design" id="barcode_design">
                                    <option value="barcode_only" {{$barcode_design == 'barcode_only'?'selected':''}}>Barcode Only</option>
                                    <option value="barcode_with_expiry" {{$barcode_design == 'barcode_with_expiry'?'selected':''}}>Barcode With Expiry</option>
                                    <option value="barcode_with_price" {{$barcode_design == 'barcode_with_price'?'selected':''}}>Barcode With Price</option>
                                    <option value="barcode_with_price_expiry" {{$barcode_design == 'barcode_with_price_expiry'?'selected':''}}>Barcode With Price & Expiry</option>
                                    <option value="ex_weight_barcode_prefix_99" {{$barcode_design == 'ex_weight_barcode_prefix_99'?'selected':''}}>Ex Weight Barcode Prefix = 99</option>
                                    <option value="shelf_tag" {{$barcode_design == 'shelf_tag'?'selected':''}}>Shelf Tag</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2" style="background: #f0f8ff;">
                            <label class="erp-col-form-label"><input type="checkbox" id="best_before" name="best_before"> Best Before:  </label>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="erp-col-form-label">No. of Days:  </label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" id="no_of_days" name="no_of_days"  class="readonly form-control erp-form-control-sm validNumber text-left">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2" style="background: #f0f8ff;">
                            <label class="erp-col-form-label">MFG Date:  </label>
                            <div class="input-group date">
                                <input type="text" name="mfg_date" class="form-control erp-form-control-sm c-date-p kt_datepicker_3" readonly value="{{isset($mfg_date)?$mfg_date:""}}" id="mfg_date"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2" style="background: #f0f8ff;">
                            <label class="erp-col-form-label">BB Date:  </label>
                            <div class="input-group date">
                                <input readonly type="text" name="sales_date" class="form-control erp-form-control-sm c-date-p kt_datepicker_3" readonly value="{{isset($date)?$date:""}}" id="sales_date"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-calendar"></i>
                                    </span>
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
                                        $headings = ['Sr No','Barcode','Product Name','1st Level Category','Last Level Category','Print Qty','Sale Rate','Weight','Sale Amount'];
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
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                    <tr id="erp_form_grid_header_row">
                                        <th scope="col" width="35px">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                                <input id="first_level_category_id" readonly type="hidden" class="first_level_category_id form-control erp-form-control-sm">
                                                <input id="last_level_category_id" readonly type="hidden" class="last_level_category_id form-control erp-form-control-sm">
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
                                                <input id="product_name" readonly type="text" class=" product_name form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">1st Level Category</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="first_level_category" readonly type="text" class=" first_level_category form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Last Level Category</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="last_level_category" readonly type="text" class=" last_level_category form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Print Qty</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Sale Rate</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="rate" type="text" class="tblGridCal_sales_rate validNumber tb_moveIndex form-control erp-form-control-sm" readonly>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Weight</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="weight" type="text" class="tblGridCal_weight validNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Sale Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="amount" type="text" readonly class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm" autocomplete="off">
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
                                    @if(isset($dtls) && count($dtls) != 0)
                                        @foreach($dtls as $dtl)
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product_id)?$dtl->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][last_level_category_id]" data-id="first_level_category_id" value="{{isset($dtl->group_item_parent_id)?$dtl->group_item_parent_id:""}}" class="first_level_category_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][first_level_category_id]" data-id="last_level_category_id" value="{{isset($dtl->group_item_id)?$dtl->group_item_id:""}}" class="last_level_category_id form-control erp-form-control-sm handle" readonly>
                                                </td>
                                                <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product_name)?$dtl->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="first_level_category" name="pd[{{$loop->iteration}}][first_level_category]" value="{{isset($dtl->group_item_parent_name)?$dtl->group_item_parent_name:""}}" class="first_level_category form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="last_level_category" name="pd[{{$loop->iteration}}][last_level_category]" value="{{isset($dtl->group_item_name)?$dtl->group_item_name:""}}" class="last_level_category form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->barcode_labels_dtl_qty}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{number_format($dtl->barcode_labels_dtl_rate,3)}}" class="tblGridCal_sales_rate tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                <td><input type="text" data-id="weight" name="pd[{{$loop->iteration}}][weight]" value="{{number_format($dtl->barcode_labels_dtl_weight,3)}}" class="tblGridCal_weight tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="amount" name="pd[{{$loop->iteration}}][amount]" value="{{number_format($dtl->barcode_labels_dtl_amount,3)}}" class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @if(isset($dataSessionBarcode) && is_array($dataSessionBarcode) && $dataSessionBarcode != NULL)
                                       @php
                                           $dataBarcode = [];
                                           foreach ($dataSessionBarcode as $barcode_ids){
                                              $ba = \App\Models\ViewPurcProductBarcodeRate::where('product_id',$barcode_ids['product_id'])
                                                       ->select('product_id','product_name','product_barcode_id','product_barcode_barcode','group_item_parent_id','group_item_parent_name','group_item_id','group_item_name')->first()->toArray();

                                               $dataBarcode[] = (object)[
                                                   'product_barcode_id' => $ba['product_barcode_id'],
                                                   'product_barcode_barcode' => $ba['product_barcode_barcode'],
                                                   'product_name' => $ba['product_name'],
                                                   'product_id' => $ba['product_id'],
                                                   'group_item_parent_id' => $ba['group_item_parent_id'],
                                                   'group_item_parent_name' => $ba['group_item_parent_name'],
                                                   'group_item_id' => $ba['group_item_id'],
                                                   'group_item_name' => $ba['group_item_name'],
                                                   'barcode_labels_dtl_rate' => $barcode_ids['sale_rate'],
                                                   'barcode_labels_dtl_qty' => $barcode_ids['qty'],
                                                   'barcode_labels_dtl_weight' => 0,
                                                   'barcode_labels_dtl_amount' => $barcode_ids['sale_rate'],
                                               ];
                                           }
                                       @endphp
                                        @foreach($dataBarcode as $dtl)
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$dtl->product_id}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{$dtl->product_barcode_id}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][first_level_category_id]" data-id="first_level_category_id" value="{{$dtl->group_item_parent_id}}" class="first_level_category_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][last_level_category_id]" data-id="last_level_category_id" value="{{$dtl->group_item_id}}" class="last_level_category_id form-control erp-form-control-sm handle" readonly>
                                                </td>
                                                <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->product_barcode_barcode}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product_name)?$dtl->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="first_level_category" name="pd[{{$loop->iteration}}][first_level_category]" value="{{isset($dtl->group_item_parent_name)?$dtl->group_item_parent_name:""}}" class="first_level_category form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="last_level_category" name="pd[{{$loop->iteration}}][last_level_category]" value="{{isset($dtl->group_item_name)?$dtl->group_item_name:""}}" class="last_level_category form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->barcode_labels_dtl_qty}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{number_format($dtl->barcode_labels_dtl_rate,3)}}" class="tblGridCal_sales_rate tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                <td><input type="text" data-id="weight" name="pd[{{$loop->iteration}}][weight]" value="{{number_format($dtl->barcode_labels_dtl_weight,3)}}" class="tblGridCal_weight tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="amount" name="pd[{{$loop->iteration}}][amount]" value="{{number_format($dtl->barcode_labels_dtl_amount,3)}}" class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                       @php
                                           session()->forget('dataBarcodeTags');
                                       @endphp
                                    @endif
                                    @if(isset($dataSessionGRN) && is_array($dataSessionGRN) && $dataSessionGRN != NULL)
                                       @php
                                           $dataGRN = \App\Models\ViewPurcGRN::where('grn_id',$dataSessionGRN)
                                            ->select('product_id','product_name','product_barcode_id','product_barcode_barcode','group_item_parent_id','group_item_parent_name','group_item_id','group_item_name','tbl_purc_grn_dtl_sale_rate','tbl_purc_grn_dtl_quantity')->get()->toArray();
                                       @endphp
                                        @foreach($dataGRN as $dtl)
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$dtl['product_id']}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{$dtl['product_barcode_id']}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][first_level_category_id]" data-id="first_level_category_id" value="{{$dtl['group_item_parent_id']}}" class="first_level_category_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][last_level_category_id]" data-id="last_level_category_id" value="{{$dtl['group_item_id']}}" class="last_level_category_id form-control erp-form-control-sm handle" readonly>
                                                </td>
                                                <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl['product_barcode_barcode']}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{$dtl['product_name']}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="first_level_category" name="pd[{{$loop->iteration}}][first_level_category]" value="{{$dtl['product_name']}}" class="first_level_category form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="last_level_category" name="pd[{{$loop->iteration}}][last_level_category]" value="{{$dtl['group_item_name']}}" class="last_level_category form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl['tbl_purc_grn_dtl_quantity']}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{number_format($dtl['tbl_purc_grn_dtl_sale_rate'],3)}}" class="tblGridCal_sales_rate tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                <td><input type="text" data-id="weight" name="pd[{{$loop->iteration}}][weight]" value="{{number_format(0,3)}}" class="tblGridCal_weight tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="amount" name="pd[{{$loop->iteration}}][amount]" value="{{number_format($dtl['tbl_purc_grn_dtl_quantity'],3)}}" class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                       @php
                                           session()->forget('dataGrnTags');
                                       @endphp
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--@ endpermission--}}
@endsection

@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection
@section('customJS')
    <script>
        // Class definition

        var KTFormWidgets = function () {
            // Private functions
            var validator;
            var formId = $( "#dynamic_barcode_tag_form" )
            $.validator.addMethod("valueNotEquals", function(value, element, arg){
                return arg !== value;
            }, "This field is required");
            var initValidation = function () {
                validator = formId.validate({
                    // define validation rules
                    rules: {
                        barcode_labels_name: {
                            //  required: true,
                            maxlength:100
                        },
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
                                    console.log(response);
                                    toastr.success(response.message);
                                    setTimeout(function () {
                                        $("form").find(":submit").prop('disabled', false);
                                    }, 2000);
                                    if(response.data.form == 'edit'){
                                         window.location.href = response.data.redirect;
                                    }else{
                                        var win = window.open(response['data']['print_url'], "dprint");
                                        win.location.reload();
                                      //  window.location.href = response['data']['redirect'];
                                        location.reload();
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

    </script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var hiddenFieldsFormName = 'SaleProductsForm';
    </script>
    <script src="{{ asset('js/pages/js/erp-form-fields-hide.js') }}" type="text/javascript"></script>
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
                'fieldClass':'product_name tb_moveIndex',
                'message':'Enter Product Detail',
                'require':true
            },
            {
                'id':'first_level_category',
                'fieldClass':'first_level_category tb_moveIndex'
            },
            {
                'id':'last_level_category',
                'fieldClass':'last_level_category tb_moveIndex'
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_sales_rate tb_moveIndex validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id':'weight',
                'fieldClass':'tblGridCal_weight tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber',
                readonly: true
            },
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','first_level_category_id','last_level_category_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>

    <script>

        $(document).on('keyup','.tblGridCal_sales_rate,.tblGridCal_weight,.tblGridCal_qty',function(){
            var thix = $(this);
            var tr = thix.parents('tr');
            var rate = tr.find('.tblGridCal_sales_rate').val();
            var qty = tr.find('.tblGridCal_weight').val();
            if(valueEmpty(rate)){
                rate = 0;
            }
            if(valueEmpty(qty)){
                qty = 0;
            }
            var amount = parseFloat(qty) * parseFloat(rate);
            if(valueEmpty(amount)){
                amount = 0;
            }
            tr.find('.tblGridCal_amount').val(parseFloat(amount).toFixed(3));
        });

        $(document).on('click','#best_before',function(){
            var thix = $(this);
            if(thix.prop('checked')) {
               $('#no_of_days').removeClass('readonly');
            } else {
                $('#no_of_days').addClass('readonly');
            }
        });

        $(document).on('keyup','#no_of_days',function(){
            var val = parseInt($(this).val());
            if(!valueEmpty(val)){
                var mfg_date = $('#mfg_date').val();

                var row = mfg_date.split("-");
                var day = row[0];
                var month = row[1];
                var year = row[2];
                var NewDate = year+'-'+month+'-'+day;
                var someDate = new Date(NewDate);

                var numberOfDaysToAdd = val;
                var result = someDate.setDate(someDate.getDate() + numberOfDaysToAdd);
                $('#sales_date').datepicker("update", new Date(result));
            }else{
                $(".datepicker").datepicker("update", new Date());
            }
        });

        $("#mfg_date").on("change", function () {
            var val = parseInt($('#no_of_days').val());
            if(!valueEmpty(val)){
                var mfg_date = $('#mfg_date').val();

                var row = mfg_date.split("-");
                var day = row[0];
                var month = row[1];
                var year = row[2];
                var NewDate = year+'-'+month+'-'+day;
                var someDate = new Date(NewDate);

                var numberOfDaysToAdd = val;
                var result = someDate.setDate(someDate.getDate() + numberOfDaysToAdd);

                console.log(new Date(result));

                $('#sales_date').datepicker("update", new Date(result));
            }else{
                $(".datepicker").datepicker("update", new Date());
            }
       });

    </script>
@endsection
