<div class="tab-pane" id="aging" role="tabpanel">
    <div id="kt_repeater_3">
        <div class="form-group-block row">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Aging Days:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_no_of_days" class="form-control erp-form-control-sm medium_no validNumber" value="{{isset($no_of_days)?$no_of_days:""}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Mode of Payment:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_mode_of_payment" class="form-control erp-form-control-sm medium_no validNumber" value="{{isset($mode_of_payment)?$mode_of_payment:""}}">
                    </div>
                </div>
            </div>
        </div>{{-- end row--}}
        <div class="form-group-block row">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Credit Period:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_credit_period" class="form-control erp-form-control-sm medium_no validNumber" value="{{isset($credit_period)?$credit_period:""}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">NTN No:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_tax_no" class="form-control erp-form-control-sm short_text" value="{{isset($tax_no)?$tax_no:""}}">
                    </div>
                </div>
            </div>
        </div>{{-- end row--}}
        <div class="form-group-block row">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Credit Limit:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_credit_limit" class="form-control erp-form-control-sm medium_no validNumber" value="{{isset($credit_limit)?$credit_limit:""}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Debit Limit:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_debit_limit" class="form-control erp-form-control-sm medium_no validNumber" value="{{isset($debit_limit)?$debit_limit:""}}">
                    </div>
                </div>
            </div>
        </div>{{-- end row--}}
        <div class="form-group-block row">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Tax Rate:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_tax_rate" class="form-control erp-form-control-sm medium_no validNumber" value="{{isset($tax_rate)?$tax_rate:""}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Tax Status:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_tax_status" class="form-control erp-form-control-sm short_text" value="{{isset($tax_status)?$tax_status:""}}">
                    </div>
                </div>
            </div>
        </div>{{-- end row--}}
        <div class="form-group-block row">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Beneficiary Name:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_cheque_beneficry_name" class="form-control erp-form-control-sm small_text" value="{{isset($cheque_beneficry_name)?$cheque_beneficry_name:""}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">STRN No:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_strn_no" class="form-control erp-form-control-sm short_text" value="{{isset($strn_no)?$strn_no:""}}">
                    </div>
                </div>
            </div>
        </div>{{-- end row--}}
        <div class="form-group-block row">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Can Sale:</label>
                    <div class="col-lg-6">
                                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                <label>
                                                    @php $select_can_scale = isset($can_scale)?$can_scale:0; @endphp
                                                    <input type="checkbox" name="customer_can_scale" {{$select_can_scale == 1?"checked":""}}>
                                                    <span></span>
                                                </label>
                                            </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Apply Additional Tax:</label>
                    <div class="col-lg-6">
                                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                <label>
                                                    @php $select_additional_tax = isset($additional_tax)?$additional_tax:0; @endphp
                                                    <input type="checkbox" name="customer_additional_tax" {{$select_additional_tax == 1?"checked":""}}>
                                                    <span></span>
                                                </label>
                                            </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>{{--tabend--}}
<div class="tab-pane" id="bank" role="tabpanel">
    <div id="kt_repeater_3">
        <div class="form-group-block row">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Bank Name:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_bank_name" class="form-control erp-form-control-sm medium_text" value="{{isset($bank_name)?$bank_name:""}}">
                    </div>
                </div>
            </div>
        </div>{{-- end row--}}
        <div class="form-group-block row">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Account No:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_bank_account_no" class="form-control erp-form-control-sm small_text validNumber text-left" value="{{isset($bank_account_no)?$bank_account_no:""}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Account Title:</label>
                    <div class="col-lg-6">
                        <input type="text" name="customer_bank_account_title" class="form-control erp-form-control-sm medium_text" value="{{isset($bank_account_title)?$bank_account_title:""}}">
                    </div>
                </div>
            </div>
        </div>{{-- end row--}}
    </div>
</div>{{--tabend--}}
<div class="tab-pane" id="credit_limits" role="tabpanel">
    <div class="row">
        <label class="col-lg-2 erp-col-form-label">Select Items:</label>
        <div class="col-lg-8">
            <div class="erp-select2 form-group">
                <select class="form-control tag-select2 erp-form-control-sm" multiple id="selectItems" name="selectItems[]">
                    <option value="productHelp">Product</option>
                    <option value="groupItemsHelp">Group Items</option>
                </select>
            </div>
        </div>
        <div class="col-lg-2">
            <button type="button" id="selectItemsBtn" class="btn btn-success btn-sm">Go</button>
        </div>
    </div>
    <div class="form-group-block" style="overflow: auto;margin: 20px 0;">
        <table id="SalesAccForm" class="ErpFormsm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
            <thead>
            <tr>
                <th width="300px">Code</th>
                <th width="300px">Name</th>
                <th width="300px">type</th>
            </tr>
            </thead>
            <tbody id="repeated_datasm">
            </tbody>
        </table>
    </div>
    <div class="row">
        <label class="col-lg-2 erp-col-form-label">Debit Limit:</label>
        <div class="col-lg-4">
            <input type="text" name="credit_limit" class="form-control erp-form-control-sm">
        </div>
    </div>
</div>{{--tabend--}}
<div class="tab-pane" id="subcustomer" role="tabpanel">
    <div class="form-group-block row">
        <div class="col-lg-12">
            <div class="form-group-block">
                <div class="erp_form___block">
                    <div class="table-scroll form_input__block">
                        <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                            <thead class="erp_form__grid_header">
                            <tr id="erp_form_grid_header_row">
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Sr.</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                        <input readonly type="hidden" id="contactp_dtl_id" class="contactp_dtl_id form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">
                                        Name
                                    </div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="contactp_dtl_name" type="text" class="contactp_dtl_name tb_moveIndex open_inline__help form-control erp-form-control-sm required_field" data-url="{{action('Common\DataTableController@inlineHelpOpen','customerHelp')}}">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Contact</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="contactp_dtl_cont_no" type="text" class="contactp_dtl_cont_no validNumber text-left form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Address</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="contactp_dtl_address" type="text" class="contactp_dtl_address form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Action</div>
                                    <div class="erp_form__grid_th_btn">
                                        <button type="button" id="addData" data-type="person" data-prefix="pd" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                            <i class="la la-plus"></i>
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="erp_form__grid_body">
                            @if(isset($contact_persons))
                                @foreach($contact_persons as $person)
                                    <tr>
                                        <td class="handle">
                                            <i class="fa fa-arrows-alt-v handle"></i>
                                            <input type="text" value="{{ $loop->iteration }}" name="pd[{{ $loop->iteration }}][sr_no]" title="{{ $loop->iteration }}" class="form-control erp-form-control-sm handle" readonly>
                                            <input type="hidden" name="pd[{{ $loop->iteration }}][contactp_dtl_id]" data-id="contactp_dtl_id" value="{{ $person->customer_dtl_id }}" maxlength="100"  class="customer_dtl_id form-control erp-form-control-sm handle" readonly>
                                        </td>
                                        <td><input type="text" name="pd[{{ $loop->iteration }}][contactp_dtl_name]" data-id="contactp_dtl_name" value="{{ $person->customer_dtl_name }}" title="{{ $person->customer_dtl_name }}" class="form-control erp-form-control-sm moveIndex medium_text"></td>
                                        <td><input type="text" name="pd[{{ $loop->iteration }}][contactp_dtl_cont_no]" data-id="contactp_dtl_cont_no"  value="{{ $person->customer_dtl_cont_no }}" title="{{ $person->customer_det_contact_no }}" class="form-control erp-form-control-sm moveIndex mob_no validNumber text-left"></td>
                                        <td><input type="text" name="pd[{{ $loop->iteration }}][contactp_dtl_address]" data-id="contactp_dtl_address" value="{{ $person->customer_dtl_address }}" title="{{ $person->customer_det_address }}" class="form-control erp-form-control-sm double_text moveIndex"></td>
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
        </div>
    </div>{{-- end row--}}
</div>
<div class="tab-pane" id="subcustomers" role="tabpanel">
    <div class="form-group-block row">
        <div class="col-lg-12">
            <div class="form-group-block">
                <div class="erp_form___block">
                    <div class="table-scroll form_input__block">
                        <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                            <thead class="erp_form__grid_header">
                            <tr id="erp_form_grid_header_row">
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Sr.</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                        <input id="customer_id" readonly type="hidden" class="customer_id form-control erp-form-control-sm required_field">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">
                                        Customer
                                    </div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="customer_name" type="text" class="customer_name tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','customerHelp')}}">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Contact No</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="customer_contact" readonly type="text" class="cust_contact form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Address</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="customer_address" readonly type="text" class="cust_address form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Action</div>
                                    <div class="erp_form__grid_th_btn">
                                        <button type="button" id="addDataSubCustomer" data-type="subcustomer" data-prefix="subcustomer" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                            <i class="la la-plus"></i>
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="erp_form__grid_body" id="repeated_data_sub_customer">
                            @if(isset($sub_customers))
                                @foreach($sub_customers as $subcustomer)
                                    <tr>
                                        <td class="handle">
                                            <i class="fa fa-arrows-alt-v handle"></i>
                                            <input type="text" value="{{ $loop->iteration }}" name="subcustomer[{{ $loop->iteration }}][sr_no]" title="{{ $loop->iteration }}" class="form-control erp-form-control-sm handle" readonly>
                                            <input type="hidden" name="subcustomer[{{ $loop->iteration }}][customer_id]" data-id="customer_id" value="{{ $subcustomer->customer_id }}" maxlength="100"  class="customer_id form-control erp-form-control-sm handle" readonly>
                                        </td>
                                        <td><input type="text" name="subcustomer[{{ $loop->iteration }}][customer_name]" data-id="customer_name" value="{{ $subcustomer->customer->customer_name }}" title="{{ $subcustomer->customer->customer_name }}" class="customer_name form-control erp-form-control-sm moveIndex medium_text"></td>
                                        <td><input type="text" name="subcustomer[{{ $loop->iteration }}][customer_contact]" data-id="customer_contact"  value="{{ $subcustomer->customer->customer_contact }}" title="{{ $subcustomer->customer->customer_contact }}" class="customer_contact form-control erp-form-control-sm moveIndex mob_no validNumber text-left"></td>
                                        <td><input type="text" name="subcustomer[{{ $loop->iteration }}][customer_address]" data-id="customer_address" value="{{ $subcustomer->customer->customer_address }}" title="{{ $subcustomer->customer->customer_address }}" class="form-control erp-form-control-sm double_text moveIndex"></td>
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
        </div>
    </div>{{-- end row--}}
</div>
