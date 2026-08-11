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

        .clickable-cell {
            cursor: pointer;
            color: #17a2b8 !important;
            text-decoration: none;
        }

        .clickable-cell:hover {
            text-decoration: underline;
            color: #117a8b !important;
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');

        // dd($data);
        $voucherMenuMap = [
            'jv' => 31,
            'obv' => 62,
            'crv' => 28,
            'cpv' => 37,
            'brv' => 29,
            'bpv' => 36,
            'ctrv' => 138,
            'lv' => 138,
            'lfv' => 171,
            'brpv' => 269,
            'brrv' => 274,
            'pv' => 270,
            'rv' => 286,
            'ipv' => 271,
            'irv' => 272,
            'pve' => 273,
            'grn' => 23,
            'po' => 38,
            'pr' => 50,
            'si' => 60,
            'so' => 58,
        ];
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
                </h6>
                @if(isset($data['voucher_type']) && count($data['voucher_type']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Voucher Type:</span>
                        @foreach($data['voucher_type'] as $voucher_type)
                            <span style="color: #5578eb;">{{" ".strtoupper($voucher_type)." "}}</span>
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
                            <th class="text-left">Voucher Date</th>
                            <th class="text-left">Voucher No</th>
                            <th class="text-left">Voucher Status</th>
                            <th class="text-center">Account Code</th>
                            <th class="text-left">Account Name<br>Description</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                        </tr>
                        @php
                            $whereVoucher = "";
                            if($data['post_wise'] == "post")
                            {
                                $whereVoucher = " and posted = 1 ";
                            }
                            if($data['post_wise'] == "unposted")
                            {
                                $whereVoucher = " and posted = 0 ";
                            }

                            $debit_grand_total = 0;
                            $credit_grand_total = 0;
                        @endphp
                        @foreach($data['list'] as $branch)
                            <tr class="sub_total">
                                <td colspan="7" class="text-left rep-font-bold">{{$branch->branch_name}}</td>
                            </tr>
                            @php
                                $VNQuery = "Select distinct voucher_date,voucher_no,voucher_status,voucher_type from VW_ACCO_VOUCHER_ALL where
                                            ( voucher_debit <> 0 OR  voucher_credit <> 0 ) and  branch_id in(".$branch->branch_id.") ".$data['where']."
                                            and voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')
                                            $whereVoucher
                                            order by voucher_date,voucher_no";

                                $VNresult = \Illuminate\Support\Facades\DB::select($VNQuery);



                                    $debit_total_unit = 0;
                                    $credit_total_unit = 0;
                            @endphp
                            @foreach($VNresult as $Voucher)
                                @php
                                    $where = "";
                                    if($Voucher->voucher_no != null && $Voucher->voucher_no != ""){
                                         $where .=  " and voucher_no = '".$Voucher->voucher_no."' ";
                                    }
                                    $vou_dr_cr = true;
                                    if($Voucher->voucher_type != null && $Voucher->voucher_type != ""){
                                        if(strtolower($Voucher->voucher_type) == 'siv' || strtolower($Voucher->voucher_type) == 'srv'){
                                             $vou_dr_cr = false;
                                        }
                                    }
                                    if($vou_dr_cr){
                                        $where .=  " and ( voucher_debit <> 0 OR  voucher_credit <> 0 ) ";
                                    }

                                    $Query = "Select voucher_id,voucher_document_id,voucher_date,voucher_no,voucher_status,voucher_type,chart_code,chart_name,voucher_descrip,voucher_debit,voucher_credit,voucher_sr_no from VW_ACCO_VOUCHER_ALL
                                                where voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')
                                            $where and branch_id in(".$branch->branch_id.") ".$data['where']."
                                            $whereVoucher
                                            order by voucher_date,voucher_no,voucher_sr_no";
                                    $result = \Illuminate\Support\Facades\DB::select($Query);


                                    $credit_total = 0;
                                    $debit_total = 0;
                                @endphp
                                @foreach($result as $voucher)
                                    @php
                                        $print_id = $voucher->voucher_document_id ?? '';
                                        if($print_id == ''){$print_id = $voucher->voucher_id;}
                                        $vTypeLower = strtolower($voucher->voucher_type);
                                        $voucherMenuId = isset($voucherMenuMap[$vTypeLower]) ? $voucherMenuMap[$vTypeLower] : '';
                                    @endphp
                                    <tr>
                                        <td class="open_doc_modal clickable-cell text-left" style="cursor: pointer;" data-form_id="{{$print_id}}" data-form_code="{{$voucher->voucher_no}}" data-form_type="{{strtolower($voucher->voucher_type)}}" data-menu_id="{{$voucherMenuId}}">{{date('d-m-Y', strtotime(trim(str_replace('/','-',$voucher->voucher_date))))}}</td>
                                        <td class="text-left">
                                            <span class="generate_report clickable-cell" data-id="{{$print_id}}" data-type="{{$voucher->voucher_type}}">{{$voucher->voucher_no}}</span>
                                        </td>
                                        <td class="text-center">{{$voucher->voucher_status}}</td>
                                        <td class="text-center">{{$voucher->chart_code}}</td>
                                        <td class="text-left">{{$voucher->chart_name}}<br><span style="margin-left:20px;">{{$voucher->voucher_descrip}}</span></td>
                                        <td class="text-right">{{($voucher->voucher_debit != 0) ? number_format($voucher->voucher_debit,3) :""}}</td>
                                        <td class="text-right">{{($voucher->voucher_credit != 0) ? number_format($voucher->voucher_credit,3) :""}}</td>
                                    </tr>
                                    @php
                                        $debit_total += $voucher->voucher_debit;
                                        $credit_total += $voucher->voucher_credit;
                                    @endphp
                                @endforeach
                                @if($data['hide_total'] !=1)
                                    <tr class="total">
                                        <td colspan="5" class="rep-font-bold">Total:</td>
                                        <td class="text-right rep-font-bold">{{number_format($debit_total,3)}}</td>
                                        <td class="text-right rep-font-bold">{{number_format($credit_total,3)}}</td>
                                    </tr>
                                @endif
                                @php
                                    $debit_total_unit+= $debit_total;
                                    $credit_total_unit+= $credit_total;
                                @endphp
                            @endforeach
                            <tr class="sub_total">
                                <td colspan="5" class="rep-font-bold">( {{$branch->branch_name}} ) Sub Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($debit_total_unit,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($credit_total_unit,3)}}</td>
                            </tr>
                            @php
                                $debit_grand_total +=$debit_total_unit;
                                $credit_grand_total +=$credit_total_unit;
                            @endphp
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="5" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($debit_grand_total,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($credit_grand_total,3)}}</td>
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
        $(document).on('click', '.open_doc_modal', function(e) {
            e.preventDefault();
            var form_id   = $(this).data('form_id');
            var form_code = $(this).data('form_code');
            var form_type = $(this).data('form_type');
            var menu_id   = $(this).data('menu_id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = {
                form_id:   form_id,
                form_code: form_code,
                form_type: form_type,
                menu_id:   menu_id
            };

            var data_url = '/upload-document';
            $('#kt_modal_md').modal('show');
            $('#kt_modal_md').find('.modal-content').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Loading document details...</p></div>');
            $('#kt_modal_md').find('.modal-content').load(data_url, formData);
        });
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



