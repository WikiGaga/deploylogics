<?php

namespace App\Traits;

use Dompdf\Dompdf;
use Dompdf\Options;

trait BuildsWideTablePdf
{
    protected function countReportTableColumns(string $html): int
    {
        $maxColumns = 0;

        if (!preg_match_all('/<tr\b[^>]*>(.*?)<\/tr>/is', $html, $rows)) {
            return 0;
        }

        foreach ($rows[1] as $rowHtml) {
            if (!preg_match_all('/<t[hd]\b/i', $rowHtml, $cells)) {
                continue;
            }

            $maxColumns = max($maxColumns, count($cells[0]));
        }

        return $maxColumns;
    }

    protected function buildReportPdfConfig(string $html, ?int $columnCount = null): array
    {
        if ($columnCount === null) {
            $columnCount = $this->countReportTableColumns($html);
        }

        $a4LandscapeWidth = 841.89;
        $a4LandscapeHeight = 595.28;
        $columnWidthPt = 46;
        $horizontalMarginPt = 60;
        $maxPageWidthPt = 2400;

        $requiredWidth = ($columnCount * $columnWidthPt) + $horizontalMarginPt;
        $pageWidth = max($a4LandscapeWidth, min($requiredWidth, $maxPageWidthPt));

        if ($columnCount > 10) {
            $fontSize = max(6, (int) round(10 - (($columnCount - 10) * 0.35)));
            $extraCss = '<style>'
                . 'table td, table th {'
                . ' font-size: ' . $fontSize . 'px !important;'
                . ' padding: 2px 3px !important;'
                . ' word-wrap: break-word !important;'
                . ' overflow-wrap: break-word !important;'
                . ' white-space: normal !important;'
                . '}'
                . '</style>';
            $html = $this->injectReportPdfCss($html, $extraCss);
        }

        return [
            'html' => $html,
            'paper' => [0, 0, $pageWidth, $a4LandscapeHeight],
        ];
    }

    protected function injectReportPdfCss(string $html, string $css): string
    {
        if (stripos($html, '</head>') !== false) {
            return preg_replace('/<\/head>/i', $css . '</head>', $html, 1);
        }

        return $css . $html;
    }

    protected function buildListingTablePdfHtml(array $results, string $title = 'Report'): string
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>'
            . 'body{font-family:Verdana,sans-serif;font-size:10px;margin:0;padding:0;color:#000;}'
            . 'table{width:100%;border-collapse:collapse;table-layout:auto;page-break-inside:auto;}'
            . 'thead{display:table-header-group;}'
            . 'td,th{padding:4px 5px;word-wrap:break-word;overflow-wrap:break-word;white-space:normal;border:1px solid #ccc;}'
            . 'th{background:#f2f2f2;font-size:10px;}'
            . 'td{font-size:10px;}'
            . '@page{margin:8mm;}'
            . '</style></head><body>';
        $html .= '<h1 style="text-align:center;font-size:14px;">' . htmlspecialchars($title) . '</h1>';

        $chunks = array_chunk($results, 50);

        foreach ($chunks as $chunk) {
            $html .= '<table><thead><tr>';

            if (count($chunk) > 0) {
                $originalKeys = array_keys((array) $chunk[0]);
                $headers = array_map(function ($key) {
                    return ucwords(str_replace('_', ' ', $key));
                }, $originalKeys);

                foreach ($headers as $header) {
                    $html .= '<th>' . htmlspecialchars($header) . '</th>';
                }
            }

            $html .= '</tr></thead><tbody>';

            foreach ($chunk as $row) {
                $html .= '<tr>';
                foreach ((array) $row as $cell) {
                    $html .= '<td>' . htmlspecialchars((string) $cell) . '</td>';
                }
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            $html .= '<div style="page-break-after:always;"></div>';
        }

        $html .= '</body></html>';

        return $html;
    }

    protected function createWideTableDompdf(string $html, ?int $columnCount = null): Dompdf
    {
        $pdfConfig = $this->buildReportPdfConfig($html, $columnCount);
        $pdfConfig['html'] = $this->injectReportPdfCss(
            $pdfConfig['html'],
            '<style>'
            . '@page{margin-top:12mm;margin-right:8mm;margin-bottom:8mm;margin-left:8mm;}'
            . '#content{padding-top:10mm !important;}'
            . '.kt-portlet .kt-portlet__head{padding-top:4mm !important;}'
            . 'h1.kt-invoice__title,.kt-invoice__title{margin-top:0 !important;}'
            . '</style>'
        );

        $options = new Options();
        $options->set('dpi', 100);
        $options->set('isPhpEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->setDefaultFont('roboto');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($pdfConfig['html'], 'UTF-8');
        $dompdf->setPaper($pdfConfig['paper']);
        $dompdf->render();

        return $dompdf;
    }
}
