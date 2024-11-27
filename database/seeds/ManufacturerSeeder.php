<?php

use Illuminate\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ["1","NONE",],
        ];
        foreach ($data as $row){
            $id = trim($row[0]);
            $name = trim($row[1]);
            \App\Models\TblPurcManufacturer::create([
                'manufacturer_id' => $id,
                'manufacturer_name' => $name,
                'manufacturer_entry_status' => 1,
                'manufacturer_user_id' => 81,
                'business_id' => 1,
                'company_id' => 1,
                'branch_id' => 1,
            ]);
        }
    }
}
