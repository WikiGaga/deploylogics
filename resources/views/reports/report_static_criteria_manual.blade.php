{{-- report wise criteria --}}
@if(in_array($data['case_name'],['supplier_wise_purchase_summary','daily_purchase','purchase_register','invoice_wise_purchase_summary']))
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Select Type:</label>
        </div>
        <div class="col-lg-6">
            <div class="erp-select2">
                <select class="form-control kt-select2 erp-form-control-sm" id="specific_purchase_typespecific_purchase_type" name="specific_purchase_type">
                    <option value="all">All</option>
                    <option value="grn" selected>Purchase Invoice (GRN)</option>
                    <option value="pr">Purchase Return (PR)</option>
                </select>
            </div>
        </div>
    </div>
@endif
@if($data['case_name'] == 'product_activity')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Month Wise:</label>
        </div>
        <div class="col-lg-6">
            <div class="kt-checkbox-inline">
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                    <input type="checkbox" name="month_wise">
                    <span></span>
                </label>
            </div>
        </div>
    </div>
@endif
@if($data['case_name'] == 'vouchers_list')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Hide:</label>
        </div>
        <div class="col-lg-6">
            <div class="kt-checkbox-inline">
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                    <input type="checkbox" name="hide_total"> Total
                    <span></span>
                </label>
            </div>
        </div>
    </div>
@endif
@if($data['case_name'] == 'combine_ledger_group_wise')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <div class="row">
                <div class="col-lg-6">
                    <label class="erp-col-form-label">Opening Balance:</label>
                </div>
                <div class="col-lg-6">
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                            <input type="checkbox" name="accounting_ledger_ob_toggle" checked>
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if($data['case_name'] == 'slow_moving_stock')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Select Movement:</label>
        </div>
        <div class="col-lg-6">
            <div class="erp-select2">
                <select class="form-control kt-select2 erp-form-control-sm" name="reorder_order">
                    <option value="ASC">Slow Moving</option>
                    <option value="DESC">Fast Moving</option>
                </select>
            </div>
        </div>
    </div>
    <div class="alert alert-warning">
        You can Take Report Between the Period of &nbsp;<b>15-April-2021</b>&nbsp;To&nbsp;<b> 14-June-2022 </b>
    </div>
