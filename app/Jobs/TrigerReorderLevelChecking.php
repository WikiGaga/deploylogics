<?php

namespace App\Jobs;

use App\Http\Controllers\Controller;
use Exception;
use App\Library\Utilities;
use Illuminate\Bus\Queueable;
use App\Models\TblSlowMovingStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\QueryException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TrigerReorderLevelChecking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $date_from;
    private $date_to;
    private $branch_ids;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date_from,$date_to,$branch_ids)
    {
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->branch_ids = $branch_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $date_from = $this->date_from;
        $date_to = $this->date_to;
        $branch_ids = $this->branch_ids;

        try {
            $count = 0;
            // Get All The Active Products
            $myProducts = DB::table('TBL_PURC_PRODUCT_BARCODE')
            ->join('TBL_PURC_PRODUCT', 'TBL_PURC_PRODUCT_BARCODE.PRODUCT_ID', '=', 'TBL_PURC_PRODUCT.PRODUCT_ID')
            ->join('TBL_PURC_PRODUCT_BARCODE_DTL', 'TBL_PURC_PRODUCT_BARCODE.PRODUCT_BARCODE_ID', '=', 'TBL_PURC_PRODUCT_BARCODE_DTL.PRODUCT_BARCODE_ID')
            ->where('TBL_PURC_PRODUCT_BARCODE.PRODUCT_BARCODE_PACKING', 1)
            ->where('TBL_PURC_PRODUCT_BARCODE_DTL.PRODUCT_BARCODE_STOCK_LIMIT_REORDER_QTY', '>' , 0)
            ->whereIn('TBL_PURC_PRODUCT_BARCODE_DTL.BRANCH_ID' , $branch_ids)
            ->select('TBL_PURC_PRODUCT_BARCODE_DTL.BUSINESS_ID','TBL_PURC_PRODUCT_BARCODE_DTL.COMPANY_ID','TBL_PURC_PRODUCT_BARCODE_DTL.BRANCH_ID','TBL_PURC_PRODUCT.PRODUCT_NAME','TBL_PURC_PRODUCT_BARCODE.PRODUCT_ID', 'TBL_PURC_PRODUCT_BARCODE_DTL.PRODUCT_BARCODE_STOCK_LIMIT_REORDER_QTY as REORDER_QTY')
            ->groupBy('TBL_PURC_PRODUCT_BARCODE_DTL.BUSINESS_ID','TBL_PURC_PRODUCT_BARCODE_DTL.COMPANY_ID','TBL_PURC_PRODUCT_BARCODE_DTL.BRANCH_ID','TBL_PURC_PRODUCT.PRODUCT_NAME','TBL_PURC_PRODUCT_BARCODE.PRODUCT_ID' , 'TBL_PURC_PRODUCT_BARCODE_DTL.PRODUCT_BARCODE_STOCK_LIMIT_REORDER_QTY')
            ->orderBy('TBL_PURC_PRODUCT_BARCODE_DTL.PRODUCT_BARCODE_STOCK_LIMIT_REORDER_QTY' , 'desc')
            ->chunk(200 , function($activeProducts) use ($date_from,$date_to,$branch_ids,&$count){
                foreach($activeProducts as $product){
                    if($count == 0){
                        // Delete The Existing Record
                        DB::beginTransaction();
                        TblSlowMovingStock::whereIn('branch_id' , $branch_ids)->whereBetween('reorder_date' , [$date_from,$date_to] )->delete();
                        DB::commit();
                        $count++;
                    }
                    // If Reorder Level is Not Set --Continue
                    if(!isset($product->reorder_qty)){ continue; }
                    
                    $from_date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($date_from) ) ));
                    $productActivity = "SELECT PROD.PRODUCT_NAME , PROD.PRODUCT_BARCODE_BARCODE, PROD.PRODUCT_BARCODE_ID, PROD.UOM_NAME , PROD.PRODUCT_BARCODE_PACKING ,STOCK.STOCK_EXPIRY, STOCK.DOCUMENT_DATE , STOCK.DOCUMENT_ID, STOCK.DOCUMENT_TYPE , STOCK.DOCUMENT_CODE , NVL (STOCK.QTY_IN, 0) + NVL (STOCK.BONUS_QTY_IN, 0) QTY_IN , CASE WHEN NVL (STOCK.PRODUCT_PACKING, 0) > 0 THEN NVL(STOCK.DOCUMENT_ACT_RATE, 0) / NVL (STOCK.PRODUCT_PACKING, 0) ELSE 0 END IN_RATE , STOCK.QTY_OUT + BONUS_QTY_OUT QTY_OUT, CASE WHEN NVL (STOCK.PRODUCT_PACKING, 0) > 0 THEN NVL(STOCK.DOCUMENT_RATE, 0) / NVL (STOCK.PRODUCT_PACKING, 0) ELSE 0 END OUT_RATE , CASE WHEN NVL (STOCK.PRODUCT_PACKING, 0) > 0 THEN NVL(STOCK.DOCUMENT_RATE, 0) / NVL (STOCK.PRODUCT_PACKING, 0) ELSE 0 END BAL_RATE , STOCK.BONUS_QTY_IN , STOCK.TRANSFER_FROM_BRANCH_ID, STOCK.TRANSFER_TO_BRANCH_ID FROM VW_PURC_STOCK_DTL STOCK , VW_PURC_PRODUCT_BARCODE_FIRST PROD WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID AND PROD.PRODUCT_ID = ".$product->product_id." AND (STOCK.DOCUMENT_DATE between to_date ('".$date_from."', 'yyyy/mm/dd') and to_date ('".$date_to."', 'yyyy/mm/dd')) AND STOCK.BUSINESS_ID = ".$product->business_id." AND STOCK.COMPANY_ID = ".$product->company_id." AND STOCK.BRANCH_ID = ". $product->branch_id ." ORDER BY STOCK.DOCUMENT_DATE , STOCK.SORTING_ID , COALESCE(TO_NUMBER(REGEXP_SUBSTR(STOCK.DOCUMENT_CODE, '^\d+')), 0), STOCK.DOCUMENT_CODE";
                    $listdata = \Illuminate\Support\Facades\DB::select($productActivity);
                    $store_qry = "SELECT SUM (NVL (QTY_BASE_UNIT_VALUE, 0)) qty
                                FROM VW_PURC_STOCK_DTL GRN
                                WHERE      GRN.PRODUCT_ID =  $product->product_id
                                AND GRN.BUSINESS_ID = ".$product->business_id."
                                AND GRN.COMPANY_ID = ".$product->company_id."
                                AND  GRN.branch_id = ".$product->branch_id."
                                AND GRN.DOCUMENT_DATE <= to_date ('".$from_date."', 'yyyy/mm/dd')";
                    $store_stock = DB::selectOne($store_qry);
                    $store_stock = ($store_stock->qty != null)?$store_stock->qty:0;
                    $checkFlag = TRUE;
                    $reorderCount = $totInQty = $totOutQty = $BalQty = $first = 0;

                    foreach($listdata as $row){
                        if(in_array(strtolower(strtoupper($row->document_type)),['sr'])){
                            $qty_in = abs($row->qty_in);
                        }else{
                            $qty_in = $row->qty_in;
                        }
                        $qty_out = $row->qty_out;
                        if ($first == 0){
                            $Qty = ((float)$store_stock + (float)$qty_in) - $qty_out;
                            
                            // Make Item Dead Product
                            DB::beginTransaction();
                            $deadReach = new TblSlowMovingStock();
                            $deadReach->slow_moving_item_id = Utilities::uuid();
                            $deadReach->product_id = $product->product_id;
                            $deadReach->product_barcode_id = $row->product_barcode_id;
                            $deadReach->product_barcode_stock_limit_reorder_qty = $product->reorder_qty;
                            $deadReach->reorder_count = 0;
                            $deadReach->stock_qty = $BalQty;
                            $deadReach->reorder_date = $row->document_date;
                            $deadReach->branch_id = $product->branch_id;
                            $deadReach->reorder_till_date = 0;
                            $deadReach->save();
                            DB::commit();

                            $first++;
                        }else{
                            $Qty = $qty_in - $qty_out;
                        }
                        $totInQty += $qty_in;
                        $totOutQty += $qty_out;
                        $BalQty += $Qty;
                        
                        if($BalQty > $product->reorder_qty){
                            $checkFlag = TRUE;
                        }
                        // Check Reorder Level If Flag is True
                        if($checkFlag == TRUE && $BalQty <= $product->reorder_qty){
                            $reorderCount++;
                            $checkFlag = FALSE;
                            // Insert Record Record
                            DB::beginTransaction();
                            $reorderReach = new TblSlowMovingStock();
                            $reorderReach->slow_moving_item_id = Utilities::uuid();
                            $reorderReach->product_id = $product->product_id;
                            $reorderReach->product_barcode_id = $row->product_barcode_id;
                            $reorderReach->product_barcode_stock_limit_reorder_qty = $product->reorder_qty;
                            $reorderReach->reorder_count = 1;
                            $reorderReach->stock_qty = $BalQty;
                            $reorderReach->reorder_date = $row->document_date;
                            $reorderReach->branch_id = $product->branch_id;
                            $reorderReach->reorder_till_date = $reorderCount;
                            $reorderReach->save();
                            DB::commit();
                        }
                    }
                }
            });
        }catch (QueryException $e) {
            DB::rollback();
            Log::debug('While Checking Reorder : ' . $e->getMessage() . ' at ' . date('Y-m-d' , time()));
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            Log::debug('While Checking Reorder : ' . $e->getMessage() . ' at ' . date('Y-m-d' , time()));
        } catch (ValidationException $e) {
            DB::rollback();
            Log::debug('While Checking Reorder : ' . $e->getMessage() . ' at ' . date('Y-m-d' , time()));
        } catch (Exception $e) {
            DB::rollback();
            Log::debug('While Checking Reorder : ' . $e->getMessage() . ' at ' . date('Y-m-d' , time()));
        }
        DB::commit();
    }

    public function failed($exception)
    {
        Log::channel('daily')->error("Error On Checking Reorder : " . $exception->getMessage() . ' at ' . date('Y-m-d' , time()));
    }
}
