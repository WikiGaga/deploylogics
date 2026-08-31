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

                <div class="alert alert-info mb-4">
                    <h5 class="alert-heading mb-3"><i class="la la-info-circle"></i> Important Instructions</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Excel file format</strong>
                            <ul class="mb-2 pl-3">
                                <li>Use the approved <strong>Salary template</strong> file (.xlsx / .xls).</li>
                                <li><strong>Cell A2:</strong> pay period (e.g. SEP-2025).</li>
                                <li><strong>Row 3:</strong> headers. <strong>Employee data from row 4</strong> onward.</li>
                                <li><strong>Column C:</strong> employee name (required).</li>
                                <li><strong>Column F:</strong> mobile number (required).</li>
                                <li>Salary values: columns H–R. Net payment is calculated automatically (do not rely on column U).</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Phone numbers</strong>
                            <ul class="mb-2 pl-3">
                                <li>Enter the <strong>full international number with country code</strong>.</li>
                                <li>Examples: <code>96891234567</code>, <code>+923001234567</code>, <code>00971501234567</code>.</li>
                                <li>Any country is allowed. Local numbers without country code are not accepted.</li>
                            </ul>
                            <strong>Sending</strong>
                            <ul class="mb-0 pl-3">
                                <li>Click <strong>Preview</strong> first and fix all row errors before sending.</li>
                                <li><strong>Confirm &amp; Send All</strong> queues WhatsApp messages in the background.</li>
                                <li>You will be redirected to the batch detail page to track Sent / Failed status.</li>
                                <li>Do not upload the same file again while a batch is still processing.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form id="salary_notification_form" class="kt-form" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="row mb-4">
                        <label class="col-lg-3 erp-col-form-label">Salary Excel File:</label>
                        <div class="col-lg-6">
                            <div class="custom-file">
                                <input type="file" name="file" id="salary_file" class="custom-file-input" accept=".xlsx,.xls">
                                <label class="custom-file-label" for="salary_file">Choose file (Salary template format)</label>
                            </div>
                            <small class="form-text text-muted">Upload the salary Excel file, then click Preview to validate all rows.</small>
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
