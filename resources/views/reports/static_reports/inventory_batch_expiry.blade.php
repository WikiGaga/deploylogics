@extends('layouts.report')
@section('title', 'Inventory Batch Expiry Report')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        .bt{
            border-top: 2px solid #008f12;
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
        //dd('ss');

        $pdo = \Illuminate\Support\Facades\DB::getPdo();
        $business_id = auth()->user()->business_id;
        $company_id = auth()->user()->company_id;
        $branch_id = auth()->user()->branch_id;
        $date =  date('Y/m/d',strtotime($data['date']));
        $v_product_id_from = 0;
        $v_product_id_to = 99999999999999999999;
        $stmt = $pdo->prepare("begin ".\App\Library\Utilities::getDatabaseUsername().".PRO_PURC_RPT_NEAR_BATCH_EXPIRY(:p1, :p2, :p3, :p4, :p5, :p6); end;");
        $stmt->bindParam(':p1', $data['date']);
        $stmt->bindParam(':p2', $business_id);
        $stmt->bindParam(':p3', $company_id);
        $stmt->bindParam(':p4', $branch_id);
        $stmt->bindParam(':p5', $v_product_id_from);
        $stmt->bindParam(':p6', $v_product_id_to);
        $stmt->execute();

       /* $list = \App\Models\Report\RptIvenBatchExpiry::whereIn('branch_id',$data['branch_ids']);
        if(count($data['product_ids']) > 0){
          $list =   $list->whereIn('product_name',$data['product_ids']);
        }
        $list =   $list->where('near_expiry_days',$data['near_expiry_days_filter_types'],$data['near_expiry_days']);

        $list =   $list->orderby('near_expiry_days')->get();*/
        $date = date('Y-m-d', strtotime($data['date'])).'+ '.$data['near_expiry_days'].' day';
        $near_expiry_days = date('Y-m-d', strtotime($date));
        $where = "";
        if(count($data['product_ids']) > 0){
          $where .=   "ribe.product_name IN( '".implode("','",$data['product_ids'])."') AND ";
        }
      //  $where .=   "ribe.near_expiry_days ".$data['near_expiry_days_filter_types']." ".$data['near_expiry_days'];
        $where .=   "ribe.batch_expiry_date ".$data['near_expiry_days_filter_types']." to_date ('".$near_expiry_days."', 'yyyy/mm/dd')";

        $qry = "select ribe.*,bsr.PRODUCT_BARCODE_SALE_RATE_RATE,bd.PRODUCT_BARCODE_TAX_APPLY,bd.PRODUCT_BARCODE_TAX_VALUE,
                (ribe.expiry_days_invoice -  ribe.near_expiry_days) expiry_days from RPT_IVEN_BATCH_EXPIRY ribe
                left join TBL_PURC_PRODUCT_BARCODE_DTL bd on (bd.PRODUCT_BARCODE_ID = ribe.PRODUCT_BARCODE_ID AND bd.branch_id = ribe.branch_id)
                left join TBL_PURC_PRODUCT_BARCODE_SALE_RATE bsr on (bsr.PRODUCT_BARCODE_ID = ribe.PRODUCT_BARCODE_ID AND bsr.PRODUCT_CATEGORY_ID = 2 AND bsr.branch_id = ribe.branch_id)
                where $where
                order by ribe.batch_expiry_date";

        $list = \Illuminate\Support\Facades\DB::select($qry);

    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__title">
                    <span style="color: #e27d00;">Date: </span>
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
                @if(isset($data['product_ids']) && count($data['product_ids']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @foreach($data['product_ids'] as $product_name)
                            <span style="color: #5578eb;">{{" ".$product_name." "}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="5%" class="text-left">Sr.No</th>
                            <th width="10%" class="text-left">Item Name</th>
                            <th width="10%" class="text-left">Barcode</th>
                            <th width="8%" class="text-left">UOM</th>
                            <th width="5%" class="text-left">Packing</th>
                            <th width="10%" class="text-center">Batch No</th>
                            <th width="10%" class="text-center">Code</th>
                            {{--<th width="8%" class="text-center">Expiry Days From Inve</th>--}}
                            <th width="8%" class="text-center">Expiry Date</th>
                            <th width="8%" class="text-center">Expiry Days</th>
                            <th width="5%" class="text-center">Qty</th>
                            <th width="5%" class="text-center">Tax %</th>
                            <th width="8%" class="text-center">E.User Rate</th>
                        </tr>
                        @if(count($list) > 0)
                            @foreach($list as $k=>$item)
                                <tr>
                                    <td>{{$k+1}}</td>
                                    <td>{{$item->product_name}}</td>
                                    <td>{{$item->product_barcode_barcode}}</td>
                                    <td>{{$item->uom_name}}</td>
                                    <td class="text-right">{{$item->barcode_packing}}</td>
                                    <td class="text-right">{{$item->batch_no}}</td>
                                    <td class="text-left"><a class="report_link" href="/grn/form/{{ $item->grn_id }}" target="_blank">{{ $item->grn_code }}</a></td>
                                    {{--<td class="text-right">{{$item->expiry_days_invoice}}</td>--}}
                                    <td class="text-right">{{date('d-m-Y',strtotime($item->batch_expiry_date))}}</td>
                                    {{--<td class="text-right">{{$item->near_expiry_days}}</td>--}}
                                    <td class="text-right">{{$item->expiry_days}}</td>
                                    <td class="text-right">{{$item->qty}}</td>
                                    <td class="text-right">{{$item->product_barcode_tax_value}}</td>
                                    <td class="text-right">{{number_format($item->product_barcode_sale_rate_rate,3)}}</td>
                                </tr>
                            @endforeach
                        @endif
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



