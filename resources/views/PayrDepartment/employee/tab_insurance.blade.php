@php
    if($case == 'new'){
        $insurance_company_id = 0;
        $insurance_type_id = 0;
    }
    if($case == 'edit'){
        $insurance_company_id = 0;
        $insurance_type_id = 0;
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
                    $headings = ['Sr No','Insurance Company','Health Insurance Name','Insurance Rate For Foreign','Insurance Rate Settlement','Insurance Type','Insurance Start Date','Insurance End Date'];
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
            <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline" prefix="ins">
                <thead class="erp_form__grid_header">
                <tr>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Sr.</div>
                        <div class="erp_form__grid_th_input">
                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                            <input id="employee_educational_sr_no" readonly type="hidden" class="employee_educational_sr_no form-control erp-form-control-sm handle">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Insurance Company</div>
                        <div class="erp-select2 erp_form__grid_th_input">
                            <select class="form-control erp-form-control-sm" name="insurance_company_id" id="insurance_company_id">
                                <option value="0">Select</option>
                                    @foreach($data['insurance'] as $insurance)
                                        <option value="{{$insurance->insurance_company_id}}">{{ucfirst(strtolower($insurance->insurance_company_name))}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Health Insurance Name</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_insurance_health_name" type="text" class="employee_insurance_health_name tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Insurance Rate For Foreign</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_insurance_rate_for_foreign" type="text" class="employee_insurance_rate_for_foreign validNumber tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Insurance Rate Settlement</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_insurance_rate_settlement" type="text" class="employee_insurance_rate_settlement tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Insurance Type</div>
                        <div class="erp-select2 erp_form__grid_th_input">
                            <select class="form-control erp-form-control-sm" name="insurance_type_id" id="insurance_type_id">
                                <option value="0">Select</option>
                                @foreach($data['insurance_type'] as $insurance_type)
                                    <option value="{{$insurance_type->insurance_type_id}}" {{ $insurance_type->insurance_type_id == $insurance_type_id ? 'selected' : '' }}>{{ucfirst(strtolower($insurance_type->insurance_type_name))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Insurance Start Date</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_insurance_start_date" type="text" class="employee_insurance_start_date form-control erp-form-control-sm c-date-p kt_datepicker">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Insurance End Date</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_insurance_end_date" type="text" class="employee_insurance_end_date form-control erp-form-control-sm c-date-p kt_datepicker">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
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
                @if(isset($data['current']->insurance))
                    @foreach($data['current']->insurance as $insurance)
                        <tr>
                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                <input type="text" value="{{$insurance->employee_insurance_sr_no}}" name="ins[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                <input type="hidden" name="ins[{{ $loop->iteration }}][employee_insurance_id]" data-id="employee_insurance_id" value="{{$insurance->employee_insurance_id}}" class="employee_educational_id form-control erp-form-control-sm handle" readonly>
                            </td>
                            <td>
                                <div class="erp-select2">
                                    <select class="insurance_company_id tb_moveIndex form-control erp-form-control-sm" name="ins[{{$loop->iteration}}][insurance_company_id]">
                                        <option value="0">Select</option>
                                        @foreach($data['insurance'] as $insur)
                                            @php $insurance_company_id = isset($insur->insurance_company_id) ? $insur->insurance_company_id : 0 ; @endphp
                                            <option value="{{$insur->insurance_company_id}}" {{ $insurance->insurance_company_id == $insurance_company_id ? 'selected' : '' }}>{{ucfirst(strtolower($insur->insurance_company_name))}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="ins[{{ $loop->iteration }}][employee_insurance_health_name]" data-id="employee_insurance_health_name" data-url="" value="{{ $insurance->employee_insurance_health_name }}" title="{{ $insurance->employee_insurance_health_name }}" class="form-control erp-form-control-sm employee_insurance_health_name tb_moveIndex" autocomplete="off">
                            </td>
                            <td>
                                <input type="text" name="ins[{{ $loop->iteration }}][employee_insurance_rate_for_foreign]" data-id="employee_insurance_rate_for_foreign" data-url="" value="{{ $insurance->employee_insurance_rate_for_foreign }}" title="{{ $insurance->employee_insurance_rate_for_foreign }}" class="form-control erp-form-control-sm employee_insurance_rate_for_foreign tb_moveIndex validNumber" autocomplete="off">
                            </td>
                            <td>
                                <input type="text" name="ins[{{ $loop->iteration }}][employee_insurance_rate_settlement]" data-id="employee_insurance_rate_settlement" data-url="" value="{{ $insurance->employee_insurance_rate_settlement }}" title="{{ $insurance->employee_insurance_rate_settlement }}" class="form-control erp-form-control-sm employee_insurance_rate_settlement tb_moveIndex validNumber" autocomplete="off">
                            </td>
                            <td>
                                <div class="erp-select2">
                                    <select class="insurance_type_id tb_moveIndex form-control erp-form-control-sm" name="ins[1][insurance_type_id]">
                                        <option value="0">Select</option>
                                        @foreach($data['insurance_type'] as $insurance_type)
                                            @php $insurance_type_id = isset($insurance->insurance_type_id) ? $insurance->insurance_type_id : 0 ; @endphp
                                            <option value="{{$insurance_type->insurance_type_id}}" {{ $insurance_type->insurance_type_id == $insurance_type_id ? 'selected' : '' }}>{{ucfirst(strtolower($insurance_type->insurance_type_name))}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>   
                                @php $insurance_start_date = date('d-m-Y' , strtotime(trim(str_replace('/','-',$insurance->employee_insurance_start_date))));  @endphp
                                <input type="text" name="ins[{{ $loop->iteration }}][employee_insurance_start_date]" data-id="employee_insurance_start_date" data-url="" value="{{ $insurance_start_date }}" title="{{ $insurance_start_date }}" class="form-control erp-form-control-sm employee_insurance_start_date tb_moveIndex" autocomplete="off">
                            </td>
                            <td>
                                @php $insurance_end_date = date('d-m-Y' , strtotime(trim(str_replace('/','-',$insurance->employee_insurance_end_date))));  @endphp
                                <input type="text" name="ins[{{ $loop->iteration }}][employee_insurance_end_date]" data-id="employee_insurance_end_date" data-url="" value="{{ $insurance_end_date }}" title="{{ $insurance_end_date }}" class="form-control erp-form-control-sm employee_insurance_end_date tb_moveIndex" autocomplete="off">
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

