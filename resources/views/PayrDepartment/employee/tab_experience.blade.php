<div class="row">
    <div class="col-lg-12 text-right">
        <div class="data_entry_header">
            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
            <div class="dropdown dropdown-inline">
                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                    <i class="flaticon-more" style="color: #666666;"></i>
                </button>
                @php
                    $headings = ['Sr No','Company Name','Field Name','Experience In Years'];
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
            <table class="table erp_form__grid" prefix="exp">
                <thead class="erp_form__grid_header">
                <tr>
                    <th scope="col" class="col-div-5">
                        <div class="erp_form__grid_th_title">Sr.</div>
                        <div class="erp_form__grid_th_input">
                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                            <input id="employee_exp_sr_no" readonly type="hidden" class="employee_exp_sr_no form-control erp-form-control-sm handle">
                        </div>
                    </th>
                    <th scope="col" class="col-div-5">
                        <div class="erp_form__grid_th_title">Company Name</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_exp_company_name" type="text" class="employee_exp_company_name tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-5">
                        <div class="erp_form__grid_th_title">Field Name</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_exp_field_name" type="text" class="employee_exp_field_name tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-5">
                        <div class="erp_form__grid_th_title">Experience In Years</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_exp_experience_years" type="text" class="employee_exp_experience_years validNumber tb_moveIndex form-control erp-form-control-sm">
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
                @if(isset($data['current']->experience))
                    @foreach($data['current']->experience as $experience)
                        <tr>
                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                <input type="text" value="{{$experience->employee_experience_sr_no}}" name="exp[{{ $loop->iteration }}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                <input type="hidden" name="exp[{{ $loop->iteration }}][employee_experience_id]" data-id="employee_experience_id" value="{{$experience->employee_experience_id}}" class="employee_experience_id form-control erp-form-control-sm handle" readonly>
                            </td>
                            <td>
                                <input type="text" name="exp[{{ $loop->iteration }}][employee_exp_company_name]" data-id="employee_exp_company_name" data-url="" value="{{ $experience->company_name }}" title="{{ $experience->company_name }}" class="form-control erp-form-control-sm employee_exp_company_name tb_moveIndex" autocomplete="off">
                            </td>
                            <td>
                                <input type="text" name="exp[{{ $loop->iteration }}][employee_exp_field_name]" data-id="employee_exp_field_name" data-url="" value="{{ $experience->field_name }}" title="{{ $experience->field_name }}" class="form-control erp-form-control-sm employee_exp_field_name tb_moveIndex" autocomplete="off" aria-invalid="false">
                            </td>
                            <td>
                                <input type="text" name="exp[{{ $loop->iteration }}][employee_exp_experience_years]" data-id="employee_exp_experience_years" data-url="" value="{{ $experience->experience_in_year }}" title="{{ $experience->experience_in_year }}" class="form-control erp-form-control-sm employee_exp_experience_years tb_moveIndex validNumber" autocomplete="off">
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
