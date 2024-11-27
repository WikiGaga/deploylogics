<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\TblSaleBankDistribution;
use App\Models\TblSaleBankDistributionDtl;
use App\Models\TblDefiDenomination;
use App\Models\TblSoftBranch;
use App\Models\User;
use App\Models\TblAccoPaymentType;
use App\Models\TblAccCoa;
use Illuminate\Http\Request;
use App\Library\Utilities;
use Image;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Session;


class BankDistributionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public static $page_title = 'Bank Distribution Entry';
    public static $redirect_url = 'bank-distribution';
    public static $menu_dtl_id = '167';
    //public static $menu_dtl_id = '145';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSaleBankDistribution::where('bd_id','LIKE',$id)->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleBankDistribution::with('distribution_dtl')->where('bd_id',$id)->where(Utilities::currentBCB())->first();
                $data['document_code'] = $data['current']->bd_code;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblSaleBankDistribution::where(Utilities::currentBCB())->max('bd_code'),'BD');
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',auth()->user()->id)->get();
        $data['payment_person'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id','<>',auth()->user()->id)->get();
        $data['denomination'] = TblDefiDenomination::where('denomination_entry_status',1)->where(Utilities::currentBC())->orderBy('denomination_id')->get();
        $data['payment_type'] = TblAccoPaymentType::where(Utilities::currentBC())->get();
        $chart_bank_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->bank_group)->where(Utilities::currentBC())->first('chart_code');
        $bank_group = substr($chart_bank_group->chart_code,0,7);
        $business_id = auth()->user()->business_id;
        $company_id = auth()->user()->company_id;
        $accList = "select CHART_CODE, CHART_NAME, CHART_ACCOUNT_ID
                        from TBL_ACCO_CHART_ACCOUNT
                        where CHART_LEVEL = 4 and CHART_CODE like '$bank_group%'
                        and (BUSINESS_ID = $business_id and COMPANY_ID = $company_id)
                        UNION ALL
                        select acc.CHART_CODE,acc.CHART_NAME,b.branch_account_code as CHART_ACCOUNT_ID from TBL_SOFT_BRANCH b
                        join TBL_ACCO_CHART_ACCOUNT acc on acc.CHART_ACCOUNT_ID = b.branch_account_code
                        order by CHART_NAME asc";

        $data['acc_code'] = DB::select($accList);
       // dd($data['acc_code'] );
        $data['form_type'] = 'bank_distribution';
        $data['menu_id'] = self::$menu_dtl_id;
        $arr = [
            'biz_type' => 'business',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_bank_distribution',
            'col_id' => 'bd_id',
            'col_code' => 'bd_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        if(isset($id)){
            return view('sales.bank_distribution.edit',compact('data'));
        }else{
            return view('sales.bank_distribution.form',compact('data'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        //dd($request->distribution_dtl);
        $data = [];
        $validator = Validator::make($request->all(), [
            'day_shift' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $bd = TblSaleBankDistribution::where('bd_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $bd = new TblSaleBankDistribution();
                $bd->bd_id = Utilities::uuid();
                $bd->bd_code = $this->documentCode(TblSaleBankDistribution::where(Utilities::currentBCB())->max('bd_code'),'BD');
            }
            $form_id = $bd->bd_id;
            $bd->bd_date =  date('Y-m-d', strtotime($request->day_date));
            $bd->bd_shift = $request->day_shift;
            $bd->saleman_id = $request->saleman_id;
            $bd->bd_payment_handover_received = $request->payment_handover_received;
            $bd->bd_payment_way_type = $request->payment_way_type;
            $bd->bd_reference_no = $request->reference_no;
            $bd->bd_notes= $request->notes;
            $bd->business_id = auth()->user()->business_id;
            $bd->company_id = auth()->user()->company_id;
            $bd->branch_id = auth()->user()->branch_id;
            $bd->bd_user_id = auth()->user()->id;
            $bd->save();


            $del_Dtls = TblSaleBankDistributionDtl::where('bd_id',$id)->where(Utilities::currentBCB())->get();
            foreach ($del_Dtls as $del_Dtl){
                TblSaleBankDistributionDtl::where('bd_dtl_id',$del_Dtl->bd_dtl_id)->where(Utilities::currentBCB())->delete();
            }

            $sr = 1;
            $vsr = 1;
            $lopCount = TblDefiDenomination::where('denomination_entry_status',1)->where(Utilities::currentBC())->count();
            //voucher setting
            $table_name = 'tbl_acco_voucher';
            if(isset($id)){
                $action = 'update';
                $bd_id = $id;
                $bank_dis = TblSaleBankDistribution::where('bd_id',$bd_id)->where(Utilities::currentBCB())->first();
                $voucher_id = (int)$bank_dis->voucher_id;
                if(empty($voucher_id)){
                    $action = 'add';
                    $voucher_id = Utilities::uuid();
                }
            }else{
                $action = 'add';
                $bd_id = $bd->bd_id;
                $voucher_id = Utilities::uuid();
            }
            if(isset($request->distribution_dtl)){
                foreach ($request->distribution_dtl as $dtl){
                    $total_amount = 0;
                    for ($i=0;$lopCount>$i; $i++ ){
                        $bd_dtl = new TblSaleBankDistributionDtl();
                        $bd_dtl->bd_dtl_id = Utilities::uuid();
                        $bd_dtl->bd_id = $bd->bd_id;
                        $bd_dtl->sr_no = $sr;
                        $bd_dtl->bank_id = isset($dtl['bank_id'])?$dtl['bank_id']:'';
                        $bd_dtl->denomination_id = $dtl["denomination_id_$i"];
                        $bd_dtl->bd_dtl_qty = $this->addNo($dtl["day_qty_$i"]);
                        $bd_dtl->bd_dtl_amount = $this->addNo($dtl["day_value_$i"]);
                        $bd_dtl->business_id = auth()->user()->business_id;
                        $bd_dtl->company_id = auth()->user()->company_id;
                        $bd_dtl->branch_id = auth()->user()->branch_id;
                        $bd_dtl->bd_dtl_user_id = auth()->user()->id;
                        $bd_dtl->save();
                        $total_amount += $this->addNo($dtl["day_value_$i"]);
                    }
                    $sr++;

                    // insert update bank distribution voucher
                    $where_clause = '';
                    $bank_id = $bd_dtl->bank_id;
                    //check account code
                    $ChartArr = [
                        $bank_id,
                        Session::get('dataSession')->bank_distribution_cr_ac,
                    ];
                    $response = $this->ValidateCharCode($ChartArr);
                    if($response == false){
                        return $this->returnjsonerror("voucher Account Code not correct",404);
                    }

                    //voucher start
                    $data = [
                        'voucher_id'            =>  $voucher_id,
                        'voucher_document_id'   =>  $bd_id,
                        'voucher_no'            =>  $bd->bd_code,
                        'voucher_date'          =>  date('Y-m-d', strtotime($request->day_date)),
                        'voucher_descrip'       =>  'Bank Distribution: '.$bd->bd_notes,
                        'voucher_type'          =>  'BD',
                        'branch_id'             =>  auth()->user()->branch_id,
                        'business_id'           =>  auth()->user()->business_id,
                        'company_id'            =>  auth()->user()->company_id,
                        'voucher_user_id'       =>  auth()->user()->id
                    ];

                    $data['chart_account_id'] = $bank_id;
                    $data['voucher_debit'] = abs($total_amount);
                    $data['voucher_credit'] = 0;
                    $data['voucher_sr_no'] = $vsr++;
                    // for debit entry $total_amount
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                    $action = 'add';
                    $data['chart_account_id'] = Session::get('dataSession')->bank_distribution_cr_ac;
                    $data['voucher_debit'] = 0;
                    $data['voucher_credit'] = abs($total_amount);
                    $data['voucher_sr_no'] = $vsr++;
                    // for credit entry total_amount
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                    // end insert update bank distribution voucher


                }
            }

            if(!isset($id)){
                $bank_dis = TblSaleBankDistribution::where('bd_id',$bd_id)->where(Utilities::currentBCB())->first();
                $bank_dis->voucher_id = $voucher_id;
                $bank_dis->save();
            }

        } catch (QueryException $e) {
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
            return $this->jsonErrorResponse($data, $e->getLine(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
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

    public function print($type,$id)
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
        $data = [];
        DB::beginTransaction();
        try{
            $bd = TblSaleBankDistribution::where('bd_id',$id)->where(Utilities::currentBCB())->first();
            $voucher_id = $bd->voucher_id;
            if(!empty($voucher_id)){
                $this->proAccoVoucherDelete($voucher_id);
            }
            $bd->distribution_dtl()->delete();
            $bd->delete();
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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
