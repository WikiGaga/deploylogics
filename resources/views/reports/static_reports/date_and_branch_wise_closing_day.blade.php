@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
                </h6>
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->where(\App\Library\Utilities::currentBC())->where('branch_active_status',1)->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @if(isset($data['is_verified']) && $data['is_verified'] == 0)
                        <button class="btn btn-primary pull-right" id="verifyClosingDay">
                            <i class="fa fa-check"></i>
                            Verify Report
                        </button>
                    @else
                        <button class="btn btn-success pull-right" disabled>
                            <i class="fa fa-check"></i>
                            Report Verified
                        </button>
                    @endif
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">No</th>
                            <th class="text-center">Cashier's Name</th>

                            <th class="text-center">CR Sale</th>
                            <th class="text-center">CR Sale Rtr</th>
                            <th class="text-center">Net Credit Sale</th>

                            <th class="text-center">VC Sale</th>
                            <th class="text-center">VC Sale Rtr</th>
                            <th class="text-center">Net VC Sale</th>

                            <th class="text-center">Cash Sale</th>
                            <th class="text-center">Cash Sale Rtr</th>
                            <th class="text-center">Net Cash Sale</th>


                            <th class="text-center">Net Sale</th>
                            <th class="text-center">Credit Stock Transfer</th>
                            <th class="text-center">Cash Stock Transfer</th>
                            <th class="text-center">Net Cash Activity</th>
                            <th class="text-center">Collection</th>
                            <th class="text-center">Difference</th>
                        </tr>
                        @php

                            $query = "select distinct sales_sales_man ,sales_sales_man_name from vw_sale_sales_invoice
                            where (SALES_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd')) and branch_id in( ".implode(",",$data['branch_ids']).")";

                            $list = \Illuminate\Support\Facades\DB::select($query);
                           // dd($list);
                                $total_CrSale =0;
                                $total_CrSaleR=0;
                                $Net_total_CrSale=0;

                                $total_VCSale =0;
                                $total_VCSaleR=0;
                                $Net_total_VCSale=0;

                                $total_CaSale =0;
                                $total_CaSaleR=0;
                                $Net_total_CaSaleR=0;
                                $Net_total_Sale =0;
                                $total_ColctionAmt =0;
                                $total_diffAmt =0;

                                $total_CaStockTrasfer = 0;
                                $total_CrStockTrasfer = 0;
                                $total_net_cash_activity = 0;
                        @endphp
                        @if(isset($list))
                            @foreach($list as $saleData)
                            <tr>
                                {{--No--}}
                                <td class="text-center">{{ $loop->iteration }}</td>
                                {{--Cashier's Name--}}
                                <td class="text-center">{{$saleData->sales_sales_man_name}}</td>
                                {{--CR Sale--}}
                                @php
                                    if(\App\Models\Defi\TblDefiConstants::where('constants_key','subdomain')->where('constants_status',1)->exists()){
                                        $subdomain = \App\Models\Defi\TblDefiConstants::where('constants_key','subdomain')->first()->constants_value;
                                    
                                        if($subdomain == 'adminalnawras'){
                                            $CrSaleQuery = "select sum(sales_net_amount) sales_net_amount from(
                                            select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                            where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SI' OR sales_type = 'POS' OR sales_type = 'SIC') and (sales_sales_type = '2' OR sales_sales_type = '3' OR sales_sales_type = '1') and
                                            (SALES_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd'))  and branch_id in( ".implode(",",$data['branch_ids']).")
                                            ) abc";
                                        }
                                    }
                                    else{
                                        $CrSaleQuery = "select sum(sales_net_amount) sales_net_amount from(
                                        select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                        where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SI' OR sales_type = 'POS') and (sales_sales_type = '2' OR sales_sales_type = '3') and
                                        (SALES_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd'))  and branch_id in( ".implode(",",$data['branch_ids']).")
                                        ) abc";
                                    }
                                    $CrSale = \Illuminate\Support\Facades\DB::select($CrSaleQuery);
                                    $CrSale = isset($CrSale[0]->sales_net_amount)?$CrSale[0]->sales_net_amount:0;
                                @endphp
                                <td class="text-right">{{number_format($CrSale,3)}}</td>
                                {{--CR Sale Rtr--}}
                                @php
                                    $CrSaleRQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                    select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                    where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SR' OR sales_type = 'RPOS') and (sales_sales_type = '2' OR sales_sales_type = '3') and
                                                    (SALES_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd')) and branch_id in( ".implode(",",$data['branch_ids']).")
                                                ) abc";
                                    $CrSaleR = \Illuminate\Support\Facades\DB::select($CrSaleRQuery);
                                    $CrSaleR = isset($CrSaleR[0]->sales_net_amount)?$CrSaleR[0]->sales_net_amount:0;
                                @endphp
                                <td class="text-right">{{number_format($CrSaleR,3)}}</td>
                                {{--Net Cr Sale--}}
                                @php $t_Cr_Sale = $CrSale - $CrSaleR; @endphp
                                <td class="text-right">{{number_format($t_Cr_Sale,3)}}</td>
                                {{--VC Sale--}}
                                @php
                                    $VCSaleQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                    select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                    where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SI' OR sales_type = 'POS') and (sales_sales_type = '4' OR sales_sales_type = '5')and
                                                    (SALES_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd')) and branch_id in( ".implode(",",$data['branch_ids']).")
                                                ) abc";
                                    $VCSale = \Illuminate\Support\Facades\DB::select($VCSaleQuery);
                                    $VCSale = isset($VCSale[0]->sales_net_amount)?$VCSale[0]->sales_net_amount:0;
                                @endphp
                                <td class="text-right">{{number_format($VCSale,3)}}</td>
                                {{--VC Sale Rtr--}}
                                @php
                                    $VCSaleRQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                    select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                    where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SR' OR sales_type = 'RPOS') and (sales_sales_type = '4' OR sales_sales_type = '5') and
                                                    (SALES_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd')) and branch_id in( ".implode(",",$data['branch_ids']).")
                                                ) abc";
                                    $VCSaleR = \Illuminate\Support\Facades\DB::select($VCSaleRQuery);
                                    $VCSaleR = isset($VCSaleR[0]->sales_net_amount)?$VCSaleR[0]->sales_net_amount:0;
                                @endphp
                                <td class="text-right">{{number_format($VCSaleR,3)}}</td>
                                {{--Net VC Sale--}}
                                @php $t_VC_Sale = $VCSale - $VCSaleR; @endphp
                                <td class="text-right">{{number_format($t_VC_Sale,3)}}</td>

                                {{--Cash Sale--}}
                                    @php
                                        $CaSaleQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                        select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                        where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SI' OR sales_type = 'POS') and sales_sales_type = '1' and
                                                        (SALES_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd')) and branch_id in( ".implode(",",$data['branch_ids']).")
                                                    ) abc";
                                        $CaSale = \Illuminate\Support\Facades\DB::select($CaSaleQuery);
                                        $CaSale = isset($CaSale[0]->sales_net_amount)?$CaSale[0]->sales_net_amount:0;
                                    @endphp
                                <td class="text-right">{{number_format($CaSale,3)}}</td>
                                {{--Cash Sale Rtr--}}
                                @php
                                    $CaSaleRQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                    select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                    where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SR' OR sales_type = 'RPOS') and sales_sales_type = '1' and
                                                    (SALES_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd')) and branch_id in( ".implode(",",$data['branch_ids']).")
                                                ) abc";
                                    $CaSaleR = \Illuminate\Support\Facades\DB::select($CaSaleRQuery);
                                    $CaSaleR = isset($CaSaleR[0]->sales_net_amount)?$CaSaleR[0]->sales_net_amount:0;
                                @endphp
                                <td class="text-right">{{number_format($CaSaleR,3)}}</td>
                                {{--Net Cash Sale--}}
                                @php $t_Ca_Sale = $CaSale - $CaSaleR; @endphp
                                <td class="text-right">{{number_format($t_Ca_Sale,3)}}</td>

                                {{--Net Sale--}}
                                @php $net_sale = $t_Cr_Sale + $t_Ca_Sale + $t_VC_Sale;  @endphp
                                <td class="text-right">{{number_format($net_sale,3)}}</td>
                                {{--Credit Stock Transfer--}}
                                @php
                                    $CrStQuery = "select sum(stock_dtl_total_amount) stock_dtl_total_amount from(
                                                    select stock_id , stock_dtl_total_amount from vw_inve_stock
                                                    where stock_user_id = '".$saleData->sales_sales_man."' and (stock_code_type = 'st') and (sales_sales_type = '2' OR sales_sales_type = '3') and
                                                    (STOCK_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd'))  and branch_id in( ".implode(",",$data['branch_ids']).")
                                                ) abc";
                                    $CrSt = \Illuminate\Support\Facades\DB::selectOne($CrStQuery);
                                    $CrSt = isset($CrSt->stock_dtl_total_amount)?$CrSt->stock_dtl_total_amount:0;
                                @endphp
                                <td class="text-right">{{number_format($CrSt,3)}}</td>
                                {{--Cash Stock Transfer--}}
                                @php
                                    $CaStQuery = "select sum(stock_dtl_total_amount) stock_dtl_total_amount from(
                                                    select stock_id , stock_dtl_total_amount from vw_inve_stock
                                                    where stock_user_id = '".$saleData->sales_sales_man."' and (stock_code_type = 'st') and (sales_sales_type = '1') and
                                                    (STOCK_DATE between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd'))  and branch_id in( ".implode(",",$data['branch_ids']).")
                                                ) abc";
                                    $CaSt = \Illuminate\Support\Facades\DB::selectOne($CaStQuery);
                                    $CaSt = isset($CaSt->stock_dtl_total_amount)?$CaSt->stock_dtl_total_amount:0;
                                @endphp
                                <td class="text-right">{{number_format($CaSt,3)}}</td>
                                {{-- Net Cash Activity --}}
                                @php $net_cash_activity = $t_Ca_Sale + $CaSt;  @endphp
                                <td class="text-right">{{number_format($net_cash_activity,3)}}</td>
                                {{--Collection--}}
                                @php
                                    $ColctionQuery = "select sum(day_amount) amount from vw_sale_day where day_payment_handover_received = '".$saleData->sales_sales_man."' and day_case_type = 'payment-received'
                                                    and (day_date between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd')) and branch_id in( ".implode(",",$data['branch_ids']).")";
                                    $Colction = \Illuminate\Support\Facades\DB::select($ColctionQuery);
                                    $ColctionAmt = isset($Colction[0]->amount)?$Colction[0]->amount:0;
                                @endphp
                                <td class="text-right">{{number_format($ColctionAmt,3)}}</td>
                                {{--Difference--}}
                                @php $diffAmt = $ColctionAmt - $net_cash_activity;
                                    if($diffAmt < 0)
                                    {
                                        $conColr = 'text-danger';
                                    }else{
                                        $conColr = '';
                                    }
                                @endphp
                                <td class="text-right {{$conColr}}">{{number_format($diffAmt,3)}}</td>
                            </tr>
                                @php
                                    $total_CrSale += $CrSale;
                                    $total_CrSaleR += $CrSaleR;
                                    $Net_total_CrSale += $t_Cr_Sale;
                                    $total_VCSale += $VCSale;
                                    $total_VCSaleR += $VCSaleR;
                                    $Net_total_VCSale += $t_VC_Sale;
                                    $total_CaSale += $CaSale;
                                    $total_CaSaleR += $CaSaleR;
                                    $Net_total_CaSaleR += $t_Ca_Sale;
                                    $Net_total_Sale += $net_sale;
                                    $total_CrStockTrasfer += $CrSt;
                                    $total_CaStockTrasfer += $CaSt;
                                    $total_net_cash_activity += $net_cash_activity;
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
                            <td class="text-right rep-font-bold">{{number_format($total_VCSale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_VCSaleR,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($Net_total_VCSale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CaSale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CaSaleR,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($Net_total_CaSaleR,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($Net_total_Sale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CrStockTrasfer,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CaStockTrasfer,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_net_cash_activity,3)}}</td>
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
                                    $CashRece = "select voucher_no,voucher_id, voucher_sr_no ,voucher_credit,chart_name , voucher_descrip from vw_acco_voucher
                                                    where voucher_type = 'crv' and voucher_credit > 0 and (voucher_date between to_date('".$data['only_date']."','yyyy/mm/dd') and to_date('".$data['only_date']."','yyyy/mm/dd'))
                                                     and branch_id in( ".implode(",",$data['branch_ids']).")";
                                    $CashReceData = \Illuminate\Support\Facades\DB::select($CashRece);
                                    $tot_CashRece = 0;
                                @endphp
                                @if(isset($CashReceData))
                                    @foreach($CashReceData as $CashRece)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-left">{{$CashRece->chart_name}}</td>
                                            <td class="text-left">{{$CashRece->voucher_descrip}}</td>
                                            <td class="text-right">{{number_format($CashRece->voucher_credit,3)}}</td>
                                        </tr>
                                        @php $tot_CashRece += $CashRece->voucher_credit; @endphp
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
                            <h5>Cash Paid TO</h5>
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
                                    $CashPay = "select voucher_no,voucher_id, voucher_sr_no ,voucher_debit,chart_name , voucher_descrip from vw_acco_voucher
                                                    where voucher_type = 'cpv' and voucher_debit > 0 and (voucher_date between to_date('".$data['only_date']."','yyyy/mm/dd') and to_date('".$data['only_date']."','yyyy/mm/dd'))
                                                     and branch_id in( ".implode(",",$data['branch_ids']).")";
                                    $CashPayData = \Illuminate\Support\Facades\DB::select($CashPay);
                                    $tot_CashPay = 0;
                                @endphp
                                @if(isset($CashPayData))
                                    @foreach($CashPayData as $CashPay)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-left">{{$CashPay->chart_name}}</td>
                                            <td class="text-left">{{$CashPay->voucher_descrip}}</td>
                                            <td class="text-right">{{number_format($CashPay->voucher_debit,3)}}</td>
                                        </tr>
                                        @php $tot_CashPay += $CashPay->voucher_debit; @endphp
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
                            <table id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                                <tr class="total">
                                    <td class="text-left">Notes</td>
                                    @php
                                       $denomination = \App\Models\TblDefiDenomination::where('denomination_entry_status',1)->orderby('denomination_id','asc')->get();
                                       $rowCount = count($denomination);
                                       $tot_BD = 0;
                                    @endphp
                                    @foreach($denomination as $Notes)
                                        <td class="text-center">{{$Notes->denomination_name}}</td>
                                    @endforeach
                                </tr>
                                @php
                                    $Bank_dist = \App\Models\TblSaleBankDistribution::with('distribution_dtl')->whereBetween('bd_date',[$data['only_date'],$data['only_date']])->whereIn('branch_id',$data['branch_ids'])->orderby('bd_code','ASC')->get();
                                @endphp
                                @foreach($Bank_dist as $Bank_Dtls)
                                    @php
                                        $dtls = isset($Bank_Dtls->distribution_dtl)? $Bank_Dtls->distribution_dtl :[];
                                        $dtl_data = [];
                                        foreach($dtls as $dtl){
                                            $dtl_data[$dtl['sr_no']][] = $dtl;
                                        }
                                    @endphp
                                    @foreach($dtl_data  as $key=>$dtls)
                                        <tr>
                                            <td class="rep-font-bold">
                                                @php
                                                    $rowTotal = 0;
                                                    $bank_name = \App\Models\TblAccCoa::where('chart_account_id',$dtls[0]->bank_id)->first('chart_name');
                                                @endphp
                                                {{$bank_name->chart_name}}
                                            </td>
                                        </tr>
                                        <tr class="sub_total">
                                            <td class="text-left">No</td>
                                            @foreach($denomination as $denomin)
                                                @php    $qty = ''; @endphp
                                                @foreach($dtls as $dtl)
                                                    @if($dtl->denomination_id == $denomin->denomination_id )
                                                        @php    $qty = $dtl->bd_dtl_qty; @endphp
                                                    @endif
                                                @endforeach
                                                <td class="text-center">{{$qty}}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="sub_total">
                                            <td class="text-left">Amount</td>
                                            @foreach($denomination as $denomin)
                                                @php    $amount = 0; @endphp
                                                @foreach($dtls as $dtl)
                                                    @if($dtl->denomination_id == $denomin->denomination_id )
                                                        @php    $amount = $dtl->bd_dtl_amount; @endphp
                                                        @php $tot_BD += $dtl->bd_dtl_amount; @endphp
                                                        @php $rowTotal += $dtl->bd_dtl_amount; @endphp
                                                    @endif
                                                @endforeach
                                                <td class="text-center">{{number_format($amount,3)}}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td colspan="{{$rowCount}}" class="rep-font-bold">Subtotal:</td>
                                            <td class="text-right rep-font-bold">{{number_format($rowTotal,3)}}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr>
                                    <td colspan="{{$rowCount}}" class="rep-font-bold">Grand Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($tot_BD,3)}}</td>
                                </tr>
                                @php 
                                    $sub_tot = $tot_CashRece + $total_ColctionAmt - $tot_CashPay - $tot_BD;
                                    $sub_tot = ($sub_tot < 0) ? abs($sub_tot) : ($sub_tot * -1); 
                                @endphp
                                <tr class="grand_total">
                                    <td colspan="{{$rowCount}}" class="rep-font-bold">Difference:</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_tot,3)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $bd_qry = "select bd_id,bd_code,bd_date,bank_id,bank_name,sum(bd_dtl_amount) amount,document_verified_status
                                    from vw_sale_bank_distribution
                                    where (bd_date between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd'))
                                    AND branch_id IN (".implode(",",$data['branch_ids']).")
                                    group by(bd_id,bd_code,bd_date,bank_id,bank_name,document_verified_status)
                                    order by bd_date";
                        $bd_data = \Illuminate\Support\Facades\DB::select($bd_qry);
                    @endphp
                    <h5>Bank Distribution Attachments</h5>
                    <table id="" class="table bt-datatable table-bordered">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Date</th>
                                <th>Bank Name</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($bd_data) == 0)
                                <tr>
                                    <td colspan="6">No data found</td>
                                </tr>
                            @endif
                            @foreach($bd_data as $list)
                                <tr>
                                    <td>{{date('d-m-Y',strtotime($list->bd_date))}}</td>
                                    <td>{{$list->bd_code }}</td>
                                    <td>{{$list->bank_name }}</td>
                                    <td>{{$list->amount }}</td>
                                    <td>
                                        <select data-code="{{$list->bd_code }}" name="document_verified_status" id="document_verified_status" class="form-control" style="padding: 0 14px;height: 28px;">
                                            <option value="1" {{$list->document_verified_status == 1?"selected":""}}>Verified</option>
                                            <option value="2" {{($list->document_verified_status == 2 || $list->document_verified_status == null)?"selected":""}}>Not Verified</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" data-id="{{$list->bd_id}}" class="getDocumentsDtl btn btn-sm btn-brand btn-elevate btn-circle btn-icon" style="width: 26px;height: 26px;">
                                            <i class="fa fa-upload" style="font-size: 11px;"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <h5>Summary</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-3">
                            <table id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                                <tr>
                                    <td class="text-left">Collection </td>
                                    <td class="text-right">{{number_format($total_ColctionAmt,3)}}</td>
                                    <td class="text-left">+</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Cash Received </td>
                                    <td class="text-right">{{number_format($tot_CashRece,3)}}</td>
                                    <td class="text-left">+</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Cash Paid </td>
                                    <td class="text-right">{{number_format($tot_CashPay,3)}}</td>
                                    <td class="text-left">-</td>
                                </tr>
                                @php $tot_sub = $total_ColctionAmt + $tot_CashRece - $tot_CashPay  @endphp
                                <tr>
                                    <td class="text-left rep-font-bold">Sub Total</td>
                                    <td class="text-center rep-font-bold" colspan="2">{{number_format($tot_sub,3)}}</td>
                                </tr>
                                <tr class="total">
                                    <td class="text-left">Bank Distribution </td>
                                    <td class="text-right">{{number_format($tot_BD,3)}}</td>
                                    <td class="text-left">-</td>
                                </tr>
                                @php 
                                    $total = $tot_sub - $tot_BD;
                                    $total = ($total < 0) ? abs($total) : ($total * -1);
                                @endphp
                                <tr class="total">
                                    <td class="text-left rep-font-bold">Difference</td>
                                    <td class="text-center rep-font-bold" colspan="2">{{number_format($total,3)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-block" style="margin-top:60px;">
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-3">
                        </div>
                        <div class="col-lg-6">
                            <center>
                                <div width="20px;" style="border-top:1px solid #969696;">
                                    <span>
                                        <h6 style="border-bottom:none; font-weight: 500 !important; font-size: 15px;" >Accountant</h6>
                                    </span>
                                </div>
                            </center>
                        </div>
                        <div class="col-lg-3">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                        <h6></h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                <div class="row">
                        <div class="col-lg-3">
                        </div>
                        <div class="col-lg-6">
                            <center>
                                <div width="20px;" style="border-top:1px solid #969696;">
                                    <span>
                                        <h6 style="border-bottom:none; font-weight: 500 !important; font-size: 15px;" >Manager</h6>
                                    </span>
                                </div>
                            </center>
                        </div>
                        <div class="col-lg-3">
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
    <div id="kt_modal_lg" class="modal fade" data-backdrop="static" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="">
                <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
@endsection

@section('customJS')
    <script>
        $(document).on('click','.getDocumentsDtl',function(){
            var val = $(this).attr('data-id')
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {
                document_id : val,
            }
            var data_url = '/verify-document-view';
            $('#kt_modal_lg').modal('show').find('.modal-content').load(data_url,formData);
        })
        var prev_val = "";
        $(document).on('focus', '#document_verified_status', function (e) {
            prev_val = $(this).val();
        }).on('change', '#document_verified_status', function (e) {
            var thix = $(this);
            var tr = $(this).parents('tr');
            swal.fire({
                title: thix.attr('data-code'),
                text: 'Are you sure to change document status',
                type: 'warning',
                showCancelButton: true,
                showConfirmButton: true
            }).then(function(result){
                if(result.value){
                    var formData = {
                        document_id : thix.parents('tr').find('.getDocumentsDtl').attr('data-id'),
                        document_verified_status : thix.val(),
                    };
                    var url = '/verify-document';
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: url,
                        dataType	: 'json',
                        data        : formData,
                        success: function(response,data) {
                            if(response.status == 'success'){
                                toastr.success(response.message);
                            }else{
                                toastr.error('Something went to wrong');
                            }
                        },
                        error: function(response,status) {}
                    });
                }else{
                    tr.find('#document_verified_status option[value="'+prev_val+'"]').prop('selected', true);
                }
            });
        });
        $(".modal").on('click', '.close', function (e) {
            $('.modal').find('.modal-content').empty();
            $('.modal').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
            $('.modal').modal('hide');
        });

        // Report Verification
        $('#verifyClosingDay').on('click' , function(e){
            e.preventDefault();
            var thix = $(this);
            var fromDate = '{{ $data["from_date"] }}';
            var toDate = '{{ $data["to_date"] }}';
            var reportCase = '{{ $data["report_case"] }}';
            var branches = "{{ json_encode($data['branch_ids']) }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url("reports/verify-closing-day-report") }}',
                method: 'POST',
                cache: false,
                data: { reportName: reportCase,from_date: fromDate, to_date: toDate , branch_ids : branches },
                beforeSend: function(){
                    thix.prop('disabled' , true);
                    $('body').addClass('pointerEventsNone');
                },
                success:function(response){
                    $('body').removeClass('pointerEventsNone');
                    if(response.status == 'success'){
                        thix.removeClass('btn-primary').addClass('btn-success');
                        thix.html('').html('<i class="fa fa-check"></i> Report Verified');

                        toastr.success('Report Verified Successfully');
                    }
                },
                error:function(response){
                    thix.prop('disabled' , false);
                    $('body').removeClass('pointerEventsNone');
                    toastr.error(response.data.message);
                } 
            });
        });
    </script>
@endsection
@section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_sale_invoice_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



