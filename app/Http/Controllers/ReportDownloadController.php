<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Session;
use App\Traits\BuildsWideTablePdf;
use App\Traits\ExportsReportCsv;

class ReportDownloadController extends Controller
{
    use BuildsWideTablePdf;
    use ExportsReportCsv;

    /**
     * Finds the generated report and forces a download.
     *
     */
    public function download()
    { 
        $qry = Session::pull('report_download_qry');
        $case_name =  Session::pull('report_download_case_name');
        $fileName =  Session::pull('report_download_fileName');

        if (!$qry) {
            return redirect('/')->with('error', 'Download parameters expired or missing.');
        }   

        $results = DB::select($qry);

        if (strpos($fileName, '.csv') !== false) {
            $filename = "data.csv";
            $headers = $this->reportCsvResponseHeaders($filename);

            $callback = function () use ($results) {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    return;
                }

                $headings = [];
                $rows = [];

                if (count($results) > 0) {
                    $originalKeys = array_keys((array) $results[0]);
                    $headings = array_map(function ($key) {
                        return ucwords(str_replace('_', ' ', $key));
                    }, $originalKeys);

                    foreach ($results as $row) {
                        $rows[] = array_values((array) $row);
                    }
                }

                $this->writeReportCsv($handle, $headings, $rows);
                fclose($handle);
            };

              DB::table('tbl_listing_downloads')->insert([
                'LISTING_CASE' => $case_name,
                'FILE_NAME' => $fileName,
                'USER_ID' => Auth::user()->id,
                'CREATED_AT' => now(),
                'DELETED' => 0,
            ]);

            return response()->stream($callback, 200, $headers);

        } elseif (strpos($fileName, '.pdf') !== false) {
            $columnCount = !empty($results) ? count((array) $results[0]) : 0;
            $html = $this->buildListingTablePdfHtml($results, 'Report');
            $dompdf = $this->createWideTableDompdf($html, $columnCount);

            $fileName = $fileName . '.pdf';

            DB::table('tbl_listing_downloads')->insert([
                'LISTING_CASE' => $case_name,
                'FILE_NAME' => $fileName,
                'USER_ID' => Auth::user()->id,
                'CREATED_AT' => now(),
                'DELETED' => 0,
            ]);

            return $dompdf->stream($fileName, ['Attachment' => 1]);
        }
    }
}
