@php
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $bank_rec_reconciled = "";
    if(isset($data['current'])){
        $id = $data['current']->bank_rec_id;
        $code = $data['current']->bank_rec_code;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->bank_rec_date))));
        $account_id = $data['current']->bank_rec_bank_id;
        $bank_balance = $data['current']->bank_rec_bank_balance;
        $from_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->bank_rec_start_date))));
        $to_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->bank_rec_end_date))));
        $satement_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->bank_rec_satement_date))));
        $closing_balance = $data['current']->bank_rec_closing_balance;
        $opening_balance = $data['current']->bank_rec_opening_balance;
        $uncleared = $data['current']->bank_rec_uncleared_balance;
        $notes = $data['current']->bank_rec_notes;
        $bank_rec_reconciled = $data['current']->bank_rec_reconciled;
        if($data['current']->branch_ids != ""){
            $branch_ids = explode(',',$data['current']->branch_ids);
        }else{
            $branch_ids = [];
        }
        $dtls = isset($data['current']->dtl)?$data['current']->dtl:[];

        $bank_acco = isset($data['current']->bank_acco)?$data['current']->bank_acco:[];
        $chart_code = isset($bank_acco['chart_code'])?$bank_acco['chart_code']:'';
        $chart_name = isset($bank_acco['chart_name'])?$bank_acco['chart_name']:'';
    }else{
        abort('404');
    }
@endphp
@permission($data['permission'])
    @extends('layouts.print_layout')
    @section('title', $heading)
    @section('heading', $heading)

    @section('pageCSS')
    @endsection

    @section('content')
        <table class="tableData" style="margin-top: 5px">
            <tbody>
            <tr>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Code :</span>
                        <span class="normal normal-block">{{isset($code)?$code:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Bank Account:</span>
                        <span class="normal normal-block">{{isset($chart_code)?$chart_code:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Bank Account Name:</span>
                        <span class="normal normal-block">{{isset($chart_name)?$chart_name:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Select Date :</span>
                        <span class="normal normal-block">{{isset($from_date)?$from_date:''}} to {{isset($to_date)?$to_date:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Select Type :</span>
                        <span class="normal normal-block">{{$bank_rec_reconciled=='unreconciled'?'Unreconciled transactions only':"All transactions"}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Branch :</span>
                        @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$branch_ids)->get('branch_name'); @endphp
                        <span class="normal normal-block">
                            @foreach($branch_lists as $branch_list)
                                {{$branch_list->branch_name}} ,
                            @endforeach
                        </span>
                    </div>
                </td>
                <td width="33.33%">
                </td>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Document Date :</span>
                        <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Opening Balance:</span>
                        <span class="normal normal-block">{{isset($opening_balance)?number_format($opening_balance,3):''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Bank Statement Balance:</span>
                        <span class="normal normal-block">{{isset($bank_balance)?number_format($bank_balance,3):''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Closing Balance:</span>
                        <span class="normal normal-block">{{isset($closing_balance)?number_format($closing_balance,3):''}}</span>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="tableData" style="margin-top: 10px">
            <thead>
            <tr>
                <th width="5%" class="dtl-head">Sr No</th>
                <th width="10%" class="dtl-head alignleft">Voucher Code</th>
                <th width="8%" class="dtl-head alignleft">Voucher No</th>
                <th width="10%" class="dtl-head">Cheque Date</th>
                <th width="10%" class="dtl-head">Cheque No</th>
                <th width="20%" class="dtl-head">Narration</th>
                <th width="8%" class="dtl-head">Debit</th>
                <th width="8%" class="dtl-head">Credit</th>
                <th width="10%" class="dtl-head">Cleared Date</th>
                <th width="20%" class="dtl-head">Notes</th>
                <th width="5%" class="dtl-head">Status</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($dtls))
                @php
                    $row_count=0;
                    $cheque_status_list = $data['cheque_status'];
                @endphp
                @foreach($dtls as $data)
                    @php
                        $row_count += 1;
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        @php $voucher_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['bank_rec_voucher_date'])))); @endphp
                        <td class="dtl-contents alignright">{{($voucher_date =='01-01-1970' || $voucher_date == '')?'':$voucher_date}}</td>
                        <td class="dtl-contents">{{$data['bank_rec_voucher_no']}}</td>
                        @php $cheque_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['bank_rec_voucher_chqdate'])))); @endphp
                        <td class="dtl-contents alignright">{{($cheque_date =='01-01-1970' || $cheque_date == '')?'':$cheque_date}}</td>
                        <td class="dtl-contents aligncenter">{{$data->bank_rec_voucher_chqno}}</td>
                        <td class="dtl-contents ">{{($data->bank_rec_voucher_descrip != null && $data->bank_rec_voucher_descrip != "")?$data->bank_rec_voucher_descrip:""}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->bank_rec_voucher_debit,3)}}</td>
                        <td class="dtl-contents alignright">{{number_format($data->bank_rec_voucher_credit,3)}}</td>
                        @php $cleared_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['bank_rec_voucher_cleared_date'])))); @endphp
                        <td class="dtl-contents alignright">{{($cleared_date =='01-01-1970' || $cleared_date == '')?'':$cleared_date}}</td>
                        <td class="dtl-contents alignright">{{$data->bank_rec_voucher_notes}}</td>
                        <td class="dtl-contents alignright">
                            @foreach($cheque_status_list  as $cheque_status)
                                {{($cheque_status->cheque_status_id == $data->bank_rec_cheque_status )?$cheque_status->cheque_status_name:""}}
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            @endif
            @if($row_count <= 9)
                @for ($z = 0; $z < (9 - $row_count); $z++)
                    <tr>
                        <td>&nbsp</td>{{--1--}}
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>{{--5--}}
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>
                        <td>&nbsp</td>{{--10--}}
                        <td>&nbsp</td>
                    </tr>
                @endfor
            @endif
            </tbody>
        </table>
        <table style="margin-top: 10px;">
            <tbody>
                <tr>
                    <td>
                        <div class="document_notes_h" style="    font-size: 11px;">Notes:</div>
                    </td>
                    <td><div class="document_notes_text" style="    font-size: 11px;">{{isset($notes)?$notes:''}}</div></td>
                </tr>
            </tbody>
        </table>
    @endsection

    @section('customJS')
    @endsection
@endpermission