<!-- Quick Panel -->
<div id="kt_quick_panel" class="kt-quick-panel kt-quick-panel-right pt-5 pb-10 px-3">     
        <!--begin::Header-->
        <div class="kt-quick-panel-header kt-quick-panel-header-navs d-flex align-items-center justify-content-between mb-2" kt-hidden-height="44" style="">
        </div>
        <!--end::Header-->
        <!--begin::Content-->
        <div class="kt-quick-panel-content px-10">
            <div class="row form-group-block">
                <div class="col-lg-6">
                    <h4>Filter Requests</h4>
                </div>
                <div class="col-lg-6">
                    <div class="pull-right">
                        <button type="button" class="btn btn-sm btn-success" id="kt_quick_panel_close_btn">Submit</button>
                        @if($case == 'edit')
                            <input type="hidden" name="form_code" value="{{ isset($code) ? $code : '' }}">
                        @endif
                    </div>
                </div>
            </div>
            <div class="row form-group-block">
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">Select Cities:</label>
                        <div class="col-lg-12">
                            <div class="erp-select2">
                                <select class="moveIndex form-control erp-form-control-sm kt-select2" id="filter_cities" name="filter_cities[]" multiple="multiple">
                                    @foreach($data['cities'] as $city)
                                        <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-group-block">
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">Select Areas:</label>
                        <div class="col-lg-12">
                            <div class="erp-select2">
                                <select class="moveIndex form-control erp-form-control-sm kt-select2" id="filter_areas" name="filter_areas[]" multiple="multiple">
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-group-block">
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">Job Schedule Status:</label>
                        <div class="col-lg-12">
                            <div class="erp-select2">
                                <select class="moveIndex form-control erp-form-control-sm kt-select2" id="filter_schedule" name="filter_schedule[]" multiple="multiple">
                                    <option value="1" selected>Scheduled</option>
                                    <option value="0">Not Scheduled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-group-block">
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">Requests Status:</label>
                        <div class="col-lg-12">
                            <div class="erp-select2">
                                <select class="moveIndex form-control erp-form-control-sm kt-select2" id="filter_status" name="filter_status[]" multiple="multiple">
                                    @foreach($data['order_status'] as $status)
                                        <option value="{{ $status->order_status_id }}">{{ $status->order_status_names }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
    <!-- End Quick Panel -->