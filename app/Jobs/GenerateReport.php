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

class GenerateReport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $qry;
    protected $fileName;

    public function __construct($qry, $fileName)
    {
        $this->qry = $qry;
        $this->fileName = $fileName;
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

    private function generatePdf($results)
{
    // Initialize dompdf instance
    $pdf = PDF::getFacadeRoot(); // dompdf instance

    // Add page to start the PDF
    $pdf->addPage();

    // Table headers (this will be added on the first page)
    $html = '<h1>Report</h1>';
    $html .= '<table border="1" style="width: 100%; border-collapse: collapse;">';
    $html .= '<thead><tr>';

    if (count($results) > 0) {
        foreach (array_keys((array) $results[0]) as $header) {
            $html .= '<th style="padding: 5px; text-align: left;">' . htmlspecialchars($header) . '</th>';
        }
    }

    $html .= '</tr></thead>';
    $html .= '<tbody>';

    // Chunk the results and process them
    $chunkSize = 500; // Number of rows per chunk
    $chunks = array_chunk($results, $chunkSize);

    foreach ($chunks as $pageIndex => $chunk) {
        // Add the data rows for the current chunk
        foreach ($chunk as $row) {
            $html .= '<tr>';
            foreach ((array) $row as $cell) {
                $html .= '<td style="padding: 5px;">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }

        // Write the current chunk to the PDF
        $pdf->loadHTML($html);
        $html = ''; // Reset the HTML content for the next chunk

        // Add a page break after each chunk except for the last one
        if ($pageIndex < count($chunks) - 1) {
            $pdf->addPage();
        }
    }

    $html .= '</tbody></table>';

    // Save the generated PDF to a file
    $filePath = storage_path('app/reports/' . $this->fileName);
    $pdf->save($filePath);
}



}
