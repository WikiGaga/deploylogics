@extends('layouts.template')
@section('title', 'Customer')

@section('pageCSS')
@endsection
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code = $data['customer_code'];
                $membership_type = 0;
                $issue_date = date('d-m-Y');
                $expiry_date = date('d-m-Y');
                $member_status = 0;
            }
            if($case == 'edit'){
                $id = $data['current']->customer_id;
                $code = $data['current']->customer_code;
                $name = $data['current']->customer_name;
                $local_name = $data['current']->customer_local_name;
                $type = $data['current']->customer_type;
                $status = $data['current']->customer_entry_status;
                $default_customer = $data['current']->customer_default_customer;
                $image = $data['current']->customer_image;

                $referenced_by = $data['current']->referenced_by;
                $home_delivery = $data['current']->home_delivery;
                $address = $data['current']->customer_address;
                $delivery_address = $data['current']->customer_delivery_address;
                $billing_address = $data['current']->customer_billing_address;
                $latitude = $data['current']->customer_latitude;
                $longitude = $data['current']->customer_longitude;
                $city_id = $data['current']->city_id;
                $region_id = $data['current']->region_id;
                $zip_code = $data['current']->customer_zip_code;
                $contact_person = $data['current']->customer_contact_person;
                $contact_person_mobile = $data['current']->customer_contact_person_mobile;
                $po_box = $data['current']->customer_po_box;
                $phone_1 = $data['current']->customer_phone_1;
                $mobile_no = $data['current']->customer_mobile_no;
                $fax = $data['current']->customer_fax;
                $whatapp_no = $data['current']->customer_whatapp_no;
                $email = $data['current']->customer_email;
                $website = $data['current']->customer_website;
                $reference_code = $data['current']->customer_reference_code;
                $remarks = $data['current']->customer_remarks;
                $membership_type = $data['current']->membership_type_id;
                $member_status = $data['current']->member_status;
                $card_number = $data['current']->card_number;
                $issue_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->issue_date))));
                $expiry_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->expiry_date))));

                $customer_account_id = $data['current']->customer_account_id;
                $no_of_days = $data['current']->customer_no_of_days;
                $credit_period = $data['current']->customer_credit_period;
                $tax_no = $data['current']->customer_tax_no;
                $credit_limit = $data['current']->customer_credit_limit;
                $debit_limit = $data['current']->customer_debit_limit;
                $tax_rate = $data['current']->customer_tax_rate;
                $tax_status = $data['current']->customer_tax_status;
                $cheque_beneficry_name = $data['current']->customer_cheque_beneficry_name;
                $strn_no = $data['current']->customer_strn_no;
                $mode_of_payment = $data['current']->customer_mode_of_payment;
                $can_scale = $data['current']->customer_can_scale;
                $additional_tax = $data['current']->customer_additional_tax;

                $bank_name = $data['current']->customer_bank_name;
                $loyalty_opnening = $data['current']->loyalty_opnening;
                $bank_account_no = $data['current']->customer_bank_account_no;
                $bank_account_title = $data['current']->customer_bank_account_title;
                $sub_customers = isset($data['current']->sub_customer) ? $data['current']->sub_customer : [] ;
                $contact_persons = isset($data['current']->contact_person) ? $data['current']->contact_person : [] ;
                $customer_branches = isset($data['current']->customer_branches) ? $data['current']->customer_branches : [] ;
            }
    @endphp
    @permission($data['permission'])
    <form id="customer_form" class="kt-form" method="post" action="{{ action('Sales\CustomerController@store',isset($id)?$id:"") }}" enctype="multipart/form-data">
        @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="form-group-block row">
                    <div class="col-lg-6">
                        <div class="erp-page--title">
                            {{isset($code)?$code:""}}
                        </div>
                        <div>
                            @if(isset($customer_account_id))
                                Account Id: <b>{{$customer_account_id}}</b>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group-block row">
                            <label class="col-lg-6 erp-col-form-label"> Name: <span class="required"> * </span></label>
                            <div class="col-lg-6">
                                <input type="text" name="customer_name" class="form-control erp-form-control-sm medium_text" value="{{isset($name)?$name:""}}">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-6 erp-col-form-label">Local Name:</label>
                            <div class="col-lg-6">
                                <input type="text" onkeyup="arabicValue(customer_local_name)" dir="rtl" name="customer_local_name" class="form-control erp-form-control-sm medium_text" value="{{isset($local_name)?$local_name:""}}">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-6 erp-col-form-label">Customer Type: <span class="required"> * </span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2 form-group">
                                    <select class="form-control erp-form-control-sm kt-select2" id="customer_type" name="customer_type">
                                        <option value="0">Select</option>
                                        @foreach($data['type'] as $list_type)
                                            @php $select_type = isset($type)?$type:0; @endphp
                                            <option value="{{$list_type->customer_type_id}}" {{$list_type->customer_type_id == $select_type ?"selected":""}}>{{$list_type->customer_type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div style="
                                background: #f0f8ff;
                                padding: 5px;
                                margin-bottom: 10px;
                            ">
                            <div class="row form-group-block">
                                <div class="col-lg-6">
                                    <label class="erp-col-form-label p-0">Membership Type:</label>
                                    <div class="erp-select2 form-group">
                                        <select class="form-control erp-form-control-sm kt-select2" id="membership_type_id" name="membership_type_id">
                                            <option value="0">Select</option>
                                            @foreach($data['membership'] as $membership)
                                                <option value="{{$membership->membership_type_id}}" {{$membership->membership_type_id == $membership_type ?"selected":""}}>{{$membership->membership_type_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="erp-col-form-label p-0">Card No#</label>
                                    <input type="text" name="card_number" class="form-control erp-form-control-sm card_number" value="{{isset($card_number)?$card_number:""}}">
                                </div>
                            </div>
                            <div class="row form-group-block">
                                <div class="col-lg-6">
                                    <label class="erp-col-form-label p-0">Issue Date</label>
                                    <div class="input-group date">
                                        <input type="text" name="issue_date" class="issue_date form-control erp-form-control-sm c-date-p" readonly value="{{isset($issue_date)?$issue_date:""}}"/>
                                        <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar"></i>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="erp-col-form-label p-0">Expiry Date</label>
                                    <div class="input-group date">
                                        <input type="text" name="expiry_date" class="expiry_date form-control erp-form-control-sm c-date-p" readonly value="{{isset($expiry_date)?$expiry_date:""}}"/>
                                        <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar"></i>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group-block">
                                <div class="col-lg-6">
                                    <label class="erp-col-form-label p-0">Opening Loyalty Point:</label>
                                    <input readonly type="text" name="loyalty_opnening" class="form-control erp-form-control-sm loyalty_opnening" value="{{isset($loyalty_opnening)?$loyalty_opnening:""}}">
                                </div>
                            </div>
                            <div class="row form-group-block">
                                <label class="col-lg-4 erp-col-form-label">Membership Status:</label>
                                <div class="col-lg-2">
                                <span class="kt-switch kt-switch--sm kt-switch--icon" style="position: relative;top: 3px;">
                                    <label>
                                        <input type="checkbox" name="member_status" {{ $member_status == 1?"checked":""}}>
                                        <span></span>
                                    </label>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group-block row">
                            <label class="col-lg-6 erp-col-form-label">Active:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($status)?$status:0; @endphp
                                            <input type="checkbox" name="customer_entry_status" {{ $entry_status == 1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="customer_entry_status" checked>
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-6 erp-col-form-label">Default Customer:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @php $default_customer = isset($default_customer)?$default_customer:0; @endphp
                                        <input type="checkbox" name="customer_default_customer" {{ $default_customer == 1?"checked":""}}>
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label"></label>
                            <div class="col-lg-6">
                                @php
                                    $image_url = isset($image)?'/images/'.$image:"";
                                @endphp
                                <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                    @if($image_url)
                                        <div class="kt-avatar__holder" style="background-image: url({{$image_url}})"></div>
                                    @else
                                        <div class="kt-avatar__holder" style="background-image: url(/assets/media/custom/select_image.png)"></div>
                                    @endif
                                    <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change image">
                                        <i class="fa fa-pen"></i>
                                        <input type="file" name="customer_image" accept="image/png, image/jpg, image/jpeg">
                                    </label>
                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel image">
                                            <i class="fa fa-times"></i>
                                        </span>
                                </div>
                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                            </div>
                        </div>
                    </div>
                </div>{{-- end row--}}
                <ul class="erp-main-nav nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-primary" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#generalinfo" role="tab">General Information</a>
                    </li>
                    {{--<li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#aging" role="tab">Aging & Accounts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#bank" role="tab">Bank Detail</a>
                    </li>
                    --}}{{--<li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#credit_limits" role="tab">Debit Limits</a>
                    </li>--}}{{--
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#subcustomer" role="tab">Contact Persons</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#subcustomers" role="tab">Sub Customers</a>
                    </li>--}}
                </ul>
                <div class="tab-content">

                    <div class="tab-pane active" id="generalinfo" role="tabpanel">

                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Referenced By:</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2 form-group">
                                            <select class="form-control erp-form-control-sm kt-select2" id="referenced_by" name="referenced_by">
                                                <option value="0">Select</option>
                                                @foreach($data['refrence'] as $refrence)
                                                @if ($case == 'new')
                                                    <option value="{{$refrence->customer_id}}">{{$refrence->customer_name}}</option>
                                                @else
                                                    @php $select_type = isset($type)?$type:0; @endphp
                                                    <option value="{{$refrence->customer_id}}" {{$refrence->customer_id == $select_type ?"selected":""}}>{{$refrence->customer_name}}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Home Delivery: <span class="required"> * </span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2 form-group">
                                            <select class="form-control erp-form-control-sm kt-select2" id="home_delivery" name="home_delivery">
                                                <option value="0">Select</option>
                                                {{-- @foreach($data['type'] as $list_type)
                                                    @php $select_type = isset($type)?$type:0; @endphp
                                                    <option value="{{$list_type->customer_type_id}}" {{$list_type->customer_type_id == $select_type ?"selected":""}}>{{$list_type->customer_type_name}}</option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <div class="form-group-block row">
                                    <label class="col-lg-3 erp-col-form-label">Customer Address:</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" rows="2" name="customer_address" class="form-control erp-form-control-sm double_text">{{isset($address)?$address:""}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="form-group-block row">
                                    <div class="col-lg-12">
                                        <div class="form-group-block row">
                                            <label class="col-lg-6 erp-col-form-label">Delivery Address:</label>
                                            <div class="col-lg-6">
                                                <textarea type="text" rows="4" name="customer_delivery_address" class="form-control erp-form-control-sm double_text">{{isset($delivery_address)?$delivery_address:""}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Latitude :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_latitude" class="form-control erp-form-control-sm large_no validNumber text-left" value="{{isset($latitude)?$latitude:""}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Longitude :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_longitude" class="form-control erp-form-control-sm large_no validNumber text-left" value="{{isset($longitude)?$longitude:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="form-group-block row">
                                    <div class="col-lg-12">
                                        <div class="form-group-block row">
                                            <label class="col-lg-6 erp-col-form-label">Billing Address:</label>
                                            <div class="col-lg-6">
                                                <textarea type="text" rows="4" name="customer_billing_address" class="form-control erp-form-control-sm double_text">{{isset($billing_address)?$billing_address:""}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Latitude :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_latitude" class="form-control erp-form-control-sm large_no validNumber text-left" value="{{isset($latitude)?$latitude:""}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Longitude :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_longitude" class="form-control erp-form-control-sm large_no validNumber text-left" value="{{isset($longitude)?$longitude:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">City : <span class="required">*</span></label>
                                    <div class="col-lg-6" >
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm" id="city_id" name="city_id">
                                                <option value="0">Select</option>
                                                @foreach($data['city'] as $country)
                                                    <optgroup label="{{$country->country_name}}">
                                                        @foreach($country->country_cities as $city)
                                                            @php $select_city_id = isset($city_id)?$city_id:0; @endphp
                                                            <option value="{{$city->city_id}}" {{$city->city_id == $select_city_id ?"selected":""}}>{{$city->city_name}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Area / Region :</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm" id="customer_area_id" name="customer_area_id">
                                                <option value="0">Select</option>
                                                @if($case == 'edit')
                                                    @php $region = $region_id ?? 0; @endphp
                                                    @foreach($data['areas'] as $area)
                                                        <option value="{{ $area->area_id }}" @if($region == $area->area_id) selected @endif>{{ $area->area_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Zip Code :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_zip_code" class="form-control erp-form-control-sm large_no validNumber text-left" value="{{isset($zip_code)?$zip_code:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Contact Person Name:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_contact_person_name" class="form-control erp-form-control-sm small_text" value="{{isset($contact_person)?$contact_person:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Contact Person Mobile No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_contact_person_mobile_no" class="form-control erp-form-control-sm mob_no validNumber text-left" value="{{isset($contact_person_mobile)?$contact_person_mobile:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Branch: <span class="required"> * </span></label>

                                    <div class="col-lg-6">
                                        <div class="erp-select2 form-group">
                                            <select class="form-control kt-select2 erp-form-control-sm tag-select2" multiple id="customer_branch_id" name="customer_branch_id[]">
                                                @if(isset($customer_branches))
                                                @php $col = []; @endphp
                                                @foreach($customer_branches as $branch)
                                                @php array_push($col,$branch->branch_id); @endphp
                                                @endforeach
                                                @foreach($data['branch'] as $branch)
                                                <option value="{{$branch->branch_id}}" {{ (in_array($branch->branch_id, $col)) ? 'selected' : '' }}>{{$branch->branch_name}}</option>
                                                @endforeach
                                                @else
                                                @foreach($data['branch'] as $branch)
                                                <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">PO Box :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_po_box" class="form-control erp-form-control-sm mob_no validNumber text-left"  value="{{isset($po_box)?$po_box:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Phone No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_phone_1" class="form-control erp-form-control-sm mob_no validNumber text-left" value="{{isset($phone_1)?$phone_1:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Mobile No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_mobile_no" class="form-control erp-form-control-sm mob_no validNumber text-left"  value="{{isset($mobile_no)?$mobile_no:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">FAX  :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_fax" class="form-control erp-form-control-sm mob_no validNumber text-left" value="{{isset($fax)?$fax:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Whatsapp No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_whatapp_no" class="form-control erp-form-control-sm mob_no validNumber text-left" value="{{isset($whatapp_no)?$whatapp_no:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Email :</label>
                                    <div class="col-lg-6">
                                        <input type="email" name="customer_email" class="form-control erp-form-control-sm small_text" value="{{isset($email)?$email:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Website :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_website" class="form-control erp-form-control-sm small_text" value="{{isset($website)?$website:""}}">

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Reference Code :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="customer_reference_code" maxlength="50" class="form-control erp-form-control-sm" value="{{isset($reference_code)?$reference_code:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <div class="form-group-block row">
                                    <label class="col-lg-3 erp-col-form-label">Remarks:</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" rows="2" name="customer_remarks" class="form-control erp-form-control-sm double_text">{{isset($remarks)?$remarks:""}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                    </div>{{--tabend--}}

                    {{--@include('sales.customer.tabs')--}}
                </div>
            </div>
        </div>
    </div>
    </form>
    <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <script>
        $('#city_id').on('change',function(e){
            var city_id = $(this).val();
            if(city_id != "0"){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : '{{ route("getAreaByCityId") }}',
                    method : 'POST',
                    data : {"city_id" : city_id},
                    async : false,
                    beforeSend : function(){
                        $('body').addClass('pointerEventsNone');
                    },
                    success : function(response,status){
                        $('body').removeClass('pointerEventsNone');
                        $('#customer_area_id').html('');
                        if(response.status == 'success'){
                            var areas = response.data;
                            var option = '';
                            option += '<option value="0">Select</option>';
                            areas.forEach((el) => {
                                option += '<option value="'+ el.area_id +'">'+el.area_name+'</option>';
                            });
                            $('#customer_area_id').append(option);
                        }else{
                            toastr.error('No Areas In This City');
                        }
                    },
                    error: function(response,status) {
                        $('body').removeClass('pointerEventsNone');
                        toastr.error(response.responseJSON.message);
                    },
                });
            }
        });
    </script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/customer.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        $('#selectItemsBtn').on('click',function (e) {
            e.preventDefault();
            var selected=[];
            $('#selectItems :selected').each(function(){
                selected.push($(this).val());
            });
            var url = ""
            for(var i=0;selected.length>i;i++){
                url += "/"+selected[i];
            }
            if(url == ""){
                alert("Please Select Items");
                return false;
            }
            var data_url = '/select-items'+url;

            $('#kt_modal_KTDatatable_local').modal('show').find('.modal-content').load(data_url);
            $('.modal-dialog').draggable({
                handle: ".modal-header"
            });

        })


        var arr_text_Field = [
            // keys = id, fieldClass, message, readonly(boolean), require(boolean)
            {
                'id':'contactp_dtl_name',
                'fieldClass':'moveIndex medium_text',
                'require':true,
                'message':'Enter Name'
            },{
                'id':'contactp_dtl_cont_no',
                'fieldClass':'moveIndex mob_no validNumber text-left',
            },
            {
                'id':'contactp_dtl_address',
                'fieldClass':'moveIndex double_text',
            }
        ];
        var  arr_hidden_field = ['contactp_dtl_id'];

        var arr_customer_text_Field = [
            {
                'id':'customer_name',
                'fieldClass':'moveIndex medium_text field_readonly',
                'require':true,
                'message':'Enter Name'
            },{
                'id':'customer_contact',
                'fieldClass':'moveIndex mob_no validNumber text-left field_readonly',
            },{
                'id':'customer_address',
                'fieldClass':'moveIndex double_text field_readonly',
            }
        ];
        var  arr_customer_hidden_field = ['customer_id'];




function sendWhatsAppMessage() {

var button = document.getElementById("whatsappmessagebtn");
if (button) {
    buttonIcons = button.innerHTML;
    button.disabled = true;
    button.textContent = 'Sending..';
}

// var cust_code = $('#customer_id').val();
var cust_code = @json($id);
// var amount = $('#pro_tot').val();
var title = @json($data['page_data']['title']);

 console.log(cust_code);
//  console.log(amount);
 console.log(title);


$.ajax({
    url: '/customer/fetch-customer-info',
    type: 'GET',
    data: {
        cust_code: cust_code
    },
    success: function(response) {
        const data = response;

        if (!data || !data.phone) {
            toastr.error("Customer phone number not found");
            if (button) {
                button.innerHTML = buttonIcons;
                button.disabled = false;
            }
            return;
        }

        var to = formatPakPhoneNumber(data.phone);
        const invoiceNumber = @json($code);
        // const invoiceDate = EntryDate;

        // console.log(to);
        // return false;

        to = formatPakPhoneNumber('03097274927');
        // to = formatOmanPhoneNumber('9156 4500');

        // generatePdfAttachment(CODE, 'SI', EntryDate, ReportType, CompCode, function(filePath) {
        //     if (!filePath) {
        //         console.error("Error generating PDF");
        //         filePath = '';
        //     }
        // });

                filePath = '';
        const message = `Thank you for your valued order\n(*Order no # ${invoiceNumber}*.\n\nThank you and regards,\nwww.deploylogics.com`;

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/customer/whatsapp-message-sending',
                type: 'POST',
                data: {
                    to: to,
                    message: message,
                    filePath: filePath,
                    invoiceNumber: invoiceNumber,
                    title: title,
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        toastr.success("WhatsApp message sent at " + to);
                        if (button) {
                        button.innerHTML = buttonIcons + '<i class="icon wb-check" aria-hidden="true"></i>';
                        button.disabled = false;
                        }
                    } else {
                        toastr.error("Failed to send message check connection");
                        if (button) {
                        button.innerHTML = buttonIcons;
                        button.disabled = false;
                        }
                    }

                },
                error: function() {
                    toastr.error("Failed to send WhatsApp message.");
                    if (button) {
                    button.innerHTML = buttonIcons;
                    button.disabled = false;
                    }
                }
            });
    },
    error: function() {
        toastr.error("Failed to fetch customer data.");
        if (button) {
        button.innerHTML = buttonIcons;
        }
    }

});

}




    </script>
    <script src="{{ asset('js/pages/js/sale/customer_row_repeat.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>  --}}
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
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

        $('.issue_date, .issue_date_validate').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows
        });
        $('.expiry_date, .expiry_date_validate').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows
        });
    </script>
@endsection

