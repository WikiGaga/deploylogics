@extends('layouts.template')
@section('title', 'Coupons')

@section('pageCSS')
@endsection
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code = $data['coupon_code'];
            }
            if($case == 'edit'){
                $id = $data['current']->coupon_id;
                $code = $data['coupon_code'];
                $coupon_date = '';
                $donater_name = $data['current']->customer->customer_name;
                $donater_id = $data['current']->customer->customer_id;
                $show_donater_name = $data['current']->show_donater_name;
                $donater_email = $data['current']->donater_email;
                $donater_phone = $data['current']->donater_phone;
                $donater_national_id = $data['current']->donater_national_id;
                $donater_budget = $data['current']->donater_budget;
                $donater_address = $data['current']->donater_address;
                $coupon_valid_branches = isset($data['current']->coupon_valid_branches) ? explode("," , $data['current']->coupon_valid_branches) : [];
                $dtls = $data['current']->coupon_dtl ?? [];
            }
    @endphp
    @permission($data['permission'])
    <form id="coupons_form" class="kt-form" method="post" action="{{ action('Sales\SaleCouponsController@store',isset($id)?$id:"") }}" enctype="multipart/form-data">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <div class="col-lg-12">
                            <div class="erp-page--title">
                                {{isset($code)?$code:""}}
                            </div>
                        </div>
                    </div>
                    {{-- SECTION --}}
                    <div class="form-group-block row my-3">
                        <div class="col-lg-12">
                            <div class="erp-page--title">
                                Donater Information:
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Date:</label>
                                <div class="col-lg-8">
                                    <div class="input-group date">
                                        <input type="text" name="coupon_sale_date" class="moveIndex form-control erp-form-control-sm c-date-p" readonly="" value="{{ date('d-m-Y') }}" id="kt_datepicker_3" autofocus="" autocomplete="off" aria-invalid="false">
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
                            <label class="col-lg-4 erp-col-form-label">Customer: <span class="required">*</span></label>
                            <div class="col-lg-8">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                        </div>
                                        <input type="text" id="customer_name" value="{{isset($donater_name)?$donater_name:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','customerHelp')}}" autocomplete="off" name="customer_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="hidden" id="customer_id" name="customer_id" value="{{isset($donater_id)?$donater_id:''}}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Show Donater Name:</label>
                                <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $show_donater_name = isset($show_donater_name)?$show_donater_name:0; @endphp
                                                <input type="checkbox" name="show_donater_name" {{ $show_donater_name == 1?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="show_donater_name">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Email: <span class="required"> * </span></label>
                                <div class="col-lg-8">
                                    <input type="email" name="donater_email" class="form-control erp-form-control-sm" value="{{isset($donater_email)?$donater_email:''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Phone: <span class="required"> * </span></label>
                                <div class="col-lg-8">
                                    <input type="text" id="customer_contact" name="donater_phone" class="form-control erp-form-control-sm" value="{{isset($donater_phone)?$donater_phone:''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">CR/ID: <span class="required"> * </span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="donater_national_id" class="form-control erp-form-control-sm" value="{{isset($donater_national_id)?$donater_national_id:''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Budget: <span class="required">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="donater_budget" id="donater_budget" class="form-control erp-form-control-sm validNumber" value="{{isset($donater_budget)?$donater_budget:''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row">
                                <label class="col-lg-2 erp-col-form-label">Address: <span class="required"> * </span></label>
                                <div class="col-lg-10">
                                    <input type="text" id="customer_address" name="donater_address" class="form-control erp-form-control-sm" value="{{isset($donater_address)?$donater_address:''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-12">
                        <div class="row">
                            <label class="col-lg-12 erp-col-form-label">Valid Branches: <span class="required">*</span></label>
                            <div class="col-lg-12">
                                <div class="erp-select2">
                                    <select name="valid_branches[]" data-kt-repeater="select2"  id="valid_branches" multiple class="moveIndex kt-select2 form-control erp-form-control-sm">
                                        <option value="0">Select</option>
                                        @if($case == 'new')
                                            @foreach($data['branches'] as $branch)
                                                <option value="{{ $branch->branch_id }}" @if($branch->branch_id == $data['current_branch']) selected @endif> {{ $branch->branch_short_name }}</option>    
                                            @endforeach
                                        @else
                                            @foreach($data['branches'] as $branch)
                                                <option value="{{ $branch->branch_id }}" @if(in_array($branch->branch_id , $coupon_valid_branches)) selected @endif> {{ $branch->branch_short_name }}</option>    
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    {{-- SECTION --}}
                    <div class="form-group-block row my-3">
                        <div class="col-lg-6">
                            <div class="erp-page--title">
                                Deals:
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="erp-page--title">
                                Total Budget: <span id="total_budget">{{ $donater_budget ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="erp-page--title">
                                Remaning Budget: <span id="remaning_budget">0</span>
                                <input type="hidden" name="remaning_budget" id="remaning_budget_input">
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block">
                        <div id="kt_repeater_coupon">
                            <div class="form-group-block">
                                @if($case == 'edit')
                                    @include('sales.coupons.partials.edit')
                                @else
                                    @include('sales.coupons.partials.new')
                                @endif
                            </div>
                            <div class="row mb-5">
                                <div class="col-lg-12 text-right">
                                    <div class="btn-group" role="group" aria-label="First group">
                                        @if($case == 'new')
                                            <button type="button" class="generateBenificery btn btn-primary btn-sm d-none"><i class="la la-check"></i> @if($case == 'edit') Re Generate Benificery @else Generate Benificery @endif</button>
                                        @endif
                                    </div>
                                    <div class="btn-group" role="group" aria-label="First group">
                                        <button type="button" data-repeater-create="coupon_data" class="btn btn-success btn-sm"><i class="la la-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- SECTION --}}
                    <div class="form-group-block row my-3">
                        <div class="col-lg-6">
                            <div class="erp-page--title">
                                List Of Benefeciary(s):
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-12">
                            <div class="row" id="benificieryList">
                                @if($case == 'edit')
                                    @if(isset($dtls) && count($dtls) > 0)
                                        @php $item_id = 0; @endphp
                                        @foreach($dtls as $benf_dtl)
                                            @if(isset($benf_dtl->coupon_benificery))
                                                <div class="col-lg-12 mt-2">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            @if(isset($benf_dtl->coupon_benificery[0]))
                                                                <h4>Coupon : {{ count($benf_dtl->coupon_benificery) }} x {{ $benf_dtl->coupon_benificery[0]->coupon_value }}</h4>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @foreach($benf_dtl->coupon_benificery as $befDtl)
                                                    @php $sr = 0; @endphp
                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                            <label class="col-lg-12 erp-col-form-label">Coupon Code: <span class="required"> * </span></label>
                                                            <div class="col-lg-12">
                                                                <input type="text" name="coupon_data[{{ $item_id }}][coupon_dtl][{{ $loop->index }}][{{ $sr++ }}]" class="form-control erp-form-control-sm" readonly value="{{ $befDtl->coupon_identifier }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                            <label class="col-lg-12 erp-col-form-label">Benefeciary Name:</label>
                                                            <div class="col-lg-12">
                                                                <input type="text" name="coupon_data[{{ $item_id }}][coupon_dtl][{{ $loop->index }}][{{ $sr++ }}]" class="form-control erp-form-control-sm" value="{{ $befDtl->coupon_benificery }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                            <label class="col-lg-12 erp-col-form-label">Coupon Value: <span class="required"> * </span></label>
                                                            <div class="col-lg-12">
                                                                <input type="text" name="coupon_data[{{ $item_id }}][coupon_dtl][{{ $loop->index }}][{{ $sr++ }}]" class="form-control erp-form-control-sm" readonly value="{{ $befDtl->coupon_value }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            @php $item_id++; @endphp
                                        @endforeach
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')
    <script>
        function makeid(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result.toUpperCase();
        }

        $(document).ready(function(e){
            calculateBudget();
            var remaning_budget = 0;
            $(document).on('keyup','.coupon_value,.coupon_qty,#donater_budget' , function(e){
                if($('#donater_budget').val() == ""){
                    $('#total_budget').html('').html('0');
                }else{
                    $('#total_budget').html('').html($('#donater_budget').val());
                }
                calculateBudget();
            });
            $(document).on('click' ,'.generateBenificery' ,function(e){
                var sr = 0;
                var parent = $(this).parents('.coupon-container');
                var labels = ['Coupon Code' , 'Benificery Name' , 'Coupon Value'];
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : '/coupons/get-latest-code',
                    method : 'GET',
                    data : {},
                    async : false,
                    beforeSend: function(){
                        $('body').addClass('pointerEventsNone');
                    },  
                    success : function(response,status){
                        $('body').removeClass('pointerEventsNone');
                        var code = response.data.lastNumber;
                        sr = parseInt(code);
                    },
                    error: function(response,status) {
                        $('body').removeClass('pointerEventsNone');
                        toastr.error(response.responseJSON.message);
                        return false;
                    },
                });
                if(calculateBudget(true)){
                    var html = '';
                    $('#benificieryList').html('');
                    $(document).find('.coupon-container').each((el,index)=>{
                        var qty = $('.coupon-container')[el].querySelector('.coupon_qty').value;
                        var value = $('.coupon-container')[el].querySelector('.coupon_value').value;
                        var item_id = $('.coupon-container')[el].getAttribute('item-id');
                        html = '<div class="col-lg-12 mt-2">'+
                                    '<div class="row">'+
                                        '<div class="col-lg-12">'+
                                            '<h4>Coupons : '+ qty +' x '+ value +'</h4>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>';
                        for(var i = 0; i < qty; i++){
                            sr++;
                            for(var j = 0; j < 3; j++){
                                var couponValues = ['COP-'+ sr , '' , value];
                                var couponPara = ['readonly' , '' , 'readonly'];
                                html += '<div class="col-lg-4">'+
                                    '<div class="row">'+
                                        '<label class="col-lg-12 erp-col-form-label">'+labels[j]+':</label>'+
                                        '<div class="col-lg-12">'+
                                            '<input type="text" name="coupon_data['+item_id+'][coupon_dtl]['+i+']['+j+']" class="form-control erp-form-control-sm" '+ couponPara[j] +' value="'+couponValues[j]+'">'+
                                        '</div>'+
                                    '</div>'+
                                '</div>';
                            }
                        }
                        $('#benificieryList').append(html);
                    });
                }else{
                    toastr.error('Budget will Exced.');
                }
            }); 
        });

    </script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/coupons.js') }}" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v=').time() }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/pages/crud/forms/widgets/form-repeater.js') }}" type="text/javascript"></script>
@endsection

