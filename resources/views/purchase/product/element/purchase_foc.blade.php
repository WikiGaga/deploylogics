<div class="form-group-block">
    <div class="erp_form___block">
        <div class="table-scroll form_input__block">
            <table data-prefix="foc" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                <thead class="erp_form__grid_header">
                <tr>
                    <th scope="col" width="5%">
                        <div class="erp_form__grid_th_title">Sr.</div>
                        <div class="erp_form__grid_th_input">
                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm text-center">
                            <input id="supplier_id" readonly type="hidden" class="supplier_id form-control erp-form-control-sm" data-require="true" data-msg="Supplier is required ">
                            <input id="supplier_branch_id" readonly type="hidden" class="supplier_branch_id form-control erp-form-control-sm" data-require="true" data-msg="Branch is required ">
                        </div>
                    </th>
                    <th scope="col" width="30%">
                        <div class="erp_form__grid_th_title">
                            Branch
                        </div>
                        <div class="erp_form__grid_th_input">
                            <select name="supplier_branch_name" id="supplier_branch_name" class="supplier_branch_name form-control erp-form-control-sm">
                                <option value="">Select</option>
                                @foreach($data['branch'] as $supplier_branch)
                                    <option value="{{$supplier_branch->branch_id}}">{{$supplier_branch->branch_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" width="30%">
                        <div class="erp_form__grid_th_title">
                            Supplier
                            <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                <i class="la la-building"></i>
                            </button>
                        </div>
                        <div class="erp_form__grid_th_input">
                            <input id="supplier_name" type="text" class="supplier_name tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" data-require="true" data-readonly="true" data-help="supplier_grid">
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
                    @if(isset($data['product_foc']))
                        @foreach($data['product_foc'] as $product_foc)
                            <tr>
                                <td>
                                    <input value="{{$loop->iteration}}" readonly type="text" class="sr_no form-control erp-form-control-sm text-center" autocomplete="off" name="foc[{{$loop->iteration}}][sr_no]" data-id="sr_no">
                                    <input readonly type="hidden" class="supplier_id form-control erp-form-control-sm" autocomplete="off" value="{{$product_foc['supplier_id']}}" name="foc[{{$loop->iteration}}][supplier_id]" data-id="supplier_id">
                                    <input readonly type="hidden" class="supplier_branch_id form-control erp-form-control-sm" autocomplete="off" value="{{$product_foc['branch_id']}}" name="foc[{{$loop->iteration}}][supplier_branch_id]" data-id="supplier_branch_id">
                                </td>
                                <td>
                                    <select name="supplier_branch_name" id="supplier_branch_name" class="supplier_branch_name form-control erp-form-control-sm">
                                        <option value="">Select</option>
                                        @foreach($data['branch'] as $supplier_branch)
                                            <option value="{{$supplier_branch->branch_id}}" {{ $product_foc['branch_id']==$supplier_branch->branch_id ? "selected" : "" }} >{{$supplier_branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="supplier_name tb_moveIndex open_inline__help form-control erp-form-control-sm" autocomplete="off" name="foc[{{$loop->iteration}}][supplier_name]" value="{{$product_foc->supplier->supplier_name}}" data-id="supplier_name" readonly>
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
</div>
