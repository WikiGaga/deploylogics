<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSaleBarcodePriceTag;
use App\Models\TblSaleBarcodePriceTagDtl;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BarcodePriceTagController extends Controller
{
    public static $page_title = 'Barcode Price Tag';
    public static $redirect_url = 'barcode-price-tag';
    public static $menu_dtl_id = '132';
    //public static $menu_dtl_id = '146';
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
        $data['form_type'] = 'barcode_price_tag';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSaleBarcodePriceTag::where('barcode_price_tag_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleBarcodePriceTag::with('barcode_price_tag_dtl')->where('barcode_price_tag_id',$id)->where(Utilities::currentBCB())->first();
                $data['document_code'] = $data['current']->barcode_price_tag_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblSaleBarcodePriceTag',
                'code_field'        => 'barcode_price_tag_code',
                'code_prefix'       => strtoupper('bptag')
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_barcode_price_tag',
            'col_id' => 'barcode_price_tag_id',
            'col_code' => 'barcode_price_tag_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('sales.barcode_price_tag.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
//            'barcode_price_tag_name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        if(!isset($request->pd)){
            return $this->returnjsonerror(trans('message.fill_the_grid'),200);
        }
        DB::beginTransaction();
        try {
            if(isset($id)){
                $BarcodePriceTag = TblSaleBarcodePriceTag::where('barcode_price_tag_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $BarcodePriceTag = new TblSaleBarcodePriceTag();
                $BarcodePriceTag->barcode_price_tag_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblSaleBarcodePriceTag',
                    'code_field'        => 'barcode_price_tag_code',
                    'code_prefix'       => strtoupper('bptag')
                ];
                $BarcodePriceTag->barcode_price_tag_code = Utilities::documentCode($doc_data);
            }
            $form_id = $BarcodePriceTag->barcode_price_tag_id;
            $BarcodePriceTag->barcode_price_tag_name = $request->barcode_price_tag_name;
            $BarcodePriceTag->business_id = auth()->user()->business_id;
            $BarcodePriceTag->company_id = auth()->user()->company_id;
            $BarcodePriceTag->branch_id = auth()->user()->branch_id;
            $BarcodePriceTag->barcode_price_tag_user_id = auth()->user()->id;
            $BarcodePriceTag->barcode_price_tag_status = 1;
            $BarcodePriceTag->save();

            if(isset($request->pd)){
                TblSaleBarcodePriceTagDtl::where('barcode_price_tag_id',$id)->delete();
                foreach($request->pd as $pd){
                    $dtl = new TblSaleBarcodePriceTagDtl();
                    $dtl->barcode_price_tag_dtl_id =  Utilities::uuid();
                    $dtl->barcode_price_tag_id =  $BarcodePriceTag->barcode_price_tag_id;
                    $dtl->uom_id = isset($pd['uom_id'])?$pd['uom_id']:'';
                    $dtl->product_id =  $pd['product_id'];
                    $dtl->product_barcode_id =  $pd['product_barcode_id'];
                    $dtl->product_barcode_barcode =  $pd['pd_barcode'];
                    $dtl->product_name =  $pd['product_name'];
                    $dtl->barcode_price_tag_dtl_rate =  $pd['rate'];
                    $dtl->barcode_price_tag_dtl_qty =  $pd['quantity'];
                    $dtl->barcode_price_tag_vat_per =  $pd['vat_perc'];
                    $dtl->barcode_price_tag_vat_amount =  $pd['vat_amount'];
                    $dtl->barcode_price_tag_total_amount =  $pd['gross_amount'];
                    $dtl->barcode_price_tag_dtl_packing_date =  isset($pd['packing_date'])?date('Y-m-d', strtotime($pd['packing_date'])):"";
                    $dtl->barcode_price_tag_dtl_expiry_date =  isset($pd['expiry_date'])?date('Y-m-d', strtotime($pd['expiry_date'])):"";
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
            //$data['print_url'] = route( 'prints.barcode_price_tag_print.blade',[$BarcodePriceTag->barcode_price_tag_id] );
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

    public function print($id){
        $data['title'] = 'Barcode Print Tag';
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblSaleBarcodePriceTag::where('barcode_price_tag_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblSaleBarcodePriceTag::with('barcode_price_tag_dtl')->where('barcode_price_tag_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        //dd($data['current']->toArray());
        return view('prints.barcode_price_tag_print',compact('data'));
    }
}
