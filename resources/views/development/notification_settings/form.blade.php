@extends('layouts.template')
@section('title', 'Notification Settings')

@section('pageCSS')
<style>
    /* Criteria table styling */
    #repeated_data tr {
        transition: background-color 0.2s;
    }

    #repeated_data tr:hover {
        background-color: #f7f8fa;
    }

    /* Fix for Select2 dropdown visibility */
    .select2-container {
        z-index: 9999 !important;
    }

    /* Flow tabs styling */
    .flow-tabs .nav-link {
        cursor: pointer;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }
    .toggle-style-1{    
        background: #e6e6e6;
        padding: 15px 6px;
        justify-content: space-between;
        border-radius: 5px;
    }
</style>
@endsection
@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                @php
                    $page_data = [
                        'page_title'=>'Form Flow Criteria',
                        'form_type'=> ''
                    ]
                @endphp
                @include('elements.page_header',['page_data'=>$page_data])
            </div>
            <div class="kt-portlet__body">
                <!--begin::Form-->
                <form id="NotificationSettings_form" class="kt-form" method="post" action="{{ isset($data['notification']) ? action('Development\NotificationSettingsController@update', $data['notification']->id) : action('Development\NotificationSettingsController@store') }}">
                    @csrf
                    @if(isset($data['notification']))
                        <input type="hidden" name="_method" value="PUT">
                    @endif
                    <div class="kt-portlet__body">
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-12 col-form-label">Title:</label>
                                    <div class="col-lg-12">
                                        <input type="text" name="notification_title" class="form-control form-control-sm" value="{{ isset($data['notification']) ? $data['notification']->title : '' }}" placeholder="Enter Title" style="height: 38px;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-12 col-form-label">Select Form:</label>
                                    <div class="col-lg-12">
                                        <select value="{{ old('notification_form', isset($data['notification']) ? $data['notification']->key : '') }}" name="notification_form" class="moveIndex form-control erp-form-control-sm kt-select2">
                                            <option value="">Select Form</option>
                                            @foreach($data['listings'] as $form)
                                                <option value="{{ $form->listing_studio_table_name }}"
                                                    {{ old('notification_form', $data['notification']->key ?? '') == $form->listing_studio_table_name ? 'selected' : '' }}>
                                                    {{ $form->listing_studio_title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-3 col-form-label">
                                        <label class="rtl-toggle toggle-style-1" title="Toggle Right-to-Left Layout">
                                            <span class="rtl-label">Push Notification</span>
                                            <input type="checkbox" id="push_notification_status" name="push_notification_status" {{ isset($data['notification']) && $data['notification']->push_notification_status == 'active'  ? 'checked' : '' }} autocomplete="off">
                                            <span class="rtl-slider"></span>
                                        </label>
                                    </div>
                                    <div class="col-lg-3 col-form-label">
                                        <label class="rtl-toggle toggle-style-1" title="Toggle Right-to-Left Layout">
                                            <span class="rtl-label">Email</span>
                                            <input type="checkbox" id="mail_status" name="mail_status" {{ isset($data['notification']) && $data['notification']->mail_status == 'active'  ? 'checked' : '' }} autocomplete="off">
                                            <span class="rtl-slider"></span>
                                        </label>
                                    </div>
                                    <div class="col-lg-3 col-form-label">
                                        <label class="rtl-toggle toggle-style-1" title="Toggle Right-to-Left Layout">
                                            <span class="rtl-label">Whatsapp</span>
                                            <input type="checkbox" id="whatsapp_status" name="whatsapp_status" {{ isset($data['notification']) && $data['notification']->whatsapp_status == 'active'  ? 'checked' : '' }} autocomplete="off">
                                            <span class="rtl-slider"></span>
                                        </label>
                                    </div>
                                    <div class="col-lg-3 col-form-label">
                                    
                                        <label class="rtl-toggle toggle-style-1" title="Toggle Right-to-Left Layout">
                                            <span class="rtl-label">SMS</span>
                                            <input type="checkbox" id="sms_status" name="sms_status" {{ isset($data['notification']) && $data['notification']->sms_status == 'active' ? 'checked' : '' }} autocomplete="off">
                                            <span class="rtl-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-12 col-form-label">Whatsapp Template Name:</label>
                                    <div class="col-lg-12">
                                        <input type="text" name="whatsapp_template" value="{{ old('whatsapp_template', isset($data['notification']) ? $data['notification']->whatsapp_template : '') }}" class="form-control form-control-sm" placeholder="Enter Whatsapp Template Name"></input>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-12 col-form-label">Notification Message:</label>
                                    <div class="col-lg-12">
                                        <textarea name="notification_message" rows="5" class="form-control form-control-sm" placeholder="Enter Notification Message">{{ isset($data['notification']) ? $data['notification']->message : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- end row--}}
                    </div>

                    <div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Form</th>
                            <th>Message</th>
                            <th>Push Notification</th>
                            <th>Email</th>
                            <th>Whatsapp</th>
                            <th>SMS</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['notification_settings'] as $setting)
                            <tr>
                                <td>{{ $setting->title }}</td>
                                <td>{{ $setting->key }}</td>
                                <td>{{ $setting->message->message ?? '' }}</td>
                                <td>
                                    <span class="badge {{ $setting->push_notification_status == 'active' ? 'bg-success' : 'bg-danger' }} text-white p-2 rounded-full">
                                        {{ $setting->push_notification_status == 'active' ? 'Enable' : 'Inactivate' }}
                                    </span>
                                </td>
                                <td><span class="badge {{ $setting->mail_status == 'active' ? 'bg-success' : 'bg-danger' }} text-white p-2 rounded-full">{{ $setting->mail_status == 'active' ? 'Enable' : 'Inactive' }}</span></td>
                                <td>
                                    <span class="badge {{ $setting->whatsapp_status == 'active' ? 'bg-success' : 'bg-danger' }} text-white p-2 rounded-full">
                                        {{ $setting->whatsapp_status == 'active' ? 'Enable' : 'Inactive' }}
                                    </span>
                                    <span>
                                        @if($setting->whatsapp_status == 'active' && $setting->whatsapp_template)
                                            <br>
                                            <small>Template: {{ $setting->whatsapp_template }}</small>
                                        @endif
                                    </span>
                                </td>
                                <td><span class="badge {{ $setting->sms_status == 'active' ? 'bg-success' : 'bg-danger' }} text-white p-2 rounded-full">{{ $setting->sms_status == 'active' ? 'Enable' : 'Inactive' }}</span></td>
                                <td><a class="btn btn-sm btn-primary" href="{{ action('Development\NotificationSettingsController@index', $setting->id) }}">Edit</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- end:: Content -->
@endsection
@section('pageJS')
    <script>
        $('#NotificationSettings_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var method = form.find('input[name="_method"]').val() || 'POST';
            var data = form.serialize();

            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function(xhr, response) {
                    var errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                }
            });
        });
    </script>
@endsection

@section('customJS')


@endsection

