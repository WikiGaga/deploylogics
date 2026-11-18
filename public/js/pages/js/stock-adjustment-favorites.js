$(function () {
    var $form = $('#stock_adjustment_form');
    if (!$form.length) return;

    var moduleType = 'sa';
    var busy = false;
    var csrf = $('meta[name="csrf-token"]').attr('content');

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    }

    function errorMessage(xhr, fallback) {
        return xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : fallback;
    }

    function showModal(id, title, body, footer) {
        $('#' + id).remove();
        var $modal = $('<div class="modal fade" tabindex="-1" role="dialog" id="' + id + '">' +
            '<div class="modal-dialog" role="document"><div class="modal-content">' +
            '<div class="modal-header"><h5 class="modal-title">' + title + '</h5>' +
            '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>' +
            '<div class="modal-body">' + body + '</div>' + (footer || '') +
            '</div></div></div>');
        $modal.on('hidden.bs.modal', function () { $modal.remove(); });
        $('body').append($modal);
        $modal.modal('show');
        return $modal;
    }

    $('#saveFavoriteBtn').on('click', function () {
        if (busy) return;
        var items = [];
        $form.find('.erp_form__grid_body tr').each(function () {
            var $row = $(this);
            var productId = $row.find('.product_id').val();
            var barcodeId = $row.find('.product_barcode_id').val();
            if (productId && barcodeId) {
                items.push({
                    sr_no: items.length + 1,
                    product_id: productId,
                    product_barcode_id: barcodeId,
                    uom_id: $row.find('.uom_id').val() || null
                });
            }
        });
        if (!items.length) {
            toastr.warning('Add products to the grid before saving a favorite.');
            return;
        }

        var $modal = showModal('saSaveFavoriteModal', 'Save as Favorite',
            '<div class="form-group"><label>Favorite Name <span class="text-danger">*</span></label>' +
            '<input type="text" class="form-control sa-favorite-name" maxlength="255"></div>' +
            '<div class="form-group"><label>Description (Optional)</label>' +
            '<textarea class="form-control sa-favorite-description" rows="2"></textarea></div>',
            '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>' +
            '<button type="button" class="btn btn-primary sa-save-favorite">Save</button></div>');

        $modal.find('.sa-save-favorite').on('click', function () {
            var name = $.trim($modal.find('.sa-favorite-name').val());
            if (!name) {
                toastr.error('Please enter a favorite name.');
                return;
            }
            var $button = $(this).prop('disabled', true);
            $.ajax({
                type: 'POST', url: '/common/favorites',
                headers: { 'X-CSRF-TOKEN': csrf },
                data: {
                    favorite_name: name,
                    favorite_description: $.trim($modal.find('.sa-favorite-description').val()),
                    module_type: moduleType,
                    items: items
                }
            }).done(function (response) {
                if (response.status === 'success') {
                    $modal.modal('hide');
                    toastr.success('Favorite saved successfully.');
                } else {
                    toastr.error(response.message || 'Unable to save favorite.');
                }
            }).fail(function (xhr) {
                toastr.error(errorMessage(xhr, 'Unable to save favorite.'));
            }).always(function () { $button.prop('disabled', false); });
        });
    });

    $('#loadFavoriteBtn').on('click', function () {
        if (busy) return;
        $.get('/common/favorites', { module_type: moduleType }).done(function (response) {
            var favorites = response.data && response.data.favorites || [];
            if (response.status !== 'success' || !favorites.length) {
                toastr.info('No Stock Adjustment favorites found.');
                return;
            }
            var body = '<div class="list-group">';
            favorites.forEach(function (favorite) {
                body += '<div class="list-group-item d-flex justify-content-between align-items-center">' +
                    '<button type="button" class="btn btn-link text-left sa-favorite-item" data-id="' + escapeHtml(favorite.favorite_id) + '">' +
                    escapeHtml(favorite.favorite_name) + '</button>' +
                    '<button type="button" class="btn btn-sm btn-danger sa-delete-favorite" data-id="' + escapeHtml(favorite.favorite_id) + '" title="Delete"><i class="la la-trash"></i></button></div>';
            });
            var $modal = showModal('saFavoritesModal', 'Select Favorite', body + '</div>');
            $modal.on('click', '.sa-favorite-item', function () {
                var id = $(this).data('id');
                $modal.modal('hide');
                loadFavorite(id);
            });
            $modal.on('click', '.sa-delete-favorite', function () {
                var id = $(this).data('id');
                var $row = $(this).closest('.list-group-item');
                swal.fire({ title: 'Delete Favorite?', text: 'Delete this favorite list?', type: 'warning',
                    showCancelButton: true, confirmButtonText: 'Delete' }).then(function (result) {
                    if (!result.value) return;
                    $.ajax({ type: 'DELETE', url: '/common/favorites/' + encodeURIComponent(id),
                        headers: { 'X-CSRF-TOKEN': csrf } }).done(function (response) {
                        if (response.status === 'success') {
                            $row.remove();
                            toastr.success('Favorite deleted.');
                        } else toastr.error(response.message || 'Unable to delete favorite.');
                    }).fail(function (xhr) { toastr.error(errorMessage(xhr, 'Unable to delete favorite.')); });
                });
            });
        }).fail(function (xhr) { toastr.error(errorMessage(xhr, 'Unable to load favorites.')); });
    });

    function loadFavorite(id) {
        if (busy) return;
        if (!$form.find('#store_id').val() || $form.find('#store_id').val() === '0' ||
            !$form.find('#rate_type').val() || $form.find('#rate_type').val() === '0') {
            toastr.warning('Select Store and Rate Type before loading a favorite.');
            return;
        }
        busy = true;
        $('#loadFavoriteBtn, #saveFavoriteBtn').prop('disabled', true);
        $.get('/common/favorites/' + encodeURIComponent(id) + '/items').done(function (response) {
            var items = response.data && response.data.items || [];
            if (response.status !== 'success' || !items.length) {
                toastr.warning('No items found in this favorite.');
                finish();
                return;
            }
            var loaded = 0;
            function next(index) {
                if (index >= items.length) {
                    if (loaded) toastr.success(loaded + ' favorite product(s) loaded.');
                    finish();
                    return;
                }
                loadItem(items[index]).then(function () { loaded++; }).catch(function () {
                    toastr.warning('One favorite product could not be loaded.');
                }).then(function () { next(index + 1); });
            }
            next(0);
        }).fail(function (xhr) {
            toastr.error(errorMessage(xhr, 'Unable to load favorite.'));
            finish();
        });
    }

    function finish() {
        busy = false;
        $('#loadFavoriteBtn, #saveFavoriteBtn').prop('disabled', false);
    }

    function loadItem(item) {
        return new Promise(function (resolve, reject) {
            $.get('/common/get-product-barcode/' + encodeURIComponent(item.product_barcode_id)).done(function (response) {
                if (response.status !== 'success' || !response.data || !response.data.barcode) {
                    reject();
                    return;
                }
                var $header = $form.find('thead.erp_form__grid_header tr').first();
                if (!$header.length) { reject(); return; }
                var barcode = response.data.barcode;
                var timeout = setTimeout(function () {
                    $(document).off('stockAdjustmentFavoriteRowReady', onReady);
                    reject();
                }, 10000);
                function onReady(event, rateLoaded) {
                    clearTimeout(timeout);
                    $(document).off('stockAdjustmentFavoriteRowReady', onReady);
                    if (!rateLoaded || $header.find('.product_barcode_id').val() != item.product_barcode_id) {
                        reject();
                        return;
                    }
                    $header.find('#addData').trigger('click');
                    resolve();
                }
                $(document).on('stockAdjustmentFavoriteRowReady', onReady);
                $header.find('#pd_barcode').val(barcode);
                initBarcode(13, $header, 'sa', { form_type: 'sa', val: barcode, from_favorite: 1 });
            }).fail(reject);
        });
    }
});
