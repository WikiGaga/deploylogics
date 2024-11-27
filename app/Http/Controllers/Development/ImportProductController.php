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

class ImportProductController extends Controller
{
    public static $page_title = 'Import Product';
    public static $redirect_url = 'import-product';
    public static $menu_dtl_id = '307';

    
    public function importFile()
    {
        $data = [];
        $data['page_data'] = [];
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Import';
        $data['page_data']['type'] = 'Import';
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        return view('purchase.product.import',compact('data'));
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
            $barcodeSR_id = 551400;
            $barcode_id = 561400;
            $product_id = 561400;
      
            for ($row=1; $row < count($collection); $row++)
            {

                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'TblPurcProduct',
                    'code_field'        => 'product_code',
                    'code_prefix'       => strtoupper('p')
                ];
                $product_code = Utilities::documentCode($doc_data);


                $item = $collection[$row];
                
                $alreadyExists = TblPurcProductBarcode::where(DB::raw('product_barcode_barcode'),$item[2])->first();
                
                if(!isset($alreadyExists))
                {
                    $group_item_name = trim(strtolower(strtoupper($item[0])));
                    $arr = TblPurcGroupItem::where(DB::raw('lower(group_item_name)'),$group_item_name)->first();
                    $group_item_id = isset($arr->group_item_id)?$arr->group_item_id:"";

                
                    if(!empty($group_item_id) && $group_item_id != "NULL")
                    {
                        $product = new TblPurcProduct();
                        $product->product_id =  Utilities::uuid();//$product_id;
                        $product->product_code = $product_code;
                        $product->product_name = $item[1];
                        $product->group_item_id = $group_item_id;
                        $product->product_entry_status = 1;
                        $product->business_id = auth()->user()->business_id;
                        $product->company_id = auth()->user()->company_id;
                        $product->branch_id = auth()->user()->branch_id;
                        $product->product_user_id = auth()->user()->id;
                        $product->save();
                    
                        $c += 1;
                        if(!empty($item[0]) && $item[0] != "NULL")
                        {
                            $uom_name = trim(strtolower(strtoupper($item[3])));
                            $uom = TblDefiUom::where(DB::raw('lower(uom_name)'),$uom_name)->first();
                            $uom_id = isset($uom->uom_id)?$uom->uom_id:"";
                            
                            $n_barcode_id = TblPurcProductBarcode::where('business_id',auth()->user()->business_id)->max('product_barcode_id');
                            $product_barcode_id = $n_barcode_id+1;

                            $product_barcode_id_1 = $barcode_id++;
                            $barcode_1 = new TblPurcProductBarcode();
                            $barcode_1->product_id = $product->product_id;
                            $barcode_1->product_barcode_id = $product_barcode_id;
                            $barcode_1->product_barcode_barcode = $item[2];
                            $barcode_1->base_barcode = 1;
                            $barcode_1->uom_id = $uom_id;
                            $barcode_1->product_barcode_packing = (!empty($item[4]) &&  $item[4] != "NULL")?$item[4]:'';
                            $barcode_1->product_barcode_entry_status = 1;
                            $barcode_1->business_id = auth()->user()->business_id;
                            $barcode_1->product_barcode_user_id = auth()->user()->id;
                            $barcode_1->save();
                            $c += 3;
                        }


                        if(!empty($item[6]))
                        {
                            $n_purch_id = TblPurcProductBarcodePurchRate::where('business_id',auth()->user()->business_id)->max('product_barcode_purch_id');
                            $product_barcode_purch_id = $n_purch_id+1;

                            $branch_name = trim(strtolower(strtoupper($item[8])));
                            $arr_b = TblSoftBranch::where(DB::raw('lower(branch_name)'),$branch_name)->first();
                            $branch_id = $arr_b->branch_id;

                            TblPurcProductBarcodePurchRate::create([
                                'product_barcode_purch_id' => $product_barcode_purch_id,
                                'product_id' => $product->product_id,
                                'product_barcode_id' => $product_barcode_id,
                                'product_barcode_barcode' => $item[2],
                                'sale_rate' => isset($item[6])?$item[6]:"",
                                'product_barcode_cost_rate' => isset($item[7])?$item[7]:"",
                                'business_id' => auth()->user()->business_id,
                                'company_id' => auth()->user()->company_id,
                                'branch_id' => $branch_id,
                            ]);
                        }
                    }
                    
                    $dataNotFound = true;
                }else{
                    
                    $dataNotFound = false;
                }
                
                $barcodeSR_id = $barcodeSR_id++;
                $barcode_id = $barcode_id++;
            
                
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

