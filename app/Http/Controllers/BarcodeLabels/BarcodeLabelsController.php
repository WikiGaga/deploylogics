<?php

namespace App\Http\Controllers\BarcodeLabels;

use App\Models\TblSoftBranch;
use Exception;
use App\Library\Utilities;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// db and Validator
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblPurcProductBarcode;
use Illuminate\Database\QueryException;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\Defi\TblDefiBarcodeLabels;
use App\Models\Defi\TblDefiBarcodeLabelsDtl;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblPurcProductBarcodePurchRate;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class BarcodeLabelsController extends Controller
{
    public static $main_menu = 'barcode-labels/';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type,$id = null)
    {
        /*
         * Sales Price = btsp
         * Shelf Price = btshp
         * Multi Barcode Labels = mbtl
         * */
        $data['page_data'] = [];
        switch ($type) {
            case 'barcode-labels-sales-price':
            {
                $data['page_data']['title'] = 'Barcode Labels Sales Price';
                $formUrl = 'sales_price';
                $data['barcode_labels_type'] = 'btsp';
                $data['barcode_labels_menu_id'] = 132;
                $data['page_data']['create'] = '/'.self::$main_menu.$type.$this->prefixCreatePage;
                break;
            }
            case 'barcode-labels-shelf-price':
            {
                $data['page_data']['title'] = 'Barcode Labels Shelf Price';
                $formUrl = 'shelf_price';
                $data['barcode_labels_type'] = 'btshp';
                $data['barcode_labels_menu_id'] = 139;
                $data['page_data']['create'] = '/'.self::$main_menu.$type.$this->prefixCreatePage;
                break;
            }
            case 'multi-barcode-labels':
            {
                $data['page_data']['title'] = 'Multi Barcode Labels';
                // $formUrl = 'shelf_price';
                $data['barcode_labels_type'] = 'mbtl';
                $data['barcode_labels_menu_id'] = 281;
                $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();
                $data['page_data']['create'] = '/'.self::$main_menu.$type.$this->prefixCreatePage;
                break;
            }
        }
        $data['form_type'] = $type;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$main_menu.$type;
        if(isset($id)){
            if(TblDefiBarcodeLabels::where('barcode_labels_id','LIKE',$id)->exists()){
                $data['permission'] = $data['barcode_labels_menu_id'].'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblDefiBarcodeLabels::with('dtl')->where('barcode_labels_id',$id)->first();
                $data['page_data']['print'] = '/'.self::$main_menu.$type.'/print/price/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = $data['barcode_labels_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'Defi\TblDefiBarcodeLabels',
                'code_field'        => 'barcode_labels_code',
                'code_prefix'       => strtoupper($data['barcode_labels_type']),
                'code_type_field'   => 'barcode_labels_type',
                'code_type'         => $data['barcode_labels_type']

            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        //dd($data['current']->toArray());
        if ($type == 'multi-barcode-labels') {
            $data['page_data']['action'] = 'Generate';
            return view('barcode_labels.dynamic_barcode.form',compact('data'));
        } else {
            return view('barcode_labels.'.$formUrl.'.form',compact('data'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$type, $id = null)
    {
        switch ($type) {
            case 'barcode-labels-sales-price':
            {
                $formUrl = 'sales_price';
                break;
            }
            case 'barcode-labels-shelf-price':
            {
                $formUrl = 'shelf_price';
                break;
            }
            case 'multi-barcode-labels':
            {
                // $formUrl = 'shelf_price';
                break;
            }
        }
        $data = [];
        $validator = Validator::make($request->all(), [
           // 'barcode_labels_name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(!isset($request->pd) || count($request->pd) == 0){
            return $this->returnjsonerror(trans('message.fill_the_grid'),200);
        }
        DB::beginTransaction();
        try{
            $formType = $request->barcode_labels_type;
            if(isset($id)){
                $BarcodePriceTag = TblDefiBarcodeLabels::where('barcode_labels_id',$id)->first();
            }else{
                $BarcodePriceTag = new TblDefiBarcodeLabels();
                $BarcodePriceTag->barcode_labels_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'Defi\TblDefiBarcodeLabels',
                    'code_field'        => 'barcode_labels_code',
                    'code_prefix'       => strtoupper($formType),
                    'code_type_field'   => 'barcode_labels_type',
                    'code_type'         => $formType

                ];
                $BarcodePriceTag->barcode_labels_code = Utilities::documentCode($doc_data);
            }
            $form_id = $BarcodePriceTag->barcode_labels_id;
            $BarcodePriceTag->barcode_design = $request->barcode_design;
            $BarcodePriceTag->supplier_name = $request->supplier_name;
            $BarcodePriceTag->best_before = isset($request->best_before)?1:0;
            $no_of_days = $request->no_of_days;
            $BarcodePriceTag->no_of_days = $no_of_days;
            $Date = date('d-m-Y');
            if(isset($request->best_before) && empty($request->no_of_days)){
                $no_of_days = 1;
            }
            //$BarcodePriceTag->sales_date = date('Y-m-d', strtotime($Date. ' + '.$no_of_days.' days'));
            $BarcodePriceTag->sales_date = date('Y-m-d', strtotime($request->sales_date));
            $BarcodePriceTag->mfg_date = date('Y-m-d', strtotime($request->mfg_date));
            $BarcodePriceTag->barcode_labels_name = $request->barcode_labels_name;
            $BarcodePriceTag->barcode_labels_type = $request->barcode_labels_type;
            $BarcodePriceTag->business_id = auth()->user()->business_id;
            $BarcodePriceTag->company_id = auth()->user()->company_id;
            $BarcodePriceTag->branch_id = auth()->user()->branch_id;
            $BarcodePriceTag->barcode_labels_user_id = auth()->user()->id;
            $BarcodePriceTag->barcode_labels_status = 1;
            $BarcodePriceTag->save();

            if(isset($request->pd)){
                $del_Dtls = TblDefiBarcodeLabelsDtl::where('barcode_labels_id',$id)->get();
                if(!empty($del_Dtls)){
                    TblDefiBarcodeLabelsDtl::where('barcode_labels_id',$id)->delete();
                }
                foreach($request->pd as $pd){
                    $dtl = new TblDefiBarcodeLabelsDtl();
                    $dtl->barcode_labels_dtl_id =  Utilities::uuid();
                    $dtl->barcode_labels_id =  $BarcodePriceTag->barcode_labels_id;
                    $dtl->product_id =  $pd['product_id'];
                    $dtl->product_barcode_id =  $pd['product_barcode_id'];
                    $dtl->product_barcode_barcode =  $pd['pd_barcode'];
                    $dtl->product_name =  $pd['product_name'];
                    $dtl->barcode_labels_dtl_rate =  Utilities::NumFormat($pd['rate']);
                    $dtl->barcode_labels_dtl_qty =  !empty($pd['quantity'])?Utilities::NumFormat($pd['quantity']):1;
                    $dtl->barcode_labels_dtl_amount =  Utilities::NumFormat($pd['amount']);
                    if ($type=='multi-barcode-labels') {
                        $dtl->barcode_labels_dtl_weight =  Utilities::NumFormat($pd['weight']);
                        $dtl->group_item_parent_id =  $pd['first_level_category_id'];
                        $dtl->group_item_id =  $pd['last_level_category_id'];
                        $dtl->group_item_parent_name =  $pd['first_level_category'];
                        $dtl->group_item_name =  $pd['last_level_category'];
                    }
                    else{
                        $dtl->product_arabic_name =  $pd['arabic_name'];
                        $dtl->barcode_labels_dtl_vat =  $pd['vat_amount'];
                        $dtl->barcode_labels_dtl_vat_per =  $pd['vat_perc'];
                        $dtl->barcode_labels_dtl_disc_per =  $pd['dis_perc'];
                        $dtl->barcode_labels_dtl_disc_amt =  $pd['dis_amount'];
                        $dtl->barcode_labels_dtl_grs_amt =  $pd['gross_amount'];
                    }
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
            $data['redirect'] = $this->prefixIndexPage.self::$main_menu.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$main_menu.$type.$this->prefixCreatePage.'/'.$form_id;
            $data['print_url'] = route( 'barcode_labels_print',[$type,$BarcodePriceTag->barcode_labels_id] );
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
        //
    }

    public function priceCheck(){
        return view('barcode_labels.price_checker');
    }

    public function getBarcodeCheckPrice(Request $request , $barcode = null)
    {
        $data = [];
        if(!isset($barcode)){
            return $this->jsonErrorResponse([] , 'Barcode is Required!' , 404);
        }else{
            if(TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$barcode)->exists()) {
                $code = $barcode;
            }else{
                $weight_prod = substr($barcode, 0, 7);
                if(TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$weight_prod)->where('product_barcode_weight_apply',1)->exists()){
                    $code = $weight_prod;
                }
            }

            if(!isset($code) && empty($code)){
                return  $this->jsonErrorResponse([] , 'Product Not Found!' , 404);
            }

            if(isset($code) && !empty($code)){
                // common
                $data['barcode_type'] = 'common';
                $data['code'] = $code;
                $data['codeVal'] = $barcode;
                $data['current_user_branch_id'] = 1;
                $data['current_product'] = TblPurcProductBarcode::with('product','barcode_dtl','uom','sale_rate')->where('product_barcode_barcode',$code)->first();
                $data['rate'] = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$data['current_product']['product_barcode_id'])->where('branch_id',1)->where('product_category_id',2)->first();
                $data['purc_rate'] = TblPurcProductBarcodePurchRate::where('product_barcode_barcode', $code)
                    ->where('product_barcode_id',$data['current_product']->product_barcode_id)
                    ->where('branch_id',1)->first();

                $vat = TblPurcProductBarcodeDtl::where('product_barcode_id',$data['current_product']['product_barcode_id'])
                    ->where('branch_id',1)->first();
                if(!empty($vat)){
                    $data['vat'] = $vat;
                }else{
                    $data['vat'] = "";
                }
            }

            return $this->jsonSuccessResponse($data , 'Item Found' , 200);
        }

    }

    public function print($type,$id){
        switch ($type) {
            case 'barcode-labels-sales-price':
            {
                $data['title'] = 'Barcode Labels Sales Price';
                $data['barcode_labels_type'] = 'btsp';
                $data['barcode_labels_menu_id'] = 132;
                $formUrl = 'sales_price';
                break;
            }
            case 'barcode-labels-shelf-price':
            {
                $data['title'] = 'Barcode Labels Shelf Price';
                $data['barcode_labels_type'] = 'btshp';
                $data['barcode_labels_menu_id'] = 139;
                $formUrl = 'shelf_price';
                break;
            }
            case 'multi-barcode-labels':
            {
                $data['title'] = 'Multi Barcode Labels';
                $data['barcode_labels_type'] = 'mbtl';
                $data['barcode_labels_menu_id'] = 281;
                // $formUrl = 'dynamic_print';
                break;
            }
        }
        $data['permission'] = $data['barcode_labels_menu_id'].'-print';
        if(isset($id)){
            if(TblDefiBarcodeLabels::where('barcode_labels_id','LIKE',$id)->exists()){
                $data['current'] = TblDefiBarcodeLabels::with('dtl')->where('barcode_labels_id',$id)->first();
            }else{
                abort('404');
            }
        }
        // if($type2 == 'price'){
        //     $newPrintDesign = [1,2,7,5,3];
        //     if(in_array(auth()->user()->branch_id,$newPrintDesign)){
        //         return view('prints.barcode_labels.'.$formUrl.'.print_zd220',compact('data'));
        //     }else{
        //         dd($type);
        //         if ($type=='multi-barcode-labels') {
        //             return view('prints.barcode_labels.dynamic_print',compact('data'));
        //         }else{
        //             return view('prints.barcode_labels.'.$formUrl.'.print',compact('data'));
        //         }
        //     }
        // }
        if ($type == 'multi-barcode-labels') {
            return view('prints.barcode_labels.dynamic_print',compact('data'));
        }
        else{
            return view('prints.barcode_labels.'.$formUrl.'.print2',compact('data'));
        }
    }


    public function dynamicBarcodeLabels(Request $request)
    {

        $data['page_data'] = [];
        $data['page_data']['title'] = 'Generate Barcode Labels';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Generate';
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();
        return view('barcode_labels.dynamic_barcode.form',compact('data'));
    }
    public function storeDynamicBarcodeLabels(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'barcode_design' => 'required|not_in:0'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(!isset($request->pd) || count($request->pd) == 0){
            return $this->returnjsonerror(trans('message.fill_the_grid'),200);
        }
        DB::beginTransaction();
        try{
            $formType = 'dynamic_barcode_labels';
            $labels_type = 'dbp';
            if(isset($id)){
                $BarcodePriceTag = TblDefiBarcodeLabels::where('barcode_labels_id',$id)->first();
            }else{
                $BarcodePriceTag = new TblDefiBarcodeLabels();
                $BarcodePriceTag->barcode_labels_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'Defi\TblDefiBarcodeLabels',
                    'code_field'        => 'barcode_labels_code',
                    'code_prefix'       => strtoupper($labels_type),
                    'code_type_field'   => 'barcode_labels_type',
                    'code_type'         => $labels_type

                ];
                $BarcodePriceTag->barcode_labels_code = Utilities::documentCode($doc_data);
            }
            $form_id = $BarcodePriceTag->barcode_labels_id;
            $BarcodePriceTag->barcode_design = $request->barcode_design;
            $BarcodePriceTag->supplier_name = $request->supplier_name;

            $BarcodePriceTag->best_before = isset($request->best_before)?1:0;
            $no_of_days = $request->no_of_days;
            $BarcodePriceTag->no_of_days = $no_of_days;
            $Date = date('d-m-Y');
            if(isset($request->best_before) && empty($request->no_of_days)){
                $no_of_days = 1;
            }
            $BarcodePriceTag->sales_date = date('Y-m-d', strtotime($request->sales_date));
            $BarcodePriceTag->mfg_date = date('Y-m-d', strtotime($request->mfg_date));

            $BarcodePriceTag->barcode_labels_type = $labels_type;
            $BarcodePriceTag->business_id = auth()->user()->business_id;
            $BarcodePriceTag->company_id = auth()->user()->company_id;
            $BarcodePriceTag->branch_id = auth()->user()->branch_id;
            $BarcodePriceTag->barcode_labels_user_id = auth()->user()->id;
            $BarcodePriceTag->barcode_labels_status = 1;
            $BarcodePriceTag->save();

            if(isset($request->pd)){
                TblDefiBarcodeLabelsDtl::where('barcode_labels_id',$form_id)->delete();
                foreach($request->pd as $pd){
                    $dtl = new TblDefiBarcodeLabelsDtl();
                    $dtl->barcode_labels_dtl_id =  Utilities::uuid();
                    $dtl->barcode_labels_id =  $BarcodePriceTag->barcode_labels_id;
                    $dtl->product_id =  $pd['product_id'];
                    $dtl->product_barcode_id =  $pd['product_barcode_id'];
                    $dtl->group_item_parent_id =  $pd['first_level_category_id'];
                    $dtl->group_item_id =  $pd['last_level_category_id'];
                    $dtl->product_barcode_barcode =  $pd['pd_barcode'];
                    $dtl->product_name =  $pd['product_name'];
                    $dtl->group_item_parent_name =  $pd['first_level_category'];
                    $dtl->group_item_name =  $pd['last_level_category'];
                    $dtl->barcode_labels_dtl_qty =  Utilities::NumFormat($pd['weight']);
                    $dtl->barcode_labels_dtl_rate =  Utilities::NumFormat($pd['rate']);
                    $dtl->barcode_labels_dtl_weight =  Utilities::NumFormat($pd['quantity']);
                    $dtl->barcode_labels_dtl_amount =  Utilities::NumFormat($pd['amount']);
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
           // $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            $data['print_url'] = route( 'dynamic_barcode_labels_print',$BarcodePriceTag->barcode_labels_id );
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function dynamicPrint($id){

       if(isset($id)){
            if(TblDefiBarcodeLabels::where('barcode_labels_id','LIKE',$id)->exists()){
                $data['current'] = TblDefiBarcodeLabels::with('dtl')->where('barcode_labels_id',$id)->first();
            }else{
                abort('404');
            }
        }
        return view('prints.barcode_labels.dynamic_print',compact('data'));
    }
}
