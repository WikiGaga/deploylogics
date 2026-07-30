@extends('layouts.report')
@section('title', 'Daily Check In Check Out')

@section('pageCSS')
    <style>
        @media print {
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tfoot>tr>td { padding: 0 !important; }
            body { margin: 0; }
        }

        .daily-att-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            background-color: #fff;
        }

        .daily-att-table th,
        .daily-att-table td {
            border: 1px solid #b0b0b0 !important;
            padding: 5px 10px;
            vertical-align: middle;
        }

        .daily-att-table thead th {
            background-color: #d9d9d9 !important;
            color: #000;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }

        .daily-att-table tbody td {
            color: #222;
        }

        .daily-att-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .td-emp  { font-weight: 600; min-width: 140px; }
        .td-branch { color: #e27d00; font-weight: 500; min-width: 100px; }
        .td-date { text-align: center; min-width: 90px; }
        .td-time { text-align: center; min-width: 90px; }
        .td-checkin  { color: #1a7f37; font-weight: 600; }
        .td-checkout { color: #c0392b; font-weight: 600; }

        .no-data-row td { text-align: center; color: #888; font-style: italic; padding: 20px; }
    </style>
@endsection

@section('content')
    @php
        $data = Session::get('data') ?: [
            'page_title'  => 'Daily check in check out',
            'from_date'   => date('Y-m-d'),
            'to_date'     => date('Y-m-d'),
            'branch_ids'  => [],
            'form_file_type' => 'report',
        ];

        $fromDateStr = !empty($data['from_date']) ? date('Y-m-d', strtotime($data['from_date'])) : date('Y-m-d');
        $toDateStr   = !empty($data['to_date'])   ? date('Y-m-d', strtotime($data['to_date']))   : date('Y-m-d');

        // -------------------------------------------------------------------
        // 1. Build Branch Filter
        // -------------------------------------------------------------------
        $branchWhere = '';
        $selectedBranches = $data['branch_ids'] ?? [];
        if (is_array($selectedBranches) && count($selectedBranches) > 0) {
            $hasAll = in_array('all', $selectedBranches) || in_array(0, $selectedBranches) || in_array('0', $selectedBranches);
            if (!$hasAll) {
                $validBranchIds = array_filter(array_map('intval', $selectedBranches));
                if (!empty($validBranchIds)) {
                    $branchWhere = ' AND A.BRANCH_ID IN (' . implode(',', $validBranchIds) . ') ';
                }
            }
        }

        // -------------------------------------------------------------------
        // 2. Query Raw Attendance Records (Sorted by Employee, Date, Branch, Time)
        // -------------------------------------------------------------------
        $rawRecords = [];
        try {
            $rawRecords = \Illuminate\Support\Facades\DB::select("
                SELECT
                    A.EMP_ID,
                    NVL(E.EMPLOYEE_NAME, 'Employee #' || A.EMP_ID) AS EMPLOYEE_NAME,
                    A.BRANCH_ID,
                    NVL(B.BRANCH_NAME, 'Main Branch') AS BRANCH_NAME,
                    TO_CHAR(A.ATTENDANCE_DATE, 'YYYY-MM-DD') AS ATTENDANCE_DATE_STR,
                    A.ATTENDANCE_TIME,
                    A.ATTENDANCE_TYPE
                FROM TBL_HR_ATTENDENCE_DTL A
                LEFT JOIN TBL_PAYR_EMPLOYEE E ON A.EMP_ID    = E.EMPLOYEE_ID
                LEFT JOIN TBL_SOFT_BRANCH   B ON A.BRANCH_ID = B.BRANCH_ID
                WHERE (A.IS_DELETED = 0 OR A.IS_DELETED IS NULL)
                  AND TRUNC(A.ATTENDANCE_DATE) >= TO_DATE('{$fromDateStr}', 'YYYY-MM-DD')
                  AND TRUNC(A.ATTENDANCE_DATE) <= TO_DATE('{$toDateStr}', 'YYYY-MM-DD')
                  {$branchWhere}
                ORDER BY E.EMPLOYEE_NAME ASC, A.ATTENDANCE_DATE ASC, A.BRANCH_ID ASC, A.ATTENDANCE_TIME ASC
            ");
        } catch (\Exception $e) {
            $rawRecords = [];
        }

        // -------------------------------------------------------------------
        // 3. Group Records by [Employee + Date + Branch]
        // -------------------------------------------------------------------
        $groups = [];
        foreach ($rawRecords as $rec) {
            $empId    = $rec->emp_id   ?? $rec->EMP_ID   ?? 0;
            $dateStr  = $rec->attendance_date_str ?? $rec->ATTENDANCE_DATE_STR ?? '';
            $branchId = $rec->branch_id ?? $rec->BRANCH_ID ?? 0;

            if (!$dateStr) continue;

            $groupKey = "{$empId}_{$dateStr}_{$branchId}";
            $groups[$groupKey][] = $rec;
        }

        // -------------------------------------------------------------------
        // 4. Pair Check-In with Check-Out per Group
        // -------------------------------------------------------------------
        $displayRows = [];
        foreach ($groups as $records) {
            $empName    = $records[0]->employee_name ?? $records[0]->EMPLOYEE_NAME ?? '';
            $branchName = $records[0]->branch_name   ?? $records[0]->BRANCH_NAME   ?? 'Main Branch';
            $dateStr    = $records[0]->attendance_date_str ?? $records[0]->ATTENDANCE_DATE_STR ?? '';

            $i = 0;
            $totalInGroup = count($records);

            while ($i < $totalInGroup) {
                $recType = strtolower(trim($records[$i]->attendance_type ?? $records[$i]->ATTENDANCE_TYPE ?? ''));
                $isOut   = (strpos($recType, 'out') !== false || in_array($recType, ['o', '2', 'exit', 'leave']));

                if (!$isOut) {
                    // Check-In entry
                    $checkIn  = $records[$i]->attendance_time ?? $records[$i]->ATTENDANCE_TIME ?? '';
                    $checkOut = null;

                    // Pair with immediately next record if it is a Check-Out
                    if ($i + 1 < $totalInGroup) {
                        $nextType  = strtolower(trim($records[$i + 1]->attendance_type ?? $records[$i + 1]->ATTENDANCE_TYPE ?? ''));
                        $nextIsOut = (strpos($nextType, 'out') !== false || in_array($nextType, ['o', '2', 'exit', 'leave']));

                        if ($nextIsOut) {
                            $checkOut = $records[$i + 1]->attendance_time ?? $records[$i + 1]->ATTENDANCE_TIME ?? '';
                            $i++; // Consume check-out
                        }
                    }

                    $displayRows[] = [
                        'emp_name'    => $empName,
                        'branch_name' => $branchName,
                        'date'        => $dateStr,
                        'check_in'    => $checkIn,
                        'check_out'   => $checkOut,
                    ];
                } else {
                    // Standalone Check-Out (without prior Check-In)
                    $displayRows[] = [
                        'emp_name'    => $empName,
                        'branch_name' => $branchName,
                        'date'        => $dateStr,
                        'check_in'    => null,
                        'check_out'   => $records[$i]->attendance_time ?? $records[$i]->ATTENDANCE_TIME ?? '',
                    ];
                }
                $i++;
            }
        }

        // Helper function for formatting time
        $fmtTime = function($raw) {
            if (empty($raw)) return '';
            $ts = strtotime($raw);
            return $ts !== false ? date('g:i A', $ts) : $raw;
        };
    @endphp

    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{ strtoupper($data['page_title'] ?? 'DAILY CHECK IN CHECK OUT') }}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color:#e27d00;">Date:</span>
                    <span style="color:#5578eb;">{{ ' ' . date('d-m-Y', strtotime($fromDateStr)) . ' to ' . date('d-m-Y', strtotime($toDateStr)) . ' ' }}</span>
                </h6>
                @if(isset($data['branch_ids']) && is_array($data['branch_ids']) && count($data['branch_ids']) > 0 && !in_array('all', $data['branch_ids']) && !in_array(0, $data['branch_ids']) && !in_array('0', $data['branch_ids']))
                    @php
                        $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')
                            ->whereIn('branch_id', $data['branch_ids'])
                            ->get(['branch_id', 'branch_name']);
                    @endphp
                    @if(count($branch_lists) > 0)
                        <h6 class="kt-invoice__criteria">
                            <span style="color:#e27d00;">Branch:</span>
                            @foreach($branch_lists as $bl)
                                <span style="color:#5578eb;">{{ $bl->branch_name }}</span><span style="color:#fd397a;">, </span>
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
                    <table id="daily_att_datatable" class="daily-att-table">
                        <thead>
                            <tr>
                                <th class="td-emp">Employee Name</th>
                                <th class="td-branch">Branch</th>
                                <th class="td-date">Date</th>
                                <th class="td-time td-checkin">Check In</th>
                                <th class="td-time td-checkout">Check Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($displayRows))
                                <tr class="no-data-row">
                                    <td colspan="5">No attendance records found for the selected criteria.</td>
                                </tr>
                            @else
                                @foreach($displayRows as $row)
                                    <tr>
                                        <td class="td-emp">{{ $row['emp_name'] }}</td>
                                        <td class="td-branch">{{ $row['branch_name'] }}</td>
                                        <td class="td-date">{{ date('d/m/Y', strtotime($row['date'])) }}</td>
                                        <td class="td-time td-checkin">{{ $fmtTime($row['check_in']) }}</td>
                                        <td class="td-time td-checkout">{{ $fmtTime($row['check_out']) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('exportXls')
    @if(isset($data['form_file_type']) && $data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#daily_att_datatable").table2excel({
                    filename: "daily_check_in_check_out.xls",
                });
            });
        </script>
    @endif
@endsection
