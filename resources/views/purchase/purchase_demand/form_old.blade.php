@extends('layouts.template')
@section('title', 'Purchase Demand')

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
                $saleman = $data['current']->salesman_id;
                $notes = $data['current']->demand_notes;
                $dtls = isset($data['current']->dtls)? $data['current']->dtls:[];
            }
    @endphp
<!--begin::Form-->
<form id="purchase_demand_form" class="master_form kt-form" method="post" action="{{ action('Purchase\PurchaseDemandController@store',isset($id)?$id:'') }}">
@csrf
    <input type="hidden" value='purc_demand' id="form_type">
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
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
                            <label class="col-lg-3 erp-col-form-label">Supplier:</label>
                            <div class="col-lg-9">
                                <div class="input-group open-modal-group">
                                    <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                                    <i class="la la-minus-circle"></i>
                                            </span>
                                    </div>
                                    <input type="text" id="supplier_name" value="{{isset($supplier_name)?$supplier_name:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" name="supplier_name" autocomplete="off" class="open-inline-help form-control erp-form-control-sm moveIndex">
                                    <input type="hidden" id="supplier_id" value="{{isset($supplier_name)?$supplier_id:''}}" name="supplier_id">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Demand By:</label>
                            <div class="col-lg-8">
                                <div class="erp-select2">
                                    <select name="salesman" id="salesman" class="form-control erp-form-control-sm moveIndex moveIndex2 kt-select2">
                                        <option value="">Select</option>
                                        @if($case == 'edit')
                                            @php $$saleman = isset($$saleman)?$$saleman:""; @endphp
                                            @foreach($data['users'] as $user)
                                                <option value="{{$user->id}}" {{$user->id ==$saleman ?"selected":""}}>{{$user->name}}</option>
                                            @endforeach
                                        @else
                                            @foreach($data['users'] as $user)
                                                <option value="{{$user->id}}" {{Auth::user()->id == $user->id?'selected':''}}>{{$user->name}}</option>
                                            @endforeach
                                        @endif
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
                        </div>
                    </div>
                </div>
                <div class="form-group-block row table_ERP_grid_row" style="overflow: auto;">
                    <table id="ProductDemandForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                        <thead>
                        <tr>
                            <th style="width: 46.6667px;">Sr No</th>
                            <th style="width: 86.6667px;">
                                Barcode
                                <button type="button" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                    <i class="la la-barcode"></i>
                                </button>
                            </th>
                            <th style="width: 153.333px;">Product Name</th>
                            <th style="width: 63.3333px;">UOM</th>
                            <th style="width: 35.3333px;">Packing</th>
                            <th style="width: 53.3333px;">Physical Stock</th>
                            <th style="width: 51.3333px;">Store Stock</th>
                            <th style="width: 53.3333px;">Stock Match</th>
                            <th style="width: 57.3333px;">Suggest Qty 1</th>
                            <th style="width: 57.3333px;">Suggest Qty 2</th>
                            <th style="width: 51.3333px;">Demand Qty</th>
                            <th style="width: 65.3333px;">WIP LPO Stock</th>
                            <th style="width: 63.3333px;">Pur.Ret in Waiting</th>
                            <th style="width: 37.3333px;">Action</th>
                        </tr>
                        <tr id="dataEntryForm">
                            <td>
                                <input readonly id="pd_sr_no" type="text" class="form-control erp-form-control-sm">
                                <input readonly id="product_id" type="hidden" class="product_id form-control erp-form-control-sm">
                                <input readonly id="product_barcode_id" type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                <input readonly id="uom_id" type="hidden" class="uom_id form-control erp-form-control-sm">
                            </td>
                            <td><input id="pd_barcode" type="text" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="open-inline-help pd_barcode moveIndex2 form-control erp-form-control-sm" autocomplete="off"></td>
                            <td><input readonly id="pd_product_name" type="text" class="pd_product_name form-control erp-form-control-sm"></td>
                            <td>
                                <select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" id="pd_uom">
                                    <option value="">Select</option>
                                </select>
                            </td>
                            <td><input readonly id="pd_packing" type="text" class="pd_packing form-control erp-form-control-sm"></td>
                            <td><input id="pd_physical_stock" type="text" class="moveIndex physical_stock form-control erp-form-control-sm validNumber"></td>
                            <td><input readonly id="pd_store_stock" type="text" class="pd_store_stock form-control erp-form-control-sm text-right"></td>
                            <td><input readonly id="pd_stock_match" type="text" class="stock_match form-control erp-form-control-sm"></td>
                            <td><input readonly id="pd_suggest_qty_1" type="text" class="suggest_qty_1 form-control erp-form-control-sm validNumber"></td>
                            <td><input readonly id="pd_suggest_qty_2" type="text" class="suggest_qty_2 form-control erp-form-control-sm validNumber"></td>
                            <td><input id="pd_demand_qty" type="text" class="moveIndex pd_demand_qty stock_amount form-control erp-form-control-sm validNumber"></td>
                            <td><input readonly id="pd_wiplpo_stock" type="text" class="form-control erp-form-control-sm validNumber"></td>
                            <td><input readonly id="pd_pur_ret" type="text" class="form-control erp-form-control-sm validNumber"></td>
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
                                        <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][pd_sr_no]" title="{{$loop->iteration}}" class="form-control erp-form-control-sm handle" readonly>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$dtl->product->product_id}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{$dtl->product_barcode_id}}" class="product_barcode_id form-control erp-form-control-sm">
                                        <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{$dtl->uom->uom_id}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][demand_dtl_id]" data-id="demand_dtl_id" value="{{$dtl->demand_dtl_id}}" class="demand_dtl_id form-control erp-form-control-sm handle" readonly>
                                    </td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_barcode]" data-id="pd_barcode" value="{{$dtl->barcode->product_barcode_barcode}}" title="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class=" form-control erp-form-control-sm" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_product_name]" data-id="pd_product_name" value="{{$dtl->product->product_name}}" title="{{$dtl->product->product_name}}" class="pd_product_name form-control erp-form-control-sm" readonly></td>
                                    <td>
                                        <select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][pd_uom]" title="{{$dtl->uom->uom_name}}">
                                            <option value="{{$dtl->uom->uom_id}}">{{$dtl->uom->uom_name}}</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_packing]" data-id="pd_packing" value="{{$dtl->product_barcode_packing}}" title="{{$dtl->product_barcode_packing}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_physical_stock]" data-id="pd_physical_stock" value="{{$dtl->demand_dtl_physical_stock}}" title="{{$dtl->demand_dtl_physical_stock}}" class="moveIndex physical_stock moveIndex form-control erp-form-control-sm validNumber"></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_store_stock]" data-id="pd_store_stock" value="{{$dtl->demand_dtl_store_stock}}" title="{{$dtl->demand_dtl_store_stock}}" class="pd_store_stock form-control erp-form-control-sm text-right" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_stock_match]" data-id="pd_stock_match" value="{{$dtl->demand_dtl_stock_match}}" title="{{$dtl->demand_dtl_stock_match}}" class="stock_match form-control erp-form-control-sm" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_suggest_qty_1]" data-id="pd_suggest_qty_1" value="{{$dtl->demand_dtl_suggest_quantity1}}" title="{{$dtl->demand_dtl_suggest_quantity1}}" class="suggest_qty_1 form-control erp-form-control-sm validNumber" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_suggest_qty_2]" data-id="pd_suggest_qty_2" value="{{$dtl->demand_dtl_suggest_quantity2}}" title="{{$dtl->demand_dtl_suggest_quantity2}}" class="suggest_qty_2 form-control erp-form-control-sm validNumber" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_demand_qty]" data-id="pd_demand_qty" value="{{$dtl->demand_dtl_demand_quantity}}" title="{{$dtl->demand_dtl_demand_quantity}}" class="moveIndex stock_amount form-control erp-form-control-sm pd_demand_qty validNumber"></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_wiplpo_stock]" data-id="pd_wiplpo_stock" value="{{$dtl->demand_dtl_wip_lpo_stock}}" title="{{$dtl->demand_dtl_wip_lpo_stock}}" class="form-control erp-form-control-sm validNumber" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][pd_pur_ret]" data-id="pd_pur_ret" value="{{$dtl->demand_dtl_pur_ret_in_waiting}}" title="{{$dtl->demand_dtl_pur_ret_in_waiting}}" class="form-control erp-form-control-sm validNumber" readonly></td>
                                    <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
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
                        <textarea type="text" rows="3" name="demand_notes" maxlength="255" class="form-control erp-form-control-sm moveIndex">{{isset($notes)?$notes:''}}</textarea>
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
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/table-calculations.js') }}" type="text/javascript"></script>
    <script>
        if($( window ).width() <= 1024){
            var MobHiddenTd = '3,4,6,7,8,9,11,12';
            var hiddenFieldsFormName = 'purchaseDemandFormMob';
        }else{
            var hiddenFieldsFormName = 'purchaseDemandForm';
        }
    </script>
    <script src="{{ asset('js/pages/js/hidden-fields.js') }}" type="text/javascript"></script>
    <script>
    var formcase = '{{$case}}';
    if(formcase == 'edit'){unreaduom();}
    function unreaduom(){
        $('#repeated_data>tr').each(function () {
            $(this).find('td>.uomHid').removeClass("pd_uom");
            $(this).find('td>.uomHid').addClass("unreaduom");
        });
    }

    $('button[type="submit"]').click(function(e){
        if($('tbody#repeated_data>tr').length <= 0){
            toastr.error("Please add product");
            return false;
        }
    });
    $('.physical_stock').keyup(function (e) {
        var tr = $(this).parents('tr');
        var stock = tr.find('td>.pd_store_stock').val();
        var physical = $(this).val();
        if(stock == physical){
            tr.find('td> .stock_match').val('Yes');
        }else{
            tr.find('td> .stock_match').val('No');
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
    var formcase = '{{$case}}';
    var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
    var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode open-inline-help moveIndex',
                'require':true,
                'readonly':true
                //'data-url' : productHelpUrl
            },
            {
                'id':'pd_product_name',
                'fieldClass':'pd_product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
            {
                'id':'pd_uom',
                'fieldClass':'pd_uom',
                'readonly':true,
                'type':'select'
            },
            {
                'id':'pd_packing',
                'fieldClass':'pd_packing',
                'readonly':true
            },
            {
                'id':'pd_physical_stock',
                'fieldClass':'moveIndex physical_stock validNumber'
            },
            {
                'id':'pd_store_stock',
                'fieldClass':'pd_store_stock validNumber',
                'readonly':true
            },
            {
                'id':'pd_stock_match',
                'fieldClass':'stock_match',
                'readonly':true
            },
            {
                'id':'pd_suggest_qty_1',
                'fieldClass':'suggest_qty_1 validNumber',
                'readonly':true
            },
            {
                'id':'pd_suggest_qty_2',
                'fieldClass':'validNumber',
                'readonly':true
            },
            {
                'id':'pd_demand_qty',
                'fieldClass':'moveIndex pd_demand_qty stock_amount validNumber'
            },
            {
                'id':'pd_wiplpo_stock',
                'fieldClass':'validNumber',
                'readonly':true
            },
            {
                'id':'pd_pur_ret',
                'fieldClass':'validNumber',
                'readonly':true
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/product-inline-ajax2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script>
        $(document).on('click','#addData',function(){
            totalAllDemandQty();
        })
        function totalAllDemandQty(){
            var t = 0;
            var v = 0;
            $( "#repeated_data>tr" ).each(function( index ) {
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


