<?php

namespace App\Http\Controllers\Setting;

use Exception;
use App\Library\Utilities;
use App\Models\TblDefiArea;
use App\Models\TblDefiCity;
use Illuminate\Http\Request;
use App\Models\TblDefiDelTypes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblServDeliveryCharges;
use Illuminate\Database\QueryException;
use App\Models\TblServDeliveryChargesDtl;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class DeliveryChargesController extends Controller
{
    public static $page_title = 'Delivery Charges';
    public static $redirect_url = 'delivery-charges';
    public static $menu_dtl_id = '211';

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
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            
            if(TblServDeliveryCharges::where('delivery_charges_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblServDeliveryCharges::with('dtls')->where('delivery_charges_id',$id)->first();
                $data['areas'] = TblDefiArea::where('city_id' , $data['current']->city_id)->orderBy('city_id')->get();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['delivery_types'] = TblDefiDelTypes::where('delivery_type_entry_status' , 1)->get();
        $data['branch'] = Utilities::getAllBranches();
        $data['cities'] = TblDefiCity::get();
        return view('setting.delivery_charges.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id = null)
    {   
        $data = [];
        $validator = Validator::make($request->all(), [
            'delivery_type' => 'required',
            'branch'        => 'required',
            'city'          => 'required',
            'area'          => 'required',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        
        DB::beginTransaction();  
        try{
            if(isset($id)){
                $delCharges = TblServDeliveryCharges::where('delivery_charges_id',$id)->first();
            }else{
                $delCharges = new TblServDeliveryCharges();
                $delCharges->delivery_charges_id = Utilities::uuid();
            }
            
            $delCharges->delivery_type = $request->delivery_type;
            $delCharges->charges_branch = $request->branch;
            $delCharges->city_id = $request->city;
            $delCharges->area_id = $request->area;
            $delCharges->business_id = auth()->user()->business_id;
            $delCharges->company_id = auth()->user()->company_id;
            $delCharges->branch_id = auth()->user()->branch_id;
            $delCharges->delivery_charges_user_id = auth()->user()->id;
            $delCharges->save();

            $form_id = $delCharges->delivery_charges_id;

            $del_Dtls = TblServDeliveryChargesDtl::where('delivery_charges_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblServDeliveryChargesDtl::where('delivery_charges_dtl_id',$del_Dtl->delivery_charges_dtl_id)->delete();
            }
            if(isset($request->delivery_charges_data)){
                $sr_no = 0;
                foreach ($request->delivery_charges_data as $pd) {
                    if(isset($pd['min_value']) && $pd['min_value'] >= 0 && isset($pd['max_value']) && $pd['max_value'] > 0){
                        if((isset($pd['perc_charges']) && $pd['perc_charges'] > 0) || (isset($pd['charges']) && $pd['charges'] > 0)){
                            $dtl = new TblServDeliveryChargesDtl();
                            $dtl->delivery_charges_dtl_id = Utilities::uuid();
                            $dtl->delivery_charges_id = $delCharges->delivery_charges_id;
                            $dtl->min_value = $pd['min_value'];
                            $dtl->max_value = $pd['max_value'];
                            $dtl->perc_charges = $pd['perc_charges'];
                            $dtl->charges = $pd['charges'];
                            $dtl->sr_no = $sr_no++;
                            $dtl->business_id = auth()->user()->business_id;
                            $dtl->company_id = auth()->user()->company_id;
                            $dtl->branch_id = auth()->user()->branch_id;
                            $dtl->delivery_charges_dtl_user_id = auth()->user()->id;
                            $dtl->save();
                        }
                    }
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


    public function getDeliveryChargesDtlData(Request $request){
        $data = [];
        $filter = [
            'delivery_type' => $request->delivery_type,
            'charges_branch' => $request->branch_id,
            'city_id'       => $request->city_id,
            'area_id'   => $request->area_id
        ];

        $search = TblServDeliveryCharges::where($filter);
        if($search->count() > 0){
            $existed = $search->first('delivery_charges_id');
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$existed->delivery_charges_id;
            return $this->jsonSuccessResponse($data, 'Redirecting...', 200);
        }else{
            $data['new'] = TRUE;
            return $this->jsonSuccessResponse($data, 'Data Successfully Loaded', 200);
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
            $charges = TblServDeliveryCharges::where('delivery_charges_id',$id)->first();
            $charges->dtls()->delete();
            $charges->delete();
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
