<div class="kt-portlet kt-portlet--mobile" style=" position: relative; top: -18px; margin-bottom: 0; ">
    <div class="kt-portlet__body">
        @if($data['case'] == 'product-discount-setup')
            <div class="row">
                <div class="col-lg-12">
                    <div class="kt-radio-inline">
                        <b>Status: </b>
                        <label class="kt-radio kt-radio--bold kt-radio--primary mb-0">
                            <input type="radio" name="pds_status" value="all"> All
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--primary mb-0">
                            <input type="radio" name="pds_status" value="in_active_expire"> In-Active Expire
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--primary mb-0">
                            <input type="radio" name="pds_status" value="in_active_valid"> In-Active Valid
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--primary mb-0">
                            <input type="radio" name="pds_status" value="active_expire"> Active Expire
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--primary mb-0">
                            <input type="radio" name="pds_status" value="active_valid"> Active Valid
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        @endif
        @if($data['case'] == 'pv' ||
            $data['case'] == 'cpv' ||
            $data['case'] == 'crv'||
            $data['case'] == 'pve'||
            $data['case'] == 'lv'||
            $data['case'] == 'jv'||
            $data['case'] == 'brpv'||
            $data['case'] == 'brrv'||
            $data['case'] == 'ipv'||
            $data['case'] == 'irv'||
            $data['case'] == 'rv'||
            $data['case'] == 'obv')
            <div class="row">
                <div class="col-lg-3">
                    <div class="kt-radio-inline">
                        <b>Voucher Date: </b>
                        <label style="background: #e4e4e4;">
                            <div class="input-daterange" style="width: 200px;display: inline-block;">
                                <input type="text" class="form-control erp-form-control-sm kt_datepicker_6" name="voucher_from" id="voucher_from" style="width:85px;height: 21px;display: inline-block;"/>
                                <div style="display: inline-block;" > TO: </div>
                                <input type="text" class="form-control erp-form-control-sm kt_datepicker_6" name="voucher_to" id="voucher_to" style="width:85px;height: 21px;display: inline-block;" />
                            </div>
                        </label>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="kt-radio-inline">
                        <b>Status: </b>
                        <label class="kt-radio kt-radio--bold kt-radio--primary mb-0">
                            <input type="radio" name="post_status" value="all"> All
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--primary mb-0">
                            <input type="radio" name="post_status" value="1"> Posted
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--primary mb-0">
                            <input type="radio" name="post_status" value="0"> Un-Posted
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
