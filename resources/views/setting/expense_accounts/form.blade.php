@extends('layouts.layout')
@section('title', 'Expense Accounts')
@section('pageCSS')
    <style>
        .section{
            height:160px;
            width:100%;
            background-repeat: no-repeat;
            background-color: #31aef5;
            margin-bottom: 5px;
        }
        .section-container{
            width: 30% !important;
            height:120px;
            color: #fff;
            margin-top: 11px;
            margin-left: 90px;
            float:left;
            padding: 10px;
        }
        .section-container>h1 {
            font-size: 17.55px;
            font-weight: 600 !important;
            padding-top: 20px;
        }
        .section-container>button {
            border: 1px solid #fff !important;
            border-radius: 4px;
            font-family: 'Lato', Helvetica, Arial, sans-serif !important;
            font-size: 12px !important;
            text-transform: capitalize !important;
            font-weight: bold !important;
            background-color: #fff !important;
            width: 20% !important;
            text-decoration: none;
            margin-top:15px;
            text-align:center;
            color: #31aef5 !important;
        }
        .section-container>button:hover {
            color: #1ea4ed !important;
        }
        .section-bg-cover {
            background-repeat: no-repeat;
            background-position:right;
            background-size:320px;
            z-index:5;
        }
        .branchInveAccounts th{
            font-size: 13px  !important;
            padding: 10px !important;
        }
        .branchInveAccounts .branch_name{
            font-size: 13px  !important;
        }
    </style>
@endsection

