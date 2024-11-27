<div class="form-group-block">
    <div class="erp_form___block">
        <div class="table-scroll form_input__block">
            <table data-prefix="pd" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                <thead class="erp_form__grid_header">
                <tr>
                    <th scope="col" width="5%">
                        <div class="erp_form__grid_th_title">Sr.</div>
                        <div class="erp_form__grid_th_input">
                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                            <input id="country" readonly type="hidden" class="country form-control erp-form-control-sm">
                        </div>
                    </th>
                    <th scope="col" width="30%">
                        <div class="erp_form__grid_th_title">Country</div>
                        <div class="erp_form__grid_th_input">
                            <select id="product_life_country_name" class="product_life_country_name form-control erp-form-control-sm" data-readonly="true" data-convert="input">
                                <option value="">Select</option>
                                @foreach($data['country'] as $country)
                                    <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" width="30%">
                        <div class="erp_form__grid_th_title">Period Type</div>
                        <div class="erp_form__grid_th_input">
                            <select id="period_type" class="period_type form-control erp-form-control-sm" data-readonly="true" data-convert="input">
                                <option value="">Select</option>
                                @foreach($data['warranty_period'] as $wp)
                                    <option value="{{$wp->warrenty_period_name}}">{{$wp->warrenty_period_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" width="30%">
                        <div class="erp_form__grid_th_title">Period</div>
                        <div class="erp_form__grid_th_input">
                            <input id="period" type="text" class="period large_no validNumber validOnlyNumber form-control erp-form-control-sm">
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
                @if(isset($data['current']->product_life))
                    @foreach($data['current']->product_life as $pl)
                        <tr>
                            <td class="handle">
                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                <input type="hidden" name="pd[{{$loop->iteration}}][country]" data-id="country" value="{{$pl->country->country_id}}" class="country form-control erp-form-control-sm handle" readonly>
                            </td>
                            <td><input type="text" readonly name="pd[{{$loop->iteration}}][product_life_country_name]" data-id="product_life_country_name"  value="{{$pl->country->country_name}}" class="product_life_country_name form-control erp-form-control-sm" ></td>
                            <td><input type="text" readonly name="pd[{{$loop->iteration}}][period_type]" data-id="period_type"  value="{{$pl->product_life_period_type}}" class="period_type form-control erp-form-control-sm" ></td>
                            <td><input type="text" name="pd[{{$loop->iteration}}][period]" data-id="period"  value="{{$pl->product_life_period}}" class="period form-control erp-form-control-sm large_no validNumber validOnlyFloatNumber" ></td>
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
</div>
