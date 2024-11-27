@extends('layouts.report')
@section('title', 'Reporting')

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
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
                </h6>
                @if(isset($data['payment_types']) && count($data['payment_types']) != 0)
                    @php
                        $payment_types = \App\Models\TblDefiPaymentType::whereIn('payment_type_id',$data['payment_types'])->get();
                    @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Payment Type:</span>
                        @foreach($payment_types as $payment_type)
                            <span style="color: #5578eb;">{{" ".$payment_type->payment_type_name." "}}</span>
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
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                        @php
                            $data['SI_salman']=[];
                            $data['SICond']='';
                            $data['SR_salman'] =[];
                            $data['SRCond']='';
                            $grand_total = 0;
                            $sum_sale = 0;
                            $sum_sale_rtn = 0;
                        @endphp
                        <tr class="sticky-header">
                            <th class="text-center">Day</th>
                            @if($data['SI_Count'] >0)
                                <th class="text-center" colspan="{{$data['SI_Count']+1}}">Sales</th>
                            @endif
                            @if($data['SR_Count'] >0)
                                <th class="text-center" colspan="{{$data['SR_Count']+1}}">Sales Return</th>
                            @endif
                            <th class="text-center">Total Value</th>
                        </tr>
                        <tr>
                            <th class="text-center"></th>
                            @if($data['SI_Count'] > 0)
                                @foreach($data['SI'] as $key=>$list)
                                    @php
                                        $sales_sales_man_name = strstr($list->sales_sales_man_name, ' ', true);
                                        if($sales_sales_man_name == false){
                                            $sales_sales_man_name = $list->sales_sales_man_name;
                                        }
                                        $data['SI_salman'][strtolower($sales_sales_man_name)] = $key;
                                        $data['SICond'] .= $list->sales_sales_man." ".$sales_sales_man_name.",";
                                    @endphp
                                    <th class="text-center">{{ucwords($list->sales_sales_man_name)}}</th>
                                @endforeach
                                @php
                                    $data['SICond'] = rtrim($data['SICond'],',');
                                @endphp
                                <th class="text-center">Total</th>
                            @endif
                            @if($data['SR_Count'] > 0)
                                @foreach($data['SR'] as $key=>$list)
                                    @php
                                        $sales_sales_man_name = strstr($list->sales_sales_man_name, ' ', true);
                                        if($sales_sales_man_name == false){
                                            $sales_sales_man_name = $list->sales_sales_man_name;
                                        }
                                        $data['SR_salman'][strtolower($sales_sales_man_name)] = $key;
                                        $data['SRCond'] .= $list->sales_sales_man." ".$sales_sales_man_name.",";
                                    @endphp
                                    <th class="text-center">{{ucwords($list->sales_sales_man_name)}}</th>
                                @endforeach
                                @php
                                    $data['SRCond'] = rtrim($data['SRCond'],',');
                                @endphp
                                <th class="text-center">Total</th>
                            @endif
                            <th class="text-center"></th>
                        </tr>
                        @php
                        //dd($data['SI_salman'][0]);
                            $total_Amt =0;
                        @endphp
                        @if(isset($data['Date']))
                            @foreach($data['Date'] as $date)
                                @php
                                    $match_date = date('Y-m-d', strtotime($date->sales_date));
                                    if($match_date == '2021-03-06'){
                                           // dd($match_date);
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center 1">{{ date('d-m-Y', strtotime(trim(str_replace('/','-',$date->sales_date)))) }}</td>
                                        @php
                                        //dd($data['where_users']);
                                            $match_date = date('Y-m-d', strtotime($date->sales_date));
                                            $POSaleQuery = "select * from (
                                                            select s.sales_date, s.sales_sales_man  ,s.sales_net_amount from tbl_sale_sales s where (s.sales_type ='SI' OR s.sales_type ='POS') ".$data['where_payment_types']." ".$data['where_users']." and branch_id in (".implode(",",$data['branch_ids']).") and sales_date = to_date ('".$match_date."', 'yyyy/mm/dd')
                                                            )
                                                            pivot (
                                                                sum(sales_net_amount) for sales_sales_man in (".$data['SICond'].")
                                                            )
                                                            order  by 1";
                                            
                                            $POSales = \Illuminate\Support\Facades\DB::select($POSaleQuery);
                                            //dump($POSales);
                                        @endphp
                                        @php
                                            $total_Sale =0;
                                        @endphp
                                        @if(isset($POSales) && count($POSales) != 0)
                                            @foreach($POSales as $POSale)
                                                @foreach($data['SI_salman'] as $key=>$salesname)
                                                    @php $POSaleName = $POSale->$key; @endphp
                                                    <td class="text-right 2-{{$key}}">{{number_format($POSaleName,3)}}</td>
                                                    @php 
                                                        $total_Sale += $POSaleName;
                                                        $sum_sale = $sum_sale + $POSaleName;
                                                    @endphp
                                                @endforeach
                                            @endforeach
                                        @endif
                                        @if(isset($POSales) && count($POSales) == 0)
                                            @foreach($data['SI_salman'] as $keyR=>$salesname)
                                                <td class="text-right 3">{{number_format(0,3)}}</td>
                                            @endforeach
                                            <td class="text-right 3_T">{{number_format(0,3)}}</td>
                                        @else
                                            <td class="text-right 4">{{number_format($total_Sale,3)}}</td>
                                        @endif
                                        @php
                                            $match_date = date('Y-m-d', strtotime($date->sales_date));
                                            $POSaleRQuery = "select * from (
                                                            select s.sales_date, s.sales_sales_man  ,s.sales_net_amount from tbl_sale_sales s where (s.sales_type ='SR' OR s.sales_type ='RPOS') ".$data['where_payment_types']." ".$data['where_users']." and branch_id in (".implode(",",$data['branch_ids']).") and sales_date = to_date ('".$match_date."', 'yyyy/mm/dd')
                                                            )
                                                            pivot (
                                                                sum(sales_net_amount) for sales_sales_man in (".$data['SRCond'].")
                                                            )
                                                            order  by 1";
                                            $POSalesR = \Illuminate\Support\Facades\DB::select($POSaleRQuery);
                                        @endphp
                                        @php
                                            $total_Sale_Rtr = 0;
                                        @endphp
                                        @if(isset($POSalesR) && count($POSalesR) != 0)
                                            @foreach($POSalesR as $POSaleR)
                                                @foreach($data['SR_salman'] as $keyR=>$salesnameR)
                                                    @php $POSaleRName = 0; @endphp
                                                    @if(isset($POSaleR->$keyR))
                                                        @php $POSaleRName = $POSaleR->$keyR; @endphp
                                                        <td class="text-right 5-{{$keyR}}">{{number_format($POSaleRName,3)}}</td>
                                                    @else
                                                        <td class="text-right 5">0.000</td>
                                                    @endif
                                                    @php 
                                                        $total_Sale_Rtr += $POSaleRName;
                                                        $sum_sale_rtn = $POSaleRName + $sum_sale_rtn; 
                                                    @endphp

                                                @endforeach
                                            @endforeach
                                        @endif
                                        @if(isset($POSalesR) && count($POSalesR) == 0)
                                            @foreach($data['SR_salman'] as $keyR=>$salesnameR)
                                                <td class="text-right POSR">{{number_format(0,3)}}</td>
                                            @endforeach
                                                <td class="text-right POSR_T">{{number_format(0,3)}}</td>
                                        @else
                                            <td class="text-right P">{{number_format($total_Sale_Rtr,3)}}</td>
                                        @endif

                                        @php
                                            $total_Amt = $total_Sale - $total_Sale_Rtr;
                                            $grand_total = $total_Amt + $grand_total;
                                        @endphp
                                        <td class="text-right L">{{number_format($total_Amt,3)}}</td>
                                </tr>
                            @endforeach
                        @endif
                        <tr class="grand_total">
                            <th class="text-left">Grand Total</th>
                            @if($data['SI_Count'] > 0)
                                @foreach($data['SI'] as $key=>$list)
                                    <th class="text-right">{{-- Sum --}}</th>
                                @endforeach
                                @php
                                    $data['SICond'] = rtrim($data['SICond'],',');
                                @endphp
                                <th class="text-right">{{number_format($sum_sale,3)}}</th>
                            @endif
                            @if($data['SR_Count'] > 0)
                                @foreach($data['SR'] as $key=>$list)
                                    <th class="text-right">{{-- Sum --}}</th>
                                @endforeach
                                <th class="text-right">{{number_format($sum_sale_rtn,3)}}</th>
                            @else
                                <th class="text-right">0.000</th>
                            @endif
                            <th class="text-right">{{ number_format($grand_total,3) }}</th>
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



