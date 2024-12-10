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
        // Start by preparing the basic HTML content for the PDF
        $html = '<h1>Report</h1>';

        // Loop through the results and append to HTML for the table
        $html .= '<table border="1" style="width: 100%; border-collapse: collapse;">';
        $html .= '<thead><tr>';

        // Add headers based on the first row keys
        if (count($results) > 0) {
            foreach (array_keys((array) $results[0]) as $header) {
                $html .= '<th style="padding: 5px; text-align: left;">' . htmlspecialchars($header) . '</th>';
            }
        }

        $html .= '</tr></thead>';
        $html .= '<tbody>';

        // Loop through results to add rows
        foreach ($results as $row) {
            $html .= '<tr>';
            foreach ((array) $row as $cell) {
                $html .= '<td style="padding: 5px;">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        // Generate the PDF
        try {
            $pdf = PDF::loadHTML($html);
            $filePath = storage_path('app/reports/' . $this->fileName);
            $pdf->save($filePath);
            \Log::info('PDF generated successfully at ' . $filePath);
        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
        }
    }



}
