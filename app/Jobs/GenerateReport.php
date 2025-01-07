<?php

namespace App\Jobs;

// namespace App\Jobs\GenerateReport\GenerateReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade as PDF;
use App\Events\PusherNotifyEvent;
use Mpdf\Mpdf;

class GenerateReport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $qry;
    private $fileName;
    private $listingCase;
    private $userId;

    public function __construct($qry, $fileName, $listingCase, $userId)
    {
        $this->qry = $qry;
        $this->fileName = $fileName;
        $this->listingCase = $listingCase;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Execute the query and get the results
        $results = DB::select($this->qry);

        // Generate the CSV or PDF file
        if (strpos($this->fileName, '.csv') !== false) {
            $this->generateCsv($results);
        } elseif (strpos($this->fileName, '.pdf') !== false) {
            $this->generatePdf($results);
        }

        DB::table('tbl_listing_downloads')->insert([
            'LISTING_CASE' => $this->listingCase,
            'FILE_NAME' => $this->fileName,
            'USER_ID' => $this->userId,
            'CREATED_AT' => now(),
            'DELETED' => 0,
        ]);

        // event(new PusherNotifyEvent($this->userId, 'Your report is ready to download.', $this->fileName));
        broadcast(new PusherNotifyEvent($this->userId,'Test message', 'https://example.com/report'));


    }

    private function generateCsv($results)
    {
        $filePath = storage_path('app/reports/' . $this->fileName);

        // Open a file pointer
        $file = fopen($filePath, 'w');

        // Write the headers (adjust as needed for your query columns)
        if (count($results) > 0) {
            fputcsv($file, array_keys((array) $results[0]));
        }

        // Write the data
        foreach ($results as $row) {
            fputcsv($file, (array) $row);
        }

        fclose($file);

        // Save the file
        Storage::disk('local')->put('reports/' . $this->fileName, file_get_contents($filePath));

    }
    // private function generatePdf($results)
    // {
    //     $html = '<h1 style="font-size: 16px; text-align: center;">Report</h1>';

    //     $chunkSize = 200; // Number of rows per page
    //     $chunks = array_chunk($results, $chunkSize);

    //     foreach ($chunks as $pageIndex => $chunk) {
    //         // $html .= "<h2>Page " . ($pageIndex + 1) . "</h2>";
    //         $html .= '<table border="1" style="width: 100%; border-collapse: collapse;font-size: 6px;">';
    //         $html .= '<thead><tr>';

    //         if (count($chunk) > 0) {
    //             foreach (array_keys((array) $chunk[0]) as $header) {
    //                 $html .= '<th style="padding: 5px; text-align: left; background-color: #f2f2f2;">' . htmlspecialchars($header) . '</th>';
    //             }
    //         }

    //         $html .= '</tr></thead>';
    //         $html .= '<tbody>';

    //         foreach ($chunk as $row) {
    //             $html .= '<tr>';
    //             foreach ((array) $row as $cell) {
    //                 $html .= '<td style="padding: 5px; text-align: left; word-wrap: break-word;">' . htmlspecialchars($cell) . '</td>';
    //             }
    //             $html .= '</tr>';
    //         }

    //         $html .= '</tbody>';
    //         $html .= '</table>';
    //         $html .= '<div style="page-break-after: always;"></div>'; // Add page break
    //     }

    //     // Generate PDF from HTML
    //     $pdf = PDF::loadHTML($html);

    //     $filePath = storage_path('app/reports/' . $this->fileName);
    //     $pdf->save($filePath);
    // }

    private function generatePdf($results)
{
    $html = '<h1 style="text-align: center;">Report</h1>';
    $chunks = array_chunk($results, 50); // Adjust chunk size for performance

    foreach ($chunks as $chunk) {
        $html .= '<table border="1" style="width: 100%; border-collapse: collapse; font-size: 10px;">';
        $html .= '<thead><tr>';

        if (count($chunk) > 0) {
            foreach (array_keys((array) $chunk[0]) as $header) {
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

    $filePath = storage_path('app/reports/' . $this->fileName);

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE); // Save file to disk
}


}
