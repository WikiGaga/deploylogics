$(function () {
    var previewToken = '';

    $('#salary_file').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(fileName || 'Choose file (Salary template format)');
        resetPreview();
    });

    $('#btn_preview').on('click', function () {
        var fileInput = $('#salary_file')[0];
        if (!fileInput.files.length) {
            toastr.error('Please select an Excel file first.');
            return;
        }

        var formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $('#btn_preview').prop('disabled', true).text('Parsing...');

        $.ajax({
            url: '/salary-notifications/preview',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status !== 'success') {
                    toastr.error(response.message || 'Preview failed.');
                    return;
                }

                previewToken = response.data.preview_token;
                $('#preview_token').val(previewToken);

                $('#summary_pay_period').text(response.data.pay_period);
                $('#summary_total').text(response.data.total_rows);
                $('#summary_valid').text(response.data.valid_rows);
                $('#summary_errors').text(response.data.error_rows);
                $('#preview-summary').show();
                $('#preview-table-wrap').show();
                $('#send-result').hide();

                var tbody = $('#preview_table tbody');
                tbody.empty();

                $.each(response.data.rows, function (index, row) {
                    var statusHtml = row.is_valid
                        ? '<span class="badge badge-success">Valid</span>'
                        : '<span class="badge badge-danger">Error</span>';

                    var errorsHtml = '';
                    if (row.errors && row.errors.length) {
                        errorsHtml = '<div class="row-error">' + row.errors.join('<br>') + '</div>';
                    }

                    var messageHtml = '<div class="preview-message">' + escapeHtml(row.preview_text || '') + '</div>';

                    tbody.append(
                        '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + row.row_no + '</td>' +
                        '<td>' + escapeHtml(row.employee_name || '') + '</td>' +
                        '<td>' + escapeHtml(row.phone || row.phone_raw || '') + '</td>' +
                        '<td>' + (row.net_payment != null ? row.net_payment : '') + '</td>' +
                        '<td>' + statusHtml + errorsHtml + '</td>' +
                        '<td>' + messageHtml + '</td>' +
                        '</tr>'
                    );
                });

                $('#btn_send_all').prop('disabled', response.data.error_rows > 0 || response.data.valid_rows === 0);
                toastr.success(response.message);
            },
            error: function (xhr) {
                var message = 'Preview failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            },
            complete: function () {
                $('#btn_preview').prop('disabled', false).html('<i class="la la-search"></i> Preview');
            }
        });
    });

    $('#btn_send_all').on('click', function () {
        if (!previewToken) {
            toastr.error('Please preview the file first.');
            return;
        }

        if (!confirm('Send WhatsApp salary notifications to all valid rows in this file?')) {
            return;
        }

        $('#btn_send_all').prop('disabled', true).text('Queueing...');

        $.ajax({
            url: '/salary-notifications/send',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                preview_token: previewToken
            },
            success: function (response) {
                if (response.status !== 'success') {
                    toastr.error(response.message || 'Send failed.');
                    $('#btn_send_all').prop('disabled', false).html('<i class="la la-whatsapp"></i> Confirm & Send All');
                    return;
                }

                toastr.success(response.message);
                if (response.data.redirect_url) {
                    window.location.href = response.data.redirect_url;
                    return;
                }

                $('#btn_send_all').hide();
            },
            error: function (xhr) {
                var message = 'Send failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
                $('#btn_send_all').prop('disabled', false).html('<i class="la la-whatsapp"></i> Confirm & Send All');
            }
        });
    });

    function resetPreview() {
        previewToken = '';
        $('#preview_token').val('');
        $('#preview-summary').hide();
        $('#preview-table-wrap').hide();
        $('#send-result').hide();
        $('#preview_table tbody').empty();
        $('#btn_send_all').prop('disabled', true).show();
    }

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }
});
