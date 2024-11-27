<?php

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'ARGENTINA',
            'AUSTRALIA',
            'AUSTRIAN',
            'BAHRAIN',
            'BELARUS',
            'BELGIUM',
            'BRAZIL',
            'CANADA',
            'CHINA',
            'DENMARK',
            'EGYPT',
            'ENGLAND',
            'FRANCE',
            'GERMANY',
            'HONG KONG',
            'INDIA',
            'INDONESIA',
            'IRELAND',
            'ITALY',
            'JAPAN',
            'KOREA',
            'LONDON',
            'MALAYSIA',
            'MEXICO',
            'MOROCCO',
            'NETHERLAND',
            'NEWZELAND',
            'OMAN',
            'PAKISTAN',
            'PHILIPPINES',
            'POLAND',
            'QATER',
            'RUSSIA',
            'SAUDI ARABIA',
            'SAUDIA',
            'SERBIA',
            'SINGAPORE',
            'SOUTH AFRICA',
            'SPAIN',
            'SRI LANKA',
            'SWITZERLAND',
            'THAILAND',
            'TIWAN',
            'TUNISIA',
            'TURKEY',
            'UAE',
            'UK',
            'UNITED STATES',
            'VIET NAM',
        ];
        $uuid = 87317222282037;
        $i = 1;
        foreach ($data as $row){
            $row = trim($row);
            $name = strtolower(strtoupper($row));
            if(!\App\Models\TblDefiCountry::where(\Illuminate\Support\Facades\DB::raw("lower('country_name')"),$name)->exists()){
                \App\Models\TblDefiCountry::create([
                    'country_id' => $uuid,
                    'country_name' => $row,
                    'country_entry_status' => 1,
                    'country_user_id' => 81,
                    'business_id' => 1,
                    'company_id' => 1,
                    'branch_id' => 1,
                ]);
                $uuid = $uuid + $i ;
                $i = $i + 1;
            }
        }
    }
}
