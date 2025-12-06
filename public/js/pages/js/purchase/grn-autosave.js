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

            $('.erp_form__grid_body tr').each(function(index) {
                const $row = $(this);
                const rowData = {
                    rowIndex: index,
                    fields: {}
                };

                $row.find('input, select, textarea').each(function() {
                    const $input = $(this);
                    const name = $input.attr('name');
                    const id = $input.attr('id');
                    const classList = $input.attr('class');
                    const identifier = name || id || classList;

                    if (identifier && $input.attr('type') !== 'file') {
                        rowData.fields[identifier] = {
                            value: $input.val(),
                            type: $input.prop('tagName').toLowerCase(),
                            inputType: $input.attr('type') || 'text',
                            classes: classList
                        };
                    }
                });

                if (Object.keys(rowData.fields).length > 0) {
                    formData.gridRows.push(rowData);
                }
            });

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

        setTimeout(function() {
            gridRows.forEach(function(rowData, index) {
                let $row = $('.erp_form__grid_body tr').eq(index);

                if ($row.length) {
                    for (const identifier in rowData.fields) {
                        const fieldData = rowData.fields[identifier];
                        let $field = $row.find('[name="' + identifier + '"]').first();

                        if ($field.length === 0) {
                            $field = $row.find('#' + identifier).first();
                        }

                        if ($field.length === 0 && fieldData.classes) {
                            const mainClass = fieldData.classes.split(' ')[0];
                            $field = $row.find('.' + mainClass).first();
                        }

                        if ($field.length) {
                            self.setFieldValue($field, fieldData.value);
                        }
                    }
                }
            });
        }, 500);
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
        localStorage.removeItem(this.storageKey);
        this.updateClearButtonVisibility();
        console.log('GRN auto-save data cleared');
    },

    clearForm: function() {
        const self = this;

        Swal.fire({
            title: 'Clear Form?',
            text: 'This will clear all form fields and remove auto-saved data.',
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
                self.clearFormFields();
                self.clearSavedData();
                toastr.success('Form cleared successfully');
            }
        });
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
