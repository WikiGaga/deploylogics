<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class BladeExport implements FromCollection, WithHeadings{

    private $data;
    private $headings;

    public function __construct($data,$headings){
        $this->data = $data;
        $this->headings = $headings;
    }


    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
