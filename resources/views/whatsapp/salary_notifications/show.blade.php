@extends('layouts.layout')
@section('title', 'Salary Notification Batch')

@section('content')
    @permission($data['permission'])
    <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header', ['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row mb-4">
                    <div class="col-md-3"><strong>Pay Period:</strong> {{ $data['batch']->pay_period }}</div>
                    <div class="col-md-3"><strong>File:</strong> {{ $data['batch']->file_name }}</div>
                    <div class="col-md-3"><strong>Status:</strong> {{ strtoupper($data['batch']->status) }}</div>
                    <div class="col-md-3"><strong>Created:</strong> {{ $data['batch']->created_at }}</div>
                </div>
                @php
                    $pendingCount = max(0, $data['batch']->total_rows - $data['batch']->sent_count - $data['batch']->failed_count);
                @endphp
                <div class="row mb-4">
                    <div class="col-md-3"><strong>Total:</strong> {{ $data['batch']->total_rows }}</div>
                    <div class="col-md-3"><strong>Sent:</strong> <span id="sent-count">{{ $data['batch']->sent_count }}</span></div>
                    <div class="col-md-3"><strong>Failed:</strong> <span id="failed-count">{{ $data['batch']->failed_count }}</span></div>
                    <div class="col-md-3"><strong>Pending:</strong> <span id="pending-count">{{ $pendingCount }}</span></div>
                </div>
                @if(in_array($data['batch']->status, ['queued', 'processing']))
                    <div class="alert alert-info">
                        Messages are sent one-by-one in the background queue. This page refreshes every 10 seconds until all rows are processed.
                    </div>
                @endif
                @if($pendingCount > 0)
                    <div class="mb-3">
                        <button type="button" class="btn btn-warning btn-sm" id="retry-pending-btn" data-batch-id="{{ $data['id'] }}">
                            Retry {{ $pendingCount }} Pending Message(s)
                        </button>
                    </div>
                @endif

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
            </div>
        </div>
    </div>
    @endpermission
@endsection

@section('pageJS')
    @if($pendingCount > 0)
    <script>
        $('#retry-pending-btn').on('click', function () {
            var batchId = $(this).data('batch-id');
            var btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: '/salary-notifications/retry/' + batchId,
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    toastr.success(res.message || 'Pending messages re-queued.');
                    setTimeout(function () { window.location.reload(); }, 1500);
                },
                error: function (xhr) {
                    btn.prop('disabled', false);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Retry failed.';
                    toastr.error(msg);
                }
            });
        });
    </script>
    @endif
    @if(in_array($data['batch']->status, ['queued', 'processing']))
    <script>
        setTimeout(function () {
            window.location.reload();
        }, 10000);
    </script>
    @endif
@endsection
