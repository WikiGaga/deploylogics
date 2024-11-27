<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcLpo;
use App\Models\TblPurcQuotation;
use App\Models\TblPurcComparativeQuotation;
use App\Models\TblPurcComparativeQuotationDtl;
use App\Models\User;
use App\Models\TblPurcComparativeQuotationAccount;
use App\Models\TblPurcSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ComparativeQuotationController extends Controller
{
    public static $page_title = 'Comparative Quotation';
    public static $redirect_url = 'comparative-quotation';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /***
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
            if(TblPurcComparativeQuotation::where('comparative_quotation_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] =  TblPurcComparativeQuotation::with("dtls","accounts","quotation")->where('comparative_quotation_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblPurcComparativeQuotation::max('comparative_quotation_code'),'CQ');
        }

        $data['currency'] = TblDefiCurrency::get();
        $data['payment_terms'] = TblAccoPaymentTerm::get();
        return view('purchase.comparative_quotation.form',compact('data'));
    }

    public function quotationData($id){
        $data['display'] = TblPurcQuotation::with('dtls')->where('quotation_id',$id)->first();
        $data['supplier'] = TblPurcSupplier::where('supplier_id',$data['display']->quotation_supplier_id)->first();
        return response()->json($data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        //dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'exchange_rate' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $quotation =TblPurcComparativeQuotation::where('comparative_quotation_id',$id)->first();
            }else{
                $quotation = new TblPurcComparativeQuotation();
                $quotation->comparative_quotation_id = Utilities::uuid();
                $quotation->comparative_quotation_code = $this->documentCode(TblPurcComparativeQuotation::max('comparative_quotation_code'),'CQ');
            }
            $quotation->comparative_quotation_entry_date = date('Y-m-d', strtotime($request->quot_date));;
            $quotation->comparative_quotation_payment_mode_id = $request->payment_terms;
            $quotation->comparative_quotation_credit_days = $request->payment_mode;
            $quotation->quotation_id = $request->quotation_id;
            $quotation->comparative_quotation_currency_id = $request->quotation_currency;
            $quotation->comparative_quotation_exchange_rate = $request->exchange_rate;
            $quotation->comparative_quotation_remarks = $request->quotation_notes;
            $quotation->comparative_quotation_terms = $request->quotation_terms;
            $quotation->comparative_quotation_entry_status = "1";
            $quotation->business_id = auth()->user()->business_id;
            $quotation->company_id = auth()->user()->company_id;
            $quotation->branch_id = auth()->user()->branch_id;
            $quotation->comparative_quotation_user_id = auth()->user()->id;
            $quotation->save();

            if(isset($id)){
                $del_Dtls = TblPurcComparativeQuotationDtl::where('comparative_quotation_id',$id)->get();
                foreach ($del_Dtls as $del_Dtl){
                    TblPurcComparativeQuotationDtl::where('comparative_quotation_dtl_id',$del_Dtl->comparative_quotation_dtl_id)->delete();
                }
            }
            if(isset($request->pd)){
                foreach($request->pd as $dtl){
                    $quotationDtl = new TblPurcComparativeQuotationDtl();
                    if(isset($id) && isset($dtl['quotation_dtl_id'])){
                        $quotationDtl->comparative_quotation_dtl_id = $dtl['quotation_dtl_id'];
                        $quotationDtl->comparative_quotation_id = $id;
                    }else{
                        $quotationDtl->comparative_quotation_dtl_id = Utilities::uuid();
                        $quotationDtl->comparative_quotation_id = $quotation->comparative_quotation_id;
                    }
                    $quotationDtl->comparative_quotation_dtl_barcode = $dtl['barcode'];
                    $quotationDtl->prod_id = $dtl['product_id'];
                    $quotationDtl->uom_id = $dtl['uom_id'];
                    $quotationDtl->supplier_id = $dtl['supplier_id'];
                    $quotationDtl->comparative_quotation_dtl_packing = $dtl['packing_id'];
                    $quotationDtl->comparative_quotation_dtl_quantity = $dtl['quantity'];
                    $quotationDtl->comparative_quotation_dtl_foc_quantity = ($dtl['foc_qty'] == 'null')?'':$dtl['foc_qty'];
                    $quotationDtl->comparative_quotation_dtl_fc_rate = ($dtl['fc_rate'] == 'null')?'':$dtl['fc_rate'];
                    $quotationDtl->comparative_quotation_dtl_rate = $dtl['rate'];
                    $quotationDtl->comparative_quotation_dtl_amount = $dtl['amount'];
                    $quotationDtl->comparative_quotation_dtl_disc_percent = $dtl['discount'];
                    $quotationDtl->comparative_quotation_dtl_disc_amount = $dtl['discount_val'];
                    $quotationDtl->comparative_quotation_dtl_vat_percent = $dtl['vat_perc'];
                    $quotationDtl->comparative_quotation_dtl_vat_amount = $dtl['vat_val'];
                    $quotationDtl->comparative_quotation_dtl_total_amount = $dtl['gross_amount'];
                    $quotationDtl->comparative_quotation_dtl_approve = isset($dtl['approve'])?'1':'0';
                    $quotationDtl->business_id = auth()->user()->business_id;
                    $quotationDtl->company_id = auth()->user()->company_id;
                    $quotationDtl->branch_id = auth()->user()->branch_id;
                    $quotationDtl->comparative_quotation_dtl_user_id = auth()->user()->id;
                    $quotationDtl->save();
                }
            }

            if(isset($id)){
                $del_AccDtls = TblPurcComparativeQuotationAccount::where('comparative_quotation_id',$id)->get();
                foreach ($del_AccDtls as $del_accDtl){
                    TblPurcComparativeQuotationAccount::where('comparative_quotation_acc_id',$del_accDtl->comparative_quotation_acc_id)->delete();
                }
            }
            if(isset($request->pdsm)){
                foreach($request->pdsm as $account){
                    $accountDtl = new TblPurcComparativeQuotationAccount();
                    if(isset($id) && isset($account['quotation_acc_id'])){
                        $accountDtl->comparative_quotation_id = $id;
                        $accountDtl->comparative_quotation_acc_id = $account['quotation_acc_id'];
                    }else{
                        $accountDtl->comparative_quotation_acc_id = Utilities::uuid();
                        $accountDtl->comparative_quotation_id = $quotation->comparative_quotation_id;
                    }
                    $accountDtl->comparative_quotation_acc_chart_code = $account['chart_code'];
                    $accountDtl->comparative_quotation_acc_name = $account['Acc_name'];
                    $accountDtl->comparative_quotation_acc_amount = $account['Acc_amount'];
                    $accountDtl->business_id = auth()->user()->business_id;
                    $accountDtl->company_id = auth()->user()->company_id;
                    $accountDtl->branch_id = auth()->user()->branch_id;
                    $accountDtl->comparative_quotation_acc_user_id = auth()->user()->id;
                    $accountDtl->save();
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
        $quotation = TblPurcComparativeQuotation::where('comparative_quotation_id',$id)->first();
        $quotation->dtls()->delete();
        $quotation->accounts()->delete();
        $quotation->delete();
        return $this->returnJsonSucccess('Comparative Quotation successfully deleted.',200);
    }
}
