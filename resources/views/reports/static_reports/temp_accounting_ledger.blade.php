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
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['chart_account']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Chart Code:</span>
                        @foreach($data['chart_account'] as $ca_list)
                            <span style="color: #5578eb;">{{$ca_list->chart_code}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['chart_account']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Chart Title:</span>
                        @foreach($data['chart_account'] as $ca_list)
                            <span style="color: #5578eb;">{{$ca_list->chart_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @php $customer_list = \Illuminate\Support\Facades\DB::table('tbl_sale_customer')->whereIn('customer_account_id',$data['chart_account_ids'])->get('customer_tax_no'); @endphp
                @if(count($customer_list) != 0)
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Customer Tax No:</span>
                    @foreach($customer_list as $customer)
                        <span style="color: #5578eb;">{{" ".$customer->customer_tax_no}}</span>
                    @endforeach
                </h6>
                @endif
                @if(isset($data['voucher_types']) && count($data['voucher_types']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Voucher Type:</span>
                        @foreach($data['voucher_types'] as $voucher_type)
                            <span style="color: #5578eb;">{{" ".strtoupper($voucher_type)." "}}</span>
                        @endforeach
                    </h6>
                @endif
                @if(isset($data['voucher_mode_date']) && $data['voucher_mode_date'] == 1)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Dispatch Date:</span>
                        <span style="color: #5578eb;"> Yes</span>
                    </h6>
                @endif
                @if(isset($data['al_ref_acc_toggle']) && $data['al_ref_acc_toggle'] == 1)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Reference Account:</span>
                        <span style="color: #5578eb;"> Yes</span>
                    </h6>
                @endif
                @if(isset($data['al_vat_amount_toggle']) && $data['al_vat_amount_toggle'] == 1)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Vat Amount:</span>
                        <span style="color: #5578eb;"> Yes</span>
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $colspan = 4;
                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <thead>
                            <tr class="sticky-header">
                                <th>Branch Name</th>
                                <th>Date</th>
                                <th>Voucher No#</th>
                                <th>Description</th>
                                @if($data['al_ref_acc_toggle'] == 1)
                                    @php $colspan += 1; @endphp
                                    <th>Reference Account</th>
                                @endif
                                @if($data['al_vat_amount_toggle'] == 1)
                                    <th>Vat Amount</th>
                                @endif
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                                <th>CR/DR</th>
                            </tr>
                        </thead>
                        @php
                            $total_debit = 0;
                            $total_credit =0 ;
                            $sub_opening_balc = 0;
                            $opening_balc = 0;
                            $total_vat_amt = 0;
                        @endphp
                        @if($data['opening_bal_toggle'] == 1)
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>...::: ( OPENING BALANCE ) :::...</th>
                            <th></th>
                            @if($data['al_ref_acc_toggle'] == 1) <th></th> @endif
                            @if($data['al_vat_amount_toggle'] == 1) <th></th> @endif
                            <th></th>
                            @php
                                if($data['opening_balance'] == null){
                                    $opening_balc =  0;
                                }else{
                                    $opening_balc =  $data['opening_balance'];
                                }
                            @endphp
                            <th class="text-right">
                                @if($opening_balc > 0)
                                    {{number_format($opening_balc,3)}}
                                @else
                                    {{number_format($opening_balc * (-1),3)}}
                                @endif
                            </th>
                            <th class="text-center">
                                @if($opening_balc > 0)
                                    DR
                                @else
                                    CR
                                @endif
                            </th>
                        </tr>
                        @endif
                        @php
                            //--------voucher type --------------
                                if($data['voucher_types_selection'] == 'contain'){
                                    $vt_type = '=';
                                    $AND_OR = 'OR';
                                }else{
                                    $vt_type = '!=';
                                    $AND_OR = 'AND';
                                }
                                $where_voucher_type = '';
                                if(count($data['voucher_types']) > 0){
                                    $voucher_type_cond = '';
                                    $where_voucher_type = ' and (';
                                    foreach($data['voucher_types'] as $voucher_type)
                                    {
                                        $voucher_type_cond .= "upper(voucher_type) $vt_type '".$voucher_type."' $AND_OR ";
                                    }
                                    $where_voucher_type .= substr($voucher_type_cond,0,-4);
                                    $where_voucher_type .= ')';
                                }
                            //-----------end type--------------

                            $where = " ( ";
                            $arr_count = count($data['chart_account']) - 1;
                            foreach ($data['chart_account'] as $k=>$chartAccoId){
                                $where .= " VOUCH.chart_account_id = $chartAccoId->chart_account_id ";
                                if($arr_count != $k){
                                    $where .= " OR ";
                                }
                            }
                            $where .= " ) ";

                            $where .= " AND (VOUCH.business_id = ".auth()->user()->business_id." AND VOUCH.branch_id in (".implode(",",$data['branch_ids']).") )";
                            $where .= $where_voucher_type;
                            if($data['voucher_mode_date'] == 1){
                                $date_field = 'voucher_mode_date';
                            }else{
                                $date_field = 'voucher_date';
                            }
                            
                            $query = "Select VOUCH.*,SALES.CUSTOMER_ID from vw_acco_voucher VOUCH,TBL_SOFT_VOUCHER_SQUENCE SEQ,TBL_SALE_SALES SALES
                            where VOUCH.voucher_TYPE = SEQ.SQUENCE_VOUCHER_TYPE(+) AND
                            VOUCH.voucher_document_id = SALES.SALES_ID(+) AND
                            (VOUCH.$date_field between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd'))
                            and ( VOUCH.voucher_debit <> 0 OR  VOUCH.voucher_credit <> 0 ) and " .$where." order by VOUCH.voucher_date,VOUCH.created_at,SEQ.SQUENCE_SORTING_ORDER,VOUCH.voucher_sr_no,VOUCH.VOUCHER_NO";
                            //  die($query);
                            $ResultList = \Illuminate\Support\Facades\DB::select($query);
                            //  dd($ResultList);
                        @endphp
                        @foreach($ResultList as $key=>$list)
                                @php 
                                    $resultCount = \Illuminate\Support\Facades\DB::table('tbl_sale_sales')->where('sales_code' , $list->voucher_no)->whereIn('customer_id',$data['not_contain_customer'])->count();
                                @endphp
                                @if($resultCount == 0)
                                    @php
                                        $color = 'black!important';
                                        $print_id = $list->voucher_document_id;
                                        if($list->voucher_document_id == ''){$print_id = $list->voucher_id;}
                                        if($list->voucher_credit > 0) { $color = 'blue!important'; }
                                        if($list->voucher_type == 'cpv' || $list->voucher_type == 'bpv'){ $color = 'red!important'; }
                                        if($list->voucher_type == 'GRN'){ $list->voucher_descrip = str_replace("Purchase:" , "" , $list->voucher_descrip); }
                                        if($list->voucher_type == 'PR'){ $list->voucher_descrip = str_replace("Purchase Return:" , "" , $list->voucher_descrip); }
                                        // Replace Some Words
                                        if($list->voucher_type == 'PR' || $list->voucher_type == 'PO' || $list->voucher_type == 'GRN'){
                                            $list->voucher_descrip = str_replace("-" , "" , $list->voucher_descrip);
                                        }
                                        $list->voucher_descrip = str_replace("Inv.:" , "" , $list->voucher_descrip);
                                    @endphp
                                    <tr>
                                        <td style="color:{{$color}}">{{ isset($list->branch_short_name) ? $list->branch_short_name : ""  }}</td>
                                        <td style="color:{{$color}}">{{date('d-m-Y', strtotime(trim(str_replace('/','-',$list->voucher_date))))}}</td>
                                        <td><span style="color:{{$color}}" class="generate_report" data-id="{{$print_id}}" data-type="{{$list->voucher_type}}">{{$list->voucher_no}}</span></td>
                                        <td style="color:{{$color}}">{{trim($list->voucher_descrip)}}</td>
                                        @if($data['al_ref_acc_toggle'] == 1) <td style="color:{{$color}}">{{$list->chart_name_ref_account}}</td> @endif
                                        @if($data['al_vat_amount_toggle'] == 1) <td class="text-right" style="color:{{$color}}">{{($list->vat_amount == null)?"":number_format($list->vat_amount,3)}}</td> @endif
                                        <td class="text-right" style="color:{{$color}}">{{($list->voucher_debit != 0)?number_format($list->voucher_debit,3):""}}</td>
                                        <td class="text-right" style="color:{{$color}}">{{($list->voucher_credit != 0)?number_format($list->voucher_credit,3):""}}</td>
                                        <td class="text-right" style="color:{{$color}}">
                                            @if($list->voucher_debit != 0)
                                                @php
                                                    $opening_balc = str_replace(',', '', $opening_balc);
                                                    $opening_balc =  $opening_balc + $list->voucher_debit;
                                                    $total_debit +=  $list->voucher_debit;
                                                @endphp
                                            @endif

                                            @if($list->voucher_credit != 0)
                                                @php
                                                    $opening_balc = str_replace(',', '', $opening_balc);
                                                    $opening_balc =  $opening_balc - $list->voucher_credit;
                                                    $total_credit += $list->voucher_credit;
                                                @endphp
                                            @endif
                                            @if($opening_balc > 0)
                                                {{number_format($opening_balc,3)}}
                                            @else
                                                {{number_format($opening_balc * (-1),3)}}
                                            @endif
                                        </td>
                                        <td class="text-center" style="color:{{$color}}">
                                            @if($opening_balc > 0)
                                                DR
                                            @else
                                                CR
                                            @endif
                                        </td>
                                    </tr>
                                    @if($data['al_vat_amount_toggle'] == 1)
                                        @php
                                            $total_vat_amt += $list->vat_amount;
                                        @endphp
                                    @endif
                                @endif
                        @endforeach
                        <tr class="sub_total">
                            <td colspan="{{$colspan}}" class="rep-font-bold">Activity Total ({{$data['currency']->currency_symbol}}):</td>
                            @if($data['al_vat_amount_toggle'] == 1)
                                <td class="text-right rep-font-bold">{{number_format($total_vat_amt,3)}}</td>
                            @endif
                            <td class="text-right rep-font-bold">{{number_format($total_debit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_credit,3)}}</td>
                            <td class="text-right rep-font-bold">
                                @php $sub_opening_balc = $total_debit - $total_credit; @endphp
                                @if($sub_opening_balc > 0)
                                    {{number_format($sub_opening_balc,3)}}
                                @else
                                    {{number_format($sub_opening_balc * (-1),3)}}
                                @endif
                            </td>
                            <td class="text-center rep-font-bold">
                                @if($sub_opening_balc > 0)
                                    DR
                                @else
                                    CR
                                @endif
                            </td>
                        </tr>
                        <tr class="grand_total">
                            <td colspan="{{$colspan}}" class="rep-font-bold">Closing Total ({{$data['currency']->currency_symbol}}):</td>
                            @if($data['al_vat_amount_toggle'] == 1)
                                <td class="text-right rep-font-bold">{{number_format($total_vat_amt,3)}}</td>
                            @endif
                            <td class="text-right rep-font-bold">{{number_format($total_debit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_credit,3)}}</td>
                            <td class="text-right rep-font-bold">
                                @if($opening_balc > 0)
                                    {{number_format($opening_balc,3)}}
                                @else
                                    {{number_format($opening_balc * (-1),3)}}
                                @endif
                            </td>
                            <td class="text-center rep-font-bold">
                                @if($opening_balc > 0)
                                    DR
                                @else
                                    CR
                                @endif
                            </td>
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
    <script>

    </script>
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


