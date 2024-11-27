<?php

namespace App\Http\Controllers\Development;

date_default_timezone_set("Asia/Karachi");
use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblInveStock;
use App\Models\TblInveStockDtl;
use App\Models\TblDefiStore;
use App\Models\TblSoftBranch;
use App\Models\TblPurcProductBarcode;
use Illuminate\Http\Request;

// db and Validator
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Facades\Excel;

use Importer;

class ImportExcelDataController extends Controller
{
    public static $page_title = 'Import Stock';
    public static $redirect_url = 'import-stock';
    public static $menu_dtl_id = '304';

    
    public function importFile()
    {
        $data = [];
        $data['page_data'] = [];
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Import';
        $data['page_data']['type'] = 'Import';
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        return view('development.import_data.formstock',compact('data'));
    }

    public function importExcle2(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        DB::beginTransaction();
        try {
            
            $startTime = microtime(true);
            $file = $request->file('file');
            $path = public_path('/upload/');
            $filename = $file->getClientOriginalName();
            $fileExtension = time() . '-' . $file->getClientOriginalExtension();
            $file->move($path, $filename);

            $filePath = $path . $filename; // Full local path to the uploaded file

            



            if (!file_exists($filePath)) {
                // Handle the case where the file does not exist
                // You can log an error or return an error response to the user
                // For example:
                die("File not found.");
            }

            $excel = Importer::make('Excel');
             
            $excel->load($filePath);
            $collection = $excel->getCollection();
           
            /*array:32 [
                  0 => "date"
                  1 => "store"
                  2 => "barcode"
                  3 => "qty"
                  4 => "rate"
                  5 => "amount"
                  6 => "STOCK_CODE"
                  7 => "STOCK_CODE_TYPE"
                  8 => "STOCK_MENU_ID"
             ]*/

            $c = 0;
            $key =1;
            $formType='os';

            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblInveStock',
                'code_field'        => 'stock_code',
                'code_prefix'       => strtoupper($formType),
                'code_type_field'   => 'stock_code_type',
                'code_type'         => $formType

            ];
            $stock_code = Utilities::documentCode($doc_data);

            $stock_id = TblInveStock::where(DB::raw('company_id'),auth()->user()->company_id)->max('stock_id');
            $stock_dtl_id = $stock_id+1;


            $store_name = trim(strtolower(strtoupper($collection[1][1])));
            $arr = TblDefiStore::where(DB::raw('lower(store_name)'),$store_name)->first();
            $store_id = $arr->store_id;

            $branch_name = trim(strtolower(strtoupper($collection[1][6])));
            $arr_b = TblSoftBranch::where(DB::raw('lower(branch_name)'),$branch_name)->first();
            $branch_id = $arr_b->branch_id;
            
            $stock = new TblInveStock();
            $stock->stock_id = $stock_dtl_id;
            $stock->stock_code = $stock_code;
            $stock->stock_code_type = 'os';
            $stock->stock_menu_id =  '54';
            $stock->stock_rate_by =  "cost_rate";
            $stock->stock_total_qty =  0;
            $stock->stock_total_amount =  0;
            $stock->stock_store_from_id =  isset($store_id)?$store_id:"";
            $stock->stock_date = $collection[1][0];
            $stock->stock_entry_status = 1;
            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->branch_id = isset($branch_id)?$branch_id:"";//auth()->user()->branch_id;
            $stock->stock_user_id = auth()->user()->id;
            $stock->save();


            for ($row=1; $row < count($collection); $row++)
            {
                $item = $collection[$row];

                $barcode_uom = TblPurcProductBarcode::where(DB::raw('product_barcode_barcode'),$item[2])->first();
                $uom_id = isset($barcode_uom->uom_id)?$barcode_uom->uom_id:"";
                $packing_id = isset($barcode_uom->packing_id)?$barcode_uom->packing_id:"";
                $product_barcode_id = isset($barcode_uom->product_barcode_id)?$barcode_uom->product_barcode_id:"";
                $product_id = isset($barcode_uom->product_id)?$barcode_uom->product_id:"";

                if(!empty($product_id) && !empty($product_barcode_id))
                {
                    $dtl_id = TblInveStockDtl::where('business_id',auth()->user()->business_id)->max('stock_dtl_id');
                    $stock_dtl_id_id = $dtl_id+1;

                    $dtl = new TblInveStockDtl();
                    $dtl->stock_id = $stock->stock_id;
                    $dtl->stock_dtl_id = $stock_dtl_id_id;
                    $dtl->product_id =  $product_id;
                    $dtl->stock_dtl_sr_no =  $key++;
                    $dtl->product_barcode_id = $product_barcode_id;
                    $dtl->product_barcode_barcode = $item[2];
                    $dtl->stock_dtl_qty_base_unit =  $this->addNo($item[3]);
                    $dtl->stock_dtl_quantity =  $this->addNo($item[3]);
                    $dtl->stock_dtl_rate =  isset($item[4])?$this->addNo($item[4]):"";
                    $dtl->stock_dtl_amount =  isset($item[5])?$this->addNo($item[5]):"";
                    $dtl->uom_id = $uom_id;
                    $dtl->stock_dtl_packing = $packing_id;
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = isset($branch_id)?$branch_id:"";//auth()->user()->branch_id;
                    $dtl->dtl_user_id = auth()->user()->id;
                    $dtl->save();
                    $c += 3;
                }
            }

            $timeEnd = (microtime(true) - $startTime);
            
        }catch (QueryException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        $data = array_merge($data, Utilities::returnJsonImportForm());
        $message = 'Stock Successfully Imported <br/>' ;
        $message .= 'Total Time Consume: '.number_format($timeEnd,3).' sec  <br/>';
        $data['loop'] = 'Loop Run '.$c;
        return $this->jsonSuccessResponse($data, $message, 200);
    }
}

