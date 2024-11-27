@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>

    </style>
@endsection

@section('content')
    @php
        $data = Session::get('data');
//dd($data['list']);
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" style="padding: 36px">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                @foreach($data['criteria'] as $show)
                    @php
                        $val_2 = isset($show['value_2'])?$show['value_2']." to ":"";
                        $val_3 = isset($show['value_3'])?$show['value_3']:"";
                    @endphp
                    @if(isset($show['value_1']))
                        @foreach($show['value_1'] as $val)
                            <h6 class="kt-invoice__title">
                                <span style="color: #e27d00;">{{$show['name']}}</span>
                                <span style="color: #5578eb;">{{" ".$show['type']." "}}</span>
                                {{$val}}
                            </h6>
                        @endforeach
                    @endif
                    @if(isset($show['value_2']) || isset($show['value_3']))
                        <h6 class="kt-invoice__title">
                            <span style="color: #e27d00;">{{$show['name']}}</span>
                            <span style="color: #5578eb;">{{" ".$show['type']." "}}</span>
                            {{$val_2.''.$val_3}}
                        </h6>
                    @endif
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
                        <tr>
                            <th>Date</th>
                            <th>Chart Code</th>
                            <th>Chart Account Name</th>
                            <th>Voucher No#</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                            <th>CR/DR</th>
                        </tr>
                        <tr>
                            <th>30-09-2020</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>...::: ( OPENING BALANCE ) :::...</th>
                            <th></th>
                            <th></th>
                            @php
                                $opening_balc =  number_format(abs($data['opening_balance']), 2)
                            @endphp
                            <th class="text-right">{{$opening_balc}}</th>
                            <th class="text-center">
                                @if($data['opening_balance'] > 0)
                                    DR
                                @else
                                    CR
                                @endif
                            </th>
                        </tr>
                        @foreach($data['list'] as $key=>$list)
                            <tr>
                                <td>{{$list->voucher_date}}</td>
                                <td>{{$list->chart_code}}</td>
                                <td>{{$list->voucher_acc_name}}</td>
                                <td>{{$list->voucher_no}}</td>
                                <td>{{$list->voucher_descrip}}</td>
                                <td class="text-right">{{($list->voucher_debit != 0)?$list->voucher_debit:""}}</td>
                                <td class="text-right">{{($list->voucher_credit != 0)?$list->voucher_credit:""}}</td>
                                <td class="text-right">
                                    @if($list->voucher_debit != 0)
                                        @php
                                            $opening_balc = str_replace(',', '', $opening_balc);
                                            $opening_balc =  $opening_balc + $list->voucher_debit;
                                        @endphp
                                    @endif

                                    @if($list->voucher_credit != 0)
                                        @php
                                            $opening_balc = str_replace(',', '', $opening_balc);
                                            $opening_balc =  $opening_balc - $list->voucher_credit;
                                        @endphp
                                    @endif
                                    {{number_format(abs($opening_balc),2)}}
                                </td>
                                <td class="text-center">
                                    @if($opening_balc > 0)
                                        DR
                                    @else
                                        CR
                                    @endif
                                </td>
                            </tr>
                        @endforeach
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



