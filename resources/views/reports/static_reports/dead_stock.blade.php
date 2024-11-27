@extends('layouts.report')
@section('title', 'Dead Stock Report')

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
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date_time_from']))." to ". date('d-m-Y', strtotime($data['date_time_to']))." "}}</span>
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
                @if(count($data['product_ids']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @foreach($data['product_ids'] as $product)
                            <span style="color: #5578eb;">{{$product}}</span><span style="color: #fd397a;">, </span>
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
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $where = "";
                        $days="";
                        
                        if(!empty($data['dead_days']) || $data['dead_days'] != 0){
                            $days = $data['dead_days'];
                        }

                        /*if(isset($data['supplier_ids']) && count($data['supplier_ids']) != 0){
                            $where .= " and PROD.supplier_id in (".implode(",",$data['supplier_ids']).") ";
                        }
                        if(count($data['product_ids']) != 0){
                            $where .= " and PROD.product_name in ('".implode("','",$data['product_ids'])."') ";
                        } */
                        
                       // and (s.created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                        
                        $qry = "SELECT 
                            BRANCH_ID, 
                            GROUP_ITEM_NAME, 
                            GROUP_ITEM_PARENT_NAME, 
                            PRODUCT_BARCODE_BARCODE, 
                            PRODUCT_BARCODE_ID, 
                            PRODUCT_ID, 
                            PRODUCT_NAME, 
                            UOM_NAME, 
                            PRODUCT_BARCODE_PACKING, 
                            BRAND_NAME, 
                            NET_TP, 
                            SALE_RATE, 
                            SUPPLIER_NAME, 
                            TBL_PURC_GRN_DTL_NET_TP, 
                            TBL_PURC_GRN_DTL_QUANTITY, 
                            GRN_DATE, 
                            SALES_DATE, 
                            CURRENT_STOCK
                            --  ,  CASE WHEN SALES_DATE is not null  THEN date '2000-01-02' - date '2000-01-01' ELSE 0  END AS DateDiff   
                        from 
                            (SELECT DISTINCT 
                                STOCK.BRANCH_ID,
                                GROUP_ITEM_NAME,
                                GROUP_ITEM_PARENT_NAME,
                                MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
                                MAX(PRODUCT_BARCODE_ID) PRODUCT_BARCODE_ID,
                                PROD.PRODUCT_ID,
                                PRODUCT_NAME, 
                                max(UOM_NAME) UOM_NAME ,
                                1  PRODUCT_BARCODE_PACKING, 
                                BRAND_NAME,

                                (SELECT 
                                    NET_TP 
                                FROM
                                    TBL_PURC_PRODUCT_BARCODE_PURCH_RATE RATE 
                                WHERE RATE.PRODUCT_ID = PROD.PRODUCT_ID 
                                    AND RATE.BRANCH_ID = STOCK.BRANCH_ID 
                                    FETCH FIRST 1 ROWS ONLY
                                ) NET_TP,

                                (SELECT 
                                    SALE_RATE 
                                FROM
                                    TBL_PURC_PRODUCT_BARCODE_PURCH_RATE RATE 
                                WHERE RATE.PRODUCT_ID = PROD.PRODUCT_ID   
                                    AND RATE.BRANCH_ID = STOCK.BRANCH_ID 
                                FETCH FIRST 1 ROWS ONLY
                                ) SALE_RATE,

                                (SELECT 
                                    SUPPLIER_NAME 
                                FROM
                                    VW_PURC_GRN GRN 
                                WHERE GRN.PRODUCT_ID = PROD.PRODUCT_ID 
                                    AND UPPER(GRN_TYPE) = 'GRN' 
                                    and GRN.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                ORDER BY grn_code DESC,
                                    CREATED_AT DESC FETCH FIRST 1 ROWS ONLY
                                ) SUPPLIER_NAME,

                                (SELECT 
                                    TBL_PURC_GRN_DTL_NET_TP 
                                FROM
                                    VW_PURC_GRN GRN 
                                WHERE GRN.PRODUCT_ID = PROD.PRODUCT_ID 
                                    AND UPPER(GRN_TYPE) = 'GRN' 
                                    and GRN.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                ORDER BY grn_code DESC,
                                    CREATED_AT DESC FETCH FIRST 1 ROWS ONLY
                                ) TBL_PURC_GRN_DTL_NET_TP,

                                (SELECT 
                                    TBL_PURC_GRN_DTL_QUANTITY 
                                FROM
                                    VW_PURC_GRN GRN 
                                WHERE GRN.PRODUCT_ID = PROD.PRODUCT_ID 
                                    AND UPPER(GRN_TYPE) = 'GRN' 
                                    and GRN.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                ORDER BY grn_code DESC,
                                    CREATED_AT DESC FETCH FIRST 1 ROWS ONLY
                                ) TBL_PURC_GRN_DTL_QUANTITY,

                                (SELECT 
                                    GRN_DATE 
                                FROM
                                    VW_PURC_GRN GRN 
                                WHERE GRN.PRODUCT_ID = PROD.PRODUCT_ID 
                                    AND UPPER(GRN_TYPE) = 'GRN' 
                                    and GRN.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                ORDER BY grn_code DESC,
                                    CREATED_AT DESC FETCH FIRST 1 ROWS ONLY
                                ) GRN_DATE,

                                (SELECT 
                                    SALE_DTL.SALES_DATE 
                                FROM
                                    TBL_SALE_SALES_DTL SALE, TBL_SALE_SALES SALE_DTL 
                                WHERE SALE.SALES_ID = SALE_DTL.SALES_ID 
                                    AND SALE.PRODUCT_ID = PROD.PRODUCT_ID 
                                    and SALE.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                ORDER BY SALE.CREATED_AT DESC 
                                    FETCH FIRST 1 ROWS ONLY
                                ) SALES_DATE, STOCK.CURRENT_STOCK
                            FROM
                                (SELECT 
                                    S.BRANCH_ID,
                                    s.PRODUCT_ID, 
                                    SUM (s.QTY_BASE_UNIT_VALUE)     CURRENT_STOCK 
                                    FROM VW_PURC_STOCK_DTL s
                                    WHERE s.BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
                                        AND (s.created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                                    GROUP BY s.PRODUCT_ID,
                                    S.BRANCH_ID 
                                ) STOCK, VW_PURC_PRODUCT_BARCODE PROD    
                            WHERE PROD.PRODUCT_ID = STOCK.PRODUCT_ID  
                            GROUP BY PRODUCT_NAME, 
                                BRAND_NAME,
                                GROUP_ITEM_NAME,
                                GROUP_ITEM_PARENT_NAME,
                                PROD.PRODUCT_ID,
                                STOCK.CURRENT_STOCK ,
                                STOCK.BRANCH_ID 
                            )gaga";
                                
           //dd($qry);    
                        
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        //dd($getdata);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[] = $row;
                        }
                        //dd($list);
                        @endphp
                        @php
                            $si_grand_total_amount = 0;
                        @endphp
                        <table width="100%" id="rep_fbr_sales_data_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th class="text-center">S.#</th>
                                <th class="text-left">1st Level Category</th>
                                <th class="text-left">Last Level Category</th>
                                <th class="text-left">Product Item Description</th>
                                <th class="text-left">Barcode</th>
                                <th class="text-center">Brand Name</th>
                                <th class="text-center">Supplier Name</th>
                                <th class="text-center">GRN Date</th>
                                <th class="text-center">Sale Date</th>
                                <th class="text-center">Net TP</th>
                                <th class="text-center">Sale Rate</th>
                                <th class="text-center">GRN Net TP</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Current Stock</th>
                            </tr>
                            @php
                                $ki=1;
                                $totqty = 0;
                                $totcurrstock = 0;
                            @endphp
                            @foreach($list as $inv_k=>$si_detail)
                                @php
                                    $totqty = $totqty + $si_detail->tbl_purc_grn_dtl_quantity;
                                    $totcurrstock = $totcurrstock + $si_detail->current_stock;
                                @endphp
                                    <tr>
                                        <td class="text-center">{{$ki}}</td>
                                        <td class="text-left">{{$si_detail->group_item_parent_name}}</td>
                                        <td class="text-left">{{$si_detail->group_item_name}}</td>
                                        <td class="text-left">{{$si_detail->product_name}}</td>
                                        <td class="text-left">{{$si_detail->product_barcode_barcode}}</td>
                                        <td class="text-left">{{$si_detail->brand_name}}</td>
                                        <td class="text-left">{{$si_detail->supplier_name}}</td>
                                        <td class="text-center">{{date('d-m-Y', strtotime($si_detail->grn_date))}}</td>
                                        <td class="text-center">{{date('d-m-Y', strtotime($si_detail->sales_date))}}</td>
                                        <td class="text-right">{{number_format($si_detail->net_tp,2)}}</td>
                                        <td class="text-right">{{number_format($si_detail->sale_rate,2)}}</td>
                                        <td class="text-right">{{number_format($si_detail->tbl_purc_grn_dtl_net_tp,2)}}</td>
                                        <td class="text-center">{{number_format($si_detail->tbl_purc_grn_dtl_quantity,2)}}</td>
                                        <td class="text-right">{{number_format($si_detail->current_stock,2)}}</td>
                                    </tr>
                                @php
                                    $ki += 1;
                                @endphp
                            @endforeach
                                <tr>
                                    <td colspan="12" class="text-right"><strong style="color:#5578eb">Total: </strong></td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($totqty,0)}}
                                        </strong>
                                    </td>
                                    <td class="text-right"></td>
                                </tr>
                            </table>
                </div>
            </div>
        </div>
        @include('reports.template.footer')
    </div>
@endsection
@section('pageJS')

@endsection

@section('customJS')

@endsection
{{-- @section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_fbr_sales_data_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
