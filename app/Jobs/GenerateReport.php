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
        $chunkSize = 100; // Number of rows per page (adjust based on your needs)
        $chunks = array_chunk($results, $chunkSize);

        // Start by preparing the first part of the HTML (for the title, etc.)
        $html = '<h1>Report</h1>';

        // Initialize a variable to store the final PDF content
        $pdf = null;

        // Loop through each chunk of data and generate the corresponding PDF
        foreach ($chunks as $pageIndex => $chunk) {
            $html .= "<h2>Page " . ($pageIndex + 1) . "</h2>";
            $html .= '<table border="1" style="width: 100%; border-collapse: collapse;">';
            $html .= '<thead><tr>';

            // Add headers (use the keys of the first row of the chunk as headers)
            if (count($chunk) > 0) {
                foreach (array_keys((array) $chunk[0]) as $header) {
                    $html .= '<th style="padding: 5px; text-align: left;">' . htmlspecialchars($header) . '</th>';
                }
            }

            $html .= '</tr></thead>';
            $html .= '<tbody>';

            // Add rows
            foreach ($chunk as $row) {
                $html .= '<tr>';
                foreach ((array) $row as $cell) {
                    $html .= '<td style="padding: 5px;">' . htmlspecialchars($cell) . '</td>';
                }
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            // Add a page break after each chunk (for multiple pages)
            if ($pageIndex < count($chunks) - 1) {
                $html .= '<div style="page-break-after: always;"></div>';
            }

            // Generate the PDF for the current chunk and append it to the final PDF
            if ($pdf === null) {
                $pdf = PDF::loadHTML($html); // For the first chunk, initialize the PDF
            } else {
                $pdf->addPage(); // Add a new page for subsequent chunks
                $pdf->loadHTML($html);
            }

            // Reset the HTML content for the next chunk to free memory
            $html = ''; // Empty the HTML variable after processing each chunk
        }

        // Save the final PDF file
        $filePath = storage_path('app/reports/' . $this->fileName);
        $pdf->save($filePath);
    }


}
