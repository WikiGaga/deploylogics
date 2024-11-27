@extends('layouts.template')
@section('title', 'Change Password')

@section('pageCSS')
@endsection
<style>
    .erp-card{
        min-height:160px;
        box-shadow:rgba(82, 63, 105, 0.05) 0px 0px 30px 0px;
        display:flex;
        flex-direction:column;
        position:relative;
        margin-bottom: 20px;
    }
    .erp-card-search {
        background-color: #FFF4DE;
        min-height: 160px;
        width: 30% !important;
        padding-left: 65px;
        padding-right: 0px;
        padding-top: 20px;
    }
    .erp-card-search>h1 {
        font-size: 17.55px;
        font-weight: 600 !important;
        padding-top: 20px;
    }
    .erp-card-search>button {
        margin: 28px 0 20px 0;
    }
    .erp-company-detail{
        width: 40% !important;
        background-color: #FFF4DE;
        padding: 25px 40px;
    }
    .erp-bg-cover {
        background-color: #FFF4DE;
        width: 30% !important;
        background-position-x: 100%;
        background-position-y: -37px;
        background-size: 165px;
        background-repeat: no-repeat;
    }
    .business-name {
        color: #fd397a;
        font-weight: 400;
    }
    .business-name>span {
        color: #737373;
        font-size: 14px;
        font-weight: 500;
        margin-left: 5px;
    }
</style>
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'edit'){
            $id = $data['user']->id;
            $name = $data['user']->name;
            $email = $data['user']->email;
            $phone = $data['user']->mobile_no;
            $usertype = $data['user']->user_type;
        }
    @endphp

    <form id="password_form" class="kt-form" method="post" action="{{ action('Setting\PasswordController@storeChangePass', isset($id)?$id:'') }}">
        @csrf
        <div class="col-lg-12">
            <div class="erp-card">
                <div class="erp-card-body rounded kt-padding-0 d-flex bg-light">
                    <div class="erp-card-search">
                        <h1 class="text-danger font-weight-bolder m-0">{{$data['page_data']['title']}}</h1>
                        <button type="submit" class="btn btn-danger font-weight-bold py-2 px-6">Update</button>
                    </div>
                    <div class="erp-company-detail">
                        <div class="business-name">Name: <span style="color: #1a252f">{{$name}}</span></div>
                        <div class="business-name">Email: <span style="color: #1a252f">{{$email}}</span></div>
                        <div class="business-name">Phone: <span style="color: #1a252f">{{$phone}}</span></div>
                        <div class="business-name">Type: <span style="color: #1a252f">{{strtoupper($usertype)}}</span></div>
                    </div>
                    <div class="erp-bg-cover" style="background-image: url(/assets/media/custom/custom-10.svg);"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">Portal</h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-wrapper">
                                    <a href="/listing/user_account" id="btn-back" class="btn btn-clean btn-icon-sm back">
                                        <i class="la la-long-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body" style="padding-top: 23px;">
                            <div class="form-group-block row">
                                <label class="col-lg-2 erp-col-form-label">New Password: <span class="required">*</span></label>
                                <div class="col-lg-4">
                                    <input type="password" id="new_password" name="new_password" class="form-control erp-form-control-sm">
                                </div>
                            </div>
                            <div class="form-group-block row">
                                <label class="col-lg-2 erp-col-form-label">Confirm Password: <span class="required">*</span></label>
                                <div class="col-lg-4">
                                    <input type="password" id="conform_password" name="conform_password" class="form-control erp-form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($usertype == 'erp')
                    <div class="kt-portlet">
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">POS</h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body" style="padding-top: 23px;">
                            <div class="form-group-block row">
                                <label class="col-lg-2 erp-col-form-label">New Password: <span class="required">*</span></label>
                                <div class="col-lg-4">
                                    <input type="password" id="new_password_pos" name="new_password_pos" class="form-control erp-form-control-sm">
                                </div>
                            </div>
                            <div class="form-group-block row">
                                <label class="col-lg-2 erp-col-form-label">Confirm Password: <span class="required">*</span></label>
                                <div class="col-lg-4">
                                    <input type="password" id="conform_password_pos" name="conform_password_pos" class="form-control erp-form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </form>
    <!--end::Form-->
@endsection
@section('pageJS')
@endsection

@section('customJS')
    @if($usertype == 'erp')
    <script src="{{ asset('js/pages/js/setting/change-password.js') }}" type="text/javascript"></script>
    @else
    <script src="{{ asset('js/pages/js/password-change.js') }}" type="text/javascript"></script>
    @endif
@endsection

