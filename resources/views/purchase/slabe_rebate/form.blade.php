@extends('layouts.layout')
@section('title', 'Slab & Rebate Agreements')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['code'];
        }
        if($case == 'edit'){
            $id = $data['current']->contract_id;
            $code = $data['current']->contract_code;
            $rebete_level = $data['current']->contract_rebete_level;
            $start_date = $data['current']->contract_start_date;
            $end_date = $data['current']->contract_end_date;
            $notes = $data['current']->contract_notes;
            $name = $data['current']->supplier->supplier_name;
            $supplier_id = $data['current']->supplier->supplier_id;
        }
$form_type = $data['form_type'];
    @endphp
    <!--begin::Form-->
    @permission($data['permission'])
    <form id="slab_form" class="master_form kt-form" method="post" action="{{ action('Purchase\SupplierContracController@store' , isset($id)?$id:'') }}">
    @csrf
    <input type="hidden" value='{{$form_type}}' id="form_type">
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
                                    {{ $code }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Supplier:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                        </div>
                                        <input type="text" id="supplier_name" value="{{isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" autocomplete="off" name="supplier_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{isset($data['current']->supplier->supplier_id)?$data['current']->supplier->supplier_id:''}}"/>
                                        <div class="input-group-append">
                                                    <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                    <i class="la la-search"></i>
                                                    </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Rebate Level Name:</label>
                            <div class="col-lg-6">
                                <input type="text" name="contract_rebate_level" value="{{ isset($rebete_level)?$rebete_level:''}}" class="form-control erp-form-control-sm moveIndex">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <label class="col-lg-2 erp-col-form-label">Date:</label>
                    <div class="col-lg-4">
                        <div class="erp-selectDateRange">
                            <div class="input-daterange input-group kt_datepicker_5">
                                @if(isset($data['id']))
                                    @php $start_date =  date('d/m/Y', strtotime($start_date)); @endphp
                                @else
                                    @php $start_date =  date('d/m/Y'); @endphp
                                @endif
                                <input type="text" class="form-control erp-form-control-sm" title="Start Date" name="contract_start_date" value="{{$start_date}}" />
                                <div class="input-group-append">
                                    <span class="input-group-text erp-form-control-sm">To</span>
                                </div>
                                @if(isset($data['id']))
                                    @php $end_date =  date('d/m/Y', strtotime($end_date)); @endphp
                                @else
                                    @php $end_date =  date('d/m/Y'); @endphp
                                @endif
                                <input type="text" class="form-control erp-form-control-sm" title="End Date" name="contract_end_date" value="{{$end_date}}" />
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
                                    $headings = ['Sr No','Barcode','Product Name','Group','Brand','Qty',
                                                'Disc %','Example Remarks',];
                                @endphp
                                <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                    @foreach($headings as $key=>$heading)
                                        <li >
                                            <label>
                                                <input value="{{$key}}" name="{{trim($key)}}" type="checkbox" checked> {{$heading}}
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
                            <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                <thead class="erp_form__grid_header">
                                <tr>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sr.</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                            <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                            <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                            <input readonly type="hidden" id="brand_id" class="brand_id form-control erp-form-control-sm">
                                            <input readonly type="hidden" id="group_id" class="group_id form-control erp-form-control-sm">
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
                                        <div class="erp_form__grid_th_title">
                                            Group
                                            <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                <i class="la la-barcode"></i>
                                            </button>
                                        </div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="group" type="text" class="group tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','groupHelp')}}">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">
                                            Brand
                                            <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                <i class="la la-barcode"></i>
                                            </button>
                                        </div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="brand" type="text" class="brand tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','brandHelp')}}">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="quantity" type="text"  id="add_qty" class=" tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>

                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Disc %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="discount" type="text" class=" tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Example Remarks</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="examp_remarks" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Action</div>
                                        <div class="erp_form__grid_th_btn">
                                            <button type="button" id="addData" onclick="addQty()" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                <i class="la la-plus"></i>
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="erp_form__grid_body">
                                @if(isset($data['current']->contractDtl))
                                    @foreach($data['current']->contractDtl as $dtl)
                                        <tr>
                                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                <input readonly data-id="sr_no" type="text" name="pd[{{$loop->iteration}}][sr_no]" title="{{$loop->iteration}}" value="{{$loop->iteration}}"  class="form-control erp-form-control-sm handle">
                                                <input readonly type="hidden" data-id="contract_dtl_id" name="pd[{{$loop->iteration}}][contract_dtl_id]" value="{{ $dtl->contract_dtl_id }}" class="contract_dtl_id form-control erp-form-control-sm handle">
                                                <input readonly type="hidden" data-id="product_id" name="pd[{{$loop->iteration}}][product_id]" value="{{ $dtl->product_id }}" class="product_id form-control erp-form-control-sm handle">
                                                <input readonly type="hidden" data-id="product_barcode_id" name="pd[{{$loop->iteration}}][product_barcode_id]" value="{{ $dtl->prod_barcode_id }}" class="product_barcode_id form-control erp-form-control-sm">
                                                <input readonly type="hidden" data-id="brand_id" name="pd[{{$loop->iteration}}][brand_id]" value="{{ $dtl->contract_dtl_brand }}" class="brand_id form-control erp-form-control-sm handle">
                                                <input readonly type="hidden" data-id="group_id" name="pd[{{$loop->iteration}}][group_id]" value="{{ $dtl->contract_dtl_group }}" class="group_id form-control erp-form-control-sm">
                                            </td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][barcode]" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" title="{{$dtl->product_barcode_barcode}}" value="{{$dtl->product_barcode_barcode}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input readonly type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{$dtl->product->product_name}}" title="{{$dtl->product->product_name}}"  class="pd_product_name form-control erp-form-control-sm"></td>
                                            <td><input type="text" data-id="group" name="pd[{{$loop->iteration}}][group]" data-url="{{action('Common\DataTableController@inlineHelpOpen','groupHelp')}}" value="{{ $dtl->group->group_item_name_string }}" title="{{ $dtl->group->group_item_name_string }}" class="group tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="brand" name="pd[{{$loop->iteration}}][brand]" data-url="{{action('Common\DataTableController@inlineHelpOpen','brandHelp')}}" value="{{ $dtl->brand->brand_name }}" title="{{ $dtl->brand->brand_name }}" class="brand tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" data-id="quantity"  value="{{$dtl->purchase_order_dtlquantity}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>

                                            <td>
                                                <input type="text" data-id="discount" name="pd[{{$loop->iteration}}][discount]" title="{{ $dtl->contract_dtl_disc_percent}}" value="{{ $dtl->contract_dtl_disc_percent}}" class="moveIndex form-control erp-form-control-sm validNumber">
                                            </td>
                                            <td><input type="text" data-id="examp_remarks" name="pd[{{$loop->iteration}}][examp_remarks]" title="{{ $dtl->contract_dtl_example_remarks }}" value="{{ $dtl->contract_dtl_example_remarks }}"  class="moveIndex form-control erp-form-control-sm"></td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div>
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
                                        <td class="total_grid_qty">
                                            <input value="0.000" readonly type="text" id="total_qty" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        
                                    </tr>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <label class="col-lg-2 erp-col-form-label">Notes:</label>
                    <div class="col-lg-10">
                        <textarea type="text" rows="2" name="contract_notes" class="moveIndex form-control erp-form-control-sm">{{isset($notes)?$notes:""}}</textarea>
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



    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var groupHelpUrl = "{{url('/common/inline-help/groupHelp')}}";
        var brandHelpUrl = "{{url('/common/inline-help/brandHelp')}}";
        var arr_text_Field = [
            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode tb_moveIndex open_inline__help',
                'data-url' : productHelpUrl,
                'message':'Enter Barcode',
                'require':true,
                'readonly':true
            },
            {
                'id':'product_name',
                'fieldClass':'product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
            {
                'id':'group',
                'fieldClass':'group tb_moveIndex open_inline__help',
                'data-url' : groupHelpUrl,
                'readonly':true
            },
            {
                'id':'brand',
                'fieldClass':'brand tb_moveIndex open_inline__help',
                'data-url' : brandHelpUrl,
                'readonly':true
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty tb_moveIndex validNumber',
                'message':'Enter Qyantity',
                'require':true
            },
            {
                'id':'discount',
                'fieldClass':'tb_moveIndex validNumber'
            },
            {
                'id':'examp_remarks',
                'fieldClass':'tb_moveIndex'
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','group_id','brand_id'];


        // function addQty(){
        //         var total = document.getElementById('total_qty').value;
        //         var newQty = document.getElementById('add_qty').value;
        //         console.log(total , newQty);
        //         if(newQty != "" || !isNaN(newQty)){
        //             document.getElementById('total_qty').value = parseFloat(total) + parseFloat(newQty);
        //         }

        // }






    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('js/pages/js/product-ajax.js') }}" type="text/javascript"></script>--}}
@endsection


