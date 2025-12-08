const GRNFormAutoSave = {
    formId: 'grn_form',
    storageKey: 'grn_form_autosave_data',
    clearFlagKey: 'grn_form_cleared_flag',
    debounceTimer: null,
    debounceDelay: 1000,
    isRestoring: false,
    autoSaveEnabled: false,

    init: function() {
        const self = this;

        $(document).ready(function() {
            const formId = $('#form_id').val();

            if (!formId) {
                const wasCleared = sessionStorage.getItem(self.clearFlagKey);

                if (wasCleared === 'true') {
                    sessionStorage.removeItem(self.clearFlagKey);
                    console.log('Form was cleared. Skipping auto-save for 3 seconds...');
                    setTimeout(function() {
                        self.autoSaveEnabled = true;
                        self.attachEventListeners();
                        console.log('Auto-save enabled after clear delay');
                    }, 3000);
                } else {
                    self.autoSaveEnabled = true;
                    self.attachEventListeners();
                }

                self.checkAndRestoreData();
                self.setupClearButton();
            } else {
                self.clearSavedData();
            }
        });
    },

    checkAndRestoreData: function() {
        const savedData = localStorage.getItem(this.storageKey);

        if (!savedData) {
            return;
        }

        try {
            const formData = JSON.parse(savedData);
            const savedTime = new Date(formData.timestamp);
            const now = new Date();
            const hoursDiff = (now - savedTime) / (1000 * 60 * 60);

            if (hoursDiff > 24) {
                this.clearSavedData();
                return;
            }

            const self = this;
            setTimeout(function() {
                self.restoreFormData(formData);
            }, 800);
        } catch (e) {
            console.error('Error parsing saved form data:', e);
            this.clearSavedData();
        }
    },

    saveFormData: function() {
        if (this.isRestoring || !this.autoSaveEnabled) {
            return;
        }

        const formData = {
            timestamp: new Date().toISOString(),
            headerFields: {},
            gridRows: [],
            select2Values: {}
        };

        try {
            $('#' + this.formId + ' input, #' + this.formId + ' select, #' + this.formId + ' textarea').each(function() {
                const $field = $(this);
                const name = $field.attr('name');
                const id = $field.attr('id');
                const identifier = name || id;

                if (!identifier ||
                    $field.closest('.erp_form__grid_body').length ||
                    $field.attr('type') === 'file' ||
                    $field.attr('type') === 'submit' ||
                    $field.attr('type') === 'button') {
                    return;
                }

                const value = $field.val();

                formData.headerFields[identifier] = value;

                if ($field.hasClass('kt-select2') || $field.hasClass('select2-hidden-accessible')) {
                    formData.select2Values[identifier] = {
                        value: value,
                        text: $field.find('option:selected').text()
                    };
                }
            });

            const $gridRows = $('.erp_form__grid_body tr').not('.erp_form__grid_body_total tr');
            const totalRows = $gridRows.length;

            if (totalRows > 0) {
                $gridRows.each(function(index) {
                    const $row = $(this);

                    if ($row.closest('.erp_form__grid_body_total').length) {
                        return;
                    }

                    if ($row.find('> th').length > 0 && $row.find('> td').length === 0) {
                        return;
                    }

                    const rowData = {
                        rowIndex: index,
                        fields: {}
                    };

                    $row.find('input, select, textarea').each(function() {
                        const $input = $(this);

                        if ($input.attr('type') === 'file' ||
                            $input.attr('type') === 'button' ||
                            $input.attr('type') === 'submit' ||
                            $input.closest('th').length > 0) {
                            return;
                        }

                        const name = $input.attr('name');
                        const id = $input.attr('id');
                        const dataId = $input.attr('data-id');
                        const classList = $input.attr('class');

                        let identifier = name || dataId || id;

                        if (!identifier && classList) {
                            const mainClass = classList.split(' ').find(cls =>
                                cls && cls !== 'form-control' && cls !== 'erp-form-control-sm' && cls !== 'handle'
                            );
                            if (mainClass) {
                                identifier = mainClass;
                            }
                        }

                        if (identifier) {
                            const value = $input.val();
                            if (value !== undefined && value !== null) {
                                rowData.fields[identifier] = {
                                    value: value,
                                    type: $input.prop('tagName').toLowerCase(),
                                    inputType: $input.attr('type') || 'text',
                                    classes: classList || '',
                                    name: name || '',
                                    id: id || '',
                                    dataId: dataId || ''
                                };
                            }
                        }
                    });

                    if (Object.keys(rowData.fields).length > 0) {
                        formData.gridRows.push(rowData);
                    }
                });
            }

            console.log('Grid rows found:', totalRows, 'Grid rows saved:', formData.gridRows.length);

            localStorage.setItem(this.storageKey, JSON.stringify(formData));
            this.updateClearButtonVisibility();
            console.log('GRN form data auto-saved', formData);
        } catch (e) {
            console.error('Error saving form data:', e);

            if (e.name === 'QuotaExceededError') {
                this.clearSavedData();
                toastr.warning('Storage limit reached. Auto-save disabled.');
            }
        }
    },

    restoreFormData: function(formData) {
        const self = this;
        self.isRestoring = true;

        try {
            for (const identifier in formData.headerFields) {
                const value = formData.headerFields[identifier];
                const $field = $('[name="' + identifier + '"]').first();

                if ($field.length === 0) {
                    const $fieldById = $('#' + identifier);
                    if ($fieldById.length) {
                        self.setFieldValue($fieldById, value);
                    }
                } else {
                    self.setFieldValue($field, value);
                }
            }

            if (formData.gridRows && formData.gridRows.length > 0) {
                self.restoreGridRows(formData.gridRows);
            }

            setTimeout(function() {
                self.isRestoring = false;
                console.log('Restoration complete. Auto-save re-enabled.');
            }, 3000);
        } catch (e) {
            console.error('Error restoring form data:', e);
            toastr.error('Error restoring form data');
            self.isRestoring = false;
        }
    },

    setFieldValue: function($field, value) {
        if (!$field.length || value === undefined || value === null) {
            return;
        }

        const tagName = $field.prop('tagName').toLowerCase();

        if (tagName === 'select') {
            if ($field.hasClass('kt-select2') || $field.hasClass('select2-hidden-accessible')) {
                $field.val(value).trigger('change.select2');
            } else {
                $field.val(value).trigger('change');
            }
        } else if (tagName === 'input') {
            const inputType = $field.attr('type');
            if (inputType === 'checkbox' || inputType === 'radio') {
                $field.prop('checked', value == 1 || value === true || value === '1');
            } else {
                $field.val(value);
                if ($field.hasClass('date_inputmask')) {
                    $field.attr('title', value);
                }
            }
            $field.trigger('input').trigger('change');
        } else {
            $field.val(value);
            $field.trigger('change');
        }
    },

    restoreGridRows: function(gridRows) {
        const self = this;
        let attempts = 0;
        const maxAttempts = 15;

        function attemptRestore() {
            attempts++;
            const $existingRows = $('.erp_form__grid_body tr').not('.erp_form__grid_body_total tr');
            let existingRowCount = $existingRows.length;
            const savedRowCount = gridRows.length;

            console.log('Grid restoration attempt', attempts, '- Existing rows:', existingRowCount, 'Saved rows:', savedRowCount);

            if (existingRowCount === 0 && savedRowCount > 0) {
                if (attempts < maxAttempts) {
                    setTimeout(attemptRestore, 300);
                    return;
                }

                console.log('No rows exist. Creating rows from saved data...');
                self.createRowsFromSavedData(gridRows);
                return;
            }

            if (existingRowCount < savedRowCount) {
                console.log('Creating missing rows...');
                for (let i = existingRowCount; i < savedRowCount; i++) {
                    self.createEmptyRow(i + 1);
                }
                existingRowCount = $('.erp_form__grid_body tr').not('.erp_form__grid_body_total tr').length;
            }

            let totalFieldsRestored = 0;
            const $allRows = $('.erp_form__grid_body tr').not('.erp_form__grid_body_total tr');

            gridRows.forEach(function(rowData, index) {
                if (index >= existingRowCount) {
                    console.log('Row', index, 'skipped - index out of range');
                    return;
                }

                let $row = $allRows.eq(index);

                if ($row.length) {
                    self.populateRowData(index, rowData.fields);
                    const restoredCount = Object.keys(rowData.fields).length;
                    totalFieldsRestored += restoredCount;
                } else {
                    console.log('Row', index, 'not found in DOM');
                }
            });

            console.log('Grid restoration complete. Total fields restored:', totalFieldsRestored);

            if (typeof allGridTotal === 'function') {
                setTimeout(function() {
                    allGridTotal();
                }, 500);
            }
        }

        setTimeout(attemptRestore, 1500);
    },

    createRowsFromSavedData: function(gridRows) {
        const self = this;

        gridRows.forEach(function(rowData, index) {
            const rowNum = index + 1;
            let barcodeValue = '';
            let productId = '';
            let productBarcodeId = '';

            for (const identifier in rowData.fields) {
                const fieldData = rowData.fields[identifier];
                if (identifier.includes('pd_barcode') || fieldData.name && fieldData.name.includes('pd_barcode')) {
                    barcodeValue = fieldData.value || '';
                }
                if (identifier.includes('product_id') || fieldData.name && fieldData.name.includes('product_id')) {
                    productId = fieldData.value || '';
                }
                if (identifier.includes('product_barcode_id') || fieldData.name && fieldData.name.includes('product_barcode_id')) {
                    productBarcodeId = fieldData.value || '';
                }
            }

            if (barcodeValue) {
                self.createRowAndPopulate(rowNum, rowData.fields, barcodeValue);
            } else {
                self.createEmptyRow(rowNum);
                setTimeout(function() {
                    self.populateRowData(index, rowData.fields);
                }, 200);
            }
        });
    },

    createEmptyRow: function(rowNum) {
        const $gridBody = $('.erp_form__grid_body');
        if (!$gridBody.length) return;

        const rowHtml = `
            <tr>
                <th scope="row" style="padding:0">
                    <div class="erp_form__grid_th_input">
                        <input type="text" readonly name="pd[${rowNum}][sr_no]" value="${rowNum}" class="sr_no form-control erp-form-control-sm">
                        <input type="hidden" name="pd[${rowNum}][product_id]" value="" class="product_id form-control erp-form-control-sm">
                        <input type="hidden" name="pd[${rowNum}][product_barcode_id]" value="" class="product_barcode_id form-control erp-form-control-sm">
                        <input type="hidden" name="pd[${rowNum}][uom_id]" value="" class="uom_id form-control erp-form-control-sm">
                        <input type="hidden" name="pd[${rowNum}][grn_supplier_id]" value="" class="grn_supplier_id form-control erp-form-control-sm">
                        <input type="hidden" name="pd[${rowNum}][grn_dtl_po_rate]" value="" class="grn_dtl_po_rate form-control erp-form-control-sm">
                    </div>
                </th>
                <td><input type="text" name="pd[${rowNum}][pd_barcode]" value="" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm"></td>
                <td><input type="text" readonly name="pd[${rowNum}][product_name]" value="" class="product_name form-control erp-form-control-sm"></td>
                <td><select name="pd[${rowNum}][pd_uom]" class="pd_uom tb_moveIndex form-control erp-form-control-sm"><option value="">Select</option></select></td>
                <td><input type="text" readonly name="pd[${rowNum}][pd_packing]" value="" class="pd_packing form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][sup_barcode]" value="" class="sup_barcode tb_moveIndex form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][quantity]" data-id="quantity" value="" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][foc_qty]" data-id="foc_qty" value="" class="tblGridCal_foc_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][sale_rate]" value="" class="tblGridSale_rate tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][fc_rate]" value="" class="fc_rate tb_moveIndex validNumber form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][rate]" data-id="rate" value="" class="tblGridCal_rate tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][amount]" data-id="amount" value="" class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][dis_perc]" value="" class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][dis_amount]" value="" class="tblGridCal_discount_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][vat_perc]" value="" class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][vat_amount]" value="" class="tblGridCal_vat_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][batch_no]" value="" class="tb_moveIndex form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][production_date]" value="" class="date_inputmask tb_moveIndex form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][expiry_date]" value="" class="date_inputmask tb_moveIndex form-control erp-form-control-sm"></td>
                <td><input type="text" name="pd[${rowNum}][gross_amount]" data-id="gross_amount" value="" readonly class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm"></td>
                <td>
                    <div class="erp_form__grid_th_btn">
                        <button type="button" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-danger btn-sm del_row">
                            <i class="la la-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;

        $gridBody.append(rowHtml);
    },

    createRowAndPopulate: function(rowNum, fields, barcodeValue) {
        const self = this;
        self.createEmptyRow(rowNum);

        setTimeout(function() {
            const $row = $('.erp_form__grid_body tr').not('.erp_form__grid_body_total tr').eq(rowNum - 1);
            if ($row.length) {
                self.populateRowData(rowNum - 1, fields);
            }
        }, 300);
    },

    populateRowData: function(rowIndex, fields) {
        const self = this;
        const $row = $('.erp_form__grid_body tr').not('.erp_form__grid_body_total tr').eq(rowIndex);

        if (!$row.length) {
            console.log('Row', rowIndex, 'not found for population');
            return;
        }

        let fieldsPopulated = 0;
        let fieldsSkipped = 0;

        for (const identifier in fields) {
            const fieldData = fields[identifier];
            let $field = $();
            let found = false;

            if (fieldData.name) {
                $field = $row.find('[name="' + fieldData.name + '"]').first();
                if ($field.length) found = true;
            }

            if (!$field.length && fieldData.dataId) {
                $field = $row.find('[data-id="' + fieldData.dataId + '"]').first();
                if ($field.length) found = true;
            }

            if (!$field.length && fieldData.classes) {
                const classes = fieldData.classes.split(' ');
                for (let i = 0; i < classes.length; i++) {
                    const cls = classes[i].trim();
                    if (cls && cls !== 'form-control' && cls !== 'erp-form-control-sm' && cls !== 'handle' && cls !== 'tb_moveIndex') {
                        $field = $row.find('.' + cls).first();
                        if ($field.length) {
                            found = true;
                            break;
                        }
                    }
                }
            }

            if (!$field.length) {
                $field = $row.find('[name="' + identifier + '"]').first();
                if ($field.length) found = true;
            }

            if (!$field.length && identifier.includes('[') && identifier.includes(']')) {
                const fieldName = identifier.split(']')[0].split('[')[1];
                if (fieldName) {
                    $field = $row.find('[name*="[' + fieldName + ']"]').first();
                    if ($field.length) found = true;
                }
            }

            if (!$field.length && fieldData.classes) {
                const classes = fieldData.classes.split(' ');
                for (let i = 0; i < classes.length; i++) {
                    const cls = classes[i].trim();
                    if (cls && cls.startsWith('tblGridCal_') || cls.startsWith('pd_') || cls.startsWith('product_')) {
                        $field = $row.find('.' + cls).first();
                        if ($field.length) {
                            found = true;
                            break;
                        }
                    }
                }
            }

            if ($field.length) {
                const value = fieldData.value;
                if (value !== undefined && value !== null) {
                    self.setFieldValue($field, value);
                    fieldsPopulated++;
                } else {
                    fieldsSkipped++;
                }
            } else {
                console.log('Field not found:', identifier, 'Name:', fieldData.name, 'DataId:', fieldData.dataId, 'Classes:', fieldData.classes);
            }
        }

        console.log('Row', rowIndex, '- Fields populated:', fieldsPopulated, 'Skipped:', fieldsSkipped);
    },

    attachEventListeners: function() {
        const self = this;

        $('#' + this.formId).on('input change', 'input:not([type="file"]), select, textarea', function() {
            clearTimeout(self.debounceTimer);
            self.debounceTimer = setTimeout(function() {
                self.saveFormData();
            }, self.debounceDelay);
        });

        $('#' + this.formId).on('blur', 'input:not([type="file"]), select, textarea', function() {
            clearTimeout(self.debounceTimer);
            self.saveFormData();
        });

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length || mutation.removedNodes.length) {
                    clearTimeout(self.debounceTimer);
                    self.debounceTimer = setTimeout(function() {
                        self.saveFormData();
                    }, self.debounceDelay);
                }
            });
        });

        const gridBody = document.querySelector('.erp_form__grid_body');
        if (gridBody) {
            observer.observe(gridBody, {
                childList: true,
                subtree: true
            });
        }

        window.addEventListener('beforeunload', function(e) {
            self.saveFormData();
        });

        $(document).ajaxSuccess(function(event, xhr, settings) {
            if (settings.url && settings.url.includes('GRNController@store')) {
                const response = xhr.responseJSON;
                if (response && response.status === 'success') {
                    self.clearSavedData();
                    toastr.success('Form saved! Auto-save data cleared.');
                }
            }
        });
    },

    clearSavedData: function() {
        const key = this.storageKey;
        localStorage.removeItem(key);

        const verify = localStorage.getItem(key);
        if (verify !== null) {
            console.error('Failed to clear localStorage. Key still exists:', key);
            try {
                delete localStorage[key];
            } catch (e) {
                console.error('Error deleting from localStorage:', e);
            }
        }

        const finalCheck = localStorage.getItem(key);
        if (finalCheck === null) {
            console.log('GRN auto-save data successfully cleared');
        } else {
            console.error('GRN auto-save data still exists after clear attempt');
        }

        this.updateClearButtonVisibility();
    },

    clearForm: function() {
        const self = this;
        const storageKey = this.storageKey;

        function performClear() {
            self.autoSaveEnabled = false;

            localStorage.removeItem(storageKey);
            sessionStorage.setItem(self.clearFlagKey, 'true');

            const verify = localStorage.getItem(storageKey);
            if (verify !== null) {
                console.error('Failed to clear localStorage. Attempting alternative method...');
                try {
                    delete localStorage[storageKey];
                } catch (e) {
                    console.error('Error with alternative clear method:', e);
                }
            }

            const finalCheck = localStorage.getItem(storageKey);
            if (finalCheck === null) {
                console.log('GRN auto-save data successfully cleared. Flag set for next page load.');
                window.location.reload();
            } else {
                console.error('GRN auto-save data still exists. Clearing all localStorage...');
                try {
                    localStorage.clear();
                    sessionStorage.setItem(self.clearFlagKey, 'true');
                    window.location.reload();
                } catch (e) {
                    console.error('Error clearing all localStorage:', e);
                    sessionStorage.setItem(self.clearFlagKey, 'true');
                    window.location.reload();
                }
            }
        }

        if (typeof Swal !== 'undefined' && Swal.fire) {
            Swal.fire({
                title: 'Clear Form?',
                text: 'This will clear all form data and refresh the page.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Clear Form',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    performClear();
                }
            });
        } else {
            if (confirm('Clear Form?\n\nThis will clear all form data and refresh the page.')) {
                performClear();
            }
        }
    },

    clearFormFields: function() {
        $('#' + this.formId + ' input[type="text"], #' + this.formId + ' input[type="number"], #' + this.formId + ' textarea').each(function() {
            const $field = $(this);
            if (!$field.closest('.erp_form__grid_body').length &&
                $field.attr('type') !== 'file' &&
                $field.attr('type') !== 'hidden' &&
                $field.attr('id') !== 'form_id' &&
                $field.attr('id') !== 'form_type' &&
                $field.attr('id') !== 'menu_id') {
                $field.val('');
            }
        });

        $('#' + this.formId + ' select').each(function() {
            const $field = $(this);
            if (!$field.closest('.erp_form__grid_body').length) {
                if ($field.hasClass('kt-select2') || $field.hasClass('select2-hidden-accessible')) {
                    $field.val(null).trigger('change.select2');
                } else {
                    $field.prop('selectedIndex', 0);
                }
            }
        });

        $('.erp_form__grid_body tr').remove();
    },

    manualSave: function() {
        this.saveFormData();
        toastr.info('Form data saved manually');
    },

    getStorageInfo: function() {
        const savedData = localStorage.getItem(this.storageKey);

        if (!savedData) {
            return null;
        }

        try {
            const formData = JSON.parse(savedData);
            return {
                timestamp: formData.timestamp,
                size: new Blob([savedData]).size,
                headerFieldsCount: Object.keys(formData.headerFields || {}).length,
                gridRowsCount: (formData.gridRows || []).length
            };
        } catch (e) {
            return null;
        }
    },

    setupClearButton: function() {
        const self = this;
        const $btn = $('#clearFormBtn');

        if (localStorage.getItem(this.storageKey)) {
            $btn.show();
        }

        $btn.on('click', function() {
            self.clearForm();
        });
    },

    updateClearButtonVisibility: function() {
        const $btn = $('#clearFormBtn');
        if (localStorage.getItem(this.storageKey)) {
            $btn.show();
        } else {
            $btn.hide();
        }
    }
};

$(document).ready(function() {
    setTimeout(function() {
        GRNFormAutoSave.init();
    }, 500);
});

window.GRNFormAutoSave = GRNFormAutoSave;
