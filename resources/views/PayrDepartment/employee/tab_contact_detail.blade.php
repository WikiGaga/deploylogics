@php
    if($case == 'new'){
        $local_country_id = 0;
        $permanent_city_id = 0;
        $permanent_country_id = 0;
    }
    if($case == 'edit'){
        $local_address_1 = $current->employee_local_address_1;
        $local_address_2 = $current->employee_local_address_2;
        $local_country_id = $current->employee_local_country_id;
        $local_city_id = $current->employee_local_city_id;
        $local_postal_code = $current->employee_local_postal_code;
        $local_mobile_no = $current->employee_local_mobile_no;
        $local_phone_no = $current->employee_local_phone_no;
        $local_personal_email = $current->employee_local_personal_email;
        $local_official_email = $current->employee_local_official_email;
        $local_emergency_contact_name = $current->employee_local_emergency_contact_name;
        $local_emergency_contact_phone = $current->employee_local_emergency_contact_phone;
        $permanent_address_1 = $current->employee_permanent_address_1;
        $permanent_address_2 = $current->employee_permanent_address_2;
        $permanent_country_id = $current->employee_permanent_country_id;
        $permanent_city_id = $current->employee_permanent_city_id;
        $permanent_postal_code = $current->employee_permanent_postal_code;
        $permanent_mobile_no = $current->employee_permanent_mobile_no;
        $permanent_phone_no = $current->employee_permanent_phone_no;
        $permanent_personal_email = $current->employee_permanent_personal_email;
        $permanent_official_email = $current->employee_permanent_official_email;
        $permanent_emergency_contact_name = $current->employee_permanent_emergency_contact_name;
        $permanent_emergency_contact_phone = $current->employee_permanent_emergency_contact_phone;
    }
@endphp
<div class="row form-group-block">
    <div class="col-lg-12">
        <i style="font-size: 1.3rem;color: #5578eb;font-weight: 500">Local Contact info</i>
    </div>
</div>
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Address 1:</label>
            <div class="col-lg-6">
                <textarea type="text" rows="2" name="employee_local_address_1" maxlength="255" class="kt-margin-b-10 form-control erp-form-control-sm">{{isset($local_address_1)?$local_address_1:""}}</textarea>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Address 2:</label>
            <div class="col-lg-6">
                <textarea type="text" rows="2" name="employee_local_address_2" maxlength="255" class="kt-margin-b-10 form-control erp-form-control-sm">{{isset($local_address_2)?$local_address_2:""}}</textarea>
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-bloc country_city_block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Country:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm country" name="employee_local_country_id">
                        <option value="0">Select</option>
                        @foreach($data['country'] as $country)
                            <option value="{{$country->country_id}}" {{$local_country_id == $country->country_id?"selected":""}}>{{ucfirst(strtolower($country->country_name))}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">City:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm city" name="employee_local_city_id">
                        <option value="0">Select</option>
                        @if($case == 'edit')
                            @foreach($data['local_cities'] as $city)
                                <option value="{{$city->city_id}}" {{$local_city_id == $city->city_id?"selected":""}}>{{ucfirst(strtolower($city->city_name))}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Postal Code:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($local_postal_code)?$local_postal_code:""}}" name="employee_local_postal_code" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Mobile No:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($local_mobile_no)?$local_mobile_no:""}}" name="employee_local_mobile_no" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Phone No:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($local_phone_no)?$local_phone_no:""}}" name="employee_local_phone_no" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Personal Email:</label>
            <div class="col-lg-6">
                <input type="email" maxlength="100" value="{{isset($local_personal_email)?$local_personal_email:""}}" name="employee_local_personal_email" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Official Email:</label>
            <div class="col-lg-6">
                <input type="email" maxlength="100" value="{{isset($local_official_email)?$local_official_email:""}}" name="employee_local_official_email" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Emergency Contact Name:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($local_emergency_contact_name)?$local_emergency_contact_name:""}}" name="employee_local_emergency_contact_name" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Emergency Contact Phone:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($local_emergency_contact_phone)?$local_emergency_contact_phone:""}}" name="employee_local_emergency_contact_phone" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
{{-- /local contact info --}}
<hr>
<div class="row form-group-block">
    <div class="col-lg-12">
        <i style="font-size: 1.3rem;color: #5578eb;font-weight: 500">Permanent Contact info</i>
    </div>
</div>
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Address 1:</label>
            <div class="col-lg-6">
                <textarea type="text" rows="2" name="employee_permanent_address_1" maxlength="255" class="kt-margin-b-10 form-control erp-form-control-sm">{{isset($permanent_address_1)?$permanent_address_1:""}}</textarea>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Address 2:</label>
            <div class="col-lg-6">
                <textarea type="text" rows="2" name="employee_permanent_address_2" maxlength="255" class="kt-margin-b-10 form-control erp-form-control-sm">{{isset($permanent_address_2)?$permanent_address_2:""}}</textarea>
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block country_city_block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Country:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm country" name="employee_permanent_country_id" >
                        <option value="0">Select</option>
                       @foreach($data['country'] as $country)
                            <option value="{{$country->country_id}}" {{ $country->country_id == $permanent_country_id ? 'selected' : '' }}>{{ucfirst(strtolower($country->country_name))}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">City:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm city" name="employee_permanent_city_id" >
                        <option value="0">Select</option>
                        @if($case == 'edit')
                            @foreach($data['permanent_cities'] as $city)
                                <option value="{{$city->city_id}}" {{$permanent_city_id == $city->city_id?"selected":""}}>{{ucfirst(strtolower($city->city_name))}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Postal Code:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($permanent_postal_code)?$permanent_postal_code:""}}" name="employee_permanent_postal_code" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Mobile No:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($permanent_mobile_no)?$permanent_mobile_no:""}}" name="employee_permanent_mobile_no" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Phone No:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($permanent_phone_no)?$permanent_phone_no:""}}" name="employee_permanent_phone_no" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Personal Email:</label>
            <div class="col-lg-6">
                <input type="email" maxlength="100" value="{{isset($permanent_personal_email)?$permanent_personal_email:""}}" name="employee_permanent_personal_email" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Official Email:</label>
            <div class="col-lg-6">
                <input type="email" maxlength="100" value="{{isset($permanent_official_email)?$permanent_official_email:""}}" name="employee_permanent_official_email" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Emergency Contact Name:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($permanent_emergency_contact_name)?$permanent_emergency_contact_name:""}}" name="employee_permanent_emergency_contact_name" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Emergency Contact Phone:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($permanent_emergency_contact_phone)?$permanent_emergency_contact_phone:""}}" name="employee_permanent_emergency_contact_phone" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
