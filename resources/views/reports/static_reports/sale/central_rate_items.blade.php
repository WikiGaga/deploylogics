@extends('layouts.report')
@section('title', 'Central Rate Items')

@section('pageCSS')
    <style>
         /* Styles go here */
         @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        tbody.product_wise_profit tr:hover {
            background: antiquewhite;
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


                $qry = "SELECT 
                    PR.BRANCH_NAME,
                    PROD_BARCODE.PRODUCT_BARCODE_BARCODE,
                    PR.product_id,
                    PR.product_code,
                    PR.product_name,
                    PR.product_arabic_name,
                    PR.BRAND_NAME,
                    PR.GROUP_ITEM_PARENT_NAME,
                    PR.SUPPLIER_NAME
                FROM
                    (SELECT 
                        BRANCH_NAME,
                        product_id,
                        product_code,
                        product_name,
                        product_arabic_name,
                        BRAND_NAME,
                        GROUP_ITEM_PARENT_NAME,
                        SUPPLIER_NAME
                    FROM
                        VW_PURC_PRODUCT_BARCODE
                    WHERE product_warranty_status  = '1'
                        $where
                    ) PR 
                    LEFT OUTER JOIN 
                        (SELECT 
                            MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
                            PRODUCT_ID 
                        FROM
                            tbl_purc_product_barcode 
                        WHERE BASE_BARCODE = 1 
                        GROUP BY PRODUCT_ID) PROD_BARCODE 
                        ON PR.PRODUCT_ID = PROD_BARCODE.PRODUCT_ID 
                    GROUP BY PROD_BARCODE.PRODUCT_BARCODE_BARCODE,
                        PR.BRANCH_NAME,
                        PR.product_id,
                        PR.product_code,
                        PR.product_name,
                        PR.product_arabic_name,
                        PR.BRAND_NAME,
                        PR.GROUP_ITEM_PARENT_NAME,
                        PR.SUPPLIER_NAME
                    ORDER BY PR.product_name";
                //dd($qry);
                $getdata = \Illuminate\Support\Facades\DB::select($qry);
                $list = [];
                foreach ($getdata as $row)
                {
                    $list[$row->group_item_parent_name][] = $row;
                }
                //dd($list);
            @endphp
                <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                    <tr class="sticky-header">
                        <th width="5%" class="text-center">Sr. No</th>
                        <th width="15%" class="text-center">Barcode</th>
                        <th width="35%" class="text-left">Product Name</th>
                        <th width="15%" class="text-left">Brand Name</th>
                        <th width="30%" class="text-center">Supplier</th>
                    </tr>
                    @foreach($list as $group_parent_key=>$group_parent_row)
                        @php
                            $group_parent_name = ucwords(strtolower($group_parent_key));
                        @endphp
                        <tr class="outer_total">
                            <td colspan="5">{{ucwords(strtolower($group_parent_key))}}</td>
                        </tr>
                        @php
                            $i = 1;
                        @endphp
                        @foreach($group_parent_row as $i_key=>$list_row)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $list_row->product_barcode_barcode }}</td>
                                <td>{{ $list_row->product_name }}</td>
                                <td>{{ $list_row->brand_name }}</td>
                                <td>{{ $list_row->supplier_name }}</td>
                            </tr>
                            @php
                                $i++;
                            @endphp
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
