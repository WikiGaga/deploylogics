<div class="row">
    <div class="col-md-8">
        <div class="kt-radio-inline"  style="float:right;">
            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="all">
                <input type="radio" name="radioDate" value="all"> All
                <span></span>
            </label>
            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="today">
                <input type="radio" name="radioDate" value="today" checked> Today
                <span></span>
            </label>
            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="yesterday">
                <input type="radio" name="radioDate" value="yesterday"> Yesterday
                <span></span>
            </label>
            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="last_7_days">
                <input type="radio" name="radioDate" value="last_7_days"> Last 7 Days
                <span></span>
            </label>
            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="last_30_days">
                <input type="radio" name="radioDate" value="last_30_days"> Last 30 Days
                <span></span>
            </label>
            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="last_days">
                <input type="radio" name="radioDate" value="last_days"> Last Days
                <span></span>
            </label>
        </div>
    </div>
    <div class="col-md-3" style="display:none;" id="inputDays">
        <div class="row form-group-block">
            <div class="col-sm-7">
                <div class="input-group">
                    <input type="text" class="validNumber form-control erp-form-control-sm" id="days" value=""/>
                    <div class="input-group-append">
                        <span class="input-group-text erp-form-control-sm">Days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><hr>

<script>
(function() {
    'use strict';

    if (window.dateFilterInitialized) {
        return;
    }

    function formatDate(date) {
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();

        return (day < 10 ? '0' : '') + day + '-' +
               (month < 10 ? '0' : '') + month + '-' +
               year;
    }

    function setDateFields(startDate, endDate) {
        if (typeof jQuery === 'undefined') return;

        var $ = jQuery;

        $('#kt_datepicker_3').val(startDate);
        $('#date_from').val(startDate);
        $('#date').val(startDate);

        $('#kt_to_date').val(endDate);
        $('#date_to').val(endDate);
        $('input[name="date_to"]').val(endDate);
    }

    function getDateRange(filterType, customDays) {
        var today = new Date();
        var startDate = new Date();
        var endDate = new Date();

        switch(filterType) {
            case 'all':
                startDate = new Date('2000-01-01');
                endDate = today;
                break;

            case 'today':
                startDate = today;
                endDate = today;
                break;

            case 'yesterday':
                startDate.setDate(today.getDate() - 1);
                endDate.setDate(today.getDate() - 1);
                break;

            case 'last_7_days':
                startDate.setDate(today.getDate() - 7);
                endDate = today;
                break;

            case 'last_30_days':
                startDate.setDate(today.getDate() - 30);
                endDate = today;
                break;

            case 'last_days':
                if (customDays && customDays > 0) {
                    startDate.setDate(today.getDate() - customDays);
                    endDate = today;
                }
                break;
        }

        return {
            startDate: formatDate(startDate),
            endDate: formatDate(endDate)
        };
    }

    function handleDateFilter(filterType, customDays) {
        var dateRange = getDateRange(filterType, customDays);
        setDateFields(dateRange.startDate, dateRange.endDate);
    }

    function initDateFilters() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initDateFilters, 100);
            return;
        }

        var $ = jQuery;

        if ($('#all').data('date-filter-initialized')) {
            return;
        }

        $('#all').data('date-filter-initialized', true);
        window.dateFilterInitialized = true;

        $("#all").off('click').on('click', function() {
            $("#inputDays").hide();
            handleDateFilter('all');
        });

        $("#today").off('click').on('click', function() {
            $("#inputDays").hide();
            handleDateFilter('today');
        });

        $("#yesterday").off('click').on('click', function() {
            $("#inputDays").hide();
            handleDateFilter('yesterday');
        });

        $("#last_7_days").off('click').on('click', function() {
            $("#inputDays").hide();
            handleDateFilter('last_7_days');
        });

        $("#last_30_days").off('click').on('click', function() {
            $("#inputDays").hide();
            handleDateFilter('last_30_days');
        });

        $("#last_days").off('click').on('click', function() {
            $("#inputDays").show();

            $("#days").off('keyup').on('keyup', function() {
                var daysNumber = parseInt($(this).val());
                if (daysNumber && daysNumber > 0) {
                    handleDateFilter('last_days', daysNumber);
                }
            });
        });

        handleDateFilter('today');
    }

    if (typeof jQuery !== 'undefined') {
        if (jQuery.isReady || document.readyState !== 'loading') {
            initDateFilters();
        } else {
            jQuery(document).ready(initDateFilters);
        }
    } else {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initDateFilters, 100);
            });
        } else {
            setTimeout(initDateFilters, 100);
        }
    }
})();
</script>
