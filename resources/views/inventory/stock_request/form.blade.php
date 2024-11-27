@extends('layouts.layout')
@section('title', 'Stock Request')

@section('pageCSS')
@endsection

@section('content')
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code  = $data['document_code'];
                $date =  date('d-m-Y');
            }
            if($case == 'edit'){
                $id = $data['current']->demand_id;
                $code = $data['current']->demand_no;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->demand_date))));
                $supplier_name = isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:"";
                $supplier_id = isset($data['current']->supplier->supplier_id)?$data['current']->supplier->supplier_id:"";
                $branch_to = $data['current']->demand_branch_to;
                $notes = $data['current']->demand_notes;
                $dtls = isset($data['current']->dtls)? $data['current']->dtls:[];
            }
            $form_type = $data['form_type'];
    @endphp
@permission($data['permission'])
<!--begin::Form-->
<form id="stock_request_form" class="stock_form kt-form" method="post" action="{{ action('Inventory\StockRequestController@store',isset($id)?$id:'') }}">
@csrf
    <input type="hidden" value='{{$form_type}}' id="form_type">
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="form-group-block row">
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
                <div class="form-group-block row">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Document Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="demand_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{$date}}" id="kt_datepicker_3" autofocus />
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
                            <label class="col-lg-4 erp-col-form-label text-center">Branch To: <span class="required">*</span></label>
                            <div class="col-lg-8">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2" name="demand_branch_to">
                                        <option value="0">Select</option>
                                        @php $transfer_to = isset($branch_to)?$branch_to:'' @endphp
                                        @foreach($data['branch'] as $branch)
                                            @if($branch->branch_id != auth()->user()->branch_id)
                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id == $transfer_to?'selected':''}}>{{$branch->branch_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
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
                                    $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Physical Stock',
                                                  'Store Stock','Stock Match','Suggest Qty 1','Suggest Qty 2',
                                                  'Demand Qty','WIP LPO Stock','Pur.Ret in Waiting'];
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
                                <tr>
                                    <th scope="col">
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
                                    <th cope="col">
                                        <div class="erp_form__grid_th_title">Packing</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Physical Stock</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_physical_stock" type="text" class="physical_stock validNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Store Stock</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_store_stock" type="text" class="pd_store_stock validNumber form-control erp-form-control-sm" readonly>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Stock Match</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_stock_match" type="text" class="stock_match form-control erp-form-control-sm" readonly>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Suggest Qty 1</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_suggest_qty_1" type="text" class="suggest_qty_1 validNumber form-control erp-form-control-sm" readonly>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Suggest Qty 2</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_suggest_qty_2" type="text" class="suggest_qty_2 validNumber form-control erp-form-control-sm" readonly>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Demand Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_demand_qty" type="text" class="pd_demand_qty stock_amount tb_moveIndex validNumber form-control erp-form-control-sm">
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
                                @if(isset($dtls))
                                    @foreach($dtls as $dtl)
                                        @if(isset($dtl->product->product_id) && isset($dtl->uom->uom_id)
                                                && isset($dtl->barcode->product_barcode_barcode))
                                        <tr>
                                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]" title="{{$loop->iteration}}" class="form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$dtl->product->product_id}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{$dtl->product_barcode_id}}" class="product_barcode_id form-control erp-form-control-sm">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{$dtl->uom->uom_id}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][demand_dtl_id]" data-id="demand_dtl_id" value="{{$dtl->demand_dtl_id}}" class="demand_dtl_id form-control erp-form-control-sm handle" readonly>
                                            </td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_barcode]" data-id="pd_barcode" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_product_name]" data-id="pd_product_name" value="{{$dtl->product->product_name}}" title="{{$dtl->product->product_name}}" class="pd_product_name form-control erp-form-control-sm" readonly></td>
                                            <td>
                                                <select class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][pd_uom]" title="{{$dtl->uom->uom_name}}">
                                                    <option value="{{$dtl->uom->uom_id}}">{{$dtl->uom->uom_name}}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_packing]" data-id="pd_packing" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_physical_stock]" data-id="pd_physical_stock" value="{{$dtl->demand_dtl_physical_stock}}" title="{{$dtl->demand_dtl_physical_stock}}" class="tb_moveIndex physical_stock form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_store_stock]" data-id="pd_store_stock" value="{{$dtl->demand_dtl_store_stock}}" title="{{$dtl->demand_dtl_store_stock}}" class="pd_store_stock form-control erp-form-control-sm text-right" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_stock_match]" data-id="pd_stock_match" value="{{$dtl->demand_dtl_stock_match}}" title="{{$dtl->demand_dtl_stock_match}}" class="stock_match form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_suggest_qty_1]" data-id="pd_suggest_qty_1" value="{{$dtl->demand_dtl_suggest_quantity1}}" title="{{$dtl->demand_dtl_suggest_quantity1}}" class="suggest_qty_1 form-control erp-form-control-sm validNumber" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_suggest_qty_2]" data-id="pd_suggest_qty_2" value="{{$dtl->demand_dtl_suggest_quantity2}}" title="{{$dtl->demand_dtl_suggest_quantity2}}" class="suggest_qty_2 form-control erp-form-control-sm validNumber" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_demand_qty]" data-id="pd_demand_qty" value="{{$dtl->demand_dtl_demand_quantity}}" title="{{$dtl->demand_dtl_demand_quantity}}" class="tb_moveIndex stock_amount form-control erp-form-control-sm pd_demand_qty validNumber"></td>
                                            <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>
                                        </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="tableTotal">
                            <tbody>
                            <tr>
                                <td><div class="t_total_label">Total Qty:</div></td>
                                <td class="text-right"><span class="t_gross_total t_total">0</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <label class="col-lg-2">Notes:</label>
                    <div class="col-lg-10">
                        <textarea type="text" rows="3" name="demand_notes" maxlength="255" class="form-control erp-form-control-sm">{{isset($notes)?$notes:''}}</textarea>
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

    <script src="{{ asset('js/pages/js/inventory/stock-request.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script>
        var formcase = '{{$case}}';
    </script>
    <script>

    $('button[type="submit"]').click(function(e){
        if($('.erp_form__grid_body>tr').length <= 0){
            toastr.error("Please add product");
            return false;
        }
    });
    $('.physical_stock').keyup(function (e) {
        var tr = $(this).parents('tr');
        var stock = tr.find('td>.pd_store_stock').val();
        var physical = $(this).val();
        if(stock == physical){
            tr.find('td>.stock_match').val('Yes');
        }else{
            tr.find('td>.stock_match').val('No');
        }
        /*
        if($(this).val() != ""){
            if(e.which === 13){
                $.ajax({
                    type:'GET',
                    url:'/demand/itembarcode/'+code,
                    data:{},
                    success: function(response, status){
                        var branch = {{auth()->user()->branch_id}};
                        if(status)
                        {
                            for(var i=0;response['data']['barcode_dtl'].length>i;i++){
                                if(branch == response['data']['barcode_dtl'][i]['branch_id']){
                                    var min_shelf_stock = response['data']['barcode_dtl'][i]['product_barcode_shelf_stock_min_qty'];
                                    var max_stock_limit = response['data']['barcode_dtl'][i]['product_barcode_stock_limit_max_qty'];
                                    var SQty1 = max_stock_limit - stock;
                                    if(stock == min_shelf_stock){
                                        tr.find('td> .stock_match').val('Yes');
                                    }else{
                                        tr.find('td> .stock_match').val('No');
                                    }
                                    tr.find('td> .suggest_qty_1').val(SQty1);
                                }
                            }
                        }
                    }
                });
            }
        }
        */
    });
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
                'id':'pd_physical_stock',
                'fieldClass':'physical_stock tb_moveIndex validNumber'
            },
            {
                'id':'pd_store_stock',
                'fieldClass':'pd_store_stock validNumber',
                'readonly':true
            },
            {
                'id':'pd_stock_match',
                'fieldClass':'stock_match validNumber',
                'readonly':true
            },
            {
                'id':'pd_suggest_qty_1',
                'fieldClass':'suggest_qty_1  validNumber',
                'readonly':true
            },
            {
                'id':'pd_suggest_qty_2',
                'fieldClass':'suggest_qty_1 validNumber',
                'readonly':true
            },
            {
                'id':'pd_demand_qty',
                'fieldClass':'pd_demand_qty stock_amount tb_moveIndex validNumber'
            }
        ];
        var  arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/product-inline-ajax-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script>
        $(document).on('click','#addData',function(){
            totalAllDemandQty();
        })
        function totalAllDemandQty(){
            var t = 0;
            var v = 0;
            $( ".erp_form__grid_body>tr" ).each(function( index ) {
                v = $(this).find('td>.pd_demand_qty').val();
                v = (v == '' || v == undefined)? 0 : v.replace( /,/g, '');
                t += parseFloat(v);
            });
            t = t.toFixed(3);
            $('.t_gross_total').html(t);
        }
        $(document).on('keyup','.pd_demand_qty', function(){
            totalAllDemandQty();
        });
    </script>
@endsection


