@extends('layouts.auth')
@section('title', 'Login')
@section('content')
    <!--begin::Signin-->
    <div class="login-form login-signin">
        <!--begin::Form-->
        <form class="form" method="POST" action="{{ route('login') }}" novalidate="novalidate" id="kt_login_signin_form">
        @csrf
        <!--begin::Title-->
            <div class="pb-13 pt-lg-0 pt-5">
                <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Welcome to Deploy ERP</h3>
                {{--<span class="text-muted font-weight-bold font-size-h4">New Here? <a href="javascript:;" id="kt_login_signup" class="text-primary font-weight-bolder">Create an Account</a></span>--}}
            </div>
            <!--begin::Title-->

            <!--begin::Form group-->
            <div class="form-group">
                <label class="font-size-h6 font-weight-bolder text-dark">Email</label>
                <input type="text" class="form-control form-control-solid h-auto py-7 px-6 rounded-lg" name="email" value="{{ old('email') }}" required autocomplete="email" style="border: 1px solid #818190;"/>
            </div>
            <!--end::Form group-->

            <!--begin::Form group-->
            <div class="form-group">
                <div class="d-flex justify-content-between mt-n5">
                    <label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
                </div>

                <input type="password" name="password" class="form-control form-control-solid h-auto py-7 px-6 rounded-lg" autocomplete="off" style="border: 1px solid #818190;"/>
            </div>
            <!--end::Form group-->

            <!--begin::Action-->
            <div class="text-right pb-lg-0 pb-5">
                <button id="kt_login_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Sign In</button>
            </div>
            <!--end::Action-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Signin-->
@endsection
@section('pageScript')
    <script src="assets7/js/pages/custom/login/login-general.min.js"></script>
@endsection
