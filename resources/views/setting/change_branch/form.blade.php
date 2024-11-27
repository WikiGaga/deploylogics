@extends('layouts.template')
@section('title', 'Change Branch')

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
            $id = Auth()->user()->id;
            $name = Auth()->user()->name;
            $email = Auth()->user()->email;
            $phone = Auth()->user()->mobile_no;
            $usertype = Auth()->user()->user_type;
    @endphp

    <form id="change_branch" class="kt-form" method="post" action="{{ action('HomeController@branchStore' ) }}">
        @csrf
        <div class="col-lg-12">
            <div class="erp-card">
                <div class="erp-card-body rounded kt-padding-0 d-flex bg-light">
                    <div class="erp-card-search">
                        <h1 class="text-danger font-weight-bolder m-0">Change Branch</h1>
                        <button type="submit" class="btn btn-danger font-weight-bold py-2 px-6">Switch</button>
                    </div>
                    <div class="erp-company-detail">
                        <div class="business-name">Name: <span style="color: #1a252f">{{$name}}</span></div>
                        <div class="business-name">Email: <span style="color: #1a252f">{{$email}}</span></div>
                        <div class="business-name">Phone: <span style="color: #1a252f">{{$phone}}</span></div>
                    </div>
                    <div class="erp-bg-cover" style="background-image: url(/assets/media/custom/custom-10.svg);"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">Branches</h3>
                            </div>
                            <!--<div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-wrapper">
                                    <a href="/listing/user_account" id="btn-back" class="btn btn-clean btn-icon-sm back">
                                        <i class="la la-long-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>-->
                        </div>
                        <div class="kt-portlet__body" style="padding-top: 23px;">
                            <div class="form-group-block row">
                                <label class="col-lg-3 erp-col-form-label">Select Branch:</label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" id="kt_select2_1" name="branches">
                                            @foreach($data['branch'] as $branch)
                                                @php $branchid = auth()->user()->branch_id; @endphp
                                                <option value="{{$branch->branch_id}}" {{$branch->branch_id == $branchid?"selected":""  }}>{{$branch->branch_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <!--end::Form-->
@endsection
@section('pageJS')
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/change-branch.js') }}" type="text/javascript"></script>
@endsection

