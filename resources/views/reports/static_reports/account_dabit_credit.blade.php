@extends('layouts.report')
@section('title', 'Account Debit Credit Report')

@section('pageCSS')
    <style>
        @media print {
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tfoot>tr>td { padding: 0 !important; }
            body { margin: 0; }
        }

        .report-matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            background-color: #fff;
        }

        .report-matrix-table th,
        .report-matrix-table td {
            border: 1px solid #b0b0b0 !important;
            padding: 6px 10px;
            vertical-align: middle;
        }

        .report-matrix-table thead th {
            background-color: #d9d9d9 !important;
            color: #000;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }

        .report-matrix-table tbody td {
            color: #222;
        }

        .report-matrix-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .tr-section-header td {
            background-color: #e9ecef !important;
            color: #1a202c !important;
            font-weight: 700 !important;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tr-subtotal td {
            background-color: #f1f3f5 !important;
            font-weight: 700 !important;
            border-top: 1.5px solid #888 !important;
            border-bottom: 1.5px solid #888 !important;
        }

        .tr-total td {
            font-weight: 700 !important;
            background-color: #d9d9d9 !important;
            border-top: 2px solid #000 !important;
            border-bottom: 2px solid #000 !important;
            font-size: 13px;
        }

        .td-particulars {
            font-weight: 600;
            min-width: 240px;
            text-align: left;
        }

        .td-amount {
            text-align: right;
            min-width: 110px;
            white-space: nowrap;
        }

        .no-data-row td {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 20px;
        }
    </style>
@endsection

@section('content')
    @php
        $data = Session::get('data') ?: [
            'page_title'     => 'Account Debit Credit Report',
            'from_date'      => date('Y-m-d'),
            'to_date'        => date('Y-m-d'),
            'branch_ids'     => [],
            'form_file_type' => 'report',
        ];

        $fromDateStr = !empty($data['from_date']) ? date('Y-m-d', strtotime($data['from_date'])) : date('Y-m-d');
        $toDateStr   = !empty($data['to_date'])   ? date('Y-m-d', strtotime($data['to_date']))   : date('Y-m-d');

        // -------------------------------------------------------------------
        // 1. Fetch Selected / Active Branches
        // -------------------------------------------------------------------
        $selectedBranches = $data['branch_ids'] ?? [];
        $hasAll = empty($selectedBranches) || in_array('all', $selectedBranches) || in_array(0, $selectedBranches) || in_array('0', $selectedBranches);

        $branchQuery = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')
            ->select('branch_id', 'branch_name');

        if (auth()->check() && !empty(auth()->user()->business_id)) {
            $branchQuery->where('business_id', auth()->user()->business_id);
        }

        if (!$hasAll) {
            $validBranchIds = array_filter(array_map('intval', $selectedBranches));
            if (!empty($validBranchIds)) {
                $branchQuery->whereIn('branch_id', $validBranchIds);
            }
        }
        $branches = $branchQuery->orderBy('branch_name', 'asc')->get();

        // -------------------------------------------------------------------
        // 2. Query Voucher Records (Grouped by Account Name & Branch ID)
        // -------------------------------------------------------------------
        $branchIds = $branches->pluck('branch_id')->toArray();
        $branchWhere = '';
        if (!empty($branchIds)) {
            $branchWhere = " AND V.BRANCH_ID IN (" . implode(',', array_map('intval', $branchIds)) . ") ";
        }

        $bizWhere = '';
        if (auth()->check() && !empty(auth()->user()->business_id)) {
            $bizWhere = " AND V.BUSINESS_ID = " . (int)auth()->user()->business_id;
        }

        $voucherData = [];
        try {
            $voucherData = \Illuminate\Support\Facades\DB::select("
                SELECT 
                    NVL(V.CHART_NAME, NVL(V.VOUCHER_ACC_NAME, 'Unassigned Account')) AS ACCOUNT_NAME,
                    V.BRANCH_ID,
                    SUM(NVL(V.VOUCHER_DEBIT, 0)) AS TOTAL_DEBIT,
                    SUM(NVL(V.VOUCHER_CREDIT, 0)) AS TOTAL_CREDIT
                FROM VW_ACCO_VOUCHER V
                WHERE V.POSTED = 1
                  AND (NVL(V.VOUCHER_DEBIT, 0) <> 0 OR NVL(V.VOUCHER_CREDIT, 0) <> 0)
                  AND TRUNC(V.VOUCHER_DATE) >= TO_DATE('{$fromDateStr}', 'YYYY-MM-DD')
                  AND TRUNC(V.VOUCHER_DATE) <= TO_DATE('{$toDateStr}', 'YYYY-MM-DD')
                  {$branchWhere}
                GROUP BY NVL(V.CHART_NAME, NVL(V.VOUCHER_ACC_NAME, 'Unassigned Account')), V.BRANCH_ID
                ORDER BY ACCOUNT_NAME ASC
            ");
        } catch (\Exception $e) {
            try {
                $voucherData = \Illuminate\Support\Facades\DB::select("
                    SELECT 
                        NVL(C.CHART_NAME, NVL(V.VOUCHER_ACC_NAME, 'Unassigned Account')) AS ACCOUNT_NAME,
                        V.BRANCH_ID,
                        SUM(NVL(V.VOUCHER_DEBIT, 0)) AS TOTAL_DEBIT,
                        SUM(NVL(V.VOUCHER_CREDIT, 0)) AS TOTAL_CREDIT
                    FROM TBL_ACCO_VOUCHER V
                    LEFT JOIN TBL_ACCO_CHART_ACCOUNT C ON V.CHART_ACCOUNT_ID = C.CHART_ACCOUNT_ID
                    WHERE (V.POSTED = 1 OR V.VOUCHER_POSTED = 1)
                      AND (V.IS_DELETED = 0 OR V.IS_DELETED IS NULL)
                      AND (NVL(V.VOUCHER_DEBIT, 0) <> 0 OR NVL(V.VOUCHER_CREDIT, 0) <> 0)
                      AND TRUNC(V.VOUCHER_DATE) >= TO_DATE('{$fromDateStr}', 'YYYY-MM-DD')
                      AND TRUNC(V.VOUCHER_DATE) <= TO_DATE('{$toDateStr}', 'YYYY-MM-DD')
                      {$bizWhere}
                      {$branchWhere}
                    GROUP BY NVL(C.CHART_NAME, NVL(V.VOUCHER_ACC_NAME, 'Unassigned Account')), V.BRANCH_ID
                    ORDER BY ACCOUNT_NAME ASC
                ");
            } catch (\Exception $ex) {
                $voucherData = [];
            }
        }

        // -------------------------------------------------------------------
        // 3. Process Data & Classify into Debit Accounts vs Credit Accounts
        // -------------------------------------------------------------------
        $rawAccounts = [];
        foreach ($voucherData as $row) {
            $accName = $row->account_name ?? $row->ACCOUNT_NAME ?? 'Unassigned Account';
            $bId     = (int)($row->branch_id ?? $row->BRANCH_ID ?? 0);
            $dr      = floatval($row->total_debit  ?? $row->TOTAL_DEBIT  ?? 0);
            $cr      = floatval($row->total_credit ?? $row->TOTAL_CREDIT ?? 0);

            if (!isset($rawAccounts[$accName])) {
                $rawAccounts[$accName] = [
                    'total_dr' => 0,
                    'total_cr' => 0,
                    'branches' => []
                ];
                foreach ($branches as $b) {
                    $rawAccounts[$accName]['branches'][(int)$b->branch_id] = [
                        'dr' => 0,
                        'cr' => 0,
                    ];
                }
            }

            if (!isset($rawAccounts[$accName]['branches'][$bId])) {
                $rawAccounts[$accName]['branches'][$bId] = ['dr' => 0, 'cr' => 0];
            }

            $rawAccounts[$accName]['branches'][$bId]['dr'] += $dr;
            $rawAccounts[$accName]['branches'][$bId]['cr'] += $cr;
            $rawAccounts[$accName]['total_dr'] += $dr;
            $rawAccounts[$accName]['total_cr'] += $cr;
        }

        $debitRows = [];
        $creditRows = [];

        $debitBranchTotals = [];
        $creditBranchTotals = [];
        foreach ($branches as $b) {
            $bId = (int)$b->branch_id;
            $debitBranchTotals[$bId] = 0;
            $creditBranchTotals[$bId] = 0;
        }

        $grandDebitTotal = 0;
        $grandCreditTotal = 0;

        foreach ($rawAccounts as $accName => $accInfo) {
            $overallNet = $accInfo['total_dr'] - $accInfo['total_cr'];
            $isDebit = ($overallNet >= 0);

            $rowObj = [
                'account_name' => $accName,
                'row_total'    => 0,
                'branches'     => []
            ];

            foreach ($branches as $b) {
                $bId = (int)$b->branch_id;
                $bDr = $accInfo['branches'][$bId]['dr'] ?? 0;
                $bCr = $accInfo['branches'][$bId]['cr'] ?? 0;

                $amt = $isDebit ? ($bDr - $bCr) : ($bCr - $bDr);

                $rowObj['branches'][$bId] = $amt;
                $rowObj['row_total'] += $amt;

                if ($isDebit) {
                    $debitBranchTotals[$bId] += $amt;
                } else {
                    $creditBranchTotals[$bId] += $amt;
                }
            }

            if ($isDebit) {
                $grandDebitTotal += $rowObj['row_total'];
                $debitRows[$accName] = $rowObj;
            } else {
                $grandCreditTotal += $rowObj['row_total'];
                $creditRows[$accName] = $rowObj;
            }
        }

        ksort($debitRows);
        ksort($creditRows);

        // Formatter Helper: positive -> "40,000", negative -> "(14,000)", zero -> "0"
        $fmtAmount = function($val) {
            if ($val == 0) return '0';
            if ($val < 0) {
                return '(' . number_format(abs($val), 0) . ')';
            }
            return number_format($val, 0);
        };
    @endphp

    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{ strtoupper($data['page_title'] ?? 'ACCOUNT DEBIT CREDIT REPORT') }}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color:#e27d00;">Date:</span>
                    <span style="color:#5578eb;">{{ ' ' . date('d-m-Y', strtotime($fromDateStr)) . ' to ' . date('d-m-Y', strtotime($toDateStr)) . ' ' }}</span>
                </h6>
                @if(count($branches) > 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color:#e27d00;">Branch:</span>
                        @foreach($branches as $bl)
                            <span style="color:#5578eb;">{{ $bl->branch_name }}</span><span style="color:#fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>

        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table id="rep_account_debit_credit_datatable" class="report-matrix-table">
                        <thead>
                            <tr>
                                <th class="td-particulars">Particulars</th>
                                @foreach($branches as $b)
                                    <th class="td-amount">{{ $b->branch_name }}</th>
                                @endforeach
                                <th class="td-amount">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($debitRows) && empty($creditRows))
                                <tr class="no-data-row">
                                    <td colspan="{{ count($branches) + 2 }}">No voucher records found for the selected criteria.</td>
                                </tr>
                            @else
                                {{-- --------------------------------------------------- --}}
                                {{-- DEBIT ACCOUNTS SECTION                             --}}
                                {{-- --------------------------------------------------- --}}
                                @if(!empty($debitRows))
                                    <tr class="tr-section-header">
                                        <td colspan="{{ count($branches) + 2 }}">
                                            <i class="la la-arrow-circle-down" style="color: #1a7f37;"></i>
                                            DEBIT ACCOUNTS (RECEIPTS / DEBITS)
                                        </td>
                                    </tr>
                                    @foreach($debitRows as $accName => $row)
                                        <tr>
                                            <td class="td-particulars" style="padding-left: 24px;">{{ $accName }}</td>
                                            @foreach($branches as $b)
                                                @php $amt = $row['branches'][(int)$b->branch_id] ?? 0; @endphp
                                                <td class="td-amount">{{ $fmtAmount($amt) }}</td>
                                            @endforeach
                                            <td class="td-amount" style="font-weight: 600;">{{ $fmtAmount($row['row_total']) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="tr-subtotal">
                                        <td class="td-particulars">Total Debit</td>
                                        @foreach($branches as $b)
                                            @php $bId = (int)$b->branch_id; @endphp
                                            <td class="td-amount">{{ $fmtAmount($debitBranchTotals[$bId] ?? 0) }}</td>
                                        @endforeach
                                        <td class="td-amount">{{ $fmtAmount($grandDebitTotal) }}</td>
                                    </tr>
                                @endif

                                {{-- --------------------------------------------------- --}}
                                {{-- CREDIT ACCOUNTS SECTION                            --}}
                                {{-- --------------------------------------------------- --}}
                                @if(!empty($creditRows))
                                    <tr class="tr-section-header">
                                        <td colspan="{{ count($branches) + 2 }}">
                                            <i class="la la-arrow-circle-up" style="color: #c0392b;"></i>
                                            CREDIT ACCOUNTS (PAYMENTS / CREDITS)
                                        </td>
                                    </tr>
                                    @foreach($creditRows as $accName => $row)
                                        <tr>
                                            <td class="td-particulars" style="padding-left: 24px;">{{ $accName }}</td>
                                            @foreach($branches as $b)
                                                @php $amt = $row['branches'][(int)$b->branch_id] ?? 0; @endphp
                                                <td class="td-amount">{{ $fmtAmount($amt) }}</td>
                                            @endforeach
                                            <td class="td-amount" style="font-weight: 600;">{{ $fmtAmount($row['row_total']) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="tr-subtotal">
                                        <td class="td-particulars">Total Credit</td>
                                        @foreach($branches as $b)
                                            @php $bId = (int)$b->branch_id; @endphp
                                            <td class="td-amount">{{ $fmtAmount($creditBranchTotals[$bId] ?? 0) }}</td>
                                        @endforeach
                                        <td class="td-amount">{{ $fmtAmount($grandCreditTotal) }}</td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                        @if(!empty($debitRows) || !empty($creditRows))
                            <tfoot>
                                <tr class="tr-total">
                                    <td class="td-particulars">Net Balance (Total Debit - Total Credit)</td>
                                    @foreach($branches as $b)
                                        @php 
                                            $bId = (int)$b->branch_id;
                                            $netB = ($debitBranchTotals[$bId] ?? 0) - ($creditBranchTotals[$bId] ?? 0);
                                        @endphp
                                        <td class="td-amount">{{ $fmtAmount($netB) }}</td>
                                    @endforeach
                                    <td class="td-amount">{{ $fmtAmount($grandDebitTotal - $grandCreditTotal) }}</td>
                                </tr>
                            </tfoot>
                        @endif
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
                $("#rep_account_debit_credit_datatable").table2excel({
                    filename: "account_debit_credit_report.xls",
                });
            });
        </script>
    @endif
@endsection
