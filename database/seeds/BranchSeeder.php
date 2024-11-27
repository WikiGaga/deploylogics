<?php

use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'Risen Fateh Ghar',
            'Risen AR Garden',
            'Risen Shad Bagh',
            'Risen - ASKARI XI',
            'Risen - Gajjumata',
            'Risen - Manawan',
        ];
        $uuid = 2;
        $i = 1;
        foreach ($data as $row){
            $row = trim($row);
            $name = strtolower(strtoupper($row));
            if(!\App\Models\TblSoftBranch::where(\Illuminate\Support\Facades\DB::raw("lower('branch_name')"),$name)->exists()){
                \App\Models\TblSoftBranch::create([
                    'branch_id' => $uuid,
                    'branch_name' => $row,
                    'branch_entry_status' => 1,
                    'branch_user_id' => 81,
                    'business_id' => 1,
                    'company_id' => 1,
                ]);
                $uuid = $uuid + $i;
                $i = $i + 1;
            }
        }
    }
}
