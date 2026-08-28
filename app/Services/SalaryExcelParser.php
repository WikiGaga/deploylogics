<?php

namespace App\Services;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SalaryExcelParser
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function parse($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $payPeriod = trim((string) $sheet->getCell('A2')->getValue());
        if ($payPeriod === '') {
            throw new \InvalidArgumentException('Pay period not found in cell A2.');
        }

        $rows = [];
        $validCount = 0;
        $errorCount = 0;
        $highestRow = (int) $sheet->getHighestRow();

        for ($rowNo = 4; $rowNo <= $highestRow; $rowNo++) {
            $phoneRaw = trim((string) $sheet->getCell('F' . $rowNo)->getFormattedValue());
            $employeeName = trim((string) $sheet->getCell('C' . $rowNo)->getValue());

            if ($phoneRaw === '' && $employeeName === '') {
                continue;
            }

            $row = $this->parseRow($sheet, $rowNo, $payPeriod);
            $rows[] = $row;

            if (!empty($row['errors'])) {
                $errorCount++;
            } else {
                $validCount++;
            }
        }

        if (count($rows) === 0) {
            throw new \InvalidArgumentException('No employee rows found. Data should start from row 4 with phone in column F.');
        }

        return [
            'pay_period' => $payPeriod,
            'total_rows' => count($rows),
            'valid_rows' => $validCount,
            'error_rows' => $errorCount,
            'rows' => $rows,
        ];
    }

    protected function parseRow($sheet, $rowNo, $payPeriod)
    {
        $errors = [];
        $employeeName = trim((string) $sheet->getCell('C' . $rowNo)->getValue());
        $phoneRaw = trim((string) $sheet->getCell('F' . $rowNo)->getFormattedValue());

        if ($employeeName === '') {
            $errors[] = 'Employee name (column C) is required.';
        }

        $phone = null;
        if ($phoneRaw === '') {
            $errors[] = 'Phone (column F) is required.';
        } else {
            try {
                $phone = $this->whatsappService->formatPhoneNumber($phoneRaw);
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }
        }

        $basicSalary = $this->numericCell($sheet, 'H' . $rowNo);
        $workingHours = $this->numericCell($sheet, 'I' . $rowNo);
        $leaves = $this->numericCell($sheet, 'J' . $rowNo);
        $workingDays = 30 - $leaves;
        $actualBasic = $workingDays > 0 ? ($basicSalary / 30) * $workingDays : 0;
        $overTime = ($basicSalary / 300) * 1.25 * $workingHours;
        $bonus = $this->numericCell($sheet, 'N' . $rowNo);
        $deduction = $this->numericCell($sheet, 'O' . $rowNo);
        $govtFines = $this->numericCell($sheet, 'P' . $rowNo);
        $adminFines = $this->numericCell($sheet, 'Q' . $rowNo);
        $loanAmount = $this->numericCell($sheet, 'R' . $rowNo);
        $netPayment = $actualBasic + $overTime + $bonus - ($deduction + $govtFines + $adminFines + $loanAmount);

        $namedParams = [
            'employee_name' => $employeeName,
            'salary_date' => $payPeriod,
            'basic_salary' => $this->formatMoney($basicSalary),
            'working_hours' => $this->formatMoney($workingHours),
            'leaves' => $this->formatMoney($leaves),
            'working_days' => $this->formatMoney($workingDays),
            'actual_basic' => $this->formatMoney($actualBasic),
            'over_time' => $this->formatMoney($overTime),
            'bonus' => $this->formatMoney($bonus),
            'deduction' => $this->formatMoney($deduction),
            'govt_fines' => $this->formatMoney($govtFines),
            'admin_fines' => $this->formatMoney($adminFines),
            'loan_amount' => $this->formatMoney($loanAmount),
            'net_payment' => $this->formatMoney($netPayment),
        ];

        return [
            'row_no' => $rowNo,
            'employee_name' => $employeeName,
            'phone' => $phone,
            'phone_raw' => $phoneRaw,
            'net_payment' => round($netPayment, 3),
            'named_params' => $namedParams,
            'preview_text' => $this->whatsappService->buildPreviewText($namedParams),
            'errors' => $errors,
            'is_valid' => count($errors) === 0,
        ];
    }

    protected function numericCell($sheet, $cell)
    {
        $cellObj = $sheet->getCell($cell);

        try {
            $value = $cellObj->getCalculatedValue();
        } catch (\Throwable $e) {
            $value = $cellObj->getValue();
        }

        if ($value === null || $value === '') {
            return 0;
        }

        if (is_string($value)) {
            $value = str_replace(',', '', $value);
        }

        return (float) $value;
    }

    protected function formatMoney($value)
    {
        return number_format((float) $value, 2, '.', '');
    }
}
