<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__body">
        <style>
            .list-radio-filter>.kt-radio{
                margin-right: 5px !important;
                padding-left: 20px !important;
            }
        </style>
        <form method="get" name="getRecordsByDateFilter">
            <div class="row">
                <div class="col-md-10">
                    <div class="kt-radio-inline list-radio-filter d-flex justify-content-between">
                        <label style="background: #e4e4e4;">
                            <span class="input-group-text time_block" style="padding: 0px 5px 0px 5px;" >
                                <input type="checkbox" class="time_checkbox">
                                <i class="la la-clock-o" style="font-size: 20px;"></i>
                            </span>
                            <div class="input-timerange" style="width: 160px; display: none; position: absolute;background: #ffb822;padding: 6px;z-index: 99;">
                                <input type="text" class="form-control erp-form-control-sm kt_timepicker_1" name="time_from" style="width:60px;height: 21px;display: inline-block;"/>
                                <div style="display: inline-block;" > TO </div>
                                <input type="text" class="form-control erp-form-control-sm kt_timepicker_2" name="time_to" style="width:60px;height: 21px;display: inline-block;" />
                            </div>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <input type="radio" name="radioDate" value="all"> All
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <input type="radio" name="radioDate" value="today" checked> Today
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <input type="radio" name="radioDate" value="yesterday"> Yesterday
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <input type="radio" name="radioDate" value="last_7_days"> Last 7 Days
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <input type="radio" name="radioDate" value="last_30_days"> Last 30 Days
                            <span></span>
                        </label>
                        <label style="background: #e4e4e4;">
                            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" style="margin-right: 5px;">
                                <input type="radio" name="radioDate" value="custom_date"> Custom
                                <span></span>
                            </label>
                            <div class="input-daterange" id="kt_datepicker_5" style="width: 200px;display: inline-block;">
                                <input type="text" class="form-control erp-form-control-sm" name="from" style="width:85px;height: 21px;display: inline-block;"/>
                                <div style="display: inline-block;" > TO </div>
                                <input type="text" class="form-control erp-form-control-sm" name="to" style="width:85px;height: 21px;display: inline-block;" />
                            </div>
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
                        <button type="submit" class="btn btn-sm btn-warning" id="getRecordsByDateFilter">Get Data</button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="window.location.href=window.location.href">Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@section('customJS_date_filter')
    <script>
        var arrows;
        if (KTUtil.isRTL()) {
            arrows = {
                leftArrow: '<i class="la la-angle-right"></i>',
                rightArrow: '<i class="la la-angle-left"></i>'
            }
        } else {
            arrows = {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        }
        $(document).find('#kt_datepicker_5').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            templates: arrows
        });
        $(document).find('.kt_datepicker_6').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            templates: arrows
        });

        $(document).find('.kt_timepicker_1').timepicker({
            minuteStep: 1,
            defaultTime: '0:00:00',
            showSeconds: true,
            showMeridian: false,
            snapToStep: true
        });
        $(document).find('.kt_timepicker_2').timepicker({
            minuteStep: 1,
            defaultTime: '23:59:59',
            showSeconds: true,
            showMeridian: false,
            snapToStep: true
        });
        $(document).on('click','.time_checkbox',function(){
            $(document).find('.input-timerange').toggle();
        })
    </script>
@endsection
