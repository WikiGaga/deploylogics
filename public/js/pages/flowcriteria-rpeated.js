$(document).ready(function() {

    var criteriaConditions = [];
    var criteriaFields = null;
    var conditionCounter = 1;
    var isEditMode = typeof flowCriteriaData !== 'undefined' && flowCriteriaData !== null;

    $('.open_notification').on('click', function() {
        var data_url = $(this).attr('data-url');
        openModal(data_url);
    });

    function syncMenuDtlId() {
        var menuDtlId = $('#menu_flow_criteria_name').find('option:selected').attr('data-menu-dtl-id') || '';
        $('#menu_dtl_id').val(menuDtlId);
    }

    syncMenuDtlId();

    $('#menu_flow_criteria_name').on('change', function() {
        syncMenuDtlId();
        loadCriteriaFields(function() {
            $('#flow_criteria_sr_number').val('');
        });
    });

    $('.apply').on('click', function() {
        var val = $(this).is(":checked");
        if (val == true) {
            $(this).parents('tr').find('input').attr('disabled', false);
        } else {
            $(this).parents('tr').find('input').attr('disabled', true);
            $(this).attr('disabled', false);
        }
    });

    $('#menu_flow_criteria_dtl_field').on('change', function() {
        $('#flow_criteria_sr_number').val(conditionCounter);
    });

    $('#menu_flow_criteria_dtl_operator').on('change', function() {
        var isBetween = $(this).val() === 'Between';
        $('#menu_flow_criteria_dtl_value').toggle(!isBetween);
        $('#between_value_wrapper').toggle(isBetween);
        if (isBetween) {
            $('#menu_flow_criteria_dtl_value').val('');
        } else {
            $('#menu_flow_criteria_dtl_value_from').val('');
            $('#menu_flow_criteria_dtl_value_to').val('');
        }
    });

    $('#addData').on('click', function(e) {
        e.preventDefault();

        var srNumber = conditionCounter;
        var field = $('#menu_flow_criteria_dtl_field').val();
        var fieldText = $('#menu_flow_criteria_dtl_field option:selected').text();
        var operator = $('#menu_flow_criteria_dtl_operator').val();
        var operatorText = $('#menu_flow_criteria_dtl_operator option:selected').text();
        var isBetween = operator === 'Between';
        var value, displayValue;
        if (isBetween) {
            var from = $('#menu_flow_criteria_dtl_value_from').val().trim();
            var to   = $('#menu_flow_criteria_dtl_value_to').val().trim();
            if (!from || !to) {
                alert('Please fill both From and To values for Between operator');
                return;
            }
            value        = from + ',' + to;
            displayValue = from + ' AND ' + to;
        } else {
            value        = $('#menu_flow_criteria_dtl_value').val();
            displayValue = value;
        }
        var logicOperator = $('#menu_flow_criteria_dtl_operation').val();
        var logicOperatorText = $('#menu_flow_criteria_dtl_operation option:selected').text();

        if (!field || !operator || !value) {
            alert('Please fill Field Name, Operator, and Value');
            return;
        }

        if (criteriaConditions.length >= 1 && !logicOperator) {
            alert('Please select Logic Operator (AND or OR) when adding multiple conditions');
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
                <td>${displayValue}</td>
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
        $('#menu_flow_criteria_dtl_operator').val('').trigger('change');
        $('#menu_flow_criteria_dtl_value').val('');
        $('#menu_flow_criteria_dtl_value_from').val('');
        $('#menu_flow_criteria_dtl_value_to').val('');
        $('#menu_flow_criteria_dtl_operation').val('');
        $('#flow_criteria_sr_number').val('');

        conditionCounter++;
    });

    $(document).on('click', '.remove-condition', function() {
        var index = $(this).data('index');

        criteriaConditions = criteriaConditions.filter(function(item) {
            return String(item.sr_number) !== String(index);
        });

        $(this).closest('tr').remove();
    });

    $('#FlowCriteria_form').on('submit', function(e) {
        e.preventDefault();

        var $flowItems = $('[data-repeater-item]');

        if ($flowItems.length === 0) {
            showErrorMessage('Please add at least one Flow stage in the <strong>Flow</strong> tab.');
            $('a[href="#flow"]').tab('show');
            return;
        }

        var flowError = null;

        $flowItems.each(function(index) {
            var $item = $(this);
            var stageNum = index + 1;

            var flowName = $item.find('select[name*="form_flow_criteria"]').val();
            if (!flowName || flowName === '0' || flowName === '') {
                flowError = 'Flow stage ' + stageNum + ': please select a <strong>Flow Name</strong>.';
                return false;
            }

            var hasAction = $item.find('input[type="checkbox"][name*="action"]:checked').length > 0;
            if (!hasAction) {
                flowError = 'Flow stage ' + stageNum + ': please select at least one <strong>Action</strong>.';
                $item.find('.flow-tabs .nav-link[data-target-tab="actions"]').trigger('click');
                return false;
            }

            var hasUser = false;
            $item.find('select[name*="users[]"], select[name*="[users]"]').each(function() {
                var val = $(this).val();
                if (val && val.length > 0 && val.filter(function(v) { return v && v !== '' && v !== '0'; }).length > 0) {
                    hasUser = true;
                }
            });

            var hasDesignation = false;
            $item.find('select[name*="designation[]"], select[name*="[designation]"]').each(function() {
                var val = $(this).val();
                if (val && val.length > 0 && val.filter(function(v) { return v && v !== '' && v !== '0'; }).length > 0) {
                    hasDesignation = true;
                }
            });

            if (!hasUser && !hasDesignation) {
                flowError = 'Flow stage ' + stageNum + ': please select at least one <strong>User</strong> or <strong>Designation</strong>.';
                $item.find('.flow-tabs .nav-link[data-target-tab="designation"]').trigger('click');
                return false;
            }
        });

        if (flowError) {
            showErrorMessage(flowError);
            $('a[href="#flow"]').tab('show');
            return;
        }

        showLoading();

        var formData = collectFormData();

        var serializedData = serializeNestedArrays(formData);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: serializedData,
            traditional: true,
            success: function(response) {
                hideLoading();

                if (response.success) {
                    if (response.data && response.data.reference_id) {
                        $('input[name="menu_flow_criteria_dtl_id"]').val(response.data.reference_id);
                    }

                    showSuccessMessage(response.message);

                    setTimeout(function() {
                        if ($('input[name="_method"]').length && $('input[name="_method"]').val() === 'PUT') {
                            window.location.reload();
                        } else if (response.data && response.data.id) {
                            window.location.href = '/flow-criteria/edit/' + response.data.id;
                        }
                    }, 1500);
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
            menu_dtl_id: $('#menu_dtl_id').val(),
            menu_flow_criteria_apply_at: $('input[name="menu_flow_criteria_apply_at"]').val(),
            menu_flow_criteria_status: $('#flow_criteria_enabled_switch').length && $('#flow_criteria_enabled_switch').is(':checked') ? 1 : 0,
            criteria_conditions: criteriaConditions.slice(),
            flow_criteria_data: []
        };

        $('[data-repeater-item]').each(function(index) {
            var $item = $(this);

            function getSelect2Value(selector) {
                var $select = $item.find(selector);
                if ($select.length === 0) return [];

                if ($select.data('select2')) {
                    return $select.val() || [];
                } else {
                    return $select.val() || [];
                }
            }

            var $flowSelect = $item.find('select[name*="form_flow_criteria"]');
            var formFlowCriteria = $flowSelect.length ? $flowSelect.val() : null;

            var $usersSelect = $item.find('select[name*="[users]"], select[name*="users[]"]');
            var users = [];
            if ($usersSelect.length) {
                users = getSelect2Value('select[name*="[users]"], select[name*="users[]"]');
                users = users.filter(function(id) { return id && id !== '' && id !== '0'; });
            }

            var $designationSelect = $item.find('select[name*="[designation]"], select[name*="designation[]"]');
            var designations = [];
            if ($designationSelect.length) {
                designations = getSelect2Value('select[name*="[designation]"], select[name*="designation[]"]');
                designations = designations.filter(function(id) { return id && id !== '' && id !== '0'; });
            }

            var $bypassUsersSelect = $item.find('select[name*="[bypass_users]"], select[name*="bypass_users[]"]');
            var bypassUsers = [];
            if ($bypassUsersSelect.length) {
                bypassUsers = getSelect2Value('select[name*="[bypass_users]"], select[name*="bypass_users[]"]');
                bypassUsers = bypassUsers.filter(function(id) { return id && id !== '' && id !== '0'; });
            }

            var $bypassDesignationSelect = $item.find('select[name*="[bypass_designation]"], select[name*="bypass_designation[]"]');
            var bypassDesignations = [];
            if ($bypassDesignationSelect.length) {
                bypassDesignations = getSelect2Value('select[name*="[bypass_designation]"], select[name*="bypass_designation[]"]');
                bypassDesignations = bypassDesignations.filter(function(id) { return id && id !== '' && id !== '0'; });
            }

            var actions = [];
            $item.find('input[type="checkbox"][name*="action"]:checked').each(function() {
                var actionCode = $(this).data('action-code');
                var actionName = actionCode ? String(actionCode).trim() : $(this).closest('label').text().trim();
                if (actionName) {
                    actions.push(actionName);
                }
            });

            var $selectUserType = $item.find('input[name*="select_user_type"]:checked');
            var selectUser = $selectUserType.length ? $selectUserType.val() : 'any';

            var $warrantyPeriod = $item.find('select[name*="product_warranty_period"]');
            var warrantyPeriod = $warrantyPeriod.length ? $warrantyPeriod.val() : null;

            var $warrantyMode = $item.find('input[name*="product_warranty_mode"]');
            var warrantyMode = $warrantyMode.length ? $warrantyMode.val() : null;

            var $reminderTime = $item.find('input[name*="reminder_time"]');
            var reminderTime = $reminderTime.length ? $reminderTime.val() : null;

            var flowItem = {
                form_flow_criteria: formFlowCriteria,
                action: actions,
                users: users,
                designation: designations,
                select_user: selectUser,
                product_warranty_period: warrantyPeriod,
                product_warranty_mode: warrantyMode,
                reminder_time: reminderTime,
                bypass_users: bypassUsers,
                bypass_designation: bypassDesignations
            };

            console.log('Flow Item ' + index + ':', flowItem);

            data.flow_criteria_data.push(flowItem);
        });

        console.log('Complete Form Data:', data);

        return data;
    }

    function serializeNestedArrays(data) {
        var params = [];

        if (data._token) params.push({name: '_token', value: data._token});
        if (data.menu_flow_criteria_dtl_id) params.push({name: 'menu_flow_criteria_dtl_id', value: data.menu_flow_criteria_dtl_id});
        if (data.menu_flow_criteria_name) params.push({name: 'menu_flow_criteria_name', value: data.menu_flow_criteria_name});
        if (data.menu_dtl_id) params.push({name: 'menu_dtl_id', value: data.menu_dtl_id});
        if (data.menu_flow_criteria_apply_at) params.push({name: 'menu_flow_criteria_apply_at', value: data.menu_flow_criteria_apply_at});
        params.push({name: 'menu_flow_criteria_status', value: (data.menu_flow_criteria_status !== undefined && data.menu_flow_criteria_status !== null) ? String(data.menu_flow_criteria_status) : '1'});

        if (data.criteria_conditions && Array.isArray(data.criteria_conditions)) {
            if (data.criteria_conditions.length === 0) {
                params.push({name: 'criteria_conditions[0][field]', value: ''});
                params.push({name: 'criteria_conditions[0][operator]', value: ''});
                params.push({name: 'criteria_conditions[0][value]', value: ''});
                params.push({name: 'criteria_conditions[0][logic_operator]', value: ''});
            } else {
                data.criteria_conditions.forEach(function(condition, index) {
                    params.push({name: 'criteria_conditions[' + index + '][sr_number]', value: condition.sr_number || ''});
                    params.push({name: 'criteria_conditions[' + index + '][field]', value: condition.field || ''});
                    params.push({name: 'criteria_conditions[' + index + '][operator]', value: condition.operator || ''});
                    params.push({name: 'criteria_conditions[' + index + '][value]', value: condition.value || ''});
                    params.push({name: 'criteria_conditions[' + index + '][logic_operator]', value: condition.logic_operator || ''});
                });
            }
        }

        if (data.flow_criteria_data && Array.isArray(data.flow_criteria_data)) {
            data.flow_criteria_data.forEach(function(flowItem, flowIndex) {
                if (flowItem.form_flow_criteria !== undefined) {
                    params.push({name: 'flow_criteria_data[' + flowIndex + '][form_flow_criteria]', value: flowItem.form_flow_criteria || ''});
                }
                if (flowItem.select_user !== undefined) {
                    params.push({name: 'flow_criteria_data[' + flowIndex + '][select_user]', value: flowItem.select_user || 'any'});
                }
                if (flowItem.product_warranty_period !== undefined) {
                    params.push({name: 'flow_criteria_data[' + flowIndex + '][product_warranty_period]', value: flowItem.product_warranty_period || ''});
                }
                if (flowItem.product_warranty_mode !== undefined) {
                    params.push({name: 'flow_criteria_data[' + flowIndex + '][product_warranty_mode]', value: flowItem.product_warranty_mode || ''});
                }
                if (flowItem.reminder_time !== undefined) {
                    params.push({name: 'flow_criteria_data[' + flowIndex + '][reminder_time]', value: flowItem.reminder_time || ''});
                }

                if (flowItem.action && Array.isArray(flowItem.action)) {
                    flowItem.action.forEach(function(action) {
                        params.push({name: 'flow_criteria_data[' + flowIndex + '][action][]', value: action});
                    });
                }

                if (flowItem.users && Array.isArray(flowItem.users)) {
                    flowItem.users.forEach(function(userId) {
                        if (userId && userId !== '' && userId !== '0') {
                            params.push({name: 'flow_criteria_data[' + flowIndex + '][users][]', value: userId});
                        }
                    });
                }

                if (flowItem.designation && Array.isArray(flowItem.designation)) {
                    flowItem.designation.forEach(function(designationId) {
                        if (designationId && designationId !== '' && designationId !== '0') {
                            params.push({name: 'flow_criteria_data[' + flowIndex + '][designation][]', value: designationId});
                        }
                    });
                }

                if (flowItem.bypass_users && Array.isArray(flowItem.bypass_users)) {
                    flowItem.bypass_users.forEach(function(userId) {
                        if (userId && userId !== '' && userId !== '0') {
                            params.push({name: 'flow_criteria_data[' + flowIndex + '][bypass_users][]', value: userId});
                        }
                    });
                }

                if (flowItem.bypass_designation && Array.isArray(flowItem.bypass_designation)) {
                    flowItem.bypass_designation.forEach(function(designationId) {
                        if (designationId && designationId !== '' && designationId !== '0') {
                            params.push({name: 'flow_criteria_data[' + flowIndex + '][bypass_designation][]', value: designationId});
                        }
                    });
                }
            });
        }

        return $.param(params);
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
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else {
            alert(message);
        }
    }

    function showErrorMessage(message) {
        var text = (message || '').replace(/<br\s*\/?>/gi, ' ').replace(/<[^>]*>/g, '');
        if (typeof toastr !== 'undefined') {
            toastr.error(text);
        } else {
            alert(text);
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

    function initializeSelect2OnItem($item) {
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
    }

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

                var fieldText = $('#menu_flow_criteria_dtl_field option[value="' + condition.condition_field + '"]').text();

                if ((!fieldText || fieldText === '') && criteriaFields && !isNaN(condition.condition_field)) {
                    var idx = parseInt(condition.condition_field, 10);
                    if (criteriaFields[idx] !== undefined) {
                        var raw = criteriaFields[idx];
                        var label = raw.replace(/_/g, ' ');
                        fieldText = label.charAt(0).toUpperCase() + label.slice(1);
                    }
                }

                if (!fieldText || fieldText === '') {
                    fieldText = condition.condition_field;
                }
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
                function addAndPopulateFlow(index) {
                    if (index >= fc.flows.length) {
                        return;
                    }

                    var flow = fc.flows[index];
                    var $item;

                    if (index === 0) {
                        $item = $('[data-repeater-item]').eq(0);
                        if ($item.length) {
                            populateFlowItem($item, flow, function() {
                                addAndPopulateFlow(index + 1);
                            });
                        }
                    } else {
                        $('#kt_repeater_flow [data-repeater-create]').click();

                        setTimeout(function() {
                            $item = $('[data-repeater-item]').eq(index);
                            if ($item.length) {
                                populateFlowItem($item, flow, function() {
                                    addAndPopulateFlow(index + 1);
                                });
                            } else {
                                setTimeout(function() {
                                    addAndPopulateFlow(index);
                                }, 100);
                            }
                        }, 200);
                    }
                }

                addAndPopulateFlow(0);
            }, 600);
        }
    }

    function populateFlowItem($item, flow, callback) {
        var $flowSelect = $item.find('select[name*="form_flow_criteria"]');
        if (!$flowSelect.data('select2')) {
            initializeSelect2OnItem($item);
        }

        setTimeout(function() {
            if (flow.stg_flows_id) {
                $flowSelect.val(String(flow.stg_flows_id)).trigger('change');
            }

            if (flow.actions && flow.actions.length > 0) {
                flow.actions.forEach(function(action) {
                    var storedActionName = action.action_name.toLowerCase().trim();

                    var actionCodeMap = {
                        'create': 'save',
                        'edit': 'save',
                        'save': 'save',
                        'forward': 'forward',
                        'post': 'post',
                        'back': 'back',
                        'archive': 'archive',
                        'new': 'new',
                        'pull_back': 'back'
                    };

                    var targetCode = actionCodeMap[storedActionName] || storedActionName;

                    $item.find('input[type="checkbox"][name*="action"]').each(function() {
                        var checkboxCode = $(this).data('action-code');
                        if (checkboxCode && checkboxCode.toLowerCase() === targetCode) {
                            $(this).prop('checked', true);
                            return false;
                        }
                    });
                });
            }

            if (flow.users && flow.users.length > 0) {
                var userIds = flow.users.map(function(u) { return String(u.user_id); });
                var $userSelect = $item.find('select[name*="users"]');
                if (!$userSelect.data('select2')) {
                    initializeSelect2OnItem($item);
                }
                setTimeout(function() {
                    $userSelect.val(userIds).trigger('change');
                }, 50);
            }

            if (flow.designations && flow.designations.length > 0) {
                var designationIds = flow.designations.map(function(d) { return String(d.designation_id); });
                var $designationSelect = $item.find('select[name*="designation"]');
                if (!$designationSelect.data('select2')) {
                    initializeSelect2OnItem($item);
                }
                setTimeout(function() {
                    $designationSelect.val(designationIds).trigger('change');
                }, 50);
            }

            if (flow.require_all_users == 1) {
                $item.find('input[name*="select_user_type"][value="all"]').prop('checked', true);
            } else {
                $item.find('input[name*="select_user_type"][value="any"]').prop('checked', true);
            }

            if (flow.lead_time_value) {
                $item.find('input[name*="product_warranty_mode"]').val(flow.lead_time_value);
            }

            if (flow.lead_time_unit) {
                var unitMap = {'Minutes': '1', 'Hours': '2', 'Days': '3', 'Weeks': '4', 'Month': '5'};
                var $timeUnitSelect = $item.find('select[name*="product_warranty_period"]');
                if (!$timeUnitSelect.data('select2')) {
                    initializeSelect2OnItem($item);
                }
                setTimeout(function() {
                    $timeUnitSelect.val(unitMap[flow.lead_time_unit] || '0').trigger('change');
                }, 50);
            }

            if (flow.reminder_time_minutes) {
                $item.find('input[name*="reminder_time"]').val(flow.reminder_time_minutes);
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
                    var $bypassUserSelect = $item.find('select[name*="bypass_users"]');
                    if (!$bypassUserSelect.data('select2')) {
                        initializeSelect2OnItem($item);
                    }
                    setTimeout(function() {
                        $bypassUserSelect.val(bypassUserIds.map(String)).trigger('change');
                    }, 50);
                }

                if (bypassDesignationIds.length > 0) {
                    var $bypassDesignationSelect = $item.find('select[name*="bypass_designation"]');
                    if (!$bypassDesignationSelect.data('select2')) {
                        initializeSelect2OnItem($item);
                    }
                    setTimeout(function() {
                        $bypassDesignationSelect.val(bypassDesignationIds.map(String)).trigger('change');
                    }, 50);
                }
            }

            if (callback && typeof callback === 'function') {
                setTimeout(callback, 100);
            }
        }, 150);
    }

    function loadCriteriaFields(callback) {
        var formTable = $('#menu_flow_criteria_name').find('option:selected').attr('data-table-name');

        if (!formTable) {
            if (callback) callback();
            return;
        }

        $.ajax({
            type: 'GET',
            url: '/flow-criteria/menu-data/' + formTable,
            success: function(response) {
                if (response) {
                    criteriaFields = response;

                    $("#menu_flow_criteria_dtl_field").empty();
                    $("#menu_flow_criteria_dtl_field").append('<option>Select</option>');
                    $.each(response, function(_, value) {
                        var fieldLabel = value.replace(/_/g, ' ');
                        var displayText = fieldLabel.charAt(0).toUpperCase() + fieldLabel.slice(1);
                        $("#menu_flow_criteria_dtl_field").append('<option value="' + value + '">' + displayText + '</option>');
                    });
                }
                if (callback) callback();
            }
        });
    }

    function initEditMode() {
        if (!isEditMode) return;
        loadCriteriaFields(function() {
            populateEditForm();
        });
    }

    setTimeout(function() {
        initEditMode();
    }, 0);
});
