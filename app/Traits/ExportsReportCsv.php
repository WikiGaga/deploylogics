<?php

namespace App\Traits;

trait ExportsReportCsv
{
    protected function formatReportExportNumber($value, int $decimal = 0): string
    {
        return number_format((float) $value, $decimal, '.', '');
    }

    protected function normalizeReportCsvCell($value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            return $this->formatReportExportNumber($value, 10);
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    protected function escapeReportCsvCell(string $cell): string
    {
        if ($cell === '' || !preg_match('/[",\r\n\t]/', $cell)) {
            return $cell;
        }

        return '"' . str_replace('"', '""', $cell) . '"';
    }

    protected function encodeReportCsvLine(array $row): string
    {
        $cells = array_map(function ($value) {
            return $this->escapeReportCsvCell($this->normalizeReportCsvCell($value));
        }, $row);

        $line = implode(',', $cells) . "\r\n";

        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($line, 'UTF-16LE', 'UTF-8');
        }

        return iconv('UTF-8', 'UTF-16LE//IGNORE', $line);
    }

    protected function writeReportCsvBom($handle): void
    {
        fwrite($handle, "\xFF\xFE");
    }

    protected function putReportCsvRow($handle, array $row): void
    {
        fwrite($handle, $this->encodeReportCsvLine($row));
    }

    protected function writeReportCsv($handle, array $headings, array $rows): void
    {
        $this->writeReportCsvBom($handle);

        if (!empty($headings)) {
            $this->putReportCsvRow($handle, $headings);
        }

        foreach ($rows as $row) {
            $this->putReportCsvRow($handle, $row);
        }
    }

    protected function beginReportCsvStream($handle, array $headings): void
    {
        $this->writeReportCsvBom($handle);

        if (!empty($headings)) {
            $this->putReportCsvRow($handle, $headings);
        }
    }

    protected function flushReportCsvStream(): void
    {
        if (function_exists('ob_get_level') && ob_get_level() > 0) {
            @ob_flush();
        }
        flush();
    }

    protected function reportCsvResponseHeaders(string $fileName): array
    {
        return [
            'Content-Type' => 'text/csv; charset=UTF-16LE',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'no-store, no-cache',
        ];
    }
}
