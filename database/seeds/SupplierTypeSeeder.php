<?php

use Illuminate\Database\Seeder;

class SupplierTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'LOCAL VENDORS-B',
            'LOCAL VENDOR-C',
            'SHAH ALAM VENDOR -C',
            'SHAH ALAM VENDOR-B',
            'SHAH ALAM VENDOR- CROCKERY AND OTHERS',
            'LOCAL VENDORS- NON FOOD',
            'OUTSOURCED VENDOR'
        ];
        $uuid = 16719820182137;
        $i = 1;
        foreach ($data as $row){
            $row = trim($row);
            $name = strtolower(strtoupper($row));
            if(!\App\Models\TblPurcSupplierType::where(\Illuminate\Support\Facades\DB::raw("lower('supplier_type_name')"),$name)->exists()){
                \App\Models\TblPurcSupplierType::create([
                    'supplier_type_id' => $uuid,
                    'supplier_type_name' => $row,
                    'supplier_type_entry_status' => 1,
                    'supplier_type_user_id' => 81,
                    'business_id' => 1,
                    'company_id' => 1,
                    'branch_id' => 1,
                ]);
                $uuid = $uuid + $i ;
                $i = $i + 1;
            }
        }



        /* chart account create */

        $data = App\Models\TblPurcSupplierType::select('supplier_type_id')->get();

        foreach ($data as $item){
            $modalData = App\Models\TblPurcSupplierType::where('supplier_type_id',$item->supplier_type_id)->first();
            $level_no = 3;
            $parent_account_code = "3-01-00-0000";
            $business_id = 1;
            $company_id = 1;
            $branch_id = 1;
            $user_id = 81;
            $chart_name = $modalData->supplier_type_name;

            $pdo = Illuminate\Support\Facades\DB::getPdo();
            $account_id = 0;
            $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_PURC_CHART_INSERT(:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8); end;");
            $stmt->bindParam(':p1', $level_no);
            $stmt->bindParam(':p2', $parent_account_code);
            $stmt->bindParam(':p3', $business_id);
            $stmt->bindParam(':p4', $company_id);
            $stmt->bindParam(':p5', $branch_id);
            $stmt->bindParam(':p6', $user_id);
            $stmt->bindParam(':p7', $chart_name);
            $stmt->bindParam(':p8', $account_id,\PDO::PARAM_INT);
            $stmt->execute();

            $modalData->supplier_type_account_id = $account_id;
            $modalData->save();
        }
    }
}
