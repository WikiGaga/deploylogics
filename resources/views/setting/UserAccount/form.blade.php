@extends('layouts.layout')
@section('title', 'User')

@section('pageCSS')
    <style>
        .pass_readonly {
            background: #e0e0e0 !important;
        }
        tr.focus_selected_tr td{
            border-top: 1px solid #ebedf2 !important;
            border-bottom: 1px solid #ebedf2 !important;
        }
    </style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $user_type = 'erp';
        }
        if($case == 'edit'){
            $id = $data['current']->id;
            $name = $data['current']->name;
            $email = $data['current']->email;
            $password = $data['current']->password;
            $password_pos = $data['current']->password_pos;
            $mobile_no = $data['current']->mobile_no;
            $user_branch = $data["current"]->branch_id;
            $expiry_date = $data['current']->expiry_date;
            $expiry_account = $data['current']->expiry_account;
            $apply_time = $data['current']->apply_time;
            $start_date = $data['current']->start_date;
            $end_date = $data['current']->end_date;
            $apply = $data['current']->apply;
            $apply_warehouse = $data['current']->apply_warehouse;
            $ip_address = $data['current']->ip_address;
            $ip_address_apply = $data['current']->ip_address_apply;
            $administrator = $data['current']->administrator;
            $central_rate = $data['current']->central_rate;
            $two_step_verification = $data['current']->two_step_verification;
            $two_step_verification_type = $data['current']->two_step_verification_type;
            $status = $data['current']->user_entry_status;
            $picture = $data['current']->image_url;
            $signature = $data['current']->degital_signature_url;
            $user_type = $data['current']->user_type;
            if($data['current']->users_type_acco != null){
                $customer = $data['current']->users_type_acco->customer != null ? $data['current']->users_type_acco->customer : "";
                if($customer != "" && $data['current']->user_type == 'customer'){
                    $customer_id = $customer['customer_id'];
                    $customer_name = $customer['customer_name'];
                }
            }
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="user_account_form" class="kt-form"  enctype='multipart/form-data' method="post" action="{{ action('Setting\UserAccountController@store', isset($id)?$id:'') }}">
    @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">User Name:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" name="name" id="name" value="{{isset($name)?$name:''}}" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                            @if($case == 'new')
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Password:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="password" name="password" class="form-control erp-form-control-sm short_text">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Email:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="email" name="email" value="{{isset($email)?$email:''}}" class="form-control erp-form-control-sm small_text">
                                    </div>
                                </div>
                            </div>
                            @if($case == 'new')
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">POS Password:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="password" name="password_pos" class="form-control erp-form-control-sm short_text">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="form-group-block row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">User Type:<span class="required">*</span></label>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 form-control-sm" id="user_type" name="user_type">
                                                        @foreach($data['user_types'] as $userType)
                                                            <option value="{{$userType->constants_key}}" {{$userType->constants_key == $user_type ? 'selected' : ''}}>{{$userType->constants_value}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row customer_block" style="display: {{$user_type == 'customer'?"":"none"}}">
                                    <label class="col-lg-6 erp-col-form-label">Customer: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp_form___block">
                                            <div class="input-group open-modal-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                                </div>
                                                <input type="text" id="customer_name" value="{{isset($customer_name)?$customer_name:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','customerHelp')}}" autocomplete="off" name="customer_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                                <input type="hidden" id="customer_id" name="customer_id" value="{{isset($customer_id)?$customer_id:''}}"/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                    <i class="la la-search"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-block row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Default Branch:<span class="required">*</span></label>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 form-control-sm" id="kt_select2_1" name="user_branch">
                                                        <option value="">Select</option>
                                                        @php $user_branch = isset($user_branch)?$user_branch:''@endphp
                                                        @foreach($data['branches'] as $branch)
                                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id == $user_branch ? 'selected' : ''}}>{{$branch->branch_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-block row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Optional Branches:</label>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    @php $col = []; @endphp
                                                    @foreach($data['pivot_optional_branch'] as $optional_branch)
                                                        @php  array_push($col,$optional_branch->branch_id); @endphp
                                                    @endforeach
                                                    <select class="form-control kt-select2 erp-form-control-sm tag-select2" multiple name="optional_branches[]">
                                                        <option value="">Select</option>
                                                        @foreach($data['branches'] as $branch)
                                                            <option value="{{$branch->branch_id}}" {{ (in_array($branch->branch_id, $col)) ? 'selected' : '' }}>{{$branch->branch_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <label class="col-lg-6 erp-col-form-label">Mobile No.:</label>
                                                    <div class="col-lg-6">
                                                        <input type="text" name="mobile_no" value="{{isset($mobile_no)?$mobile_no:''}}" class="form-control erp-form-control-sm mob_no validNumber text-left">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Picture:</label>
                                    <div class="col-lg-6">
                                        <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1" >
                                            @if($case == 'edit')
                                                @php $picture = isset($picture)?'/images/'.$picture:""; @endphp
                                                <div class="kt-avatar__holder" style="background-image: url({{$picture}})"></div>
                                            @else
                                                <div class="kt-avatar__holder"  style="background-image: url(/assets/media/project-logos/7.png)"></div>
                                            @endif
                                            <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                                <i class="fa fa-pen"></i>
                                                <input type="file" name="user_image" accept="image/png, image/jpg, image/jpeg">
                                            </label>
                                            <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
                                                <i class="fa fa-times"></i>
                                            </span>
                                        </div>
                                        <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Expiry Date:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            @if(isset($data['id']))
                                                @php $expiry_date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$expiry_date )))); @endphp
                                            @else
                                                @php $expiry_date =  date('d-m-Y'); @endphp
                                            @endif
                                            <input type="text" class="form-control erp-form-control-sm"  value="{{$expiry_date}}" name="expiry_date" id="kt_datepicker_3" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6 erp-col-form-label">Expiry Account :  </div>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @php $expiry_account = isset($expiry_account)?$expiry_account:'' @endphp
                                                <input type="checkbox" name="expiry_account" {{$expiry_account == 1?'checked':''}}>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6 erp-col-form-label">Apply Time:</div>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                            @php $apply_time = isset($apply_time)?$apply_time:''@endphp
                                                <input type="checkbox" name="apply_time" {{$apply_time ==1?'checked':''}}>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Start Date:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            @if(isset($data['id']))
                                                @php $start_date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$start_date )))); @endphp
                                            @else
                                                @php $start_date =  date('d-m-Y'); @endphp
                                            @endif
                                            <input type="text" class="form-control erp-form-control-sm"  value="{{$start_date}}" name="start_date" id="kt_datepicker_1" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">End Date:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            @if(isset($data['id']))
                                                @php $end_date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$end_date )))); @endphp
                                            @else
                                                @php $end_date =  date('d-m-Y'); @endphp
                                            @endif
                                            <input type="text" class="form-control erp-form-control-sm"  value="{{$end_date}}" name="end_date" id="kt_datepicker_2" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6 erp-col-form-label">Apply:</div>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @php $apply = isset($apply)?$apply:''@endphp
                                                <input type="checkbox" name="apply" {{$apply==1?'checked':''}}>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6 erp-col-form-label">Apply Warehouse:</div>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @php $apply_warehouse = isset($apply_warehouse)?$apply_warehouse:''@endphp
                                                <input type="checkbox" name="apply_warehouse" {{$apply_warehouse==1?'checked':''}}>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6 erp-col-form-label">Administrator:</div>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @php $administrator = isset($administrator)?$administrator:'' @endphp
                                                <input type="checkbox" name="administrator" id="administrator" {{$administrator==1?'checked':''}}>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6 erp-col-form-label">Central Rate:</div>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @php $central_rate = isset($central_rate)?$central_rate:''@endphp
                                                <input type="checkbox" name="central_rate" {{$central_rate==1?'checked':''}}>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6 erp-col-form-label">Two Step Verification:</div>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @php $two_step_verification = isset($two_step_verification)?$two_step_verification:''@endphp
                                                <input type="checkbox" name="two_step_verification" id="two_step_verification" {{$two_step_verification==1?'checked':''}}>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row" id="two_step_verification_type">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Verification Type:</label>
                                    <div class="col-lg-6">
                                        <div class="kt-radio-inline">
                                            @php $two_step_verification_type = isset($two_step_verification_type)?$two_step_verification_type:''@endphp
                                            <label class="kt-radio kt-radio--bold kt-radio--brand">
                                                <input type="radio"  name="two_step_verification_type" {{$two_step_verification_type=='sms'?'checked':''}} value="sms"> Sms
                                                <span></span>
                                            </label>
                                            <label class="kt-radio kt-radio--bold kt-radio--brand">
                                                <input type="radio" name="two_step_verification_type" {{$two_step_verification_type=='email'?'checked':''}} value="email"> Email
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6 erp-col-form-label">Digital Signature:</div>
                                    <div class="col-lg-6">
                                        <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_2" >
                                            @if($case == 'edit')
                                                @php $signature = isset($signature)?'/images/'.$signature:""; @endphp
                                                <div class="kt-avatar__holder" style="background-image: url({{$signature}})"></div>
                                            @else
                                                <div class="kt-avatar__holder"  style="background-image: url(/assets/media/project-logos/7.png)"></div>
                                            @endif
                                            <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                                <i class="fa fa-pen"></i>
                                                <input type="file" name="user_signature" accept="image/png, image/jpg, image/jpeg">
                                            </label>
                                            <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
                                                <i class="fa fa-times"></i>
                                            </span>
                                        </div>
                                        <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Status:</label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @if($case == 'edit')
                                                    @php $entry_status = isset($status)?$status:""; @endphp
                                                    <input type="checkbox" name="user_entry_status" {{$entry_status==1?"checked":""}}>
                                                @else
                                                    <input type="checkbox" name="user_entry_status" checked>
                                                @endif
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Apply WAN Access:</label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @if($case == 'edit')
                                                    @php $wan_status = isset($ip_address_apply)?$ip_address_apply:""; @endphp
                                                    <input type="checkbox" name="ip_address_apply" {{$wan_status==1?"checked":""}}>
                                                @else
                                                    <input type="checkbox" name="ip_address_apply" checked>
                                                @endif
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <h4>Select IP Location for login</h4>
                            </div>
                            <div class="col-lg-12">
                                <table class="table table-bordered table-hover"">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>IP</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data['ip_location'] as $ip_location)
                                    <tr>
                                        <td>{{$ip_location->ip_location_name}}</td>
                                        <td>{{$ip_location->ip_location_address}}</td>
                                        <td style="padding-top: 0px;padding-bottom: 0;position: relative;top: 5px;">
                                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                <label>
                                                    <input type="checkbox" name="ip[]" value="{{$ip_location->ip_location_id}}" {{in_array($ip_location->ip_location_id, $data['user_ip'])?"checked":""}}>
                                                    <span></span>
                                                </label>
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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

<script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/user.js') }}" type="text/javascript"></script>

<script>

$(document).ready(function(){
    verification_type();

    $('#two_step_verification').change(function(){
        verification_type();
    });

    function verification_type(){
        if($('#two_step_verification').is(":checked") == true){
            $('#two_step_verification_type').show();
        }else{
            $('#two_step_verification_type').hide();
        }
    }

    $('#user_type').on('change', function() {
        if(this.value  == 'customer'){
            $('.customer_block').show();
        }else{
            $('.customer_block').hide();
        }
    })

});
</script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
