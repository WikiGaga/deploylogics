@extends('layouts.template')
@section('title', 'Delivery Charges')

@section('pageCSS')
<style>
    .erp-select2 .select2-container--default .select2-selection--single{
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
</style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->delivery_charges_id;
            $delivery_type_id = $data['current']->delivery_type;
            $delivery_branch_id = $data['current']->charges_branch;
            $city_id = $data['current']->city_id; 
            $area_id = $data['current']->area_id; 
            $dtls = $data['current']->dtls;
        }
    @endphp
    @permission($data['permission'])
    <form id="delivery_type_form" class="master_form kt-form" method="post" action="{{ action('Setting\DeliveryChargesController@store', isset($id)?$id:"") }}">
    @csrf
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Delivery Type: <span class="required">*</span></label>
                                    <div class="col-lg-9">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm delivery_type" id="kt_select2_1" name="delivery_type">
                                                <option value="0">Select</option>
                                                @foreach($data['delivery_types'] as $type)
                                                    @php $type_id = isset($delivery_type_id)?$delivery_type_id:"" @endphp
                                                    <option value="{{$type->delivery_type_id}}" {{ $type_id == $type->delivery_type_id ? "selected" : "" }}>{{$type->delivery_type_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Branch: <span class="required">*</span></label>
                                    <div class="col-lg-9">
                                        <div class="erp-select2 branch-select">
                                            <select class="form-control kt-select2 erp-form-control-sm branch_id" id="kt_select2_2" name="branch">
                                                <option value="0">Select</option>
                                                @foreach($data['branch'] as $branch)
                                                    @php $branch_id_var = isset($delivery_branch_id)?$delivery_branch_id:"" @endphp
                                                    <option value="{{$branch->branch_id}}" {{ $branch_id_var == $branch->branch_id ? "selected" : "" }}>{{$branch->branch_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">City: <span class="required">*</span></label>
                                    <div class="col-lg-9">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm delivery_type" id="city_list" name="city">
                                                <option value="0">Select</option>
                                                @foreach($data['cities'] as $city)
                                                    @php $c_id = isset($city_id)?$city_id:"" @endphp
                                                    <option value="{{$city->city_id}}" {{ $c_id == $city->city_id ? "selected" : "" }}>{{$city->city_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Area: <span class="required">*</span></label>
                                    <div class="col-lg-9">
                                        <div class="erp-select2 branch-select">
                                            <select class="form-control kt-select2 erp-form-control-sm area_id" id="area_list" name="area">
                                                <option value="0">Select</option>
                                                @if($case == 'edit')
                                                    @foreach($data['areas'] as $area)
                                                        @php $a_id = isset($area_id)?$area_id:"" @endphp
                                                        <option value="{{$area->area_id}}" {{ $a_id == $area->area_id ? "selected" : "" }}>{{ $area->area_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="delivery_charges_detail" class="mt-4">
                            <div id="kt_repeater_delivery_charges" class="@if($case == 'new') d-none @endif">
                                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
                                <div class="form-group row">
                                <div data-repeater-list="delivery_charges_data" class="col-lg-12">
                                        <div class="col-lg-12 mb-3">
                                            <div class="row text-left"> 
                                                <div class="col-lg-2 font-weight-bold">Min Value</div>
                                                <div class="col-lg-2 font-weight-bold">Max value</div>
                                                <div class="col-lg-3 font-weight-bold">% Charges</div>
                                                <div class="col-lg-3 font-weight-bold">Charges</div>
                                                <div class="col-lg-2 font-weight-bold"></div>
                                            </div>
                                        </div>
                                        @if($case == 'new')
                                        <div data-repeater-item class="kt-margin-b-10 delivery-charge-data">
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <div class="row parent-tr">
                                                    <div class="col-lg-2"><input type="text" class="form-control validNumber validOnlyNumber erp-form-control-sm min_value" name="pd[min_value]" ></div>
                                                    <div class="col-lg-2"><input type="text" class="form-control validNumber validOnlyNumber erp-form-control-sm max_value" name="pd[max_value]" ></div>
                                                    <div class="col-lg-3"><input type="text" class="form-control validNumber validOnlyNumber erp-form-control-sm perc_charges" name="pd[perc_charges]" ></div>
                                                    <div class="col-lg-3"><input type="text" class="form-control validNumber validOnlyNumber erp-form-control-sm charges" name="pd[charges]" ></div>
                                                    <div class="col-lg-2">
                                                        <a href="javascript:;" style="height: 28px;" data-repeater-delete="" class="btn btn-danger btn-icon btn-sm">
                                                            <i class="la la-remove"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($case == 'edit')
                                        @foreach($dtls as $dtl)
                                            <div data-repeater-item class="kt-margin-b-10 delivery-charge-data">
                                                <div class="form-group row">
                                                    <div class="col-lg-12">
                                                        <div class="row parent-tr">
                                                            <div class="col-lg-2"><input type="text" class="form-control validNumber validOnlyNumber erp-form-control-sm min_value" name="delivery_charges_data[{{ $loop->iteration }}][min_value]" value="{{ $dtl->min_value }}"></div>
                                                            <div class="col-lg-2"><input type="text" class="form-control validNumber validOnlyNumber erp-form-control-sm max_value" name="delivery_charges_data[{{ $loop->iteration }}][max_value]" value="{{ $dtl->max_value }}"></div>
                                                            <div class="col-lg-3"><input type="text" class="form-control validNumber validOnlyNumber erp-form-control-sm perc_charges" name="delivery_charges_data[{{ $loop->iteration }}][perc_charges]" value="{{ $dtl->perc_charges }}"></div>
                                                            <div class="col-lg-3"><input type="text" class="form-control validNumber validOnlyNumber erp-form-control-sm charges" name="delivery_charges_data[{{ $loop->iteration }}][charges]" value="{{ $dtl->charges }}"></div>
                                                            <div class="col-lg-2">
                                                                <a href="javascript:;" style="height: 28px;" data-repeater-delete="" class="btn btn-danger btn-icon btn-sm">
                                                                    <i class="la la-remove"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <div data-repeater-create="" class="btn btn btn-primary">
                                            <span id="new">
                                            <i class="la la-plus pr-0"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--end::Form-->
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')
        <script>
            var between = [];
            $(document).on('change','#area_list',function(){
                if($('#kt_select2_1').val() == "0"){
                    toastr.error('Please Select Delivery Type');
                    $('#area_list').val(1).trigger('change.select2');
                }else if($('#kt_select2_2').val() == "0"){
                    toastr.error('Please Select Branch');
                    $('#area_list').val(1).trigger('change.select2');
                }else if($('#city_list').val() == "0"){
                    toastr.error('Please Select City');
                }else if($('#area_list').val() == "0"){
                    toastr.error('Please Select Area');
                }else{
                    var body = '';
                    var formData = {
                        delivery_type : $('#kt_select2_1').val(),
                        branch_id : $('#kt_select2_2').val(),
                        city_id : $('#city_list').val(),
                        area_id : $('#area_list').val(),
                    };
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type        : 'POST',
                        url         : '/delivery-charges/get-delivery-charges-dtl-data',
                        dataType	: 'json',
                        data        : formData,
                        beforeSend  : function(){
                            $('#kt_repeater_delivery_charges').addClass('d-none');
                            $('#charges-detail-table-body').html('');
                            $('#getDeliveryChargesData').prop('disabled');
                            $('body').addClass('pointerEventsNone');
                        },
                        success: function(response) {
                            $('body').removeClass('pointerEventsNone');
                            if(response['status'] == 'success'){
                                if(response['data'].hasOwnProperty("redirect")){
                                    toastr.warning(response.message);
                                    window.location.replace(response['data'].redirect);
                                }else{
                                    toastr.success(response.message);
                                    $('#kt_repeater_delivery_charges').removeClass('d-none');
                                }
                            }
                        },
                        error : function(xhr,message){
                            $('body').removeClass('pointerEventsNone');
                            toastr.error('Something went wrong');
                        }
                    });
                }
            });

            $(document).on('change' , '#city_list',function(){
                if($('#city_list').val() != "0"){
                    var city_id = $(this).val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url : '/area/get-area-by-city',
                        method : 'POST',
                        data : {"city_id" : city_id},
                        async : false,
                        beforeSend : function(){
                            $('#getDeliveryChargesData option:not(first)').remove();
                            $('body').addClass('pointerEventsNone');
                        },
                        success : function(response,status){
                            $('body').removeClass('pointerEventsNone');
                            $('#area_list').html('');
                            if(response.status == 'success'){
                                var areas = response.data;
                                var option = '';
                                option += '<option value="0">Select</option>';
                                areas.forEach((el) => {
                                    option += '<option value="'+ el.area_id +'">'+el.area_name+'</option>';
                                });
                                $('#area_list').append(option);
                            }else{
                                toastr.error('No Areas In This City');
                            }
                        },
                        error: function(response,status) {
                            $('body').removeClass('pointerEventsNone');
                            toastr.error(response.responseJSON.message);    
                        },
                    });
                }else{
                    toastr.error('Please Select City');
                }
            });

            $(document).on('paste blur drop', '.min_value' , function(e){
                var tr = $(this).parents('.parent-tr');
                var min_val = parseFloat($(this).val());
                var max_val = parseFloat(tr.find('.max_value').val());
                if(min_val > max_val){
                    tr.find('.min_value').val(max_val);
                }
            });

            $(document).on('paste blur drop', '.max_value' , function(e){
                var tr = $(this).parents('.parent-tr');
                var max_val = parseFloat($(this).val());
                var min_val = parseFloat(tr.find('.min_value').val());
                if(min_val > max_val){
                    tr.find('.min_value').val(max_val);
                }
            });

            $(document).on('paste blur drop', '.perc_charges' , function(e){
                var tr = $(this).parents('.parent-tr');
                if($(this).val() != 0){
                    tr.find('.charges').val(0);
                }
            });
            $(document).on('paste blur drop', '.charges' , function(e){
                var tr = $(this).parents('.parent-tr');
                if($(this).val() != 0){
                    tr.find('.perc_charges').val(0);
                }
            });

            var deliveryCharges = function() {
                $('#kt_repeater_delivery_charges').repeater({
                    initEmpty: false,
                    isFirstItemUndeletable: true,
                    defaultValues: {
                        'text-input': 'foo'
                    },
                    show: function(){
                        var container = $(this).parents('#kt_repeater_delivery_charges');
                        var repeaters = container.find('.delivery-charge-data');
                        $(this).slideDown();
                    },
                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                    }
                });
            }
            deliveryCharges();

        </script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/setting/delivery-charges.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/pages/crud/forms/widgets/form-repeater.js') }}" type="text/javascript"></script>
@endsection
