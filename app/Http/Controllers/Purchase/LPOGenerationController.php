<?php

namespace App\Http\Controllers\Purchase;

use Exception;
use Validator;
use App\Library\Utilities;
use App\Models\TblPurcLpo;
use App\Models\ViewPurcGRN;
use Illuminate\Http\Request;
use App\Models\TblPurcDemand;
use App\Models\TblPurcLpoDtl;
use App\Models\TblSoftBranch;
use App\Models\TblDefiCurrency;
use Illuminate\Validation\Rule;
use App\Models\TblPurcLpoDtlDtl;

// db and Validator
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblPurcPurchaseOrder;
use App\Models\ViewPurcDemandApproval;
use Illuminate\Database\QueryException;
use App\Models\TblPurcProductBarcodePurchRate;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LPOGenerationController extends Controller
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
    public static $page_title = 'Lpo Generation';
    public static $redirect_url = 'lpo';
    public static $menu_dtl_id = '21';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'lpo';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblPurcLpo::where('lpo_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcLpo::with('dtls')->where('lpo_id',$id)->where(Utilities::currentBCB())->first();
                $data['document_code'] = $data['current']->lpo_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblPurcLpo::max('lpo_code'),'L');
        }
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->where(Utilities::currentBC())->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_lpo',
            'col_id' => 'lpo_id',
            'col_code' => 'lpo_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.lpo_generation.form', compact('data'));
    }
    public function demand($id)
    {
        $data = [];
        DB::beginTransaction();
        try {
            $data['all'] = ViewPurcDemandApproval::with('product_dtl')->where('demand_approval_dtl_id',$id)
                ->where('demand_approval_dtl_approve_status','approved')
                ->where('demand_approval_dtl_entry_status',1)
                ->orderBy('sr_no' , 'asc')
                ->get();
            foreach ($data['all'] as $item) {
                if(!isset($item->supplier_id)){
                    $rateData = TblPurcProductBarcodePurchRate::where('product_barcode_barcode', $item->product_barcode_barcode)
                    ->where('product_barcode_id',$item->product_barcode_id)
                    ->where('branch_id',auth()->user()->branch_id)->first();
                }else{
                    $rateData = ViewPurcGRN::where('grn_type' , 'GRN')
                    ->where('product_id' , $item->product_id)
                    ->where('product_barcode_barcode' , $item->product_barcode_barcode)
                    ->where('supplier_id' , $item->supplier_id)
                    ->orderBy('grn_updated_at' , 'desc');
                    if($rateData->count() > 0){
                        $rateData = $rateData->first();
                    }else{
                        $rateData = TblPurcProductBarcodePurchRate::where('product_barcode_barcode', $item->product_barcode_barcode)
                        ->where('product_barcode_id',$item->product_barcode_id)
                        ->where('branch_id',auth()->user()->branch_id)->first();
                    }
                }
                // $item->vat = TblPurcProductBarcodeDtl::where('product_barcode_id',$data['current_product']['product_barcode_id'])
                //     ->where('branch_id',auth()->user()->branch_id)->first();
                $item->rate = $this->filterData($rateData);
            }
            $data['current_branch'] = TblSoftBranch::where('branch_id' , auth()->user()->branch_id)->first();
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
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data['id'] = $id;
            return $this->jsonSuccessResponse($data, 'Data loaded', 200);
        }else{
            return $this->jsonErrorResponse($data, 'Something will be wrong', 200);
        }
    }
    /*public function supplier()
    {
        $data = TblPurcSupplier::get();
        return view('purchase.lpo_generation.supplier',compact('data'));
    }*/
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {
        // dd($request->all());
        $data = [];
        $validator = Validator::make($request->all(), [
            'lpo_currency' => 'required',
            'exchange_rate' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if($request->pro_tot <= 0){
            return $this->returnjsonerror("Please Fill The Data",201);
        }
        DB::beginTransaction();
        try {
            if(isset($id)){
                $lpo =TblPurcLpo::where('lpo_id',$id)->first();
            }else{
                $lpo = new TblPurcLpo();
                $lpo->lpo_id = Utilities::uuid();
                $lpo->lpo_code = $this->documentCode(TblPurcLpo::max('lpo_code'),'L');
            }
            $form_id = $lpo->lpo_id;
            $lpo->currency_id = $request->lpo_currency;
            $lpo->lpo_exchange_rate = $request->exchange_rate;
            $lpo->lpo_date = date('Y-m-d', strtotime($request->lpo_date));
            $lpo->lpo_remarks = $request->lpo_remarks;
            $lpo->lpo_entry_status = 1;
            $lpo->business_id = auth()->user()->business_id;
            $lpo->company_id = auth()->user()->company_id;
            $lpo->branch_id = auth()->user()->branch_id;
            $lpo->lpo_user_id = auth()->user()->id;
            $lpo->save();

            if(isset($id)){
                $del_Dtls = TblPurcLpoDtl::where('lpo_id',$id)->get();
                foreach ($del_Dtls as $del_Dtl){
                    TblPurcLpoDtl::where('lpo_dtl_id',$del_Dtl->lpo_dtl_id)->delete();
                }
            }
            if(isset($request->pd)){
                $sr_no = 1;
                foreach ($request->pd as $pd){
                    $dtl = new TblPurcLpoDtl();
                    if(isset($id) && isset($pd['lpo_dtl_id'])){
                        $dtl->lpo_dtl_id = $pd['lpo_dtl_id'];
                        $dtl->lpo_id = $id;
                    }else{
                        $dtl->lpo_dtl_id = Utilities::uuid();
                        $dtl->lpo_id = $lpo->lpo_id;
                    }
                    if(!isset($pd['supplier_id']) && $pd['action'] == 'lpo'){
                        return $this->jsonErrorResponse($data, 'Please Select Supplier For : '. $pd['sr_no'], 200);
                    }
                    $dtl->sr_no = $sr_no++;
                    $dtl->supplier_id = isset($pd['supplier_id'])?$pd['supplier_id']:"";
                    $dtl->lpo_dtl_branch_id = isset( $pd['lpo_dtl_branch_id'])? $pd['lpo_dtl_branch_id']:"";
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_barcode = isset( $pd['pd_barcode'])? $pd['pd_barcode']:"";
                    $dtl->product_barcode_id = isset($pd['product_barcode_id'])?$pd['product_barcode_id']:"";
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->demand_approval_dtl_id = isset($pd['demand_approval_dtl_id'])?$pd['demand_approval_dtl_id']:"";
                    $dtl->lpo_dtl_packing = $pd['pd_packing'];
                    $dtl->demand_id = $pd['demand_id'];
                    $dtl->payment_mode_id = $pd['payment_mode'];
                    $dtl->lpo_dtl_quantity = $pd['quantity'];
                    $dtl->lpo_dtl_foc_quantity = $pd['foc_qty'];
                    $dtl->lpo_dtl_fc_rate = $this->addNo($pd['fc_rate']);
                    $dtl->lpo_dtl_rate = $this->addNo($pd['rate']);
                    $dtl->lpo_dtl_amount = $this->addNo($pd['amount']);
                    $dtl->lpo_dtl_disc_percent = $this->addNo($pd['dis_perc']);
                    $dtl->lpo_dtl_disc_amount = $this->addNo($pd['dis_amount']);
                    $dtl->lpo_dtl_approv_quantity = '';
                    $dtl->lpo_dtl_vat_percent = $this->addNo($pd['vat_perc']);
                    $dtl->lpo_dtl_vat_amount = $this->addNo($pd['vat_amount']);
                    $dtl->lpo_dtl_gross_amount = $this->addNo($pd['gross_amount']);
                    if(!isset($pd['action'])){
                        return $this->jsonErrorResponse($data, 'Please Select Generate Lpo Or Quotation', 200);
                    }
                    $dtl->lpo_dtl_generate_quotation = ($pd['action'] == 'quot')?1:"";
                    $dtl->lpo_dtl_generate_lpo = ($pd['action'] == 'lpo')?1:"";
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->lpo_dtl_user_id = auth()->user()->id;
                    $dtl->save();

                    if(isset($id)){
                        $del_dlt_Dtls = TblPurcLpoDtlDtl::where('lpo_id',$id)->get();
                        foreach ($del_dlt_Dtls as $del_dtl_Dtl){
                            TblPurcLpoDtlDtl::where('lpo_dtl_dtl_id',$del_dtl_Dtl->lpo_dtl_dtl_id)->delete();
                        }
                    }
                    if(isset($pd['sub'])){
                        foreach($pd['sub'] as $sub_pd){
                            $dtl_dtl = new TblPurcLpoDtlDtl();
                            if(isset($id) && isset($sub_pd['lpo_dtl_id']) && isset($sub_pd['lpo_dtl_dtl_id'])){
                                $dtl_dtl->lpo_dtl_dtl_id = $sub_pd['lpo_dtl_dtl_id'];
                                $dtl_dtl->lpo_dtl_id = $pd['lpo_dtl_id'];
                                $dtl_dtl->lpo_id = $id;
                            }else{
                                $dtl_dtl->lpo_dtl_dtl_id = Utilities::uuid();
                                $dtl_dtl->lpo_dtl_id = $dtl->lpo_dtl_id;
                                $dtl_dtl->lpo_id = $lpo->lpo_id;
                            }
                            $dtl_dtl->lpo_dtl_branch_id = isset($sub_pd['lpo_dtl_branch_id'])?$sub_pd['lpo_dtl_branch_id']:'';
                            $dtl_dtl->uom_id = $sub_pd['uom_id'];
                            $dtl_dtl->demand_approval_dtl_id = isset($sub_pd['demand_approval_dtl_id'])?$sub_pd['demand_approval_dtl_id']:"";
                            $dtl_dtl->supplier_id = isset($sub_pd['supplier_id'])?$sub_pd['supplier_id']:"";
                            $dtl_dtl->product_id = $sub_pd['product_id'];
                            $dtl_dtl->product_barcode_barcode = $sub_pd['pd_barcode'];
                            $dtl_dtl->product_barcode_id = $sub_pd['product_barcode_id'];
                            $dtl_dtl->lpo_dtl_packing = $sub_pd['pd_packing'];
                            $dtl_dtl->demand_id = $sub_pd['demand_id'];
                            $dtl_dtl->payment_mode_id = $sub_pd['payment_mode'];
                            $dtl_dtl->lpo_dtl_quantity = $sub_pd['quantity'];
                            $dtl_dtl->lpo_dtl_foc_quantity = $sub_pd['foc_qty'];
                            $dtl_dtl->lpo_dtl_fc_rate = $this->addNo($sub_pd['fc_rate']);
                            $dtl_dtl->lpo_dtl_fc_rate = $this->addNo($sub_pd['rate']);
                            $dtl_dtl->lpo_dtl_amount = $this->addNo($sub_pd['amount']);
                            $dtl_dtl->lpo_dtl_disc_percent = $this->addNo($sub_pd['dis_perc']);
                            $dtl_dtl->lpo_dtl_disc_amount = $this->addNo($sub_pd['dis_amount']);
                            $dtl_dtl->lpo_dtl_approv_quantity = '';
                            $dtl_dtl->lpo_dtl_vat_percent = $this->addNo($sub_pd['vat_perc']);
                            $dtl_dtl->lpo_dtl_vat_amount = $this->addNo($sub_pd['vat_amount']);
                            $dtl_dtl->lpo_dtl_gross_amount = $this->addNo($sub_pd['gross_amount']);
                            if(isset($sub_pd['action'])){
                                $dtl_dtl->lpo_dtl_generate_quotation = ($sub_pd['action'] == 'quot')?1:"";
                                $dtl_dtl->lpo_dtl_generate_lpo = ($sub_pd['action'] == 'lpo')?1:"";
                            }
                            $dtl_dtl->business_id = auth()->user()->business_id;
                            $dtl_dtl->company_id = auth()->user()->company_id;
                            $dtl_dtl->branch_id = auth()->user()->branch_id;
                            $dtl_dtl->lpo_dtl_user_id = auth()->user()->id;
                            $dtl_dtl->save();
                        }
                    }
                }
            }
        }catch (QueryException $e) {
            \Illuminate\Support\Facades\DB::rollback();
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
        $data['title'] = self::$page_title;
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblPurcLpo::where('lpo_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblPurcLpo::with('dtls')->where('lpo_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->where('currency_id',$data['current']->currency_id)->first();
        return view('prints.lpo_print', compact('data'));
    }

    public function filterData($arr){
        $data = [];

        $data['fc_rate'] = isset($arr->tbl_purc_grn_dtl_fc_rate) ? $arr->tbl_purc_grn_dtl_fc_rate : 0;
        $data['purc_rate'] = isset($arr->product_barcode_purchase_rate) ? $arr->product_barcode_purchase_rate : $arr->tbl_purc_grn_dtl_rate;
        $data['disc_per'] = isset($arr->tbl_purc_grn_dtl_disc_percent) ? $arr->tbl_purc_grn_dtl_disc_percent : 0;
        $data['vat_per'] = isset($arr->tbl_purc_grn_dtl_vat_percent) ? $arr->tbl_purc_grn_dtl_vat_percent : 0;
        $data['disc_amount'] = isset($arr->tbl_purc_grn_dtl_disc_amount) ? $arr->tbl_purc_grn_dtl_disc_amount : 0;
        $data['vat_amount'] = isset($arr->tbl_purc_grn_dtl_vat_amount) ? $arr->tbl_purc_grn_dtl_vat_amount : 0;

        return $data;
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

            $dataExist = TblPurcPurchaseOrder::where('lpo_id','Like',$id)->exists();
            if($dataExist === false)
            {
                $lpo = TblPurcLpo::where('lpo_id',$id)->first();
                $lpo->dtls()->delete();
                $lpo->delete();
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
