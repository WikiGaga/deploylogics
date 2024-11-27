<?php

namespace App\Jobs;

use Exception;
use App\Library\Utilities;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TrigerAutoDemandProcedure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $pdo = DB::getPdo();
            $ad_id = $this->id;
            $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_AUTO_DEMAND(:p1); end;");
            $stmt->bindParam(':p1', $ad_id);
            $stmt->execute();

            // $pdo = DB::getPdo();
            // $product_id = 122;
            // $supplier_id = 77250242404024 ;
            // $grn_supplier_barcode = "";
            // $business_id = 1;
            // $company_id = 1;
            // $branch_id = 1;
            // $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_PURC_SUP_BATCH_INSERT(:p1, :p2, :p3, :p4, :p5, :p6); end;");
            // $stmt->bindParam(':p1', $product_id);
            // $stmt->bindParam(':p2', $supplier_id);
            // $stmt->bindParam(':p3', $grn_supplier_barcode);
            // $stmt->bindParam(':p4', $business_id);
            // $stmt->bindParam(':p5', $company_id);
            // $stmt->bindParam(':p6', $branch_id);
            // $stmt->execute();


        } catch (Exception $exception) {
            Log::critical('Unkown error when trying to Run Job', [
            'error' => $exception->getMessage()
        ]);
            $this->fail($exception);
        }
    }

    public function failed($exception)
    {
        // throw $exception->getMessage();
        // Log::error($exception->getMessage());
    }
}
