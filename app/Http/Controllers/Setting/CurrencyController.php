<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblDefiCountry;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblPurcGrn;
use App\Models\TblSaleSalesOrder;
use App\Models\TblSaleSales;
use App\Models\TblAccoVoucher;
use App\Models\TblSoftBusiness;
use App\Models\TblSoftBranch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Currency';
    public static $menu_dtl_id = '3';
    public static $redirect_url = 'currency';
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
            if(TblDefiCurrency::where('currency_id','LIKE',$id)->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblDefiCurrency::where('currency_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['country'] = TblDefiCountry::where('country_entry_status',1)->orderBy('country_name', 'ASC')->get();
        return view('setting.currency.form',compact('data'));
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
            'currency_country' => 'required|numeric',
            'currency_decimal_precision' => 'nullable|max:5',
            'currency_rate' => 'nullable|max:10',
            'currency_symbol' => 'nullable|max:5',
            'currency_remarks' => 'nullable|max:255'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $currency = TblDefiCurrency::where('currency_id',$id)->first();
            }else{
                $currency = new TblDefiCurrency();
                $currency->currency_id = Utilities::uuid();
            }
            $form_id = $currency->currency_id;
            $currency->currency_country = $request->currency_country;
            $currency->currency_name = $request->name;
            $currency->currency_symbol = $request->currency_symbol;
            $currency->currency_rate = $request->currency_rate;
            $currency->currency_decimal_precision = $request->currency_decimal_precision;
            $currency->currency_remarks = $request->currency_remarks;
            $currency->currency_default = isset($request->currency_default)?1:0;
            $currency->currency_entry_status = isset($request->currency_entry_status)?1:0;
            $currency->business_id = auth()->user()->business_id;
            $currency->company_id = auth()->user()->company_id;
            $currency->branch_id = auth()->user()->branch_id;
            $currency->currency_user_id = auth()->user()->id;
            $currency->save();

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
            $po = TblPurcPurchaseOrder::where('currency_id',$id)->first();
            $Grn = TblPurcGrn::where('currency_id',$id)->first();
            $SO = TblSaleSalesOrder::where('currency_id',$id)->first();
            $Sale = TblSaleSales::where('currency_id',$id)->first();
            $Voucher = TblAccoVoucher::where('currency_id',$id)->first();
            $business = TblSoftBusiness::where('currency_id',$id)->first();
            $branch = TblSoftBranch::where('branch_currency_id',$id)->first();

            if($po == null && $Grn == null && $SO == null && $Sale == null && $Voucher == null && $business == null && $branch == null)
            {
                $Currency = TblDefiCurrency::where('currency_id',$id)->first();
                $Currency->delete();
            }else{
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
