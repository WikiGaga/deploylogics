@extends('layouts.template')
@section('title', 'System Configuration')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $configs = isset($data['configurations']) ? $data['configurations'] : [];

        $getConfig = function($key, $default = '') use ($configs) {
            return isset($configs[$key]) ? $configs[$key] : $default;
        };
    @endphp

    @permission($data['permission'])
    <form id="system_configuration_form" class="erp_form_validation kt-form" method="post" action="{{ action('Development\SystemConfigurationController@store') }}">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#notifications" role="tab">Notifications</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#api_configurations" role="tab">API Configurations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#branch_sync_status" role="tab">Branch Sync Status</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="notifications" role="tabpanel">
                            <div class="kt-portlet__body" style="padding-top: 20px;">
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <h4 class="kt-section__title" style="margin-bottom: 20px; font-weight: bold; color: #5d78ff;">Purchase Module</h4>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Purchase Above Per Purchase Limit:</label>
                                    <div class="col-lg-3">
                                        <input type="number" step="0.01" min="0" class="form-control erp-form-control-sm"
                                               name="purchase_per_purchase_limit"
                                               value="{{ $getConfig('purchase_per_purchase_limit', '') }}"
                                               placeholder="Enter limit">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="purchase_per_purchase_limit_whatsapp"
                                                   {{ $getConfig('purchase_per_purchase_limit_whatsapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            WhatsApp
                                        </label>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="purchase_per_purchase_limit_inapp"
                                                   {{ $getConfig('purchase_per_purchase_limit_inapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            In-App
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Monthly Purchase Above Monthly Budget:</label>
                                    <div class="col-lg-3">
                                        <input type="number" step="0.01" min="0" class="form-control erp-form-control-sm"
                                               name="purchase_monthly_budget"
                                               value="{{ $getConfig('purchase_monthly_budget', '') }}"
                                               placeholder="Enter budget">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="purchase_monthly_budget_whatsapp"
                                                   {{ $getConfig('purchase_monthly_budget_whatsapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            WhatsApp
                                        </label>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="purchase_monthly_budget_inapp"
                                                   {{ $getConfig('purchase_monthly_budget_inapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            In-App
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">PO Above Per Order Limit:</label>
                                    <div class="col-lg-3">
                                        <input type="number" step="0.01" min="0" class="form-control erp-form-control-sm"
                                               name="po_per_order_limit"
                                               value="{{ $getConfig('po_per_order_limit', '') }}"
                                               placeholder="Enter limit">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="po_per_order_limit_whatsapp"
                                                   {{ $getConfig('po_per_order_limit_whatsapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            WhatsApp
                                        </label>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="po_per_order_limit_inapp"
                                                   {{ $getConfig('po_per_order_limit_inapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            In-App
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Purchase Rate Increase Percentage:</label>
                                    <div class="col-lg-3">
                                        <input type="number" step="0.01" min="0" max="100" class="form-control erp-form-control-sm"
                                               name="purchase_rate_increase_percentage"
                                               value="{{ $getConfig('purchase_rate_increase_percentage', '') }}"
                                               placeholder="Enter percentage">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="purchase_rate_increase_whatsapp"
                                                   {{ $getConfig('purchase_rate_increase_whatsapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            WhatsApp
                                        </label>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="purchase_rate_increase_inapp"
                                                   {{ $getConfig('purchase_rate_increase_inapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            In-App
                                        </label>
                                    </div>
                                </div>

                                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <h4 class="kt-section__title" style="margin-bottom: 20px; font-weight: bold; color: #5d78ff;">Inventory Module</h4>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Stock Difference Percentage (Wastage):</label>
                                    <div class="col-lg-3">
                                        <input type="number" step="0.01" min="0" max="100" class="form-control erp-form-control-sm"
                                               name="inventory_stock_difference_percentage"
                                               value="{{ $getConfig('inventory_stock_difference_percentage', '') }}"
                                               placeholder="Enter percentage">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="inventory_stock_difference_whatsapp"
                                                   {{ $getConfig('inventory_stock_difference_whatsapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            WhatsApp
                                        </label>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="inventory_stock_difference_inapp"
                                                   {{ $getConfig('inventory_stock_difference_inapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            In-App
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Reorder Level:</label>
                                    <div class="col-lg-3">
                                        <input type="number" step="0.01" min="0" class="form-control erp-form-control-sm"
                                               name="inventory_reorder_level"
                                               value="{{ $getConfig('inventory_reorder_level', '') }}"
                                               placeholder="Enter level">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="inventory_reorder_level_whatsapp"
                                                   {{ $getConfig('inventory_reorder_level_whatsapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            WhatsApp
                                        </label>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="inventory_reorder_level_inapp"
                                                   {{ $getConfig('inventory_reorder_level_inapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            In-App
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Minimum Level:</label>
                                    <div class="col-lg-3">
                                        <input type="number" step="0.01" min="0" class="form-control erp-form-control-sm"
                                               name="inventory_minimum_level"
                                               value="{{ $getConfig('inventory_minimum_level', '') }}"
                                               placeholder="Enter level">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="inventory_minimum_level_whatsapp"
                                                   {{ $getConfig('inventory_minimum_level_whatsapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            WhatsApp
                                        </label>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" name="inventory_minimum_level_inapp"
                                                   {{ $getConfig('inventory_minimum_level_inapp', false) ? 'checked' : '' }}>
                                            <span></span>
                                            In-App
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="tab-pane" id="api_configurations" role="tabpanel">
                            <div class="kt-portlet__body" style="padding-top: 20px;">
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <h4 class="kt-section__title" style="margin-bottom: 20px; font-weight: bold; color: #5d78ff;">API Keys & Secrets</h4>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">WhatsApp API Key:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control erp-form-control-sm"
                                               name="api_whatsapp_key"
                                               value="{{ $getConfig('api_whatsapp_key', '') }}"
                                               placeholder="Enter WhatsApp API Key">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">WhatsApp API Secret:</label>
                                    <div class="col-lg-9">
                                        <input type="password" class="form-control erp-form-control-sm"
                                               name="api_whatsapp_secret"
                                               value="{{ $getConfig('api_whatsapp_secret', '') }}"
                                               placeholder="Enter WhatsApp API Secret">
                                    </div>
                                </div>

                                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">SMS API Key:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control erp-form-control-sm"
                                               name="api_sms_key"
                                               value="{{ $getConfig('api_sms_key', '') }}"
                                               placeholder="Enter SMS API Key">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">SMS API Secret:</label>
                                    <div class="col-lg-9">
                                        <input type="password" class="form-control erp-form-control-sm"
                                               name="api_sms_secret"
                                               value="{{ $getConfig('api_sms_secret', '') }}"
                                               placeholder="Enter SMS API Secret">
                                    </div>
                                </div>

                                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Email API Key:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control erp-form-control-sm"
                                               name="api_email_key"
                                               value="{{ $getConfig('api_email_key', '') }}"
                                               placeholder="Enter Email API Key">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Email API Secret:</label>
                                    <div class="col-lg-9">
                                        <input type="password" class="form-control erp-form-control-sm"
                                               name="api_email_secret"
                                               value="{{ $getConfig('api_email_secret', '') }}"
                                               placeholder="Enter Email API Secret">
                                    </div>
                                </div>

                                {{-- <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Other API Configurations (JSON):</label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control erp-form-control-sm" rows="5"
                                                  name="api_other_config"
                                                  placeholder='Enter other API configurations in JSON format, e.g., {"api_name": "key", "api_name2": "secret"}'>{{ $getConfig('api_other_config', '') }}</textarea>
                                    </div>
                                </div> --}}
                            </div>
                        </div>

                        <div class="tab-pane" id="branch_sync_status" role="tabpanel">
                            <div class="kt-portlet__body" style="padding-top: 20px;">
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <h4 class="kt-section__title" style="margin-bottom: 20px; font-weight: bold; color: #5d78ff;">Branch Sync Status</h4>
                                    </div>
                                </div>

                                @php
                                    $syncStatus = isset($data['branch_sync_status']) ? $data['branch_sync_status'] : collect([]);
                                @endphp

                                @if($syncStatus->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Restaurant ID</th>
                                                    <th>Entity Type</th>
                                                    <th>Last Synced At</th>
                                                    <th>Created At</th>
                                                    <th>Updated At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($syncStatus as $sync)
                                                    @php
                                                        $lastSynced = $sync->last_synced_at ? \Carbon\Carbon::parse($sync->last_synced_at) : null;
                                                        $now = \Carbon\Carbon::now();
                                                        $hoursSinceSync = $lastSynced ? $now->diffInHours($lastSynced) : null;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $sync->id }}</td>
                                                        <td>{{ $sync->restaurant_id }}</td>
                                                        <td><strong>{{ ucwords(str_replace('_', ' ', $sync->entity_type)) }}</strong></td>
                                                        <td>
                                                            @if($lastSynced)
                                                                {{ $lastSynced->format('d/m/Y h:i:s A') }}
                                                                <br>
                                                                <small class="text-muted">({{ $hoursSinceSync }} hours ago)</small>
                                                            @else
                                                                <span class="text-muted">Never</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($sync->created_at)
                                                                {{ \Carbon\Carbon::parse($sync->created_at)->format('d/m/Y h:i:s A') }}
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($sync->updated_at)
                                                                {{ \Carbon\Carbon::parse($sync->updated_at)->format('d/m/Y h:i:s A') }}
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <strong>No sync status data found.</strong> Sync status information will appear here once data is available.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endpermission
@endsection

@section('pageJS')
@endsection

@section('customJS')
<script type="text/javascript">
    $(document).ready(function() {
        $('#system_configuration_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();
            var url = form.attr('action');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if(response.status == 200) {
                        toastr.success(response.message || 'Configuration saved successfully');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Error saving configuration');
                    }
                },
                error: function(xhr) {
                    if(xhr.status == 422) {
                        var errors = xhr.responseJSON.validator_errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('An error occurred while saving configuration');
                    }
                }
            });
        });
    });
</script>
@endsection

