<div data-repeater-list="scheme_slab_data" class="col-lg-12">
    <div data-repeater-item class="kt-margin-b-10 slab p-3 border repeater-container" item-id="0">
        <div class="form-group-block row">
            <div class="col-lg-12 text-right">
                <div class="row">
                    <div class="col-lg-6 text-left">
                        <h5>Slab Information</h5>
                    </div>
                    <div class="col-lg-6">
                        <a href="javascript:;" data-repeater-delete="" class="btn btn-danger btn-icon btn-sm">
                            <i class="la la-remove"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group-block row">
            <div class="col-lg-4">
                <div class="row">
                    <label class="col-lg-4 erp-col-form-label">Slab Name: <span class="required">*</span></label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="text" name="slab_name" value="" class="moveIndex form-control erp-form-control-sm noEmpty" autocomplete="off" aria-describedby="scheme_name-error" aria-invalid="false"><div id="scheme_name-error" class="error invalid-feedback" style="display: block;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="col-lg-4 erp-col-form-label">Min Sale: <span class="required">*</span></label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="text" name="slab_min_sale" value="" class="moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm noEmpty" autocomplete="off" aria-describedby="scheme_name-error" aria-invalid="false"><div id="scheme_name-error" class="error invalid-feedback" style="display: block;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="col-lg-4 erp-col-form-label">Max Sale: <span class="required">*</span></label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="text" name="slab_max_sale" value="" class="moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm noEmpty" autocomplete="off" aria-describedby="scheme_name-error" aria-invalid="false"><div id="scheme_name-error" class="error invalid-feedback" style="display: block;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group-block row">
            <div class="col-lg-4">
                <div class="row">
                    <label class="col-lg-4 erp-col-form-label">Disc%: <span class="required">*</span></label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="text" id="slab_disc_per" name="slab_disc_per" class="slab_disc_per moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm noEmpty" autocomplete="off" aria-describedby="scheme_name-error" aria-invalid="false"><div id="scheme_name-error" class="error invalid-feedback" style="display: block;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="col-lg-4 erp-col-form-label">Disc: <span class="required">*</span></label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="text" id="slab_disc" name="slab_disc" tabindex="-1" class="slab_disc validNumber validOnlyFloatNumber form-control erp-form-control-sm noEmpty" autocomplete="off" aria-describedby="scheme_name-error" aria-invalid="false"><div id="scheme_name-error" class="error invalid-feedback" style="display: block;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="col-lg-4 erp-col-form-label">Expiry Days: <span class="required">*</span></label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="text" name="slab_expiry_days" value="" class="moveIndex validNumber form-control erp-form-control-sm noEmpty" autocomplete="off" aria-describedby="scheme_name-error" aria-invalid="false"><div id="scheme_name-error" class="error invalid-feedback" style="display: block;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group-block row">
            <div class="col-lg-4">
                <div class="row">
                    <label class="col-lg-4 erp-col-form-label">Expiry Date: <span class="required">*</span></label>
                    <div class="col-lg-8">
                        <div class="input-group date">
                            <input type="text" name="slab_expiry_date" class="kt_datepicker_3 moveIndex form-control erp-form-control-sm c-date-p noEmpty" style="color:#495057;" value="{{ date('d-m-Y') }}" id="kt_datepicker_3" autocomplete="off" aria-invalid="false">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <label class="col-lg-6 erp-col-form-label">Generate Coupon:</label>
                    <div class="col-lg-6">
                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                            <label>
                                <input type="checkbox" name="generate_coupon" checked>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group-block row">
            <div class="col-lg-12 text-right">
                <div class="row">
                    <div class="col-lg-12 text-left">
                        <h5>Slab Detail</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group-block">
            <div class="erp_form___block">
                <div class="table-scroll form_input__block">
                    <table class="table erp_form__grid table-resizable dtr-inline product_table">
                        <thead class="erp_form__grid_header">
                            <tr id="erp_form_grid_header_row">
                                <th scope="col" style="width: 3%!important;">
                                    <div class="erp_form__grid_th_title">Sr.</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                        <input id="sldtl_product_id" readonly type="hidden" class="required_field product_id sldtl_product_id form-control erp-form-control-sm">
                                        <input id="sldtl_product_barcode_id" readonly type="hidden" class="required_field product_barcode_id sldtl_product_barcode_id form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">
                                        Type
                                    </div>
                                    <div class="erp_form__grid_th_input">
                                        <select id="sldtl_type" class="sldtl_type tb_moveIndex form-control erp-form-control-sm">
                                            <option value="Product">Product</option>
                                            <option value="Product Group" disabled>Product Group</option>
                                        </select>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">
                                        Barcode
                                        <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                            <i class="la la-barcode"></i>
                                        </button>
                                    </div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="sldtl_barcode" type="text" class="pd_barcode sldtl_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Product Name</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="sldtl_product_name" readonly type="text" class="required_field product_name sldtl_product_name form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Disc%</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="sldtl_disc_per" type="text" class="required_field sldtl_disc_per grid_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Disc</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="sldtl_disc" type="text" tabindex="-1" class="sldtl_disc grid_discount_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="erp_form__grid_th_title">Foc Qty</div>
                                    <div class="erp_form__grid_th_input">
                                        <input id="sldtl_foc_qty" type="text" class="sldtl_foc_qty grid_foc_qty tb_moveIndex validNumber form-control erp-form-control-sm">
                                    </div>
                                </th>
                                <th scope="col" width="48">
                                    <div class="erp_form__grid_th_title">Action</div>
                                    <div class="erp_form__grid_th_btn">
                                        <button type="button" data-type="slabdtl" data-prefix="sldtl" id="sldtlAddData" class="addData tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                            <i class="la la-plus"></i>
                                        </button>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="erp_form__grid_body slab_detail_grid_body">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>