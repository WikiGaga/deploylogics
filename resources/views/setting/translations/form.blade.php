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
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <input type="text" name="translations[{{ $loop->iteration }}][key]" value="{{ $key }}" class="form-control erp-form-control-sm" required>
                                </td>
                                <td>
                                    <input type="text" name="translations[{{ $loop->iteration }}][value]" value="{{ $value }}" class="form-control erp-form-control-sm" required>
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

            {{-- Update All Button --}}
            <div class="form-group text-right">
                <button type="submit" class="btn btn-success">Update All Translations</button>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-start">
                {!! $data['translations']->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
</div>

{{-- Add JavaScript to Handle Form Submission --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle form submission
        document.getElementById('translation-form').addEventListener('submit', function(event) {
            event.preventDefault();

            // Gather the updated translations from the input fields
            const updatedTranslations = [];
            const rows = document.querySelectorAll('#translations-table-body tr');
            rows.forEach(row => {
                const keyInput = row.querySelector('input[name^="translations"][name$="[key]"]');
                const valueInput = row.querySelector('input[name^="translations"][name$="[value]"]');

                updatedTranslations.push({
                    key: keyInput.value.trim(),
                    value: valueInput.value.trim()
                });
            });

            // Optionally, do further validation here if needed

            // Send the updated translations
            const formData = new FormData();
            updatedTranslations.forEach((translation, index) => {
                formData.append(`translations[${index}][key]`, translation.key);
                formData.append(`translations[${index}][value]`, translation.value);
            });

            // Submit the form data
            fetch('{{ route("languages.create", ["id" => $data["id"]]) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Handle success
                    alert('Translations updated successfully');
                } else {
                    // Handle error
                    alert('An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        });
    });
</script>
@endsection
