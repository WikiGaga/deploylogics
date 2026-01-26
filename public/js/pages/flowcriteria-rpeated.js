$(document).ready(function() {

    var criteriaConditions = [];
    var conditionCounter = 1;
    var isEditMode = typeof flowCriteriaData !== 'undefined' && flowCriteriaData !== null;

    if (isEditMode) {
        var formTable = $('#menu_flow_criteria_name').find('option:selected').attr('data-table-name');
        if (formTable) {
            $.ajax({
                type: 'GET',
                url: '/flow-criteria/menu-data/' + formTable,
                success: function(response) {
                    if (response) {
                        $("#menu_flow_criteria_dtl_field").empty();
                        $("#menu_flow_criteria_dtl_field").append('<option>Select</option>');
                        $.each(response, function(key, value) {
                            $("#menu_flow_criteria_dtl_field").append('<option value="' + key + '">' + value + '</option>');
                        });
                        populateEditForm();
                    }
                }
            });
        } else {
            populateEditForm();
        }
    }
    $('#menu_flow_criteria_dtl_field').on('change', function() {
        $('#flow_criteria_sr_number').val(conditionCounter);
    });

    $('#addData').on('click', function(e) {
        e.preventDefault();

        var srNumber = conditionCounter;
        var field = $('#menu_flow_criteria_dtl_field').val();
        var fieldText = $('#menu_flow_criteria_dtl_field option:selected').text();
        var operator = $('#menu_flow_criteria_dtl_operator').val();
        var operatorText = $('#menu_flow_criteria_dtl_operator option:selected').text();
        var value = $('#menu_flow_criteria_dtl_value').val();
        var logicOperator = $('#menu_flow_criteria_dtl_operation').val();
        var logicOperatorText = $('#menu_flow_criteria_dtl_operation option:selected').text();

        if (!field || !operator || !value) {
            alert('Please fill Field Name, Operator, and Value');
            return;
        }

        criteriaConditions.push({
            sr_number: srNumber,
            field: field,
            operator: operator,
            value: value,
            logic_operator: logicOperator
        });

        var row = `
            <tr data-index="${srNumber}">
                <td>${srNumber}</td>
                <td>${fieldText}</td>
                <td>${operatorText}</td>
                <td>${value}</td>
                <td>${logicOperatorText || '-'}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-condition" data-index="${srNumber}">
                        <i class="la la-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#repeated_data').append(row);

        $('#menu_flow_criteria_dtl_field').val('').trigger('change');
        $('#menu_flow_criteria_dtl_operator').val('');
        $('#menu_flow_criteria_dtl_value').val('');
        $('#menu_flow_criteria_dtl_operation').val('');
        $('#flow_criteria_sr_number').val('');

        conditionCounter++;
    });

    $(document).on('click', '.remove-condition', function() {
        var index = $(this).data('index');

        criteriaConditions = criteriaConditions.filter(function(item) {
            return item.sr_number !== index;
        });

        $(this).closest('tr').remove();
    });

    $('#FlowCriteria_form').on('submit', function(e) {
        e.preventDefault();

        showLoading();

        var formData = collectFormData();
        var method = isEditMode ? 'PUT' : 'POST';
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();

                if (response.success) {
                    if (response.data && response.data.reference_id) {
                        $('input[name="menu_flow_criteria_dtl_id"]').val(response.data.reference_id);
                    }

                    showSuccessMessage(response.message);

                    setTimeout(function() {
                        resetForm();
                    }, 3000);
                } else {
                    showErrorMessage(response.message);
                }
            },
            error: function(xhr) {
                hideLoading();

                var errorMessage = 'An error occurred';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage += '<br><br>';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMessage += value[0] + '<br>';
                    });
                }

                showErrorMessage(errorMessage);
            }
        });
    });

    function collectFormData() {
        var data = {
            _token: $('input[name="_token"]').val(),
            menu_flow_criteria_dtl_id: $('input[name="menu_flow_criteria_dtl_id"]').val(),
            menu_flow_criteria_name: $('#menu_flow_criteria_name').val(),
            menu_flow_criteria_apply_at: $('input[name="menu_flow_criteria_apply_at"]').val(),
            criteria_conditions: criteriaConditions,
            flow_criteria_data: []
        };

        $('[data-repeater-item]').each(function(index) {
            var $item = $(this);

            var flowItem = {
                form_flow_criteria: $item.find('select[name="form_flow_criteria"]').val(),
                action: [],
                users: $item.find('select[name="users[]"]').val() || [],
                designation: $item.find('select[name="designation[]"]').val() || [],
                select_user: $item.find('input[name="select_user_type"]:checked').val() || 'any',
                product_warranty_period: $item.find('select[name="product_warranty_period"]').val(),
                product_warranty_mode: $item.find('input[name="product_warranty_mode"]').val(),
                reminder_time: $item.find('input[name="reminder_time"]').val(),
                bypass_users: $item.find('select[name="bypass_users[]"]').val() || [],
                bypass_designation: $item.find('select[name="bypass_designation[]"]').val() || []
            };

            $item.find('input[type="checkbox"][name="action"]:checked').each(function() {
                var actionName = $(this).closest('label').text().trim();
                flowItem.action.push(actionName);
            });

            data.flow_criteria_data.push(flowItem);
        });

        return data;
    }

    function resetForm() {
        $('#FlowCriteria_form')[0].reset();
        $('#repeated_data').empty();
        criteriaConditions = [];
        conditionCounter = 1;
    }

    function showLoading() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Please wait...',
                text: 'Saving Flow Criteria',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    }

    function hideLoading() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }

    function showSuccessMessage(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    function showErrorMessage(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: message,
                showConfirmButton: true
            });
        } else {
            alert(message);
        }
    }

    $(document).on('click', '.flow-tabs .nav-link', function(e) {
        e.preventDefault();

        var $this = $(this);
        var targetTab = $this.attr('data-target-tab');
        var $repeaterItem = $this.closest('[data-repeater-item]');

        $repeaterItem.find('.flow-tabs .nav-link').removeClass('active');
        $this.addClass('active');
        $repeaterItem.find('.tab-pane').removeClass('active');
        $repeaterItem.find('.tab-pane[data-tab-pane="' + targetTab + '"]').addClass('active');
    });

    if (typeof $.fn.repeater !== 'undefined' && !$('#kt_repeater_flow').hasClass('repeater-initialized')) {
        $('#kt_repeater_flow').addClass('repeater-initialized');

        $('#kt_repeater_flow').repeater({
        initEmpty: false,
        show: function() {
            $(this).slideDown();

            var $newItem = $(this);

            $newItem.find('.kt-select2').each(function() {
                var $select = $(this);
                var $parent = $select.parent();

                if ($select.data('select2')) {
                    $select.select2('destroy');
                }

                $parent.find('.select2-container').remove();
                $select.removeClass('select2-hidden-accessible').removeAttr('data-select2-id');

                $select.select2({
                    placeholder: "Select",
                    allowClear: false
                });
            });

            $newItem.find('.tag-select2').each(function() {
                var $select = $(this);
                var $parent = $select.parent();

                if ($select.data('select2')) {
                    $select.select2('destroy');
                }

                $parent.find('.select2-container').remove();
                $select.removeClass('select2-hidden-accessible').removeAttr('data-select2-id');

                $select.select2({
                    placeholder: "Select",
                    tags: false,
                    multiple: true,
                    width: '100%',
                    allowClear: false
                });
            });

            $newItem.find('.flow-tabs .nav-link').removeClass('active');
            $newItem.find('.flow-tabs .nav-link:first').addClass('active');
            $newItem.find('.tab-pane').removeClass('active');
            $newItem.find('.tab-pane:first').addClass('active');
        },
        hide: function(deleteElement) {
            if(confirm('Are you sure you want to delete this flow stage?')) {
                $(this).slideUp(deleteElement);
            }
        }
        });
    }
    $('[data-repeater-item]').each(function() {
        var $item = $(this);

        $item.find('.kt-select2').each(function() {
            var $select = $(this);
            var $parent = $select.parent();

            if ($select.data('select2')) {
                $select.select2('destroy');
            }

            $parent.find('.select2-container').remove();
            $select.removeClass('select2-hidden-accessible').removeAttr('data-select2-id');

            $select.select2({
                placeholder: "Select",
                allowClear: false
            });
        });

        $item.find('.tag-select2').each(function() {
            var $select = $(this);
            var $parent = $select.parent();

            if ($select.data('select2')) {
                $select.select2('destroy');
            }

            $parent.find('.select2-container').remove();
            $select.removeClass('select2-hidden-accessible').removeAttr('data-select2-id');

            $select.select2({
                placeholder: "Select",
                tags: false,
                multiple: true,
                width: '100%',
                allowClear: false
            });
        });

        $item.find('.flow-tabs .nav-link').removeClass('active');
        $item.find('.flow-tabs .nav-link:first').addClass('active');
        $item.find('.tab-pane').removeClass('active');
        $item.find('.tab-pane:first').addClass('active');
    });

    function populateEditForm() {
        if (typeof flowCriteriaData === 'undefined' || !flowCriteriaData) return;

        var fc = flowCriteriaData;

        if (fc.conditions && fc.conditions.length > 0) {
            fc.conditions.forEach(function(condition) {
                criteriaConditions.push({
                    sr_number: condition.condition_sr_number,
                    field: condition.condition_field,
                    operator: condition.condition_operator,
                    value: condition.condition_value,
                    logic_operator: condition.condition_logic_operator
                });

                var fieldText = $('#menu_flow_criteria_dtl_field option[value="' + condition.condition_field + '"]').text() || condition.condition_field;
                var operatorText = condition.condition_operator;
                var logicOperatorText = condition.condition_logic_operator || '-';

                var row = '<tr data-index="' + condition.condition_sr_number + '">' +
                    '<td>' + condition.condition_sr_number + '</td>' +
                    '<td>' + fieldText + '</td>' +
                    '<td>' + operatorText + '</td>' +
                    '<td>' + condition.condition_value + '</td>' +
                    '<td>' + logicOperatorText + '</td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-condition" data-index="' + condition.condition_sr_number + '"><i class="la la-trash"></i></button></td>' +
                    '</tr>';

                $('#repeated_data').append(row);
                conditionCounter = Math.max(conditionCounter, condition.condition_sr_number + 1);
            });
        }

        if (fc.flows && fc.flows.length > 0) {
            setTimeout(function() {
                fc.flows.forEach(function(flow, index) {
                    if (index > 0) {
                        $('#kt_repeater_flow [data-repeater-create]').click();
                    }

                    var $item = $('[data-repeater-item]').eq(index);

                    if (flow.stg_flows_id) {
                        $item.find('select[name="form_flow_criteria"]').val(flow.stg_flows_id).trigger('change');
                    }

                    if (flow.actions && flow.actions.length > 0) {
                        flow.actions.forEach(function(action) {
                            $item.find('input[type="checkbox"][name="action"]').each(function() {
                                var actionName = $(this).closest('label').text().trim();
                                if (actionName === action.action_name) {
                                    $(this).prop('checked', true);
                                }
                            });
                        });
                    }

                    if (flow.users && flow.users.length > 0) {
                        var userIds = flow.users.map(function(u) { return u.user_id; });
                        $item.find('select[name="users[]"]').val(userIds).trigger('change');
                    }

                    if (flow.designations && flow.designations.length > 0) {
                        var designationIds = flow.designations.map(function(d) { return d.designation_id; });
                        $item.find('select[name="designation[]"]').val(designationIds).trigger('change');
                    }

                    if (flow.require_all_users == 1) {
                        $item.find('input[name="select_user_type"][value="all"]').prop('checked', true);
                    } else {
                        $item.find('input[name="select_user_type"][value="any"]').prop('checked', true);
                    }

                    if (flow.lead_time_value) {
                        $item.find('input[name="product_warranty_mode"]').val(flow.lead_time_value);
                    }

                    if (flow.lead_time_unit) {
                        var unitMap = {'Minutes': '1', 'Hours': '2', 'Days': '3', 'Weeks': '4', 'Month': '5'};
                        $item.find('select[name="product_warranty_period"]').val(unitMap[flow.lead_time_unit] || '0');
                    }

                    if (flow.reminder_time_minutes) {
                        $item.find('input[name="reminder_time"]').val(flow.reminder_time_minutes);
                    }

                    if (flow.bypasses && flow.bypasses.length > 0) {
                        var bypassUserIds = [];
                        var bypassDesignationIds = [];

                        flow.bypasses.forEach(function(bypass) {
                            if (bypass.bypass_type === 'user' && bypass.bypass_user_id) {
                                bypassUserIds.push(bypass.bypass_user_id);
                            } else if (bypass.bypass_type === 'designation' && bypass.bypass_designation_id) {
                                bypassDesignationIds.push(bypass.bypass_designation_id);
                            }
                        });

                        if (bypassUserIds.length > 0) {
                            $item.find('select[name="bypass_users[]"]').val(bypassUserIds).trigger('change');
                        }

                        if (bypassDesignationIds.length > 0) {
                            $item.find('select[name="bypass_designation[]"]').val(bypassDesignationIds).trigger('change');
                        }
                    }
                });
            }, 500);
        }
    }
});
