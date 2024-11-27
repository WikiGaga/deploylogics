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
    <style>
        table#rep_sale_invoice_datatable {
            border: 1.4px solid #bbbbbb !important;
        }
    </style>
    @php
        $data = Session::get('data');
        $from_date = $data['from_date'];
        $to_date = $data['to_date'];
        $business_id = auth()->user()->business_id;
        $company_id = auth()->user()->company_id;
        $and_where_bcb = " and branch_id in (".implode(",",$data['branch_ids']).") AND business_id = ".auth()->user()->business_id." AND company_id =".auth()->user()->company_id;

        $qry_domestic_taxable_supplies = "SELECT SUM(GROSS_AMT)  GROSS_AMT ,  SUM( VAT_AMOUNT)  VAT_AMOUNT ,  ( SUM(VAT_AMOUNT) / sum(GROSS_AMT)) * 100 VAT_PER,  (SUM( VAT_AMOUNT) / 5 * 100 ) VAT_SALE FROM
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
                                        from VW_SALE_SALES_INVOICE INV , TBL_PURC_PRODUCT_BARCODE_DTL  PROD WHERE  INV.PRODUCT_BARCODE_ID =  PROD.PRODUCT_BARCODE_ID AND
                                        PROD.PRODUCT_BARCODE_TAX_APPLY = 1 AND PROD.PRODUCT_BARCODE_TAX_VALUE = 0
                                        and (INV.SALES_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd'))
                                        and INV.branch_id in (".implode(",",$data['branch_ids']).") AND INV.business_id = ".$business_id." AND INV.company_id =".$company_id."
                                        union all
                                        SELECT sum(STOCK_DTL_AMOUNT) GROSS_AMT ,  0 VAT_AMT , ( SUM(STOCK_DTL_VAT_AMOUNT) / sum(STOCK_DTL_AMOUNT)) * 100
                                        FROM VW_INVE_STOCK INV, TBL_PURC_PRODUCT_BARCODE_DTL  PROD
                                        WHERE   INV.PRODUCT_BARCODE_ID =  PROD.PRODUCT_BARCODE_ID AND   PROD.PRODUCT_BARCODE_TAX_APPLY = 1 AND PROD.PRODUCT_BARCODE_TAX_VALUE = 0
                                        and INV.STOCK_CODE_TYPE = 'st' AND (INV.STOCK_DTL_VAT_PERCENT <= 0 or INV.STOCK_DTL_VAT_PERCENT IS NULL )
                                        and (INV.STOCK_DATE between to_date('$from_date', 'yyyy/mm/dd') and to_date ('$to_date', 'yyyy/mm/dd'))
                                        and INV.branch_id in (".implode(",",$data['branch_ids']).") AND INV.business_id = ".$business_id." AND INV.company_id =".$company_id."
                                        ) gaga";
        $domestic_zero_rated_supplies = \Illuminate\Support\Facades\DB::selectOne($qry_domestic_zero_rated_supplies);

        $qry_domestic_taxable_purchases = "SELECT SUM(GROSS_AMT)  GROSS_AMT ,  SUM( VAT_AMOUNT)  VAT_AMOUNT ,  ( SUM(VAT_AMOUNT) / sum(GROSS_AMT)) * 100 VAT_PER ,  (SUM( VAT_AMOUNT) / 5 * 100 ) VAT_SALE  FROM
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
                                <tr class="sticky-header">
                                    <th width="5%" class="text-left">1</th>
                                    <th width="55%" class="text-left">Supplies in the Sultanate of Oman</th>
                                    <th width="15%" class="text-right">Taxable Base (OMR)</th>
                                    <th width="15%" class="text-right">VAT Due (OMR)</th>
                                </tr>
                                <tr>
                                    <td>1a</td>
                                    <td class="rep-font">Supplies of goods / services taxed at 5%</td>
                                    <td class="text-right">{{number_format($domestic_taxable_supplies->vat_sale,3)}}</td>
                                    <td class="text-right">{{number_format($domestic_taxable_supplies->vat_amount,3)}}</td>
                                </tr>
                                <tr>
                                    <td>1b</td>
                                    <td class="text-left">Supplies of goods / services taxed at {{number_format($domestic_zero_rated_supplies->vat_per,2)}}%</td>
                                    <td class="text-right">{{number_format($domestic_zero_rated_supplies->gross_amt,3)}}</td>
                                    <td class="text-right">{{number_format($domestic_zero_rated_supplies->vat_amount,3)}}</td>
                                </tr>
                                <tr>
                                    <td>1c</td>
                                    <td class="text-left">Supplies of goods / services tax exempt</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>1d</td>
                                    <td class="text-left">Supplies of goods, tax levy shifted to recipient inside GCC (supplies made by you that are subject to Reverse Charge Mechanism)</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>1e</td>
                                    <td class="rep-font">Supplies of services, tax levy shifted to recipient inside GCC (supplies made by you that are subject to Reverse Charge Mechanism)</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>1f</td>
                                    <td class="rep-font">Supply of goods as per profit margin scheme</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
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
                                    <th width="5%" class="text-left">2</th>
                                    <th width="55%" class="text-left">Purchases subject to Reverse Charge Mechanism</th>
                                    <th width="15%" class="text-right">Taxable Base (OMR)</th>
                                    <th width="15%" class="text-right">VAT Due (OMR)</th>
                                </tr>
                                <tr>
                                    <td>2a</td>
                                    <td class="rep-font">Purchases from the GCC subject to Reverse Charge Mechanism: {{number_format($domestic_taxable_purchases->vat_per,2)}}%</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>2b</td>
                                    <td class="text-left">Purchases from outside of GCC subject to Reverse Charge Mechanism</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                @php
                                    $total_supplies_gross_amt =  (float)$domestic_taxable_supplies->gross_amt + (float)$domestic_zero_rated_supplies->gross_amt;
                                    $total_supplies_vat_amount =  (float)$domestic_taxable_supplies->vat_amount + (float)$domestic_zero_rated_supplies->vat_amount;
                                @endphp

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
                                    <th width="5%">3</th>
                                    <th width="55%" class="text-left">Supplies to countries outside of Oman</th>
                                    <th width="15%" class="text-right">Taxable Base (OMR)</th>
                                    <th width="15%" class="text-right">VAT Due (OMR)</th>
                                </tr>
                                <tr>
                                    <td>3a</td>
                                    <td class="text-left">Exports</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
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
                                    <th width="5%">4</th>
                                    <th width="55%" class="text-left">Imports of Goods</th>
                                    <th width="15%" class="text-right">Taxable Base (OMR)</th>
                                    <th width="15%" class="text-right">VAT Due (OMR)</th>
                                </tr>
                                <tr>
                                    <td>4a</td>
                                    <td class="text-left">Import of Goods (Postponed payment)</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>4b</td>
                                    <td class="text-left">Total goods imported</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
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
                                    <th width="5%">5</th>
                                    <th width="55%" class="text-left">Total VAT due</th>
                                    <th width="15%" class="text-right"></th>
                                    <th width="15%" class="text-right">OMR</th>
                                </tr>
                                <tr>
                                    <td>5a</td>
                                    <td class="text-left">Total VAT due under (1(a)+1(f)+2(a)+2(b)+4(a)):</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>5b</td>
                                    <td class="text-left">Adjustment of VAT due</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
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
                                    <th width="5%">6</th>
                                    <th width="55%" class="text-left">Input VAT credit</th>
                                    <th width="15%" class="text-right">OMR</th>
                                    <th width="15%" class="text-right">Receiveable VAT(OMR)</th>
                                </tr>
                                <tr>
                                    <td>6a</td>
                                    <td class="text-left">Purchases (except import of goods)</td>
                                    <td class="text-right">{{number_format($domestic_taxable_purchases->vat_sale,3)}}</td>
                                    <td class="text-right">{{number_format($domestic_taxable_purchases->vat_amount,3)}}</td>
                                </tr>
                                <tr>
                                    <td>6b</td>
                                    <td class="text-left">Import of goods</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>6c</td>
                                    <td class="text-left">VAT on acquisition of fixed assets</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>6d</td>
                                    <td class="text-left">Adjustment of input VAT credit</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
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
                                    <th width="5%">7</th>
                                    <th width="55%" class="text-left">Tax liability calculation</th>
                                    <th width="15%" class="text-right"></th>
                                    <th width="15%" class="text-right">OMR</th>
                                </tr>
                                <tr>
                                    <td>7a</td>
                                    <td class="text-left">Total VAT due (5(a) + 5(b)):</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>7b</td>
                                    <td class="text-left">Total input VAT Credit (6(a)+6(b)+6(c)+6(d)):</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>7c</td>
                                    <td class="text-left">Total (7(a) + 7(b))</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
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



