@extends('layouts.layout')
@section('title', 'Update Product Price')

@section('pageCSS')
    <style>
        .erp_form__grid_body td, .erp_form__grid_header th {
            border: 1px solid #a0b0cc !important;
        }
    </style>
    <link href="/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />

@endsection

@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $date =  date('d-m-Y');
            $user_id = Auth::user()->id;
            $code = $data['customer_code'];
            $categ_id = 2;

            $dataSessionPrice = session('UpdatePrice');
        }
        if($case == 'edit'){
            $id = $data['current']->change_rate_id;
            $date = $data['current']->change_rate_date;
            $name = $data['current']->change_rate_name;
            $code = $data['current']->change_rate_code;
            $supplier_id = isset($data['current']->supplier)?$data['current']->supplier->supplier_id:"";;
            $supplier_code = isset($data['current']->supplier)?$data['current']->supplier->supplier_name:"";;
            $notes = $data['current']->change_rate_notes;
            $refGrnCode = $data['current']->ref_grn_code;
            $refGrnId = $data['current']->reg_grn_id;
            $stock_receiving_id = $data['current']->stock_receiving_id;
            $stock_receiving_code = $data['current']->stock_receiving_code;
            /*if($categ_id == ''){
                $categ_id = 'all';
            }*/
            $dtls = isset($data['current']->change_rate_dtl)? $data['current']->change_rate_dtl:[];
        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="change_rate" class="kt-form" method="post" action="{{ action('Purchase\ChangeRate@store', isset($id)?$id:"") }}">
    <input type="hidden" name="change_rate" value='{{$form_type}}' id="form_type">

    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="form-group-block row">
                    <div class="col-lg-6">
                        <div class="erp-page--title">
                            {{isset($code)?$code:""}}
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <div class="col-md-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="change_rate_name" value="{{isset($name)?$name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="change_rate_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{$date}}" id="kt_datepicker_3" autofocus/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            @if(isset($dataSessionPrice) && is_array($dataSessionPrice) && $dataSessionPrice != NULL)
                            @php
                                $GrnPrice = \App\Models\ViewPurcGRN::where('grn_id',$dataSessionPrice)
                                ->select('supplier_id','supplier_name')->first()->toArray();
                            @endphp
                            <label class="col-lg-6 erp-col-form-label">Vendor:</label>
                            <div class="col-lg-6">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" value="{{[$GrnPrice]['0']['supplier_name']}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" id="supplier_name" name="supplier_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{[$GrnPrice]['0']['supplier_id']}}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                            <i class="la la-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php
                                session()->forget('UpdatePrice');
                            @endphp
                            @else
                            <label class="col-lg-6 erp-col-form-label">Vendor:</label>
                            <div class="col-lg-6">
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
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Branch: <span class="required">*</span></label>
                            <div class="col-lg-8">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="branch_id" name="branch_ids[]" multiple>
                                        <option value="{{auth()->user()->branch_id}}" selected>{{auth()->user()->branch->branch_name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            @if(isset($dataSessionPrice) && is_array($dataSessionPrice) && $dataSessionPrice != NULL)
                            @php
                                $GrnPriceCode = \App\Models\ViewPurcGRN::where('grn_id',$dataSessionPrice)
                                ->select('grn_code')->first()->toArray();
                            @endphp
                            <label class="col-lg-4 erp-col-form-label">GRN Reference:</label>
                            <div class="col-lg-8">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" value="{{ $GrnPriceCode['grn_code'] }}" data-url="{{action('Common\DataTableController@inlineHelpOpen','grnHelp')}}" id="ref_grn_code" name="ref_grn_code" class="open_inline__help form-control erp-form-control-sm moveIndex open_inline__help__focus" placeholder="Enter Ref GRN" autocomplete="off">
                                        <input type="hidden" id="ref_grn_id" name="ref_grn_id" value="{{ $dataSessionPrice['0'] }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text group-input-btn get-lpo-data" id="grnGetData">
                                                Go
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php
                            session()->forget('UpdatePrice');
                            @endphp
                            @else
                            <label class="col-lg-4 erp-col-form-label">GRN Reference:</label>
                            <div class="col-lg-8">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" value="{{ $refGrnCode ?? '' }}" data-url="{{action('Common\DataTableController@inlineHelpOpen','grnHelp')}}" id="ref_grn_code" name="ref_grn_code" class="open_inline__help form-control erp-form-control-sm moveIndex open_inline__help__focus" placeholder="Enter Ref GRN" autocomplete="off">
                                        <input type="hidden" id="ref_grn_id" name="ref_grn_id" value="{{ $refGrnId ?? '' }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text group-input-btn get-lpo-data" id="grnGetData">
                                                Go
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Stock Receiving:</label>
                            <div class="col-lg-8">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" value="{{ $stock_receiving_code ?? '' }}" data-url="{{action('Common\DataTableController@inlineHelpOpen','stockReceivingHelp')}}" id="stock_receiving_code" name="stock_receiving_code" class="open_inline__help form-control erp-form-control-sm moveIndex open_inline__help__focus" placeholder="Enter Ref ST">
                                        <input type="hidden" id="stock_receiving_id" name="stock_receiving_id" value="{{ $stock_receiving_id ?? '' }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text group-input-btn get-lpo-data" id="stockReceivingGetData">
                                                Go
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
                        <button type="button" id="getListOfProduct" class="btn btn-sm btn-primary">Product help</button>
                        <div style="font-size: 9px;color: red;">(Click Here or Press F4)</div>
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
                                    $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Old Rate','New Rate','Difference'];
                                @endphp
                                <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                    @foreach($headings as $key=>$heading)
                                        <li>
                                            <label>
                                                <input value="{{$key}}" type="checkbox" checked> {{$heading}}
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="kt-user-page-setting" style="display: inline-block">
                                <button type="button" style="width: 30px;height: 30px;" title="Setting Save" data-toggle="tooltip" class="btn btn-brand btn-elevate btn-circle btn-icon" id="pageUserSettingSave">
                                    <i class="la la-floppy-o"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block">
                    <div class="erp_form___block">
                        <div class="table-scroll form_input__block">
                            <table class="table table_pit_list erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
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
                                        <div class="erp_form__grid_th_title">Current TP</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="current_tp" type="text" class="current_tp form-control erp-form-control-sm validNumber validOnlyNumber" readonly>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Last TP</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="last_tp" type="text" class="last_tp form-control erp-form-control-sm validNumber validOnlyNumber" readonly>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sale Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input readonly id="sale_rate" type="text" class="sale_rate form-control erp-form-control-sm validNumber validOnlyNumber">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">GP %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input readonly id="gp_perc" type="text" class="gp_perc form-control erp-form-control-sm validNumber validOnlyNumber">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">GP Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input readonly id="gp_amount" type="text" class="gp_amount form-control erp-form-control-sm validNumber validOnlyNumber">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">MRP</div>
                                        <div class="erp_form__grid_th_input">
                                            <input readonly id="mrp" type="text" class="mrp form-control erp-form-control-sm validNumber validOnlyNumber">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Whole Sale Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input readonly id="whole_sale_rate" type="text" class="whole_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">New TP</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="new_tp" type="text" class="new_tp form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">New Sale Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="new_sale_rate" type="text" class="new_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">New GP %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="new_gp_perc" type="text" class="new_gp_perc form-control erp-form-control-sm validNumber validOnlyNumber">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">New GP Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="new_gp_amount" type="text" class="new_gp_amount form-control erp-form-control-sm validNumber validOnlyNumber">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">New MRP</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="new_mrp" type="text" class="new_mrp form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">New Whole Sale Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="new_whole_sale_rate" type="text" class="new_whole_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex">
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
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="sr_count form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product_id)?$dtl->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                            </td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                            <td><input readonly data-id="current_tp" name="pd[{{$loop->iteration}}][current_tp]" type="text" class="current_tp form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                            <td><input readonly data-id="last_tp" name="pd[{{$loop->iteration}}][last_tp]" type="text" class="last_tp form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                            <td><input readonly data-id="sale_rate" name="pd[{{$loop->iteration}}][sale_rate]" type="text" class="sale_rate form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                            <td><input readonly data-id="gp_perc" name="pd[{{$loop->iteration}}][gp_perc]" type="text" class="gp_perc form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                            <td><input readonly data-id="gp_amount" name="pd[{{$loop->iteration}}][gp_amount]" type="text" class="gp_amount form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                            <td><input readonly data-id="mrp" name="pd[{{$loop->iteration}}][mrp]" type="text" class="mrp form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                            <td><input readonly data-id="whole_sale_rate" name="pd[{{$loop->iteration}}][whole_sale_rate]" type="text" class="whole_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                            <td><input data-id="new_tp" name="pd[{{$loop->iteration}}][new_tp]" type="text" class="new_tp form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>
                                            <td><input data-id="new_sale_rate" name="pd[{{$loop->iteration}}][new_sale_rate]" type="text" class="new_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>
                                            <td><input data-id="new_gp_perc" name="pd[{{$loop->iteration}}][new_gp_perc]" type="text" class="new_gp_perc form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                            <td><input data-id="new_gp_amount" name="pd[{{$loop->iteration}}][new_gp_amount]" type="text" class="new_gp_amount form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                            <td><input data-id="new_mrp" name="pd[{{$loop->iteration}}][new_mrp]" type="text" class="new_mrp form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>
                                            <td><input data-id="new_whole_sale_rate" name="pd[{{$loop->iteration}}][new_whole_sale_rate]" type="text" class="new_whole_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>
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
                <div class="form-group-block row">
                    <label class="col-lg-2 erp-col-form-label">Notes:</label>
                    <div class="col-lg-10">
                        <textarea type="text" rows="3" id="change_rate_notes" name="change_rate_notes" maxlength="255" class="form-control erp-form-control-sm">{{isset($notes)?$notes:""}}</textarea>
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

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/purchase/change_rate.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        //var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode tb_moveIndex open_inline__help',
                'require':true,
                'readonly':true
                // 'data-url' : productHelpUrl
            },
            {
                'id':'product_name',
                'fieldClass':'product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
            {
                'id':'current_tp',
                'fieldClass':'current_tp',
                'readonly':true
            },{
                'id':'last_tp',
                'fieldClass':'last_tp',
                'readonly':true
            },{
                'id':'sale_rate',
                'fieldClass':'sale_rate',
                'readonly':true
            },{
                'id':'gp_amount',
                'fieldClass':'gp_amount',
                'readonly':true
            },{
                'id':'gp_perc',
                'fieldClass':'gp_perc',
                'readonly':true
            },{
                'id':'mrp',
                'fieldClass':'mrp',
                'readonly':true
            },{
                'id':'whole_sale_rate',
                'fieldClass':'whole_sale_rate',
                'readonly':true
            },
            {
                'id':'new_tp',
                'fieldClass':'new_tp validNumber validOnlyNumber tb_moveIndex',
            },{
                'id':'new_sale_rate',
                'fieldClass':'new_sale_rate validNumber validOnlyNumber tb_moveIndex',
            },{
                'id':'new_gp_perc',
                'fieldClass':'new_gp_perc',
            },{
                'id':'new_gp_amount',
                'fieldClass':'new_gp_amount',
            },{
                'id':'new_mrp',
                'fieldClass':'new_mrp validNumber validOnlyNumber tb_moveIndex',
            },{
                'id':'new_whole_sale_rate',
                'fieldClass':'new_whole_sale_rate validNumber validOnlyNumber',
            },
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
        var remain_req = 0; // variable use start from in funcAddSelectedPro

        $(document).on('click' , '#grnGetData' , function(e){
            validate = true
            var grn_id = $('#ref_grn_id').val();
            if(valueEmpty(grn_id)){
                toastr.error('Please Select GRN No. First');
                validate = false;
                return false;
            }
            if(validate){
                var url = '/change-rate/grn/' + grn_id;
                $.ajax({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'GET',
                    url : url,
                    beforeSend : function(){
                        $('body').addClass('pointerEventsNone');
                    },
                    success : function(response){
                        $('body').removeClass('pointerEventsNone');
                        if(response.status == 'success'){
                            var grns = response.data['grn'].grn_dtl;
                            var tr = '';
                            var total_length = $('tbody.erp_form__grid_body tr').length;
                            for(var p=0; p < grns.length; p++ ){
                                total_length++;
                                var  row = grns[p];
                                tr += '<tr class="new-row">'+
                                        '<td class="handle">'+
                                            '<i class="fa fa-arrows-alt-v handle"></i>'+
                                            '<input type="text" value="'+total_length+'" name="pd['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly="" autocomplete="off" aria-invalid="false">'+
                                            '<input type="hidden" name="pd['+total_length+'][product_id]" data-id="product_id" value="'+grns[p].product_id+'" class="product_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                            '<input type="hidden" name="pd['+total_length+'][product_barcode_id]" data-id="product_barcode_id" value="'+grns[p].product_barcode_id+'" class="product_barcode_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                            '<input type="hidden" name="pd['+total_length+'][uom_id]" data-id="uom_id" value="'+grns[p].uom_id+'" class="uom_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                        '</td>'+
                                        '<td>'+
                                            '<input type="text" name="pd['+total_length+'][pd_barcode]" data-id="pd_barcode" data-url="" value="'+ grns[p].barcode.product_barcode_barcode +'" title="'+grns[p].barcode.product_barcode_barcode+'" class="form-control erp-form-control-sm pd_barcode tb_moveIndex open_inline__help" readonly="" autocomplete="off">'+
                                        '</td>'+
                                        '<td>'+
                                            '<input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" data-url="" value="'+ grns[p].product.product_name +'" class="form-control erp-form-control-sm product_name" readonly="" autocomplete="off">'+
                                        '</td>'+
                                        '<td><input readonly data-id="current_tp" name="pd['+total_length+'][current_tp]" value="'+ grns[p].tbl_purc_grn_dtl_net_tp +'" type="text" class="current_tp form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="last_tp" name="pd['+total_length+'][last_tp]" value="'+ grns[p].tbl_purc_grn_dtl_last_tp +'" type="text" class="last_tp form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="sale_rate" name="pd['+total_length+'][sale_rate]" value="'+ grns[p].tbl_purc_grn_dtl_sale_rate +'" type="text" class="sale_rate form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="gp_perc" name="pd['+total_length+'][gp_perc]" value="'+ grns[p].tbl_purc_grn_dtl_gp_perc +'" type="text" class="gp_perc form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="gp_amount" name="pd['+total_length+'][gp_amount]" value="'+ grns[p].tbl_purc_grn_dtl_gp_amount +'" type="text" class="gp_amount form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="mrp" name="pd['+total_length+'][mrp]"  value="'+ grns[p].tbl_purc_grn_dtl_mrp +'" type="text" class="mrp form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="whole_sale_rate" name="pd['+total_length+'][whole_sale_rate]" value="" type="text" class="whole_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input data-id="new_tp" name="pd['+total_length+'][new_tp]"  value="'+ grns[p].tbl_purc_grn_dtl_net_tp +'" type="text" class="new_tp form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>'+
                                        '<td><input data-id="new_sale_rate" name="pd['+total_length+'][new_sale_rate]"  value="'+ grns[p].tbl_purc_grn_dtl_sale_rate +'" type="text" class="new_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>'+
                                        '<td><input data-id="new_gp_perc" name="pd['+total_length+'][new_gp_perc]"  value="'+ grns[p].tbl_purc_grn_dtl_gp_perc +'" type="text" class="new_gp_perc form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input data-id="new_gp_amount" name="pd['+total_length+'][new_gp_amount]"  value="'+ grns[p].tbl_purc_grn_dtl_gp_amount +'" type="text" class="new_gp_amount form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input data-id="new_mrp" name="pd['+total_length+'][new_mrp]"  value="'+ grns[p].tbl_purc_grn_dtl_mrp +'" type="text" class="new_mrp form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>'+
                                        '<td><input data-id="new_whole_sale_rate" name="pd['+total_length+'][new_whole_sale_rate]" type="text" class="new_whole_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>'+

                                        '<td class="text-center">'+
                                            '<div class="btn-group btn-group btn-group-sm" role="group">'+
                                                '<button type="button" class="btn btn-danger gridBtn delData">'+
                                                '<i class="la la-trash"></i>'+
                                                '</button>'+
                                            '</div>'+
                                        '</td>'+
                                    '</tr>';
                            }
                            $('tbody.erp_form__grid_body').html('');
                            $('tbody.erp_form__grid_body').append(tr);

                            toastr.success(response.message);
                        }else{
                            toastr.error(response.message);
                        }
                    },
                    error : function(xhr,response){
                        $('body').removeClass('pointerEventsNone');
                        toastr.error('Something went wrong!');
                    }
                });
            }
        });

        $(document).on('click' , '#stockReceivingGetData' , function(e){
            validate = true
            var stock_receiving_id = $('#stock_receiving_id').val();
            if(valueEmpty(stock_receiving_id)){
                toastr.error('Please Select Stock Receiving First');
                validate = false;
                return false;
            }
            if(validate){
                var url = '/change-rate/stock-receiving/' + stock_receiving_id;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'GET',
                    url : url,
                    beforeSend : function(){
                        $('body').addClass('pointerEventsNone');
                    },
                    success : function(response){
                        $('body').removeClass('pointerEventsNone');
                        if(response.status == 'success'){
                            if(response.data['str'] !== null){
                                var total_length = 1;
                                var dtls = response.data['str'];
                                if(response.data['str']){
                                    $('#supplier_name').val(dtls['supplier_name']);
                                    $('#supplier_id').val(dtls['supplier_id']);
                                }
                                var tr = '';
                                //var total_length = $('tbody.erp_form__grid_body tr').length;
                                
                                for(var p=0; p < dtls.length; p++ ){
                                    var  row = dtls[p];
                                    tr += '<tr class="new-row">'+
                                        '<td class="handle">'+
                                        '<i class="fa fa-arrows-alt-v handle"></i>'+
                                        '<input type="text" value="'+total_length+'" name="pd['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly="" autocomplete="off" aria-invalid="false">'+
                                        '<input type="hidden" name="pd['+total_length+'][product_id]" data-id="product_id" value="'+row['product_id']+'" class="product_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                        '<input type="hidden" name="pd['+total_length+'][product_barcode_id]" data-id="product_barcode_id" value="'+row['product_barcode_id']+'" class="product_barcode_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                        '<input type="hidden" name="pd['+total_length+'][uom_id]" data-id="uom_id" value="'+row['uom_id']+'" class="uom_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                        '</td>'+
                                        '<td>'+
                                        '<input type="text" name="pd['+total_length+'][pd_barcode]" data-id="pd_barcode" data-url="" value="'+ row['product_barcode_barcode'] +'" title="'+row['barcode.product_barcode_barcode']+'" class="form-control erp-form-control-sm pd_barcode tb_moveIndex open_inline__help" readonly="" autocomplete="off">'+
                                        '</td>'+
                                        '<td>'+
                                        '<input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" data-url="" value="'+ row['product_name'] +'" class="form-control erp-form-control-sm product_name" readonly="" autocomplete="off">'+
                                        '</td>'+
                                        '<td><input readonly data-id="current_tp" name="pd['+total_length+'][current_tp]" value="'+ (!valueEmpty(row['net_tp'])?row['net_tp']:"") +'" type="text" class="current_tp form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="last_tp" name="pd['+total_length+'][last_tp]" value="'+ (!valueEmpty(row['last_tp'])?row['last_tp']:"") +'" type="text" class="last_tp form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="sale_rate" name="pd['+total_length+'][sale_rate]" value="'+ (!valueEmpty(row['sale_rate'])?row['sale_rate']:"") +'" type="text" class="sale_rate form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="gp_perc" name="pd['+total_length+'][gp_perc]" value="'+ (!valueEmpty(row['gp_perc'])?row['gp_perc']:"") +'" type="text" class="gp_perc form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="gp_amount" name="pd['+total_length+'][gp_amount]" value="'+ (!valueEmpty(row['gp_amount'])?row['gp_amount']:"") +'" type="text" class="gp_amount form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="mrp" name="pd['+total_length+'][mrp]" value="'+ (!valueEmpty(row['mrp'])?row['mrp']:"") +'" type="text"  class="mrp form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input readonly data-id="whole_sale_rate" name="pd['+total_length+'][whole_sale_rate]" value="" type="text" class="whole_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input data-id="new_tp" name="pd['+total_length+'][new_tp]" value="'+ row['stock_dtl_purc_rate'] +'" type="text" class="new_tp form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>'+
                                        '<td><input data-id="new_sale_rate" name="pd['+total_length+'][new_sale_rate]"  value="'+ (!valueEmpty(row['sale_rate'])?row['sale_rate']:"") +'" type="text" class="new_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>'+
                                        '<td><input data-id="new_gp_perc" name="pd['+total_length+'][new_gp_perc]" value="'+ (!valueEmpty(row['gp_perc'])?row['gp_perc']:"") +'" type="text" class="new_gp_perc form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input data-id="new_gp_amount" name="pd['+total_length+'][new_gp_amount]" value="'+ (!valueEmpty(row['gp_amount'])?row['gp_amount']:"") +'" type="text" class="new_gp_amount form-control erp-form-control-sm validNumber validOnlyNumber"></td>'+
                                        '<td><input data-id="new_mrp" name="pd['+total_length+'][new_mrp]" value="'+ row['mrp'] +'" type="text" class="new_mrp form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>'+
                                        '<td><input data-id="new_whole_sale_rate" name="pd['+total_length+'][new_whole_sale_rate]" type="text" class="new_whole_sale_rate form-control erp-form-control-sm validNumber validOnlyNumber tb_moveIndex"></td>'+

                                        '<td class="text-center">'+
                                        '<div class="btn-group btn-group btn-group-sm" role="group">'+
                                        '<button type="button" class="btn btn-danger gridBtn delData">'+
                                        '<i class="la la-trash"></i>'+
                                        '</button>'+
                                        '</div>'+
                                        '</td>'+
                                        '</tr>';
                                        
                                    total_length++;
                                }
                                $('tbody.erp_form__grid_body').html('');
                                $('tbody.erp_form__grid_body').append(tr);
                            }
                            toastr.success(response.message);
                        }else{
                            toastr.error(response.message);
                        }
                    },
                    error : function(xhr,response){
                        $('body').removeClass('pointerEventsNone');
                        toastr.error('Something went wrong!');
                    }
                });
            }
        });

        $(document).on('keyup','.new_tp,.new_sale_rate',function(){
            var tr = $(this).parents('tr');

            funcGPAmountCalc(tr);
        })

        function funcGPAmountCalc(tr){
            var sale_rate = tr.find('.new_sale_rate').val();
            var new_tp = tr.find('.new_tp').val();
            if(!emptyArr.includes(sale_rate) && !emptyArr.includes(new_tp)){
                var gp_amount = parseFloat(sale_rate) - parseFloat(new_tp);
                tr.find('.new_gp_amount').val(parseFloat(gp_amount).toFixed(3));
                var gp_perc = (parseFloat(gp_amount) / parseFloat(new_tp)) * 100;
                tr.find('.new_gp_perc').val(parseFloat(gp_perc).toFixed(3));
            }
        }
        $(document).on('keyup','.new_gp_amount',function(){
            var tr = $(this).parents('tr');

            var new_gp_amount = tr.find('.new_gp_amount').val();
            var new_tp = tr.find('.new_tp').val();
            if(emptyArr.includes(new_tp)){
                new_tp = 0;
            }
            var new_sale_rate = parseFloat(new_gp_amount) + parseFloat(new_tp);
            tr.find('.new_sale_rate').val(parseFloat(new_sale_rate).toFixed(3));
            if(emptyArr.includes(new_tp)){
                new_tp = 1;
            }
            var gp_perc = (parseFloat(new_gp_amount) / parseFloat(new_tp)) * 100;
            tr.find('.new_gp_perc').val(parseFloat(gp_perc).toFixed(3));
        })

        $(document).on('keyup','.new_gp_perc',function(){
            var tr = $(this).parents('tr');
            var new_gp_perc = tr.find('.new_gp_perc').val();
            var new_tp = tr.find('.new_tp').val();
            if(!emptyArr.includes(new_tp) && !emptyArr.includes(new_gp_perc)){
                var new_gp_amount = (parseFloat(new_gp_perc) / 100) * parseFloat(new_tp);
                tr.find('.new_gp_amount').val(parseFloat(new_gp_amount).toFixed(3));
            }
        })

        $(document).on('click','.btn-minus-selected-data', function(){
            $('form#change_rate').find('.erp_form___block').find('#supplier_name').removeClass('readonly');
            $('form#change_rate').find('.erp_form___block').find('#supplier_name').parents('.open-modal-group').removeClass('readonly');
        })
        function funcAfterAddRow(){}
    </script>
    <script>

    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script>
        var form_modal_type = 'change_rate';
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
