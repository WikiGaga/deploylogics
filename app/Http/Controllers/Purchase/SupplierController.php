<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\TblAccCoa;
use App\Models\TblDefiCity;
use App\Models\TblDefiCountry;
use App\Models\TblPurcDemand;
use App\Models\TblPurcGrn;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcSupplierBranch;
use App\Models\TblPurcSupplierDtl;
use App\Models\TblPurcSupplierAccount;
use App\Models\TblPurcSupplierType;
use App\Models\TblSoftBranch;
use App\Models\TblDefiBank;
use Illuminate\Http\Request;
use App\Library\Utilities;
use App\Models\TblDefiContactType;
use App\Models\TblDefiWHT;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Image;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierController extends Controller
{
    public static $page_title = 'Vendor';
    public static $redirect_url = 'supplier';
    public static $menu_dtl_id = '26';

    public function __construct()
    {
        $getStaticPrefix = Utilities::getStaticPrefix(self::$redirect_url);
        $this->current_path = $getStaticPrefix['path'];
        $this->page_form = '/'.self::$redirect_url.'/form';
        $this->page_view = '/'.self::$redirect_url.'/view';
    }

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
            if(TblPurcSupplier::where('supplier_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                if($this->current_path == $this->page_view){
                    $data['page_data'] = array_merge($data['page_data'], Utilities::viewForm());
                    $data['permission'] = self::$menu_dtl_id.'-view';
                }
                if($this->current_path == $this->page_form){
                    $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                    $data['permission'] = self::$menu_dtl_id.'-edit';
                }
                $data['id'] = $id;
                $data['current'] =  TblPurcSupplier::with("sub_supplier","supplier_acc",'supplier_branches')->where('supplier_id',$id)->where(Utilities::currentBC())->first();
                $data['current_supplier_type'] =  TblPurcSupplierType::where('supplier_type_id',$data['current']->supplier_type)->where(Utilities::currentBC())->first();
                $data['supplier_code'] = $data['current']->supplier_code;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'TblPurcSupplier',
                'code_field'        => 'supplier_code',
                'code_prefix'       => strtoupper('sp')
            ];
            $data['supplier_code'] = Utilities::documentCode($doc_data);
        }

        $data['city'] = TblDefiCountry::with('country_cities')->where(Utilities::currentBC())->where('country_entry_status',1)->orderBy('country_name','ASC')->get();
        $data['type'] = TblPurcSupplierType::where('supplier_type_entry_status',1)->where(Utilities::currentBC())->get();
        $data['contact'] = TblDefiContactType::where('contact_type_entry_status',1)->where(Utilities::currentBC())->get();
        $data['wht'] = TblDefiWHT::where(Utilities::currentBC())->get();
        if(isset($id)){
            $collection = collect($data['type']);
            $data['type'] = $collection->push($data['current_supplier_type']);
        }
        $data['branch'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
        $data['bank'] = TblDefiBank::where('bank_entry_status',1)->where(Utilities::currentBC())->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_entry_status',1)->where(Utilities::currentBC())->get();
        $arr = [
            'biz_type' => 'business',
            'code' => $data['supplier_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_supplier',
            'col_id' => 'supplier_id',
            'col_code' => 'supplier_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.supplier.form',compact('data'));
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
            'supplier_name' => 'required|max:150',
            'supplier_cr_no' => 'nullable|numeric'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }

        $referer = Utilities::getReferer($request,$id);
        if($referer == $this->page_view){
            return $this->jsonErrorResponse($data, "Cannot update this entry.", 200);
        }
        DB::beginTransaction();
        try{
            $sup_type = TblPurcSupplierType::where('supplier_type_id',$request->supplier_type)->where(Utilities::currentBC())->first();
            $acc_code = TblAccCoa::where('chart_account_id',$sup_type->supplier_type_account_id)->where(Utilities::currentBC())->first();
            $level_no = 4;
            $parent_account_code = $acc_code->chart_code; //
            $business_id = auth()->user()->business_id;
            $company_id = auth()->user()->company_id;
            $branch_id = auth()->user()->branch_id;
            $user_id = auth()->user()->id;
            $chart_name = $request->supplier_name;

            if(isset($id)){
                $supplier =TblPurcSupplier::where('supplier_id',$id)->where(Utilities::currentBC())->first();
                $acc_id = $supplier->supplier_account_id;
                $this->proPurcChartUpdate($business_id,$company_id,$branch_id,$chart_name,$acc_id);
            }else{
                $supplier_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                $supplier = new TblPurcSupplier();
                $supplier->supplier_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'TblPurcSupplier',
                    'code_field'        => 'supplier_code',
                    'code_prefix'       => strtoupper('sp')
                ];
                $supplier->supplier_code = Utilities::documentCode($doc_data);
                $supplier->supplier_account_id = $supplier_account_id;
            }
            $form_id = $supplier->supplier_id;
            $supplier->supplier_name = $request->supplier_name;
            $supplier->supplier_cr_no = $request->supplier_cr_no;
            $supplier->supplier_local_name = $request->supplier_local_name;
            $supplier->supplier_type = $request->supplier_type;
            $supplier->supplier_entry_status = isset($request->supplier_entry_status)?"1":"0";
            if($request->hasFile('supplier_image'))
            {
                $folder = 'images/supplier/';
                if (! File::exists($folder)) {
                    File::makeDirectory($folder, 0775, true,true);
                }
                $image = $request->file('supplier_image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = public_path($folder . $filename);
                Image::make($image->getRealPath())->resize(200, 200)->save($path);
                $supplier->supplier_image = isset($filename)?$filename:'';
            }

            $supplier->supplier_address = $request->supplier_address;
            if($request->city_id != 0){
                $supplier->city_id = $request->city_id;
                $country_id = TblDefiCity::with('city_country')->where(Utilities::currentBC())->where('city_id',$request->city_id)->where('city_entry_status',1)->first();
                $supplier->country_id = $country_id->city_country['country_id'];
            }else{
                $supplier->city_id = '';
                $supplier->country_id = '';
            }
            $supplier->supplier_zip_code = $request->supplier_zip_code;
            $supplier->supplier_longitude = $request->supplier_longitude;
            $supplier->supplier_latitude = $request->supplier_latitude;
            $supplier->supplier_contact_person = $request->supplier_contact_person_name;
            $supplier->supplier_contact_person_mobile = $request->supplier_contact_person_mobile_no;
            $supplier->supplier_contact_person_designation = $request->supplier_contact_person_designation;
            $supplier->supplier_po_box = $request->supplier_po_box;
            $supplier->supplier_phone_1 = $request->supplier_phone_1;
            $supplier->supplier_mobile_no = $request->supplier_mobile_no;
            $supplier->supplier_fax = $request->supplier_fax;
            $supplier->supplier_whatapp_no = $request->supplier_whatapp_no;
            $supplier->supplier_email = $request->supplier_email;
            $supplier->supplier_website = $request->supplier_website;
            $supplier->supplier_reference_code = $request->supplier_reference_code;
            $supplier->payment_term_id = $request->supplier_aging_terms;
            $supplier->supplier_ageing_terms_value = $request->supplier_ageing_terms_value;
            $supplier->supplier_credit_period = $request->supplier_credit_period;
            $supplier->supplier_tax_no = $request->supplier_tax_no;
            $supplier->supplier_credit_limit = $request->supplier_credit_limit;
            $supplier->supplier_debit_limit = $request->supplier_debit_limit;
            $supplier->wht_type_id = $request->wht_type;
            $supplier->supplier_tax_rate = $request->supplier_tax_rate;
            $supplier->supplier_tax_status = $request->supplier_tax_status;
            //$supplier->supplier_cheque_beneficry_name = $request->supplier_cheque_beneficry_name;
            $supplier->supplier_mode_of_payment = $request->supplier_mode_of_payment;
            $supplier->supplier_can_scale = isset($request->supplier_can_scale)?"1":"0";
            $supplier->supplier_bank_name = $request->supplier_bank_name;
            $supplier->supplier_bank_account_no = $request->supplier_bank_account_no;
            $supplier->supplier_bank_account_title = $request->supplier_bank_account_title;
            $supplier->supplier_gst_no = $request->supplier_gst_no;
            $supplier->supplier_ntn_no = $request->supplier_ntn_no;
            $supplier->business_id = auth()->user()->business_id;
            $supplier->company_id = auth()->user()->company_id;
            $supplier->branch_id = auth()->user()->branch_id;
            $supplier->supplier_user_id = auth()->user()->id;
            $supplier->save();

            // TblPurcSupplierBranch
            if(isset($id)){
                $del_brchs = TblPurcSupplierBranch::where('supplier_id',$id)->get();
                foreach ($del_brchs as $del_brch){
                    TblPurcSupplierBranch::where('supplier_id',$del_brch->supplier_id)->delete();
                }
            }
            if(isset($request->supplier_branch_id)){
                foreach($request->supplier_branch_id as $branch){
                    $supplier_branch = new TblPurcSupplierBranch();
                    $supplier_branch->supplier_branch_id = Utilities::uuid();
                    $supplier_branch->supplier_id = $supplier->supplier_id;
                    $supplier_branch->branch_id = $branch;
                    $supplier_branch->supplier_branch_entry_status = 1;
                    $supplier_branch->save();
                }
            }
            if(isset($id)){
                $del_Dtls = TblPurcSupplierDtl::where('supplier_id',$id)->where(Utilities::currentBC())->get();
                foreach ($del_Dtls as $del_Dtl){
                    TblPurcSupplierDtl::where('supplier_dtl_id',$del_Dtl->supplier_dtl_id)->where(Utilities::currentBC())->delete();
                }
            }
            if(isset($request->pd)){
                foreach ($request->pd as $subSupplier){
                    $sub_supplier = new TblPurcSupplierDtl();
                    if(isset($id) && isset($subSupplier['supplier_dtl_id'])){
                        $sub_supplier->supplier_dtl_id = $subSupplier['supplier_dtl_id'];
                        $sub_supplier->supplier_id = $id;
                    }else{
                        $sub_supplier->supplier_dtl_id = Utilities::uuid();
                        $sub_supplier->supplier_id = $supplier->supplier_id;
                    }
                    $sub_supplier->supplier_dtl_name = $subSupplier['supplier_dtl_name'];
                    $sub_supplier->supplier_dtl_cont_no = $subSupplier['supplier_dtl_cont_no'];
                    $sub_supplier->supplier_dtl_address = $subSupplier['supplier_dtl_address'];
                    $sub_supplier->supplier_dtl_entry_status = 1;
                    $sub_supplier->business_id = auth()->user()->business_id;
                    $sub_supplier->company_id = auth()->user()->company_id;
                    $sub_supplier->branch_id = auth()->user()->branch_id;
                    $sub_supplier->supplier_dtl_user_id = auth()->user()->id;
                    $sub_supplier->save();
                }
            }
            if(isset($id)){
                $del_accounts = TblPurcSupplierAccount::where('supplier_id',$id)->where(Utilities::currentBC())->get();
                foreach ($del_accounts as $del_acc){
                    TblPurcSupplierAccount::where('supplier_account_id',$del_acc->supplier_account_id)->where(Utilities::currentBC())->delete();
                }
            }
            if(isset($request->pdsm)){
                foreach ($request->pdsm as $SupplierAcc){
                    $Supplier_Acc = new TblPurcSupplierAccount();
                    if(isset($id) && isset($SupplierAcc['supplier_account_id'])){
                        $Supplier_Acc->supplier_account_id = $SupplierAcc['supplier_account_id'];
                        $Supplier_Acc->supplier_id = $id;
                    }else{
                        $Supplier_Acc->supplier_account_id = Utilities::uuid();
                        $Supplier_Acc->supplier_id = $supplier->supplier_id;
                    }
                    $Supplier_Acc->supplier_bank_name = $SupplierAcc['supplier_bank_name'];
                    $Supplier_Acc->supplier_account_no = $SupplierAcc['supplier_bank_account_no'];
                    $Supplier_Acc->supplier_account_title = $SupplierAcc['supplier_bank_account_title'];
                    $Supplier_Acc->supplier_iban_no = $SupplierAcc['supplier_bank_iban_no'];
                    $Supplier_Acc->business_id = auth()->user()->business_id;
                    $Supplier_Acc->company_id = auth()->user()->company_id;
                    $Supplier_Acc->branch_id = auth()->user()->branch_id;
                    $Supplier_Acc->supplier_account_user_id = auth()->user()->id;
                    $Supplier_Acc->save();
                }
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
    public function supplierCode($code)
    {
        if(TblPurcSupplier::where(DB::raw('lower(supplier_code)'),'LIKE',strtolower($code))->where(Utilities::currentBC())->exists()){
            $data['data'] = TblPurcSupplier::where(DB::raw('lower(supplier_code)'),'LIKE',strtolower($code))
                ->where(Utilities::currentBC())->first();

        }else{
            $data['data'] = null;
        }
        return response()->json($data);
    }

    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
            $demand = TblPurcDemand::withCount('supplier')->where(Utilities::currentBC())->where('supplier_id',$id)->first();
            $po = TblPurcPurchaseOrder::withCount('supplier')->where(Utilities::currentBC())->where('supplier_id',$id)->first();
            $grn = TblPurcGrn::withCount('supplier')->where(Utilities::currentBC())->where('supplier_id',$id)->first();
            if($demand == null && $po == null && $grn == null) {
                $supplier = TblPurcSupplier::where('supplier_id',$id)->where(Utilities::currentBC())->first();

                $business_id = auth()->user()->business_id;
                $company_id = auth()->user()->company_id;
                $branch_id = auth()->user()->branch_id;
                $acc_id = $supplier->supplier_account_id;
                $this->proPurcChartDelete($business_id,$company_id,$branch_id,$acc_id);

                $supplier->sub_supplier()->delete();
                $supplier->supplier_acc()->delete();
                $supplier->delete();
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
