<?php

namespace App\Http\Controllers\Development;


use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSoftBranch;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcProductBarcodeDtl;
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

class ImportAlternateBarcodeController extends Controller
{
    public static $page_title = 'Import Alternate Barcode';
    public static $redirect_url = 'import-alternate-barcode';
    public static $menu_dtl_id = '308';

    
    public function importFile()
    {
        $data = [];
        $data['page_data'] = [];
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Import';
        $data['page_data']['type'] = 'Import';
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        return view('purchase.product_smart.import',compact('data'));
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


            for ($row=1; $row < count($collection); $row++)
            {
                $item = $collection[$row];

                $parent_barcode = $item[0];
                $alternate_barcode = strval($item[1]);
                $packing = $item[2];
                $dataNotFound = false;
                if(!empty($parent_barcode) && !empty($alternate_barcode) && !empty($packing))
                {
                    $alreadyExists = TblPurcProductBarcode::where(DB::raw('product_barcode_barcode'),$alternate_barcode)->first();
                
                    if(!isset($alreadyExists))
                    {
                        $barcode_uom = TblPurcProductBarcode::where(DB::raw('product_barcode_barcode'),$parent_barcode)->first();
                        $uom_id = isset($barcode_uom->uom_id)?$barcode_uom->uom_id:"";
                        $product_id = isset($barcode_uom->product_id)?$barcode_uom->product_id:"";
        
                        $max = TblPurcProductBarcode::where('business_id',auth()->user()->business_id)->max('product_barcode_sr_no');
                        $n_barcode_id = TblPurcProductBarcode::where('business_id',auth()->user()->business_id)->max('product_barcode_id');
                        
                        $product_barcode_id = $n_barcode_id+1;
                        
                        
                        $data['product_barcode_id'] = $product_barcode_id;

                        $barcode = new TblPurcProductBarcode();
                        $barcode->product_barcode_id = $product_barcode_id;
                        $v_product_barcode = trim($alternate_barcode);
                        $barcode->product_id = $product_id;
                        $barcode->product_barcode_barcode = $v_product_barcode;
                        $barcode->product_barcode_entry_status = 1;
                        $barcode->product_barcode_sr_no = $max + 1;
                        $barcode->uom_id = $uom_id;
                        $barcode->product_barcode_packing = $packing;
                        $barcode->product_barcode_user_id = auth()->user()->id;
                        $barcode->business_id = auth()->user()->business_id;
                        $barcode->base_barcode = 0;
                        $barcode->save();
                        

                        $c += 1;

                        $n_purch_id = TblPurcProductBarcodePurchRate::where('business_id',auth()->user()->business_id)->max('product_barcode_purch_id');
                        $product_barcode_purch_id = $n_purch_id+1;

                        $branch_name = trim(strtolower(strtoupper($item[5])));
                        $arr_b = TblSoftBranch::where(DB::raw('lower(branch_name)'),$branch_name)->first();
                        $branch_id = $arr_b->branch_id;

                        $purcRates = TblPurcProductBarcodePurchRate::where('product_barcode_id',$product_barcode_id)
                        ->where('branch_id',$branch_id)
                        ->get();

                        TblPurcProductBarcodePurchRate::create([
                            'product_barcode_purch_id' => $product_barcode_purch_id,
                            'product_id' => $product_id,
                            'product_barcode_id' => $product_barcode_id,
                            'product_barcode_barcode' => $alternate_barcode,
                            'sale_rate' => isset($purcRates->sale_rate)?$purcRates->sale_rate:$item[3],
                            'net_tp' => isset($purcRates->net_tp)?$purcRates->net_tp:$item[4],
                            'business_id' => auth()->user()->business_id,
                            'company_id' => auth()->user()->company_id,
                            'branch_id' => $branch_id,
                        ]);
                        $dataNotFound = true;
                    }
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
            $message = 'Alternate Barcode Successfully Imported <br/>' ;
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

