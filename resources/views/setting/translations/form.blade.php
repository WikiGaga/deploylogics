@extends('layouts.layout')
@section('title', 'Translations')

@section('content')
<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
            @include('elements.page_header', ['page_data' => $data['page_data']])
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
            <form method="POST" action="{{ route('languages.create', ['id' => $data['id']]) }}" class="kt-form">
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
                    <button type="submit" class="btn btn-primary">Add / Update Translation</button>
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
                    <tbody>
                        @forelse($data['translations'] as $key => $value)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $key }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No translations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-end">
                {!! $data['translations']->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
</div>
@endsection
