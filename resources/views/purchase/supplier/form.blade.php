@extends('layouts.template')
@section('title', 'Vendor')

@section('pageCSS')
@endsection
@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['supplier_code'];
        }
        if($case == 'edit' || $case == 'view'){
            $id = $data['current']->supplier_id;
            $code = $data['current']->supplier_code;
            $name = $data['current']->supplier_name;
            $supplier_cr_no = $data['current']->supplier_cr_no;
            $local_name = $data['current']->supplier_local_name;
            $type = $data['current']->supplier_type;
            $contact_type = $data['current']->contact_type_id;
            $status = $data['current']->supplier_entry_status;
            $image = $data['current']->supplier_image;
            $address = $data['current']->supplier_address;
            $city_id = $data['current']->city_id;
            $zip_code = $data['current']->supplier_zip_code;
            $longitude = $data['current']->supplier_longitude;
            $latitude = $data['current']->supplier_latitude;
            $contact_person = $data['current']->supplier_contact_person;
            $contact_person_mobile = $data['current']->supplier_contact_person_mobile;
            $designation = $data['current']->supplier_contact_person_designation;
            $po_box = $data['current']->supplier_po_box;
            $phone_1 = $data['current']->supplier_phone_1;
            $mobile_no = $data['current']->supplier_mobile_no;
            $fax = $data['current']->supplier_fax;
            $whatapp_no = $data['current']->supplier_whatapp_no;
            $email = $data['current']->supplier_email;
            $website = $data['current']->supplier_website;
            $reference_code = $data['current']->supplier_reference_code;
            $supplier_account_id = $data['current']->supplier_account_id;
            $payment_terms = $data['current']->payment_term_id;
            $no_of_days = $data['current']->supplier_ageing_terms_value;
            $credit_period = $data['current']->supplier_credit_period;
            $tax_no = $data['current']->supplier_tax_no;
            $supplier_ntn_no = $data['current']->supplier_ntn_no;
            $credit_limit = $data['current']->supplier_credit_limit;
            $debit_limit = $data['current']->supplier_debit_limit;
            $tax_rate = $data['current']->supplier_tax_rate;
            $tax_status = $data['current']->supplier_tax_status;
            $cheque_beneficry_name = $data['current']->supplier_cheque_beneficry_name;
            $mode_of_payment = $data['current']->supplier_mode_of_payment;
            $can_scale = $data['current']->supplier_can_scale;
            $bank_name = $data['current']->supplier_bank_name;
            $supplier_gst_no = $data['current']->supplier_gst_no;
            $bank_account_no = $data['current']->supplier_bank_account_no;
            $bank_account_title = $data['current']->supplier_bank_account_title;
            $sub_suppliers = isset($data['current']->sub_supplier) ? $data['current']->sub_supplier : [] ;
            $acc_suppliers = isset($data['current']->supplier_acc) ? $data['current']->supplier_acc : [] ;
            $supplier_branches = isset($data['current']->supplier_branches) ? $data['current']->supplier_branches : [] ;
        }
        $url = "";
        if($case == 'edit' || $case == 'new'){
            $url = action('Purchase\SupplierController@store',isset($id)?$id:"");
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="supplier_form" class="kt-form" method="post" action="{{ $url }}" enctype="multipart/form-data">
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
                            <div>
                                @if(isset($supplier_account_id))
                                    Account Id: <b>{{$supplier_account_id}}</b>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="form-group-block row">
                                <label class="col-lg-5 erp-col-form-label"> Name:<span class="required">* </span></label>
                                <div class="col-lg-7">
                                    <input type="text" name="supplier_name" maxlength="150" class="form-control erp-form-control-sm" value="{{isset($name)?$name:""}}">
                                </div>
                            </div>
                            <div class="form-group-block row">
                                <label class="col-lg-5 erp-col-form-label">Local Name:</label>
                                <div class="col-lg-7">
                                    <input type="text" maxlength="150" dir="auto" name="supplier_local_name" class="form-control erp-form-control-sm" value="{{isset($local_name)?$local_name:""}}">
                                </div>
                            </div>
                            <div class="form-group-block row">
                                <label class="col-lg-5 erp-col-form-label"> Supplier Company:<span class="required">* </span></label>
                                <div class="col-lg-7">
                                    <input type="text" name="supplier_company" maxlength="150" class="form-control erp-form-control-sm" value="{{isset($name)?$name:""}}">
                                </div>
                            </div>
                            <div class="form-group-block row">
                                <label class="col-lg-5 erp-col-form-label">Supplier Group:<span class="required">* </span></label>
                                <div class="col-lg-7">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm kt-select2" id="supplier_type" name="supplier_type">
                                            <option value="0">Select</option>
                                            @foreach($data['type'] as $list_type)
                                                @php $select_type = isset($type)?$type:0; @endphp
                                                <option value="{{$list_type->supplier_type_id}}" {{$list_type->supplier_type_id == $select_type ?"selected":""}}>{{$list_type->supplier_type_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group-block row">
                                <label class="col-lg-5 erp-col-form-label">Active:</label>
                                <div class="col-lg-7">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @php $entry_status = isset($status)?$status:0; @endphp
                                            <input type="checkbox" name="supplier_entry_status" {{ $entry_status == 1?"checked":""}} checked>
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="row">
                                <label class="col-lg-5 erp-col-form-label"></label>
                                <div class="col-lg-7">
                                    @php
                                        $image_url = isset($image)?'/images/supplier/'.$image:"";
                                    @endphp
                                    <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                        @if($image_url)
                                            <div class="kt-avatar__holder" style="background-image: url({{$image_url}})"></div>
                                        @else
                                            <div class="kt-avatar__holder" style="background-image: url(/assets/media/custom/select_image.png)"></div>
                                        @endif
                                        <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change image">
                                            <i class="fa fa-pen"></i>
                                            <input type="file" name="supplier_image" accept="image/png, image/jpg, image/jpeg">
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
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#aging" role="tab">Aging & Accounts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#bank" role="tab">Bank Detail</a>
                        </li>
                        {{--<li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#subsupplier" role="tab">Sub-Supplier</a>
                        </li>--}}
                    </ul>
                    <div class="tab-content">

                        <div class="tab-pane active" id="generalinfo" role="tabpanel">
                            <div class="form-group-block row">
                                <div class="col-lg-12">
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Supplier Address:</label>
                                        <div class="col-lg-9">
                                            <textarea type="text" rows="2" maxlength="250" name="supplier_address" class="form-control erp-form-control-sm">{{isset($address)?$address:""}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
                            <div class="form-group-block row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Longitude :</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_longitude" maxlength="15" class="form-control erp-form-control-sm validNumber text-left" value="{{isset($longitude)?$longitude:""}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Latitude:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_latitude" maxlength="15" class="form-control erp-form-control-sm validNumber text-left" value="{{isset($latitude)?$latitude:""}}">
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
                            <div class="form-group-block row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">City :</label>
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
                                        <label class="col-lg-6 erp-col-form-label">Zip Code :</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_zip_code" maxlength="15" class="form-control erp-form-control-sm" value="{{isset($zip_code)?$zip_code:""}}">
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
                            <div class="form-group-block row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Contact Type:</label>
                                        <div class="col-lg-6">
                                            <div class="erp-select2">
                                                <select class="form-control erp-form-control-sm kt-select2" id="contact_type" name="contact_type">
                                                    <option value="0">Select</option>
                                                    @foreach($data['contact'] as $contact)
                                                        @php $select_type = isset($contact_type)?$contact_type:0; @endphp
                                                        <option value="{{$contact->contact_type_id}}" {{$contact->contact_type_id == $select_type ?"selected":""}}>{{$contact->contact_type_name}}</option>
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
                                        <label class="col-lg-6 erp-col-form-label">Contact Person Name:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_contact_person_name" maxlength="150" class="form-control erp-form-control-sm" value="{{isset($contact_person)?$contact_person:""}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Contact Person Mobile No:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_contact_person_mobile_no" maxlength="15" class="form-control erp-form-control-sm" value="{{isset($contact_person_mobile)?$contact_person_mobile:""}}">
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
                            <div class="form-group-block row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Contact Person Designation:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_contact_person_designation" maxlength="50" class="form-control erp-form-control-sm" value="{{isset($designation)?$designation:""}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Branch :</label>

                                        <div class="col-lg-6">
                                            <div class="erp-select2">
                                                <select class="form-control kt-select2 erp-form-control-sm tag-select2" multiple  id="supplier_branch_id" name="supplier_branch_id[]">
                                                    @if(isset($supplier_branches))
                                                        @php $col = []; @endphp
                                                        @foreach($supplier_branches as $branch)
                                                            @php array_push($col,$branch->branch_id); @endphp
                                                        @endforeach
                                                        @foreach($data['branch'] as $branch)
                                                            <option value="{{$branch->branch_id}}" {{ (in_array($branch->branch_id, $col)) ? 'selected' : '' }}>{{$branch->branch_name}}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($data['branch'] as $branch)
                                                            <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
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
                                            <input type="text" name="supplier_po_box" maxlength="15" class="form-control erp-form-control-sm"  value="{{isset($po_box)?$po_box:""}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">FAX  :</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_fax" maxlength="50" class="form-control erp-form-control-sm" value="{{isset($fax)?$fax:""}}">
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
                            <div class="form-group-block row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Phone No:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_phone_1" maxlength="15" class="form-control erp-form-control-sm validNumber text-left" value="{{isset($phone_1)?$phone_1:""}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Mobile No:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_mobile_no" maxlength="15" class="form-control erp-form-control-sm validNumber text-left" value="{{isset($mobile_no)?$mobile_no:""}}">
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
                            <div class="form-group-block row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Whatsapp No:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_whatapp_no" maxlength="15" class="form-control erp-form-control-sm validNumber text-left" value="{{isset($whatapp_no)?$whatapp_no:""}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Email :</label>
                                        <div class="col-lg-6">
                                            <input type="email" name="supplier_email" maxlength="50" class="form-control erp-form-control-sm" value="{{isset($email)?$email:""}}">
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
                            <div class="form-group-block row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">CNIC :</label>
                                        <div class="col-lg-6">
                                            <input type="number" name="supplier_cnic" maxlength="50" class="form-control erp-form-control-sm" value="{{isset($email)?$email:""}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Payment Mode:</label>
                                        <div class="col-lg-6">
                                            <div class="erp-select2">
                                                <select class="form-control erp-form-control-sm kt-select2" id="payment_mode" name="payment_mode">
                                                    <option value="0">Select</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">WHT Type:<span class="required">* </span></label>
                                        <div class="col-lg-6">
                                            <div class="erp-select2">
                                                <select class="form-control erp-form-control-sm kt-select2" id="wht_type" name="wht_type">
                                                    <option wht_rate="" value="0">Select</option>
                                                    @foreach($data['wht'] as $wht)
                                                        @php $wht_type = isset($wht_type)?$wht_type:0; @endphp
                                                        <option wht_rate="{{$wht->wht_type_rate}}" value="{{$wht->wht_type_id}}" {{$wht->wht_type_id == $wht_type ?"selected":""}}>{{$wht->wht_type_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Tax Rate:</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_tax_rate" maxlength="15" class="supplier_tax_rate form-control erp-form-control-sm validNumber readonly" value="{{isset($tax_rate)?$tax_rate:""}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Website :</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_website" maxlength="50" class="form-control erp-form-control-sm" value="{{isset($website)?$website:""}}">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label">Reference Code :</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="supplier_reference_code" maxlength="50" class="form-control erp-form-control-sm" value="{{isset($reference_code)?$reference_code:""}}">

                                        </div>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
                        </div>{{--tabend--}}

                        <div class="tab-pane" id="aging" role="tabpanel">
                            <div id="kt_repeater_3">
                                <div class="form-group-block row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Aging Days:</label>
                                            <div class="col-lg-6">
                                                <div class="input-group erp-select2-sm">
                                                    <select name="supplier_payment_terms"  id="supplier_aging_terms" class="moveIndex kt-select2 width form-control erp-form-control-sm">
                                                        <option value="0">Select</option>
                                                        @foreach($data['payment_terms'] as $payment_term)
                                                            @php $payment_terms_id = isset($payment_terms)?$payment_terms:""; @endphp
                                                            <option value="{{$payment_term->payment_term_id}}" {{$payment_terms_id == $payment_term->payment_term_id?"selected":""}}>{{$payment_term->payment_term_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append" style="width: 33%;">
                                                        <input type="text" name="supplier_ageing_terms_value" maxlength="15" class="form-control erp-form-control-sm validNumber" value="{{isset($no_of_days)?$no_of_days:""}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Mode of Payment:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_mode_of_payment" maxlength="50" class="form-control erp-form-control-sm" value="{{isset($mode_of_payment)?$mode_of_payment:""}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group-block row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Credit Period:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_credit_period" maxlength="15" class="form-control erp-form-control-sm validNumber" value="{{isset($credit_period)?$credit_period:""}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">NTN No:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_ntn_no" maxlength="15" class="form-control erp-form-control-sm" value="{{isset($supplier_ntn_no)?$supplier_ntn_no:""}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">GST No:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_gst_no" maxlength="15" class="form-control erp-form-control-sm" value="{{isset($supplier_gst_no)?$supplier_gst_no:""}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Tax Status:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_tax_status" maxlength="50" class="form-control erp-form-control-sm" value="{{isset($tax_status)?$tax_status:""}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group-block row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Credit Limit:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_credit_limit" maxlength="15" class="form-control erp-form-control-sm validNumber" value="{{isset($credit_limit)?$credit_limit:""}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Debit Limit:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_debit_limit" maxlength="15" class="form-control erp-form-control-sm validNumber" value="{{isset($debit_limit)?$debit_limit:""}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group-block row" style="display:none;">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Beneficiary Name:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_cheque_beneficry_name" class="form-control erp-form-control-sm" value="{{isset($cheque_beneficry_name)?$cheque_beneficry_name:""}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Account No:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_account_no" class="form-control erp-form-control-sm" value="{{isset($account_no)?$account_no:""}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group-block row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">CR NO:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_cr_no" maxlength="150" class="form-control erp-form-control-sm validNumber" value="{{isset($supplier_cr_no)?$supplier_cr_no:""}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Payee Title:</label>
                                            <div class="col-lg-6">
                                                <input type="text" name="supplier_payee_title" maxlength="150" class="form-control erp-form-control-sm validNumber" value="{{isset($supplier_payee_title)?$supplier_payee_title:""}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group-block row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Can Sale:</label>
                                            <div class="col-lg-6">
                                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                <label>
                                                    @php $select_can_scale = isset($can_scale)?$can_scale:0; @endphp
                                                    <input type="checkbox" name="supplier_can_scale" {{$select_can_scale == 1?"checked":""}}>
                                                    <span></span>
                                                 </label>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{--tabend--}}
                        <div class="tab-pane" id="bank" role="tabpanel">
                            <div class="form-group-block row">
                                <div class="col-lg-12">
                                    <div class="form-group-block" style="overflow: auto;">
                                        <table id="SupplierAccountForm" class="ErpFormsm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                                            <thead>
                                            <tr>
                                                <th width="5%" class="text-center">Sr No</th>
                                                <th width="20%" class="text-center">Bank Name</th>
                                                <th width="20%" class="text-center">Account No</th>
                                                <th width="30%" class="text-center">Account Title</th>
                                                <th width="20%" class="text-center">Branch Code</th>
                                                <th width="5%" >Action</th>
                                            </tr>
                                            <tr id="dataEntryFormsm">
                                                <td>
                                                    <input readonly type="text"  class="form-control erp-form-control-sm" id="supplier_account_sr_number">
                                                </td>
                                                <td>
                                                    <select class="form-control erp-form-control-sm supplier_bank_name moveIndexsm" id="supplier_bank_name">
                                                        <option value="0">Select</option>
                                                        @foreach($data['bank'] as $bank)
                                                            <option value="{{ $bank->bank_id }}">{{ $bank->bank_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="supplier_bank_account_no" maxlength="50" type="text" class="moveIndexsm form-control erp-form-control-sm">
                                                </td>
                                                <td>
                                                    <input id="supplier_bank_account_title" maxlength="50" type="text" class="moveIndexsm form-control erp-form-control-sm">
                                                </td>
                                                <td>
                                                    <input id="supplier_bank_iban_no" maxlength="50" type="text" class="moveIndexsm form-control erp-form-control-sm">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" id="addDatasm" class="moveIndexBtnsm moveIndexsm gridBtn btn btn-primary btn-sm">
                                                        <i class="la la-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            </thead>
                                            <tbody id="repeated_datasm">
                                            @if(isset($acc_suppliers))
                                                @foreach($acc_suppliers as $accsupplier)
                                                    <tr>
                                                        <td class="handle">
                                                            <i class="fa fa-arrows-alt-v handle"></i>
                                                            <input type="text" value="{{ $loop->iteration }}" name="pdsm[{{ $loop->iteration }}][sr_no]" title="{{ $loop->iteration }}" class="form-control erp-form-control-sm handle" readonly>
                                                            <input type="hidden" name="pdsm[{{ $loop->iteration }}][supplier_account_id]" data-id="supplier_account_id" value="{{ $accsupplier->supplier_account_id }}" class="supplier_account_id form-control erp-form-control-sm handle" readonly>
                                                        </td>
                                                        <td>
                                                            <select class="form-control erp-form-control-sm supplier_bank_name moveIndexsm" name="pdsm[{{ $loop->iteration }}][supplier_bank_name]">
                                                                <option value="0">Select</option>
                                                                @php $bankName = isset($accsupplier->supplier_bank_name)?$accsupplier->supplier_bank_name:0 @endphp
                                                                @foreach($data['bank'] as $bank)
                                                                    <option value="{{ $bank->bank_id }}" {{ $bank->bank_id ==$bankName?'selected':'' }}>{{ $bank->bank_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="pdsm[{{ $loop->iteration }}][supplier_account_no]" data-id="supplier_account_no"  value="{{ $accsupplier->supplier_account_no }}" title="{{ $accsupplier->supplier_account_no }}" class="form-control erp-form-control-sm moveIndex" maxlength="50"></td>
                                                        <td><input type="text" name="pdsm[{{ $loop->iteration }}][supplier_account_title]" data-id="supplier_account_title" value="{{ $accsupplier->supplier_account_title }}" title="{{ $accsupplier->supplier_account_title }}" class="form-control erp-form-control-sm moveIndex" maxlength="50"></td>
                                                        <td><input type="text" name="pdsm[{{ $loop->iteration }}][supplier_iban_no]" data-id="supplier_iban_no" value="{{ $accsupplier->supplier_iban_no }}" title="{{ $accsupplier->supplier_iban_no }}" class="form-control erp-form-control-sm moveIndex" maxlength="50"></td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group btn-group-sm" role="group">
                                                                <button type="button" class="btn btn-danger gridBtn delDatasm">
                                                                    <i class="la la-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
                        </div>{{--tabend--}}
                        <div class="tab-pane" id="subsupplier" role="tabpanel">
                            <div class="form-group-block row">
                                <div class="col-lg-12">
                                    <div class="form-group-block" style="overflow: auto;">
                                        <table id="subSupplierForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                                            <thead>
                                            <tr>
                                                <th width="10%" class="text-center">Sr No</th>
                                                <th width="20%" class="text-center">Name</th>
                                                <th width="15%" class="text-center">Contact No</th>
                                                <th width="45%" class="text-center">Address</th>
                                                <th width="10%" >Action</th>
                                            </tr>
                                            <tr id="dataEntryForm">
                                                <td>
                                                    <input readonly type="text"  class="form-control erp-form-control-sm" id="supplier_sr_number">
                                                    <input readonly type="hidden" id="supplier_dtl_id" class="supplier_dtl_id form-control erp-form-control-sm">
                                                </td>
                                                <td>
                                                    <input id="supplier_dtl_name" type="text" maxlength="150" class="moveIndex form-control erp-form-control-sm">
                                                </td>
                                                <td>
                                                    <input id="supplier_dtl_cont_no" type="text" maxlength="15" class="moveIndex form-control erp-form-control-sm validNumber text-left">
                                                </td>
                                                <td>
                                                    <input id="supplier_dtl_address" type="text" maxlength="150" class="moveIndex form-control erp-form-control-sm">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" id="addData" class="moveIndexBtn moveIndex gridBtn btn btn-primary btn-sm">
                                                        <i class="la la-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            </thead>
                                            <tbody id="repeated_data">
                                            @if(isset($sub_suppliers))
                                                @foreach($sub_suppliers as $subsupplier)
                                                    <tr>
                                                        <td class="handle">
                                                            <i class="fa fa-arrows-alt-v handle"></i>
                                                            <input type="text" value="{{ $loop->iteration }}" name="pd[{{ $loop->iteration }}][sr_no]" title="{{ $loop->iteration }}" class="form-control erp-form-control-sm handle" readonly>
                                                            <input type="hidden" name="pd[{{ $loop->iteration }}][supplier_dtl_id]" data-id="supplier_dtl_id" value="{{ $subsupplier->supplier_dtl_id }}" class="supplier_dtl_id form-control erp-form-control-sm handle" readonly>
                                                        </td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][supplier_dtl_name]" data-id="supplier_dtl_name" value="{{ $subsupplier->supplier_dtl_name }}" title="{{ $subsupplier->supplier_dtl_name }}" class="form-control erp-form-control-sm moveIndex" maxlength="150"></td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][supplier_dtl_cont_no]" data-id="supplier_dtl_cont_no"  value="{{ $subsupplier->supplier_dtl_cont_no }}" title="{{ $subsupplier->supplier_det_contact_no }}" class="form-control erp-form-control-sm moveIndex validNumber text-left" maxlength="15"></td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][supplier_dtl_address]" data-id="supplier_dtl_address" value="{{ $subsupplier->supplier_dtl_address }}" title="{{ $subsupplier->supplier_det_address }}" class="form-control erp-form-control-sm moveIndex" maxlength="150"></td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group btn-group-sm" role="group">
                                                                <button type="button" class="btn btn-danger gridBtn delData">
                                                                    <i class="la la-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>{{-- end row--}}
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
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/supplier.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/supplier_account.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var arr_text_Field = [
            // keys = id, fieldClass, message, readonly(boolean), require(boolean)
            {
                'id':'supplier_dtl_name',
                'fieldClass':'moveIndex',
                'require':true,
                'message':'Enter Name'
            },{
                'id':'supplier_dtl_cont_no',
                'fieldClass':'moveIndex validNumber text-left',
            },{
                'id':'supplier_dtl_address',
                'fieldClass':'moveIndex',
            }
        ];
        var  arr_hidden_field = ['supplier_dtl_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>

    <script !src="">
        $(document).on('change','#wht_type',function(){
            var wht_rate = $(this).find('option:selected').attr('wht_rate');
            $('.supplier_tax_rate').val(wht_rate);
        })
    </script>
@endsection

