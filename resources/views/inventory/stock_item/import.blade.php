@extends('layouts.layout')
@section('title', 'Import Sale/Cost Rate')

@section('pageCSS')

@endsection

@section('content')
    <form id="form_import" class="form_import kt-form" enctype="multipart/form-data" autocomplete="off" method="post" action="{{ route('rate_import') }}">
        @csrf
        <input type="hidden" name="form_type" value="os">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body">
                        <div class="row">
                            <label class="col-lg-3 erp-col-form-label">Select File:</label>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" name="file" class="custom-file-input" id="customFile">
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('pageJS')
    <script src="{{ asset('js/pages/js/common/import.js') }}" type="text/javascript"></script>
@endsection

@section('customJS')

@endsection
