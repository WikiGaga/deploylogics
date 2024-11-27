@extends('layouts.layout')
@section('title', 'Purchasing')

@section('pageCSS')
    <style>
        .rotate{
            -moz-transition: all 0.5s linear;
            -webkit-transition: all 0.5s linear;
            transition: all 0.5s linear;
        }
        .rotate.down{
            -ms-transform: rotate(180deg);
            -moz-transform: rotate(180deg);
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
        }
</style>
@endsection

@section('content')
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code  = $data['document_code'];
                $date =  date('d-m-Y');
                $id = "";
            }
            if($case == 'edit'){
                $current = $data['current'];
                $id = $current->purchasing_id;
                $code = $current->purchasing_code;
                $date = $current->purchasing_entry_date;
                $saleman = $current->salesman_id;
            }
            $form_type = $data['form_type'];

    //dd($current->dtl->toArray());
    @endphp
@permission($data['permission'])
<!--begin::Form-->
<form id="purchasing_form" class="master_form kt-form" method="post" action=" {{action('Purchase\PurchasingController@store',isset($id)?$id:'')}} ">
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
                                    <input type="text" name="document_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{$date}}" id="kt_datepicker_3"  />
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
                            <label class="col-lg-4 erp-col-form-label">Purchaser: <span class="required">*</span></label>
                            <div class="col-lg-8">
                                <div class="erp-select2">
                                    <select name="salesman" id="salesman" class="form-control erp-form-control-sm moveIndex kt-select2">
                                        <option value="">Select</option>
                                        @if($case == 'edit')
                                            @php $$saleman = isset($$saleman)?$$saleman:""; @endphp
                                            @foreach($data['users'] as $user)
                                                <option value="{{$user->id}}" {{$user->id ==$saleman ?"selected":""}}>{{$user->name}} - {{$user->email}}</option>
                                            @endforeach
                                        @else
                                            @foreach($data['users'] as $user)
                                                <option value="{{$user->id}}">{{ucwords(strtolower(strtoupper($user->name)))}} - {{$user->email}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block" style="max-height:300px;overflow: auto;">
                    <table id="request_data" class="data_table table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed">
                        <thead>
                        <tr>
                            <th width="15%">Stock Request No</th>
                            <th width="15%">Date</th>
                            <th width="15%">Branch From</th>
                            <th width="15%">Branch To</th>
                            <th width="30%">Notes</th>
                            <th width="10%">
                                {{--<label class="kt-checkbox kt-checkbox--success">
                                    <input type="checkbox" id="checkAll" class="">
                                    <span></span>
                                </label>
                                Select--}}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($case == 'new')
                            @foreach($data['stock_request_list'] as $list)
                                <tr>
                                    <td>{{$list->demand_no}}</td>
                                    <td>{{date('d-m-Y',strtotime($list->demand_date))}}</td>
                                    <td>{{$list->branch->branch_name}}</td>
                                    <td>{{auth()->user()->branch->branch_name}}</td>
                                    <td>{{$list->demand_notes}}</td>
                                    <td class="text-center">
                                        <label class="kt-checkbox kt-checkbox--success">
                                            <input type="checkbox" name="request_code[]" value="{{$list->demand_id}}" data-id="{{$list->demand_id}}" class="select_checkbox">
                                            <span></span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        @if($case == 'edit')
                            @foreach($data['stock_request_list'] as $list)
                                <tr>
                                    <td>{{$list->demand_no}}</td>
                                    <td>{{date('d-m-Y',strtotime($list->demand_date))}}</td>
                                    <td>{{$list->branch->branch_name}}</td>
                                    <td>{{auth()->user()->branch->branch_name}}</td>
                                    <td>{{$list->demand_notes}}</td>
                                    <td class="text-center">
                                        <label class="kt-checkbox kt-checkbox--success">
                                            <input type="checkbox" readonly name="request_code[]" onchange='this.checked = true;' {{in_array($list->demand_id,$data['stock_ids'])?"checked":"disabled"}} value="{{$list->demand_id}}" data-id="{{$list->demand_id}}" class="select_checkbox">
                                            <span></span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                @if($case == 'new')
                    <div class="row">
                        <div class="col-lg-3">
                            <button type="button" id="get_demand_data" class="btn btn-primary btn-sm">GO <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                @endif
                <div id="demand_products_details" class="dpd kt-margin-t-15">
                </div>
                <div class="row kt-margin-t-15">
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
    <script>
        var cd  = console.log;
        var formcase = '{{$case}}'; // new or edit
    </script>
    <script src="{{ asset('js/pages/js/purchase/purchasing.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/common/table-calculations-multi.js') }}" type="text/javascript"></script>
    <script>
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
                'id':'quantity',
                'fieldClass':'tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'foc_qty',
                'fieldClass':'tb_moveIndex validNumber'
            },
            {
                'id':'fc_rate',
                'fieldClass':'fc_rate tb_moveIndex validNumber'
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate tb_moveIndex validNumber'
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'dis_perc',
                'fieldClass':'tblGridCal_discount_perc tb_moveIndex validNumber'
            },
            {
                'id':'dis_amount',
                'fieldClass':'tblGridCal_discount_amount tb_moveIndex validNumber'
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
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];

    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_multi.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>

    <script>
        $(document).on('click','.product_copy',function(){
            var thix = $(this);
            var product_block = thix.parents('.product_block');
            var barcode = product_block.find('input[type="hidden"].copy_barcode').val();
            product_block.find('.erp_form__grid_header>tr th>.erp_form__grid_th_input>.pd_barcode').val(barcode);
            var keycodeNo = 13;
            var tr = product_block.find('.erp_form__grid_header>tr');
            var form_type = $('#form_type').val();
            var formData = {
                form_type : form_type,
                val : barcode,
            }
            initBarcode(keycodeNo,tr,form_type,formData);
        });
        $(document).on('click','#get_demand_data',function(){
            var url = '/purchasing/product_data';
            get_stock_data(url);
        });
        $(document).on('click','.product_table_toggle',function(){
            var thix = $(this);
            var product_block = thix.parents('.product_block');
            thix.find('i').toggleClass('down');
            product_block.find('.erp_form___block').slideToggle(500);
        });
        function get_stock_data(url,purchasing_id=null){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var ids = [];
            $('.select_checkbox:checked').each(function(index){
                ids[index] = $(this).attr('data-id');
            })
            var formData = {
                ids : ids,
                purchasing_id : purchasing_id,
            }
            if(ids.length != 0){
                $('#demand_products_details').load(url,formData,function(responseTxt, statusTxt, xhr){
                    if(statusTxt == "success")
                        toastr.success('Data loaded successfully');
                    if(statusTxt == "error")
                        toastr.error("Error: " + xhr.status + ": " + xhr.statusText);
                });
            }else{
                toastr.error("Please select stock request code");
                $('#demand_products_details').html("");
            }
        }
        @if($case == 'edit')
            var url = '/purchasing/product_data_edit';
            var purchasing_id = "{{$id}}";
            get_stock_data(url,purchasing_id);
        @endif
    </script>
@endsection


