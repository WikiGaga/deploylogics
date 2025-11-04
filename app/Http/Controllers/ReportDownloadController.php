<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Session;
use Mpdf\Mpdf;
use Dompdf\Dompdf;
use Dompdf\Options;
class ReportDownloadController extends Controller
{
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
            
            $html = '<h1 style="text-align: center;">Report</h1>';
            $chunks = array_chunk($results, 50); // Adjust chunk size for performance

            foreach ($chunks as $chunk) {
                $html .= '<table border="1" style="width: 100%; border-collapse: collapse; font-size: 10px;">';
                $html .= '<thead><tr>';

                if (count($chunk) > 0) {
                    $original_keys = array_keys((array)$chunk[0]);

                    $new_keys = array_map(function($key) {
                            return ucwords(str_replace('_', ' ', $key));
                        }, $original_keys);

                    foreach ($new_keys as $header) {
                        $html .= '<th style="background-color: #f2f2f2; padding: 5px;">' . htmlspecialchars($header) . '</th>';
                    }
                }

                $html .= '</tr></thead><tbody>';

                foreach ($chunk as $row) {
                    $html .= '<tr>';
                    foreach ((array) $row as $cell) {
                        $html .= '<td style="padding: 5px; word-wrap: break-word;">' . htmlspecialchars($cell) . '</td>';
                    }
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
                $html .= '<div style="page-break-after: always;"></div>';
            }

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true); // Important for loading remote assets (like images/CSS)

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);

            // 3. Set paper size and orientation (optional)
            $dompdf->setPaper('A4', 'portrait');

            // 4. Render the HTML to PDF
            $dompdf->render();

            // 5. Stream the PDF to the browser and force download
            $fileName = $fileName . '.pdf';

            return $dompdf->stream($fileName, ["Attachment" => 1]);
        }

      


        // 1. Define the file path (must match where the Job saved the file)
        // $filePath = 'reports/' . $fileName;

        // // 2. Check if the file exists on the 'public' disk
        // if (!Storage::disk('public')->exists($filePath)) {
        //     // Log the error for debugging
        //     logger()->error("Download failed: File not found at path: {$filePath}");
        //     return back()->with('error', 'The requested report file was not found.');
        // }

        // // 3. Authorization Check: Ensure only the original requester can download it
        // $downloadRecord = DB::table('tbl_listing_downloads')
        //     ->where('FILE_NAME', $fileName)
        //     ->where('USER_ID', Auth::id())
        //     ->first();
        
        // if (!$downloadRecord) {
        //      // If the record doesn't exist or doesn't belong to the current user
        //      return abort(403, 'Unauthorized access to this report.');
        // }

        // // 4. Serve the file for download
        // // The second argument forces the browser to download the file with the original name
        // return Storage::disk('public')->download($filePath, $fileName);
    }
}
