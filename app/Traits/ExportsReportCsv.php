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

    protected function writeReportCsvBom($handle): void
    {
        fwrite($handle, "\xEF\xBB\xBF");
    }

    protected function putReportCsvRow($handle, array $row): void
    {
        fputcsv($handle, array_map([$this, 'normalizeReportCsvCell'], $row));
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

    protected function reportCsvResponseHeaders(string $fileName): array
    {
        return [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'no-store, no-cache',
        ];
    }
}
