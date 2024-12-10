<div class="modal-header">
    <h5 class="modal-title">Available Downloads</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>File Name</th>
                <th>User</th>
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if($downloads && $downloads->count() > 0)
                @foreach($downloads as $download)
                    <tr>
                        <td>{{ $download->file_name }}</td>
                        <td>{{ $download->user->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($download->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <a href="{{ route('listing-file-download', ['filename' => $download->file_name]) }}" class="btn btn-success btn-sm">
                                Download
                            </a>
                            <button type="button" class="btn btn-danger btn-sm delete-download" data-id="{{ $download->id }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            @else
            <tr>
                <td colspan="4" class="text-center">No downloads available</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<script>

$(document).on('click', '.delete-download', function () {
    var downloadId = $(this).data('id');

    if (confirm('Are you sure you want to delete this download?')) {
        $.ajax({
            url: '/common/listing-downloads/delete/' + downloadId,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    $('#listing_user_downloads').trigger('click');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                toastr.error('An error occurred while deleting the file.');
            }
        });
    }
});

</script>
