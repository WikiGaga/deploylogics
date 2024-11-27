@extends('layouts.report')
@section('title', 'Inventory Look Up Report')

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
       // dd($data);
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date']))." "}}</span>
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
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->get('group_item_name_string'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Group:</span>
                        @foreach($product_groups as $product_group)
                            <span style="color: #5578eb;">{{$product_group->group_item_name_string}}</span><span style="color: #fd397a;">, </span>
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
                    $whereSup = "";
                    if(count($data['product_ids']) != 0){
                        $where .= " and PROD.product_name in ('".implode("','",$data['product_ids'])."') ";
                    }
                    if(count($data['supplier_ids']) != 0){
                        $whereSup .= " and SUP_PROD.supplier_id in (".implode(",",$data['supplier_ids']).")";
                    }
                    if(count($data['product_group']) != 0){
                        $inner_where = "";
                        foreach($data['product_group'] as $product_group){
                            $group_item_item = \App\Models\TblPurcGroupItem::where('group_item_id',$product_group)->first();
                            if($group_item_item->group_item_level == 1){

                            }
                            if($group_item_item->group_item_level == 2){
                                $group_items = \App\Models\TblPurcGroupItem::where('parent_group_id',$product_group)->pluck('group_item_id')->toArray();

                                $inner_where .= " PROD.group_item_id in (".implode(",",$group_items).") OR";
                            }
                            if($group_item_item->group_item_level == 3){
                                $inner_where .= " PROD.group_item_id = $product_group OR ";
                            }
                        }
                        if(!empty($inner_where)){
                            $inner_where = rtrim($inner_where, " OR ");
                            $where .= "and ( ".$inner_where." ) ";
                        }
                    }

                    // Vendor Wise whereclause
                    $vendorfrom = "";
                    $vendorjoin = "";
                    if(isset($data['supplier_ids']) && !empty($data['supplier_ids']) != 0)
                    {
                        $vendorfrom = " ,VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD";
                        $vendorjoin = " AND STOCK.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
                    }
                    //End Vendor Wise whereclause
if($data['consolidate'] == 1)
{
    $qry = "SELECT 
    PROD_BARCODE.PRODUCT_BARCODE_BARCODE,
    PR.BRANCH_ID,
    PR.BRANCH_NAME,
    PR.BRAND_NAME,
    PR.product_id,
    PR.product_code,
    PR.product_name,
    PR.product_arabic_name,
    PR.PRODUCT_QTY,
    SALE_RATE 
FROM(
    SELECT 
        PROD.product_id,
        PROD.product_code,
        PROD.BRANCH_ID,
        PROD.BRANCH_NAME,
        PROD.BRAND_NAME,
        product_name,
        product_arabic_name,
        PRODUCT_QTY 
    FROM (
        SELECT 
            PRODUCT_ID, 
            -- SUM(QTY_BASE_UNIT_VALUE) PRODUCT_QTY ,
            (SUM (NVL (QTY_IN, 0)) - SUM (NVL (QTY_OUT, 0)) ) PRODUCT_QTY 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE branch_id in (".implode(",",$data['branch_ids']).")
            AND ".$data['clause_business_id'] . $data['clause_company_id'] . "  
            AND document_date <= to_date('".$data['date']."', 'yyyy/mm/dd')
        GROUP BY PRODUCT_ID
    ) STOCK, VW_PURC_PRODUCT PROD 
    $vendorfrom 
    WHERE branch_id in (".implode(",",$data['branch_ids']).")
        AND STOCK.PRODUCT_ID = PROD.PRODUCT_ID 
        $vendorjoin 
        $whereSup 
        $where
    ) PR 
    LEFT OUTER JOIN (
        SELECT DISTINCT 
            PRODUCT_ID,
            MAX(SALE_RATE) SALE_RATE 
        FROM
            TBL_PURC_PRODUCT_BARCODE_PURCH_RATE PROD_RATE 
        WHERE branch_id in (".implode(",",$data['branch_ids']).") 
        GROUP BY PRODUCT_ID
    ) PROD_RATE 
    ON PR.PRODUCT_ID = PROD_RATE.PRODUCT_ID 
    LEFT OUTER JOIN (
        SELECT 
            MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
            PRODUCT_ID 
        FROM
            tbl_purc_product_barcode 
        WHERE BASE_BARCODE = 1 
        GROUP BY PRODUCT_ID
    ) PROD_BARCODE 
    ON PR.PRODUCT_ID = PROD_BARCODE.PRODUCT_ID 
    ORDER BY PR.product_name";

    $list = \Illuminate\Support\Facades\DB::select($qry);
}
else
{
    $qry = "SELECT 
    PROD_BARCODE.PRODUCT_BARCODE_BARCODE,
    PR.BRANCH_ID,
    PR.BRANCH_NAME,
    PR.BRAND_NAME,
    PR.product_id,
    PR.product_code,
    PR.product_name,
    PR.product_arabic_name,
    PR.PRODUCT_QTY,
    SALE_RATE 
FROM(
    SELECT 
        PROD.product_id,
        PROD.product_code,
        STOCK.BRANCH_ID,
        BB.BRANCH_NAME,
        PROD.BRAND_NAME,
        product_name,
        product_arabic_name,
        PRODUCT_QTY 
    FROM (
        SELECT 
            PRODUCT_ID, 
            BRANCH_ID,
            -- SUM(QTY_BASE_UNIT_VALUE) PRODUCT_QTY ,
            (SUM (NVL (QTY_IN, 0)) - SUM (NVL (QTY_OUT, 0)) ) PRODUCT_QTY 
        FROM
            VW_PURC_STOCK_DTL 
        WHERE branch_id in (".implode(",",$data['branch_ids']).")
            AND ".$data['clause_business_id'] . $data['clause_company_id'] . "  
            AND document_date <= to_date('".$data['date']."', 'yyyy/mm/dd')
        GROUP BY PRODUCT_ID, BRANCH_ID
    ) STOCK, VW_PURC_PRODUCT PROD , tbl_soft_branch BB
    $vendorfrom 
    WHERE STOCK.BRANCH_ID in (".implode(",",$data['branch_ids']).")
        AND STOCK.PRODUCT_ID = PROD.PRODUCT_ID 
        AND STOCK.BRANCH_ID = BB.BRANCH_ID 
        $vendorjoin 
        $whereSup 
        $where
    ) PR 
    LEFT OUTER JOIN (
        SELECT DISTINCT 
            PRODUCT_ID,
            MAX(SALE_RATE) SALE_RATE 
        FROM
            TBL_PURC_PRODUCT_BARCODE_PURCH_RATE PROD_RATE 
        WHERE branch_id in (".implode(",",$data['branch_ids']).") 
        GROUP BY PRODUCT_ID
    ) PROD_RATE 
    ON PR.PRODUCT_ID = PROD_RATE.PRODUCT_ID 
    LEFT OUTER JOIN (
        SELECT 
            MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
            PRODUCT_ID 
        FROM
            tbl_purc_product_barcode 
        WHERE BASE_BARCODE = 1 
        GROUP BY PRODUCT_ID
    ) PROD_BARCODE 
    ON PR.PRODUCT_ID = PROD_BARCODE.PRODUCT_ID 
    ORDER BY PR.product_name";
//dd($qry);
    $getdata = \Illuminate\Support\Facades\DB::select($qry);
    $list = [];
    foreach ($getdata as $row)
    {
        $list[$row->branch_name][] = $row;
    }
}
        @endphp
        @if($data['consolidate'] == 1)
            <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                <tr class="sticky-header">
                    <th width="5%" class="text-center">Sr. No</th>
                    <th width="15%" class="text-left">Brand Name</th>
                    <th width="15%" class="text-left">BarCode</th>
                    <th width="45%" class="text-left">Product Name</th>
                    <th width="10%" class="text-right">Sale Rate</th>
                    <th width="10%" class="text-right">Inventory</th>
                </tr>
                @php
                    $total_Stock =  0;
                @endphp
                @foreach($list as $key=>$list_row)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{ $list_row->brand_name }}</td>
                        <td>{{ $list_row->product_barcode_barcode }}</td>
                        <td>{{ $list_row->product_name }}</td>
                        <td class="text-right">
                            {{ number_format($list_row->sale_rate,3) }}
                        </td>
                        <td class="text-right">
                            @php
                                $total_Stock += (int)$list_row->product_qty;
                            @endphp
                            {{ $list_row->product_qty }}
                        </td>
                    </tr>
                @endforeach
                <tr class="grand_total">
                    <td class="rep-font-bold text-right" colspan="5"><b> Total: </b></td>
                    <td class="text-right rep-font-bold">{{number_format($total_Stock,3)}}</td>
                </tr>
            </table>
        @else
            <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                <tr class="sticky-header">
                    <th width="5%" class="text-center">Sr. No</th>
                    <th width="15%" class="text-left">Brand Name</th>
                    <th width="15%" class="text-left">BarCode</th>
                    <th width="45%" class="text-left">Product Name</th>
                    <th width="10%" class="text-right">Sale Rate</th>
                    <th width="10%" class="text-right">Inventory</th>
                </tr>
                @php
                    $gtotal_Stock =  0;
                @endphp
                @foreach($list as $name=>$items)
                    <tr style="background-color:aliceblue">
                        <td colspan="7">{{$name}}</td>
                    </tr>
                    @php
                        $total_Stock =  0;
                    @endphp
                    @foreach($items as $key=>$list_row)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{ $list_row->brand_name }}</td>
                            <td>{{ $list_row->product_barcode_barcode }}</td>
                            <td>{{ $list_row->product_name }}</td>
                            <td class="text-right">
                                {{ number_format($list_row->sale_rate,3) }}
                            </td>
                            <td class="text-right">
                                @php
                                    $total_Stock += (int)$list_row->product_qty;
                                    $gtotal_Stock += (int)$list_row->product_qty;
                                @endphp
                                {{ $list_row->product_qty }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="grand_total">
                        <td class="rep-font-bold text-right" colspan="5"><b> Total: </b></td>
                        <td class="text-right rep-font-bold">{{number_format($total_Stock,3)}}</td>
                    </tr>
                @endforeach
                    <tr class="grand_total">
                        <td class="rep-font-bold text-right" colspan="5"><b> Grand Total: </b></td>
                        <td class="text-right rep-font-bold">{{number_format($gtotal_Stock,3)}}</td>
                    </tr>
            </table>
        @endif
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
