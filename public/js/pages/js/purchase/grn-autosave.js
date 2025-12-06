const GRNFormAutoSave = {
    formId: 'grn_form',
    storageKey: 'grn_form_autosave_data',
    debounceTimer: null,
    debounceDelay: 1000,
    isRestoring: false,

    init: function() {
        const self = this;

        $(document).ready(function() {
            const formId = $('#form_id').val();

            if (!formId) {
                self.checkAndRestoreData();
                self.attachEventListeners();
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
        if (this.isRestoring) {
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
        } catch (e) {
            console.error('Error restoring form data:', e);
            toastr.error('Error restoring form data');
        } finally {
            self.isRestoring = false;
        }
    },

    setFieldValue: function($field, value) {
        if (!$field.length || value === undefined || value === null) {
            return;
        }

        $field.val(value);

        if ($field.hasClass('kt-select2') || $field.hasClass('select2-hidden-accessible')) {
            setTimeout(function() {
                $field.trigger('change.select2');
            }, 100);
        }

        $field.trigger('change');
    },

    restoreGridRows: function(gridRows) {
        const self = this;
        let attempts = 0;
        const maxAttempts = 10;

        function attemptRestore() {
            attempts++;
            const $existingRows = $('.erp_form__grid_body tr').not('.erp_form__grid_body_total tr');
            const existingRowCount = $existingRows.length;
            const savedRowCount = gridRows.length;

            console.log('Grid restoration attempt', attempts, '- Existing rows:', existingRowCount, 'Saved rows:', savedRowCount);

            if (existingRowCount === 0 && savedRowCount > 0 && attempts < maxAttempts) {
                setTimeout(attemptRestore, 300);
                return;
            }

            if (existingRowCount === 0 && savedRowCount > 0) {
                console.warn('Grid rows not found in DOM. Grid may be empty. Cannot restore grid data.');
                return;
            }

            if (existingRowCount < savedRowCount) {
                console.warn('Fewer rows in DOM than saved. Restoring to available rows.');
            }

            let totalFieldsRestored = 0;
            gridRows.forEach(function(rowData, index) {
                if (index >= existingRowCount) {
                    console.log('Row', index, 'skipped - no corresponding DOM row');
                    return;
                }

                let $row = $existingRows.eq(index);

                if ($row.length) {
                    let fieldsRestored = 0;
                    for (const identifier in rowData.fields) {
                        const fieldData = rowData.fields[identifier];
                        let $field = $();

                        if (fieldData.name) {
                            $field = $row.find('[name="' + fieldData.name + '"]').first();
                        }

                        if (!$field.length && fieldData.dataId) {
                            $field = $row.find('[data-id="' + fieldData.dataId + '"]').first();
                        }

                        if (!$field.length && fieldData.id) {
                            $field = $row.find('#' + fieldData.id).first();
                        }

                        if (!$field.length && fieldData.classes) {
                            const mainClass = fieldData.classes.split(' ').find(cls =>
                                cls && cls !== 'form-control' && cls !== 'erp-form-control-sm' && cls !== 'handle'
                            );
                            if (mainClass) {
                                $field = $row.find('.' + mainClass).first();
                            }
                        }

                        if (!$field.length) {
                            $field = $row.find('[name="' + identifier + '"]').first();
                        }

                        if (!$field.length) {
                            $field = $row.find('#' + identifier).first();
                        }

                        if ($field.length) {
                            self.setFieldValue($field, fieldData.value);
                            fieldsRestored++;
                            totalFieldsRestored++;
                        }
                    }
                    console.log('Row', index, '- Fields restored:', fieldsRestored);
                } else {
                    console.log('Row', index, 'not found in DOM');
                }
            });

            console.log('Grid restoration complete. Total fields restored:', totalFieldsRestored);
        }

        setTimeout(attemptRestore, 1500);
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
            localStorage.removeItem(storageKey);

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
                console.log('GRN auto-save data successfully cleared');
                window.location.reload();
            } else {
                console.error('GRN auto-save data still exists. Clearing all localStorage...');
                try {
                    localStorage.clear();
                    window.location.reload();
                } catch (e) {
                    console.error('Error clearing all localStorage:', e);
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
