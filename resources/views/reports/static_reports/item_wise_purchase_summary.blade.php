@extends('layouts.report')
@section('title', 'Item Wise Purchase Summary')

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

                        if(count($data['supplier_ids']) != 0){
                          $where .= " and supplier_id in (".implode(",",$data['supplier_ids']).")";
                        }

                        if(count($data['product_ids']) != 0){
                            $where .= " and product_name in ('".implode("','",$data['product_ids'])."') ";
                        }
                        if(count($data['product_group']) != 0){
                            $inner_where = "";
                            foreach($data['product_group'] as $product_group){
                                $group_item_item = \App\Models\TblPurcGroupItem::where('group_item_id',$product_group)->first();
                                if($group_item_item->group_item_level == 1){

                                }
                                if($group_item_item->group_item_level == 2){
                                    $group_items = \App\Models\TblPurcGroupItem::where('parent_group_id',$product_group)->pluck('group_item_id')->toArray();

                                    $inner_where .= " group_item_id in (".implode(",",$group_items).") OR";
                                }
                                if($group_item_item->group_item_level == 3){
                                    $inner_where .= " group_item_id = $product_group OR ";
                                }
                            }
                            if(!empty($inner_where)){
                                $inner_where = rtrim($inner_where, " OR ");
                                $where .= "and ( ".$inner_where." ) ";
                            }
                        }

                        $qry = "SELECT  PR.group_item_parent_name,
                            PR.group_item_name,
                            PR.product_barcode_barcode,
                            PR.product_id,
                            PR.product_name,
                            PR.grn_type,
                            SUM(PR.GRN_TOTAL_QTY) GRN_TOTAL_QTY,
                            SUM(PR.GRN_TOTAL_AMOUNT) GRN_TOTAL_AMOUNT,
                            SUM(PR.GRN_TOTAL_DISC_AMOUNT) GRN_TOTAL_DISC_AMOUNT,
                            SUM(PR.GRN_TOTAL_GST_AMOUNT) GRN_TOTAL_GST_AMOUNT,
                            SUM(PR.GRN_TOTAL_FED_AMOUNT) GRN_TOTAL_FED_AMOUNT,
                            SUM(
                                PR.GRN_TOTAL_SPEC_DISC_AMOUNT
                            ) GRN_TOTAL_SPEC_DISC_AMOUNT,
                            SUM(PR.GRN_OVERALL_DISC_AMOUNT) GRN_OVERALL_DISC_AMOUNT,
                            SUM(PR.GRN_ADVANCE_TAX_AMOUNT) GRN_ADVANCE_TAX_AMOUNT,
                            SUM(PR.GRN_TOTAL_NET_AMOUNT) GRN_TOTAL_NET_AMOUNT 
                        FROM (
                            SELECT  group_item_parent_name,
                                group_item_name,
                                product_barcode_barcode  ,
                                product_id,
                                product_name  ,
                                grn_type ,
                                SUM(TBL_PURC_GRN_DTL_QUANTITY)  GRN_TOTAL_QTY ,
                                SUM(TBL_PURC_GRN_DTL_AMOUNT)  GRN_TOTAL_AMOUNT  ,
                                SUM(TBL_PURC_GRN_DTL_DISC_AMOUNT)  GRN_TOTAL_DISC_AMOUNT ,
                                SUM(TBL_PURC_GRN_DTL_VAT_AMOUNT) GRN_TOTAL_GST_AMOUNT ,
                                SUM(TBL_PURC_GRN_DTL_FED_AMOUNT)  GRN_TOTAL_FED_AMOUNT,
                                SUM(TBL_PURC_GRN_DTL_SPEC_DISC_AMOUNT) GRN_TOTAL_SPEC_DISC_AMOUNT  ,
                                SUM(GRN_OVERALL_DISC_AMOUNT) GRN_OVERALL_DISC_AMOUNT  ,
                                SUM(GRN_ADVANCE_TAX_AMOUNT)   GRN_ADVANCE_TAX_AMOUNT ,
                                SUM(TBL_PURC_GRN_DTL_TOTAL_AMOUNT) GRN_TOTAL_NET_AMOUNT
                            FROM 
                                VW_PURC_GRN
                            where branch_id in (".implode(",",$data['branch_ids']).")
                                and (grn_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') )
                                and grn_type = 'GRN'
                                $where  
                            GROUP BY grn_type,
                                product_barcode_barcode,
                                product_id,
                                product_name,
                                group_item_parent_name,
                                group_item_name 
                            UNION 
                            ALL
                            SELECT  
                                group_item_parent_name,
                                group_item_name,
                                product_barcode_barcode  ,
                                product_id,
                                product_name  ,
                                grn_type ,
                                -SUM(TBL_PURC_GRN_DTL_QUANTITY)  GRN_TOTAL_QTY ,
                                -SUM(TBL_PURC_GRN_DTL_AMOUNT)  GRN_TOTAL_AMOUNT  ,
                                -SUM(TBL_PURC_GRN_DTL_DISC_AMOUNT)  GRN_TOTAL_DISC_AMOUNT ,
                                -SUM(TBL_PURC_GRN_DTL_VAT_AMOUNT) GRN_TOTAL_GST_AMOUNT ,
                                -SUM(TBL_PURC_GRN_DTL_FED_AMOUNT)  GRN_TOTAL_FED_AMOUNT,
                                -SUM(TBL_PURC_GRN_DTL_SPEC_DISC_AMOUNT) GRN_TOTAL_SPEC_DISC_AMOUNT  ,
                                -SUM(GRN_OVERALL_DISC_AMOUNT) GRN_OVERALL_DISC_AMOUNT  ,
                                -SUM(GRN_ADVANCE_TAX_AMOUNT)   GRN_ADVANCE_TAX_AMOUNT ,
                                -SUM(TBL_PURC_GRN_DTL_TOTAL_AMOUNT) GRN_TOTAL_NET_AMOUNT
                            FROM 
                                VW_PURC_GRN
                            where branch_id in (".implode(",",$data['branch_ids']).")
                                and (grn_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') )
                                and grn_type = 'PR'
                                $where  
                            GROUP BY grn_type,
                                product_barcode_barcode,
                                product_id,
                                product_name,
                                group_item_parent_name,
                                group_item_name 
                            ) PR
                        GROUP BY PR.grn_type,
                            PR.product_barcode_barcode,
                            PR.product_id,
                            PR.product_name,
                            PR.group_item_parent_name,
                            PR.group_item_name 
                        order by PR.product_name";
//dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        $list = [];
                        foreach ($getdata as $row){
                             $list[$row->group_item_parent_name.'>>>'.$row->group_item_name][] = $row;
                        }
                    @endphp
                    @php
                        $pi_grand_total_amount = 0;
                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">S.#</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Avg Rate</th>
                            <th class="text-center">Amount</th>
                        </tr>
                        @foreach($list as $pi_key=>$pi_row)
                            @php
                                $sub_total_amount = 0;
                                $ki = 1;
                            @endphp
                            <tr>
                                <td colspan="6"><b>{{strtoupper($pi_key)}}</b></td>
                            </tr>
                            @foreach($pi_row as $pi_k=>$pi_product)
                                <tr>
                                    <td>{{$ki}}</td>
                                    <td>{{$pi_product->product_barcode_barcode}}</td>
                                    <td>{{$pi_product->product_name}}</td>
                                    <td class="text-right">{{number_format($pi_product->grn_total_qty)}}</td>
                                    @php
                                        $avg = $pi_product->grn_total_amount / $pi_product->grn_total_qty
                                    @endphp
                                    <td class="text-right">{{number_format($avg,3)}}</td>
                                    <td class="text-right">{{number_format($pi_product->grn_total_amount,3)}}</td>
                                </tr>
                                @php
                                    $ki += 1;
                                    $sub_total_amount += $pi_product->grn_total_amount;
                                @endphp
                            @endforeach
                            <tr class="sub_total">
                                <td colspan="5" class="rep-font-bold">( {{$pi_key}} ) Sub Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_amount,3)}}</td>
                            </tr>
                            @php
                                $pi_grand_total_amount += $sub_total_amount;
                            @endphp
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="5" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($pi_grand_total_amount,3)}}</td>
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



