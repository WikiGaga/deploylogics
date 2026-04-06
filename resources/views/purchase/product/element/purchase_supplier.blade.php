<div class="form-group-block">
    <div class="erp_form___block">
        <div class="table-scroll form_input__block">
            <table data-prefix="foc" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                <thead class="erp_form__grid_header">
                <tr>
                    <th scope="col" width="5%">
                        <div class="erp_form__grid_th_title">{{ __('message.sr') }}</div>
                    </th>
                    <th scope="col" width="30%">
                        <div class="erp_form__grid_th_title">
                            {{ __('message.branch') }}
                        </div>
                    </th>
                    <th scope="col" width="30%">
                        <div class="erp_form__grid_th_title">
                            {{ __('message.supplier') }}
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody class="erp_form__grid_body">
                    @if(isset($data['branch']))
                        @foreach($data['branch'] as $branch)
                            @php
                                $product_supplier = $data['purchase_supplier']->where('branch_id',$branch->branch_id)->first() ?? [];
                            @endphp
                            <tr>
                                <td>
                                    <input value="{{$loop->iteration}}" readonly type="text" class="sr_no form-control erp-form-control-sm text-center" autocomplete="off" name="purchase_supplier[{{$loop->iteration}}][sr_no]" data-id="sr_no">
                                    <input readonly type="hidden" class="supplier_branch_id form-control erp-form-control-sm" autocomplete="off" value="{{$branch['branch_id']}}" name="purchase_supplier[{{$loop->iteration}}][branch_id]" data-id="supplier_branch_id">
                                </td>
                                <td>
                                    <input readonly type="text" class="supplier_branch_name form-control erp-form-control-sm" autocomplete="off" value="{{$branch['branch_name']}}" name="purchase_supplier[{{$loop->iteration}}][branch_name]" data-id="supplier_branch_name">
                                </td>
                                <td>
                                    <select name="purchase_supplier[{{$loop->iteration}}][supplier_id]" id="supplier_id" class="supplier_id form-control erp-form-control-sm">
                                        <option value="">{{ __('message.select') }}</option>
                                        @foreach($data['suppliers'] as $suppliers)
                                            <option value="{{$suppliers->supplier_id}}" {{ ($product_supplier['supplier_id'] ?? -1) == $suppliers->supplier_id ? "selected" : "" }} >{{$suppliers->supplier_name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