@endif
@if($data['case_name'] == 'temp_accounting_ledger')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Customer:</label>
        </div>
        <div class="col-lg-3">
            <div class="erp-select2">
                <select class="form-control erp-form-control-sm" name="customer_contain_selection">
                    <option value="not_contain">Does not Contain</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="erp-select2">
                <select class="form-control erp-form-control-sm kt_select2" multiple name="customer_not_contain[]">
                    @foreach($data['customers'] as $customer)
                        <option value="{{ $customer->customer_id }}" >{{ $customer->customer_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
@if($data['case_name'] == 'accounting_ledger' || $data['case_name'] == 'temp_accounting_ledger')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <div class="row">
                <div class="col-lg-6">
                    <label class="erp-col-form-label">Opening Balance:</label>
                </div>
                <div class="col-lg-6">
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                            <input type="checkbox" name="accounting_ledger_ob_toggle" checked>
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="row">
                <div class="col-lg-6">
                    <label class="erp-col-form-label">Dispatch Date:</label>
                </div>
                <div class="col-lg-6">
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                            <input type="checkbox" name="voucher_mode_date" >
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row form-group-block">
        <div class="col-lg-3">
            <div class="row">
                <div class="col-lg-6">
                    <label class="erp-col-form-label">Reference Account:</label>
                </div>
                <div class="col-lg-6">
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                            <input type="checkbox" name="al_ref_acc_toggle">
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="row">
                <div class="col-lg-6">
                    <label class="erp-col-form-label">Vat Amount:</label>
                </div>
                <div class="col-lg-6">
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                            <input type="checkbox" name="al_vat_amount_toggle" >
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if($data['case_name'] == 'inventory_batch_expiry')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Near Expiry Days:</label>
        </div>
        <div class="col-lg-3">
            <select name="near_expiry_days_filter_types" class="form-control form-control-sm" id="near_expiry_days_filter_types">
                <option value="">Select</option>
                <option value="=">is equal to</option>
                <option value="!=">is not equal to</option>
                <option value=">">greater than</option>
                <option value="<">less than</option>
                <option value=">=">greater than or equal to</option>
                <option value="<=">less than or equal to</option>
            </select>
        </div>
        <div class="col-lg-3">
            <input type="text" name="near_expiry_days" class="form-control form-control-sm validNumber ">
        </div>
    </div>
@endif
@if($data['case_name'] == 'inventory_checklist')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Stock Quantity:</label>
        </div>
        <div class="col-lg-3">
            <select name="stock_quantity_filter_types" class="form-control form-control-sm" id="stock_quantity_filter_types">
                <option value="">Select</option>
                <option value="=">is equal to</option>
                <option value="!=">is not equal to</option>
                <option value=">">greater than</option>
                <option value="<">less than</option>
                <option value=">=">greater than or equal to</option>
                <option value="<=">less than or equal to</option>
            </select>
        </div>
        <div class="col-lg-3">
            <input type="text" name="stock_quantity_filter_types_val" class="form-control form-control-sm validNumber ">
        </div>
    </div>
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Stock Value:</label>
        </div>
        <div class="col-lg-3">
            <select name="stock_value_filter_types" class="form-control form-control-sm" id="stock_value_filter_types">
                <option value="">Select</option>
                <option value="=">is equal to</option>
                <option value="!=">is not equal to</option>
                <option value=">">greater than</option>
                <option value="<">less than</option>
                <option value=">=">greater than or equal to</option>
                <option value="<=">less than or equal to</option>
            </select>
        </div>
        <div class="col-lg-3">
            <input type="text" name="stock_value_filter_types_val" class="form-control form-control-sm validNumber ">
        </div>
    </div>
@endif
@if($data['case_name'] == 'top_sale_qty_barcode_wise')
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Product Amount:</label>
        </div>
        <div class="col-lg-3">
            <select name="top_sale_qty_barcode_wise_product_amount" class="form-control form-control-sm" id="top_sale_qty_barcode_wise_product_amount">
                <option value="">Select</option>
                <option value="=">is equal to</option>
                <option value="!=">is not equal to</option>
                <option value=">">greater than</option>
                <option value="<">less than</option>
                <option value=">=">greater than or equal to</option>
                <option value="<=">less than or equal to</option>
            </select>
        </div>
        <div class="col-lg-3">
            <input type="text" name="top_sale_qty_barcode_wise_product_amount_val" class="form-control form-control-sm validNumber ">
        </div>
    </div>
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Product Qty:</label>
        </div>
        <div class="col-lg-3">
            <select name="top_sale_qty_barcode_wise_product_qty" class="form-control form-control-sm" id="top_sale_qty_barcode_wise_product_qty">
                <option value="">Select</option>
                <option value="=">is equal to</option>
                <option value="!=">is not equal to</option>
                <option value=">">greater than</option>
                <option value="<">less than</option>
                <option value=">=">greater than or equal to</option>
                <option value="<=">less than or equal to</option>
            </select>
        </div>
        <div class="col-lg-3">
            <input type="text" name="top_sale_qty_barcode_wise_product_qty_val" class="form-control form-control-sm validNumber ">
        </div>
    </div>
    <div class="row form-group-block">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Order By:</label>
        </div>
        <div class="col-lg-6">
            <select name="top_sale_qty_barcode_wise_orderby" class="form-control form-control-sm" id="top_sale_qty_barcode_wise_orderby">
                <option value="qty" selected>Qty</option>
                <option value="amount">Amount</option>
            </select>
        </div>
    </div>
@endif
