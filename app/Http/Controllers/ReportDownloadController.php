<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Session;
use App\Traits\BuildsWideTablePdf;

class ReportDownloadController extends Controller
{
    use BuildsWideTablePdf;

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

            $headers = [
                "Content-Type" => "text/csv",
                "Content-Disposition" => "attachment; filename={$filename}",
            ];

            $callback = function()use ($results) {
                $handle = fopen('php://output', 'w');
                 if (count($results) > 0) {

                    $original_keys = array_keys((array)$results[0]);

                    $new_keys = array_map(function($key) {
                            return ucwords(str_replace('_', ' ', $key));
                        }, $original_keys);

                        fputcsv($handle, $new_keys);
                    }

                    foreach ($results as $row) {
                        fputcsv($handle, (array) $row);
                    }
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
