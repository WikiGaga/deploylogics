<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSoftBranch;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class PostingCashVoucherController extends Controller
{
    public static $page_title = 'Cash Voucher Posting';
    public static $redirect_url = 'cash-voucher-posting';
    public static $menu_dtl_id = '169';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id=null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = 'Cash Voucher Posting';
        $data['permission'] = self::$menu_dtl_id.'-view';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Insert Voucher';
        $data['branches'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();

        return view('accounts.posting_cash_voucher.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id=null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'pos_branch_ids' => 'required',
            'date_from' => 'required|date|date_format:d-m-Y|before_or_equal:date_to',
            'date_to' => 'required|date|date_format:d-m-Y|after_or_equal:date_from',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $f_date = $request->date_from;
            $t_date = $request->date_to;
            $from_date = date('Y-m-d', strtotime($f_date));
            $to_date = date('Y-m-d', strtotime($t_date));
            $branches = $request->pos_branch_ids;
            $business_id = Auth::user()->business_id;
            $company_id = Auth::user()->company_id;
            $BC = "business_id = $business_id AND company_id = $company_id";

            $begin = new \DateTime($from_date);
            $end = new \DateTime($to_date);
            $end = $end->modify( '+1 day' );

            $interval = new \DateInterval('P1D');
            $daterange = new \DatePeriod($begin, $interval ,$end);

            $del_data_qry = "Delete from TBL_ACCO_VOUCHER where $BC AND voucher_type = 'CAS' and ( voucher_date between to_date('".$from_date."','yyyy/mm/dd') and to_date('".$to_date."','yyyy/mm/dd'))";
            DB::delete($del_data_qry);

            foreach($daterange as $now_date){
                $date = $now_date->format("Y-m-d");
                $query = "select distinct sales_sales_man ,sales_sales_man_name from vw_sale_sales_invoice
                            where (SALES_DATE between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')) and branch_id in( ".implode(",",$branches).")";
                $list = \Illuminate\Support\Facades\DB::select($query);
                foreach($list as $saleData){
                    $CaSaleQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                        select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                        where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SI' OR sales_type = 'POS') and sales_sales_type = '1' and
                                                        (SALES_DATE between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')) and branch_id in( ".implode(",",$branches).")
                                                    ) abc";
                    $CaSale = \Illuminate\Support\Facades\DB::select($CaSaleQuery);
                    $CaSale = isset($CaSale[0]->sales_net_amount)?$CaSale[0]->sales_net_amount:0;


                    $CaSaleRQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                    select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                    where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SR' OR sales_type = 'RPOS') and sales_sales_type = '1' and
                                                    (SALES_DATE between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')) and branch_id in( ".implode(",",$branches).")
                                                ) abc";
                    $CaSaleR = \Illuminate\Support\Facades\DB::select($CaSaleRQuery);
                    $CaSaleR = isset($CaSaleR[0]->sales_net_amount)?$CaSaleR[0]->sales_net_amount:0;

                    $t_Ca_Sale = $CaSale - $CaSaleR;

                    $ColctionQuery = "select sum(day_amount) amount from vw_sale_day where day_payment_handover_received = '".$saleData->sales_sales_man."' and day_case_type = 'payment-received'
                                                    and (day_date between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')) and branch_id in( ".implode(",",$branches).")";
                    $Colction = \Illuminate\Support\Facades\DB::select($ColctionQuery);
                    $ColctionAmt = isset($Colction[0]->amount)?$Colction[0]->amount:0;

                    $diffAmt = $ColctionAmt - $t_Ca_Sale;

                    $general_cash_ac = Session::get('dataSession')->general_cash_ac;
                    $excess_cash_ac = Session::get('dataSession')->excess_cash_ac;
                    $ChartArr = [
                        $general_cash_ac,
                        $excess_cash_ac,
                    ];
                    $response = $this->ValidateCharCode($ChartArr);
                    if($response == false){
                        return $this->returnjsonerror("voucher Account Code not correct",404);
                    }
                    $action = 'add';
                    $table_name = 'tbl_acco_voucher';
                    $voucher_id = Utilities::uuid();
                    $where_clause = '';
                    //voucher start
                    $doc_data = [
                        'biz_type'          => 'branch',
                        'model'             => 'TblAccoVoucher',
                        'code_field'        => 'voucher_no',
                        'code_prefix'       => strtoupper('cas')
                    ];
                    $customer_code = Utilities::documentCode($doc_data);

                    $data = [
                        'voucher_id'            =>  $voucher_id,
                        'voucher_document_id'   =>  $voucher_id,
                        'voucher_no'            =>  $customer_code,
                        'voucher_date'          =>  date('Y-m-d', strtotime($date)),
                        'voucher_descrip'       =>  'Excess/Shortage Cash : '.$saleData->sales_sales_man_name,
                        'voucher_type'          =>  "CAS",
                        'branch_id'             =>  auth()->user()->branch_id,
                        'business_id'           =>  auth()->user()->business_id,
                        'company_id'            =>  auth()->user()->company_id,
                        'voucher_user_id'       =>  auth()->user()->id
                    ];
                    $data['chart_account_id'] = $general_cash_ac;
                    if($diffAmt > 0){
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs(number_format((float)$diffAmt,3));
                    }else{
                        $data['voucher_debit'] = abs(number_format((float)$diffAmt,3));
                        $data['voucher_credit'] = 0;
                    }
                    $data['voucher_sr_no'] = 1;
                    // for debit entry total_amount
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                    $action = 'add';
                    $data['chart_account_id'] = $excess_cash_ac;
                    if($diffAmt > 0){
                        $data['voucher_debit'] = abs(number_format((float)$diffAmt,3));
                        $data['voucher_credit'] = 0;
                    }else{
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs(number_format((float)$diffAmt,3));
                    }
                    $data['voucher_sr_no'] = 2;
                    // for credit entry total_amount
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                }

            }
         //   dd($request->toArray());

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
        $data = array_merge($data, Utilities::returnJsonNewForm());
        return $this->jsonSuccessResponse($data, 'Voucher Posted Successfully', 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
