@extends('layouts.report')
@section('title', 'Product Group Activity Report')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        body{
            width: 2000px;
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
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->select('group_item_name_string','group_item_id','group_item_name_code_string')->get(); @endphp
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
            @if(isset($product_groups))
                @foreach($product_groups as $product_group)
                    <div class="row row-block">
                        <div class="col-lg-12">
                            <h6 class="kt-invoice__criteria">
                                <span style="color: #e27d00;">Product Group:</span>
                                <span style="color: #5578eb;">{{$product_group->group_item_name_string}}</span>
                            </h6>
                            @php
                           // dd($product_group);
                                $product_group_id = "gi.group_item_name_code_string like '".$product_group->group_item_name_code_string."%'";
                                $business_id = "business_id = ".auth()->user()->business_id;
                                $company_id = "company_id = ".auth()->user()->company_id;
                                $date_between = "between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd')";
                                $q_st = "SELECT  DISTINCT  vis.STOCK_BRANCH_TO_ID ,  vis.STOCK_BRANCH_TO_NAME   FROM VW_INVE_STOCK vis, VW_PURC_GROUP_ITEM gi WHERE vis.GROUP_ITEM_ID = gi.GROUP_ITEM_ID and upper(vis.STOCK_CODE_TYPE) =  'ST'
                                        AND (vis.STOCK_DATE $date_between)
                                        AND vis.$business_id and vis.$company_id  and vis.STOCK_BRANCH_FROM_ID in (".implode(",",$data['branch_ids']).") AND $product_group_id";
                              //  dd($q_st);
                                $st = \Illuminate\Support\Facades\DB::select($q_st);

                                $d_q = "select CALENDAR_DATE from TBL_SOFT_CALENDAR where CALENDAR_DATE $date_between";
                                $dates = \Illuminate\Support\Facades\DB::select($d_q);

                                $gt_cr_purc = 0;
                                $gt_ca_purc = 0;
                                $gt_total_purc = 0;
                                $gt_si = 0;
                                $gt_pos = 0;
                                $gt_sr = 0;
                                $gt_total_sale = 0;
                                $gt_da = 0;
                                $gt_total_st = 0;
                                $grand_total_amount = 0;
                                $arr = [];
                                foreach($st as $k=>$var){
                                   $a_{$k} = 0;
                                }
                            @endphp
                            <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                                <tr class="sticky-header">
                                    <th class="text-center">Date</th>
                                    <th class="text-center" colspan="3">Purchases</th>
                                    <th class="text-center" colspan="4">Sales</th>
                                    <th class="text-center" colspan="{{count($st)+1}}"></th>
                                    <th class="text-center" colspan="2">Damage</th>
                                    <th class="text-center">Total Value</th>
                                </tr>
                                <tr>
                                    <th class="text-center" width="130px"></th>
                                    <th class="text-center" width="125px">CR Purchase</th>
                                    <th class="text-center" width="150px">Cash Purchase</th>
                                    <th class="text-center" width="125px">Total</th>
                                    <th class="text-center" width="125px">SI</th>
                                    <th class="text-center" width="125px">POS</th>
                                    <th class="text-center" width="125px">SR</th>
                                    <th class="text-center" width="125px">Total</th>
                                    @foreach($st as $st_item)
                                        <th class="text-center" width="125px">{{$st_item->stock_branch_to_name}}</th>
                                    @endforeach
                                    <th class="text-center" width="125px">Total</th>
                                    <th class="text-center" width="125px">Damage</th>
                                    <th class="text-center" width="125px">Total</th>
                                    <th class="text-center" width="125px"></th>
                                </tr>
                                @foreach($dates as $date)
                                    @php
                                        $date = date('Y-m-d', strtotime($date->calendar_date));
                                        $date_between = "between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')";
                                        $cp_qry = "select sum(vpg.TBL_PURC_GRN_DTL_TOTAL_AMOUNT) AMOUNT FROM VW_PURC_GRN vpg, vw_purc_group_item gi  WHERE vpg.group_item_id = gi.group_item_id and (vpg.GRN_TYPE = 'GRN' )
                                                AND (vpg.GRN_DATE $date_between) AND vpg.$business_id and vpg.$company_id
                                                and vpg.BRANCH_ID in (".implode(",",$data['branch_ids']).") AND vpg.PAYMENT_TYPE_NAME = 'Cash' AND $product_group_id";
                                        $c_purchase = \Illuminate\Support\Facades\DB::selectOne($cp_qry);
                                        $crp_qry = "select sum(vpg.TBL_PURC_GRN_DTL_TOTAL_AMOUNT) AMOUNT FROM VW_PURC_GRN vpg, vw_purc_group_item gi WHERE vpg.group_item_id = gi.group_item_id and (vpg.GRN_TYPE = 'GRN' )
                                                AND (vpg.GRN_DATE $date_between) AND vpg.$business_id and vpg.$company_id
                                                and vpg.BRANCH_ID in (".implode(",",$data['branch_ids']).") AND vpg.PAYMENT_TYPE_NAME = 'Credit' AND $product_group_id";
                                        $cr_purchase = \Illuminate\Support\Facades\DB::selectOne($crp_qry);

                                        $si_qry = "select sum(vssi.sales_dtl_total_amount) amount from vw_sale_sales_invoice vssi, vw_purc_group_item gi where vssi.group_item_id = gi.group_item_id and  ( vssi.sales_type = 'SI')
                                                AND (vssi.SALES_DATE $date_between) AND vssi.$business_id and vssi.$company_id
                                                and vssi.BRANCH_ID in (".implode(",",$data['branch_ids']).") AND $product_group_id";
                                        $si = \Illuminate\Support\Facades\DB::selectOne($si_qry);
                                        $pos_qry = "select sum(vssi.sales_dtl_total_amount) amount from vw_sale_sales_invoice vssi, vw_purc_group_item gi where vssi.group_item_id = gi.group_item_id and  ( vssi.sales_type = 'POS')
                                                AND (vssi.SALES_DATE $date_between) AND vssi.$business_id and vssi.$company_id
                                                and vssi.BRANCH_ID in (".implode(",",$data['branch_ids']).") AND $product_group_id";
                                        $pos = \Illuminate\Support\Facades\DB::selectOne($pos_qry);
                                        $sr_qry = "select sum(vssi.sales_dtl_total_amount) amount from vw_sale_sales_invoice vssi, vw_purc_group_item gi where vssi.group_item_id = gi.group_item_id and  ( vssi.sales_type = 'SR' or vssi.sales_type = 'RPOS')
                                                AND (vssi.SALES_DATE $date_between) AND vssi.$business_id and vssi.$company_id
                                                and vssi.BRANCH_ID in (".implode(",",$data['branch_ids']).") AND $product_group_id";
                                        $sr = \Illuminate\Support\Facades\DB::selectOne($sr_qry);
                                        $da_qry = "select sum(vis.stock_dtl_amount)  amount from vw_inve_stock vis, vw_purc_group_item gi  where vis.group_item_id = gi.group_item_id and  upper(vis.stock_code_type) =  'DI'
                                                AND (vis.STOCK_DATE $date_between) AND vis.$business_id and vis.$company_id
                                                and vis.BRANCH_ID in (".implode(",",$data['branch_ids']).") AND $product_group_id";
                                        $da = \Illuminate\Support\Facades\DB::selectOne($si_qry);
                                        $tstd_amount = 0;

                                        $gt_cr_purc += (float)$cr_purchase->amount;
                                        $gt_ca_purc += (float)$c_purchase->amount;
                                        $gt_total_purc += ((float)$c_purchase->amount + (float)$cr_purchase->amount);

                                        $gt_si += (float)$si->amount;
                                        $gt_pos += (float)$pos->amount;
                                        $gt_sr += (float)$sr->amount;
                                        $gt_total_sale += (((float)$si->amount + (float)$pos->amount) - (float)$sr->amount);
                                        $gt_da += (float)$da->amount;

                                        $grand_total_amount += ((float)$cr_purchase->amount + (float)$c_purchase->amount) - ((((float)$si->amount + (float)$pos->amount) - (float)$sr->amount) + (float)$tstd_amount + (float)$da->amount );
                                    @endphp
                                    <tr>
                                        <td class="text-left">{{$date}}</td>
                                        <td class="text-right">{{$cr_purchase->amount}}</td>
                                        <td class="text-right">{{$c_purchase->amount}}</td>
                                        <td class="text-right">{{(float)$cr_purchase->amount + (float)$c_purchase->amount}}</td>
                                        <td class="text-right">{{$si->amount}}</td>
                                        <td class="text-right">{{$pos->amount}}</td>
                                        <td class="text-right">{{$sr->amount}}</td>
                                        <td class="text-right">{{((float)$si->amount + (float)$pos->amount) - (float)$sr->amount}}</td>
                                        @foreach($st as $k=>$st_item)
                                            @php
                                                $std_qry = "select sum(vis.stock_dtl_total_amount) amount from vw_inve_stock vis,vw_purc_group_item gi where vis.group_item_id = gi.group_item_id and upper(stock_code_type) =  'ST'
                                                and (vis.stock_date $date_between) AND vis.$business_id and vis.$company_id
                                                and vis.stock_branch_from_id in (".implode(",",$data['branch_ids']).") and vis.stock_branch_to_id  = $st_item->stock_branch_to_id  AND $product_group_id";
                                                $std = \Illuminate\Support\Facades\DB::selectOne($std_qry);
                                                if($std->amount == null){
                                                    $amt = 0;
                                                    $a_{$k} += $amt;
                                                    $arr[$k] = $a_{$k};
                                                }else{
                                                    $amt = (float)$std->amount;
                                                    $a_{$k} += $amt;
                                                    $arr[$k] = $a_{$k};
                                                    $tstd_amount += (float)$std->amount;
                                                }
                                            @endphp
                                            <td class="text-right">{{$amt}}</td>
                                        @endforeach
                                        <td class="text-right">{{$tstd_amount}}</td>
                                        <td class="text-right">{{$da->amount}}</td>
                                        <td class="text-right">{{$da->amount}}</td>
                                        <td class="text-right">{{((float)$cr_purchase->amount + (float)$c_purchase->amount) - ((((float)$si->amount + (float)$pos->amount) - (float)$sr->amount) + (float)$tstd_amount + (float)$da->amount )  }}</td>
                                    </tr>

                                    @php
                                        $gt_total_st += $tstd_amount;
                                    @endphp
                                @endforeach
                                <tr class="grand_total">
                                    <td class="rep-font-bold">Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($gt_cr_purc,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($gt_ca_purc,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($gt_total_purc,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($gt_si,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($gt_pos,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($gt_sr,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($gt_total_sale,3)}}</td>
                                    @foreach($arr as $item)
                                        <td class="text-right rep-font-bold">{{number_format($item,3)}}</td>
                                    @endforeach
                                    <td class="text-right rep-font-bold">{{number_format($gt_total_st,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($gt_da,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($gt_da,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($grand_total_amount,3)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif
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



