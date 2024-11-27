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
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" style="padding: 36px">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                @foreach($data['criteria'] as $show)
                    @php
                        $val_1 = isset($show['value_1'])?$show['value_1']:"";
                        $val_2 = isset($show['value_2'])?$show['value_2']." to ":"";
                        $val_3 = isset($show['value_3'])?$show['value_3']:"";
                    @endphp
                    <h6 class="kt-invoice__title">
                        <span style="color: #e27d00;">{{$show['name']}}</span>
                        <span style="color: #5578eb;">{{" ".$show['type']." "}}</span>
                        {{$val_1.''.$val_2.''.$val_3}}</h6>
                @endforeach
            </div>
            <div class="kt-portlet__head-toolbar text-center">
                <div href="#" class="kt-invoice__logo">
                    <a href="#"><img src="/images/1601992238.jpeg" width="60px"></a>
                    <div class="kt-invoice__desc">
                    <div>Cecilia Chapman, 711-2880 Nulla St, Mankato</div>
                    <div>Mississippi 96522</div>
                </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        @php
                            $data['SI_salman']=[];
                            $data['SICond']='';
                            $data['SRCond']=[];
                        @endphp
                        <tr>
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
                            @if($data['SI_Count'] >0)
                                @foreach($data['SI'] as $key=>$list)
                                    @php 
                                        array_push($data['SI_salman'],$list->sales_sales_man_name);
                                        $data['SICond'].=$list->sales_sales_man." ".$list->sales_sales_man_name.",";
                                    @endphp
                                    <th class="text-center">{{$list->sales_sales_man_name}}</th>
                                @endforeach
                                @php 
                                    $data['SICond']=rtrim($data['SICond'],',');
                                @endphp
                                <th class="text-center">Total</th>
                            @endif
                            @if($data['SR_Count'] >0)
                                @foreach($data['SR'] as $key=>$list)
                                    <th class="text-center">{{$list->sales_sales_man_name}}</th>
                                @endforeach
                                <th class="text-center">Total</th>
                            @endif
                            <th class="text-center"></th>
                        </tr>
                        @php
                        //dd($data['SI_salman'][0]);
                            $total_Sale =0;
                            $total_Amt =0;
                        @endphp
                        @if(isset($data['Date']))
                            @foreach($data['Date'] as $date)
                                <tr>
                                    <td class="text-center">{{ $date->sales_date }}</td>
                                        @php
                                            $match_date = date('Y-m-d', strtotime($date->sales_date));
                                            $POSaleQuery = "select * from (
                                                            select s.sales_date, s.sales_sales_man  ,s.sales_net_amount from tbl_sale_sales s where s.sales_type ='SI' and sales_date = to_date ('".$match_date."', 'yyyy/mm/dd')
                                                            )
                                                            pivot ( 
                                                                sum(sales_net_amount) for sales_sales_man in (".$data['SICond'].")
                                                            )
                                                            order  by 1";
                                            $POSales = \Illuminate\Support\Facades\DB::select($POSaleQuery);
                                        @endphp
                                        @if(isset($POSales))
                                            @foreach($POSales as $POSale)
                                                    <td class="text-right">{{number_format($POSale->umar,3)}}</td>
                                                    <td class="text-right">{{number_format($POSale->royal,3)}}</td>
                                                    @php $total_Sale = $POSale->umar + $POSale->royal; @endphp
                                            @endforeach
                                        @endif
                                            <td class="text-right">{{number_format($total_Sale,3)}}</td>
                                        @php
                                            $match_date = date('Y-m-d', strtotime($date->sales_date));
                                            $POSaleRQuery = "select * from (
                                                            select s.sales_date, s.sales_sales_man  ,s.sales_net_amount from tbl_sale_sales s where s.sales_type ='SR' and sales_date = to_date ('".$match_date."', 'yyyy/mm/dd')
                                                            )
                                                            pivot ( 
                                                                sum(sales_net_amount) for sales_sales_man in (".$data['SICond'].")
                                                            )
                                                            order  by 1";
                                            $POSalesR = \Illuminate\Support\Facades\DB::select($POSaleRQuery);
                                            $total_Sale_Rtr = 0;
                                        @endphp
                                        @if(isset($POSalesR))
                                            @foreach($POSalesR as $POSaleR)
                                                    <td class="text-right">{{number_format($POSaleR->royal,3)}}</td>
                                                    @php $total_Sale_Rtr = $POSaleR->royal; @endphp
                                            @endforeach
                                        @endif
                                        @if($total_Sale_Rtr == '' || $total_Sale_Rtr == null || $total_Sale_Rtr == 0)
                                            <td class="text-right">{{number_format(0,3)}}</td>
                                        @endif
                                        <td class="text-right">{{number_format($total_Sale_Rtr,3)}}</td>
                                        @php $total_Amt = $total_Sale - $total_Sale_Rtr ; @endphp
                                    <td class="text-right">{{number_format($total_Amt,3)}}</td>
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



