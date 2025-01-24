@extends('layouts.layout')
@section('title', 'Translations')

@section('content')
<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon2-file"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    {{ isset($page_data['title']) ? $page_data['title'] : "" }}<small class="text-capitalize">{{ isset($page_data['type']) ? ucwords($page_data['type']) : "" }}</small>
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">

            <form method="POST" action="{{ route('languages.create', ['id' => $data['id']]) }}" class="kt-form" id="translation-form">
                @csrf
                <div class="form-group">
                    <label for="translations">Add Multiple Translations</label>
                    <div id="translations">
                        <div class="translation-row">
                            <div class="row form-group-block">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="key[]">Key</label>
                                        <input type="text" name="translations[0][key]" class="form-control erp-form-control-sm" placeholder="Enter translation key" required>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="value[]">Value</label>
                                        <input type="text" name="translations[0][value]" class="form-control erp-form-control-sm" placeholder="Enter translation value" required>
                                    </div>
                                </div>
                                <div class="col-lg-2 text-right">
                                    <button type="button" class="btn btn-sm btn-danger mt-2 remove-translation-row">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-more-btn" class="btn btn-info">Add More</button>
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Save Translations</button>
                </div>
            </form>

            <form method="POST" action="{{ route('languages.create', ['id' => $data['id']]) }}" id="translations-form">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="translations-table-body">
                            @forelse($data['translations'] as $key => $value)
                                <tr>
                                    <td>
                                        <input type="text" name="translations[{{ $loop->iteration }}][key]" value="{{ $key }}" class="form-control erp-form-control-sm" required>
                                    </td>
                                    <td>
                                        <input type="text" name="translations[{{ $loop->iteration }}][value]" value="{{ $value }}" class="form-control erp-form-control-sm" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-table-row">Remove</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No translations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-success">Update All Translations</button>
                </div>
            </form>

            <div class="d-flex justify-content-start">
                {!! $data['translations']->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
</div>

{{-- JavaScript to Handle Dynamic Row Addition, Removal, and Table Row Removal --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let translationIndex = 1;  // Index for the new translations

        // Add more translation rows
        document.getElementById('add-more-btn').addEventListener('click', function() {
            const translationsContainer = document.getElementById('translations');

            const newRow = document.createElement('div');
            newRow.classList.add('translation-row');
            newRow.innerHTML = `
                <div class="row form-group-block">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label for="key[]">Key</label>
                            <input type="text" name="translations[${translationIndex}][key]" class="form-control erp-form-control-sm" placeholder="Enter translation key" required>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label for="value[]">Value</label>
                            <input type="text" name="translations[${translationIndex}][value]" class="form-control erp-form-control-sm" placeholder="Enter translation value" required>
                        </div>
                    </div>
                    <div class="col-lg-2 text-right">
                        <button type="button" class="btn btn-sm btn-danger remove-translation-row">Remove</button>
                    </div>
                </div>
            `;

            translationsContainer.appendChild(newRow);
            translationIndex++;
        });

        // Remove dynamic row functionality
        document.getElementById('translations').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-translation-row')) {
                // Remove the clicked translation row
                event.target.closest('.translation-row').remove();
            }
        });

        // Remove table row functionality
        document.getElementById('translations-table-body').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-table-row')) {
                // Remove the clicked table row
                event.target.closest('tr').remove();
            }
        });
    });
</script>

@endsection
