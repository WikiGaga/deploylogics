<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\TblPurcSupplierContract;
use App\Models\TblPurcSupplierContractDtl;
use Illuminate\Http\Request;
// db and Validator
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierContracController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Slab & Rebate Agreements';
    public static $redirect_url = 'supcontract';
    public static $menu_dtl_id = '53';
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
        $data['form_type'] = 'slabrebate';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] =  $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblPurcSupplierContract::where('contract_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcSupplierContract::with('contractDtl','supplier')->where(Utilities::currentBC())->where('contract_id',$id)->first();
                $data['code'] = $data['current']->contract_code;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'TblPurcSupplierContract',
                'code_field'        => 'contract_code',
                'code_prefix'       => strtoupper('sc')
            ];
            $data['code'] = Utilities::documentCode($doc_data);
        }
        $arr = [
            'biz_type' => 'business',
            'code' => $data['code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_supplier_contract',
            'col_id' => 'contract_id',
            'col_code' => 'contract_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.slabe_rebate.form',compact('data'));
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
            'supplier_name' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $SC = TblPurcSupplierContract::where('contract_id',$id)->where(Utilities::currentBC())->first();
            }else{
                $SC = new TblPurcSupplierContract();
                $SC->contract_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'TblPurcSupplierContract',
                    'code_field'        => 'contract_code',
                    'code_prefix'       => strtoupper('sc')
                ];
                $SC->contract_code = Utilities::documentCode($doc_data);
            }
            $form_id = $SC->contract_id;
            $SC->supplier_id = $request->supplier_id;
            $SC->contract_rebete_level = $request->contract_rebate_level;
            $SC->contract_start_date = date('Y-m-d', strtotime($request->contract_start_date));
            $SC->contract_end_date = date('Y-m-d', strtotime($request->contract_end_date));
            $SC->contract_notes = $request->contract_notes;
            $SC->contract_entry_status = '1';
            $SC->business_id = auth()->user()->business_id;
            $SC->company_id = auth()->user()->company_id;
            $SC->branch_id = auth()->user()->branch_id;
            $SC->contract_user_id = auth()->user()->id;
            $SC->save();

            $sc_dtls = TblPurcSupplierContractDtl::where('contract_id',$SC->contract_id)->where(Utilities::currentBC())->get();
            foreach($sc_dtls as $sc_dtl){
                TblPurcSupplierContractDtl::where('contract_dtl_id',$sc_dtl->contract_dtl_id)->where(Utilities::currentBC())->delete();
            }

            if(isset($request->pd)){
                $sr_no = 1;
                foreach($request->pd as $dtl){
                    $scDtl = new TblPurcSupplierContractDtl();
                    if(isset($id) && isset($dtl['contract_dtl_id'])){
                        $scDtl->contract_id = $id;
                        $scDtl->contract_dtl_id = $dtl['contract_dtl_id'];
                    }else{
                        $scDtl->contract_dtl_id = Utilities::uuid();
                        $scDtl->contract_id  = $SC->contract_id;
                    }
                    $scDtl->contract_dtl_sr_no = $sr_no++;
                    $scDtl->product_id = $dtl['product_id'];
                    $scDtl->product_barcode_id = $dtl['product_barcode_id'];
                    $scDtl->product_barcode_barcode = $dtl['pd_barcode'];
                    $scDtl->contract_dtl_group = $dtl['group_id'];
                    $scDtl->contract_dtl_brand = $dtl['brand_id'];
                    $scDtl->contract_dtl_quantity = $dtl['quantity'];
                    $scDtl->contract_dtl_disc_percent = $dtl['discount'];
                    $scDtl->contract_dtl_example_remarks = $dtl['examp_remarks'];
                    $scDtl->business_id = auth()->user()->business_id;
                    $scDtl->company_id = auth()->user()->company_id;
                    $scDtl->branch_id = auth()->user()->branch_id;
                    $scDtl->contract_dtl_user_id = auth()->user()->id;
                    $scDtl->save();

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
            $SC= TblPurcSupplierContract::where('contract_id',$id)->where(Utilities::currentBC())->first();
            $SC->contractDtl()->delete();
            $SC->delete();
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
