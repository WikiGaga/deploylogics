<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcSupProd;
use App\Models\TblPurcSupProdDtl;
use App\Models\TblSoftUserPageSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Dompdf\Dompdf;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierProductController extends Controller
{
    public static $page_title = 'Supplier Product Registration';
    public static $redirect_url = 'supplier-product';
    public static $menu_dtl_id = '184';
    //public static $menu_dtl_id = '157';
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
        $data['form_type'] = 'sup_prod_reg';
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblPurcSupProd::where('sup_prod_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcSupProd::with('sub_prod','supplier')->where('sup_prod_id',$id)->where(Utilities::currentBCB())->first();
                $data['document_code'] = $data['current']->sup_prod_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcSupProd',
                'code_field'        => 'sup_prod_code',
                'code_prefix'       => strtoupper('spr')
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->where(Utilities::currentBC())->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_entry_status',1)->where(Utilities::currentBC())->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_sup_prod',
            'col_id' => 'sup_prod_id',
            'col_code' => 'sup_prod_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.supplier_product.form', compact('data'));
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {
        //dd($request->toArray());
        $data = [];
        if(isset($request->pd)){
            foreach($request->pd as $pd){
                if(!empty($pd['barcode'])){
                    $exits = TblPurcProductBarcode::where('product_barcode_barcode',$pd['barcode'])->exists();
                    if (!$exits) {
                        return $this->jsonErrorResponse($data, trans('message.not_barcode'), 200);
                    }
                }
            }
        }else{
            return $this->jsonErrorResponse($data, 'Fill The Grid', 200);
        }
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required',
            'supplier_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'notes' => 'nullable|max:255'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try {
            if(isset($id)){
                $sup_prod = TblPurcSupProd::where('sup_prod_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $sup_prod = new TblPurcSupProd();
                $sup_prod->sup_prod_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcSupProd',
                    'code_field'        => 'sup_prod_code',
                    'code_prefix'       => strtoupper('spr')
                ];
                $sup_prod->sup_prod_code = Utilities::documentCode($doc_data);
            }
            $form_id = $sup_prod->sup_prod_id;
            $sup_prod->sup_prod_type = 'SPR';
            $sup_prod->sup_prod_date = date('Y-m-d', strtotime($request->prod_reg_date));
            $sup_prod->supplier_id = $request->supplier_id;
            $sup_prod->currency_id = $request->currency_id;
            $sup_prod->sup_prod_exchange_rate = $request->exchange_rate;
            $sup_prod->sup_prod_remarks = $request->notes;
            $sup_prod->business_id = auth()->user()->business_id;
            $sup_prod->company_id = auth()->user()->company_id;
            $sup_prod->branch_id = auth()->user()->branch_id;
            $sup_prod->sup_prod_user_id = auth()->user()->id;
            $sup_prod->save();

            if(isset($id)){
                TblPurcSupProdDtl::where('sup_prod_id',$id)->where(Utilities::currentBCB())->delete();   
            }
            if(isset($request->pd)){
                $sr_no = 1;
                foreach($request->pd as $pd){
                    $dtl = new TblPurcSupProdDtl();
                    if(isset($id) && isset($pd['sup_prod_dtl_id'])){
                        $dtl->sup_prod_dtl_id = $pd['sup_prod_dtl_id'];
                        $dtl->sup_prod_id = $id;
                    }else{
                        $dtl->sup_prod_dtl_id = utilities::uuid();
                        $dtl->sup_prod_id = $sup_prod->sup_prod_id;
                    }
                    $dtl->sup_prod_dtl_sr_no = $sr_no++;
                    $dtl->sup_prod_supplier_id = $sup_prod->supplier_id;
                    $dtl->sup_prod_dtl_barcode = isset($pd['pd_barcode'])?$pd['pd_barcode']:"";
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = isset($pd['product_barcode_id'])?$pd['product_barcode_id']:"";
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->sup_prod_sup_barcode = $pd['supplier_barcode'];
                    $dtl->sup_prod_sup_description = $pd['supplier_description'];
                    $dtl->sup_prod_sup_uom = $pd['supplier_uom'];
                    $dtl->sup_prod_sup_pack = $pd['supplier_packing'];
                    $dtl->sup_prod_sup_category = $pd['supplier_category'];
                    $dtl->sup_prod_sup_brand = $pd['supplier_brand'];
                    $dtl->sup_prod_sup_pur_rate = $this->addNo($pd['supplier_pur_price']);
                    $dtl->sup_prod_sup_sale_rate = $this->addNo($pd['supplier_sale_price']);
                    $dtl->sup_prod_sup_vat_per = $this->addNo($pd['supplier_vat_perc']);
                    $dtl->sup_prod_sup_hs_code = $pd['supplier_hs_code'];
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->sup_prod_dtl_user_id = auth()->user()->id;
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

    public function print($id,$type = null)
    {
        $data['title'] = 'Supplier Product Registration';
        $data['type'] = $type;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $data['print_link'] = '/supplier-product/print/'.$id.'/pdf';
        if(isset($id)){
            if(TblPurcSupProd::where('sup_prod_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblPurcSupProd::with('sub_prod','supplier')->where('sup_prod_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        if(isset($type) && $type=='pdf'){
            $view = view('prints.sup_prod_print', compact('data'))->render();
            //dd($view);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 100);
            $options->set('isPhpEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $options->setDefaultFont('roboto');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view,'UTF-8');
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            return $dompdf->stream();
        }else{
            return view('prints.sup_prod_print', compact('data'));
        }
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
            $sup_prod = TblPurcSupProd::where('sup_prod_id',$id)->where(Utilities::currentBCB())->first();
            $sup_prod->sub_prod()->delete();
            $sup_prod->delete();
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
