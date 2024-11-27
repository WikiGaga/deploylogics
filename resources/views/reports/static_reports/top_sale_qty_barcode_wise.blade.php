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
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->where(\App\Library\Utilities::currentBC())->where('branch_active_status',1)->get(['branch_name','branch_short_name']); @endphp
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
                @if(!App\Helpers\Helper::valEmpty($data['filter_qty']) && !App\Helpers\Helper::valEmpty($data['filter_qty_val']))
                   <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Qty:</span>
                        <span style="color: #5578eb;">{{" ".$data['filter_qty']." ".$data['filter_qty_val']}}</span><span style="color: #fd397a;">, </span>
                    </h6>
                @endif
                @if(!App\Helpers\Helper::valEmpty($data['filter_amount']) && !App\Helpers\Helper::valEmpty($data['filter_amount_val']))
                   <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Amount:</span>
                        <span style="color: #5578eb;">{{" ".$data['filter_amount']." ".$data['filter_amount_val']}}</span><span style="color: #fd397a;">, </span>
                    </h6>
                @endif
                @if(!App\Helpers\Helper::valEmpty($data['orderby']))
                   <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Order By:</span>
                        <span style="color: #5578eb;">{{" ".$data['orderby']}}</span><span style="color: #fd397a;">, </span>
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $orderby = "KAKA.PRODUCT_QTY DESC ,  KAKA.PRODUCT_ID, BARCODE_QTY DESC";
                        if($data['orderby'] == "amount"){
                            $orderby = "KAKA.amount DESC ,  KAKA.PRODUCT_ID, BARCODE.amount DESC";
                        }
                        $filter_qty = "";
                        if($data['filter_qty'] != "" && $data['filter_qty_val'] != ""){
                            $filter_qty = " AND KAKA.PRODUCT_QTY ". $data['filter_qty'] ." ". $data['filter_qty_val']." ";
                        }
                        $filter_amount = "";
                        if($data['filter_amount'] != "" && $data['filter_amount_val'] != ""){
                            $filter_amount= " AND KAKA.amount ". $data['filter_amount'] ." ". $data['filter_amount_val']." ";
                        }
                        $filter_group_item = "";
                        if(isset($product_groups) && count($product_groups) != 0){
                            // AND (group_item_name_code_string like '1-10-85%' OR group_item_name_code_string like '1-21-98%')
                            $filter_group_item = " AND ( ";
                            $arr_count = count($product_groups) - 1;
                            foreach ($product_groups as $k=>$product_group){
                                $filter_group_item .= "group_item_name_code_string like '".$product_group->group_item_name_code_string."%'";
                                if($arr_count != $k){
                                  $filter_group_item .= " OR ";
                                }
                            }
                            $filter_group_item .= " ) ";
                           // $filter_group_item = " AND PRODUCT.group_item_id IN (".implode(",",$data['product_group']).") ";
                        }
                        $query = "SELECT
                            PRODUCT.PRODUCT_NAME , 
                            PRODUCT.UOM_NAME , 
                            PRODUCT_BARCODE_PACKING ,
                            PRODUCT_BARCODE_BARCODE ,
                            group_item_name_code_string, 
                            GROUP_ITEM_NAME_STRING ,
                            GROUP_ITEM_LEVEL ,
                            BARCODE.BRANCH_ID ,
                             KAKA.PRODUCT_QTY TOT_PRODUCT_QTY ,  
                             KAKA.AMOUNT TOT_PRODUCT_AMOUNT ,  
                             BARCODE.PRODUCT_ID , 
                             BARCODE.PRODUCT_BARCODE_ID  ,    
                             BARCODE.BARCODE_QTY ,  
                             BARCODE.PRODUCT_QTY , 
                             BARCODE.AMOUNT
                        FROM
                        (
                            select 
                                BRANCH_ID ,  
                                PRODUCT_ID , 
                                PRODUCT_BARCODE_ID  ,  
                                 sum(SALES_DTL_QUANTITY) BARCODE_QTY ,  
                                 sum(QTY_BASE_UNIT) PRODUCT_QTY , 
                                 SUM(SALES_DTL_TOTAL_AMOUNT) AMOUNT 
                            FROM 
                                VW_SALE_SALES_INVOICE 
                            WHERE UPPER(SALES_TYPE) IN ('SI','POS')
                                AND BRANCH_ID  IN (".implode(",",$data['branch_ids']).") 
                                AND (TRUNC(SALES_DATE) between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd'))  GROUP BY    PRODUCT_ID , PRODUCT_BARCODE_ID , BRANCH_ID   ORDER BY PRODUCT_ID , PRODUCT_BARCODE_ID
                        ) BARCODE
                        INNER JOIN(
                            SELECT  PRODUCT_ID , PRODUCT_QTY ,  AMOUNT  FROM
                            (
                                SELECT   PRODUCT_ID , sum(PRODUCT_QTY) PRODUCT_QTY ,   sum(AMOUNT) AMOUNT   FROM
                                (
                                    select BRANCH_ID ,  PRODUCT_ID , PRODUCT_BARCODE_ID  ,   sum(SALES_DTL_QUANTITY) BARCODE_QTY ,  sum(QTY_BASE_UNIT) PRODUCT_QTY , SUM(SALES_DTL_TOTAL_AMOUNT) AMOUNT FROM VW_SALE_SALES_INVOICE   WHERE  UPPER(SALES_TYPE) IN ('SI','POS')
                                    AND  BRANCH_ID  IN (".implode(",",$data['branch_ids']).")  AND
                                    (TRUNC(SALES_DATE) between to_date('".$data['from_date']."','yyyy/mm/dd') and to_date('".$data['to_date']."','yyyy/mm/dd'))
                                    GROUP BY    PRODUCT_ID , PRODUCT_BARCODE_ID , BRANCH_ID   ORDER BY PRODUCT_ID , PRODUCT_BARCODE_ID
                                ) ABC  
                                GROUP BY PRODUCT_ID
                            ) XYZ  
                            ORDER BY PRODUCT_QTY DESC
                        )KAKA ON (KAKA.PRODUCT_ID = BARCODE.PRODUCT_ID) ,
                        VW_PURC_PRODUCT_BARCODE PRODUCT , VW_PURC_GROUP_ITEM  GRP_PRODUCT
                    WHERE PRODUCT.PRODUCT_ID = BARCODE.PRODUCT_ID 
                        AND  PRODUCT.PRODUCT_BARCODE_ID =  BARCODE.PRODUCT_BARCODE_ID
                        AND  PRODUCT.GROUP_ITEM_ID = GRP_PRODUCT.GROUP_ITEM_ID
                        $filter_qty
                        $filter_amount
                        $filter_group_item
                    ORDER BY $orderby";
                        
                      //  dd($query);
                       
                        $get_data = DB::select($query);
                        $data_list = [];
                    @endphp
                    @foreach ($get_data as $item)
                        @php
                            $group_item = $item->group_item_name_string;
                            $product_name = $item->product_name;
                            $barcode = $item->product_barcode_barcode;
                            $data_list[$product_name]['barcode'][$barcode]['list'][] = $item;
                            $data_list[$product_name]['barcode'][$barcode]['dtl']['barcode'] = $item->product_barcode_barcode;
                            $data_list[$product_name]['barcode'][$barcode]['dtl']['uom'] = $item->uom_name;
                            $data_list[$product_name]['barcode'][$barcode]['dtl']['packing'] = $item->product_barcode_packing;
                             // $data_list[$product_name]['branch'][$item->branch_id][] = $item;

                            $data_list[$product_name]['branch'][$item->branch_id][] = $item->product_qty;

                            $data_list[$product_name]['tot_product_qty'] = $item->tot_product_qty;
                            $data_list[$product_name]['tot_product_amount'] = $item->tot_product_amount;
                        @endphp
                    @endforeach
                    @php
                        $overall_qty_total = 0;
                        $overall_amount_total = 0;
                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center" width="5%">Sr</th>
                            <th class="text-left" width="30%">Product Name</th>
                            @foreach($branch_lists as $branch_list)
                                <th class="text-center" width="15%" >{{$branch_list->branch_short_name}}</th>
                            @endforeach
                            <th class="text-center" width="10%">Total Qty</th>
                            <th class="text-center" width="10%">Sale Amount</th>
                        </tr>
                        <tbody>
                            @foreach($data_list as $product_name=>$rows)
                            <tr>
                                <td class="text-center"><b>{{$loop->iteration}}</b></td>
                                <td><b>{{$product_name}}</b></td>
                                @foreach($data['branch_ids'] as $branch_id)
                                    @php $branch_id_fond = false; @endphp
                                    @foreach($rows['branch'] as $branch_id_row=>$branch_row)
                                        @if($branch_id == $branch_id_row)
                                            <td class="text-right"><b>{{number_format(array_sum($branch_row),3,".","")}}</b></td>
                                            @php $branch_id_fond = true;  @endphp
                                        @endif
                                    @endforeach
                                    @if($branch_id_fond == false)
                                        <td class="text-right"></td>
                                    @endif
                                @endforeach
                                <td class="text-right"><b>{{isset($rows['tot_product_qty'])?number_format($rows['tot_product_qty'],3,".",""):""}}</b></td>
                                <td class="text-right"><b>{{isset($rows['tot_product_amount'])?number_format($rows['tot_product_amount'],3,".",""):""}}</b></td>
                                @php
                                    $overall_qty_total += (float)$rows['tot_product_qty'];
                                    $overall_amount_total += (float)$rows['tot_product_amount'];
                                @endphp
                            </tr>
                            @foreach($rows['barcode'] as $barcode=>$row)
                                <tr>
                                    <td></td>
                                    <td>{{$row['dtl']['barcode']}} - {{$row['dtl']['uom']}} ({{$row['dtl']['packing']}})</td>
                                    @php $barcode_qty = 0; $sale_amount = 0; @endphp
                                    @foreach($data['branch_ids'] as $branch_id)
                                        @php $branch_fond = false; @endphp
                                        @foreach($row['list'] as $barcode_row)
                                            @if($branch_id ==  $barcode_row->branch_id)
                                                <td class="text-right" >{{number_format($barcode_row->barcode_qty,3,".","")}}</td>
                                                @php
                                                    $branch_fond = true;
                                                    $barcode_qty += $barcode_row->barcode_qty;
                                                    $sale_amount += $barcode_row->amount;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @if($branch_fond == false)
                                            <td class="text-right"></td>
                                        @endif
                                    @endforeach
                                    <td class="text-right">{{number_format($barcode_qty,3,".","")}}</td>
                                    <td class="text-right">{{number_format($sale_amount,3,".","")}}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                        <tr class="grand_total">
                            <td class="rep-font-bold"></td>
                            <td colspan="{{count($branch_lists)+1}}" class="rep-font-bold text-left">Total</td>
                            <td class="text-right rep-font-bold">{{number_format($overall_qty_total,3,".","")}}</td>
                            <td class="text-right rep-font-bold">{{number_format($overall_amount_total,3,".","")}}</td>
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



