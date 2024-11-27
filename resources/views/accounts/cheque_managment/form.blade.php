@extends('layouts.layout')
@section('title', 'Cheque Managment')

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
            $date   =  date('d-m-Y');  
            $code   =  $data['code']; 
        }
        if($case == 'edit'){
            $id = $data['current']->cheque_managment_id;
            $code= $data['current']->cheque_managment_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->cheque_managment_date))));
            $receive_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->receive_date))));
            $cheque_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->cheque_date))));
            $notifyBeforeDays = $data['current']->notify_before_days;
            $notifyTo = $data['current']->notify_to;
            $notes = $data['current']->cheque_managment_remarks;
            $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
        }
    @endphp
    @permission($data['permission'])
     <!--begin::Form-->
    <form id="voucher_form" class="kt-form" method="post" action="{{action('Accounts\ChequeManagmentController@store', isset($id)?$id:'')}}">
        <input type="hidden" id="form_type" value="cheque_mangment">
    @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{$code}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row mt-4">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Entry Date:<span class="required">*</span></label>
                                <div class="col-lg-8">
                                    <div class="input-group date">
                                        <input type="text" name="cheque_entry_date" class="moveIndex form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" autofocus/>
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
                                <label class="col-lg-4 erp-col-form-label">Notify On:<span class="required">*</span></label>
                                <div class="col-lg-8">
                                    <div class="input-group date">
                                        <input type="text" name="notify_on" class="moveIndex form-control erp-form-control-sm moveIndex kt_datepicker_above_day" readonly value="{{isset($notifyDate)?$notifyDate: date('d-m-Y' , strtotime('+1 days')) }}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                        <!-- <input type="text" name="notify_on" class="moveIndex form-control erp-form-control-sm moveIndex validNumber" value="{{isset($notifyBeforeDays)?$notifyBeforeDays:""}}" id="notify_on"/> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-3 erp-col-form-label">Notify To:<span class="required">*</span></label>
                                <div class="col-lg-9">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm moveIndex kt-select2 notify_to" id="notify_to" name="notify_to">
                                            <option value="">Select</option>
                                                @foreach($data['users'] as $user)
                                                    <option value="{{$user->id}}" @if(isset($notifyTo) && $notifyTo == $user->id) selected @endif>{{$user->name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
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
                                        $headings = ['Sr No','Cheque Type','Receive Date','Cheque Date','Cheque No','Account Code','Account Name',"Amount",
                                        'Status','Description'];
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
                                <table id="AccForm" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col" width="35px">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input readonly id="account_id" type="hidden" class="account_id form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Cheque Type</div>
                                            <div class="erp_form__grid_th_input">
                                                <select id="cheque_type" class="cheque_type form-control erp-form-control-sm">
                                                    <option value="issue">Issue</option>
                                                    <option value="receive">Receive</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Receive Date</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="cheque_receive_date" id="cheque_receive_date" type="text" value="{{ $date ?? '' }}" class="kt_datepicker_3 form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Cheque Date</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="cheque_date" type="text" class="kt_datepicker_3 form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Cheque No</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="cheque_no" type="text" class="form-control erp-form-control-sm">
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
                                            <div class="erp_form__grid_th_title">Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="cheque_amount" type="text" class="credit validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Status</div>
                                            <div class="erp_form__grid_th_input">
                                                <select id="cheque_status" class="cheque_status form-control erp-form-control-sm">
                                                    <option value="active">Active</option>
                                                    <option value="clear">Clear</option>
                                                    <option value="cancel">Cancel</option>
                                                    <option value="return">Return</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Narration</div>
                                            <div class="erp_form__grid_th_input">
                                                <input  id="voucher_descrip" type="text" class="tb_moveIndex form-control erp-form-control-sm">
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
                                            <tr>
                                                <td class="handle">
                                                    <i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{ $loop->iteration }}" name="pd[{{ $loop->iteration }}][sr_no]" title="{{ $loop->iteration }}" class="form-control erp-form-control-sm handle" readonly="" autocomplete="off" aria-invalid="false">
                                                    <input type="hidden" name="pd[{{ $loop->iteration }}][account_id]" data-id="account_id" value="{{ $data->accounts->chart_account_id }}" class="account_id form-control erp-form-control-sm" readonly="" autocomplete="off">
                                                </td>
                                                <td>
                                                    <div class="erp-select2">
                                                        <select name="pd[{{ $loop->iteration }}][cheque_type]" data-id="cheque_type" class="tb_moveIndex form-control erp-form-control-sm" aria-invalid="false">
                                                            <option value="issue" @if(isset($data->cheque_managment_dtl_type) && $data->cheque_managment_dtl_type == 'issue') selected @endif>Issue</option>
                                                            <option value="receive" @if(isset($data->cheque_managment_dtl_type) && $data->cheque_managment_dtl_type == 'receive') selected @endif>Receive</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="">
                                                    <input type="text" name="pd[{{ $loop->iteration }}][cheque_receive_date]" data-id="cheque_receive_date" value="{{ date('d-m-Y', strtotime(trim(str_replace('/','-',$data->receive_date)))) }}" title="{{ date('d-m-Y', strtotime(trim(str_replace('/','-',$data->receive_date)))) }}" class="form-control erp-form-control-sm tb_moveIndex kt_datepicker_3" autocomplete="off">
                                                </td>
                                                <td class="">
                                                    <input type="text" name="pd[{{ $loop->iteration }}][cheque_date]" data-id="cheque_date" value="{{ date('d-m-Y', strtotime(trim(str_replace('/','-',$data->cheque_date)))) }}" title="{{ date('d-m-Y', strtotime(trim(str_replace('/','-',$data->cheque_date)))) }}" class="form-control erp-form-control-sm tb_moveIndex kt_datepicker_3" autocomplete="off">
                                                </td>
                                                <td class="">
                                                    <input type="text" name="pd[{{ $loop->iteration }}][cheque_no]" data-id="cheque_no" value="{{ $data->cheque_no }}" title="{{ $data->cheque_no }}" class="form-control erp-form-control-sm tb_moveIndex" autocomplete="off">
                                                </td>
                                                <td class="">
                                                    <input type="text" name="pd[{{ $loop->iteration }}][account_code]" data-id="account_code" value="{{ $data->accounts->chart_code }}" title="{{ $data->accounts->chart_code }}" class="form-control erp-form-control-sm acc_code open_inline__help" autocomplete="off">
                                                </td>
                                                <td class="">
                                                    <input type="text" name="pd[{{ $loop->iteration }}][account_name]" data-id="account_name" value="{{ $data->accounts->chart_name }}" title="{{ $data->accounts->chart_name }}" class="form-control erp-form-control-sm acc_name" readonly="" autocomplete="off">
                                                </td>
                                                <td class="">
                                                    <input type="text" name="pd[{{ $loop->iteration }}][cheque_amount]" data-id="cheque_amount" value="{{ $data->amount }}" title="{{ $data->amount }}" class="form-control erp-form-control-sm tb_moveIndex validNumber" autocomplete="off">
                                                </td>
                                                <td>
                                                    <div class="erp-select2">
                                                        <select name="pd[{{ $loop->iteration }}][cheque_status]" data-id="cheque_status" class="tb_moveIndex form-control erp-form-control-sm">
                                                            <option value="active" @if(isset($data->cheque_status) && $data->cheque_status == 'active') selected @endif>Active</option>
                                                            <option value="clear" @if(isset($data->cheque_status) && $data->cheque_status == 'clear') selected @endif>Clear</option>
                                                            <option value="cancel" @if(isset($data->cheque_status) && $data->cheque_status == 'cancel') selected @endif>Cancel</option>
                                                            <option value="return" @if(isset($data->cheque_status) && $data->cheque_status == 'return') selected @endif>Return</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="">
                                                    <input type="text" name="pd[{{ $loop->iteration }}][voucher_descrip]" data-id="voucher_descrip" value="{{ $data->cheque_description }}" title="{{ $data->cheque_description }}" class="form-control erp-form-control-sm tb_moveIndex" autocomplete="off">
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn delData">
                                                            <i class="la la-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-block row">
                        <label class="col-lg-12 erp-col-form-label">Remarks:</label>
                        <div class="col-lg-12">
                            <textarea type="text" rows="2" id="voucher_notes" name="voucher_notes" class="form-control erp-form-control-sm">{{isset($notes)?$notes:''}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!--end::Form-->
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
        var invoiceHelp = "{{url('/common/inline-help/invoiceHelp')}}";

        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id' : 'cheque_type',
                'fieldClass':'tb_moveIndex',
                'type': 'select'
            },
            {
                'id' : 'cheque_receive_date',
                'fieldClass':'tb_moveIndex kt_datepicker_3',
                'require':true,
            },
            {
                'id': 'cheque_date',
                'fieldClass':'tb_moveIndex kt_datepicker_3',
                'require':true,
            },
            {
                'id': 'cheque_no',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'account_code',
                'fieldClass':'acc_code open_inline__help',
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
                'id':'cheque_amount',
                'fieldClass':'tb_moveIndex validNumber',
                'require':true,
            },
            {
                'id':'cheque_status',
                'fieldClass':'tb_moveIndex',
                'type': 'select'
            },
            {
                'id':'voucher_descrip',
                'fieldClass':'tb_moveIndex'
            }
        ];
        var arr_hidden_field = ['account_id'];
    </script>
    {{---<script src="{{ asset('js/pages/js/account-row-repeated.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection

