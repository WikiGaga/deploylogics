<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblDefiIncentiveType;
use App\Models\TblDefiMembershipType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MembershipTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Membership Type';
    public static $redirect_url = 'membership-type';
    public static $menu_dtl_id = '255';

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
            if(TblDefiMembershipType::where('membership_type_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblDefiMembershipType::where('membership_type_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['incentives'] = TblDefiIncentiveType::where('incentive_type_entry_status',1)->get();
        // dd($data['incentives']->toArray());
        $data['membership_type'] = TblDefiMembershipType::where('membership_type_entry_status',1)->get();
        return view('setting.membership_type.form',compact('data'));
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
            'name' => 'required|max:100',
            'short_name' => 'required|max:100',
            'qualification_amount' => 'required|max:100',
            'incentive_type' => 'required|max:100',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(!isset($id)){
            if(TblDefiMembershipType::where('membership_type_name','LIKE',$request->name)->where('business_id', auth()->user()->business_id)->exists()){
                return $this->jsonErrorResponse($data, trans('message.duplicate_record'), 422);
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $membership_type = TblDefiMembershipType::where('membership_type_id',$id)->first();
            }else{
                $membership_type = new TblDefiMembershipType();
                $membership_type->membership_type_id = Utilities::uuid();
            }
            $form_id = $membership_type->membership_type_id;
            $membership_type->membership_type_name = $request->name;
            $membership_type->membership_type_short_name = $request->short_name;
            $membership_type->membership_type_qualification_amount = $request->qualification_amount;
            $membership_type->membership_type_invoice_number = $request->invoice_number;
            $membership_type->membership_type_incentive_type_id = $request->incentive_type;
            $membership_type->membership_type_discount = $request->discount;
            $membership_type->membership_type_min_amount = $request->min_amount;
            $membership_type->membership_type_point_value = $request->point_value;
            $membership_type->membership_type_monthly_discount_limit = $request->discount_limit;
            $membership_type->membership_type_monthly_sale_limit = $request->sale_limit;
            $membership_type->membership_type_entry_status = isset($request->membership_type_entry_status)?"1":"0";
            $membership_type->business_id = auth()->user()->business_id;
            $membership_type->company_id = auth()->user()->company_id;
            $membership_type->branch_id = auth()->user()->branch_id;
            $membership_type->membership_type_user_id = auth()->user()->id;
            $membership_type->save();

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
        //
    }
}
