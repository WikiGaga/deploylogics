@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>
        .tdBorder{
            border-top: 2px solid #777777 !important;
            cursor: pointer;
        }
        .totFont{
            font-weight: 500 !important;
        }
        .table tr>th:first-child,
        .table tr>td:first-child {
            border-left: 0 !important;
        }
        .table tr>th:last-child,
        .table tr>td:last-child {
            border-right: 0 !important;
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" style="padding: 36px">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                @foreach($data['criteria'] as $show)
                    @php
                        $val_1 = isset($show['value_1'])?$show['value_1']:"";
                        $val_2 = isset($show['value_2'])?$show['value_2']." to ":"";
                        $val_3 = isset($show['value_3'])?$show['value_3']:"";
                    @endphp
                    <h6 class="kt-invoice__title">
                        <span style="color: #e27d00;">{{$show['name']}}</span>
                        <span style="color: #5578eb;">{{" ".$show['type']." "}}</span>
                        {{$val_1.''.$val_2.''.$val_3}}</h6>
                @endforeach
            </div>
            <div class="kt-portlet__head-toolbar text-center">
                <div href="#" class="kt-invoice__logo">
                    <a href="#"><img src="/images/1601992238.jpeg" width="60px"></a>
                    <div class="kt-invoice__desc">
                    <div>Cecilia Chapman, 711-2880 Nulla St, Mankato</div>
                    <div>Mississippi 96522</div>
                </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Cashier's Name</th>
                            <th class="text-center">CR Sale</th>
                            <th class="text-center">CR Sale Rtr</th>
                            <th class="text-center">Net Cr Sale</th>
                            <th class="text-center">Cash Sale</th>
                            <th class="text-center">Cash Sale Rtr</th>
                            <th class="text-center">Net Cash Sale</th>
                            <th class="text-center">Net Sale</th>
                            <th class="text-center">Collection</th>
                            <th class="text-center">Difference</th>
                        </tr>
                        @php
                            $total_CrSale =0;
                            $total_CrSaleR=0;
                            $Net_total_CrSale=0;
                            $total_CaSale =0;
                            $total_CaSaleR=0;
                            $Net_total_CaSaleR=0;
                            $Net_total_Sale =0;
                            $total_ColctionAmt =0;
                            $total_diffAmt =0;
                        @endphp
                        @if(isset($data['SI']))
                            @foreach($data['SI'] as $saleData)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{$saleData->sales_sales_man_name}}</td>
                                    @php
                                        $CrSaleQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                        select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                        where sales_sales_man = '".$saleData->sales_sales_man."' and sales_type = 'SI' and sales_sales_type = '2' and
                                                        sales_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd')
                                                        and to_date ('".$data['from_date']."', 'yyyy/mm/dd')
                                                    ) abc";
                                        $CrSale = \Illuminate\Support\Facades\DB::select($CrSaleQuery);
                                        $CrSale = isset($CrSale[0]->sales_net_amount)?$CrSale[0]->sales_net_amount:0;
                                    @endphp
                                <td class="text-right">{{number_format($CrSale,3)}}</td>
                                    @php
                                        $CrSaleRQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                        select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                        where sales_sales_man = '".$saleData->sales_sales_man."' and sales_type = 'SR' and sales_sales_type = '2' and
                                                        sales_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd')
                                                        and to_date ('".$data['from_date']."', 'yyyy/mm/dd')
                                                    ) abc";
                                        $CrSaleR = \Illuminate\Support\Facades\DB::select($CrSaleRQuery);
                                        $CrSaleR = isset($CrSaleR[0]->sales_net_amount)?$CrSaleR[0]->sales_net_amount:0;
                                    @endphp
                                <td class="text-right">{{number_format($CrSaleR,3)}}</td>
                                    @php $t_Cr_Sale = $CrSale - $CrSaleR; @endphp
                                <td class="text-right">{{number_format($t_Cr_Sale,3)}}</td>
                                    @php
                                        $CaSaleQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                        select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                        where sales_sales_man = '".$saleData->sales_sales_man."' and sales_type = 'SI' and sales_sales_type = '1' and
                                                        sales_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd')
                                                        and to_date ('".$data['from_date']."', 'yyyy/mm/dd')
                                                    ) abc";
                                        $CaSale = \Illuminate\Support\Facades\DB::select($CaSaleQuery);
                                        $CaSale = isset($CaSale[0]->sales_net_amount)?$CaSale[0]->sales_net_amount:0;
                                    @endphp
                                <td class="text-right">{{number_format($CaSale,3)}}</td>
                                    @php
                                        $CaSaleRQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                        select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                        where sales_sales_man = '".$saleData->sales_sales_man."' and sales_type = 'SR' and sales_sales_type = '1' and
                                                        sales_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd')
                                                        and to_date ('".$data['from_date']."', 'yyyy/mm/dd')
                                                    ) abc";
                                        $CaSaleR = \Illuminate\Support\Facades\DB::select($CaSaleRQuery);
                                        $CaSaleR = isset($CaSaleR[0]->sales_net_amount)?$CaSaleR[0]->sales_net_amount:0;
                                    @endphp
                                <td class="text-right">{{number_format($CaSaleR,3)}}</td>
                                    @php $t_Ca_Sale = $CaSale - $CaSaleR; @endphp
                                <td class="text-right">{{number_format($t_Ca_Sale,3)}}</td>
                                    @php $net_sale = $t_Cr_Sale + $t_Ca_Sale;  @endphp
                                <td class="text-right">{{number_format($net_sale,3)}}</td>
                                    @php
                                        $ColctionQuery = "select sum(day_amount) amount from vw_sale_day where day_payment_handover_received = '".$saleData->sales_sales_man."' and day_case_type = 'payment-received'
                                                        and day_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd') and to_date ('".$data['from_date']."', 'yyyy/mm/dd')";
                                        $Colction = \Illuminate\Support\Facades\DB::select($ColctionQuery);
                                        $ColctionAmt = isset($Colction[0]->amount)?$Colction[0]->amount:0;
                                    @endphp
                                <td class="text-right">{{number_format($ColctionAmt,3)}}</td>
                                    @php $diffAmt = $t_Ca_Sale - $ColctionAmt;  @endphp
                                <td class="text-right">{{number_format($diffAmt,3)}}</td>
                            </tr>
                                @php
                                    $total_CrSale += $CrSale;
                                    $total_CrSaleR += $CrSaleR;
                                    $Net_total_CrSale += $t_Cr_Sale;
                                    $total_CaSale += $CaSale;
                                    $total_CaSaleR += $CaSaleR;
                                    $Net_total_CaSaleR += $t_Ca_Sale;
                                    $Net_total_Sale += $net_sale;
                                    $total_ColctionAmt += $ColctionAmt;
                                    $total_diffAmt += $diffAmt;
                                @endphp
                            @endforeach
                        @endif
                        <tr>
                            <td colspan="2" class="rep-font-bold">Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CrSale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CrSaleR,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($Net_total_CrSale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CaSale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CaSaleR,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($Net_total_CaSaleR,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($Net_total_Sale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_ColctionAmt,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_diffAmt,3)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <h5>Cash Received From</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                                <tr>
                                    <th width="10%" class="text-center">SN</th>
                                    <th width="25%" class="text-left">Account Name</th>
                                    <th width="45%" class="text-left">Note</th>
                                    <th width="20%" class="text-center">Amount</th>
                                </tr>
                                @php
                                    $CashRece = "select voucher_no,voucher_id, voucher_sr_no ,voucher_debit,chart_name , voucher_descrip from vw_acco_voucher
                                                    where voucher_type = 'crv' and voucher_debit > 0 and voucher_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd')
                                                    and to_date ('".$data['from_date']."', 'yyyy/mm/dd') ";
                                    $CashReceData = \Illuminate\Support\Facades\DB::select($CashRece);
                                    $tot_CashRece = 0;
                                @endphp
                                @if(isset($CashReceData))
                                    @foreach($CashReceData as $CashRece)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-left">{{$CashRece->chart_name}}</td>
                                            <td class="text-left">{{$CashRece->voucher_descrip}}</td>
                                            <td class="text-right">{{number_format($CashRece->voucher_debit,3)}}</td>
                                        </tr>
                                        @php $tot_CashRece += $CashRece->voucher_debit; @endphp
                                    @endforeach
                                @endif
                                <tr>
                                    <td colspan="3" class="rep-font-bold">Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($tot_CashRece,3)}}</td>
                                </tr>
                                @php $sub_tot = $total_ColctionAmt + $tot_CashRece;  @endphp
                                <tr>
                                    <td colspan="3" class="rep-font-bold">Sub Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_tot,3)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <h5>Cash Paid To</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                                <tr>
                                    <th width="10%" class="text-center">SN</th>
                                    <th width="25%" class="text-left">Account Name</th>
                                    <th width="45%" class="text-left">Note</th>
                                    <th width="20%" class="text-center">Amount</th>
                                </tr>
                                @php
                                    $CashPay = "select voucher_no,voucher_id, voucher_sr_no ,voucher_credit,chart_name , voucher_descrip from vw_acco_voucher
                                                    where voucher_type = 'cpv' and voucher_credit > 0 and voucher_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd')
                                                    and to_date ('".$data['from_date']."', 'yyyy/mm/dd') ";
                                    $CashPayData = \Illuminate\Support\Facades\DB::select($CashPay);
                                    $tot_CashPay = 0;
                                @endphp
                                @if(isset($CashPayData))
                                    @foreach($CashPayData as $CashPay)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-left">{{$CashPay->chart_name}}</td>
                                            <td class="text-left">{{$CashPay->voucher_descrip}}</td>
                                            <td class="text-right">{{number_format($CashPay->voucher_credit,3)}}</td>
                                        </tr>
                                        @php $tot_CashPay += $CashPay->voucher_credit; @endphp
                                    @endforeach
                                @endif
                                <tr>
                                    <td colspan="3" class="rep-font-bold">Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($tot_CashPay,3)}}</td>
                                </tr>
                                @php $sub_tot = $tot_CashRece + $total_ColctionAmt - $tot_CashPay;  @endphp
                                <tr>
                                    <td colspan="3" class="rep-font-bold">Net Sub Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_tot,3)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <h5>Bank Distribution</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <table  class="table bt-datatable">
                                <tr>
                                    <td class="text-left tdBorder totFont">Notes</td>
                                    @php
                                        $NotesQuery = "select denomination_name from vw_sale_day where day_case_type = 'payment-handover'
                                                        and day_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd') and to_date ('".$data['from_date']."', 'yyyy/mm/dd') order by denomination_id";
                                        $NotesRes = \Illuminate\Support\Facades\DB::select($NotesQuery);
                                    @endphp
                                    @if(isset($NotesRes))
                                            @foreach($NotesRes as $Notes)
                                                <td class="text-center tdBorder">{{$Notes->denomination_name}}</td>
                                            @endforeach
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-left totFont">No</td>
                                    @php
                                        $QtyQuery = "select day_qty from vw_sale_day where day_case_type = 'payment-handover'
                                                        and day_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd') and to_date ('".$data['from_date']."', 'yyyy/mm/dd') order by denomination_id";
                                        $QtyRes = \Illuminate\Support\Facades\DB::select($QtyQuery);
                                    @endphp
                                    @if(isset($QtyRes))
                                            @foreach($QtyRes as $NO)
                                                <td class="text-center">{{$NO->day_qty}}</td>
                                            @endforeach
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-left totFont">Amount</td>
                                    @php
                                        $AmtQuery = "select day_amount from vw_sale_day where day_case_type = 'payment-handover'
                                                        and day_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd') and to_date ('".$data['from_date']."', 'yyyy/mm/dd') order by denomination_id";
                                        $AmtRes = \Illuminate\Support\Facades\DB::select($AmtQuery);
                                        $rowCount = count($AmtRes);
                                        $tot_BD = 0;
                                    @endphp
                                    @if(isset($AmtRes))
                                            @foreach($AmtRes as $Amt)
                                                <td class="text-center">{{$Amt->day_amount}}</td>
                                                @php $tot_BD += $Amt->day_amount; @endphp
                                            @endforeach
                                    @endif
                                </tr>
                                <tr>
                                    <td colspan="{{$rowCount}}" class="totFont">Total:</td>
                                    <td class="text-right totFont">{{number_format($tot_BD,3)}}</td>
                                </tr>
                                @php $sub_tot = $tot_CashRece + $total_ColctionAmt - $tot_CashPay - $tot_BD;  @endphp
                                <tr>
                                    <td colspan="{{$rowCount}}" class="totFont">Difference:</td>
                                    <td class="text-right totFont">{{number_format($sub_tot,3)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot sale_invoice_footer" style="background: #f7f8fa">
            <div class="row">
                <div class="col-lg-12 kt-align-right">
                    <div class="date"><span>Date: </span>{{ date('d-m-Y') }} - <span>User: </span>{{auth()->user()->name}}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')

@endsection

@section('customJS')

@endsection



