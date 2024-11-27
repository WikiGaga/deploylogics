@extends('layouts.report')
@section('title', 'Branch Wise Stock')

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
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{date('d-m-Y', strtotime($data['date']))}}</span>
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
                @endif
                @if(count($data['product_ids']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @foreach($data['product_ids'] as $product)
                            <span style="color: #5578eb;">{{$product}}</span><span style="color: #fd397a;">, </span>
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
                    if(isset($data['supplier_ids']) && count($data['supplier_ids']) != 0){
                        $where .= " AND SUP_PROD.supplier_id IN (".implode(",",$data['supplier_ids']).") ";
                    }
                    if(count($data['product_ids']) != 0){
                        $where .= " AND PROD.product_name IN ('".implode("','",$data['product_ids'])."') ";
                    }

                    $vendorfrom = "";
                    $vendorjoin = "";
                    // Vendor Wise whereclause
                    if(isset($data['supplier_ids']) && count($data['supplier_ids']) != 0){
                        $vendorfrom = " ,VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD";
                        $vendorjoin = " AND STOCK.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
                        $vendor = ", SUP_PROD.supplier_id";
                    }else{
                        $vendor = ", PROD.supplier_id";
                    }


                    $qry = "SELECT 
                        * 
                    FROM
                    (SELECT 
                        GROUP_ITEM_ID,
                        GROUP_ITEM_NAME,
                        GROUP_ITEM_PARENT_ID,
                        GROUP_ITEM_PARENT_NAME,
                        product_barcode_barcode,
                        product_name,
                        PRODUCT_QTY,
                        BRANCH_ID,
                        PROD.PRODUCT_ID 
                        -- , SUPPLIER_ID                       
                    FROM
                    (SELECT 
                        GROUP_ITEM_ID,
                        GROUP_ITEM_NAME,
                        GROUP_ITEM_PARENT_ID,
                        GROUP_ITEM_PARENT_NAME,
                        PROD.PRODUCT_ID,
                        product_name,
                        PRODUCT_QTY,
                        STOCK.BRANCH_ID 
                        $vendor
                    FROM
                    (SELECT 
                        PRODUCT_ID,
                        BUSINESS_ID,
                        COMPANY_ID,
                        BRANCH_ID,
                        (SUM(NVL (QTY_IN, 0)) - SUM(NVL (QTY_OUT, 0))) PRODUCT_QTY,
                        SUM(
                        QTY_BASE_UNIT_VALUE * DOCUMENT_ACT_RATE
                        ) PRODUCT_VAL 
                    FROM
                        VW_PURC_STOCK_DTL 
                    WHERE business_id = ".auth()->user()->business_id." 
                        AND company_id = ".auth()->user()->company_id." 
                        AND branch_id in (".implode(",",$data['branch_ids']).")
                        AND VW_PURC_STOCK_DTL.DOCUMENT_DATE <= to_date('".$data['date']."','yyyy/mm/dd')
                    GROUP BY PRODUCT_ID,
                        BUSINESS_ID,
                        COMPANY_ID,
                        BRANCH_ID 
                    HAVING SUM(QTY_BASE_UNIT_VALUE) <> 0) STOCK,
                    VW_PURC_PRODUCT PROD 
                        $vendorfrom 
                    WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID 
                        $vendorjoin
                        $where
                    ) PROD
                    LEFT OUTER JOIN 
                    (SELECT 
                        MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
                        PRODUCT_ID 
                    FROM
                        tbl_purc_product_barcode 
                    WHERE BASE_BARCODE = 1 
                    GROUP BY PRODUCT_ID) PROD_BARCODE 
                    ON PROD.PRODUCT_ID = PROD_BARCODE.PRODUCT_ID) 
                PIVOT (SUM(PRODUCT_QTY) FOR BRANCH_ID IN (".implode(",",$data['branch_ids'])."))";
                          
   //dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        $list = [];
                        foreach ($getdata as $row)
                        {
                            $list[$row->group_item_parent_name][$row->group_item_name][] = $row;
                        }

                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">Sr No.</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Product Name</th>
                            @if(count($data['branch_ids']) != 0)
                                @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                                @foreach($branch_lists as $branch_list)
                                    <th class="text-center">{{$branch_list->branch_name}}</th>
                                @endforeach
                            @endif
                            <th class="text-center">Total</th>
                        </tr>
                        @foreach($list as $fk=>$f_row)
                            <tr class="first_group_title">
                                <td colspan="{{count($data['branch_ids'])+4}}">{{ucwords(strtolower($fk))}}</td>
                            </tr>
                            @foreach($f_row as $sk=>$sec_row)
                                <tr class="second_group_title">
                                    <td></td>
                                    <td colspan="{{count($data['branch_ids'])+3}}">{{ucwords(strtolower($sk))}}</td>
                                </tr>
                                @php
                                    $ki = 1;
                                @endphp
                                @foreach($sec_row as $item)
                                    <tr>
                                        <td>{{$ki}}</td>
                                        <td>{{$item->product_barcode_barcode}}</td>
                                        <td>{{$item->product_name}}</td>
                                        @php $total_stock = 0; @endphp
                                        @foreach($data['branch_ids'] as $branch_ids)
                                        <td class="text-right">{{ $item->{$branch_ids} }}</td>
                                            @php $total_stock += $item->{$branch_ids}; @endphp
                                        @endforeach
                                        <td class="text-right">{{$total_stock}}</td>
                                    </tr>
                                    @php
                                        $ki += 1;
                                    @endphp
                                @endforeach
                            @endforeach
                        @endforeach
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



