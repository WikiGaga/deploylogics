<?php

namespace App\Http\Controllers\Development;


use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblPurcGroupItem;
use App\Models\TblDefiUom;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblSoftBranch;
use App\Models\TblPurcProductBarcodePurchRate;
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
use Mpdf\Tag\Em;

class ImportRateController extends Controller
{
    public static $page_title = 'Import Sale/Cost Rate';
    public static $redirect_url = 'import-rate';
    public static $menu_dtl_id = '307';

    
    public function importFile()
    {
        $data = [];
        $data['page_data'] = [];
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Import';
        $data['page_data']['type'] = 'Import';
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        return view('inventory.stock_item.import',compact('data'));
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
           
            $c = 0;
            
            $uuid = (string)date("mdyis");
            $uuid .= 10001;
            $_id = (int)$uuid;
      
            for ($row=1; $row < count($collection); $row++)
            {
                $item = $collection[$row];

                $barcode_barcode = (string)trim($item[0]);
                $cost_rate = $item[1];
                $sale_rate = $item[2];

                $branch_name = trim(strtolower(strtoupper($item[3])));
                $arr = TblSoftBranch::where(DB::raw('lower(branch_name)'),$branch_name)->first();
                $branch_id = isset($arr->branch_id)?$arr->branch_id:"";

                if(!empty($barcode_barcode))
                {
                    $barcode = TblPurcProductBarcodePurchRate::where('product_barcode_barcode',$barcode_barcode)->first();
                    $product_id = isset($barcode->product_id)?$barcode->product_id:"";
                    $barcode_id = isset($barcode->product_barcode_id)?$barcode->product_barcode_id:"";
                    
                    if(!empty($barcode_id) && !empty($product_id))
                    {
                        $rate = TblPurcProductBarcodePurchRate::where('product_id',$product_id)
                        ->where('product_barcode_id',$barcode_id)
                        ->where('branch_id',$branch_id)
                        ->first();

                        if(!empty($rate)){
                            if($cost_rate > 0){
                                $rate->product_barcode_purchase_rate = $cost_rate;
                            }
                            if($cost_rate > 0){
                                $rate->product_barcode_cost_rate = $cost_rate;
                            }
                            if($cost_rate > 0){
                                $rate->net_tp = $cost_rate;
                            }
                            if($sale_rate > 0){
                                $rate->sale_rate = $sale_rate;
                            }

                            $rate->save();
                        }else{
                            TblPurcProductBarcodePurchRate::create([
                                'product_barcode_purch_id' => $_id++,
                                'product_id' => $product_id,
                                'product_barcode_id' => $barcode_id,
                                'branch_id' => $branch_id,
                                'product_barcode_purchase_rate' => $cost_rate,
                                'product_barcode_cost_rate' => $cost_rate,
                                'sale_rate' => $sale_rate,
                                'net_tp' => $cost_rate,
                                'product_barcode_avg_rate' => 0,
                                'product_barcode_barcode' => $barcode_barcode,
                                'company_id' => auth()->user()->company_id,
                                'business_id' => auth()->user()->business_id,
                            ]);
                        }
                        $dataNotFound = true;
                    }
                }else{
                    $dataNotFound = false;
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
        if($dataNotFound)
        {
            $data = array_merge($data, Utilities::returnJsonImportForm());
            $message = 'Products Successfully Imported <br/>' ;
            $message .= 'Total Time Consume: '.number_format($timeEnd,3).' sec  <br/>';
            $data['loop'] = 'Loop Run '.$c;
            return $this->jsonSuccessResponse($data, $message, 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonImportForm());
            $message = 'Sheet Not upload <br/>' ;
            $message .= 'Total Time Consume: '.number_format($timeEnd,3).' sec  <br/>';
            $data['loop'] = 'Loop Run '.$c;
            return $this->jsonSuccessResponse($data, $message, 200);
        }
    }
}

