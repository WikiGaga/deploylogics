@extends('layouts.layout')
@section('title', 'PageTitle')

@section('pageCSS')
@endsection
{{--
    ### chnaging this page
        PageTitle to ???
        form_id to ???
        Setting\Controller to ???
        master-form.js to ???--}}

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $date =  date('d-m-Y');
        }
        if($case == 'edit'){
            $current = $data['current'];
            $id = "";

        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="form_id" class="kt-form" method="post" action="{{ action('Setting\Controller@store', isset($id)?$id:"") }}">
    @csrf
    <input type="hidden" value='{{$form_type}}' id="form_type">
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{$code}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Document Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="document_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{$date}}" id="kt_datepicker_3"  />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">

                        </div>
                        <div class="col-lg-4">

                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
@endsection
