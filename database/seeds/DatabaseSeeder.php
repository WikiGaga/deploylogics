<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // $this->call(UserSeeder::class);
        /*$list = [
            ['key'=> 'da','val'=> 'da','type'=> 'tax_on'],
            ['key'=> 'mrp','val'=> 'MRP','type'=> 'tax_on'],
            ['key'=> 'ga','val'=> 'GA','type'=> 'disc_on'],
            ['key'=> 'ta','val'=> 'TA','type'=> 'disc_on'],
            ['key'=> 'ia','val'=> 'IA','type'=> 'disc_on'],
        ];

        foreach ($list as $r){
            \App\Models\Defi\TblDefiConstants::create([
                'constants_id' => \App\Library\Utilities::uuid(),
                'constants_key' => $r['key'],
                'constants_value' => $r['val'],
                'constants_type' => $r['type'],
                'constants_status' => 1,
            ]);
        }*/
    }
}