@section('content')
    @php

    @endphp
    @permission($data['permission'])
    <form id="expense_accounts_form" class="kt-form" method="post" action="{{ action('Setting\ExpenseAccountsController@store') }}">
        @csrf
        <div class="col-lg-12">
            <div class="content-header">
                <div id="block-section-header-reference">
                    <div class="section">
                        <section class="section section--pad-top-small section--pad-bottom-small hide-background-on-mobile section-bg-cover" style="background-image: url(/assets/media/custom/config_header.png);">
                            <div class="section-container">
                                <h1>
                                    {{$data['page_data']['title']}}
                                </h1>
                                <button type="submit" class="btn btn-danger font-weight-bold py-2 px-6">Save</button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="kt-portlet">
                        <div class="kt-portlet__body" style="padding-top:10px;">
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#grn_acc" role="tab">GRN</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="grn_acc" role="tabpanel">
                                    <div class="form-group-block">
                                        <div class="erp_form___block">
                                            <div class="table-scroll form_input__block">
                                                <table data-prefix="grn_acc" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                    <thead class="erp_form__grid_header">
                                                    <tr>
                                                        <th scope="col" width="5%">
                                                            <div class="erp_form__grid_th_title">Sr.</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm text-center">
                                                                <input readonly id="account_id" type="hidden" class="account_id form-control erp-form-control-sm" data-require="true" data-msg="Account Code is required">
                                                                <input readonly id="acc_dr_cr_id" type="hidden" class="acc_dr_cr_id form-control erp-form-control-sm">
                                                                <input readonly id="acc_plus_minus_id" type="hidden" class="acc_plus_minus_id form-control erp-form-control-sm">
                                                            </div>
                                                        </th>
                                                        <th scope="col" width="30%">
                                                            <div class="erp_form__grid_th_title">
                                                                Account Code
                                                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                                    <i class="la la-building"></i>
                                                                </button>
                                                            </div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input id="account_code" type="text" class="acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" data-require="true" data-readonly="true" data-help="grn_acc_grid">
                                                            </div>
                                                        </th>
                                                        <th scope="col" width="30%">
                                                            <div class="erp_form__grid_th_title">Account Name</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <input id="account_name" readonly type="text" class="acc_name form-control erp-form-control-sm" data-readonly="true" data-require="true">
                                                            </div>
                                                        </th>
                                                        <th scope="col" width="30%">
                                                            <div class="erp_form__grid_th_title">Dr/CR</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <select id="acc_dr_cr" class="acc_dr_cr tb_moveIndex form-control erp-form-control-sm select_node" data-convert-id="acc_dr_cr_id" data-readonly="true" data-require="true">
                                                                    <option value="dr">DR</option>
                                                                    <option value="cr">CR</option>
                                                                </select>
                                                            </div>
                                                        </th>
                                                        <th scope="col" width="30%">
                                                            <div class="erp_form__grid_th_title">+/-</div>
                                                            <div class="erp_form__grid_th_input">
                                                                <select id="acc_plus_minus" class="acc_plus_minus tb_moveIndex form-control erp-form-control-sm select_node" data-convert-id="acc_plus_minus_id" data-readonly="true" data-require="true">
                                                                    <option value="+">+</option>
                                                                    <option value="-">-</option>
                                                                </select>
                                                            </div>
                                                        </th>
                                                        <th scope="col" width="5%">
                                                            <div class="erp_form__grid_th_title">Action</div>
                                                            <div class="erp_form__grid_th_btn">
                                                                <button type="button" class="add_data tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                                    <i class="la la-plus"></i>
                                                                </button>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="erp_form__grid_body">
                                                    @php
                                                        $grn_acc_list = \App\Models\Settings\TblDefiExpenseAccounts::with('account')->where('expense_accounts_type','grn_acc')->where('branch_id',auth()->user()->branch_id)->orderBy('sr_no')->get();
                                                    @endphp
                                                    @if(count($grn_acc_list) != 0)
                                                        @foreach($grn_acc_list as $grn_acc)
                                                            <tr>
                                                                <td>
                                                                    <input value="{{$loop->iteration}}" readonly type="text" class="sr_no form-control erp-form-control-sm text-center" autocomplete="off" name="foc[{{$loop->iteration}}][sr_no]" data-id="sr_no">
                                                                    <input readonly value="{{$grn_acc['chart_account_id']}}" type="hidden" class="account_id form-control erp-form-control-sm" autocomplete="off" name="grn_acc[{{$loop->iteration}}][account_id]" data-id="account_id">
                                                                    <input readonly value="{{$grn_acc['expense_accounts_dr_cr']}}" type="hidden" class="acc_dr_cr_id form-control erp-form-control-sm" autocomplete="off" name="grn_acc[{{$loop->iteration}}][acc_dr_cr_id]" data-id="acc_dr_cr_id">
                                                                    <input readonly value="{{$grn_acc['expense_accounts_plus_minus']}}" type="hidden" class="acc_plus_minus_id form-control erp-form-control-sm" autocomplete="off" name="grn_acc[{{$loop->iteration}}][acc_plus_minus_id]" data-id="acc_plus_minus_id">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" autocomplete="off" name="grn_acc[{{$loop->iteration}}][acc_code]" value="{{$grn_acc->account->chart_code}}" data-id="acc_code" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="acc_name tb_moveIndex open_inline__help form-control erp-form-control-sm" autocomplete="off" name="grn_acc[{{$loop->iteration}}][account_name]" value="{{$grn_acc->account->chart_name}}" data-id="account_name" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="acc_dr_cr tb_moveIndex form-control erp-form-control-sm select_node" autocomplete="off" aria-invalid="false" name="grn_acc[{{$loop->iteration}}][acc_dr_cr]" value="{{strtoupper($grn_acc['expense_accounts_dr_cr'])}}" readonly data-id="acc_dr_cr">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="acc_plus_minus tb_moveIndex form-control erp-form-control-sm select_node" autocomplete="off" aria-invalid="false" name="grn_acc[{{$loop->iteration}}][acc_plus_minus]" value="{{$grn_acc['expense_accounts_plus_minus']}}" readonly data-id="acc_plus_minus">
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                                        <button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>{{-- end table block--}}
                                </div> {{-- end grn_acc--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endpermission
@endsection

@section('pageJS')
@endsection
@section('customJS')
    <script src="{{ asset('js/pages/js/setting/expense-accounts.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/table-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script>

    </script>
@endsection
