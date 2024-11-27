@extends('layouts.template')
@section('title', 'Purchase Demand Approve')

@section('pageCSS')
@endsection

@section('content')

    @php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : '';
    if ($case == 'new') {
        $length = 0;
    }
    if ($case == 'edit') {
        $dtls = $data['current'];
        $id = $data['id'];
    }
    @endphp
    <form id="demand_approve_form" class="kt-form" method="post" action="{{ action('Purchase\PurchaseDemandApproveController@store' , isset($id) ? $id : "") }}">
    @csrf
    <!-- begin:: Content -->
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
                                        @if(isset($data['id']))
                                            {{$data['code_date']->demand_approval_dtl_code}}
                                        @else
                                            {{$data['document_code']}}
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-8"></div>
                                <div class="col-lg-4">
                                    <div class="input-group date">
                                        @if(isset($data['id']))
                                            @php $date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$data['code_date']->demand_approval_dtl_date)))); @endphp
                                        @else
                                            @php $date =  date('d-m-Y'); @endphp
                                        @endif
                                        <input type="text" name="demand_approve_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{$date}}" {{--id="kt_datepicker_3"--}} />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    {{--{{dd($dtls->toArray())}}--}}
                    @if(isset($data['id']))
                    <div class="form-group-block" style="max-height:300px;overflow: auto;">
                        <table id="demand_data" class="data_table table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed">
                            <thead>
                            <tr>
                                <th>Demand No</th>
                                <th>Demand Date</th>
                                <th>Demand By</th>
                                <th>Branch</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data['demand_list'] as $demand_list)
                                <tr>
                                    <td>{{$demand_list->demand_no}}</td>
                                    <td>{{date('d-m-Y',strtotime($demand_list->demand_date))}}</td>
                                    <td>{{$demand_list->name}}</td>
                                    <td>{{$demand_list->branch_name}}</td>
                                    <td>{{$demand_list->demand_notes}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group-block" style="overflow: auto;">
                        <table id="ProductDemandDtlForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                            <thead>
                            <tr>
                                <th width="5%">Sr No</th>
                                <th width="5%">Demand No</th>
                                <th width="8%">Branch</th>
                                <th width="10%">Product Name</th>
                                <th width="4%">UOM</th>
                                <th width="4%">Packing</th>
                                <th width="4%">Physical Stock</th>
                                <th width="4%">Store Stock</th>
                                <th width="4%">Stock Match</th>
                                <th width="3%">Suggest Qty 1</th>
                                <th width="3%">Suggest Qty 2</th>
                                <th width="3%">Purchase Rate</th>
                                <th width="3%">Demand Qty</th>
                                <th width="7%">WIP LPO Stock</th>
                                <th width="7%">Pur.Ret in Waiting</th>
                                <th width="4%">Aprov qty</th>
                                <th width="8%">Notes</th>
                                <th width="8%">Remarks</th>
                                <th width="3%">
                                    <label class="kt-radio kt-radio--brand" style="padding-left: 17px; top: -5px;">
                                        <input style="left:0;" type="radio" id="pendingAll" name="checkAllgrid" value="pending" disabled>
                                        <span></span>
                                    </label> <div class="noselect">Pnding</div>
                                </th>
                                <th width="3%">
                                    <label class="kt-radio kt-radio--success" style="padding-left: 17px; top: -5px;">
                                        <input style="left:0;" type="checkbox" id="approveAll" name="checkAllgrid" value="approve" disabled>
                                        <span></span>
                                    </label> <div class="noselect">Aprv</div>
                                </th>
                                <th width="3%">
                                    <label class="kt-radio kt-radio--danger" style="padding-left: 17px; top: -5px;">
                                        <input style="left:0;" type="radio" id="rejectAll" name="checkAllgrid" value="reject" disabled>
                                        <span></span>
                                    </label> <div class="noselect">Rjct</div>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="repeated_data">
                                @if($case == 'edit')
                                    @foreach($dtls as $dtl)
                                        <tr id="81402121091214">
                                            <td class="handle">
                                                <i class="fa fa-arrows-alt-v handle"></i>
                                                <input type="text" id="sr_no" name="pd[{{ $loop->iteration }}][sr_no]" value="{{ $loop->iteration }}" class="form-control erp-form-control-sm handle" readonly="">
                                                <input type="hidden" id="product_id" data-id="product_id" name="pd[{{ $loop->iteration }}][product_id]" value="{{ $dtl->product_id }}" class="product_id form-control erp-form-control-sm handle" readonly="">
                                                <input type="hidden" id="uom_id" data-id="uom_id" name="pd[{{ $loop->iteration }}][uom_id]" value="{{ $dtl->uom_id }}" class="form-control erp-form-control-sm handle" readonly="">
                                                <input type="hidden" id="branch_id" data-id="branch_id" name="pd[{{ $loop->iteration }}][branch_id]" value="{{ $dtl->branch_id }}" class="form-control erp-form-control-sm handle" readonly="">
                                                <input type="hidden" id="bar_code" data-id="bar_code" name="pd[{{ $loop->iteration }}][bar_code]" value="{{ $dtl->product_barcode_barcode }}" class="form-control erp-form-control-sm handle" readonly="">
                                                <input type="hidden" id="demand_id" data-id="demand_id" name="pd[{{ $loop->iteration }}][demand_id]" value="{{ $dtl->demand_id }}" class="form-control erp-form-control-sm handle" readonly="">
                                                <input type="hidden" id="demand_dtl_id" data-id="demand_dtl_id" name="pd[{{ $loop->iteration }}][demand_dtl_id]" value="{{ $dtl->demand_dtl_id }}" class="form-control erp-form-control-sm handle" readonly="">
                                                <input type="hidden" id="product_barcode_id" data-id="product_barcode_id" name="pd[{{ $loop->iteration }}][product_barcode_id]" value="{{ $dtl->product_barcode_id }}" class="product_barcode_id form-control erp-form-control-sm">
                                                <input type="hidden" id="notes_id" data-id="notes_id" name="pd[{{ $loop->iteration }}][notes_id]" value="{{ $dtl->demand_approval_dtl_remarks_id }}" class="form-control erp-form-control-sm handle" readonly="">
                                            </td>
                                            <td><input type="text" id="demand_no" data-id="demand_no" name="pd[{{ $loop->iteration }}][demand_no]" value="{{ $dtl->demand->demand_no }}" title="{{ $dtl->demand->demand_no }}" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" id="branch_name" data-id="branch_name" name="pd[{{ $loop->iteration }}][branch_name]" value="{{ $dtl->branch->branch_name }}" title="{{ $dtl->branch->branch_name }}" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" id="product_name" data-id="product_name"  name="pd[{{ $loop->iteration }}][product_name]" value="{{ $dtl->product->product_name }}" title="{{ $dtl->product->product_name }}" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" id="uom" data-id="uom" name="pd[{{ $loop->iteration }}][uom_name]" value="{{ $dtl->uom->uom_name }}" title="{{ $dtl->uom->uom_name }}" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" id="packing_name" data-id="packing_name" name="pd[{{ $loop->iteration }}][packing_name]" value="{{ $dtl->demand_approval_dtl_packing }}" title="{{ $dtl->demand_approval_dtl_packing }}" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" id="physical_stock" data-id="physical_stock" name="pd[{{ $loop->iteration }}][physical_stock]" value="{{ $dtl->demand_approval_dtl_physical_stock }}" title="{{ $dtl->demand_approval_dtl_physical_stock }}" class="form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td><input type="text" id="store_stock" data-id="store_stock" name="pd[{{ $loop->iteration }}][store_stock]" value="{{ $dtl->demand_approval_dtl_store_stock }}" title="{{ $dtl->demand_approval_dtl_store_stock }}" class="form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td><input type="text" id="stock_match" data-id="stock_match" name="pd[{{ $loop->iteration }}][stock_match]" value="{{ $dtl->demand_approval_dtl_stock_match }}" title="No" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" id="suggest_qty_1" data-id="suggest_qty_1" name="pd[{{ $loop->iteration }}][suggest_qty_1]" value="{{ $dtl->demand_approval_dtl_suggest_quantity1 }}" title="{{ $dtl->demand_approval_dtl_suggest_quantity1 }}" class="form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td><input type="text" id="suggest_qty_2" data-id="suggest_qty_2" name="pd[{{ $loop->iteration }}][suggest_qty_2]" value="{{ $dtl->demand_approval_dtl_suggest_quantity2 }}" title="{{ $dtl->demand_approval_dtl_suggest_quantity2 }}" class="form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td><input type="text" id="purchase_rate" data-id="purchase_rate" name="pd[{{ $loop->iteration }}][purchase_rate]" value="{{ number_format($dtl->product_barcode_purchase_rate,3) }}" title="{{ number_format($dtl->product_barcode_purchase_rate,3) }}" class="form-control erp-form-control-sm purchase-rate validNumber validOnlyFloatNumber" readonly=""></td>
                                            <td><input type="text" id="demand_qty" data-id="demand_qty" name="pd[{{ $loop->iteration }}][demand_qty]" value="{{ $dtl->demand_approval_dtl_demand_qty }}" title="{{ $dtl->demand_approval_dtl_demand_qty }}" class="form-control erp-form-control-sm demand-qty validNumber validOnlyFloatNumber" readonly=""></td>
                                            <td><input type="text" id="wiplpo_stock" data-id="wiplpo_stock" name="pd[{{ $loop->iteration }}][wiplpo_stock]" value="{{ $dtl->demand_approval_dtl_wip_lpo_stock }}" title="{{ $dtl->demand_approval_dtl_wip_lpo_stock }}" class="form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td><input type="text" id="pur_ret" data-id="pur_ret" name="pd[{{ $loop->iteration }}][pur_ret]" value="{{ $dtl->demand_approval_dtl_pur_ret_in_waiting }}" title="{{ $dtl->demand_approval_dtl_pur_ret_in_waiting }}" class="form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td><input type="text" id="approve_qty" data-id="approve_qty" name="pd[{{ $loop->iteration }}][approve_qty]" value="{{ $dtl->demand_approval_dtl_approve_qty }}" title="{{ $dtl->demand_approval_dtl_approve_qty }}" class="moveIndex form-control erp-form-control-sm approv-qty validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" id="remarks" data-id="remarks" name="pd[{{ $loop->iteration }}][remarks]" value="{{ $dtl->demand_approval_dtl_notes }}" class="moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" id="notes" data-id="notes" data-url="{{ url('common/help-open/RejectReasonHelp') }}" name="pd[{{ $loop->iteration }}][notes]" value="{{ $dtl->demand_approval_dtl_remarks }}" class="open_js_modal moveIndex OnlyEnterAllow form-control erp-form-control-sm" readonly=""></td>
                                            <td class="text-center"><label class="kt-radio kt-radio--brand"><input type="radio" id="pending" value="pending" @if($dtl->demand_approval_dtl_approve_status == 'pending') checked @endif  name="pd[{{ $loop->iteration }}][action]" onclick="return false;"><span></span></label></td>
                                            <td class="text-center"><label class="kt-radio kt-radio--success"><input type="radio" id="approve" value="approved" @if($dtl->demand_approval_dtl_approve_status == 'approved') checked @endif name="pd[{{ $loop->iteration }}][action]" onclick="return false;"><span></span></label></td>
                                            <td class="text-center"><label class="kt-radio kt-radio--danger"><input type="radio" id="reject" value="reject" @if($dtl->demand_approval_dtl_approve_status == 'reject') checked @endif name="pd[{{ $loop->iteration }}][action]" onclick="return false;"><span></span></label></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="form-group-block" style="max-height:300px;overflow: auto;">
                        <table id="demand_data" class="data_table table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed">
                            <thead>
                            <tr>
                                <th>Demand No</th>
                                <th>Demand Date</th>
                                <th>Demand By</th>
                                <th>Branch</th>
                                <th>Supplier</th>
                                <th>Notes</th>
                                <th>
                                    <label class="kt-checkbox kt-checkbox--success">
                                        <input type="checkbox" id="checkAll" class="">
                                        <span></span>
                                    </label>
                                    Approve
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data['demand'] as $demand)
                                <tr>
                                    <td>{{$demand->demand_no}}</td>
                                    <td>{{date('d-m-Y',strtotime($demand->demand_date))}}</td>
                                    <td>{{isset($demand->name)?$demand->name:""}}</td>
                                    <td>{{isset($demand->branch_name)?$demand->branch_name:""}}</td>
                                    <td>{{isset($demand->supplier_name)?$demand->supplier_name:""}}</td>
                                    <td>{{$demand->demand_notes}}</td>
                                    <td>
                                        <label class="kt-checkbox kt-checkbox--success">
                                            <input type="checkbox" data-id="{{$demand->demand_id}}" class="approve_checkbock">
                                            <span></span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group-block" style="overflow: auto;">
                        <table id="ProductDemandDtlForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                            <thead>
                            <tr>
                                <th width="5%">Sr No</th>
                                {{--<th width="5%">Product Id</th>--}}
                                <th width="8%">Branch</th>
                                <th width="10%">Product Name</th>
                                <th width="4%">UOM</th>
                                <th width="4%">Packing</th>
                                <th width="4%">Physical Stock</th>
                                <th width="4%">Store Stock</th>
                                <th width="3%">Stock Match</th>
                                <th width="3%">Suggest Qty 1</th>
                                <th width="3%">Suggest Qty 2</th>
                                <th width="3%">Purchase Rate</th>
                                <th width="3%">Demand Qty</th>
                                <th width="7%">WIP LPO Stock</th>
                                <th width="7%">Pur.Ret in Waiting</th>
                                <th width="4%">Aprov qty</th>
                                <th width="8%">Notes</th>
                                <th width="8%">Remarks</th>
                                <th width="3%">
                                    <label class="kt-radio kt-radio--brand" style="padding-left: 17px; top: -5px;">
                                        <input style="left:0;" type="radio" id="pendingAll" name="checkAllgrid" value="pending" disabled>
                                        <span></span>
                                    </label> <div class="noselect">Pnding</div>
                                </th>
                                <th width="3%">
                                    <label class="kt-radio kt-radio--success" style="padding-left: 17px; top: -5px;">
                                        <input style="left:0;" type="checkbox" id="approveAll" name="checkAllgrid" value="approve" disabled>
                                        <span></span>
                                    </label> <div class="noselect">Aprv</div>
                                </th>
                                <th width="3%">
                                    <label class="kt-radio kt-radio--danger" style="padding-left: 17px; top: -5px;">
                                        <input style="left:0;" type="radio" id="rejectAll" name="checkAllgrid" value="reject" disabled>
                                        <span></span>
                                    </label> <div class="noselect">Rjct</div>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="repeated_data">

                            </tbody>
                        </table>
                    </div>
                    @endif
                    <div class="form-group row">
                        <div class="col-lg-12 text-right">
                            <h5>Total: <span id="gridTotalValue">0.000</span></h5>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 erp-col-form-label">Notes:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="3" name="demand_notes" maxlength="255" class="demand_approval_notes form-control erp-form-control-sm moveIndex"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/js/pages/js/purchase/demand_approval.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script>
        $(document).ready(function(e){
            calculateGridTotalValue();
        });
        $("#checkAll").click(function(){
            var that = $(this);
           // that.parents('table').css('pointer-events', 'none');
            if(this.checked == false){
                swal.fire({
                    title: 'Are you sure to unselect the demand no?',
                    text: "You have approve some qty against this demand no, your data will be lost!",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then(function(result) {
                    if (result.value) {
                        $('#demand_data>tbody>tr>td:last-child').find('input:checkbox').prop('checked', false);
                        $('#demand_data>tbody>tr>td').find('input:checkbox').each(function(){
                            var id = $(this).attr('data-id');
                            if($(this).is(":checked") == true) {
                                ajaxData(id);
                            }else{
                                $("#repeated_data>tr#"+id).remove();
                            }
                        })
                    }else{
                        that.prop('checked', true);
                    }
                });
            }else{
                $('#demand_data>tbody>tr>td:last-child').find('input:checkbox').not(this).prop('checked', true);
                $('#demand_data>tbody>tr>td').find('input:checkbox').each(function(){
                    var id = $(this).attr('data-id');
                    if($(this).is(":checked") == true) {
                        ajaxData(id);
                    }else{
                        $("#repeated_data>tr#"+id).remove();
                    }
                })
            }
        });
        function AddAutoApprovQty(){
            $('#repeated_data').on('click', '#approve', function (e) {
                var tr = $(this).parents('tr');
                var val_approve_qty = tr.find('#approve_qty').val();
                var val_deamnd_qty = tr.find('#demand_qty').val();
                if(val_approve_qty == ''){
                    tr.find('#approve_qty').val(val_deamnd_qty);
                }
            });
        }
        $('#demand_data').on('click', '.approve_checkbock', function (e) {
            var id = $(this).attr('data-id');
            var that = $(this);
            if($(this).is(":checked") == true){
                $('#repeated_data').append(spinner);
                setTimeout(function(){
                    ajaxData(id);
                },0);

            }else{
                var val_qty = $("#repeated_data>tr#"+id).find('td> .approv-qty').val();
                if(val_qty !=''){
                    swal.fire({
                    title: 'Are you sure to unselect the demand no?',
                    text: "You have approve some qty against this demand no, your data will be lost!",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                    }).then(function(result) {
                        if (result.value) {
                            $("#repeated_data>tr#"+id).remove();
                            calculateGridTotalValue();
                        }else{
                            that.prop("checked", true);
                        }
                    });
                }else{
                    $("#repeated_data>tr#"+id).remove();
                }
            }

        });
        function ajaxData(id){
            $.ajax({
                type:'GET',
                url:'/demand-approve/detail/'+id,
                data:{},
                beforeSend:function(){
                  //  $('table#demand_data').css('pointer-events', 'none');
                },
                complete:function(){
                    //  $('table#demand_data').css('pointer-events', '');
                },
                success: function(response, status){
                    $('#ProductDemandDtlForm>thead>tr>th>label>input').attr('disabled',false);
                    $('#ProductDemandDtlForm>thead>tr>th>label>input#pending').attr('checked',true);
                    $('#repeated_data').find("#spinner").remove();
                    var tr = "";
                    var val = "";
                    function notNull(val){
                        if(val == null){
                            return "";
                        }else{
                            return val;
                        }
                    }
                    for(var i=0; response.length > i; i++){
                        var tb_length = $('#repeated_data>tr[id]:not("#spinner")').length;
                        var total_length = i+1+ parseInt(tb_length);
                        var sr_no = i+1+parseInt(tb_length);
                        tr += '<tr id="'+notNull(response[i]['demand_id'])+'">' +
                            '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                            '    <input type="text" id="sr_no" name="pd['+total_length+'][sr_no]"  value="'+sr_no+'"  class="form-control erp-form-control-sm handle" readonly>' +
                            '    <input type="hidden" id="product_id" data-id="product_id" name="pd['+total_length+'][product_id]" value="'+notNull(response[i]['product_id'])+'" class="product_id form-control erp-form-control-sm handle" readonly>' +
                            '    <input type="hidden" id="uom_id" data-id="uom_id" name="pd['+total_length+'][uom_id]" value="'+notNull(response[i]['demand_dtl_uom'])+'" class="form-control erp-form-control-sm handle" readonly>' +
                            '    <input type="hidden" id="branch_id" data-id="branch_id" name="pd['+total_length+'][branch_id]" value="'+notNull(response[i]['branch_id'])+'" class="form-control erp-form-control-sm handle" readonly>' +
                            '    <input type="hidden" id="bar_code" data-id="bar_code" name="pd['+total_length+'][bar_code]" value="'+notNull(response[i]['barcode']['product_barcode_barcode'])+'" class="pd_barcode form-control erp-form-control-sm handle" readonly>' +
                            '    <input type="hidden" id="demand_id" data-id="demand_id" name="pd['+total_length+'][demand_id]" value="'+notNull(response[i]['demand_id'])+'" class="form-control erp-form-control-sm handle" readonly>' +
                            '    <input type="hidden" id="demand_dtl_id" data-id="demand_dtl_id" name="pd['+total_length+'][demand_dtl_id]" value="'+notNull(response[i]['demand_dtl_id'])+'" class="form-control erp-form-control-sm handle" readonly>' +
                            '    <input type="hidden" id="product_barcode_id" data-id="product_barcode_id" name="pd['+total_length+'][product_barcode_id]" value="'+notNull(response[i]['product_barcode_id'])+'" class="product_barcode_id form-control erp-form-control-sm">'+
                            '    <input type="hidden" id="notes_id" data-id="notes_id" name="pd['+total_length+'][notes_id]" value="" class="form-control erp-form-control-sm handle" readonly>' +
                            '</td>' +
                            '<td><input type="text" id="branch_name" name="pd['+total_length+'][branch_name]" value="'+notNull(response[i]['branch']['branch_name'])+'" title="'+notNull(response[i]['branch']['branch_name'])+'" class="form-control erp-form-control-sm" readonly></td>' +
                            '<td><input type="text" id="product_name" name="pd['+total_length+'][product_name]" data-id="product_name" value="'+notNull(response[i]['product']['product_name'])+'" title="'+notNull(response[i]['product']['product_name'])+'" class="form-control erp-form-control-sm" readonly></td>' +
                            '<td><input type="text" id="uom" name="pd['+total_length+'][uom_name]" value="'+notNull(response[i]['uom']['uom_name'])+'" title="'+notNull(response[i]['uom']['uom_name'])+'" class="form-control erp-form-control-sm" readonly></td>' +
                            '<td><input type="text" id="packing" name="pd['+total_length+'][packing_name]" value="'+notNull(response[i]['demand_dtl_packing'])+'" title="'+notNull(response[i]['demand_dtl_packing'])+'" class="form-control erp-form-control-sm" readonly></td>' +
                            '<td><input type="text" id="physical_stock" name="pd['+total_length+'][physical_stock]" value="'+notNull(response[i]['demand_dtl_physical_stock'])+'" title="'+notNull(response[i]['demand_dtl_physical_stock'])+'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
                            '<td><input type="text" id="store_stock" name="pd['+total_length+'][store_stock]" value="'+notNull(response[i]['demand_dtl_store_stock'])+'" title="'+notNull(response[i]['demand_dtl_store_stock'])+'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
                            '<td><input type="text" id="stock_match" name="pd['+total_length+'][stock_match]" value="'+notNull(response[i]['demand_dtl_stock_match'])+'" title="'+notNull(response[i]['demand_dtl_stock_match'])+'" class="form-control erp-form-control-sm" readonly></td>' +
                            '<td><input type="text" id="suggest_qty_1" name="pd['+total_length+'][suggest_qty_1]" value="'+notNull(response[i]['demand_dtl_suggest_quantity1'])+'" title="'+notNull(response[i]['demand_dtl_suggest_quantity1'])+'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
                            '<td><input type="text" id="suggest_qty_2" name="pd['+total_length+'][suggest_qty_2]" value="'+notNull(response[i]['demand_dtl_suggest_quantity2'])+'" title="'+notNull(response[i]['demand_dtl_suggest_quantity2'])+'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
                            '<td><input type="text" id="purchase_rate" name="pd['+total_length+'][purchase_rate]" value="'+notNull(response[i]['purchase_rate'])+'" title="'+notNull(response[i]['purchase_rate'])+'" class="form-control erp-form-control-sm purchase-rate validNumber validOnlyFloatNumber" readonly></td>' +
                            '<td><input type="text" id="demand_qty" name="pd['+total_length+'][demand_qty]" value="'+notNull(response[i]['demand_dtl_demand_quantity'])+'" title="'+notNull(response[i]['demand_dtl_demand_quantity'])+'" class="form-control erp-form-control-sm demand-qty validNumber validOnlyFloatNumber" readonly></td>' +
                            '<td><input type="text" id="wiplpo_stock" name="pd['+total_length+'][wiplpo_stock]" value="'+notNull(response[i]['demand_dtl_wip_lpo_stock'])+'" title="'+notNull(response[i]['demand_dtl_wip_lpo_stock'])+'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
                            '<td><input type="text" id="pur_ret" name="pd['+total_length+'][pur_ret]" value="'+notNull(response[i]['demand_dtl_pur_ret_in_waiting'])+'" title="'+notNull(response[i]['demand_dtl_pur_ret_in_waiting'])+'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
                            '<td><input type="text" id="approve_qty" name="pd['+total_length+'][approve_qty]" value="'+notNull(response[i]['demand_dtl_demand_quantity'])+'" title="'+notNull(response[i]['demand_dtl_demand_quantity'])+'" class="moveIndex form-control erp-form-control-sm approv-qty validNumber validOnlyFloatNumber" ></td>' +
                            '<td><input type="text" id="remarks" name="pd['+total_length+'][remarks]" class="moveIndex form-control erp-form-control-sm" ></td>' +
                            '<td><input type="text" id="notes" data-url="{{action('Common\DataTableController@helpOpen','RejectReasonHelp')}}"  name="pd['+total_length+'][notes]" class="open_js_modal moveIndex OnlyEnterAllow form-control erp-form-control-sm" readonly></td>' +
                            '<td class="text-center"><label class="kt-radio kt-radio--brand"><input type="radio" id="pending" value="pending" name="pd['+total_length+'][action]" checked><span></span></label></td>' +
                            '<td class="text-center"><label class="kt-radio kt-radio--success"><input type="radio" id="approve" value="approved" name="pd['+total_length+'][action]" ><span></span></label></td>' +
                            '<td class="text-center"><label class="kt-radio kt-radio--danger"><input type="radio"  id="reject" value="reject" name="pd['+total_length+'][action]" ><span></span></label></td>' +
                            '</tr>';
                    }
                    $('#repeated_data').append(tr);
                    approveQty();
                    AddAutoApprovQty();
                    dataSubmit();
                    openSelectRejectReason();
                    checkedAllInGrid();
                    addDataInit();
                    calculateGridTotalValue();
                }
            });
        }
        $(document).on('keyup','.approv-qty',function(){
            calculateGridTotalValue();
        });
        function calculateGridTotalValue(){
            var sumQty = totalPurchaseRate = 0;
            $('.approv-qty').each(function(){
                var purchaseRate = $(this).parents('tr').find('.purchase-rate').val();
                var sumQty = parseFloat($(this).val());
                totalPurchaseRate += (purchaseRate*sumQty);  
            });
            $('#gridTotalValue').html('').html((totalPurchaseRate).toFixed(3));
        }
        function openSelectRejectReason(){
            $('#ProductDemandDtlForm>tbody>tr>td>label>input#reject').click(function(){
                $(this).parents('tr').find('#notes').removeAttr('readonly');
            })
            $('#ProductDemandDtlForm>tbody>tr>td>label>input#approve , #ProductDemandDtlForm>tbody>tr>td>label>input#pending').click(function(){
                $(this).parents('tr').find('#notes').attr('readonly',true);
                $(this).parents('tr').find('#notes').val('');
            })

        }
        function checkedAllInGrid(){
            $('#ProductDemandDtlForm>thead>tr>th>label>input#pendingAll').prop('checked', true).attr('checked', true);
            $('#ProductDemandDtlForm>thead>tr>th>label>input#approveAll').click(function(){
                $('#ProductDemandDtlForm>thead>tr>th>label>input#pendingAll').prop('checked', false).attr('checked', false);
                $('#ProductDemandDtlForm>thead>tr>th>label>input#rejectAll').prop('checked', false).attr('checked', false);
                var appAllcheck = $(this).is(":checked");
                $('#ProductDemandDtlForm>tbody>tr>td').find('input#approve').each(function(){
                    if(appAllcheck) {
                        $(this).prop('checked', true).attr('checked', true);
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#pending').each(function(){
                            $(this).prop('checked', false).attr('checked', false);
                        })
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#reject').each(function(){
                            $(this).prop('checked', false).attr('checked', false);
                        })
                        var tr = $(this).parents('tr');
                        var val_approve_qty = tr.find('#approve_qty').val();
                        var val_deamnd_qty = tr.find('#demand_qty').val();
                        if(val_approve_qty == ''){
                            tr.find('#approve_qty').val(val_deamnd_qty);
                        }
                    }else{
                        $(this).prop('checked', false).attr('checked', false);
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#pending').each(function(){
                            $(this).prop('checked', true).attr('checked', true);
                        })
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#reject').each(function(){
                            $(this).prop('checked', true).attr('checked', true);
                        })
                    }
                })
            })
            $('#ProductDemandDtlForm>thead>tr>th>label>input#pendingAll').click(function(){
                $('#ProductDemandDtlForm>thead>tr>th>label>input#approveAll').prop('checked', false).attr('checked', false);
                $('#ProductDemandDtlForm>thead>tr>th>label>input#rejectAll').prop('checked', false).attr('checked', false);
                var appAllcheck = $(this).is(":checked");
                $('#ProductDemandDtlForm>tbody>tr>td').find('input#pending').each(function(){
                    if(appAllcheck) {
                        $(this).prop('checked', true).attr('checked', true)
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#approve').each(function(){
                            $(this).prop('checked', false).attr('checked', false);
                        })
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#reject').each(function(){
                            $(this).prop('checked', false).attr('checked', false);
                        })
                    }else{
                        $(this).prop('checked', false).attr('checked', false);
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#approve').each(function(){
                            $(this).prop('checked', true).attr('checked', true);
                        })
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#reject').each(function(){
                            $(this).prop('checked', true).attr('checked', true);
                        })
                    }
                })
            })
            $('#ProductDemandDtlForm>thead>tr>th>label>input#rejectAll').click(function(){
                $('#ProductDemandDtlForm>thead>tr>th>label>input#approveAll').prop('checked', false).attr('checked', false);
                $('#ProductDemandDtlForm>thead>tr>th>label>input#pendingAll').prop('checked', false).attr('checked', false);
                var appAllcheck = $(this).is(":checked");
                $('#ProductDemandDtlForm>tbody>tr>td').find('input#reject').each(function(){
                    if(appAllcheck) {
                        $(this).prop('checked', true).attr('checked', true)
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#approve').each(function(){
                            $(this).prop('checked', false).attr('checked', false);
                        })
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#pending').each(function(){
                            $(this).prop('checked', false).attr('checked', false);
                        })
                    }else{
                        $(this).prop('checked', false).attr('checked', false);
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#approve').each(function(){
                            $(this).prop('checked', true).attr('checked', true);
                        })
                        $('#ProductDemandDtlForm>tbody>tr>td').find('input#pending').each(function(){
                            $(this).prop('checked', true).attr('checked', true);
                        })
                    }
                })
            })

            $('input#approve, input#pending, input#reject').click(function(){
                $('input#approveAll').prop('checked', false).attr('checked', false);
                $('input#pendingAll').prop('checked', false).attr('checked', false);
                $('input#rejectAll').prop('checked', false).attr('checked', false);
            });
        }
        function approveQty(){
            $('.approv-qty').keyup(function(){
                var val = parseInt($(this).val());
                var demand_val = parseInt($(this).parents('tr').find('.demand-qty').val());
                if(demand_val < val){
                    $(this).parents('tr').find('td:eq(0)>input').addClass('trColor');
                    $(this).parents('tr').find('td>input').addClass('trColor');
                }else{
                    $(this).parents('tr').removeClass('trColor');
                    $(this).parents('tr').find('td>input').removeClass('trColor');
                }
            });
        }
        function dataSubmit(){
            $('button[type="submit"]').unbind();
            $('button[type="submit"]').click(function(e){
                e.preventDefault();
                var reject = '';
                var rejectId = '';
                var approve_qty = '';
                $('tbody#repeated_data>tr').each(function(){
                    reject = $(this).find('td input[type="radio"]:checked').val();
                    rejectId = $(this).find('td input[id="notes_id"]').val();
                    approve_qty = $(this).find('td input[id="approve_qty"]').val();
                    if(reject == 'reject' && rejectId == ""){
                        // toastr.error("Please select remarks");
                        // return false;
                    }
                    if(reject == 'approved' && approve_qty == ""){
                        toastr.error("Add approved quantity");
                        return false;
                    }
                });
                if(reject == 'reject' && rejectId == ""){
                    // return false;
                }else if(reject == 'approved' && approve_qty == ""){
                    return false;
                }else{
                    $('form').find(":submit").prop('disabled', true);
                    var url = $('#demand_approve_form').attr('action');
                    var formData = new FormData(document.getElementById("demand_approve_form"));
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type        : 'POST',
                        url         : url,
                        dataType	: 'json',
                        data        : formData,
                        cache       : false,
                        contentType : false,
                        processData : false,
                        success: function(response, status){

                            if(response.status == 'success'){
                                toastr.success(response.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                                if(response.data.form == 'new'){
                                    window.location.href = response.data.redirect;
                                }else{
                                    $('.new-row').removeClass('new-row');
                                }
                            }else{
                                toastr.error(response.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                            }
                        },
                        error: function(response,status) {
                            // console.log(response.responseJSON);
                            toastr.error(response.responseJSON.message);
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                        },
                    });
                }
            });
        }
        $('button[type="submit"]').click(function(){
            if($('tbody#repeated_data>tr').length == 0){
                toastr.error("Please select any product");
                return false;
            }
        });

    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>
@endsection
