<?php

namespace App\Http\Controllers\Sales;

use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblSoftBranch;
use App\Models\TblSaleCoupons;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblSaleCouponsDtl;
use App\Models\TblSaleCouponsDtlDtl;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SaleCouponsController extends Controller
{
    public static $page_title = 'Coupons';
    public static $redirect_url = 'coupons';
    public static $menu_dtl_id = '229';

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
            if(TblSaleCoupons::where('coupon_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] =  TblSaleCoupons::with('coupon_dtl','benicifery_dtls','customer')->where(Utilities::currentBC())->where('coupon_id',$id)->first();
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
                $data['coupon_code'] = $data['current']->coupon_code;
            }else{
                abort('404');
            }
        }else{         
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblSaleCoupons',
                'code_field'        => 'coupon_code',
                'code_prefix'       => strtoupper('cop')
            ];

            $data['coupon_code'] = Utilities::documentCode($doc_data);
        }
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['coupon_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_coupons',
            'col_id' => 'coupon_id',
            'col_code' => 'coupon_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        $data['branches'] = TblSoftBranch::where('branch_active_status' , 1)->get();
        $data['current_branch'] = Auth()->user()->branch_id;

        return view('sales.coupons.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id = null)
    {
        // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|max:100',
            'customer_id' => 'required',
            'donater_email' => 'required',
            'donater_phone' => 'required',
            'donater_phone' => 'required',
            'donater_national_id' => 'required',
            'donater_budget' => 'required',
            'donater_address' => 'required',
            'valid_branches' => 'required|not_in:0',
            
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if($request->remaning_budget < 0){
            return $this->jsonErrorResponse($data, 'Budget Limit Not Validated.', 200);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $coupon = TblSaleCoupons::where('coupon_id' , $id)->where(Utilities::currentBC())->first();
            }else{
                $coupon = new TblSaleCoupons();
                $coupon->coupon_id = Utilities::uuid();
                $coupon->coupon_code = $this->documentCode(TblSaleCoupons::where(Utilities::currentBCB())->max('coupon_code'),'COP');
            }
            $form_id = $coupon->coupon_id;
            $coupon->customer_name = $request->customer_name; 
            $coupon->customer_id = $request->customer_id; 
            $coupon->donater_email = $request->donater_email; 
            $coupon->donater_phone = $request->donater_phone; 
            $coupon->donater_national_id = $request->donater_national_id; 
            $coupon->donater_address = $request->donater_address; 
            $coupon->show_donater_name = isset($request->show_donater_name) ? 1 : 0; 
            $coupon->coupon_date = date('Y-m-d', strtotime($request->coupon_sale_date));
            $coupon->coupon_valid_branches = isset($request->valid_branches) ? implode("," , $request->valid_branches) : '';
            $coupon->branch_id = Auth()->user()->branch_id;
            $coupon->company_id = Auth()->user()->company_id;
            $coupon->business_id = Auth()->user()->business_id;
            $coupon->donater_budget = $request->donater_budget;
            $coupon->save();

            if(isset($id)){
                $del_dtl = TblSaleCouponsDtl::where('coupon_id',$id)->get();
                TblSaleCouponsDtlDtl::where('coupon_id' , $id)->delete();
                foreach ($del_dtl as $del_row){
                    TblSaleCouponsDtl::where('coupon_id',$del_row->coupon_id)->delete();
                }
            }

            $sr_no = 0;

            if(isset($request->coupon_data)){
                foreach ($request->coupon_data as $couponData) {
                    $coupon_dtl = new TblSaleCouponsDtl();
                    $coupon_dtl->coupon_dtl_id = Utilities::uuid();
                    $coupon_dtl->coupon_id = $coupon->coupon_id;
                    $coupon_dtl->coupon_qty = $couponData['coupon_qty'];
                    $coupon_dtl->coupon_value = $couponData['coupon_value'];
                    $coupon_dtl->validity_date = $couponData['coupon_validity'];
                    $coupon_dtl->valid_branches = 0;
                    $coupon_dtl->product_groups = 0;
                    $coupon_dtl->save();

                    if(isset($couponData['coupon_dtl'])){
                        foreach ($couponData['coupon_dtl'] as $value) {
                            $benificeryDtl = new TblSaleCouponsDtlDtl();
                            $benificeryDtl->coupon_dtl_dtl_id = Utilities::uuid();
                            $benificeryDtl->coupon_dtl_id = $coupon_dtl->coupon_dtl_id;
                            $benificeryDtl->coupon_id = $coupon->coupon_id;
                            $benificeryDtl->coupon_identifier = $value[0]; 
                            $benificeryDtl->max_identifier = explode("-" , $value[0])[1]; 
                            $benificeryDtl->coupon_benificery = $value[1]; 
                            $benificeryDtl->coupon_value = $value[2];
                            $benificeryDtl->sr_no = $sr_no++;
                            $benificeryDtl->save(); 
                        }
                    }
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
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            $data = array_merge($data, Utilities::returnJsonNewForm());
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function print($id)
    {
        $data['title'] = 'Sales Coupons';
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblSaleCoupons::where('coupon_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblSaleCoupons::with('coupon_dtl','benicifery_dtls','customer')->where(Utilities::currentBCB())->where('coupon_id',$id)->first();
            }else{
                abort('404');
            }
        }

        return view('prints.coupons.print',compact('data'));
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

    public function getLatestCode(){
        $data = [];
        $data['lastNumber'] = TblSaleCouponsDtlDtl::max('max_identifier');
        return $this->jsonSuccessResponse($data, '', 200);
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
            
            $coupon = TblSaleCoupons::where('coupon_id',$id)->where(Utilities::currentBC())->first();
            $coupon->coupon_dtl()->delete();
            $coupon->benicifery_dtls()->delete();
            $coupon->delete();

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
