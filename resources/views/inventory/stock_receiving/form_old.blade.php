@extends('layouts.template')
@section('title', 'Stock Transfer Receiving')

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
                $dtls = isset($data['current']->stock_dtls)? $data['current']->stock_dtls :[];
            }
            $type =$data['form_type'];
    @endphp
    <form id="stock_transfer_receiving" class="master_form kt-form" method="post" action="{{ action('Inventory\StockController@store', [$type,$id]) }}">
    @csrf
    <input type="hidden" name="stock_code_type" value='{{$data['stock_code_type']}}' id="form_type">
    <input type="hidden" name="stock_menu_id" value='{{$data['stock_menu_id']}}'>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
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
                                    <label class="col-lg-6 erp-col-form-label text-center">Transfer To:</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="moveIndex form-control erp-form-control-sm kt-select2" name="branch_to">
                                                <option value="0">Select</option>
                                                @php $transfer_from = isset($branch_id)?$branch_id:'' @endphp
                                                @foreach($data['branch'] as $branch)
                                                    <option value="{{$branch->branch_id}}" {{$branch->branch_id == $transfer_from?'selected':''}}>{{$branch->branch_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label text-center">Receiving From:</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="moveIndex form-control erp-form-control-sm kt-select2" name="branch">
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
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Store:</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="moveIndex form-control erp-form-control-sm kt-select2" name="store">
                                                <option value="0">Select</option>
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
                            <div class="col-lg-12 text-right">
                                <div class="data_entry_header" style="margin-bottom: -30px;">
                                    <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                                    <div class="dropdown dropdown-inline">
                                        <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                            <i class="flaticon-more" style="color: #666666;"></i>
                                        </button>
                                        @php
                                            $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Qty',
                                                          'Rate','Batch No','Amount'];
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
                        <div class="form-group-block" style="overflow: auto;">
                            <table id="StockTransferForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                                <thead>
                                <tr>
                                    <th width="5%">Sr No</th>
                                    <th width="14%">Barcode</th>
                                    <th width="20%">Product Name</th>
                                    <th width="12%">UOM</th>
                                    <th width="8%">Packing</th>
                                    <th width="12%">Qty</th>
                                    <th width="12%">Rate</th>
                                    <th width="12%">Batch No</th>
                                    <th width="12%">Amount</th>
                                    <th width="5%">Action</th>
                                </tr>
                                <tr id="dataEntryForm">
                                    <td><input readonly id="sr_no" type="text" class="form-control erp-form-control-sm">
                                        <input readonly type="hidden" id="product_id" class="product_id form-control erp-form-control-sm">
                                        <input readonly type="hidden" id="uom_id" class="uom_id form-control erp-form-control-sm">
                                        <input readonly type="hidden" id="product_barcode_id" class="product_barcode_id form-control erp-form-control-sm">
                                    </td>
                                    <td><input id="barcode" type="text" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}" class="open-inline-help pd_barcode moveIndex2 form-control erp-form-control-sm" autocomplete="off"></td>
                                    <td><input readonly id="product_name" type="text" class="pd_product_name form-control erp-form-control-sm"></td>
                                    <td>
                                        <select class="pd_uom moveIndex form-control erp-form-control-sm" id="uom">
                                            <option value="">Select</option>
                                        </select>
                                    </td>
                                    <td><input readonly id="packing" type="text" class="pd_packing form-control erp-form-control-sm"></td>
                                    <td><input id="quantity" type="text" class="moveIndex tblGridCal_qty form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                    <td><input id="rate" type="text" class="moveIndex tblGridCal_rate form-control erp-form-control-sm validNumber"></td>
                                    <td><input id="batch_no" type="text" class="moveIndex form-control erp-form-control-sm"></td>
                                    <td><input readonly id="amount" type="text" class="tblGridCal_amount stock_amount form-control erp-form-control-sm validNumber"></td>
                                    <td class="text-center">
                                        <button type="button" id="addData" class="moveIndexBtn moveIndex gridBtn btn btn-primary btn-sm">
                                            <i class="la la-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                </thead>
                                <tbody id="repeated_data">
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
                                                <td><input type="text" data-id="barcode" name="pd[{{$loop->iteration}}][barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}" class="pd_barcode moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="pd_product_name form-control erp-form-control-sm" readonly></td>
                                                <td>
                                                    <select class="pd_uom moveIndex form-control erp-form-control-sm" data-id="uom" name="pd[{{$loop->iteration}}][uom]">
                                                        <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" data-id="packing" name="pd[{{$loop->iteration}}][packing]" value="{{isset($dtl->stock_dtl_packing)?$dtl->stock_dtl_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->stock_dtl_quantity}}" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{number_format($dtl->stock_dtl_rate,2)}}" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="batch_no" name="pd[{{$loop->iteration}}][batch_no]" value="{{isset($dtl->stock_dtl_batch_no)?$dtl->stock_dtl_batch_no:""}}" class="moveIndex form-control erp-form-control-sm"></td>
                                                <td><input type="text" data-id="amount" name="pd[{{$loop->iteration}}][amount]" value="{{number_format($dtl->stock_dtl_amount,3)}}" class="tblGridCal_amount stock_amount form-control erp-form-control-sm validNumber" readonly></td>
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
    </div>
    </form>
                <!--end::Form-->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var hiddenFieldsFormName = 'StockTransferReceivingForm';
        var formcase = '{{$case}}';
    </script>
    <script src="{{ asset('js/pages/js/hidden-fields.js') }}" type="text/javascript"></script>
    <script>
        var productHelpUrl = "{{url('/common/inline-help/productHelpSI')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'barcode',
                'fieldClass':'pd_barcode moveIndex',
                'require':true,
                'data-url' : productHelpUrl
            },
            {
                'id':'product_name',
                'fieldClass':'pd_product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
            {
                'id':'uom',
                'fieldClass':'pd_uom',
                'readonly':true,
                'type':'select'
            },
            {
                'id':'packing',
                'fieldClass':'pd_packing',
                'readonly':true
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty moveIndex validNumber'
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate moveIndex validNumber'
            },
            {
                'id':'batch_no',
                'fieldClass':'moveIndex'
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount stock_amount validNumber',
                'readonly':true
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/product-inline-ajax2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
@endsection
