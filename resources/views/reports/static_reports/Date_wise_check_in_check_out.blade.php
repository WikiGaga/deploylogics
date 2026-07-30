@extends('layouts.report')
@section('title', 'Date Wise Check In Check Out')

@section('pageCSS')
    <style>
        @media print {
            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            tfoot>tr>td {
                padding: 0 !important;
            }

            body {
                margin: 0;
            }

            .att-table-container {
                overflow-x: visible !important;
            }
        }

        .att-table-container {
            overflow-x: auto;
            margin-bottom: 30px;
            width: 100%;
        }

        .att-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            background-color: #fff;
        }

        .att-table th,
        .att-table td {
            border: 1px solid #b0b0b0 !important;
            padding: 4px 8px;
            white-space: normal;
            vertical-align: middle;
        }

        .att-table th {
            white-space: nowrap;
        }

        .att-table thead th {
            background-color: #d9d9d9 !important;
            color: #000;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }

        .att-table td.emp-name-column {
            font-weight: 600;
            background-color: #f2f2f2;
            color: #222;
            min-width: 130px;
            text-align: left;
        }

        .att-cell-content {
            font-size: 10.5px;
            color: #111;
        }

        .month-block {
            margin-bottom: 25px;
        }

        .month-heading {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            padding-left: 2px;
        }
    </style>
@endsection

@section('content')
    @php
        $data = Session::get('data');
        if (!$data) {
            $data = [
                'page_title' => 'Date wise check in check out',
                'from_date' => date('Y-m-01'),
                'to_date' => date('Y-m-t'),
                'branch_ids' => [],
            ];
        }

        // Snap to full month boundaries:
        // from_date -> first day of that month, to_date -> last day of that month
        $rawFrom = isset($data['from_date']) && !empty($data['from_date'])
            ? date('Y-m-d', strtotime($data['from_date']))
            : date('Y-m-d');
        $rawTo = isset($data['to_date']) && !empty($data['to_date'])
            ? date('Y-m-d', strtotime($data['to_date']))
            : date('Y-m-d');

        // First day of the from_date's month
        $fromDateStr = date('Y-m-01', strtotime($rawFrom));
        // Last day of the to_date's month
        $toDateStr = date('Y-m-t', strtotime($rawTo));

        // 1. Fetch Employees
        $employeesMap = [];
        try {
            $empQuery = \Illuminate\Support\Facades\DB::table('tbl_payr_employee')->select(
                'employee_id',
                'employee_name',
            );

            if (isset($data['branch_ids']) && count($data['branch_ids']) > 0) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('tbl_payr_employee', 'branch_id')) {
                    $empQuery->whereIn('branch_id', $data['branch_ids']);
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('tbl_payr_employee', 'is_deleted')) {
                $empQuery->where(function ($q) {
                    $q->where('is_deleted', 0)->orWhereNull('is_deleted');
                });
            }

            $employeesList = $empQuery->orderBy('employee_name', 'asc')->get();

            foreach ($employeesList as $emp) {
                $empId = $emp->employee_id ?? ($emp->EMPLOYEE_ID ?? null);
                $empName = $emp->employee_name ?? ($emp->EMPLOYEE_NAME ?? '');
                if ($empId !== null) {
                    $employeesMap[$empId] = $empName;
                }
            }
        } catch (\Exception $e) {
            // Fallback if table query directly fails
        }

        // 2. Fetch Attendance Records
        $branchWhere = '';
        if (isset($data['branch_ids']) && count($data['branch_ids']) > 0) {
            $validBranchIds = array_map('intval', $data['branch_ids']);
            if (!empty($validBranchIds)) {
                $branchWhere = ' AND A.BRANCH_ID IN (' . implode(',', $validBranchIds) . ') ';
            }
        }

        $attQuery =
            "
            SELECT
                A.ID,
                A.EMP_ID,
                A.ATTENDANCE_DATE,
                A.ATTENDANCE_TIME,
                A.ATTENDANCE_TYPE,
                E.EMPLOYEE_NAME
            FROM TBL_HR_ATTENDENCE_DTL A
            LEFT JOIN TBL_PAYR_EMPLOYEE E ON A.EMP_ID = E.EMPLOYEE_ID
            WHERE (A.IS_DELETED = 0 OR A.IS_DELETED IS NULL)
              AND A.ATTENDANCE_DATE >= TO_DATE('" .
            $fromDateStr .
            "', 'YYYY-MM-DD')
              AND A.ATTENDANCE_DATE <= TO_DATE('" .
            $toDateStr .
            "', 'YYYY-MM-DD')
              $branchWhere
            ORDER BY A.EMP_ID, A.ATTENDANCE_DATE, A.ATTENDANCE_TIME ASC
        ";

        $attendanceMap = [];
        try {
            $attRecords = \Illuminate\Support\Facades\DB::select($attQuery);

            foreach ($attRecords as $rec) {
                $empId = $rec->emp_id ?? ($rec->EMP_ID ?? null);
                $empName = $rec->employee_name ?? ($rec->EMPLOYEE_NAME ?? '');
                $attDateRaw = $rec->attendance_date ?? ($rec->ATTENDANCE_DATE ?? null);
                $attTimeRaw = $rec->attendance_time ?? ($rec->ATTENDANCE_TIME ?? null);
                $attTypeRaw = $rec->attendance_type ?? ($rec->ATTENDANCE_TYPE ?? '');

                if ($empId === null) {
                    continue;
                }

                if (!isset($employeesMap[$empId])) {
                    $employeesMap[$empId] = !empty($empName) ? $empName : 'Employee #' . $empId;
                }

                $formattedDate = date('Y-m-d', strtotime($attDateRaw));

                $attendanceMap[$empId][$formattedDate][] = [
                    'type' => $attTypeRaw,
                    'time' => $attTimeRaw,
                ];
            }
        } catch (\Exception $e) {
            // Handle query error gracefully
        }

        // Sort employee map alphabetically by name
        asort($employeesMap);

        // 3. Construct Month & Day Grid Structure
        $startPeriod = new DateTime($fromDateStr);
        $endPeriod = new DateTime($toDateStr);
        $endPeriod->modify('+1 day');

        $monthsData = [];
        $currentDate = clone $startPeriod;

        while ($currentDate < $endPeriod) {
            $monthKey = $currentDate->format('Y-m');
            $monthName = $currentDate->format('F Y');
            $monthShort = $currentDate->format('M');

            if (!isset($monthsData[$monthKey])) {
                $monthsData[$monthKey] = [
                    'month_key' => $monthKey,
                    'month_name' => $monthName,
                    'month_short' => $monthShort,
                    'days' => [],
                ];
            }

            $dateStr = $currentDate->format('Y-m-d');
            $dayNum = (int) $currentDate->format('j');
            $headerLabel = $dayNum . '-' . $monthShort;

            $monthsData[$monthKey]['days'][$dateStr] = [
                'day_num' => $dayNum,
                'header_label' => $headerLabel,
                'date_str' => $dateStr,
            ];

            $currentDate->modify('+1 day');
        }
    @endphp

    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{ strtoupper($data['page_title'] ?? 'DATE WISE CHECK IN CHECK OUT') }}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span
                        style="color: #5578eb;">{{ ' ' . date('d-m-Y', strtotime($fromDateStr)) . ' to ' . date('d-m-Y', strtotime($toDateStr)) . ' ' }}</span>
                </h6>
                @if (isset($data['branch_ids']) && count($data['branch_ids']) != 0)
                    @php
                        $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')
                            ->whereIn('branch_id', $data['branch_ids'])
                            ->get(['branch_id', 'branch_name']);
                    @endphp
                    @if (count($branch_lists) > 0)
                        <h6 class="kt-invoice__criteria">
                            <span style="color: #e27d00;">Branch:</span>
                            @foreach ($branch_lists as $branch_list)
                                <span style="color: #5578eb;">{{ $branch_list->branch_name }}</span><span
                                    style="color: #fd397a;">, </span>
                            @endforeach
                        </h6>
                    @endif
                @endif
            </div>
            @include('reports.template.branding')
        </div>

        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @if (empty($monthsData))
                        <div class="alert alert-info text-center">No date range selected or invalid dates.</div>
                    @else
                        @foreach ($monthsData as $monthKey => $monthObj)
                            <div class="month-block">
                                <div class="month-heading">{{ $monthObj['month_name'] }}</div>
                                <div class="att-table-container">
                                    <table class="att-table table2ExcelExport">
                                        <thead>
                                            <tr>
                                                <th class="emp-name-column">Employee name</th>
                                                @foreach ($monthObj['days'] as $dateStr => $dayObj)
                                                    <th>{{ $dayObj['header_label'] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (empty($employeesMap))
                                                <tr>
                                                    <td colspan="{{ count($monthObj['days']) + 1 }}" class="text-center">No
                                                        employee records found.</td>
                                                </tr>
                                            @else
                                                @foreach ($employeesMap as $empId => $empName)
                                                    <tr>
                                                        <td class="emp-name-column">{{ $empName }}</td>
                                                        @foreach ($monthObj['days'] as $dateStr => $dayObj)
                                                            <td>
                                                                @if (isset($attendanceMap[$empId][$dateStr]) && count($attendanceMap[$empId][$dateStr]) > 0)
                                                                    @php
                                                                        $cellItems = [];
                                                                        foreach (
                                                                            $attendanceMap[$empId][$dateStr]
                                                                            as $entry
                                                                        ) {
                                                                            $rawType = strtolower(trim($entry['type']));
                                                                            if (
                                                                                $rawType === 'in' ||
                                                                                $rawType === 'check in' ||
                                                                                $rawType === 'check_in'
                                                                            ) {
                                                                                $typeLabel = 'check in';
                                                                            } elseif (
                                                                                $rawType === 'out' ||
                                                                                $rawType === 'check out' ||
                                                                                $rawType === 'check_out'
                                                                            ) {
                                                                                $typeLabel = 'check out';
                                                                            } else {
                                                                                $typeLabel = $rawType;
                                                                            }

                                                                            $timeFormatted = '';
                                                                            if (!empty($entry['time'])) {
                                                                                $ts = strtotime($entry['time']);
                                                                                if ($ts !== false) {
                                                                                    $timeFormatted = date('g:i A', $ts);
                                                                                    $timeFormatted = str_replace(
                                                                                        ['AM', 'PM'],
                                                                                        ['Am', 'Pm'],
                                                                                        $timeFormatted,
                                                                                    );
                                                                                }
                                                                            }

                                                                            if (!empty($timeFormatted)) {
                                                                                $cellItems[] =
                                                                                    $typeLabel . ' : ' . $timeFormatted;
                                                                            } else {
                                                                                $cellItems[] = $typeLabel;
                                                                            }
                                                                        }
                                                                        $cellText = implode('<br>', $cellItems);
                                                                    @endphp
                                                                    <span class="att-cell-content">{!! $cellText !!}</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('exportXls')
    @if(isset($data['form_file_type']) && $data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                // Build a single combined table from all month tables
                var $combined = $('<table id="att_combined_export" style="display:none;"></table>');

                $('.month-block').each(function() {
                    // Add a full-width month label row as separator
                    var monthLabel = $(this).find('.month-heading').text().trim();
                    var colCount = $(this).find('.att-table thead tr th').length;

                    var $monthRow = $('<tr><td colspan="' + colCount + '" style="font-weight:bold;background:#d9d9d9;text-align:left;">' + monthLabel + '</td></tr>');
                    $combined.append($monthRow);

                    // Clone and append all rows (thead + tbody) from this month's table
                    $(this).find('.att-table thead tr').each(function() {
                        $combined.append($(this).clone());
                    });
                    $(this).find('.att-table tbody tr').each(function() {
                        $combined.append($(this).clone());
                    });

                    // Add a blank spacer row
                    $combined.append($('<tr><td colspan="' + colCount + '">&nbsp;</td></tr>'));
                });

                $('body').append($combined);

                $('#att_combined_export').table2excel({
                    filename: "date_wise_attendance.xls",
                });

                // Remove the temporary table after export triggers
                setTimeout(function() {
                    $('#att_combined_export').remove();
                }, 3000);
            });
        </script>
    @endif
@endsection
