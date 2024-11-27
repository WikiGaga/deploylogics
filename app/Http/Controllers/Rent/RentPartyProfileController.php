<?php

namespace App\Http\Controllers\Rent;

use Exception;
use Validator;
use App\Models\TblAccCoa;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblSoftBranch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Models\Rent\TblRentPartyProfile;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RentPartyProfileController extends Controller
{
    public static $page_title = 'Rent Party Profile';
    public static $redirect_url = 'rent-party-profile';
    public static $menu_dtl_id = '239';

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
    public function create(Request $request, $id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblRentPartyProfile::where('party_profile_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] =  TblRentPartyProfile::where(Utilities::currentBC())->where('party_profile_id',$id)->first();
                
                $data['party_code'] = $data['current']->party_code;
            }else{
                abort('404');
            }
        }else{
            // Check SubDomain Of the Project
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['party_code'] = $this->documentCode(TblRentPartyProfile::max('party_code'),'RP');
        }

        $arr = [
            'biz_type' => 'branch',
            'code' => $data['party_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_rent_party_profile',
            'col_id' => 'party_profile_id',
            'col_code' => 'party_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('rent.party_profile.form',compact('data'));
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
            'rent_party_name'       => 'required|max:100',
            'parent_account_id'     => 'required'
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $acc_code = TblAccCoa::where('chart_account_id',$request->parent_account_id)->first();
            
            $level_no = 4;
            $parent_account_code = $acc_code->chart_code;
            $business_id = auth()->user()->business_id;
            $company_id = auth()->user()->company_id;
            $branch_id = auth()->user()->branch_id;
            $user_id = auth()->user()->id;
            $chart_name = $request->rent_party_name;

            if(isset($id)){
                $partyProfile =TblRentPartyProfile::where('party_profile_id',$id)->where(Utilities::currentBC())->first();
                $acc_id = $partyProfile->chart_account_id;
                $partyProfile->updated_at = Carbon::now();
                $this->proPurcChartUpdate($business_id,$company_id,$branch_id,$chart_name,$acc_id);
            }else{
                $chart_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                $partyProfile = new TblRentPartyProfile();
                $partyProfile->party_profile_id = Utilities::uuid();
                $partyProfile->party_code = $this->documentCode(TblRentPartyProfile::max('party_code'),'RP');
                $partyProfile->chart_account_id = $chart_account_id;
                $partyProfile->parent_account_id = $request->parent_account_id;
                $partyProfile->rent_type = (isset($request->parent_account_id) && $request->parent_account_id == 269) ? 'Rent Receive' : 'Rent Pay';
            }
            $form_id = $partyProfile->party_profile_id;
            $partyProfile->party_name = $request->rent_party_name;
            $partyProfile->rent_party_status = isset($request->rent_party_status)?"1":"0";
            $partyProfile->party_cr_no = $request->rent_party_cr_no ?? '';
            $partyProfile->party_telephone = $request->rent_party_phone ?? '';
            $partyProfile->party_labor_card_no = $request->rent_party_labour_card_no ?? "";
            $partyProfile->party_passport_no = $request->rent_party_passport ?? "";
            $partyProfile->party_sponsor_name = $request->rent_party_sponsor ?? "";
            $partyProfile->party_nationality = $request->rent_party_nationality ?? "";
            $partyProfile->party_po_code = $request->rent_party_postal ?? "";
            $partyProfile->party_po_box = $request->rent_party_po_box ?? "";
            $partyProfile->party_address = $request->rent_party_address ?? "";
            $partyProfile->business_id = auth()->user()->business_id;
            $partyProfile->company_id = auth()->user()->company_id;
            $partyProfile->branch_id = auth()->user()->branch_id;
            $partyProfile->user_id = auth()->user()->id;
            $partyProfile->save();

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

    public function getRentPartyProfile(Request $request , $id = null){
        $data = [];

        if(!isset($id)){
            return $this->jsonErrorResponse([] , 'Something went wrong!' , 500);
        }
        $data['partyProfile'] = TblRentPartyProfile::where('party_profile_id' , $id)->first();
        return $this->jsonSuccessResponse($data , 'Party Data Loaded!' , 200);
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
        return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
    }
}
