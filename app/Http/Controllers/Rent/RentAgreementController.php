<?php

namespace App\Http\Controllers\Rent;

use Session;
use Exception;
use Validator;
use App\Models\User;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblAccoVoucher;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Rent\TblRentAgreement;
use Illuminate\Database\QueryException;
use App\Models\Rent\TblRentAgreementDtl;
use App\Models\Rent\TblRentPartyProfile;
use App\Models\Rent\ViewRentRentLocation;
use Illuminate\Auth\Events\Validated;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RentAgreementController extends Controller
{
    public static $page_title = 'Rent Agreement Form';
    public static $redirect_url = 'rent-agreement';
    public static $menu_dtl_id = '240';
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
    public function create(Request $request , $id = null)
    {
        $data['form_type'] = 'rent_agreement';
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['menu_id'] = self::$menu_dtl_id;
        if(isset($id)){
            if(TblRentAgreement::where('rent_agreement_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] =  TblRentAgreement::with('dtls','firstParty','secondParty')->where(Utilities::currentBC())->where('rent_agreement_id',$id)->first();
                $data['totalAmount'] = (float)$data['current']->rent_agreement_total_rent;
                $data['paidAmount'] = TblRentAgreementDtl::where('rent_agreement_id',$id)->where('rent_agreement_dtl_status' , 1)->sum('rent_agreement_dtl_amount');
                $data['remaningAmount'] = ($data['totalAmount'] - $data['paidAmount']) + $data['current']->rent_opening_balance;
                // $data['differenceAmount'] = ($data['totalAmount'] - $data['paidAmount']) - $data['differenceAmount'];
                $data['agreement_code'] = $data['current']->agreement_code;

                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            // Check SubDomain Of the Project
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['agreement_code'] = $this->documentCode(TblRentAgreement::max('agreement_code'),'RAG');
        }

        $arr = [
            'biz_type' => 'branch',
            'code' => $data['agreement_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_rent_rent_agreement',
            'col_id' => 'rent_agreement_id',
            'col_code' => 'agreement_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        $data['rentalReceiveParties'] = TblRentPartyProfile::where('rent_party_status' , 1)->where('parent_account_id' , '19515121212300')->get();
        $data['rentalPayParties'] = TblRentPartyProfile::where('rent_party_status' , 1)->where('parent_account_id' , '269')->get();
        $data['rentalLocations'] = ViewRentRentLocation::where(Utilities::currentBC())->orderBy('rent_location_name_string')->get();
        return view('rent.rent_agreement.form',compact('data'));
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
        $totalRent = 0;
        $validator = Validator::make($request->all(), [
            'rent_agreement_date' => 'required|date',
            'rent_agreement_start_date' => 'required_if:form_case,new|date',
            'rent_agreement_end_date' => 'required_if:form_case,newrequired|date',
            'rent_agreement_location' => 'required_if:form_case,newrequired|numeric',
            'rent_agreement_period' => 'required_if:form_case,newrequired|numeric|gte:1',
            'first_party_id' => 'required_if:form_case,newrequired|numeric',
            // 'first_party_cr' => 'required_if:form_case,new|numeric',
            // 'first_party_mobile' => 'required_if:form_case,newnumeric',
            'second_party_id' => 'required_if:form_case,newrequired|numeric',
            // 'second_party_cr' => 'required_if:form_case,new|numeric',
            // 'second_party_mobile' => 'required_if:form_case,new|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        if($request->form_case == 'new' && $request->first_party_id == $request->second_party_id){
            return $this->jsonErrorResponse($data, 'Both Parties must be different!', 422);
        }
        
        try {
            if(isset($id)){
                $rentAgreement = TblRentAgreement::where('rent_agreement_id',$id)->where(Utilities::currentBC())->first();
            }else{
                $rentAgreement = new TblRentAgreement();
                $rentAgreement->rent_agreement_id = Utilities::uuid();
                $rentAgreement->agreement_code = $this->documentCode(TblRentAgreement::max('agreement_code'),'RAG');
                $rentAgreement->rent_agreement_start_date = date('Y-m-d', strtotime($request->rent_agreement_start_date));
                $rentAgreement->rent_agreement_end_date = date('Y-m-d', strtotime($request->rent_agreement_end_date));
                $rentAgreement->rent_agreement_date = date('Y-m-d', strtotime($request->rent_agreement_date));
                $rentAgreement->rent_location_id = $request->rent_agreement_location;
                $rentAgreement->rent_agreement_period = $request->rent_agreement_period;
                $rentAgreement->rent_agreement_amount = $request->rent_agreement_amount;
                $rentAgreement->rent_advance_paid = $request->rent_agreement_advance ?? 0;
                $rentAgreement->rent_opening_balance = $request->rent_agreement_ob ?? 0;
                $rentAgreement->first_party_id = $request->first_party_id;
                $rentAgreement->second_party_id = $request->second_party_id;
            }
            $form_id = $rentAgreement->rent_agreement_id;
            $rentAgreement->city_id = $request->rent_agreement_city ?? '';
            $rentAgreement->rent_agreement_remarks = $request->rent_agreement_remarks ?? '';
            $rentAgreement->business_id = auth()->user()->business_id;
            $rentAgreement->company_id = auth()->user()->company_id;
            $rentAgreement->branch_id = auth()->user()->branch_id;
            $rentAgreement->user_id = auth()->user()->id;
            $rentAgreement->save();

            if(!isset($request->pd) || count($request->pd) < 0){
                return $this->jsonErrorResponse($data, 'No Rent Installments Found!', 422);
            }

            if(isset($request->pd)){
                $sr = 0;
                foreach ($request->pd as $row) {
                    if(isset($row['rent_agreement_dtl_id'])){
                        $agreementDtl = TblRentAgreementDtl::where('rent_agreement_dtl_id' , $row['rent_agreement_dtl_id'])->first();
                    }else{
                        $agreementDtl = new TblRentAgreementDtl();
                        $agreementDtl->rent_agreement_dtl_id = Utilities::uuid();
                        $agreementDtl->rent_agreement_id = $rentAgreement->rent_agreement_id;
                    }
                    $agreementDtl->rent_agreement_dtl_date = date('Y-m-d' , strtotime($row['rent_collect_date']));
                    $agreementDtl->rent_agreement_dtl_desc = $row['rent_collect_descripiton'] ?? '';
                    $agreementDtl->rent_agreement_dtl_discount = $row['rent_collect_discount'];
                    $agreementDtl->rent_agreement_dtl_amount = $row['rent_collect_amount'];
                    $agreementDtl->rent_agreement_dtl_balance = $row['rent_collect_balance'];
                    $agreementDtl->business_id = auth()->user()->business_id;
                    $agreementDtl->company_id = auth()->user()->company_id;
                    $agreementDtl->branch_id = auth()->user()->branch_id;
                    $agreementDtl->user_id = auth()->user()->id;
                    $agreementDtl->sr_no = $sr++;
                    $agreementDtl->save();
                    // Calculating the total rent value
                    $totalRent += $agreementDtl->rent_agreement_dtl_amount;
                }    
            }
            // Update The Total Rent Agreement Amount
            $rentAgreement = TblRentAgreement::where('rent_agreement_id',$rentAgreement->rent_agreement_id)->where(Utilities::currentBC())->first();
            $rentAgreement->rent_agreement_total_rent = $totalRent;
            $rentAgreement->save();

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
     * Make Print of the Record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($id = null , $type = null)
    {
        if(!isset($id)){
            abort('404');
        }

        $form_type = 'RAG';
        $data['title'] = 'Rent Agreement';
        $data['type'] = $type;
        $data['rag_menu_id'] = '240';
        $data['print_link'] = '/'.self::$redirect_url.'/print/'.$id.'/pdf';
        $data['permission'] = $data['rag_menu_id'].'-print';
        if(isset($id)){
            if(TblRentAgreement::where('rent_agreement_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblRentAgreement::with('dtls','firstParty','secondParty','location')->where(Utilities::currentBCB())->where('rent_agreement_id',$id)->first();
            }else{
                abort('404');
            }
        }

        if(isset($type) && $type=='pdf'){
            $view = view('prints.rent.rent_agreement', compact('data'))->render();

            $Arabic = new Arabic();
            $p = $Arabic->arIdentify($view);
            for ($i = count($p)-1; $i >= 0; $i-=2) {
                $utf8ar = $Arabic->utf8Glyphs(substr($view, $p[$i-1], $p[$i] - $p[$i-1]));
                $view   = substr_replace($view, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
            }

            //dd($view);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 100);
            $options->set('isPhpEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $options->setDefaultFont('arial');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view,'UTF-8');
            // (Optional) Setup the paper size and orientation
            $paper_orientation = 'portrait';
            // $customPaper = array(25,0,272,1122);
            $customPaper = array(0,0,242,1122);
            $dompdf->setPaper($customPaper);
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            return $dompdf->stream();
        }else{
            return view('prints.rent.rent_agreement',compact('data'));
        }
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


    public function redirectVoucherScreen(Request $request){
        $data = [];
        $validator = Validator::make($request->all() , [
            'voucherType' => 'required',
            'installmentId' => 'required|numeric',
            'installmentAmount' => 'required|numeric',
            'receiverId'    => 'required|numeric'
        ]);
        
        if($validator->fails()){
            return $this->jsonErrorResponse([] , 'Something went wrong!' , 422);
        }
        $accountDetails = TblRentPartyProfile::with('chartAccount')->where('party_profile_id' , $request->receiverId)->first();
        $accountDetails->installmentId = $request->installmentId;
        $accountDetails->installmentAmount = $request->installmentAmount;
        $accountDetails->installmentDiscount = $request->installmentDiscount ?? 0;
        $accountDetails->installmentBalance = $request->installmentBalance ?? 0;
        $accountDetails->installmentDesc = $request->installmentDesc;
        $accountDetails->voucherType = $request->voucherType;
        // Store Data In Session
        $request->session()->forget('installmentDetail');
        $request->session()->put('installmentDetail', $accountDetails->toArray());
        
        if($request->voucherType == 'cash'){
            $data['redirect'] = url('accounts/crv/form');
        }else{
            $data['redirect'] = url('accounts/brv/form');
        }
        return $this->jsonSuccessResponse($data , 'Redirecting to Voucher Screen!' , 200);
    }

    public function alreadyEnterdVoucher(Request $request){
        $data = [];
        $validator = Validator::make($request->all() , [
            'installmentId' => 'required|numeric',
            'installmentAmount' => 'required',
            'installmentDiscount' => 'required',
            'installmentBalance'    => 'required',
            'installmentDesc'   => 'required',
        ]);

        if($validator->fails()){
            return $this->jsonErrorResponse([] , trans('message.required_fields') , 422);
        }

        DB::beginTransaction();
        try{
            
            $installment = TblRentAgreementDtl::where('rent_agreement_dtl_id' , $request->installmentId)->first();
            $installment->rent_agreement_dtl_status = 1;
            $installment->rent_agreement_dtl_amount = $request->installmentAmount;
            $installment->rent_agreement_dtl_discount = $request->installmentDiscount;
            $installment->rent_agreement_dtl_balance = $request->installmentBalance;
            $installment->rent_agreement_dtl_desc = $request->installmentDesc;
            $installment->business_id = auth()->user()->business_id;
            $installment->company_id = auth()->user()->company_id;
            $installment->branch_id = auth()->user()->branch_id;
            $installment->user_id = auth()->user()->id;
            $installment->save();

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
        return $this->jsonSuccessResponse($data, 'Installment Status Updated!', 200);
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
            $agreement = TblRentAgreement::with('dtls')->where('rent_agreement_id',$id)->where(Utilities::currentBCB())->first();
            foreach ($agreement->dtls as $vch){
                TblAccoVoucher::where('voucher_id',$vch->voucher_id)->where(Utilities::currentBCB())->delete();
            }
            $agreement->dtls()->delete();
            $agreement->delete();
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
