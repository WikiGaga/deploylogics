@extends('layouts.layout')
@section('title', 'Salary Notifications')

@section('pageCSS')
<style>
    #preview-summary { display: none; }
    #preview-table-wrap { display: none; }
    #send-result { display: none; }
    .preview-message {
        white-space: pre-wrap;
        font-size: 12px;
        background: #f7f8fa;
        padding: 10px;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
    }
    .row-error { color: #fd397a; font-size: 12px; }
</style>
@endsection

@section('content')
    @permission($data['permission'])
    <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header', ['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <input type="hidden" value="{{ $data['menu_id'] }}" id="menu_id">

                <form id="salary_notification_form" class="kt-form" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="row mb-4">
                        <label class="col-lg-3 erp-col-form-label">Salary Excel File:</label>
                        <div class="col-lg-6">
                            <div class="custom-file">
                                <input type="file" name="file" id="salary_file" class="custom-file-input" accept=".xlsx,.xls">
                                <label class="custom-file-label" for="salary_file">Choose file (Salary template format)</label>
                            </div>
                            <small class="form-text text-muted">Use the approved salary sheet format. Data starts row 4; pay period in A2.</small>
                        </div>
                        <div class="col-lg-3">
                            <button type="button" class="btn btn-brand btn-sm" id="btn_preview">
                                <i class="la la-search"></i> Preview
                            </button>
                        </div>
                    </div>
                </form>

                <div id="preview-summary" class="alert alert-light border mb-4">
                    <strong>Pay period:</strong> <span id="summary_pay_period">-</span> |
                    <strong>Total:</strong> <span id="summary_total">0</span> |
                    <strong>Valid:</strong> <span id="summary_valid">0</span> |
                    <strong>Errors:</strong> <span id="summary_errors">0</span>
                </div>

                <div id="preview-table-wrap" class="table-responsive mb-4">
                    <table class="table table-bordered table-sm" id="preview_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Excel Row</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Net Payment</th>
                                <th>Status</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-success" id="btn_send_all" disabled>
                        <i class="la la-whatsapp"></i> Confirm &amp; Send All
                    </button>
                </div>

                <div id="send-result" class="alert alert-info"></div>
            </div>
        </div>
    </div>
    <input type="hidden" id="preview_token" value="">
    @endpermission
@endsection

@section('pageJS')
<script src="{{ asset('js/pages/js/whatsapp/salary-notification.js') }}" type="text/javascript"></script>
@endsection
