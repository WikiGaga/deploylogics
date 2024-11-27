<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblSaleSalesOrder;
use App\Models\TblSaleSalesOrderDtl;
use App\Models\TblSaleCustomer;
use App\Models\TblSaleSales;
use App\Models\TblAccCoa;
use App\Models\TblSaleSalesOrderExpense;
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

class SalesOrderController extends Controller
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

    public static $page_title = 'Sales Order';
    public static $redirect_url = 'sales-order';
    public static $menu_dtl_id = '45';
    public static $type = 'so';
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
            if(TblSaleSalesOrder::where('sales_order_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleSalesOrder::with('dtls','expense','customer')->where(Utilities::currentBCB())->where('sales_order_id',$id)->first();
                $data['document_code'] = $data['current']->sales_order_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblSaleSalesOrder',
                'code_field'        => 'sales_order_code',
                'code_prefix'       => strtoupper(self::$type),
                'code_type_field'   => 'sales_order_code_type',
                'code_type'         => self::$type
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
            $data['customer'] = TblSaleCustomer::select(['customer_id','customer_name'])->where(Utilities::currentBC())->where('customer_default_customer',1)->first();
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',auth()->user()->id)->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where(Utilities::currentBC())->get();
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->where(Utilities::currentBC())->get();
        $data['accounts'] = TblAccCoa::where('chart_sale_expense_account',1)->where(Utilities::currentBC())->get();

        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)
            ->where(Utilities::currentBC())->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_sales_order',
            'col_id' => 'sales_order_id',
            'col_code' => 'sales_order_code',
            'code_type_field'   => 'sales_order_code_type',
            'code_type'         => self::$type,
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('sales.sales_order.form',compact('data'));
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
            'sales_order_credit_days' => 'nullable|numeric',
            'sales_order_sales_type' => 'required|numeric',
            'sales_order_sales_man' => 'required|numeric',
            'sales_order_remarks' => 'nullable|max:255',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(isset($request->pdsm)){
            foreach($request->pdsm as $expense){
                if(!empty($expense['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$expense['account_id'])->where('chart_code',$expense['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Account Code",201);
                }
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $sales_order = TblSaleSalesOrder::where('sales_order_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $sales_order = new TblSaleSalesOrder();
                $sales_order->sales_order_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblSaleSalesOrder',
                    'code_field'        => 'sales_order_code',
                    'code_prefix'       => strtoupper(self::$type),
                    'code_type_field'   => 'sales_order_code_type',
                    'code_type'         => self::$type
                ];
                $sales_order->sales_order_code = Utilities::documentCode($doc_data);
            }
            $form_id = $sales_order->sales_order_id;
            $sales_order->currency_id = $request->currency_id;
            $sales_order->sales_order_exchange_rate = $request->exchange_rate;
            $sales_order->sales_order_date = date('Y-m-d', strtotime($request->sales_order_date));;
            $sales_order->payment_term_id = $request->payment_term_id;
            $sales_order->sales_order_credit_days = $request->sales_order_credit_days;
            $sales_order->customer_id = $request->customer_id;
            $sales_order->sales_order_delivery_id = $request->sales_order_delivery_id;
            $sales_order->sales_order_sales_man = $request->sales_order_sales_man;
            $sales_order->sales_order_sales_type = $request->sales_order_sales_type;
            $sales_order->payment_mode_id = $request->payment_mode_id;
            $sales_order->sales_order_address = $request->sales_order_address;
            $sales_order->sales_order_remarks = $request->sales_order_remarks;
            $sales_order->sales_order_entry_status = 1;
            $sales_order->business_id = auth()->user()->business_id;
            $sales_order->company_id = auth()->user()->company_id;
            $sales_order->branch_id = auth()->user()->branch_id;
            $sales_order->sales_order_user_id = auth()->user()->id;
            $sales_order->sales_order_code_type = self::$type;
            $sales_order->save();

            $del_Dtls = TblSaleSalesOrderDtl::where('sales_order_id',$id)->where(Utilities::currentBCB())->get();
            foreach ($del_Dtls as $del_Dtl){
                TblSaleSalesOrderDtl::where('sales_order_dtl_id',$del_Dtl->sales_order_dtl_id)->where(Utilities::currentBCB())->delete();
            }
            if(isset($request->pd)){
                $sr_no = 1;
                foreach($request->pd as $pd){
                    $dtl = new TblSaleSalesOrderDtl();
                    if(isset($id) && isset($pd['sales_order_dtl_id']) && $pd['sales_order_dtl_id'] != 'undefined' ){
                        $dtl->sales_order_dtl_id = $pd['sales_order_dtl_id'];
                        $dtl->sales_order_id = $id;
                    }else{
                        $dtl->sales_order_dtl_id = Utilities::uuid();
                        $dtl->sales_order_id = $sales_order->sales_order_id;
                    }
                    $dtl->sr_no = $sr_no++;
                    $dtl->sales_order_dtl_barcode = $pd['pd_barcode'];
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->sales_order_dtl_packing = $this->addNo(isset($pd['pd_packing'])?$pd['pd_packing']:0);
                    $dtl->sales_order_dtl_quantity = $this->addNo(isset($pd['quantity'])?$pd['quantity']:0);
                    $dtl->sales_order_dtl_foc_qty = $this->addNo(isset($pd['foc_qty'])?$pd['foc_qty']:0);
                    $dtl->sales_order_dtl_fc_rate = $this->addNo(isset($pd['fc_rate'])?$pd['fc_rate']:0);
                    $dtl->sales_order_dtl_rate = $this->addNo(isset($pd['rate'])?$pd['rate']:0);
                    $dtl->sales_order_dtl_amount = $this->addNo(isset($pd['amount'])?$pd['amount']:0);
                    $dtl->sales_order_dtl_disc_per = $this->addNo(isset($pd['dis_perc'])?$pd['dis_perc']:0);
                    $dtl->sales_order_dtl_disc_amount = $this->addNo(isset($pd['dis_amount'])?$pd['dis_amount']:0);
                    $dtl->sales_order_dtl_vat_per = $this->addNo(isset($pd['vat_perc'])?$pd['vat_perc']:0);
                    $dtl->sales_order_dtl_vat_amount = $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:0);
                    $dtl->sales_order_dtl_total_amount = $this->addNo(isset($pd['gross_amount'])?$pd['gross_amount']:0);
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->sales_order_dtl_user_id = auth()->user()->id;
                    $dtl->save();
                }
            }

             if(isset($id)){
                 $del_AccDtls = TblSaleSalesOrderExpense::where('sales_order_id',$id)->where(Utilities::currentBCB())->get();
                 foreach ($del_AccDtls as $del_accDtl){
                    TblSaleSalesOrderExpense::where('sales_order_expense_id',$del_accDtl->sales_order_expense_id)->where(Utilities::currentBCB())->delete();
                 }
             }
            if(isset($request->pdsm)){
                foreach($request->pdsm as $expense){
                    $expenseDtl = new TblSaleSalesOrderExpense();
                    if(isset($id) && isset($account['sales_order_expense_id'])){
                        $expenseDtl->sales_order_id = $id;
                        $expenseDtl->sales_order_expense_id = $expense['sales_order_expense_id'];
                    }else{
                        $expenseDtl->sales_order_expense_id = Utilities::uuid();
                        $expenseDtl->sales_order_id = $sales_order->sales_order_id;
                    }
                    $expenseDtl->chart_account_id = $expense['account_id'];
                    $expenseDtl->sales_order_expense_account_code = $expense['account_code'];
                    $expenseDtl->sales_order_expense_account_name = $expense['account_name'];
                    $expenseDtl->sales_order_expense_amount = $this->addNo($expense['expense_amount']);
                    $expenseDtl->business_id = auth()->user()->business_id;
                    $expenseDtl->company_id = auth()->user()->company_id;
                    $expenseDtl->branch_id = auth()->user()->branch_id;
                    $expenseDtl->sales_order_expense_user_id = auth()->user()->id;
                    $expenseDtl->save();
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

            $sales = TblSaleSales::where('sales_order_booking_id',$id)->where(Utilities::currentBCB())->first();
            if($sales == null)
            {
                $saleOrder = TblSaleSalesOrder::where('sales_order_id',$id)->where(Utilities::currentBCB())->first();
                $saleOrder->dtls()->delete();
                $saleOrder->expense()->delete();
                $saleOrder->delete();
            }else{
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
