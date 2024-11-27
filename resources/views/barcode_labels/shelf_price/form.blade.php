@extends('layouts.layout')
{{--@section('title', 'Page Title')--}}
@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $type =$data['form_type'];
        if($case == 'new'){
            $date =  date('d-m-Y');
            $user_id = Auth::user()->id;
            $code = $data['document_code'];
            $id = '';
        }
        if($case == 'edit'){
            $id = $data['current']->barcode_labels_id;
            $name = $data['current']->barcode_labels_name;
            $code = $data['current']->barcode_labels_code;
            $dtls = isset($data['current']->dtl)? $data['current']->dtl:[];
        }
    @endphp
    @permission($data['permission'])
    <form id="barcode_tag_form" class="kt-form" method="post" action="{{ action('BarcodeLabels\BarcodeLabelsController@store',[$type,$id]) }}">
        @csrf
        <input type="hidden" name="barcode_labels_type" value="{{$data['barcode_labels_type']}}">
        <input type="hidden" id="form_type" value="barcode_labels">
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
                        <label class="col-lg-2 erp-col-form-label">Name: </label>
                        <div class="col-lg-3">
                            <input type="text" name="barcode_labels_name" value="{{isset($name)?$name:""}}" maxlength="100" class="form-control erp-form-control-sm">
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
                                        $headings = ['Sr No','Barcode','Product Name','Arabic Name','Rate','Qty'];
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
                                            <div class="erp_form__grid_th_title">Arabic Name</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="arabic_name" type="text" class="arabic_name text-right form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Qty</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Rate</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="rate" type="text" class="tblGridCal_rate validNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="amount" type="text" class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm" autocomplete="off">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Disc %</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="dis_perc" type="text" class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm" readonly autocomplete="off">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Disc Amt</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="dis_amount" type="text" class="tblGridCal_discount_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm" readonly autocomplete="off">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Vat %</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="vat_perc" type="text" class="tblGridCal_vat_perc validNumber tb_moveIndex form-control erp-form-control-sm"  readonly>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Vat Amt</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="vat_amount" type="text" class="tblGridCal_vat_amount validNumber tb_moveIndex form-control erp-form-control-sm" readonly>
                                            </div>
                                        </th>

                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Gross Amt</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="gross_amount" type="text" readonly class="tblGridCal_gross_amount validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
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
                                                </td>
                                                <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product_name)?$dtl->product_name:""}}" class="product_name form-control erp-form-control-sm"></td>
                                                <td><input type="text" data-id="arabic_name" name="pd[{{$loop->iteration}}][arabic_name]" value="{{isset($dtl->product_arabic_name)?$dtl->product_arabic_name:""}}" class="product_name text-right form-control erp-form-control-sm"></td>
                                                <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->barcode_labels_dtl_qty}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{number_format($dtl->barcode_labels_dtl_rate,3)}}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="amount" name="pd[{{$loop->iteration}}][amount]" value="{{number_format($dtl->barcode_labels_dtl_amount,3)}}" class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="dis_perc" name="pd[{{$loop->iteration}}][dis_perc]" value="{{number_format($dtl->barcode_labels_dtl_disc_per,3)}}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="dis_amount" name="pd[{{$loop->iteration}}][dis_amount]" value="{{number_format($dtl->barcode_labels_dtl_disc_amt,3)}}" class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="vat_perc" readonly name="pd[{{$loop->iteration}}][vat_perc]" value="{{number_format($dtl->barcode_labels_dtl_vat_per,3)}}" class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="vat_amount" readonly name="pd[{{$loop->iteration}}][vat_amount]" value="{{number_format($dtl->barcode_labels_dtl_vat,3)}}" class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="gross_amount" readonly name="pd[{{$loop->iteration}}][gross_amount]" value="{{$dtl->barcode_labels_dtl_grs_amt}}" class="tblGridCal_gross_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
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
    @endpermission
@endsection

@section('pageJS')
@endsection
@section('customJS')
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>>
    <script src="{{ asset('js/pages/js/barcode_price_tag.js') }}" type="text/javascript"></script>
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
                'fieldClass':'product_name',
                'message':'Enter Product Detail',
                'require':true
            },
            {
                'id':'arabic_name',
                'fieldClass':'arabic_name text-right '
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate tb_moveIndex validNumber'
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount tb_moveIndex validNumber'
            },
            {
                'id':'dis_perc',
                'fieldClass':'tblGridCal_discount_perc tb_moveIndex validNumber field_readonly'
            },
            {
                'id':'dis_amount',
                'fieldClass':'tblGridCal_discount_amount tb_moveIndex validNumber field_readonly'
            },
            {
                'id':'vat_perc',
                'fieldClass':'tblGridCal_vat_perc tb_moveIndex validNumber field_readonly'
            },
            {
                'id':'vat_amount',
                'fieldClass':'tblGridCal_vat_amount tb_moveIndex validNumber field_readonly'
            },
            {
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount tb_moveIndex validNumber field_readonly'
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
