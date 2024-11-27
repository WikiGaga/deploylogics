@extends('layouts.layout')
@section('title', 'Supplier Product Registration')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['document_code'];
        }
        if($case == 'edit'){
            $id = $data['current']->sup_prod_id;
            $code = $data['current']->sup_prod_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sup_prod_date))));
            $supplier_name = isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:'';
            $supplier_id = isset($data['current']->supplier->supplier_id)?$data['current']->supplier->supplier_id:'';
            $currency_id = $data['current']->currency_id;
            $exchange_rate = $data['current']->sup_prod_exchange_rate;
            $notes = $data['current']->sup_prod_remarks;
            $details = $data['current']->sub_prod;
        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="sup_prod_form" class="kt-form" method="post" action="{{ action('Purchase\SupplierProductController@store' , isset($id)?$id:'') }}">
        <input type="hidden" value='{{$form_type}}' id="form_type">
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
                                    {{$code}}
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
                                    <input type="text" name="prod_reg_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($date)?$date:date('d-m-Y')}}" id="kt_datepicker_3" autofocus/>
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
                            <label class="col-lg-6 erp-col-form-label">Supplier: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                        </div>
                                        <input type="text" id="supplier_name" value="{{isset($supplier_name)?$supplier_name:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" autocomplete="off" name="supplier_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
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
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Currency: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm moveIndex currency" id="kt_select2_1" name="currency_id">
                                        <option value="0">Select</option>
                                        @if(isset($id))
                                            @php $currency_id = isset($currency_id)?$currency_id:0;@endphp
                                            @foreach($data['currency'] as $currency)
                                                <option value="{{$currency->currency_id}}" {{$currency->currency_id==$currency_id?'selected':''}}>{{$currency->currency_name}}</option>
                                            @endforeach
                                        @else
                                            @foreach($data['currency'] as $currency)
                                                @if($currency->currency_default=='1')
                                                    @php $exchange_rate = $currency->currency_rate; @endphp
                                                @endif
                                                <option value="{{$currency->currency_id}}" {{$currency->currency_default=='1'?'selected':''}}>{{$currency->currency_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Exchange Rate:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($exchange_rate)?$exchange_rate:$exchange_rate}}" class="form-control erp-form-control-sm moveIndex validNumber">
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
                                    $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Category','Brand',
                                                'Pur Price','Sale Price','VAT %','HS Code','Sup Barcode','Sup Description',
                                                'Sup UOM','Sup Packing','Sup Category','Sup Brand','Sup Pur Price','Sup Sale Price',
                                                'Sup VAT %','Sup HS Code'];
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
                                        <div class="erp_form__grid_th_title">Sr No</div>
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
                                    <th cope="col">
                                        <div class="erp_form__grid_th_title">Packing</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Category</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_category" readonly type="text" class="pd_category form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Brand</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_brand" type="text" readonly class="pd_brand tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Pur Price</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_pur_price" type="text" readonly class="tblGridCal_purc_rate validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sale Price</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_sale_price" type="text" readonly class="tblGridCal_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">VAT %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_vat_perc" type="text" readonly class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">HS Code</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_hs_code" type="text" readonly class="pd_hs_code tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sup Barcode</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_barcode" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sup Description</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_description" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sup UOM</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_uom" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th cope="col">
                                        <div class="erp_form__grid_th_title">Sup Packing</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_packing" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sup Category</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_category" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sup Brand</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_brand" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sup Pur Price</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_pur_price" type="text" class="validNumber validOnlyFloatNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sup Sale Price</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_sale_price" type="text" class="tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sup VAT %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_vat_perc" type="text" class="validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sup HS Code</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_hs_code" type="text" class="validNumber tb_moveIndex form-control erp-form-control-sm">
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
                                @if(isset($details))
                                    @foreach($details as $dtl)
                                    @php
                                        $prod_type    = \App\Models\TblPurcProductType::where('product_type_id',isset($dtl->product->product_type_id)?$dtl->product->product_type_id:'')->first();
                                        $prod_brand   = \App\Models\TblPurcBrand::where('brand_id',isset($dtl->product->product_brand_id)?$dtl->product->product_brand_id:'')->first();
                                        $sale_rate = \App\Models\TblPurcProductBarcodeSaleRate::where('product_barcode_id',$dtl->product_barcode_id)->where('branch_id',auth()->user()->branch_id)->where('product_category_id',2)->first();
                                        $purc_rate = \App\Models\TblPurcProductBarcodePurchRate::where('product_barcode_barcode', $dtl->barcode->product_barcode_barcode)
                                                            ->where('product_barcode_id',$dtl->product_barcode_id)
                                                            ->where('branch_id',auth()->user()->branch_id)->first();
                                        $tax = \App\Models\TblPurcProductBarcodeDtl::where('product_barcode_id',$dtl->product_barcode_id)
                                                            ->where('branch_id',auth()->user()->branch_id)->first();
                                        @endphp
                                    <tr>
                                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sup_prod_dtl_id]" data-id="sup_prod_dtl_id" value="{{$dtl->sup_prod_dtl_id}}" class="sup_prod_dtl_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                           </td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                            <td>
                                                <select class="pd_uom field_readonly form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][pd_uom]">
                                                    <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_packing]" data-id="pd_packing" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_category]" data-id="pd_category" value="{{isset($prod_type)?$prod_type->product_type_name:''}}" class="pd_category form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_brand]" data-id="pd_brand" value="{{isset($prod_brand)?$prod_brand->brand_name:''}}"  class="pd_brand form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_pur_price]" data-id="pd_pur_price" value="{{isset($purc_rate)?number_format($purc_rate->product_barcode_purchase_rate,3):''}}"  class="tblGridCal_purc_rate form-control erp-form-control-sm validNumber" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_sale_price]" data-id="pd_sale_price" value="{{isset($sale_rate)?number_format($sale_rate->product_barcode_sale_rate_rate,3):''}}" class="tblGridCal_rate form-control erp-form-control-sm validNumber" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_vat_perc]" data-id="pd_vat_perc" value="{{isset($tax)?number_format($tax->product_barcode_tax_value,3):''}}" class="tblGridCal_vat_perc form-control erp-form-control-sm validNumber" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_hs_code]" data-id="pd_hs_code" value="{{isset($dtl->product->product_hs_code)?$dtl->product->product_hs_code:""}}" class="pd_hs_code form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_barcode]" data-id="supplier_barcode"  value="{{isset($dtl->sup_prod_sup_barcode)?$dtl->sup_prod_sup_barcode:""}}" class="tb_moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_description]" data-id="supplier_description"  value="{{isset($dtl->sup_prod_sup_description)?$dtl->sup_prod_sup_description:""}}" class="tb_moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_uom]" data-id="supplier_uom"  value="{{isset($dtl->sup_prod_sup_uom)?$dtl->sup_prod_sup_uom:""}}" class="tb_moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_packing]" data-id="supplier_packing"  value="{{isset($dtl->sup_prod_sup_pack)?$dtl->sup_prod_sup_pack:""}}" class="tb_moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_category]" data-id="supplier_category"  value="{{isset($dtl->sup_prod_sup_category)?$dtl->sup_prod_sup_category:""}}" class="tb_moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_brand]" data-id="supplier_brand"  value="{{isset($dtl->sup_prod_sup_brand)?$dtl->sup_prod_sup_brand:""}}" class="tb_moveIndex form-control erp-form-control-sm"></td>       
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_pur_price]" data-id="supplier_pur_price"  value="{{number_format($dtl->sup_prod_sup_pur_rate,2)}}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_sale_price]" data-id="supplier_sale_price"  value="{{number_format($dtl->sup_prod_sup_sale_rate,3)}}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_vat_perc]" data-id="supplier_vat_perc"  value="{{number_format($dtl->sup_prod_sup_vat_per,2)}}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_hs_code]" data-id="supplier_hs_code"  value="{{isset($dtl->sup_prod_sup_hs_code)?$dtl->sup_prod_sup_hs_code:""}}" class="validNumber tb_moveIndex form-control erp-form-control-sm"></td>
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
                <div class="row form-group-block">
                    <label class="col-lg-2 erp-col-form-label">Remarks:</label>
                    <div class="col-lg-10">
                        <textarea type="text" rows="2" id="notes" name="notes" maxlength="255" class="form-control erp-form-control-sm">{{isset($notes)?$notes:''}}</textarea>
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
    <script src="{{ asset('js/pages/js/sup_prod.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script>
        var formcase = '{{$case}}';
    </script>
    <script>
        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode tb_moveIndex open_inline__help',
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
                'id':'pd_category',
                'fieldClass':'pd_category',
                'readonly':true
            },
            {
                'id':'pd_brand',
                'fieldClass':'pd_brand',
                'readonly':true
            },
            {
                'id':'pd_pur_price',
                'fieldClass':'tblGridCal_purc_rate',
                'readonly':true
            },
            {
                'id':'pd_sale_price',
                'fieldClass':'tblGridCal_rate',
                'readonly':true
            },
            {
                'id':'pd_vat_perc',
                'fieldClass':'tblGridCal_vat_perc',
                'readonly':true
            },
            {
                'id':'pd_hs_code',
                'fieldClass':'pd_hs_code',
                'readonly':true
            },
            {
                'id':'supplier_barcode',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'supplier_description',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'supplier_uom',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'supplier_packing',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'supplier_category',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'supplier_brand',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'supplier_pur_price',
                'fieldClass':'tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'supplier_sale_price',
                'fieldClass':'tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'supplier_vat_perc',
                'fieldClass':'tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'supplier_hs_code',
                'fieldClass':'tb_moveIndex validNumber'
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection


