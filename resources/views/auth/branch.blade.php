@extends('layouts.auth')
@section('title', 'Branch')

@section('content')
    <!--begin::Signin-->
    <div class="login-form login-signin">
        <!--begin::Form-->
        <form class="form" method="POST" action="{{ action('HomeController@branchStore' ) }}" novalidate="novalidate" id="kt_branch_form">
        @csrf
        <!--begin::Title-->
            <div class="pb-13 pt-lg-0 pt-5">
                <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Welcome to Royal ERP</h3>
            </div>
            <!--begin::Title-->

            <!--begin::Form group-->
            <div class="form-group">
                <label class="font-size-h6 font-weight-bolder text-dark">Branches</label>
                <select name="branches" id="branches" class="form-control">
                    @foreach($data as $branch)
                        <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
                    @endforeach
                </select>
            </div>
            <!--end::Form group-->

            <!--begin::Action-->
            <div class="text-right pb-lg-0 pb-5">
                <button id="kt_branch_form_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Select Branch</button>
            </div>
            <!--end::Action-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::branch-->
@endsection
@section('pageScript')
    <script src="assets/new708/js/login-branch.js" type="text/javascript"></script>
@endsection
