@extends('layouts.layout')
@section('title', 'Barcode Price Tag')

@section('pageCSS')
@endsection

@section('content')
    <!--begin::Form-->
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $date =  date('d-m-Y');
                $user_id = Auth::user()->id;
                $code = $data['document_code'];
            }
            if($case == 'edit'){
                $id = $data['current']->barcode_price_tag_id;
                $name = $data['current']->barcode_price_tag_name;
                $code = $data['current']->barcode_price_tag_code;

                $dtls = isset($data['current']->barcode_price_tag_dtl)? $data['current']->barcode_price_tag_dtl:[];
            }
$form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="barcode_tag_form" class="kt-form" method="post" action="{{ action('Sales\BarcodePriceTagController@store', isset($id)?$id:"") }}">
    <input type="hidden" name="barcode_price_tag" value='{{$form_type}}' id="form_type">
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
                <div class="form-group-block row">
                    <label class="col-lg-2 erp-col-form-label">Name: </label>
                    <div class="col-lg-3">
                        <input type="text" name="barcode_price_tag_name" value="{{isset($name)?$name:""}}" maxlength="100" class="form-control erp-form-control-sm">
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
                                    $headings = ['Sr No','Barcode','Product Name','UOM','rate','Qty','Packing Date','Expiry Date'];
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
                                            <input id="product_name" type="text" class="product_name form-control erp-form-control-sm">
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
                                        <div class="erp_form__grid_th_title">Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="rate" type="text" class="tblGridCal_rate validNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">VAT %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="vat_perc" type="text" class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">VAT Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="vat_amount" type="text" class="tblGridCal_vat_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Packing Date</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="packing_date" title="{{date('d-m-Y')}}" type="text" class="date_inputmask tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Expiry Date</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="expiry_date" title="{{date('d-m-Y')}}" type="text" class="date_inputmask tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Gross Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="gross_amount" readonly type="text" class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm">
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
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                            </td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product_name)?$dtl->product_name:""}}" class="product_name form-control erp-form-control-sm"></td>
                                            <td>
                                                <select class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm" name="pd[{{$loop->iteration}}][pd_uom]" data-id="pd_uom" title="{{ isset($dtl->uom->uom_name)?$dtl->uom->uom_name:'' }}">
                                                    <option value="{{ isset($dtl->uom->uom_id)?$dtl->uom->uom_id:'' }}">{{ isset($dtl->uom->uom_name)?$dtl->uom->uom_name:'' }}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{number_format($dtl->barcode_price_tag_dtl_rate,3)}}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->barcode_price_tag_dtl_qty}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" data-id="vat_perc"  value="{{number_format($dtl->barcode_price_tag_vat_per,2)}}" class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_amount]" data-id="vat_amount"  value="{{number_format($dtl->barcode_price_tag_vat_amount,3)}}" class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            @php $pack_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->barcode_price_tag_dtl_packing_date)))); @endphp
                                            <td><input type="text" data-id="packing_date" name="pd[{{$loop->iteration}}][packing_date]" value="{{($pack_date =='01-01-1970')?'':$pack_date}}" title="{{($pack_date =='01-01-1970')?'':$pack_date}}" class="date_inputmask tb_moveIndex form-control erp-form-control-sm"/></td>
                                            @php $expiry_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->barcode_price_tag_dtl_expiry_date)))); @endphp
                                            <td><input type="text" data-id="expiry_date" name="pd[{{$loop->iteration}}][expiry_date]" value="{{($expiry_date =='01-01-1970')?'':$expiry_date}}" title="{{($expiry_date =='01-01-1970')?'':$expiry_date}}" class="date_inputmask tb_moveIndex form-control erp-form-control-sm"/></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount"  value="{{number_format($dtl->barcode_price_tag_total_amount,3)}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
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
    </div>
    </form>
                <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/barcode_price_tag.js') }}" type="text/javascript"></script>
    <script>
        $(document).on("keyup blur",".tblGridCal_rate, .tblGridCal_vat_perc, .tblGridCal_vat_amount",function(){
            let eleThis = $(this);
            let thix = eleThis.parents('tr');
            let gross_amount = 0;
            /*
                qty
            */
            var qty = 1;

            /*
                rate
            */
            var rate = thix.find('.tblGridCal_rate').val();
            rate = rate.replace(",","");
            rate = Number(rate);
            rate = parseFloat(rate);
            if(!rate){ rate = 0; thix.find('.tblGridCal_rate').val(js__number_format(rate));}

            /*
                amount calculate
            */
            var amount = qty * rate;

            gross_amount = 0 + amount;
            /*
                vat calculate
            */


            if(eleThis.hasClass('tblGridCal_vat_perc') || eleThis.hasClass('tblGridCal_rate')){
                var vat_perc = thix.find('.tblGridCal_vat_perc').val();
                if(vat_perc != ""){
                    vat_perc = vat_perc.replace(",","");
                    vat_perc = Number(vat_perc);
                    if(!vat_perc){ vat_perc = 0; }
                    if(vat_perc != 0){
                        var vat_amount = amount / 100 * vat_perc;
                        vat_amount = parseFloat(vat_amount);
                        thix.find('.tblGridCal_vat_amount').val(js__number_format(vat_amount));
                    }
                    if(vat_perc == 0){
                        var vat_amount = 0;
                        thix.find('.tblGridCal_vat_perc').val(parseFloat(0).toFixed(3));
                    }
                }else{
                    var vat_amount = 0;
                    thix.find('.tblGridCal_vat_amount').val(parseFloat(0).toFixed(3));
                }
            }
            if(eleThis.hasClass('tblGridCal_vat_amount') || (eleThis.hasClass('tblGridCal_rate') && vat_perc == 0) ){
                var vat_amount = thix.find('.tblGridCal_vat_amount').val();
                if(vat_amount != ""){
                    vat_amount = vat_amount.replace(",","");
                    vat_amount = Number(vat_amount);
                    if(!vat_amount){ vat_amount = 0;}
                    if(vat_amount != 0){
                        var vat_perc = vat_amount * 100 / amount;
                        vat_perc = parseFloat(vat_perc);
                        thix.find('.tblGridCal_vat_perc').val(js__number_format(vat_perc));
                    }
                    if(vat_amount == 0){
                        thix.find('.tblGridCal_vat_amount').val(parseFloat(0).toFixed(3));
                    }
                }else{
                    var vat_amount = 0;
                    thix.find('.tblGridCal_vat_perc').val(parseFloat(0).toFixed(2));
                }
            }

            gross_amount = gross_amount + vat_amount;
            gross_amount = parseFloat(gross_amount);
            thix.find('.tblGridCal_gross_amount').val(js__number_format(gross_amount));
        });
    </script>
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
                'require':true
            },
            {
                'id':'pd_uom',
                'fieldClass':'pd_uom field_readonly',
                'type':'select'
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate tb_moveIndex validNumber'
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'vat_perc',
                'fieldClass':'tblGridCal_vat_perc tb_moveIndex validNumber'
            },
            {
                'id':'vat_amount',
                'fieldClass':'tblGridCal_vat_amount tb_moveIndex validNumber'
            },
            {
                'id':'packing_date',
                'fieldClass':'date_inputmask tb_moveIndex',
            },
            {
                'id':'expiry_date',
                'fieldClass':'date_inputmask tb_moveIndex',
            },
            {
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true
            },
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
        $(".date_inputmask").inputmask("99-99-9999", {
            "mask": "99-99-9999",
            "placeholder": "dd-mm-yyyy",
            autoUnmask: true
        });
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
