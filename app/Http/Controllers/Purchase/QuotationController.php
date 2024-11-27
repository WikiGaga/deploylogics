<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcLpo;
use App\Models\TblPurcQuotation;
use App\Models\TblPurcQuotationDtl;
use App\Models\User;
use App\Models\TblPurcQuotationAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuotationController extends Controller
{
    public static $page_title = 'Quotation';
    public static $redirect_url = 'quotation';
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
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblPurcQuotation::where('quotation_id',$id)->exists()){
                $data['page_data']['type'] = 'Edit';
                $data['page_data']['action'] = 'Update';
                $data['id'] = $id;
                $data['current'] = TblPurcQuotation::with('dtls','supplier','lpo','accounts')->where('quotation_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['page_data']['type'] = 'New';
            $data['page_data']['action'] = 'Save';
            $data['document_code'] = $this->documentCode(TblPurcQuotation::max('quotation_code'),'Q');
        }

        $data['currency'] = TblDefiCurrency::get();
        $data['payment_terms'] = TblAccoPaymentTerm::get();
        return view('purchase.quotation.form',compact('data'));
    }

    public function lpo()
    {
        $data = DB::table('tbl_purc_lpo')
            ->join('tbl_purc_lpo_dtl','tbl_purc_lpo.lpo_id','=','tbl_purc_lpo_dtl.lpo_id')
            ->join('tbl_purc_product','tbl_purc_product.product_id','=','tbl_purc_lpo_dtl.prod_id')
            ->join('tbl_defi_uom', 'tbl_defi_uom.uom_id', '=', 'tbl_purc_lpo_dtl.uom_id')
            ->join('tbl_purc_packing', 'tbl_purc_packing.packing_id', '=', 'tbl_purc_lpo_dtl.lpo_dtl_packing')
            ->select('tbl_purc_lpo.*','tbl_purc_lpo_dtl.*','tbl_defi_uom.uom_name','tbl_purc_packing.packing_name','tbl_purc_product.product_name')
            ->get();
      //  dd($data->toArray());
        return view('purchase.quotation.lpo',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
      //  dd($request->toArray());
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
                $quotation = TblPurcQuotation::where('quotation_id',$id)->first();
            }else{
                $quotation = new TblPurcQuotation();
                $quotation->quotation_id = Utilities::uuid();
                $quotation->quotation_code = $this->documentCode(TblPurcQuotation::max('quotation_code'),'Q');
            }
            $quotation->quotation_entry_date = date('Y-m-d', strtotime($request->quot_date));;
            $quotation->quotation_payment_mode_id = $request->payment_terms;
            $quotation->quotation_credit_days = $request->payment_mode;
            $quotation->lpo_id = $request->lpo_generation_no_id;
            $quotation->quotation_supplier_id = $request->supplier_id;
            $quotation->quotation_currency_id = $request->quotation_currency;
            $quotation->quotation_exchange_rate = $request->exchange_rate;
            $quotation->quotation_remarks = $request->quotation_notes;
            $quotation->quotation_terms = $request->quotation_terms;
            $quotation->quotation_entry_status = "1";
            $quotation->business_id = auth()->user()->business_id;
            $quotation->company_id = auth()->user()->company_id;
            $quotation->branch_id = auth()->user()->branch_id;
            $quotation->quotation_user_id = auth()->user()->id;
            $quotation->save();

            $del_QuotationDtls = TblPurcQuotationDtl::where('quotation_id',$id)->get();
            foreach ($del_QuotationDtls as $del_QuotationDtl){
                TblPurcQuotationDtl::where('quotation_dtl_id',$del_QuotationDtl->quotation_dtl_id)->delete();
            }
            if(isset($request->pd)){
                foreach($request->pd as $dtl){
                    $quotationDtl = new TblPurcQuotationDtl();
                    if(isset($id) && isset($dtl['q_dtl_id'])){
                        $quotationDtl->quotation_dtl_id = $dtl['q_dtl_id'];
                        $quotationDtl->quotation_id = $id;
                    }else{
                        $quotationDtl->quotation_dtl_id = Utilities::uuid();
                        $quotationDtl->quotation_id = $quotation->quotation_id;
                    }
                    $quotationDtl->quotation_dtl_barcode = $dtl['barcode'];
                    $quotationDtl->prod_id = $dtl['product_id'];
                    $quotationDtl->uom_id = $dtl['uom_id'];
                    $quotationDtl->quotation_dtl_packing = $dtl['packing_id'];
                    $quotationDtl->quotation_dtl_quantity = $dtl['quantity'];
                    $quotationDtl->quotation_dtl_foc_quantity = $dtl['foc_qty'];
                    $quotationDtl->quotation_dtl_fc_rate = $dtl['fc_rate'];
                    $quotationDtl->quotation_dtl_rate = $dtl['rate'];
                    $quotationDtl->quotation_dtl_amount = $dtl['amount'];
                    $quotationDtl->quotation_dtl_disc_percent = $dtl['discount'];
                    $quotationDtl->quotation_dtl_disc_amount = $dtl['discount_val'];
                    $quotationDtl->quotation_dtl_vat_percent = $dtl['vat_perc'];
                    $quotationDtl->quotation_dtl_vat_amount = $dtl['vat_val'];
                    $quotationDtl->quotation_dtl_total_amount = $dtl['gross_amount'];
                    $quotationDtl->business_id = auth()->user()->business_id;
                    $quotationDtl->company_id = auth()->user()->company_id;
                    $quotationDtl->branch_id = auth()->user()->branch_id;
                    $quotationDtl->quotation_dtl_user_id = auth()->user()->id;
                    $quotationDtl->save();
                }
            }

            if(isset($id)){
                $del_AccDtls = TblPurcQuotationAccount::where('quotation_id',$id)->get();
                foreach ($del_AccDtls as $del_accDtl){
                    TblPurcQuotationAccount::where('quotation_acc_id',$del_accDtl->quotation_acc_id)->delete();
                }
            }
            if(isset($request->pdsm)){
                foreach($request->pdsm as $account){
                    $accountDtl = new TblPurcQuotationAccount();
                    if(isset($id) && isset($account['quotation_acc_id'])){
                        $accountDtl->quotation_id = $id;
                        $accountDtl->quotation_acc_id = $account['quotation_acc_id'];
                    }else{
                        $accountDtl->quotation_acc_id = Utilities::uuid();
                        $accountDtl->quotation_id = $quotation->quotation_id;
                    }
                    $accountDtl->quotation_acc_chart_code = $account['chart_code'];
                    $accountDtl->quotation_acc_name = $account['Acc_name'];
                    $accountDtl->quotation_acc_amount = $account['Acc_amount'];
                    $accountDtl->business_id = auth()->user()->business_id;
                    $accountDtl->company_id = auth()->user()->company_id;
                    $accountDtl->branch_id = auth()->user()->branch_id;
                    $accountDtl->quotation_acc_user_id = auth()->user()->id;
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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
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
        $quotation = TblPurcQuotation::where('quotation_id',$id)->first();
        $quotation->dtls()->delete();
        $quotation->accounts()->delete();
        $quotation->delete();
        return $this->returnJsonSucccess('Quotation successfully deleted.',200);
    }
}
