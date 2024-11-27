<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GroupExport implements WithMapping{

    public function map($data): array
    {
        $rows = [];
        return $rows;
    }

    /*public function headings(): array
    {
        $headings = [

        ];
        return $headings;
    }*/
}
