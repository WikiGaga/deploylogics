<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblSaleSalesContract;
use App\Models\TblSaleSalesContractDtl;
use App\Models\TblSaleCustomer;
use App\Models\TblSaleSales;
use App\Models\TblAccCoa;
use App\Models\TblSaleSalesContractExpense;
use App\Models\TblDefiPaymentType;
use App\Models\User;
use Illuminate\Http\Request;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SalesContractController extends Controller
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

    public static $page_title = 'Sales Contract';
    public static $redirect_url = 'sales-contract';
    public static $menu_dtl_id = '130';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSaleSalesContract::where('sales_contract_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleSalesContract::with('dtls','customer')->where('sales_contract_id',$id)->where(Utilities::currentBCB())->first();
                $data['document_code'] = $data['current']->sales_contract_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblSaleSalesContract::where(Utilities::currentBCB())->max('sales_contract_code'),'SC');
            $data['customer'] = TblSaleCustomer::select(['customer_id','customer_name'])->where('customer_default_customer',1)->where(Utilities::currentBC())->first();
        }
        $data['payment_terms'] = TblAccoPaymentTerm::where(Utilities::currentBC())->get();
        $data['currency'] = TblDefiCurrency::where(Utilities::currentBC())->where('currency_entry_status',1)->get();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)
            ->where(Utilities::currentBC())->get();
        $data['rate_types'] = config('constants.rate_type');
        $arr = [
            'biz_type' => 'business',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_sales_contract',
            'col_id' => 'sales_contract_id',
            'col_code' => 'sales_contract_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('sales.sales_contract.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'customer_id' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'payment_term_id' => 'nullable|numeric',
            'sales_contract_credit_days' => 'nullable|numeric',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(!in_array($request->rate_type,array_flip(config('constants.rate_type')))){
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if(!isset($request->pd) || count($request->pd) == 0){
            return $this->jsonErrorResponse($data, trans('message.fill_the_grid'), 200);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $sales_contract = TblSaleSalesContract::where('sales_contract_id',$id)->first();
            }else{
                $sales_contract = new TblSaleSalesContract();
                $sales_contract->sales_contract_id = Utilities::uuid();
                $sales_contract->sales_contract_code = $this->documentCode(TblSaleSalesContract::where(Utilities::currentBCB())->max('sales_contract_code'),'SC');
            }
            $form_id = $sales_contract->sales_contract_id;
            $sales_contract->sales_contract_date = date('Y-m-d', strtotime($request->sales_contract_date));
            $sales_contract->currency_id = $request->currency_id;
            $sales_contract->sales_contract_exchange_rate = $request->exchange_rate;
            $sales_contract->customer_id = $request->customer_id;
            $sales_contract->sales_contract_start_date = date('Y-m-d', strtotime($request->sales_contract_start_date));
            $sales_contract->sales_contract_end_date = date('Y-m-d', strtotime($request->sales_contract_end_date));
            $sales_contract->payment_term_id = $request->payment_term_id;
            $sales_contract->sales_contract_credit_days = $request->sales_contract_credit_days;
            $sales_contract->sales_contract_rate_type = $request->rate_type;
            $sales_contract->sales_contract_rate_perc = $request->rate_perc;
            $sales_contract->sales_contract_remarks = $request->sales_contract_remarks;
            $sales_contract->sales_contract_entry_status = 1;
            $sales_contract->business_id = auth()->user()->business_id;
            $sales_contract->company_id = auth()->user()->company_id;
            $sales_contract->branch_id = auth()->user()->branch_id;
            $sales_contract->sales_contract_user_id = auth()->user()->id;
            $sales_contract->save();

            $del_Dtls = TblSaleSalesContractDtl::where('sales_contract_id',$id)->where(Utilities::currentBCB())->get();
            foreach ($del_Dtls as $del_Dtl){
                TblSaleSalesContractDtl::where('sales_contract_dtl_id',$del_Dtl->sales_contract_dtl_id)->where(Utilities::currentBCB())->delete();
            }
            if(isset($request->pd)){
                foreach($request->pd as $k=>$pd){
                    $dtl = new TblSaleSalesContractDtl();
                    if(isset($id) && isset($pd['sales_contract_dtl_id']) && $pd['sales_contract_dtl_id'] != 'undefined' ){
                        $dtl->sales_contract_dtl_id = $pd['sales_contract_dtl_id'];
                        $dtl->sales_contract_id = $id;
                    }else{
                        $dtl->sales_contract_dtl_id = Utilities::uuid();
                        $dtl->sales_contract_id = $sales_contract->sales_contract_id;
                    }
                    $dtl->sales_contract_sr = $k;
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->sales_contract_dtl_barcode = $pd['pd_barcode'];
                    $dtl->sales_contract_dtl_packing = $this->addNo(isset($pd['pd_packing'])?$pd['pd_packing']:0);
                    $dtl->sales_contract_dtl_fc_rate = $this->addNo(isset($pd['fc_rate'])?$pd['fc_rate']:0);
                    $dtl->sales_contract_dtl_rate = $this->addNo(isset($pd['rate'])?$pd['rate']:0);
                    $dtl->sales_contract_dtl_vat_per = $this->addNo(isset($pd['vat_perc'])?$pd['vat_perc']:0);
                    $dtl->sales_contract_dtl_vat_amount = $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:0);
                    $dtl->sales_contract_dtl_net_rate = $this->addNo(isset($pd['net_rate'])?$pd['net_rate']:0);
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->sales_contract_dtl_user_id = auth()->user()->id;
                    $dtl->save();
                }
            }
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

    public function print($id)
    {
        $data['title'] = 'Sales Contract';
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblSaleSalesContract::where('sales_contract_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblSaleSalesContract::with('dtls','customer')->where('sales_contract_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['payment_terms'] = TblAccoPaymentTerm::where(Utilities::currentBC())->where('payment_term_id',$data['current']->payment_term_id)->where('payment_term_entry_status',1)->first();
        $data['currency'] = TblDefiCurrency::where(Utilities::currentBC())->where('currency_id',$data['current']->currency_id)->first();
        $data['payment_type'] = TblDefiPaymentType::where(Utilities::currentBC())->where('payment_type_entry_status',1)->where('payment_type_id',$data['current']->sales_contract_sales_type)->first();
        return view('prints.sale_contract_print',compact('data'));
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
                $saleContract = TblSaleSalesContract::where('sales_contract_id',$id)->where(Utilities::currentBCB())->first();
                $saleContract->dtls()->delete();
                $saleContract->delete();

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
