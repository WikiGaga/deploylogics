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
        $from_date = $data['from_date'];
        $to_date = $data['to_date'];
        $and_where_bcb = " and branch_id in (".implode(",",$data['branch_ids']).") AND business_id = ".auth()->user()->business_id." AND company_id =".auth()->user()->company_id;

        $qry_domestic_taxable_supplies = "SELECT SUM(GROSS_AMT)  GROSS_AMT ,  SUM( VAT_AMOUNT)  VAT_AMOUNT ,  ( SUM(VAT_AMOUNT) / sum(GROSS_AMT)) * 100 VAT_PER FROM
                                        (
                                        select sum(SALES_DTL_TOTAL_AMOUNT) GROSS_AMT ,  SUM( SALES_DTL_VAT_AMOUNT) VAT_AMOUNT  , ( SUM(SALES_DTL_VAT_AMOUNT) / sum(SALES_DTL_TOTAL_AMOUNT)) * 100 VAT_PER
                                        from VW_SALE_SALES_INVOICE WHERE
                                         SALES_DTL_VAT_PER > 0 and (SALES_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb
                                        union all
                                        SELECT sum(STOCK_DTL_AMOUNT) GROSS_AMT ,  SUM(STOCK_DTL_VAT_AMOUNT) VAT_AMT , ( SUM(STOCK_DTL_VAT_AMOUNT) / sum(STOCK_DTL_AMOUNT)) * 100   FROM VW_INVE_STOCK
                                        WHERE   STOCK_CODE_TYPE = 'st' AND STOCK_DTL_VAT_PERCENT > 0
                                        and (STOCK_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb
                                        ) gaga";
        $domestic_taxable_supplies = \Illuminate\Support\Facades\DB::selectOne($qry_domestic_taxable_supplies);

        $qry_domestic_zero_rated_supplies = "SELECT SUM(GROSS_AMT)  GROSS_AMT ,  SUM( VAT_AMOUNT)  VAT_AMOUNT ,  ( SUM(VAT_AMOUNT) / sum(GROSS_AMT)) * 100 VAT_PER FROM
                                        ( select sum(SALES_DTL_TOTAL_AMOUNT) GROSS_AMT ,  0 VAT_AMOUNT  , ( SUM(SALES_DTL_VAT_AMOUNT) / sum(SALES_DTL_TOTAL_AMOUNT)) * 100
                                        from VW_SALE_SALES_INVOICE WHERE
                                        ( SALES_DTL_VAT_PER <= 0 OR  SALES_DTL_VAT_PER IS NULL) and (SALES_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb
                                        union all
                                        SELECT sum(STOCK_DTL_AMOUNT) GROSS_AMT ,  0 VAT_AMT , ( SUM(STOCK_DTL_VAT_AMOUNT) / sum(STOCK_DTL_AMOUNT)) * 100   FROM VW_INVE_STOCK
                                        WHERE   STOCK_CODE_TYPE = 'st' AND (STOCK_DTL_VAT_PERCENT <= 0 or STOCK_DTL_VAT_PERCENT IS NULL )
                                        and (STOCK_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb
                                        ) gaga";
        $domestic_zero_rated_supplies = \Illuminate\Support\Facades\DB::selectOne($qry_domestic_zero_rated_supplies);

        $qry_domestic_taxable_purchases = "SELECT SUM(GROSS_AMT)  GROSS_AMT ,  SUM( VAT_AMOUNT)  VAT_AMOUNT ,  ( SUM(VAT_AMOUNT) / sum(GROSS_AMT)) * 100 VAT_PER FROM
                                            (
                                            select sum(TBL_PURC_GRN_DTL_AMOUNT -  TBL_PURC_GRN_DTL_DISC_AMOUNT ) GROSS_AMT ,  SUM( TBL_PURC_GRN_DTL_VAT_AMOUNT) VAT_AMOUNT  ,  0
                                             from VW_PURC_GRN WHERE  GRN_TYPE = 'GRN' AND
                                             TBL_PURC_GRN_DTL_VAT_AMOUNT > 0  and (GRN_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb
                                             union all
                                             select -sum(TBL_PURC_GRN_DTL_AMOUNT -  TBL_PURC_GRN_DTL_DISC_AMOUNT ) GROSS_AMT ,  SUM( -TBL_PURC_GRN_DTL_VAT_AMOUNT)  , ( SUM(TBL_PURC_GRN_DTL_AMOUNT) / sum(TBL_PURC_GRN_DTL_VAT_AMOUNT)) * 100
                                            from VW_PURC_GRN WHERE  GRN_TYPE = 'PR' AND
                                             TBL_PURC_GRN_DTL_VAT_AMOUNT > 0  and (GRN_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb
                                            union all
                                            SELECT sum(STOCK_DTL_AMOUNT - STOCK_DTL_DISC_AMOUNT) GROSS_AMT ,  SUM(STOCK_DTL_VAT_AMOUNT) VAT_AMT , ( SUM(STOCK_DTL_VAT_AMOUNT) / sum(STOCK_DTL_AMOUNT)) * 100   FROM  VW_INVE_STOCK
                                            WHERE   STOCK_CODE_TYPE = 'str' AND STOCK_DTL_VAT_AMOUNT > 0
                                            and (STOCK_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb
                                            ) gaga";
      //  dd($qry_domestic_taxable_purchases);
        $domestic_taxable_purchases = \Illuminate\Support\Facades\DB::selectOne($qry_domestic_taxable_purchases);


        $qry_domestic_purchases_unregistered = "SELECT SUM(GROSS_AMT)  GROSS_AMT ,  SUM( VAT_AMOUNT)  VAT_AMOUNT ,  ( SUM(VAT_AMOUNT) / sum(GROSS_AMT)) * 100 VAT_PER FROM
                            (
                            select sum(TBL_PURC_GRN_DTL_AMOUNT -  TBL_PURC_GRN_DTL_DISC_AMOUNT ) GROSS_AMT  ,  SUM( TBL_PURC_GRN_DTL_VAT_AMOUNT) VAT_AMOUNT  , 0 VAT_PER
                             from VW_PURC_GRN WHERE  GRN_TYPE = 'GRN' AND
                             (TBL_PURC_GRN_DTL_VAT_AMOUNT <= 0 OR  TBL_PURC_GRN_DTL_VAT_AMOUNT IS NULL)  and (GRN_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb

                             union all

                             select -sum(TBL_PURC_GRN_DTL_AMOUNT -  TBL_PURC_GRN_DTL_DISC_AMOUNT ) GROSS_AMT ,  SUM( -TBL_PURC_GRN_DTL_VAT_AMOUNT)  ,  0 VAT_PER
                             from VW_PURC_GRN WHERE  GRN_TYPE = 'PR' AND
                             (TBL_PURC_GRN_DTL_VAT_AMOUNT <= 0 OR  TBL_PURC_GRN_DTL_VAT_AMOUNT IS NULL)    and (GRN_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb

                            union all
                            SELECT sum(STOCK_DTL_AMOUNT - STOCK_DTL_DISC_AMOUNT) GROSS_AMT ,  SUM(STOCK_DTL_VAT_AMOUNT) VAT_AMT ,  0 VAT_PER   FROM  VW_INVE_STOCK
                            WHERE   STOCK_CODE_TYPE = 'str' AND (STOCK_DTL_VAT_AMOUNT <= 0 OR  STOCK_DTL_VAT_AMOUNT IS NULL)
                            and (STOCK_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd')) $and_where_bcb
                            ) gaga";
        $domestic_purchases_unregistered = \Illuminate\Support\Facades\DB::selectOne($qry_domestic_purchases_unregistered);







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
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                                <tr>
                                    <th width="50%" class="text-left">Particulars</th>
                                    <th width="50%" class="text-right">Voucher Count</th>
                                </tr>
                                <tr>
                                    <td class="rep-font-bold">Total Vouchers</td>
                                    <td class="text-right rep-font-bold">0</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Included In Return</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Not Relevant for This Return</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Uncertain Transaction (Corrections Needed)</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td class="rep-font-bold">Parties With Invalid VATIN</td>
                                    <td class="text-right rep-font-bold">0</td>
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
                            <table id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                                <tr>
                                    <th width="50%" class="text-left">Particulars</th>
                                    <th width="25%" class="text-right">Taxable Amount</th>
                                    <th width="25%" class="text-right">Tax Amount</th>
                                </tr>
                                <tr>
                                    <th class="rep-font-bold">Sales (Outwards):</th>
                                    <th class="text-right rep-font-bold" colspan="2"></th>
                                </tr>
                                <tr>
                                    <td class="rep-font-bold" colspan="3">Local Supplies</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Domestic Taxable Supplies@ {{number_format($domestic_taxable_supplies->vat_per,3)}}% </td>
                                    <td class="text-right">{{number_format($domestic_taxable_supplies->gross_amt,3)}}</td>
                                    <td class="text-right">{{number_format($domestic_taxable_supplies->vat_amount,3)}}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Domestic Zero Rated Supplies@ {{number_format($domestic_zero_rated_supplies->vat_per,3)}}% </td>
                                    <td class="text-right">{{number_format($domestic_zero_rated_supplies->gross_amt,3)}}</td>
                                    <td class="text-right">{{number_format($domestic_zero_rated_supplies->vat_amount,3)}}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Domestic Exempt Supplies</td>
                                    <td class="text-right">{{number_format(0,3)}}</td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td class="rep-font-bold">Reverse Charge</td>
                                    <td class="text-right">{{number_format(0,3)}}</td>
                                    <td class="text-right">{{number_format(0,3)}}</td>
                                </tr>
                                @php
                                    $total_supplies_gross_amt =  (float)$domestic_taxable_supplies->gross_amt + (float)$domestic_zero_rated_supplies->gross_amt;
                                    $total_supplies_vat_amount =  (float)$domestic_taxable_supplies->vat_amount + (float)$domestic_zero_rated_supplies->vat_amount;
                                @endphp
                                <tr class="grand_total">
                                    <td class="text-left rep-font-bold">Total</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_supplies_gross_amt,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_supplies_vat_amount,3)}}</td>
                                </tr>
                                <tr>
                                    <th class="rep-font-bold">Purchases (Inwards):</th>
                                    <th class="text-right rep-font-bold" colspan="2"></th>
                                </tr>
                                <tr>
                                    <td class="rep-font-bold" colspan="3">Local Purchases</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Domestic Taxable Purchases@ {{number_format($domestic_taxable_purchases->vat_per,3)}}% </td>
                                    <td class="text-right">{{number_format($domestic_taxable_purchases->gross_amt,3)}}</td>
                                    <td class="text-right">{{number_format($domestic_taxable_purchases->vat_amount,3)}}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Domestic Purchases - Unregistered@ {{number_format($domestic_purchases_unregistered->vat_per,3)}}% </td>
                                    <td class="text-right">{{number_format($domestic_purchases_unregistered->gross_amt,3)}}</td>
                                    <td class="text-right">{{number_format($domestic_purchases_unregistered->vat_amount,3)}}</td>
                                </tr>
                                <tr class="total">
                                    <td class="rep-font-bold">GCC Purchases</td>
                                    <td class="text-right rep-font-bold">{{number_format(0,3)}}</td>
                                    <td class="text-right rep-font-bold"></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Intra GCC Zero Rated Purchases</td>
                                    <td class="text-right ">{{number_format(0,3)}}</td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td class="rep-font-bold">Adjustments for input</td>
                                    <td class="text-right" colspan="2"></td>
                                </tr>
                                @php
                                    $total_purchases_gross_amt =  (float)$domestic_taxable_purchases->gross_amt + (float)$domestic_purchases_unregistered->gross_amt;
                                    $total_purchases_vat_amount =  (float)$domestic_taxable_purchases->vat_amount + (float)$domestic_purchases_unregistered->vat_amount;
                                @endphp
                                <tr class="grand_total">
                                    <td class="text-left rep-font-bold">Total</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_purchases_gross_amt,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_purchases_vat_amount,3)}}</td>
                                </tr>
                                @php
                                    $payable =  (float)$total_supplies_vat_amount - (float)$total_purchases_vat_amount;
                                @endphp
                                <tr class="grand_total">
                                    <td class="text-left rep-font-bold">Payable</td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($payable,3)}}</td>
                                </tr>
                                <tr>
                                    <th class="rep-font-bold">Payment Details</th>
                                    <th class="text-right rep-font-bold" colspan="2">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Tax payment (inciuded)</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Tax payments (not included/uncertan)</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td class="text-left">Tax Paid at Customs</td>
                                    <td class="text-right">{{number_format(0,0)}}</td>
                                    <td class="text-right">{{number_format(0,0)}}</td>
                                </tr>
                                <tr>
                                    <th class="rep-font-bold">VAT Paid</th>
                                    <th class="text-right rep-font-bold" colspan="2"></th>
                                </tr>
                                <tr>
                                    <td class="rep-font-bold">Balance VAT payable</td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($payable,3)}}</td>
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



