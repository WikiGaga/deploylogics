@extends('layouts.layout')
@section('title', 'Journal/Opening Balance')

@section('pageCSS')
    <style>
        #account_code-error{
            display: none !important;
        }
    </style>
@endsection
@section('content')
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $id = "";
                $voucher_no = $data['voucher_no'];
                $date =  date('d-m-Y');
            }
            if($case == 'new' && $data['copy_entry']){
                $id = "";
                $voucher_no = $data['voucher_no'];
                $payment_modes = $data['payment_mode'];
                $date =  date('d-m-Y');
                $dtls = isset($data['dtl'])? $data['dtl'] :[];
            }
            if($case == 'edit'){
                $id = $data['current']->voucher_id;
                $voucher_no= $data['current']->voucher_no;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
                $currency_id = $data['current']->currency_id;
                $exchange_rate = $data['current']->voucher_exchange_rate;
                $notes = $data['current']->voucher_notes;
                $dtls = isset($data['dtl'])? $data['dtl'] :[];
            }
            $type = $data['type'];
            $form_type = $type;
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="voucher_form" class="kt-form" method="post" action="{{ action('Accounts\VoucherController@jvStore', [$type,isset($id)?$id:'']) }}">
    @csrf
    @if(session('msg'))
        <script>
            alert('This voucher enter in BRS!');
            document.location='/listing/accounts/{{ $type }}'; 
        </script>
    @endif
        <input type="hidden" name="form_type" id="form_type" value="{{$form_type}}">
        <input type="hidden" name="voucher_no" value="{{$voucher_no}}">
        <input type="hidden" id="voucher_id" value='{{$id}}' >
        @if($case == 'edit')
            <input type="hidden" id="form_id" value='{{$id}}' >
            <input type="hidden" id="menu_id" value="{{$data['stock_menu_id']}}">
        @endif
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{$voucher_no}}
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    @if($case == 'new')
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label text-right">Last Voucher No.</label>
                                        <div class="col-lg-6">
                                            <div class="erp_form___block">
                                                <div class="input-group open-modal-group">
                                                    <input type="text" id="last_voucher_no" value="{{isset($data['last_voucher_no'])?$data['last_voucher_no']:""}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','oJVVoucherHelp')}}" autocomplete="off" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text btn-open-mob-help" id="getDataByVoucherNo">
                                                           GO
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Voucher Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="voucher_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" autofocus/>
                                        <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Currency:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm moveIndex kt-select2 currency" id="currency_id" name="currency_id">
                                            <option value="">Select</option>
                                            @if($case == 'edit')
                                                @php $currency_id = isset($currency_id)?$currency_id:''@endphp
                                                @foreach($data['currency'] as $currency)
                                                    <option value="{{$currency->currency_id}}" {{$currency->currency_id==$currency_id?'selected':''}}>{{$currency->currency_name}}</option>
                                                @endforeach
                                            @else
                                                @foreach($data['currency'] as $currency)
                                                    @if($currency->currency_default=='1')
                                                        @php $exchange_rate = $currency->currency_rate; @endphp
                                                    @endif
                                                    <option value="{{$currency->currency_id}}" {{$currency->currency_default=='1'?'selected':''}}>{{$currency->currency_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Exchange Rate:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($exchange_rate)?$exchange_rate:''}}" class="moveIndex form-control erp-form-control-sm validNumber">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="data_entry_header">
                                <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                                <div class="dropdown dropdown-inline">
                                    <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                        <i class="flaticon-more" style="color: #666666;"></i>
                                    </button>
                                    @php
                                        $headings = ['Sr No','Account Code','Account Name','Description','Budget','Debit',
                                                      'Credit','FC Debit','FC Credit'];
                                    @endphp
                                    <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                        @foreach($headings as $key=>$heading)
                                            <li >
                                                <label>
                                                    <input value="{{$key}}" type="checkbox" checked> {{$heading}}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @include('layouts.pageSettingBtn')
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col" width="35px">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input id="account_id" readonly type="hidden" class="account_id form-control erp-form-control-sm">
                                                <input id="budget_id" readonly type="hidden" class="budget_id form-control erp-form-control-sm">
                                                <input id="budget_branch_id" readonly type="hidden" class="budget_branch_id form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">
                                                Account Code
                                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                    <i class="la la-barcode"></i>
                                                </button>
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="account_code" type="text" class="acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Account Name</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="account_name" readonly type="text" class="acc_name form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Description</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_descrip" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">
                                                Budget
                                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                    <i class="la la-barcode"></i>
                                                </button>
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget" type="text" class="budget_dscrp tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','budgetHelp')}}">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Debit</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_debit" type="text" class="debit validNumber validOnlyFloatNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Credit</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_credit" type="text" class="credit tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">FC Debit</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_fc_debit" type="text" class="fcdebit tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">FC Credit</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_fc_credit" type="text" class="fccredit tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col" width="48">
                                            <div class="erp_form__grid_th_title">Action</div>
                                            <div class="erp_form__grid_th_btn">
                                                <button type="button" id="addData" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body">
                                    @if(isset($dtls))
                                        @foreach($dtls as $data)
                                            @php
                                                $bgt_dsc = '';
                                                $budget =\App\Models\TblAccBudget::where('budget_id',$data->budget_id)->where('budget_branch_id',$data->budget_branch_id)->first();
                                                if($budget != Null){
                                                    $bgt_dsc = $budget->budget_budgetart_position;
                                                }
                                            @endphp
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{ $loop->iteration }}" name="pd[{{ $loop->iteration }}][voucher_sr_number]" title="{{ $loop->iteration }}"  class=" form-control erp-form-control-sm handle" readonly>
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][account_id]" data-id="account_id" value="{{$data->chart_account_id}}"  class="account_id form-control erp-form-control-sm">
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][budget_id]" data-id="budget_id" value="{{$data->budget_id}}"  class="budget_id form-control erp-form-control-sm">
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][budget_branch_id]" data-id="budget_branch_id" value="{{$data->budget_branch_id}}"  class="budget_branch_id form-control erp-form-control-sm">
                                                </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][account_code]" data-id="account_code" value="{{$data->accounts->chart_code}}" title="{{$data->accounts->chart_code}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" class="acc_code open_inline__help tb_moveIndex form-control erp-form-control-sm"></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][account_name]" data-id="account_name" value="{{$data->accounts->chart_name}}" title="{{$data->accounts->chart_name}}" class="acc_name form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_descrip]" data-id="voucher_descrip" value="{{$data->voucher_descrip}}" title="{{$data->voucher_descrip}}" class="moveIndex moveIndex2  form-control erp-form-control-sm" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][budget]" data-id="budget" value="{{isset($bgt_dsc)?$bgt_dsc:''}}" title="{{isset($bgt_dsc)?$bgt_dsc:''}}"   data-url="{{action('Common\DataTableController@inlineHelpOpen','budgetHelp')}}" class="budget_dscrp open_inline__help tb_moveIndex form-control erp-form-control-sm"></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_debit]" data-id="voucher_debit" value="{{number_format($data->voucher_debit,3)}}" title="{{$data->voucher_debit}}" class="tb_moveIndex debit form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_credit]" data-id="voucher_credit" value="{{number_format($data->voucher_credit,3)}}" title="{{$data->voucher_credit}}" class="tb_moveIndex credit form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_fc_debit]" data-id="voucher_fc_debit" value="{{number_format($data->voucher_fc_debit,3)}}" title="{{$data->voucher_fc_debit}}" class="tb_moveIndex fcdebit form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_fc_credit]" data-id="voucher_fc_credit" value="{{number_format($data->voucher_fc_credit,3)}}" title="{{$data->voucher_fc_credit}}" class="tb_moveIndex fccredit form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tbody>
                                    <tr height="30">
                                        <td colspan="2" class="text-right align-middle font-weight-bold">Total Difference :</td>
                                        <td class="text-center align-middle font-weight-bold" >
                                            <span id="tot_difference" ></span>
                                            <input type="hidden" name="tot_jv_difference" id="tot_jv_difference">
                                        </td>
                                        <td colspan="2" class="text-right align-middle font-weight-bold">Total :</td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_debit" ></span>
                                        </td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_credit" ></span>
                                            <input id="tot_voucher_credit" name="tot_voucher_credit" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                        </td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_fcdebit" ></span>
                                        </td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_fccredit" ></span>
                                        </td>

                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <label class="col-lg-2 erp-col-form-label">Notes:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="2" id="voucher_notes" name="voucher_notes" class="form-control erp-form-control-sm">{{isset($notes)?$notes:''}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
                <!--end::Form-->
    @php session()->forget('jv'); @endphp
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')

    <script src="{{ asset('js/pages/js/voucher.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/account-table-calculations.js') }}" type="text/javascript"></script>
    <script>
        var accountsHelpUrl = "{{url('/common/inline-help/accountsHelp')}}";
        var budgetHelpUrl = "{{url('/common/inline-help/budgetHelp')}}";
    </script>
    <script>
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'account_code',
                'fieldClass':'acc_code tb_moveIndex open_inline__help',
                'message':'Enter Account Detail',
                'require':true,
                'readonly':false,
                'data-url' : accountsHelpUrl
            },
            {
                'id':'account_name',
                'fieldClass':'acc_name',
                'message':'Enter Account Name',
                'require':true,
                'readonly':true
            },
            {
                'id':'voucher_descrip',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'budget',
                'fieldClass':'budget_dscrp tb_moveIndex open_inline__help',
                'data-url' : budgetHelpUrl
            },
            {
                'id':'voucher_debit',
                'fieldClass':'tb_moveIndex debit validNumber validOnlyFloatNumber'
            },
            {
                'id':'voucher_credit',
                'fieldClass':'tb_moveIndex credit validNumber validOnlyFloatNumber'
            },
            {
                'id':'voucher_fc_debit',
                'fieldClass':'tb_moveIndex fcdebit validNumber validOnlyFloatNumber'
            },
            {
                'id':'voucher_fc_credit',
                'fieldClass':'tb_moveIndex fccredit validNumber validOnlyFloatNumber'
            }
        ];
        var arr_hidden_field = ['account_id','budget_id','budget_branch_id'];
    </script>
    {{--<script src="{{ asset('js/pages/js/account-row-repeated.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    @if($case == 'new')
        @include('partial_script.copy_voucher_data')
    @endif
@endsection

