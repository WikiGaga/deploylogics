

@extends('layouts.template')
@section('title', 'Company')

@section('pageCSS')
    <link href="/assets/css/pages/wizard/wizard-1.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @php

    @endphp
   
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
             
            </div>
            <div class="kt-portlet__body">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-grid kt-wizard-v1 kt-wizard-v1--white" id="kt_wizard_v1" data-ktwizard-state="step-first">
                    
                        <h2>Create Cheque Template</h2>

                        <form method="POST" action="/cheque/template/store">
                        @csrf
                        <input name="name" placeholder="Template Name" required>
                        <!-- <input type="file" name="cheque_image" accept="image/png,image/jpeg" onchange="previewCheque(this)" class="w-full border p-2 rounded"> -->
                        <button>Create</button>
                        </form>
                     
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/business.js') }}" type="text/javascript"></script>
@endsection

@section('customJS')

@endsection
