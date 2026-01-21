<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BladeExport implements FromCollection, WithHeadings, WithStyles{

    private $data;
    private $headings;
    private $boldRows;

    public function __construct($data,$headings,$boldRows = []){
        $this->data = $data;
        $this->headings = $headings;
        $this->boldRows = $boldRows;
    }


    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        foreach ($this->boldRows as $rowNumber) {
            $styles[(int)$rowNumber] = ['font' => ['bold' => true]];
        }
        return $styles;
    }
}
