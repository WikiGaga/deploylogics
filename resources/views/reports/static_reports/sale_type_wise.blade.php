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
                @if(isset($data['sales_type']) && count($data['sales_type']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Sales Type:</span>
                        @foreach($data['sales_type'] as $sales_type)
                            <span style="color: #5578eb;">{{" ".$sales_type." "}}</span>
                        @endforeach
                    </h6>
                @endif
                @if(isset($data['users']) && count($data['users']) != 0)
                @php
                    $data['Salesman'] = \App\Models\User::whereIn('id',$data['users'])->get();
                @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Salesman:</span>
                        @foreach($data['Salesman'] as $Salesman)
                            <span style="color: #5578eb;">{{" ".ucfirst(strtolower($Salesman->name))}}</span><span style="color: #ff0000">,</span>
                        @endforeach
                    </h6>
                @endif
                @if(isset($data['users']) && count($data['users']) != 0)
                @php
                    $customers = \App\Models\TblSaleCustomer::whereIn('customer_id',$data['customer_ids'])->get();
                @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Customers:</span>
                        @foreach($customers as $customer)
                            <span style="color: #5578eb;">{{" ".ucfirst(strtolower($customer->customer_name))}}</span><span style="color: #ff0000">,</span>
                        @endforeach
                    </h6>
                @endif
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-invoice__logo">
                    <div>
                        @php
                            $path = base_path()."/public/images/".auth()->user()->business->business_profile;
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $data_img = file_get_contents($path);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_img);
                        @endphp
                        <img src="{{$base64}}" width="60px">
                    </div>
                    <div class="kt-invoice__desc">
                        <div>{{strtoupper(auth()->user()->branch->branch_name)}}</div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $data['where'] = "sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') and ";
            //--------sale type --------------
            if(count($data['sales_type']) != 0){
                $sale_type_cond = '';
                $data['where'] .= "(";
                foreach($data['sales_type'] as $sale_type)
                {
                    $sale_type_cond .= "sales_type ='".$sale_type."' OR ";
                }
                $data['where'] .= substr($sale_type_cond,0,-4);
                $data['where'] .= ") and ";
            }
            //-----------end type--------------
            //--------payment type --------------

            if(count($data['payment_types']) != 0){
                $payment_type_cond = '';
                $data['where'] .= "(";
                foreach($data['payment_types'] as $payment_type)
                {
                    $payment_type_cond .= "sales_sales_type = ".(int)$payment_type." OR ";
                }
                $data['where'] .= substr($payment_type_cond,0,-4);
                $data['where'] .= ") and ";
            }else{
                 $payment_type_cond = "sales_sales_type <> '3' and";
            }
            //-----------end type--------------
            //--------users --------------

            if(count($data['users']) != 0){
                $users_cond = '';
                $data['where'] .= "(";
                foreach($data['users'] as $users)
                {
                    $users_cond .= "sales_sales_man = ".(int)$users." OR ";
                }
                $data['where'] .= substr($users_cond,0,-4);
                $data['where'] .= ") and ";
            }
            //-----------end users--------------
            if(count($data['customer_ids']) != 0){
                $data['where'] .= " customer_id in( ".implode(",",$data['customer_ids']).") AND";
            }
            $data['where'] .= " branch_id in( ".implode(",",$data['branch_ids']).")";
            $query = "Select distinct sales_id,sales_type,sales_sales_man_name,sales_code,sales_date,customer_name,sales_sales_type,
            case when  SALES_TYPE = 'RPOS' OR SALES_TYPE = 'SR' THEN
            sales_net_amount * -1 ELSE sales_net_amount  END sales_net_amount from vw_sale_sales_invoice
            where ".$data['where']."
            order by sales_sales_type asc,sales_date asc,sales_code asc";
           // dd($query);
            $listdata = \Illuminate\Support\Facades\DB::select($query);
            $list_data = [];
            foreach ($listdata as $ar){
                $list_data[$ar->sales_sales_man_name][] = $ar;
            }
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">Document No</th>
                            <th class="text-center">Document Date</th>
                            <th class="text-left">Customer Name</th>
                            <th class="text-left">Visa Card</th>
                            <th class="text-left">Cash and Visa Card</th>
                            <th class="text-left">Cash and Credit</th>
                            <th class="text-center">Credit</th>
                            <th class="text-center">Cash</th>
                            <th class="text-center">Total Amount</th>
                        </tr>
                        @php
                            $total_VCAmount = 0;
                            $total_CaVCAmount = 0;
                            $total_CaCrAmount = 0;
                            $total_CaAmount = 0;
                            $total_CrAmount = 0;
                        @endphp
                        @foreach($list_data as $key=>$lists)
                            @php
                                $sub_VCAmount = 0;
                                $sub_CaVCAmount = 0;
                                $sub_CaCrAmount = 0;
                                $sub_CaAmount = 0;
                                $sub_CrAmount = 0;
                            @endphp
                            <tr>
                                <td colspan="9"><b>{{$key}}</b></td>
                            </tr>
                            @foreach($lists as $list)
                            <tr>
                                @if($list->sales_type == 'SI')
                                    <td class="text-center"><a class="report_link" href="/sales-invoice/form/{{$list->sales_id}}" target="_blank">{{$list->sales_code}}</a></td>
                                @elseif($list->sales_type == 'POS')
                                    <td class="text-center"><a class="report_link" href="/pos-sales-invoice/form/{{$list->sales_id}}" target="_blank">{{$list->sales_code}}</a></td>
                                @elseif($list->sales_type == 'SR')
                                    <td class="text-center"><a class="report_link" href="/sale-return/form/{{$list->sales_id}}" target="_blank">{{$list->sales_code}}</a></td>
                                @elseif($list->sales_type == 'RPOS')
                                    <td class="text-center"><a class="report_link" href="/pos-sales-return/form/{{$list->sales_id}}" target="_blank">{{$list->sales_code}}</a></td>
                                @else
                                <td class="text-center">{{$list->sales_code}}</td>
                                @endif
                                <td class="text-center">{{date('d-m-Y', strtotime($list->sales_date))}}</td>
                                <td>{{$list->customer_name}}</td>
                                @if($list->sales_sales_type == '4')
                                    @php $sub_VCAmount += $list->sales_net_amount; @endphp
                                    @php $total_VCAmount += $list->sales_net_amount; @endphp
                                    <td class="text-right">{{number_format($list->sales_net_amount,3)}}</td>
                                @else
                                    <td class="text-right">0</td>
                                @endif
                                @if($list->sales_sales_type == '5')
                                    @php $sub_CaVCAmount += $list->sales_net_amount; @endphp
                                    @php $total_CaVCAmount += $list->sales_net_amount; @endphp
                                    <td class="text-right">{{number_format($list->sales_net_amount,3)}}</td>
                                @else
                                    <td class="text-right">0</td>
                                @endif
                                @if($list->sales_sales_type == '3')
                                    @php $sub_CaCrAmount += $list->sales_net_amount; @endphp
                                    @php $total_CaCrAmount += $list->sales_net_amount; @endphp
                                    <td class="text-right">{{number_format($list->sales_net_amount,3)}}</td>
                                @else
                                    <td class="text-right">0</td>
                                @endif
                                @if($list->sales_sales_type == '2')
                                    @php $sub_CrAmount += $list->sales_net_amount; @endphp
                                    @php $total_CrAmount += $list->sales_net_amount; @endphp
                                    <td class="text-right">{{number_format($list->sales_net_amount,3)}}</td>
                                @else
                                    <td class="text-right">0</td>
                                @endif

                                @if($list->sales_sales_type == '1')
                                    @php $sub_CaAmount += $list->sales_net_amount; @endphp
                                    @php $total_CaAmount += $list->sales_net_amount; @endphp
                                    <td class="text-right">{{number_format($list->sales_net_amount,3)}}</td>
                                @else
                                    <td class="text-right">0</td>
                                @endif
                                <td class="text-right"><b>{{number_format($list->sales_net_amount,3)}}</b></td>
                            </tr>
                            @endforeach
                            @php $sub_grand_Amount = $sub_CrAmount + $sub_CaAmount + $sub_VCAmount + $sub_CaVCAmount + $sub_CaCrAmount ; @endphp
                            <tr class="sub_total">
                                <td colspan="3" class="rep-font-bold">Sub Total ({{$key}}):</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_VCAmount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_CaVCAmount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_CaCrAmount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_CrAmount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_CaAmount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_grand_Amount,3)}}</td>
                            </tr>
                        @endforeach
                        @php $grand_Amount = $total_CrAmount + $total_CaAmount + $total_VCAmount + $total_CaVCAmount + $total_CaCrAmount ; @endphp
                        <tr class="grand_total">
                            <td colspan="3" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($total_VCAmount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CaVCAmount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CaCrAmount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CrAmount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CaAmount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_Amount,3)}}</td>
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



