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
                    {{isset($page_data['title'])?$page_data['title']:""}}<small class="text-capitalize">{{isset($page_data['type'])?ucwords($page_data['type']):""}}</small>
                </h3>
            </div>

        </div>
        <div class="kt-portlet__body">
            {{-- Display Success or Error Messages --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ implode(', ', $errors->all()) }}</div>
            @endif

            {{-- Add/Update Translation Form --}}
            <form method="POST" action="{{ route('languages.create', ['id' => $data['id']]) }}" class="kt-form" id="translation-form">
                @csrf
                <div class="row form-group-block">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="key">Key</label>
                            <input type="text" name="key" id="key" class="form-control erp-form-control-sm" placeholder="Enter translation key" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="value">Value</label>
                            <input type="text" name="value" id="value" class="form-control erp-form-control-sm" placeholder="Enter translation value" required>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Add Translation</button>
                </div>
            </form>

            {{-- Existing Translations Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Key</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody id="translations-table-body">
                        @forelse($data['translations'] as $key => $value)
                            <tr data-key="{{ $key }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="editable" data-column="key">{{ $key }}</td>
                                <td class="editable" data-column="value">{{ $value }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No translations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Update All Button --}}
            <div class="form-group text-right">
                <button type="button" id="update-all-btn" class="btn btn-success">Update All Translations</button>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-start">
                {!! $data['translations']->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
</div>

{{-- Add JavaScript to Make Rows Editable and Handle Updates --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Make table cells editable
        const editableCells = document.querySelectorAll('.editable');
        editableCells.forEach(cell => {
            cell.addEventListener('click', function() {
                const currentText = cell.innerText;
                const input = document.createElement('input');
                input.type = 'text';
                input.value = currentText;
                input.classList.add('form-control', 'erp-form-control-sm');

                // Replace the cell content with the input field
                cell.innerHTML = '';
                cell.appendChild(input);

                // Focus on the input field
                input.focus();

                input.addEventListener('blur', function() {
                    // Replace the input with the updated text
                    const updatedValue = input.value.trim();
                    cell.innerHTML = updatedValue;
                });
            });
        });

        // Handle Update All button click
        document.getElementById('update-all-btn').addEventListener('click', function() {
            const updatedTranslations = [];

            const rows = document.querySelectorAll('#translations-table-body tr');
            rows.forEach(row => {
                const key = row.getAttribute('data-key');
                const keyCell = row.querySelector('td[data-column="key"]');
                const valueCell = row.querySelector('td[data-column="value"]');

                const updatedKey = keyCell.innerText.trim();
                const updatedValue = valueCell.innerText.trim();

                if (updatedKey && updatedValue) {
                    updatedTranslations.push({ key: updatedKey, value: updatedValue });
                }
            });

            // Create hidden inputs to send the updated translations via POST request
            updatedTranslations.forEach((translation, index) => {
                const inputKey = document.createElement('input');
                inputKey.type = 'hidden';
                inputKey.name = `translations[${index}][key]`;
                inputKey.value = translation.key;

                const inputValue = document.createElement('input');
                inputValue.type = 'hidden';
                inputValue.name = `translations[${index}][value]`;
                inputValue.value = translation.value;

                document.getElementById('translation-form').appendChild(inputKey);
                document.getElementById('translation-form').appendChild(inputValue);
            });

            // Submit the form with updated translations
            document.getElementById('translation-form').submit();
        });
    });
</script>
@endsection

