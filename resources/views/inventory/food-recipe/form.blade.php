@extends('layouts.layout')
@section('title', 'Food Recipe')

@section('pageCSS')
    <style>
        div#f_product_barcode_id-error {
            position: absolute;
            top: 28px;
        }
    </style>
@endsection

@section('content')
    <!--begin::Form-->
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code = $data['document_code'];
                $date =  date('d-m-Y');
            }
            if($case == 'edit'){
                $id = $data['current']->id;
                $code = $data['current']->id;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->item_formulation_date))));
                $food_id = $data['current']->id;
                $food_name = $data['current']->food->name;
                // $remarks = $data['current']->item_formulation_remarks;
                $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
            }
            $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="food-recipe_form" class="kt-form" method="post" action="{{ action('Inventory\ItemFormulationController@store', isset($id)?$id:"") }}">
        <input type="hidden" value='{{$data['form_type']}}' id="form_type">
        @csrf
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
                                        <input type="text" name="formulation_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
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
                                <label class="col-lg-6 erp-col-form-label text-center"> Recipe for Food:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp_form___block">
                                        <div class="input-group open-modal-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                                    <i class="la la-minus-circle"></i>
                                                </span>
                                            </div>
                                            <input type="text" id="food_id" name="food_id" value="{{isset($food_id)?$food_id:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','FoodRecipeHelp')}}" class="open_inline__help pd_barcode moveIndex form-control erp-form-control-sm" placeholder="Enter Here">
                                            <input type="hidden" id="form_type" name="form_type" value="{{isset($food_id)?$food_id:''}}" class="form-control erp-form-control-sm">
                                            <!-- <div class="input-group-append">
                                                <span class="input-group-text btn-open-modal">
                                                <i class="la la-search"></i>
                                                </span>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <input id="food_name" name="food_name" value="{{isset($food_name)?$food_name:''}}" type="text" class="form-control erp-form-control-sm" readonly>
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
                                        $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Qty'];
                                    @endphp
                                    <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="max-height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
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
                                                <input id="constants_id" readonly type="hidden" class="constants_id form-control erp-form-control-sm">
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
                                            <div class="erp_form__grid_th_title">Qty</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        {{-- <th scope="col">
                                            <div class="erp_form__grid_th_title">Remarks</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="remarks" type="text" class="tblGridCal_remarks form-control erp-form-control-sm">
                                            </div>
                                        </th> --}}
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
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][constants_id]" data-id="constants_id" value="{{isset($dtl->constants->constants_id)?$dtl->constants->constants_id:""}}" class="constants_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                </td>
                                                <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                                <td>
                                                    <select class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][uom]">
                                                        <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" data-id="pd_packing" name="pd[{{$loop->iteration}}][packing]" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->item_formulation_dtl_quantity}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                {{-- <td><input type="text" data-id="remarks" name="pd[{{$loop->iteration}}][remarks]" value="{{$dtl->item_formulation_dtl_remarks}}" class="tblGridCal_remarks tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td> --}}
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
                                            <td class="total_grid_qty">
                                                <input value="0.000" readonly type="text" id="total_qty" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            {{-- <td></td> --}}
                                        </tr>
                                        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <label class="col-lg-2 erp-col-form-label">Notes:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="2" maxlength="100"  name="formulation_remarks" class="moveIndex form-control erp-form-control-sm">{{isset($remarks)?$remarks:""}}</textarea>
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
    <script src="{{ asset('js/pages/js/food-recipe.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
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
                'id':'quantity',
                'fieldClass':'tblGridCal_qty validNumber validOnlyNumber tb_moveIndex'
            },
            // {
            //     'id':'remarks',
            //     'fieldClass':'tblGridCal_remarks'
            // }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id','constants_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
