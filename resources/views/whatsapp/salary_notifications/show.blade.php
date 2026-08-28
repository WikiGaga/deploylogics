@extends('layouts.layout')
@section('title', 'Salary Notification Batch')

@section('content')
<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            @include('elements.page_header', ['page_data' => $data['page_data']])
        </div>
        <div class="kt-portlet__body">
            <div class="row mb-4">
                <div class="col-md-3"><strong>Pay Period:</strong> {{ $data['batch']->pay_period }}</div>
                <div class="col-md-3"><strong>File:</strong> {{ $data['batch']->file_name }}</div>
                <div class="col-md-3"><strong>Status:</strong> {{ strtoupper($data['batch']->status) }}</div>
                <div class="col-md-3"><strong>Created:</strong> {{ $data['batch']->created_at }}</div>
            </div>
            <div class="row mb-4">
                <div class="col-md-3"><strong>Total:</strong> {{ $data['batch']->total_rows }}</div>
                <div class="col-md-3"><strong>Sent:</strong> {{ $data['batch']->sent_count }}</div>
                <div class="col-md-3"><strong>Failed:</strong> {{ $data['batch']->failed_count }}</div>
                <div class="col-md-3"><strong>Queued:</strong> {{ $data['batch']->queued_count }}</div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Row</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Net Payment</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['batch']->details as $detail)
                        <tr>
                            <td>{{ $detail->row_no }}</td>
                            <td>{{ $detail->employee_name }}</td>
                            <td>{{ $detail->phone }}</td>
                            <td>{{ number_format($detail->net_payment, 3) }}</td>
                            <td>
                                @if($detail->status === 'sent')
                                    <span class="badge badge-success">Sent</span>
                                @elseif($detail->status === 'failed')
                                    <span class="badge badge-danger">Failed</span>
                                @else
                                    <span class="badge badge-warning">{{ ucfirst($detail->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $detail->sent_at }}</td>
                            <td class="text-danger small">{{ $detail->message_exception }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <a href="{{ url('/listing/salary-notifications') }}" class="btn btn-secondary btn-sm mt-3">Back to Listing</a>
            <a href="{{ url('/salary-notifications/form') }}" class="btn btn-brand btn-sm mt-3">New Upload</a>
        </div>
    </div>
</div>
@endsection
