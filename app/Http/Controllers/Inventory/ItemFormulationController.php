<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Defi\TblDefiConstants;
use App\Models\TblInveItemFormulation;
use App\Models\TblInveItemFormulationDtl;
use Illuminate\Http\Request;
// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ItemFormulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Formulation';
    public static $menu_dtl_id = '66';
    public static $redirect_url = 'formulation';
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
        $data['form_type'] = 'formulation';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblInveItemFormulation::where('item_formulation_id','LIKE',$id)->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblInveItemFormulation::with('dtls','product')->where('item_formulation_id',$id)->first();
                // dd($data['current']);
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
                $data['document_code'] = $data['current']->item_formulation_code;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblInveItemFormulation',
                'code_field'        => 'item_formulation_code',
                'code_prefix'       => strtoupper('fn')
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }

        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_inve_item_formulation',
            'col_id' => 'item_formulation_id',
            'col_code' => 'item_formulation_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        $data['sale_type'] = TblDefiConstants::where('constants_type','sale_type')->where('constants_status','1')->get();
        $data['ingrediant_type'] = TblDefiConstants::where('constants_type','ingrediant_type')->where('constants_status','1')->get();
        // dd($data['sale_type']->toArray());
        return view('inventory.formulation.form',compact('data'));
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
            'formulation_date' => 'required|date_format:d-m-Y',
            //'f_product_id' => 'required|numeric',
           // 'f_product_barcode_id' => 'required|numeric',
            'formulation_qty' => 'required|numeric',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
            'pd.*.uom_id' => 'nullable|numeric',
            'pd.*.pd_barcode' => 'nullable|max:100',
            'pd.*.quantity' => 'nullable|numeric',
            'pd.*.cost_price' => 'nullable|numeric',
            'pd.*.pd_sale_type' => 'nullable|numeric',
            'pd.*.pd_ingredient_type' => 'nullable|numeric',
            'formulation_remarks' => 'nullable|max:100',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $formulation= TblInveItemFormulation::where('item_formulation_id',$id)->first();
            }else{
                $formulation = new TblInveItemFormulation();
                $formulation->item_formulation_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblInveItemFormulation',
                    'code_field'        => 'item_formulation_code',
                    'code_prefix'       => strtoupper('fn')
                ];
                $formulation->item_formulation_code = Utilities::documentCode($doc_data);
            }
            $form_id = $formulation->item_formulation_id;
            $formulation->product_id = $request->f_product_id;
            $formulation->product_barcode_id = $request->f_product_barcode_id;
            $formulation->product_barcode_barcode = $request->f_barcode;
            $formulation->item_formulation_qty = $request->formulation_qty;
            $formulation->item_formulation_date = date('Y-m-d', strtotime($request->formulation_date));
            $formulation->item_formulation_purchase_unit = $request->f_purchase_unit;
            $formulation->item_formulation_current_tp = $request->f_current_tp;
            $formulation->item_formulation_remarks = $request->formulation_remarks;
            $formulation->item_formulation_entry_status = 1;
            $formulation->business_id = auth()->user()->business_id;
            $formulation->company_id = auth()->user()->company_id;
            $formulation->branch_id = auth()->user()->branch_id;
            $formulation->item_formulation_user_id = auth()->user()->id;
            $formulation->save();

            $del_Dtls = TblInveItemFormulationDtl::where('item_formulation_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveItemFormulationDtl::where('item_formulation_dtl_id',$del_Dtl->item_formulation_dtl_id)->delete();
            }
            if(isset($request->pd)){
                foreach($request->pd as $pd){
                    $dtl = new TblInveItemFormulationDtl();
                    if(isset($id) && isset($pd['item_formulation_dtl_id'])){
                        $dtl->item_formulation_dtl_id = $pd['item_formulation_dtl_id'];
                        $dtl->item_formulation_id = $id;
                    }else{
                        $dtl->item_formulation_dtl_id = Utilities::uuid();
                        $dtl->item_formulation_id = $formulation->item_formulation_id;
                    }
                    $dtl->product_id = $form_id;
                   // dd( $dtl->product_id);
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->product_barcode_barcode = $pd['pd_barcode'];
                    $dtl->item_formulation_dtl_packing = $pd['pd_packing'];
                    $dtl->item_formulation_dtl_quantity = $pd['quantity'];
                    $dtl->item_formulation_dtl_purchase_unit = $pd['purchase_unit'];
                    $dtl->item_formulation_dtl_percentage = $pd['percentage'];
                    $dtl->item_formulation_dtl_cost_price = $pd['cost_price'];
                    $dtl->contants_sale_type_id = $pd['pd_sale_type'];
                    $dtl->contants_ingredient_type_id = $pd['pd_ingredient_type'];
                    $dtl->item_formulation_dtl_remarks = $pd['remarks'];
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->item_formulation_dtl_user_id = auth()->user()->id;
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
        $data['title'] = 'Formulation';
        if(isset($id)){
            if(TblInveItemFormulation::where('item_formulation_id','LIKE',$id)->exists()){
                $data['current'] = TblInveItemFormulation::with('product','dtls')->where('item_formulation_id',$id)->first();
                $data['permission'] = self::$menu_dtl_id.'-print';
            }else{
                abort('404');
            }
        }
        return view('prints.formulation_print',compact('data'));
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

            $stock= TblInveItemFormulation::where('item_formulation_id',$id)->first();
            $stock->dtls()->delete();
            $stock->delete();

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
