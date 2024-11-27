@extends('layouts.report')
@section('title', 'Supplier Wise for Rebate Calculation Report')

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
        //dd($data);
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
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['supplier_ids']) != 0)
                    @php $suppliers = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->whereIn('supplier_id',$data['supplier_ids'])->get(); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier:</span>
                        @foreach($suppliers as $supplier)
                            <span style="color: #5578eb;">{{$supplier->supplier_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @else
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier: </span>
                        <span style="color: #5578eb;"> All</span><span style="color: #fd397a;">, </span>
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        @php
            $where = "";
            $where .= " AND branch_id in (".implode(",",$data['branch_ids']).")";
            $where .= " AND (grn_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd'))";
            if(count($data['supplier_ids']) != 0){
                $where .= " AND supplier_id in (".implode(",",$data['supplier_ids']).")";
            }

            $query = "SELECT * FROM
                (
                select BUSINESS_ID , COMPANY_ID , BRANCH_ID ,
                GRN_TYPE , GRN_ID , GRN_CODE ,  GRN_DATE , SUPPLIER_ID ,  SUPPLIER_NAME ,
                GRN_TOTAL_NET_AMOUNT  GRN_AMOUNT_WITH_VAT  ,  GRN_TOTAL_NET_AMOUNT  - SUM( TBL_PURC_GRN_DTL_VAT_AMOUNT) GRN_AMOUNT_WITHOUT_VAT ,
                0   GRN_RETURN_AMOUNT_WITH_VAT   , 0   GRN_RETURN_AMOUNT_WITHOUT_VAT ,
                0   PRD_AMOUNT_WITH_VAT   , 0   PRD_AMOUNT_WITHOUT_VAT
                from VW_PURC_GRN where upper(GRN_TYPE) =   'GRN' $where
                GROUP BY   BUSINESS_ID , COMPANY_ID , BRANCH_ID ,
                GRN_TYPE , GRN_ID , GRN_CODE ,  GRN_DATE , SUPPLIER_ID ,  SUPPLIER_NAME ,
                GRN_TOTAL_NET_AMOUNT
                UNION ALL

                select BUSINESS_ID , COMPANY_ID , BRANCH_ID ,
                GRN_TYPE , GRN_ID , GRN_CODE ,  GRN_DATE , SUPPLIER_ID ,  SUPPLIER_NAME ,
                 0 GRN_AMOUNT_WITH_VAT  ,  0 GRN_AMOUNT_WITHOUT_VAT ,
                GRN_TOTAL_NET_AMOUNT   GRN_RETURN_AMOUNT_WITH_VAT   , GRN_TOTAL_NET_AMOUNT  - SUM( TBL_PURC_GRN_DTL_VAT_AMOUNT)   GRN_RETURN_AMOUNT_WITHOUT_VAT ,
                0   PRD_AMOUNT_WITH_VAT   , 0   PRD_AMOUNT_WITHOUT_VAT
                from VW_PURC_GRN
                where upper(GRN_TYPE) =   'PR' $where

                GROUP BY   BUSINESS_ID , COMPANY_ID , BRANCH_ID ,
                GRN_TYPE , GRN_ID , GRN_CODE ,  GRN_DATE , SUPPLIER_ID ,  SUPPLIER_NAME ,
                GRN_TOTAL_NET_AMOUNT

                UNION ALL

                select BUSINESS_ID , COMPANY_ID , BRANCH_ID ,
                GRN_TYPE , GRN_ID , GRN_CODE ,  GRN_DATE , SUPPLIER_ID ,  SUPPLIER_NAME ,
                0  GRN_AMOUNT_WITH_VAT  ,  0 GRN_AMOUNT_WITHOUT_VAT ,
                0   GRN_RETURN_AMOUNT_WITH_VAT   , 0   GRN_RETURN_AMOUNT_WITHOUT_VAT ,
                GRN_TOTAL_NET_AMOUNT   PRD_AMOUNT_WITH_VAT   , GRN_TOTAL_NET_AMOUNT  - SUM( TBL_PURC_GRN_DTL_VAT_AMOUNT)   PRD_AMOUNT_WITHOUT_VAT
                from VW_PURC_GRN
                where upper(GRN_TYPE) =   'PDS' $where

                GROUP BY   BUSINESS_ID , COMPANY_ID , BRANCH_ID ,
                GRN_TYPE , GRN_ID , GRN_CODE ,  GRN_DATE , SUPPLIER_ID ,  SUPPLIER_NAME ,
                GRN_TOTAL_NET_AMOUNT
                ) rpt  order by    SUPPLIER_NAME ,  GRN_DATE ,GRN_CODE";

            $ResultList = DB::select($query);
            $dataList = [];
            foreach ($ResultList as $list){
                $dataList[$list->supplier_name][] = $list;
            }
           //dd($dataList);
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="15%">Document No</th>
                            <th width="15%">Document Date</th>
                            <th colspan="2" class="text-center">Purchase</th>
                            <th colspan="2" class="text-center">Purchase Return</th>
                            <th colspan="2" class="text-center">Price Difference</th>
                            <th colspan="2" class="text-center">Total</th>
                        </tr>
                        <tr>
                            <th class="text-center" colspan="2"></th>
                            <th class="text-center">With Vat</th>
                            <th class="text-center">W/O Vat</th>
                            <th class="text-center">With Vat</th>
                            <th class="text-center">W/O Vat</th>
                            <th class="text-center">With Vat</th>
                            <th class="text-center">W/O Vat</th>
                            <th class="text-center">With Vat</th>
                            <th class="text-center">W/0 Vat</th>
                        </tr>
                        @php
                            $gt_grn_amount_with_vat = 0;
                            $gt_grn_amount_without_vat = 0;
                            $gt_grn_return_amount_with_vat = 0;
                            $gt_grn_return_amount_without_vat = 0;
                            $gt_prd_amount_with_vat = 0;
                            $gt_prd_amount_without_vat = 0;
                            $gt_ttl_amount_with_vat = 0;
                            $gt_ttl_amount_without_vat = 0;
                        @endphp
                        @foreach($dataList as $name=>$items)
                            <tr>
                                <td colspan="10">{{$name}}</td>
                            </tr>
                            @php
                                $t_grn_amount_with_vat = 0;
                                $t_grn_amount_without_vat = 0;
                                $t_grn_return_amount_with_vat = 0;
                                $t_grn_return_amount_without_vat = 0;
                                $t_prd_amount_with_vat = 0;
                                $t_prd_amount_without_vat = 0;
                                $t_ttl_amount_with_vat = 0;
                                $t_ttl_amount_without_vat = 0;
                            @endphp
                            @foreach($items as $item)
                                <tr>
                                    <td><span class="generate_report" data-id="{{$item->grn_id}}" data-type="{{$item->grn_type}}">{{$item->grn_code}}</span></td>
                                    <td>{{date('d-m-Y',strtotime($item->grn_date))}}</td>
                                    <td class="text-right">{{number_format($item->grn_amount_with_vat,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_amount_without_vat,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_return_amount_with_vat,3)}}</td>
                                    <td class="text-right">{{number_format($item->grn_return_amount_without_vat,3)}}</td>
                                    <td class="text-right">{{number_format($item->prd_amount_with_vat,3)}}</td>
                                    <td class="text-right">{{number_format($item->prd_amount_without_vat,3)}}</td>
                                    @php $item_ttl_with_vat = $item->grn_amount_with_vat - $item->grn_return_amount_with_vat - $item->prd_amount_with_vat;  @endphp
                                    <td class="text-right">{{ number_format(($item_ttl_with_vat),3) }}</td>
                                    @php $item_ttl_without_vat = $item->grn_amount_without_vat - $item->grn_return_amount_without_vat - $item->prd_amount_without_vat;  @endphp
                                    <td class="text-right">{{ number_format(($item_ttl_without_vat),3) }}</td>
                                </tr>
                                @php
                                    $t_grn_amount_with_vat += $item->grn_amount_with_vat;
                                    $t_grn_amount_without_vat += $item->grn_amount_without_vat;
                                    $t_grn_return_amount_with_vat += $item->grn_return_amount_with_vat;
                                    $t_grn_return_amount_without_vat += $item->grn_return_amount_without_vat;
                                    $t_prd_amount_with_vat += $item->prd_amount_with_vat;
                                    $t_prd_amount_without_vat += $item->prd_amount_without_vat;
                                    $t_ttl_amount_with_vat = $t_grn_amount_with_vat - $t_grn_return_amount_with_vat - $t_prd_amount_with_vat;
                                    $t_ttl_amount_without_vat = $t_grn_amount_without_vat - $t_grn_return_amount_without_vat - $t_prd_amount_without_vat;
                                @endphp
                            @endforeach
                            @php
                                $gt_grn_amount_with_vat += $t_grn_amount_with_vat;
                                $gt_grn_amount_without_vat += $t_grn_amount_without_vat;
                                $gt_grn_return_amount_with_vat += $t_grn_return_amount_with_vat;
                                $gt_grn_return_amount_without_vat += $t_grn_return_amount_without_vat;
                                $gt_prd_amount_with_vat += $t_prd_amount_with_vat;
                                $gt_prd_amount_without_vat += $t_prd_amount_without_vat;
                                $gt_ttl_amount_with_vat = $gt_grn_amount_with_vat - $gt_grn_return_amount_with_vat - $gt_prd_amount_with_vat;
                                $gt_ttl_amount_without_vat = $gt_grn_amount_without_vat - $gt_grn_return_amount_without_vat - $gt_prd_amount_without_vat;
                            @endphp
                            <tr class="sub_total">
                                <th colspan="2">{{$name}} # Total</th>
                                <th class="text-right" class="rep-font-bold">{{number_format($t_grn_amount_with_vat,3)}}</th>
                                <th class="text-right rep-font-bold">{{number_format($t_grn_amount_without_vat,3)}}</th>
                                <th class="text-right rep-font-bold">{{number_format($t_grn_return_amount_with_vat,3)}}</th>
                                <th class="text-right rep-font-bold">{{number_format($t_grn_return_amount_without_vat,3)}}</th>
                                <th class="text-right rep-font-bold">{{number_format($t_prd_amount_with_vat,3)}}</th>
                                <th class="text-right rep-font-bold">{{number_format($t_prd_amount_without_vat,3)}}</th>
                                <th class="text-right rep-font-bold">{{number_format(($t_ttl_amount_with_vat),3)}}</th>
                                <th class="text-right rep-font-bold">{{number_format(($t_ttl_amount_without_vat),3)}}</th>
                            </tr>
                        @endforeach
                        {{-- Add Foreach To Put Data Rows --}}
                        <tr class="grand_total">
                            <th colspan="2" class="rep-font-bold">Grand Total : </th>
                            <th class="text-right" class="rep-font-bold">{{number_format($gt_grn_amount_with_vat,3)}}</th>
                            <th class="text-right rep-font-bold">{{number_format($gt_grn_amount_without_vat,3)}}</th>
                            <th class="text-right rep-font-bold">{{number_format($gt_grn_return_amount_with_vat,3)}}</th>
                            <th class="text-right rep-font-bold">{{number_format($gt_grn_return_amount_without_vat,3)}}</th>
                            <th class="text-right rep-font-bold">{{number_format($gt_prd_amount_with_vat,3)}}</th>
                            <th class="text-right rep-font-bold">{{number_format($gt_prd_amount_without_vat,3)}}</th>
                            <th class="text-right rep-font-bold">{{number_format(($gt_ttl_amount_with_vat),3)}}</th>
                            <th class="text-right rep-font-bold">{{number_format(($gt_ttl_amount_without_vat),3)}}</th>
                        </tr>
                    </table>
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



