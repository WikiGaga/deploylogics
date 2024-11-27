@extends('layouts.template')
@section('title', 'Rent Party Profile')

@section('pageCSS')
    <style>
        select[readonly].select2-hidden-accessible+.select2-container{pointer-events:none;touch-action:none;box-shadow:none}.select2-selection__arrow,select[readonly].select2-hidden-accessible+.select2-container .select2-selection__clear{display:none}
    </style>
@endsection
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code = $data['party_code'];
            }
            if($case == 'edit'){
                $id = $data['current']->party_profile_id;
                $code = $data['current']->party_code;
                $party_account_id = $data['current']->chart_account_id;
                $parent_account_id = $data['current']->parent_account_id;
                $name = $data['current']->party_name;
                $party_status = $data['current']->rent_party_status;
                $cr_no = $data['current']->party_cr_no;
                $mobile_no = $data['current']->party_telephone;
                $labor_card_no = $data['current']->party_labor_card_no;
                $passport_no = $data['current']->party_passport_no;
                $sponsor_name = $data['current']->party_sponsor_name;
                $nationality = $data['current']->party_nationality;
                $po_code = $data['current']->party_po_code;
                $po_box = $data['current']->party_po_box;
                $customer_address = $data['current']->party_address;
            }   
    @endphp
    @permission($data['permission'])
    <form id="rent_party_profile_form" class="kt-form" method="post" action="{{ action('Rent\RentPartyProfileController@store',isset($id)?$id:"") }}" enctype="multipart/form-data">
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
                        <div>
                            @if(isset($party_account_id))
                                Account Id: <b>{{$party_account_id}}</b>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Type: <span class="required"> * </span></label>
                            <div class="col-lg-8">
                                <div class="erp-select2">
                                    <select class="tb_moveIndex form-control erp-form-control-sm kt-select2" name="parent_account_id" id="parent_account_id" @if($case == 'edit') readonly @endif>
                                        <option value="0">Select</option>
                                        <option value="269" @if(isset($parent_account_id) && $parent_account_id == '269') selected @endif>Rent Receive</option>
                                        <option value="19515121212300" @if(isset($parent_account_id) && $parent_account_id == '19515121212300') selected @endif>Rent Pay</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Name: <span class="required"> * </span></label>
                            <div class="col-lg-9">
                                <input type="text" name="rent_party_name" id="rent_party_name" class="form-control erp-form-control-sm medium_text" value="{{ $name ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Active: <span class="required">*</span></label>
                            <div class="col-lg-8">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($party_status)?$party_status:0; @endphp
                                            <input type="checkbox" name="rent_party_status" {{ $entry_status == 1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="rent_party_status" checked>
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>{{-- end row--}}
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group-block row">
                            <label class="col-lg-2 erp-col-form-label">CR No.:</label>
                            <div class="col-lg-10">
                                <input type="text" name="rent_party_cr_no" id="rent_party_cr_no" class="form-control erp-form-control-sm medium_text" value="{{ $cr_no ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Mobile No:</label>
                            <div class="col-lg-8">
                                <input type="text" name="rent_party_phone" id="rent_party_phone" class="form-control erp-form-control-sm medium_text" value="{{ $mobile_no ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Labour Card No.:</label>
                            <div class="col-lg-8">
                                <input type="text" name="rent_party_labour_card_no" class="form-control erp-form-control-sm medium_text" value="{{ $labor_card_no ?? ''}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Passport No.:</label>
                            <div class="col-lg-8">
                                <input type="text" name="rent_party_passport" class="form-control erp-form-control-sm medium_text" value="{{ $passport_no ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Sponsor Name:</label>
                            <div class="col-lg-8">
                                <input type="text" name="rent_party_sponsor" class="form-control erp-form-control-sm medium_text" value="{{ $sponsor_name ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>{{-- end row--}}
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Nationality</label>
                            <div class="col-lg-8">
                                <input type="text" name="rent_party_nationality" class="form-control erp-form-control-sm medium_text" value="{{ $nationality ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">P Code:</label>
                            <div class="col-lg-8">
                                <input type="text" name="rent_party_postal" class="form-control erp-form-control-sm medium_text" value="{{ $po_code ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">P.O Box:</label>
                            <div class="col-lg-8">
                                <input type="text" name="rent_party_po_box" class="form-control erp-form-control-sm medium_text" value="{{ $po_box ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>{{-- end row--}}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group-block row">
                            <label class="col-lg-12 erp-col-form-label">Customer Address:</label>
                            <div class="col-lg-12">
                                <textarea type="text" rows="2" name="rent_party_address" class="form-control erp-form-control-sm double_text">{{$customer_address ?? ''}}</textarea>
                            </div>
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
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/rent/rent_party_profile.js') }}" type="text/javascript"></script>
@endsection

