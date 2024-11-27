@extends('layouts.report')
@section('title', 'Final Price Update Report')

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

                        $qry = "SELECT DISTINCT
                            BRC.BRANCH_ID,
                            BRC.BRANCH_NAME,
                            max(PROD.PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
                            PROD.PRODUCT_NAME,
                            USERS.ID as GRN_USER_ID,
                            USERS.NAME AS GRN_USER_NAME,
                            GAGA.CREATED_AT,
                            GAGA.UPDATED_AT,
                            GRN_TYPE,
                            GRN_ID,
                            GRN_CODE,
                            GRN_DATE,
                            GAGA.PRODUCT_ID,
                            TBL_PURC_GRN_DTL_NET_TP,
                            TBL_PURC_GRN_DTL_SALE_RATE 
                        FROM
                            (SELECT 
                                BRANCH_ID,
                                BRANCH_NAME,
                                GRN_USER_ID,
                                GRN_USER_NAME,
                                CREATED_AT,
                                UPDATED_AT,
                                GRN_TYPE,
                                GRN_ID,
                                GRN_CODE,
                                GRN_DATE,
                                PRODUCT_ID,
                                TBL_PURC_GRN_DTL_NET_TP,
                                TBL_PURC_GRN_DTL_SALE_RATE 
                            FROM
                                VW_PURC_GRN 
                            WHERE GRN_TYPE = 'GRN' 
                            UNION
                            ALL 
                            SELECT 
                                RATE_BR.BRANCH_ID,
                                '' BRANCH_NAME,
                                CHANGE_RATE_USER_ID,
                                '',
                                RATE.CREATED_AT,
                                RATE.UPDATED_AT,
                                'RATE CHANGE',
                                RATE.CHANGE_RATE_ID,
                                CHANGE_RATE_CODE,
                                RATE.CREATED_AT,
                                PRODUCT_ID,
                                CURRENT_TP,
                                SALE_RATE 
                            FROM
                                TBL_PURC_CHANGE_RATE RATE,
                                TBL_PURC_CHANGE_RATE_BRANCHES RATE_BR,
                                TBL_PURC_CHANGE_RATE_DTL RATE_DTL 
                            WHERE RATE.CHANGE_RATE_ID = RATE_BR.CHANGE_RATE_ID 
                            AND RATE.CHANGE_RATE_ID = RATE_DTL.CHANGE_RATE_ID 
                            UNION
                            ALL 
                            SELECT 
                                BRANCH_ID,
                                BRANCH_NAME,
                                STOCK_USER_ID,
                                '',
                                CREATED_AT,
                                UPDATED_AT,
                                'STOCK RECEIVING',
                                STOCK_ID,
                                STOCK_CODE,
                                STOCK_DATE,
                                PRODUCT_ID,
                                STOCK_DTL_PURC_RATE,
                                0 
                            FROM
                                VW_INVE_STOCK 
                            WHERE STOCK_CODE_TYPE = 'str') GAGA,
                                VW_PURC_PRODUCT_BARCODE PROD ,
                                TBL_SOFT_BRANCH BRC,
                                USERS
                            WHERE GAGA.PRODUCT_ID = PROD.PRODUCT_ID
                                AND GAGA.BRANCH_ID = BRC.BRANCH_ID
                                AND GAGA.GRN_USER_ID = USERS.ID
                                and GAGA.branch_id IN (".implode(",",$data['branch_ids']).") 
                                AND (GAGA.created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                                $where 
                            group by BRC.BRANCH_ID,
                                BRC.BRANCH_NAME,
                                PROD.PRODUCT_NAME,
                                USERS.ID ,
                                USERS.NAME ,
                                GAGA.CREATED_AT,
                                GAGA.UPDATED_AT,
                                GRN_TYPE,
                                GRN_ID,
                                GRN_CODE,
                                GRN_DATE,
                                GAGA.PRODUCT_ID,
                                TBL_PURC_GRN_DTL_NET_TP,
                                TBL_PURC_GRN_DTL_SALE_RATE 
                            ORDER BY GAGA.CREATED_AT DESC";
            //dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        //dd($getdata);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[$row->branch_name][$row->product_name][] = $row;
                        }
                        //dd($list);
                        @endphp
                        <table width="100%" id="rep_final_price_report_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th class="text-center">S.#</th>
                                <th class="text-center">Document No</th>
                                <th class="text-center">Document Type</th>
                                <th class="text-center">User</th>
                                <th class="text-center">Entry Date/Time</th>
                                <th class="text-center">Updation Date/Time</th>
                                <th class="text-center">Net TP</th>
                                <th class="text-center">Sale Rate</th>
                            </tr>
                            @foreach($list as $branch_id_keys=>$branch_row)
                                <tr style="background-color:#f8ecec;">
                                    <td colspan="8"><b>Branch Name: {{ ucwords(strtolower($branch_id_keys)) }}</b></td>
                                </tr>
                                @foreach($branch_row as $product_key=>$products)
                                    <tr style="background-color:#f8ecec;">
                                        <td colspan="8">
                                            <b>
                                                {{ ucwords(strtolower($product_key)) }}
                                            </b>
                                        </td>
                                    </tr>
                                    @php
                                        $ki=1;
                                    @endphp
                                    @foreach($products as $i_key=>$detail)
                                        @php
                                            if(date('d-m-Y h:i A', strtotime($detail->created_at)) == date('d-m-Y h:i A', strtotime($detail->updated_at)))
                                            {
                                                $updated_at = "";
                                            }else{
                                                $updated_at = date('d-m-Y h:i A', strtotime($detail->updated_at));
                                            }

                                            if($ki == 1){
                                                $bold ="style=font-weight:bold!important;";
                                            }else{
                                                $bold="";
                                            }

                                        @endphp
                                        <tr>
                                            <td class="text-center">{{$ki}}</td>
                                            <td class="text-center">{{$detail->grn_code}}</td>
                                            <td class="text-center">{{$detail->grn_type}}</td>
                                            <td class="text-center">{{$detail->grn_user_name}}</td>
                                            <td class="text-center">{{ date('d-m-Y h:i A', strtotime($detail->created_at)) }}</td>
                                            <td class="text-center">{{ $updated_at }}</td>
                                            <td {{$bold}} class="text-right">{{number_format($detail->tbl_purc_grn_dtl_net_tp,2)}}</td>
                                            <td {{$bold}} class="text-right">{{number_format($detail->tbl_purc_grn_dtl_sale_rate,2)}}</td>
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
{{-- @section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_final_price_report_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
