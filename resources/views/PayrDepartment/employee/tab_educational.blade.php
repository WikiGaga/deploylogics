<div class="row">
    <div class="col-lg-12 text-right">
        <div class="data_entry_header">
            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
            <div class="dropdown dropdown-inline">
                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                    <i class="flaticon-more" style="color: #666666;"></i>
                </button>
                @php
                    $headings = ['Sr No','Degree Name','Marks','Grade','Subject Detail','Board Name','Passing Year'];
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
            <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline" prefix="edu">
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
                        <div class="erp_form__grid_th_title">Degree Name</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_educational_degree_name" type="text" class="employee_educational_degree_name tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Marks</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_educational_marks" type="text" class="employee_educational_marks validNumber tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Grade</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_educational_grade" type="text" class="employee_educational_grade tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Subject Detail</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_educational_subject_detail" type="text" class="employee_educational_subject_detail tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Board Name</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_educational_board_name" type="text" class="employee_educational_board_name tb_moveIndex form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Passing Year</div>
                        <div class="erp_form__grid_th_input">
                            <input id="employee_educational_passing_year" type="text" class="employee_educational_passing_year validNumber tb_moveIndex form-control erp-form-control-sm">
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
                @if(isset($data['current']->educational))
                    @foreach($data['current']->educational as $educational)
                        <tr>
                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                <input type="text" value="{{$educational->employee_educational_sr_no}}" name="edu[{{ $loop->iteration }}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                <input type="hidden" name="edu[{{ $loop->iteration }}][employee_educational_id]" data-id="employee_educational_id" value="{{$educational->employee_educational_id}}" class="employee_educational_id form-control erp-form-control-sm handle" readonly>
                            </td>
                            <td>
                                <input name="edu[{{ $loop->iteration }}][employee_educational_degree_name]" data-id="employee_educational_degree_name" type="text" value="{{$educational->employee_educational_degree_name}}" class="employee_educational_degree_name tb_moveIndex form-control erp-form-control-sm">
                            </td>
                            <td>
                                <input name="edu[{{ $loop->iteration }}][employee_educational_marks]" data-id="employee_educational_marks" type="text" value="{{$educational->employee_educational_marks}}" class="employee_educational_marks tb_moveIndex form-control erp-form-control-sm validNumber">
                            </td>
                            <td>
                                <input name="edu[{{ $loop->iteration }}][employee_educational_grade]" data-id="employee_educational_grade" type="text" value="{{$educational->employee_educational_grade}}" class="employee_educational_grade tb_moveIndex form-control erp-form-control-sm">
                            </td>
                            <td>
                                <input name="edu[{{ $loop->iteration }}][employee_educational_subject_detail]" data-id="employee_educational_subject_detail" type="text" value="{{$educational->employee_educational_subject_detail}}" class="employee_educational_subject_detail tb_moveIndex form-control erp-form-control-sm">
                            </td>
                            <td>
                                <input name="edu[{{ $loop->iteration }}][employee_educational_board_name]" data-id="employee_educational_board_name" type="text" value="{{$educational->employee_educational_board_name}}" class="employee_educational_board_name tb_moveIndex form-control erp-form-control-sm">
                            </td>
                            <td>
                                <input name="edu[{{ $loop->iteration }}][employee_educational_passing_year]" data-id="employee_educational_passing_year" type="text" value="{{$educational->employee_educational_passing_year}}" class="employee_educational_passing_year tb_moveIndex form-control erp-form-control-sm validNumber">
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
