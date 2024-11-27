@php
    if($case == 'new'){

    }
    if($case == 'edit'){

    }
@endphp

<div class="row">
    <div class="col-lg-12 text-right">
        <div class="data_entry_header">
            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
            <div class="dropdown dropdown-inline">
                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                    <i class="flaticon-more" style="color: #666666;"></i>
                </button>
                @php
                    $headings = ['Sr No','Select Bank','A/C No','A/C Title'];
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
        </div>
    </div>
</div>
<div class="form-group-block">
    <div class="erp_form___block">
        <div class="table-scroll form_input__block">
            <table class="table erp_form__grid" prefix="bank">
                <thead class="erp_form__grid_header">
                <tr>
                    <th scope="col" class="col-div-5">
                        <div class="erp_form__grid_th_title">Sr.</div>
                        <div class="erp_form__grid_th_input">
                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                            <input id="employee_bank_sr_no" readonly type="hidden" class="employee_bank_sr_no form-control erp-form-control-sm handle">
                        </div>
                    </th>
                    <th scope="col" class="col-div-5">
                        <div class="erp_form__grid_th_title">Select Bank</div>
                        <div class="erp-select2 erp_form__grid_th_input">
                            <select class="form-control erp-form-control-sm" name="bank_id" id="employee_bank_bank_id">
                                <option value="0">Select</option>
                                @foreach($data['bank'] as $bank)
                                    <option value="{{$bank->bank_id}}">{{ucwords(strtolower($bank->bank_name))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" class="col-div-5">
                        <div class="erp_form__grid_th_title">A/C No.</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_bank_account_no" type="text" class="employee_bank_account_no validNumber tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-5">
                        <div class="erp_form__grid_th_title">A/C Title</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_bank_account_title" type="text" class="employee_bank_account_title tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-5">
                        <div class="erp_form__grid_th_title">Action</div>
                        <div class="erp_form__grid_th_btn">
                            <button type="button" id="addData" class="addData tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                <i class="la la-plus"></i>
                            </button>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody class="erp_form__grid_body">
                @if(isset($data['current']->bank))
                    @foreach($data['current']->bank as $bank)
                        <tr>
                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                <input type="text" value="{{$bank->employee_bank_sr_no}}" name="bank[{{$bank->employee_bank_sr_no}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                <input type="hidden" name="bank[{{$bank->employee_bank_sr_no}}][employee_bank_id]" data-id="employee_bank_id" value="{{$bank->employee_bank_id}}" class="employee_bank_id form-control erp-form-control-sm handle" readonly>
                            </td>
                            <td>
                            <div class="erp-select2">
                                <select class="employee_bank_bank_id tb_moveIndex form-control erp-form-control-sm" name="bank[{{ $loop->iteration }}][employee_bank_bank_id]">
                                <option value="0">Select</option>
                                @foreach($data['bank'] as $b)
                                    @php $bank_id = isset($b->bank_id) ? $b->bank_id : 0 ;  @endphp
                                    <option value="{{$b->bank_id}}" {{ $bank->chart_bank_id == $bank_id ? 'selected' : '' }}>{{ucwords(strtolower($b->bank_name))}}</option>
                                @endforeach
                                </select>
                            </div>
                            </td>
                            <td>
                                <input type="text" name="bank[{{ $loop->iteration }}][employee_bank_account_no]" data-id="employee_bank_account_no" data-url="" value="{{ $bank->account_no }}" class="form-control erp-form-control-sm employee_bank_account_no tb_moveIndex validNumber" autocomplete="off">
                            </td>
                            <td>
                                <input type="text" name="bank[{{ $loop->iteration }}][employee_bank_account_title]" data-id="employee_bank_account_title" data-url="" value="{{ $bank->account_title }}" class="form-control erp-form-control-sm employee_bank_account_title tb_moveIndex" autocomplete="off">
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
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
