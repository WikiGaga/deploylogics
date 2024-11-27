<?php

namespace App\Http\Controllers\Development;

date_default_timezone_set("Asia/Karachi");
use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblDefiCity;
use App\Models\TblDefiStore;
use App\Models\TblSoftBranch;
use App\Models\TblSaleCustomer;
use App\Models\TblSaleCustomerDtl;
use App\Models\TblDefiCountry;
use App\Models\TblSaleCustomerType;
use App\Models\TblDefiArea;
use App\Models\TblDefiMembershipType;
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

class ImportCustomerController extends Controller
{
    public static $page_title = 'Import Customer';
    public static $redirect_url = 'import-customer';
    public static $menu_dtl_id = '306';

    
    public function importFile()
    {
        $data = [];
        $data['page_data'] = [];
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Import';
        $data['page_data']['type'] = 'Import';
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        return view('development.import_data.formcustomer',compact('data'));
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
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblSaleCustomer',
                    'code_field'        => 'customer_code',
                    'code_prefix'       => strtoupper('cu')
                ];

                $customer_code = Utilities::documentCode($doc_data);
                $dataNotFound = false;

                $item = $collection[$row];


                if(!empty($item[1]))
                {
                    $alreadyExists = TblSaleCustomer::where(DB::raw('card_number'),trim($item[3]))->first();
                    
                    if(empty($alreadyExists))
                    {
                        $MembershipType = trim(strtolower(strtoupper($item[2])));
                        $arr = TblDefiMembershipType::where(DB::raw('lower(membership_type_name)'),$MembershipType)->first();
                        $membership_type_id = $arr->membership_type_id;

                        $status = trim(strtolower(strtoupper($item[6])));
                        if($status == "active"){
                            $customer_entry_status = 1;
                        }else{
                            $customer_entry_status = 0;
                        }

                        $customer_type = trim(strtolower(strtoupper($item[7])));
                        $arr_b = TblSaleCustomerType::where(DB::raw('lower(customer_type_name)'),$customer_type)->first();
                        
                        if(isset($arr_b)){
                            $customer_type_id = $arr_b->customer_type_id;
                        }else{
                            $customer_type_id ="";
                        }

                        $branch_name = trim(strtolower(strtoupper($item[9])));
                        $arr_c = TblSoftBranch::where(DB::raw('lower(branch_name)'),$branch_name)->first();
                        $branch_id = $arr_c->branch_id;
                        
                        $m_status = trim(strtolower(strtoupper($item[10])));
                        if($m_status == "active"){
                            $member_status = 1;
                        }else{
                            $member_status = 0;
                        }

                        $cust_id = TblSaleCustomer::where('business_id',auth()->user()->business_id)->max('customer_id');
                        $customer_id = $cust_id+1;

                        $customer = new TblSaleCustomer();
                        $customer->customer_id = $customer_id;
                        $customer->customer_code = $customer_code;
                        $customer->customer_name = trim($item[0]);
                        $customer->customer_phone_1 = $item[1];
                        $customer->customer_type = isset($customer_type_id)?$customer_type_id:"";;
                        $customer->customer_entry_status = $customer_entry_status;
                        $customer->membership_type_id = isset($membership_type_id)?$membership_type_id:"";
                        $customer->card_number = isset($item[3])?$item[3]:"";
                        
                        if($item[4] != ""){
                            $customer->issue_date = $item[4];
                        }
                        if($item[5] != ""){
                            $customer->expiry_date = $item[5];
                        }
                        $customer->customer_email = isset($item[8])?$item[8]:"";
                        $customer->loyalty_opnening  = isset($item[11])?$item[11]:"";  
                        $customer->member_status = $member_status;
                        $customer->business_id = auth()->user()->business_id;
                        $customer->company_id = auth()->user()->company_id;
                        $customer->branch_id = $branch_id;
                        $customer->customer_user_id = auth()->user()->id;
                        $customer->save();

                        $c += 1;
                        
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
            $message = 'Customer Successfully Imported <br/>' ;
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

