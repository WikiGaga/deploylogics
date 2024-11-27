@extends('layouts.layout')
@section('title', 'Day')

@section('pageCSS')
<style>
    .box{
    margin:0 auto;
    width:300px;
    /* padding:20px; */
    background:#f9f9f9;
}
</style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $casetype = $data['casetype'];
        if($casetype == 'day-closing'){
            $day_calc = [];
            $day_pos = [];
            $day_pad = [];
        }
        if($case == 'new'){
            $code = $data['document_code'];
            $document_type = $data['document_type'];
            $date =  date('d-m-Y');
            $to_date =  date('d-m-Y', strtotime('+1 days'));
            //$from_time =  '12:00 AM';
            $from_time =  '03:00 AM';
            $to_time =  '03:00 AM';
            //$to_time =  '11:59 PM';
            $user_id = Auth::user()->id;
            $shift_id = 0;
            $cash_transfer_status = 1;
            $cih_per_sys = 0;
        }
        if($case == 'edit'){
            $id = $data['current']->day_id;
            $document_type = $data['current']->day_code_type;
            $code = $data['current']->day_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->day_date))));
            $to_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->to_date))));
            $from_time =  date('h:i A', strtotime(trim(str_replace('/','-',$data['current']->day_date))));;
            $to_time =  date('h:i A', strtotime(trim(str_replace('/','-',$data['current']->to_date))));;
            $shift_id = $data['current']->shift_id;
            $user_id = $data['current']->saleman_id;
            $payment_handover_received = $data['current']->day_payment_handover_received;
            $payment_way_type = $data['current']->day_payment_way_type;
            $reference_no = $data['current']->day_reference_no;
            $notes = $data['current']->day_notes;
            $denomins = isset($data['denomins'])? $data['denomins']:[];
            $dtls = isset($data['current']->dtl)? $data['current']->dtl:[];

            if($casetype == 'day-closing'){
                foreach ($dtls as $dtl){
                    if($dtl['day_case_type'] == 'day_calc'){
                        $day_calc = $dtl;
                    }
                    if($dtl['day_case_type'] == 'day_payment'){
                        $day_pad[] = $dtl;
                    }
                    if($dtl['day_case_type'] == 'day_pos'){
                        $day_pos[] = $dtl;
                    }
                }
                $cash_transfer_status = isset($day_calc['cash_transfer_status'])?$day_calc['cash_transfer_status']:"";
            }
        }
    @endphp
    @permission($data['permission']);
    <form id="day_form" class="master_form kt-form" method="post" action="{{ action('Sales\DayController@store', [$casetype,isset($id)?$id:'']) }}">
        @csrf
        {{-- @php
            $business_id = "business_id = ".auth()->user()->business_id;
            $company_id = "company_id = ".auth()->user()->company_id;
            $branch_id = "branch_id = ".auth()->user()->branch_id;

            $purc_query = "select distinct branch_id,branch_name,sales_date,document_name,sales_type,document_type,sales_id,sales_net_amount  from vw_sale_sales_invoice
            where sales_type=POS and $business_id and $company_id and $branch_id";
            $purc_dtl = \Illuminate\Support\Facades\DB::selectOne($purc_query);
            dd($purc_dtl);
        @endphp --}}
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
                                            <input type="hidden" name="day_code" value="{{isset($code)?$code:''}}">
                                            <input type="hidden" name="document_type" value="{{isset($document_type)?$document_type:''}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="erp-col-form-label">From Date:</label>
                                        <div class="input-group date">
                                            <input type="text" name="day_date" class="day_date form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="erp-col-form-label">From Time:</label>
                                        <div class="input-group date">
                                            <input type="text" name="from_time" class="from_time form-control erp-form-control-sm" readonly value="{{isset($from_time)?$from_time:""}}" id="kt_from_time" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-clock-o"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="erp-col-form-label">To Date:</label>
                                        <div class="input-group date">
                                            <input type="text" name="to_date" class="to_date form-control erp-form-control-sm c-date-p" readonly value="{{isset($to_date)?$to_date:""}}" id="kt_to_date" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="erp-col-form-label">To Time:</label>
                                        <div class="input-group date">
                                            <input type="text" name="to_time" class="to_time form-control erp-form-control-sm" readonly value="{{isset($to_time)?$to_time:""}}" id="kt_to_time" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-clock-o"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @if($casetype == 'payment-handover' || $casetype == 'payment-received')
                                            <label class="erp-col-form-label text-center">User: <span class="required">*</span></label>
                                        @else
                                            <label class="erp-col-form-label text-center">Salesman: <span class="required">*</span></label>
                                        @endif
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="saleman_id" name="saleman_id">
                                                @php $select_user = isset($user_id)?$user_id:""; @endphp
                                                @foreach($data['users'] as $users)
                                                    <option value="{{$users->id}}" {{$users->id == $select_user?"selected":""}}>{{ucwords($users->name)}} - {{$users->email}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="erp-col-form-label text-center">Shift: <span class="required">*</span></label>
                                        <div class="erp-select2">
                                            <select class="moveIndex form-control erp-form-control-sm kt-select2" id="day_shift" name="day_shift">
                                                <option value="0">Select</option>
                                                @foreach($data['shift'] as $shift)
                                                <option value="{{$shift->shift_id}}" {{$shift_id == $shift->shift_id?'selected':''}}>{{$shift->shift_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="erp-col-form-label text-center">Counter Name: <span class="required">*</span></label>
                                        <div class="erp-select2">
                                            <input type="text" value="{{isset($data['current']->terminal)?$data['current']->terminal->terminal_name:""}}" class="form-control erp-form-control-sm readonly" name="terminal_name" id="terminal_name" readonly>
                                            <input type="hidden" value="{{isset($data['current']->terminal)?$data['current']->terminal->terminal_id:""}}" name="terminal_id" id="terminal_id">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($casetype == 'day-closing')
                        <div class="row">
                            <div class="col-lg-4">
                                <button type="button" id="getData" class="btn btn-sm btn-primary">Get Data</button>
                            </div>
                        </div>
                        @endif
                        @if($casetype == 'payment-handover' || $casetype == 'payment-received')
                            <div class="row form-group-block">
                                <div class="col-lg-4">
                                    <div class="row">
                                        @if($casetype == 'payment-handover')
                                            <label class="col-lg-6 erp-col-form-label">Payment Handover User:</label>
                                        @endif
                                        @if($casetype == 'payment-received')
                                            <label class="col-lg-6 erp-col-form-label">Payment Receiving User:</label>
                                        @endif
                                        <div class="col-lg-6">
                                            <div class="erp-select2">
                                                <select class="form-control erp-form-control-sm kt-select2 moveIndex" name="payment_handover_received">
                                                    <option value="0">Select</option>
                                                    @php $payment_handover_received = isset($payment_handover_received)?$payment_handover_received:""; @endphp
                                                    @foreach($data['payment_person'] as $payment_person)
                                                        <option value="{{$payment_person->id}}" {{$payment_person->id == $payment_handover_received?"selected":""}}>{{$payment_person->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Payment Way Type:</label>
                                        <div class="col-lg-6">
                                            <div class="erp-select2">
                                                <select class="form-control erp-form-control-sm kt-select2 moveIndex" name="payment_way_type">
                                                    <option value="0">Select</option>
                                                    @php $payment_way_type = isset($payment_way_type)?$payment_way_type:""; @endphp
                                                    @foreach($data['payment_type'] as $payment_type)
                                                        <option value="{{$payment_type->payment_type_id}}" {{$payment_type->payment_type_id == $payment_way_type?"selected":""}}>{{$payment_type->payment_type_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label text-center">Reference No:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="reference_no" value="{{isset($reference_no)?$reference_no:''}}" class="moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <br>
                        <div class="row">
                            <div class="col-lg-6" style="padding-right: 15px;">
                                @if($casetype == 'day-closing')
                                <div class="row">
                                    <h6>POS Document Activity</h6>
                                    <table class="pos_activity table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                        <thead class="erp_form__grid_header">
                                            <tr>
                                                <th width="10px"><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Sr.</div></th>
                                                <th><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Document Name</div></th>
                                                <th><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Total No. of Document</div></th>
                                                <th><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Total Amt.</div></th>
                                                <th><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Total Disc.</div></th>
                                            </tr>
                                        </thead>
                                        <tbody class="erp_form__grid_body">
                                        @php $i = 1; @endphp
                                        @foreach($day_pos as $pos_row)
                                            <tr>
                                                <td style='padding: 5px !important;'>{{$i}}
                                                    <input type='hidden' name='pos[{{$i}}][sr]' value='{{$i}}'>
                                                    <input type='hidden' name='pos[{{$i}}][document_name]' value='{{$pos_row['document_name']}}'>
                                                    <input type='hidden' name='pos[{{$i}}][total_doc]' value='{{$pos_row['no_of_documents']}}'>
                                                    <input type='hidden' name='pos[{{$i}}][amount]' value='{{$pos_row['total_amount']}}'>
                                                    <input type='hidden' name='pos[{{$i}}][discount]' value='{{$pos_row['total_discount']}}'>
                                                </td>
                                                <td style='padding: 5px !important;'>{{$pos_row['document_name']}}</td>
                                                <td class='text-right' style='padding: 5px !important;'>{{$pos_row['no_of_documents']}}</td>
                                                <td class='text-right' style='padding: 5px !important;'>{{$pos_row['total_amount']}}</td>
                                                <td class='text-right' style='padding: 5px !important;'>{{$pos_row['total_discount']}}</td>
                                            </tr>
                                            @php $i += 1; @endphp
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <div class="row">
                                    <h6>Payment Mode Detail</h6>
                                    <table class="payment_mode_detail table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                        <thead class="erp_form__grid_header">
                                            <tr>
                                                <th width="10px"><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Sr.</div></th>
                                                <th><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Payment Mode</div></th>
                                                <th><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Opening Amount</div></th>
                                                <th><div class="erp_form__grid_th_title" style='padding: 5px !important;'>In Flow(Dr)</div></th>
                                                <th><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Out Flow(Cr)</div></th>
                                                <th><div class="erp_form__grid_th_title" style='padding: 5px !important;'>Balance</div></th>
                                            </tr>
                                        </thead>
                                        <tbody class="erp_form__grid_body">
                                        @php $i = 1; @endphp
                                        @foreach($day_pad as $pad_row)
                                            <tr>
                                                <td style='padding: 5px !important;'>{{$i}}
                                                    <input type='hidden' name='pad[{{$i}}][sr]' value='{{$i}}'>
                                                    <input type='hidden' name='pad[{{$i}}][payment_mode]' value='{{$pad_row['payment_mode']}}'>
                                                    <input type='hidden' name='pad[{{$i}}][opening_amount]' value='{{$pad_row['opening_amount']}}'>
                                                    <input type='hidden' name='pad[{{$i}}][in_flow]' value='{{$pad_row['in_flow']}}'>
                                                    <input type='hidden' name='pad[{{$i}}][out_flow]' value='{{$pad_row['out_flow']}}'>
                                                    <input type='hidden' name='pad[{{$i}}][balance_amount]' value='{{$pad_row['payment_mode_balance']}}'>
                                                </td>
                                                <td style='padding: 5px !important;'>{{$pad_row['payment_mode']}}</td>
                                                <td class='text-right' style='padding: 5px !important;'>{{$pad_row['opening_amount']}}</td>
                                                <td class='text-right' style='padding: 5px !important;'>{{$pad_row['in_flow']}}</td>
                                                <td class='text-right' style='padding: 5px !important;'>{{$pad_row['out_flow']}}</td>
                                                <td class='text-right' style='padding: 5px !important;'>{{$pad_row['payment_mode_balance']}}</td>
                                            </tr>
                                            @php $i += 1; @endphp
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <h6>Cash Closing</h6>
                                    <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed">
                                        <thead>
                                            <tr>
                                                <th width="25%" style="text-align:center;">Denomination</th>
                                                <th width="28%" style="text-align:center;">Qty</th>
                                                <th width="28%" style="text-align:center;">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody id="repeated_data">
                                        @if($case == 'edit')
                                            @php $sr = 0; @endphp
                                            @php $sr = 0; @endphp
                                            @foreach($data['denomination'] as $key=>$denomination)
                                                @php
                                                    $qty = 0;
                                                    $amt = 0;
                                                @endphp
                                                @foreach($denomins as $deno)
                                                    @if($deno->denomination_id == $denomination->denomination_id )
                                                        @php
                                                            $qty = $deno->day_qty;
                                                            $amt = $deno->day_amount;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <tr>
                                                    <td style="text-align:center;">
                                                        <input type="hidden" name="dayDtl[{{$sr}}][denomination_id]" value="{{$denomination->denomination_id}}"><b class="denomination">{{$denomination->denomination_name}}</b>
                                                    </td>
                                                    <td>
                                                        <input type="text" value="{{$qty}}" class="form-control erp-form-control-sm qty validNumber" maxlength="15" name="dayDtl[{{$sr}}][day_qty]">
                                                    </td>
                                                    <td>
                                                        <input type="text" value="{{$amt}}" class="form-control erp-form-control-sm amt validNumber readonly" maxlength="15" name="dayDtl[{{$sr}}][day_value]" readonly>
                                                    </td>
                                                </tr>
                                                @php $sr++; @endphp
                                            @endforeach
                                        @else
                                            @php $sr = 0; @endphp
                                             @foreach($data['denomination'] as $key=>$denomination)
                                                <tr>
                                                    <td style="text-align:center;">
                                                        <input type="hidden" name="dayDtl[{{$sr}}][denomination_id]" value="{{$denomination->denomination_id}}"><b class="denomination">{{$denomination->denomination_name}}</b>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control erp-form-control-sm qty validNumber" maxlength="15" name="dayDtl[{{$sr}}][day_qty]">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control erp-form-control-sm amt validNumber readonly" maxlength="15" name="dayDtl[{{$sr}}][day_value]" readonly>
                                                    </td>
                                                </tr>
                                                @php $sr++; @endphp
                                            @endforeach
                                        @endif
                                        <tr>
                                            <td colspan="2"><b class="denomination">Total</b></td>
                                            <td id="total_Amt" class="text-right font-weight-bold"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if($casetype == 'day-closing')
                                <div class="row">
                                    <h6>POS Session Summary</h6>
                                    <div class="col-lg-12 box">
                                        <div class="form-group-block row" style="margin-top: 10px;">
                                            <label class="col-lg-5 erp-col-form-label">C.I.H.(As Per System)</label>
                                            <div class="col-lg-7">
                                                <input type="text" id="cih_per_sys" name="cih_per_sys" value="{{isset($day_calc['cash_in_hand_per_system'])?number_format($day_calc['cash_in_hand_per_system'],3,'.',''):""}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber readonly">
                                            </div>
                                        </div>
                                        <div class="form-group-block row">
                                            <label class="col-lg-5 erp-col-form-label">Closing Cash</label>
                                            <div class="col-lg-7">
                                                <input type="text" id="closing_cash" name="closing_cash" value="{{isset($day_calc['closing_cash'])?number_format($day_calc['closing_cash'],3,'.',''):""}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </div>
                                        </div>
                                        <div class="form-group-block row">
                                            <label class="col-lg-5 erp-col-form-label">Difference (Short)</label>
                                            <div class="col-lg-7">
                                                <input type="text" id="diff_amount" name="diff_amount" value="{{isset($day_calc['cash_difference'])?number_format($day_calc['cash_difference'],3,'.',''):""}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber readonly">
                                            </div>
                                        </div>
                                        <div class="form-group-block row">
                                            <label class="col-lg-5 erp-col-form-label">Transfer Amount</label>
                                            <div class="col-lg-7">
                                                <input type="text" id="trans_amount" name="trans_amount" value="{{isset($day_calc['transfer_amount'])?number_format($day_calc['transfer_amount'],3,'.',''):""}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber ">
                                            </div>
                                        </div>
                                        <div class="form-group-block row">
                                            <label class="col-lg-5 erp-col-form-label">Opening Amount</label>
                                            <div class="col-lg-7">
                                                <input type="text" id="opening_amount" name="opening_amount" value="{{isset($day_calc['pos_opening_amount'])?number_format($day_calc['pos_opening_amount'],3,'.',''):""}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </div>
                                        </div>
                                        <div class="form-group-block row">
                                            <label class="col-lg-5 erp-col-form-label">Cash Transfer</label>
                                            <div class="col-lg-7">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        <input type="checkbox" id="cash_transfer_status" name="cash_transfer_status" {{$cash_transfer_status==1?"checked":""}}>
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group-block row" style="margin-top: 5px;">
                            <label class="col-lg-2 erp-col-form-label">Notes:</label>
                            <div class="col-lg-10">
                                <textarea type="text" rows="3" name="notes" maxlength="255" class="form-control erp-form-control-sm moveIndex">{{isset($notes)?$notes:''}}</textarea>
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
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        $("#kt_to_date").datepicker({
            format: "dd-mm-yyyy",
        });
        $("#kt_from_time").timepicker({
            minuteStep:1,
        });
        $("#kt_to_time").timepicker({
            minuteStep:1,
        });
    </script>
    <script>
        $(".qty").keyup(function(){
            var qty = $(this).val();
            var denomination = $(this).parents('tr').find('.denomination').html();
            qty = (qty == '' || qty == undefined)? 0 : qty;
            denomination = (denomination == '' || denomination == undefined)? 0 : denomination;
            var amount = qty * denomination;
            amount= amount.toFixed(3);
            $(this).parents('tr').find('.amt').val(amount);
            totalAmount();
        });
        function totalAmount(){
            var t = 0;
            var v = 0;
            $( "#repeated_data>tr" ).each(function( index ) {
                v = $(this).find('td>.amt').val();
                v = (v == '' || v == undefined)? 0 : v.replace( /,/g, '');
                t += parseFloat(v);
            });

            t = t.toFixed(3);
            $('#total_Amt').html(t);
            $('#closing_cash').val(t);
            fucAmountDiff()
        }
        $( document ).ready(function() {
            totalAmount();
        });
        var xhrGetData = true;
        $(document).on('click','#getData',function(){
            var thix = $(this);
            var form = thix.parents('form');
            var day_date = form.find('.day_date').val();
            var to_date = form.find('.to_date').val();
            var from_time = form.find('.from_time').val();
            var to_time = form.find('.to_time').val();
            var salesman_id = form.find('#saleman_id option:selected').val();
            var day_shift = form.find('#day_shift option:selected').val();
            var validate = true;
            if(valueEmpty(day_date)){
                toastr.error("From Date is required");
                validate = false;
                return true;
            }
            if(valueEmpty(to_date)){
                toastr.error("To Date is required");
                validate = false;
                return true;
            }
            if(valueEmpty(salesman_id)){
                toastr.error("Salesman is required");
                validate = false;
                return true;
            }
            if(valueEmpty(day_shift)){
                toastr.error("Day Shift is required");
                validate = false;
                return true;
            }
            if(validate && xhrGetData){
                $('body').addClass('pointerEventsNone');
                xhrGetData = false;
                var formData = {
                    day_date : day_date,
                    to_date : to_date,
                    from_time : from_time,
                    to_time : to_time,
                    salesman_id : salesman_id,
                    day_shift : day_shift,
                };
                var url = '{{route('getPosShiftData')}}';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    success: function(response,data) {
                        console.log(response);
                        if(response.status == 'success'){
                            toastr.success(response.message);
                            var pos = response.data.pos_activity;
                            var tr = "";
                            var sr = 1;
                            pos.forEach(function(row,index){
                                if(!valueEmpty(row.document_name)){
                                    tr += "<tr>" +
                                        "<td width='10px' style='padding: 5px !important;'>"+sr+
                                        "<input type='hidden' name='pos["+sr+"][sr]' value='"+sr+"'>" +
                                        "<input type='hidden' name='pos["+sr+"][document_name]' value='"+row.document_name+"'>" +
                                        "<input type='hidden' name='pos["+sr+"][total_doc]' value='"+row.total_doc+"'>" +
                                        "<input type='hidden' name='pos["+sr+"][amount]' value='"+row.amount+"'>" +
                                        "<input type='hidden' name='pos["+sr+"][discount]' value='"+row.discount+"'>" +
                                        "</td>"+
                                        "<td style='padding: 5px !important;'>"+row.document_name+"</td>"+
                                        "<td class='text-right' style='padding: 5px !important;'>"+row.total_doc+"</td>"+
                                        "<td class='text-right' style='padding: 5px !important;'>"+row.amount+"</td>"+
                                        "<td class='text-right' style='padding: 5px !important;'>"+row.discount+"</td>"+
                                        "</tr>";
                                    sr = sr + 1;
                                    $('#terminal_id').val(row.terminal_id);
                                    $('#terminal_name').val(row.terminal_name);
                                }
                            })
                            $('.pos_activity tbody.erp_form__grid_body').html(tr);

                            var payment_dtl = response.data.payment_dtl;
                            var tr = "";
                            var sr = 1;
                            var cash = 0;
                            var internal_vouch = 0;
                            payment_dtl.forEach(function(row,index){
                                if(!valueEmpty(row.document_name)){
                                    tr += "<tr>" +
                                        "<td width='10px' style='padding: 5px !important;'>"+sr+
                                        "<input type='hidden' name='pad["+sr+"][sr]' value='"+sr+"'>" +
                                        "<input type='hidden' name='pad["+sr+"][payment_mode]' value='"+row.document_name+"'>" +
                                        "<input type='hidden' name='pad["+sr+"][opening_amount]' value='"+row.opening_amount+"'>" +
                                        "<input type='hidden' name='pad["+sr+"][in_flow]' value='"+row.in_flow+"'>" +
                                        "<input type='hidden' name='pad["+sr+"][out_flow]' value='"+row.out_flow+"'>" +
                                        "<input type='hidden' name='pad["+sr+"][balance_amount]' value='"+row.balance_amount+"'>" +
                                        "</td>"+
                                        "<td style='padding: 5px !important;'>"+row.document_name+"</td>"+
                                        "<td class='text-right' style='padding: 5px !important;'>"+row.opening_amount+"</td>"+
                                        "<td class='text-right' style='padding: 5px !important;'>"+row.in_flow+"</td>"+
                                        "<td class='text-right' style='padding: 5px !important;'>"+row.out_flow+"</td>"+
                                        "<td class='text-right' style='padding: 5px !important;'>"+row.balance_amount+"</td>"+
                                        "</tr>";
                                        sr = sr + 1;
                                        if(row.document_name == 'Cash'){
                                            cash = parseFloat(row.balance_amount);
                                        }
                                        if(row.document_name == 'Internal Voucher'){
                                            internal_vouch = parseFloat(row.balance_amount);
                                        }
                                }
                            })
                            var bal = parseFloat(cash) + parseFloat(internal_vouch);
                            $('#cih_per_sys').val(parseFloat(bal).toFixed(3));
                            fucAmountDiff()

                            $('.payment_mode_detail tbody.erp_form__grid_body').html(tr);

                        }else{
                            toastr.error(response.message);
                        }
                        xhrGetData = true;
                        $('body').removeClass('pointerEventsNone');
                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                        xhrGetData = true;
                        $('body').removeClass('pointerEventsNone');
                    }
                });
            }
        })

        function fucAmountDiff(){
            var cih_per_sys = $(document).find('#cih_per_sys').val();
            var closing_cash = $(document).find('#closing_cash').val();
            if(valueEmpty(cih_per_sys)){
                cih_per_sys = 0;
            }
            if(valueEmpty(closing_cash)){
                closing_cash = 0;
            }
            var diff = parseFloat(cih_per_sys) - parseFloat(closing_cash);
            $(document).find('#diff_amount').val(parseFloat(diff).toFixed(3))
        }
    </script>
@endsection
