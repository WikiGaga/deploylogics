(function ($) {
    'use strict';

    var sdAllStoreOptions = null;
    var sdAllBranchOptions = null;

    function getSourceBranchId() {
        return $('#new_branch_id').val() || '';
    }

    function cacheSelectOptions() {
        if (sdAllStoreOptions === null) {
            var $storeTemplate = $('#store_to');
            sdAllStoreOptions = $storeTemplate.length ? $storeTemplate.find('option').clone() : $();
        }
        if (sdAllBranchOptions === null) {
            var $branchTemplate = $('#branch_to');
            sdAllBranchOptions = $branchTemplate.length ? $branchTemplate.find('option').clone() : $();
        }
    }

    function filterSdStoresForSelect($select, branchId, selectedVal) {
        cacheSelectOptions();
        var $placeholder = sdAllStoreOptions.filter(function () {
            return !$(this).attr('data-branch');
        }).clone();
        var $matching = sdAllStoreOptions.filter(function () {
            return $(this).attr('data-branch') == branchId;
        }).clone();

        $select.html('');
        $select.append($placeholder);
        $select.append($matching);

        if (selectedVal && $select.find('option[value="' + selectedVal + '"]').length) {
            $select.val(String(selectedVal));
        } else if (branchId && branchId !== '0' && $matching.length) {
            $select.val(String($matching.first().val()));
        } else {
            $select.val('0');
        }
    }

    function filterSdBranchOptionsForSelect($select, resetIfSource) {
        var sourceBranchId = getSourceBranchId();
        $select.find('option').each(function () {
            var val = $(this).val();
            if (val === '0' || val === '') {
                $(this).prop('disabled', false);
                return;
            }
            $(this).prop('disabled', sourceBranchId && String(val) === String(sourceBranchId));
        });
        if (resetIfSource && String($select.val()) === String(sourceBranchId)) {
            $select.val('0');
        }
    }

    function ensureBodySelectHtml($cell, fieldName, optionsHtml, selectedVal, extraClass) {
        var $select = $cell.find('select');
        if (!$select.length) {
            $cell.html(
                '<select class="' + extraClass + ' form-control erp-form-control-sm" data-id="' + fieldName + '">' +
                optionsHtml +
                '</select>'
            );
            $select = $cell.find('select');
        } else {
            $select.html(optionsHtml);
            $select.attr('class', extraClass + ' form-control erp-form-control-sm');
            $select.attr('data-id', fieldName);
            $select.removeAttr('id').removeClass('select2-hidden-accessible').css('display', '');
        }
        if (selectedVal && $select.find('option[value="' + selectedVal + '"]').length) {
            $select.val(String(selectedVal));
        }
        return $select;
    }

    window.sdInitGridRow = function ($row) {
        if (!$row || !$row.length) {
            return;
        }
        cacheSelectOptions();

        var $branchCell = $row.find('td').has('select.sd_branch_to, select[data-id="branch_to"]').first();
        if (!$branchCell.length) {
            $branchCell = $row.find('td').eq($('#branch_to').closest('th').index());
        }
        var $storeCell = $row.find('td').has('select.sd_store_to, select[data-id="store_to"]').first();
        if (!$storeCell.length) {
            $storeCell = $row.find('td').eq($('#store_to').closest('th').index());
        }

        var branchOptions = '';
        sdAllBranchOptions.each(function () {
            branchOptions += $('<div>').append($(this).clone()).html();
        });
        var currentBranch = $branchCell.find('select').val() || '0';
        var $branch = ensureBodySelectHtml(
            $branchCell,
            'branch_to',
            branchOptions,
            currentBranch,
            'branch_to sd_branch_to'
        );
        filterSdBranchOptionsForSelect($branch, true);

        var storeOptions = '';
        sdAllStoreOptions.each(function () {
            storeOptions += $('<div>').append($(this).clone()).html();
        });
        var currentStore = $storeCell.find('select').val() || '0';
        var $store = ensureBodySelectHtml(
            $storeCell,
            'store_to',
            storeOptions,
            currentStore,
            'store_to sd_store_to'
        );
        filterSdStoresForSelect($store, $branch.val(), currentStore);

        if (typeof updateKeys === 'function') {
            updateKeys();
        }
    };

    function initSdGrid() {
        cacheSelectOptions();
        $('.erp_form__grid_body>tr').each(function () {
            sdInitGridRow($(this));
        });
        filterSdBranchOptionsForSelect($('#branch_to'), true);
        filterSdStoresForSelect($('#store_to'), $('#branch_to').val(), $('#store_to').val());
    }

    window.funcAfterAddRow = function () {
        var $row = $('.erp_form__grid_body>tr:last');
        sdInitGridRow($row);
        filterSdBranchOptionsForSelect($('#branch_to'), true);
        filterSdStoresForSelect($('#store_to'), '0', '0');
    };

    function copyRowToHeader($source) {
        var $header = $('.erp_form__grid_header>tr');
        var mapIds = [
            'product_id', 'product_barcode_id', 'uom_id',
            'pd_barcode', 'product_name', 'pd_packing',
            'demand_qty', 'quantity', 'sale_rate', 'rate', 'amount',
            'dis_perc', 'dis_amount', 'vat_perc', 'vat_amount', 'gross_amount',
            'grn_qty', 'after_dis_amount', 'gst_perc', 'gst_amount',
            'fed_perc', 'fed_amount', 'spec_disc_perc', 'spec_disc_amount',
            'net_amount', 'unit_price'
        ];

        mapIds.forEach(function (id) {
            var $src = $source.find('[data-id="' + id + '"]').first();
            if (!$src.length && (id === 'product_id' || id === 'product_barcode_id' || id === 'uom_id')) {
                $src = $source.find('.' + id).first();
            }
            if ($src.length) {
                $header.find('#' + id).val($src.val());
            }
        });

        var uomVal = $source.find('[data-id="pd_uom"], [data-id="uom_id"], .uom_id').first().val();
        var uomText = $source.find('[data-id="pd_uom"] option:selected').text() ||
            $source.find('select.pd_uom option:selected').text() || '';
        if (!uomText) {
            uomText = $source.find('select.pd_uom').find('option:selected').text();
        }
        var uomId = $source.find('.uom_id').val() || uomVal;
        if (uomId) {
            $('#uom_id').val(uomId);
            $('#pd_uom').html('<option value="' + uomId + '">' + (uomText || uomId) + '</option>');
        }

        $('#quantity').val('');
        $('#amount').val('');
        $('#gross_amount').val('');
        $('#branch_to').val('0');
        filterSdStoresForSelect($('#store_to'), '0', '0');
        $('#quantity').focus();
    }

    $(document).ready(function () {
        if ($('#form_type').val() !== 'sd') {
            return;
        }
        initSdGrid();

        $(document).on('mousedown', '.copyRowToHeader, .delData', function (e) {
            e.stopPropagation();
        });

        $(document).on('change', '#new_branch_id', function () {
            $('.sd_branch_to').each(function () {
                filterSdBranchOptionsForSelect($(this), true);
            });
        });

        $(document).on('change', 'select.sd_branch_to', function () {
            var $el = $(this);
            var $row = $el.closest('tr');
            var $store = $row.find('select.sd_store_to');
            if (!$store.length && $el.is('#branch_to')) {
                $store = $('#store_to');
            }
            filterSdStoresForSelect($store, $el.val(), '');
        });

        $(document).on('click', '.copyRowToHeader', function (e) {
            e.preventDefault();
            copyRowToHeader($(this).closest('tr'));
        });
    });
})(jQuery);
