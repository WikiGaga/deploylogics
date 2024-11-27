<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-9"></div>
            <div class="col-lg-3">
                <div class="row">
                    <label for="" class="erp-col-form-label col-lg-6">Selected Val</label>
                    <div class="col-lg-6">
                        <div class="input-group">
                            <input readonly type="text" class="selected_ref_code form-control erp-form-control-sm readonly">
                            <div class="input-group-prepend">
                                <span class="input-group-text btn-ref-code-remove">
                                    <i class="la la-minus-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group-block">
            <div class="erp_form___block">
                <div class="table-scroll form_input__block">
                    <table id="grnVoucherReturn" data-prefix="invr" class="table erp_form__grid  dtr-inline {{$on_account_voucher == 1?" pointerEventsNone":""}}">
                        <thead class="erp_form__grid_header">
                        <tr>
                            <th scope="col" width="75px">
                                <div class="erp_form__grid_th_title">Sr.</div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Branch</div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Invoice No.</div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Invoice Date</div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">Invoice Amount</div>
                            </th>
                            <th scope="col">
                                <div class="erp_form__grid_th_title">GRN Code.</div>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="erp_form__grid_body">
                        @if($on_account_voucher == 0)
                            @foreach($voucher_purchase_return_bills as $bill)
                                @php
                                    $grn_type = "";
                                    if(empty($bill->voucher_type)){
                                        $grn = \App\Models\TblPurcGrn::where('grn_id',$bill->voucher_document_id)->first();
                                        $grn_type = $grn->grn_type;
                                    }else{
                                        $grn_type = $bill->voucher_type;
                                    }
                                @endphp
                                @if(strtolower($grn_type) == 'pr')
                                @php
                                    $bi = $loop->iteration;
                                    $bra = \App\Models\TblSoftBranch::where('branch_id',$bill->branch_id)->first();
                                    $grn = \App\Models\TblPurcGrn::where('grn_id',$bill->document_reference_id)->first();
                                @endphp
                                <tr>
                                    <td>
                                        <input readonly type="text" value="{{$bi}}" name="invr[{{$bi}}][sr_no]" data-id="sr_no" class="form-control erp-form-control-sm">
                                        <input readonly type="hidden" value="{{$bill->branch_id}}" name="invr[{{$bi}}][branch_id]" data-id="branch_id" class="form-control erp-form-control-sm">
                                        <input readonly type="hidden" value="{{$bill->voucher_document_id}}" name="invr[{{$bi}}][grn_id]" data-id="grn_id" class="form-control erp-form-control-sm">
                                        <input readonly type="hidden" value="{{$grn_type}}" name="invr[{{$bi}}][grn_type]" data-id="grn_type" class="form-control erp-form-control-sm">
                                    </td>
                                    <td>
                                        <input readonly type="text" value="{{$bra->branch_name}}" name="invr[{{$bi}}][branch_name]" data-id="branch_name" class="form-control erp-form-control-sm">
                                    </td>
                                    <td>
                                        <input readonly type="text" value="{{$bill->voucher_document_code}}" name="invr[{{$bi}}][grn_code]" data-id="grn_code" class="form-control erp-form-control-sm">
                                    </td>
                                    <td>
                                        @php $voucher_document_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$bill->voucher_document_date)))); @endphp
                                        <input readonly type="text" value="{{$voucher_document_date}}" name="invr[{{$bi}}][grn_date]" data-id="grn_date" class="form-control erp-form-control-sm">
                                    </td>
                                    <td>
                                        <input readonly type="text" value="{{number_format(abs($bill->voucher_bill_amount),3)}}" name="invr[{{$bi}}][grn_total_net_amount]" data-id="grn_total_net_amount" class="grn_total_net_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input readonly type="text" value="{{isset($grn->grn_code)?$grn->grn_code:""}}" name="invr[{{$bi}}][ref_code]" data-id="ref_code" class="ref_code form-control erp-form-control-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text btn-ref-code-selected">
                                                    <i class="la la-refresh"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
