@if(isset($data['current']->coupon_dtl))
    <div data-repeater-list="coupon_data" class="col-lg-12">
        @foreach($data['current']->coupon_dtl as $couponData)
        <div data-repeater-item class="kt-margin-b-10 coupon-container border p-3 repeater-container" item-id="{{ $loop->index }}">
            <div class="form-group-block row">
                <div class="col-lg-5">
                    <div class="row">
                        <label class="col-lg-4 erp-col-form-label">Coupon Qty: <span class="required">*</span></label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <input type="text" name="coupon_qty" value="{{ $couponData->coupon_qty }}" class="coupon_qty moveIndex form-control erp-form-control-sm noEmpty" autocomplete="off" aria-describedby="scheme_name-error" aria-invalid="false"><div id="scheme_name-error" class="error invalid-feedback" style="display: block;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row">
                        <label class="col-lg-4 erp-col-form-label">Coupon Value: <span class="required">*</span></label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <input type="text" name="coupon_value" value="{{ $couponData->coupon_value }}" class="coupon_value moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm noEmpty" autocomplete="off" aria-describedby="scheme_name-error" aria-invalid="false"><div id="scheme_name-error" class="error invalid-feedback" style="display: block;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <a href="javascript:;" data-repeater-delete="" class="btn btn-danger btn-icon btn-sm">
                                <i class="la la-remove"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group-block row">
                <div class="col-lg-5">
                    <div class="row">
                        <label class="col-lg-4 erp-col-form-label">Validity: <span class="required">*</span></label>
                        <div class="col-lg-8">
                            <div class='input-group' id='kt_daterangepicker_3'>
                                <input type='text' name="coupon_validity" value="{{ $couponData->validity_date }}" class="kt_daterangepicker_3 moveIndex form-control erp-form-control-sm c-date-p noEmpty" readonly  placeholder="Select date range"/>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif