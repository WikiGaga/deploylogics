@extends('layouts.report')
@section('title', 'Product Price Comparison')

@section('pageCSS')
    <style>
        /* Styles go here */
        .dtl-head{ 
            font-weight:bold;
            vertical-align: middle  !important;
            text-align:center;
        }
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        .sticky-header-to {
            position: sticky;
            top: 19px;
            background-color: #f7f8fa;
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
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->get('group_item_name_string'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Group:</span>
                        @foreach($product_groups as $product_group)
                            <span style="color: #5578eb;">{{$product_group->group_item_name_string}}</span><span style="color: #fd397a;">, </span>
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
                        
                        if(count($data['product_ids']) != 0){
                            $where .= " and PROD.product_name in ('".implode("','",$data['product_ids'])."') ";
                        }
                        if(count($data['product_group']) != 0){
                            $where .= " and PROD.group_item_id in ('".implode("','",$data['product_group'])."') ";
                        }


                        $query = "SELECT 
                            XYZ.BRANCH_ID,
                            BRC.BRANCH_NAME,
                            PRODUCT_ID,
                            PRODUCT_BARCODE_BARCODE,
                            PRODUCT_NAME,
                            XYZ.CREATED_AT,
                            USER_ID,
                            US.NAME,
                            CURRENT_SALE_RATE,
                            OLD_CREATED_AT,
                            OLD_SALE_RATE 
                        FROM
                            (SELECT 
                                BRANCH_ID,
                                PRODUCT_ID,
                                PRODUCT_NAME,
                                PRODUCT_BARCODE_BARCODE,
                                CREATED_AT,
                                (SELECT 
                                MAX(GRN_USER_ID) 
                                FROM
                                VW_PURC_PRODUCT_RATE_HISTORY OLD_DATA 
                                WHERE ABC.CREATED_AT = OLD_DATA.CREATED_AT 
                                AND ABC.PRODUCT_ID = OLD_DATA.PRODUCT_ID 
                                AND ABC.BRANCH_ID = OLD_DATA.BRANCH_ID) USER_ID,
                                CURRENT_SALE_RATE,
                                OLD_CREATED_AT,
                                (SELECT 
                                MAX(TBL_PURC_GRN_DTL_SALE_RATE) 
                                FROM
                                VW_PURC_PRODUCT_RATE_HISTORY OLD_DATA 
                                WHERE ABC.OLD_CREATED_AT = OLD_DATA.CREATED_AT 
                                AND ABC.PRODUCT_ID = OLD_DATA.PRODUCT_ID 
                                AND ABC.BRANCH_ID = OLD_DATA.BRANCH_ID) OLD_SALE_RATE 
                            FROM
                                (SELECT 
                                GAGA.BRANCH_ID,
                                PROD.PRODUCT_NAME,
                                MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
                                GAGA.PRODUCT_ID,
                                GAGA.CREATED_AT,
                                GAGA.CURRENT_SALE_RATE,
                                (SELECT 
                                    MAX(CREATED_AT) CREATED_AT 
                                FROM
                                    VW_PURC_PRODUCT_RATE_HISTORY OLD_DATA 
                                WHERE (
                                    GAGA.PRODUCT_ID = OLD_DATA.PRODUCT_ID 
                                    AND GAGA.BRANCH_ID = OLD_DATA.BRANCH_ID 
                                    AND OLD_DATA.CREATED_AT < GAGA.CREATED_AT 
                                    -- AND OLD_DATA.TBL_PURC_GRN_DTL_SALE_RATE <> GAGA.CURRENT_SALE_RATE
                                    )) OLD_CREATED_AT 
                                FROM
                                (SELECT 
                                    PRODUCT_ID,
                                    BRANCH_ID,
                                    MAX(CREATED_AT) CREATED_AT,
                                    MAX(TBL_PURC_GRN_DTL_SALE_RATE) keep (
                                    dense_rank LAST 
                                ORDER BY CREATED_AT
                                ) CURRENT_SALE_RATE 
                                FROM
                                    VW_PURC_PRODUCT_RATE_HISTORY 
                                WHERE BRANCH_ID in (".implode(",",$data['branch_ids']).")
                                        AND (CREATED_AT between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))                         
                                        AND TBL_PURC_GRN_DTL_SALE_RATE <> 0 
                                GROUP BY PRODUCT_ID,
                                    BRANCH_ID) GAGA,
                                VW_PURC_PRODUCT_BARCODE PROD 
                                WHERE GAGA.PRODUCT_ID = PROD.PRODUCT_ID
                                $where
                            GROUP BY GAGA.BRANCH_ID,
                                PROD.PRODUCT_NAME,
                                GAGA.PRODUCT_ID,
                                GAGA.CREATED_AT,
                                GAGA.CURRENT_SALE_RATE) ABC
                                ) XYZ,
                                USERS US,
                                TBL_SOFT_BRANCH BRC 
                            WHERE XYZ.USER_ID = US.ID (+) 
                            AND XYZ.BRANCH_ID = BRC.BRANCH_ID
                            AND nvl(CURRENT_SALE_RATE,0) <>  nvl(OLD_SALE_RATE,0)";
//dump($query);
                        $getdata = \Illuminate\Support\Facades\DB::select($query);
                     // dd($getdata);
                        $list = [];
                        foreach ($getdata as $row)
                        {
                            $list[] = $row;
                        }
                       
                        //dd($list);
                   @endphp
                    <table width="100%" id="rep_product_price_comparison_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="6%" class="dtl-head" rowspan="2">Sr#</th>
                            <th width="15%" class="dtl-head" rowspan="2">BarCode</th>
                            <th width="31%" class="dtl-head" rowspan="2">Product Name</th>
                            <th width="24%" class="dtl-head" colspan="3">Current Sale Rate</th>
                            <th width="24%" class="dtl-head" colspan="3">Previous Sale Rate</th>
                        </tr>
                        <tr class="sticky-header-to">
                            <td width="8%" class="text-center">Price</td>
                            <td width="8%" class="text-center">Date</td>
                            <td width="8%" class="text-center">User</td>

                            <td width="8%" class="text-center">Price</td>
                            <td width="8%" class="text-center">Date</td>
                        </tr>
                        @php
                            $ki=1;
                        @endphp
                        @foreach($list as $i_key=>$detail)
                            @php
                                if(date('d-m-Y', strtotime($detail->created_at)) != "01-01-1970"){
                                    $created_at = date('d-M-Y', strtotime($detail->created_at));
                                }else{
                                    $created_at = "";
                                }
                                if(date('d-m-Y', strtotime($detail->old_created_at)) != "01-01-1970"){
                                    $old_created_at = date('d-M-Y', strtotime($detail->old_created_at));
                                }else{
                                    $old_created_at = "";
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{$ki}}</td>
                                <td class="text-left">{{$detail->product_barcode_barcode}}</td>
                                <td class="text-left">{{$detail->product_name}}</td>
                                <td class="text-center">{{number_format($detail->current_sale_rate,2)}}</td>
                                <td class="text-center">{{ $created_at }}</td>
                                <td class="text-left">{{$detail->name}}</td>
                                <td class="text-center">{{number_format($detail->old_sale_rate,2)}}</td>
                                <td class="text-center">{{ $old_created_at }}</td>
                            </tr>
                            @php
                                $ki += 1;
                            @endphp
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
                $("#rep_product_price_comparison_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



