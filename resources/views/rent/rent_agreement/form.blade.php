@extends('layouts.template')
@section('title', 'Rent Agreement')

@section('pageCSS')
    <style>
        .modal-body .wrapper{display:inline-flex;background:#fff;height:100px;width:400px;align-items:center;justify-content:space-evenly;border-radius:5px;padding:20px 15px;box-shadow:5px 5px 30px rgba(0,0,0,.2)}.modal-body .wrapper .option{background:#fff;height:100%;width:100%;display:flex;align-items:center;justify-content:space-evenly;margin:0 10px;border-radius:5px;cursor:pointer;padding:0 10px;border:2px solid #d3d3d3;transition:.3s}.modal-body .wrapper .option .dot{height:20px;width:20px;background:#d9d9d9;border-radius:50%;position:relative}.modal-body .wrapper .option .dot::before{position:absolute;content:"";top:4px;left:4px;width:12px;height:12px;background:#0069d9;border-radius:50%;opacity:0;transform:scale(1.5);transition:.3s}.modal-body input[type=radio]{display:none}.modal-body #option-1:checked:checked~.option-1,.modal-body #option-2:checked:checked~.option-2{border-color:#0069d9;background:#0069d9}.modal-body #option-1:checked:checked~.option-1 .dot,.modal-body #option-2:checked:checked~.option-2 .dot{background:#fff}.modal-body #option-1:checked:checked~.option-1 .dot::before,.modal-body #option-2:checked:checked~.option-2 .dot::before{opacity:1;transform:scale(1)}.modal-body .wrapper .option span{font-size:20px;color:grey}#option-1:checked:checked~.option-1 span,#option-2:checked:checked~.option-2 span{color:#fff}
        .select2-container--default.select2-container--disabled .select2-selection--single{border-color: #e2e5ec!important;}
    </style>
@endsection
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code = $data['agreement_code'];
                $menu_id = $data['menu_id'];
            }
            if($case == 'edit'){
                $id = $data['current']->rent_agreement_id;
                $agreement_date = date('d-m-Y' , strtotime($data['current']->rent_agreement_date));
                $agreement_start_date = date('d-m-Y' , strtotime($data['current']->rent_agreement_start_date));
                $agreement_end_date = date('d-m-Y' , strtotime($data['current']->rent_agreement_end_date));
                $menu_id = $data['menu_id'];
                $code = $data['current']->agreement_code;
                $agreement_location_id = $data['current']->rent_location_id;
                $agreement_city_id = $data['current']->city_id;
                $agreement_period = $data['current']->rent_agreement_period;
                $agreemetn_amount = $data['current']->rent_agreement_amount;
                $agreement_advance = $data['current']->rent_advance_paid;
                $agreement_ob = $data['current']->rent_opening_balance;
                $agreement_remarks = $data['current']->rent_agreement_remarks;
                $first_party_details = $data['current']->firstParty ?? [];
                $second_party_details = $data['current']->secondParty ?? [];
                $agreement_dtls = $data['current']->dtls ?? [];
            }   
            $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="rent_party_agreement_form" class="kt-form" method="post" action="{{ action('Rent\RentAgreementController@store',isset($id)?$id:"") }}" enctype="multipart/form-data">
    <input type="hidden" value='{{$form_type}}' id="form_type">
    <input type="hidden" value='{{$menu_id}}' id="menu_id">
    <input type="hidden" value='{{isset($id)?$id:""}}' id="form_id">
    <input type="hidden" name="form_case" value='{{$case}}' id="form_case">
    @csrf
    <div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="form-group-block row mb-4">
                    <div class="col-lg-12">
                        <div class="erp-page--title">
                            {{isset($code)?$code:""}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Date: <span class="required"> * </span></label>
                            <div class="col-lg-8">
                            <input type="text" name="rent_agreement_date" id="rent_agreement_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex kt_datepicker_3" readonly value="{{ date('d-m-Y') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Start Date: <span class="required"> * </span></label>
                            <div class="col-lg-8">
                            <input type="text" name="rent_agreement_start_date" id="rent_agreement_start_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex kt_datepicker_3" @if($case == 'edit') disabled @else readonly @endif value="{{ $agreement_start_date ?? date('d-m-Y') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">End Date: <span class="required"> * </span></label>
                            <div class="col-lg-8">
                            <input type="text" name="rent_agreement_end_date" id="rent_agreement_end_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex kt_datepicker_3" @if($case == 'edit') disabled @else readonly @endif value="{{ $agreement_end_date ?? date('d-m-Y' , strtotime('+1 month')) }}"/>
                            </div>
                        </div>
                    </div>
                </div>{{-- end row--}}
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group-block row">
                            <label class="col-lg-2 erp-col-form-label">Location <span class="required"> * </span></label>
                            <div class="col-lg-10">
                                <div class="erp-select2">
                                    <select name="rent_agreement_location" id="rent_agreement_location" class="form-control erp-form-control-sm kt-select2" @if($case == 'edit') disabled @endif>
                                        @foreach($data['rentalLocations'] as $rentalLocation)
                                            @php $rentalLocationId = isset($agreement_location_id)?$agreement_location_id:""; @endphp
                                            <option value="{{$rentalLocation->rent_location_id}}" {{ $rentalLocation->rent_location_id == $rentalLocationId?'selected':'' }}>{{$rentalLocation->rent_location_name_string}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">City:</label>
                            <div class="col-lg-8">
                                <input type="text" name="rent_agreement_city" id="rent_agreement_city" class="form-control erp-form-control-sm medium_text" value="{{ $agreement_city_id ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>{{-- end row--}}
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Period: <span class="required">*</span></label>
                            <div class="col-lg-8">
                                <div class="erp_form___block">
                                    <div class="input-group">
                                        <input type="text" id="rent_agreement_period" value="{{ $agreement_period ?? 1 }}" autocomplete="off" name="rent_agreement_period" class="form-control erp-form-control-sm" @if($case == 'edit') disabled @else readonly @endif>
                                        <div class="input-group-append">
                                            <span class="input-group-text btn-minus-selected-data px-2">
                                                Month's
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group-block row">
                            <label class="col-lg-2 erp-col-form-label">Rent/Amount: <span class="required">*</span></label>
                            <div class="col-lg-10">
                                <div class="erp_form___block">
                                    <div class="input-group">
                                        <input type="text" id="rent_agreement_amount" value="{{ $agreemetn_amount ?? '' }}" autocomplete="off" name="rent_agreement_amount" class="form-control erp-form-control-sm" @if($case == 'edit') disabled @endif>
                                        @if($case == 'new')
                                            <div class="input-group-append">
                                                <span class="input-group-text btn-minus-selected-data bg-primary text-white px-2 border-0 generateInstallments">
                                                    Generate Installments
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <small>This rent amount is per month</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>{{-- end row--}}
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Advance Paid:</label>
                            <div class="col-lg-8">
                                <div class="erp_form___block">
                                    <div class="input-group">
                                        <input type="text" id="rent_agreement_advance" value="{{ $agreement_advance ?? 0 }}" autocomplete="off" name="rent_agreement_advance" class="form-control erp-form-control-sm" @if($case == 'edit') disabled @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Opening Balance:</label>
                            <div class="col-lg-8">
                                <div class="erp_form___block">
                                    <div class="input-group">
                                        <input type="text" id="rent_agreement_ob" value="{{ $agreement_ob ?? 0 }}" autocomplete="off" name="rent_agreement_ob" class="form-control erp-form-control-sm" @if($case == 'edit') disabled @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>{{-- end row--}}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group-block row">
                            <label class="col-lg-12 erp-col-form-label">Remarks:</label>
                            <div class="col-lg-12">
                                <textarea type="text" rows="2" name="rent_agreement_remarks" class="form-control erp-form-control-sm double_text">{{ $agreement_remarks ?? ''}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-6">
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">First Party: <span class="required">* <small>(Receiver)</small></span></h5>
                                        <div class="row mb-4">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <label for="" class="col-lg-12">Select Party <span class="required">*</span></label>
                                                    <div class="col-lg-12">
                                                        <div class="erp-select2">
                                                            <select name="first_party_id" id="first_party_id" class="form-control erp-form-control-sm kt-select2" @if($case == 'edit') disabled @endif> 
                                                                <option value="0">Select First Party</option>
                                                                @foreach($data['rentalReceiveParties'] as $party)
                                                                    <option value="{{ $party->party_profile_id }}" @if(isset($first_party_details->party_profile_id) && $first_party_details->party_profile_id == $party->party_profile_id) selected @endif>{{ $party->party_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <label for="first_party_cr" class="col-lg-12">CR No: <span class="required">*</span></label>
                                                    <div class="col-lg-12">
                                                        <input type="text" name="first_party_cr" id="first_party_cr" class="form-control erp-form-control-sm medium_text rent_party_cr" value="{{ $first_party_details->party_cr_no ?? '' }}" @if($case == 'edit') disabled @else readonly @endif>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <label for="first_party_mobile" class="col-lg-12">Mobile No: <span class="required">*</span></label>
                                                    <div class="col-lg-12">
                                                        <input type="text" name="first_party_mobile" id="first_party_mobile" value="{{ $first_party_details->party_telephone ?? '' }}" class="form-control erp-form-control-sm medium_text rent_party_mobile" @if($case == 'edit') disabled @else readonly @endif>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Second Party: <span class="required">* <small>(Payer)</small></span></h5>
                                        <div class="row mb-4">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <label for="" class="col-lg-12">Select Party <span class="required">*</span></label>
                                                    <div class="col-lg-12">
                                                        <div class="erp-select2">
                                                            <select name="second_party_id" id="second_party_id" class="form-control erp-form-control-sm kt-select2" @if($case == 'edit') disabled @endif>
                                                                <option value="0">Select Second Party</option>
                                                                @foreach($data['rentalPayParties'] as $party)
                                                                    <option value="{{ $party->party_profile_id }}" @if(isset($second_party_details->party_profile_id) && $second_party_details->party_profile_id == $party->party_profile_id) selected @endif>{{ $party->party_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <label for="second_party_cr" class="col-lg-12">CR No: <span class="required">*</span></label>
                                                    <div class="col-lg-12">
                                                        <input type="text" name="second_party_cr" id="second_party_cr" class="form-control erp-form-control-sm medium_text rent_party_cr" value="{{ $second_party_details->party_cr_no ?? '' }}" @if($case == 'edit') disabled @else readonly @endif>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <label for="second_party_mobile" class="col-lg-12">Mobile No: <span class="required">*</span></label>
                                                    <div class="col-lg-12">
                                                        <input type="text" name="second_party_mobile" id="second_party_mobile" class="form-control erp-form-control-sm medium_text rent_party_mobile" value="{{ $second_party_details->party_telephone ?? '' }}" @if($case == 'edit') disabled @else readonly @endif>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>{{-- Party Section Ended --}}
                <div class="row mt-5">
                    <div class="col-lg-12">
                        <h5>Rent Months</h5>
                        <table class="table table-borderd table-stripped table-hover">
                            <thead>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Discount</th>
                                <th>Balance</th>
                                <th>Description</th>
                                <th class="text-right">Action</th>
                            </thead>
                            <tbody class="rentInstallments">
                                @if($case == 'new')
                                    <tr>
                                        <td colspan="4">No Data...</td>
                                    </tr>
                                @else
                                    @if(isset($agreement_dtls) && count($agreement_dtls) > 0)
                                        @foreach($agreement_dtls as $dtl)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="pd[{{ $loop->iteration }}][rent_agreement_dtl_id]" value="{{ $dtl->rent_agreement_dtl_id }}">
                                                    <input type="text" name="pd[{{ $loop->iteration }}][rent_collect_date]" value="{{ date('d-m-Y' , strtotime($dtl->rent_agreement_dtl_date)) }}" class="form-control form-control-sm" readonly>
                                                    <input type="hidden" value="{{ number_format($dtl->rent_agreement_dtl_amount,3) }}" class="form-control form-control-sm actualInstallment">
                                                </td>
                                                <td>
                                                    <input type="text" name="pd[{{ $loop->iteration }}][rent_collect_amount]" class="form-control form-control-sm installment-amount" value="{{ number_format($dtl->rent_agreement_dtl_amount,3) }}" @if($dtl->rent_agreement_dtl_status == 1) readonly @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="pd[{{ $loop->iteration }}][rent_collect_discount]" class="form-control form-control-sm installment-discount-amount" value="{{ number_format($dtl->rent_agreement_dtl_discount,3) }}" @if($dtl->rent_agreement_dtl_status == 1) readonly @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="pd[{{ $loop->iteration }}][rent_collect_balance]" class="form-control form-control-sm installment-balance-amount" value="{{ number_format($dtl->rent_agreement_dtl_balance,3) }}" @if($dtl->rent_agreement_dtl_status == 1) readonly @endif>
                                                </td>
                                                <td>
                                                    <input type="text" name="pd[{{ $loop->iteration }}][rent_collect_descripiton]" class="form-control form-control-sm installment-desc" value="{{ $dtl->rent_agreement_dtl_desc }}">
                                                </td>
                                                <td class="text-right">
                                                    @if($dtl->rent_agreement_dtl_status == 0)
                                                    <button type="button" class="btn btn-primary btn-sm openVoucherModal" data-id="{{ $dtl->rent_agreement_dtl_id }}">Collect</button>
                                                    @else
                                                    <button type="button" class="btn btn-success btn-sm">Collected</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4">No Data...</td>
                                    </tr>
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-5 border-top pt-4">
                    <div class="col-lg-4">
                        <h5>Total Amount : <span id="totalRentAmount">{{ $data['totalAmount'] ?? 0.000 }}</span></h5>
                    </div>
                    <div class="col-lg-4">
                        <h5>Remaning Amount : <span id="totalRentRemaning">{{ $data['remaningAmount'] ?? 0.000 }}</span></h5>
                    </div>
                    {{-- <div class="col-lg-3">
                        <h5>Paid Amount : <span id="totalRentPaid">0.000</span></h5>
                    </div>
                    <div class="col-lg-3">
                        <h5>Diffrence : <span id="totalRentDiffrence">{{ $data['diffrenceAmount'] ?? 0.000 }}</span></h5>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    </form>

    <!-- Modal -->
        <div class="modal fade" id="rentVoucherModal" tabindex="-1" role="dialog" aria-labelledby="rentVoucherModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Rent Voucher</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-lg-12 text-center">
                      <div class="wrapper">
                        <input type="radio" class="voucherType" name="voucherType" value="cash" id="option-1" data-installment="0" checked>
                        <input type="radio" class="voucherType" name="voucherType" value="cheque" data-installment="0" id="option-2">
                        <label for="option-1" class="option option-1">
                            <div class="dot"></div>
                            <span>Cash</span>
                            </label>
                        <label for="option-2" class="option option-2">
                            <div class="dot"></div>
                            <span>Cheque</span>
                        </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="btn btn-success" id="alreadyCollectedRent">Already Collected</span>
                <span id="goVoucherScreen" class="btn btn-primary goVoucherScreen">
                    Post Voucher
                    <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <!-- Generator: Sketch 50.2 (55047) - http://www.bohemiancoding.com/sketch -->
                        <title>Stockholm-icons / Navigation / Up-right</title>
                        <desc>Created with Sketch.</desc>
                        <defs></defs>
                        <g id="Stockholm-icons-/-Navigation-/-Up-right" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon id="Shape" points="0 0 24 0 24 24 0 24"></polygon>
                            <rect id="Rectangle" fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"></rect>
                            <path fill="#fff" d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" id="Path-94" fill="#000000" fill-rule="nonzero"></path>
                        </g>
                    </svg>
                    </span>
            </div>
            </div>
        </div>
        </div>
    <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>  
    <script type="text/javascript" src="/js/datejs/build/date.js"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/rent/rent_party_agreement.js') }}" type="text/javascript"></script>
    <script>
        $('#first_party_id,#second_party_id').on('change' , function(e){
            e.preventDefault();
            var thix = $(this);
            var id = thix.val();
            var hitUrl = '{{ route("getRentPartyProfile" ,":id") }}';
            hitUrl = hitUrl.replace(':id', id);
            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:hitUrl,
                method:'POST',
                data:{'party_id' : id},
                beforeSend: function(){
                    $('body').addClass('pointerEventsNone');
                    thix.parents('.card-body').find('.rent_party_cr').val('');
                    thix.parents('.card-body').find('.rent_party_mobile').val('');
                },
                success:function(response){
                    $('body').removeClass('pointerEventsNone');
                    if(response.status == 'success'){
                        toastr.success('Party Data Loaded!');
                        var partyProfile = response.data.partyProfile;
                        thix.parents('.card-body').find('.rent_party_cr').val(partyProfile.party_cr_no);
                        thix.parents('.card-body').find('.rent_party_mobile').val(partyProfile.party_telephone);
                    }else{
                        toastr.error('Something went wrong!');
                    }
                },
                error:function(response){
                    $('body').removeClass('pointerEventsNone');
                    toastr.error(response.data.message);
                }
            }); 
        });

        function monthDiff(d1, d2) {
            var months;
            months = (d2.getFullYear() - d1.getFullYear()) * 12;
            months -= d1.getMonth();
            months += d2.getMonth();
            return months <= 0 ? 0 : months;
        }

        @if($case == 'new')
            $('#rent_agreement_start_date,#rent_agreement_end_date').on('change',function(e){
                var startDate   = $('#rent_agreement_start_date').val();
                startDate = startDate.split("-");
                var endDate     = $('#rent_agreement_end_date').val();
                endDate = endDate.split("-");
                var period = monthDiff(new Date(startDate[2], startDate[1]-1,startDate[0]),new Date(endDate[2],endDate[1]-1,endDate[0]));
                $('#rent_agreement_period').val(period + 1);
            });
        @endif

        $(document).on('click' , '.generateInstallments' , function(e){
            e.preventDefault();
            var flag = true; var message = '';
            var requiredFields = ['rent_agreement_location','rent_agreement_period','first_party_id','second_party_id','rent_agreement_amount'];
            requiredFields.forEach(function(field){
                if($('#'+field).val() == '0' || $('#'+field).val() == ''){
                    message = field.replaceAll("_"," ");
                    message = message.toUpperCase();
                    flag = false;
                }
            });

            if(flag == true){
                var peroid = $('#rent_agreement_period').val();
                peroid = parseFloat(peroid);
                var rows = '';
                for(var i=0;i<peroid;i++){
                    var rentDate    = $('#rent_agreement_start_date').val();
                    rentDate = rentDate.split("-");
                    rentDate        = new Date(rentDate[1] + '/' + rentDate[0] + '/' + rentDate[2]);
                    rentDate        = rentDate.addMonths(i);
                    rentDate = rentDate.toString('dd-MM-yyyy');
                    var rentAmount = parseFloat( (parseFloat($('#rent_agreement_amount').val()) * parseFloat($('#rent_agreement_period').val())) - parseFloat($('#rent_agreement_advance').val())) / parseFloat($('#rent_agreement_period').val());
                    rentAmount = rentAmount.toFixed(3);
                    rows += '<tr>'+
                                '<td>'+
                                    '<input type="text" name="pd['+i+'][rent_collect_date]" class="form-control form-control-sm" readonly value="'+ rentDate +'">'+
                                '</td>'+
                                '<td>'+
                                    '<input type="text" name="pd['+i+'][rent_collect_amount]" class="form-control form-control-sm" readonly value="'+ rentAmount +'">'+
                                '</td>'+
                                '<td>'+
                                    '<input type="text" name="pd['+i+'][rent_collect_discount]" class="form-control form-control-sm" readonly value="0">'+
                                '</td>'+
                                '<td>'+
                                    '<input type="text" name="pd['+i+'][rent_collect_balance]" class="form-control form-control-sm" readonly value="0">'+
                                '</td>'+
                                '<td>'+
                                    '<input type="text" name="pd['+i+'][rent_collect_descripiton]" class="form-control form-control-sm" value="">'+
                                '</td>'+
                                '<td class="text-right">'+
                                    '<button type="button" class="btn btn-primary btn-sm" disabled>Collect</button>'+
                                '</td>'+
                            '</tr>';
                }
                $('.rentInstallments').html('').html(rows);
                var totalRent = parseFloat( parseFloat($('#rent_agreement_amount').val()) * parseFloat($('#rent_agreement_period').val()) - 0);
                var remaningRent = parseFloat( (parseFloat($('#rent_agreement_amount').val()) * parseFloat($('#rent_agreement_period').val())) - parseFloat($('#rent_agreement_advance').val()));
                var differenceRent = (totalRent - remaningRent) - parseFloat($('#rent_agreement_advance').val());
                $('#totalRentAmount').html('').html(totalRent.toFixed(3));
                $('#totalRentRemaning').html('').html(remaningRent.toFixed(3));
                $('#totalRentDiffrence').html('').html(differenceRent.toFixed(3));
            }else{
                toastr.error("INVALID FIELD " + message);
            }
        });
        $(document).on('click','#upload_documents',function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {
                form_id : $('#form_id').val(),
                form_type : $('#form_type').val(),
                menu_id : $('#menu_id').val(),
                form_code : $('.erp-page--title').text().trim(),
            }
            var data_url = '/upload-document';
            $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,formData);
        });

        $(document).on('click','.openVoucherModal',function(e){
            e.preventDefault();
            var thix = $(this);
            var installmentId = $(this).data('id');
            var installmentAmount = thix.parents('tr').find('.installment-amount').val();
            var installmentDiscount = thix.parents('tr').find('.installment-discount-amount').val();
            var installmentBalance = thix.parents('tr').find('.installment-balance-amount').val();
            var installmentDesc = thix.parents('tr').find('.installment-desc').val();

            if(installmentAmount < 0){
                toastr.error('Invalid Amount');
            }else{
                $('input.voucherType').attr('data-installment' , installmentId);
                $('input.voucherType').attr('data-amount' , installmentAmount);
                $('input.voucherType').attr('data-desc' , installmentDesc);
                $('input.voucherType').attr('data-discount' , installmentDiscount);
                $('input.voucherType').attr('data-balance' , installmentBalance);

                $('#rentVoucherModal').modal('show');
            }
        });

        $('#alreadyCollectedRent').on('click' , function(e){
            e.preventDefault();
            swal.fire({
                title: 'Alert!',
                text: "Are You Sure?",
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes'
            }).then(function(result) {
                if (result.value) {
                    var formData = {
                        voucherType : $('input.voucherType:checked').val(),
                        installmentId : $('input.voucherType').data('installment'),
                        installmentAmount : $('input.voucherType').data('amount'),
                        installmentDiscount : $('input.voucherType').data('discount'),
                        installmentBalance : $('input.voucherType').data('balance'),
                        installmentDesc : $('input.voucherType').data('desc'),
                        receiverId : $('select#first_party_id').val(),
                    }
                    $.ajax({
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ url("rent-agreement/already-entered-voucher") }}',
                        method: 'POST',
                        data: formData,
                        cache: false,
                        beforeSend: function(){
                            $('body').addClass('pointerEventsNone');
                        },
                        success: function(response){
                            // $('body').removeClass('pointerEventsNone');
                            if(response.status == 'success'){
                                $('#rentVoucherModal').modal('hide');
                                location.reload();
                            }else{
                                toastr.error(response.message);
                            } 
                        },
                        error:function(response){
                            $('body').removeClass('pointerEventsNone');
                            toastr.error('Something went wrong!');
                        }
                    });
                }
            });
        });

        $('.goVoucherScreen').on('click' , function(e){
            e.preventDefault();
            var formData = {
                voucherType : $('input.voucherType:checked').val(),
                installmentId : $('input.voucherType').data('installment'),
                installmentAmount : $('input.voucherType').data('amount'),
                installmentDiscount : $('input.voucherType').data('discount'),
                installmentBalance : $('input.voucherType').data('balance'),
                installmentDesc : $('input.voucherType').data('desc'),
                receiverId : $('select#first_party_id').val(),
            }
            $.ajax({
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url("rent-agreement/redirect-voucher-screen") }}',
                method: 'POST',
                data: formData,
                cache: false,
                beforeSend: function(){
                    $('body').addClass('pointerEventsNone');
                },
                success: function(response){
                    // $('body').removeClass('pointerEventsNone');
                    if(response.status == 'success'){
                        $('#rentVoucherModal').modal('hide');
                        window.location = response.data.redirect;
                    }else{
                        toastr.error(response.message);
                    } 
                },
                error:function(response){
                    toastr.error('Something went wrong!');
                    $('body').removeClass('pointerEventsNone');
                }
            });
        });

        function calculateBalance(thix){
            var thix = thix;
            var tr = thix.parents('tr');
            var actualAmount = tr.find('.actualInstallment').val();
            var amount = tr.find('.installment-amount').val();
            var discount = tr.find('.installment-discount-amount').val();
            var balance = amount - discount;
            tr.find('.installment-balance-amount').val(balance.toFixed(3));
        }

        $('.installment-discount-amount').on('blur' , function(e){
            var thix = $(this);
            var tr = thix.parents('tr');
            var amount = tr.find('.installment-amount').val();
            var discount = thix.val();

            var balanceAmount = amount - discount;
            tr.find('.installment-balance-amount').val(balanceAmount.toFixed(3));
            // calculateBalance($(this));
        });

        // $('.installment-amount').on('blur' , function(e){
        //     var thix = $(this);
        //     var tr = thix.parents('tr');
        //     var actualAmount = tr.find('.actualInstallment').val();
        //     var amount = tr.find('.installment-amount').val();
        //     var balance = actualAmount - amount;
        //     if(balance < 0){ balance = 0; }
        //     tr.find('.installment-balance-amount').val(balance.toFixed(3));
        // });
    </script>
@endsection

