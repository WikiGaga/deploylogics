<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblDefiConfigBranches;
use App\Models\TblDefiConfiguration;
use App\Models\TblAccCoa;
use App\Models\TblDefiShortcutKeys;
use App\Models\TblSoftBranch;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;
class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Configuration Form';
    public static $redirect_url = 'configuration';
    public static $menu_dtl_id = '71';

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
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        if(TblDefiConfiguration::where('branch_id','LIKE',auth()->user()->branch_id)->exists()){
            $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
            $data['permission'] = self::$menu_dtl_id.'-edit';
            $data['current'] = TblDefiConfiguration::where('branch_id',auth()->user()->branch_id)->first();
            $data['short_keys'] = TblDefiShortcutKeys::where('branch_id',auth()->user()->branch_id)->first();
            $data['branch_wise_acc'] = TblDefiConfigBranches::where('branch_id',auth()->user()->branch_id)->get();
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['Chart_L2']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',2)->get();
        $data['Chart_L3']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',3)->get();
        $data['Chart_L4']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->get();
        $data['branches'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
      //  dd($data['branch_wise_acc']->toArray());
        return view('setting.configuration.form',compact('data'));
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
            'customer_group' => 'nullable|numeric',
            'sale_income' => 'nullable|numeric',
            'sale_discount' => 'nullable|numeric',
            'sale_vat_payable' => 'nullable|numeric',
            'sale_stock' => 'nullable|numeric',
            'sale_stock_consumption' => 'nullable|numeric',
            'supplier_group' => 'nullable|numeric',
            'purchase_stock' => 'nullable|numeric',
            'purchase_discount' => 'nullable|numeric',
            'purchase_vat' => 'nullable|numeric',
            'bank_group' => 'nullable|numeric',
            'cash_group' => 'nullable|numeric',
            'sale_cash_ac' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{

            /*if(TblDefiConfiguration::where('configuration_user_id','LIKE',auth()->user()->id)->exists()){
                $configdata = TblDefiConfiguration::where('configuration_user_id',auth()->user()->id)->first();
                $configdata->delete();
            }
            $a = "[";
            $a .= '{ "'.$request->purchase_discount['name'].'":"'.$request->purchase_discount['val'].'",
                    "'.$request->purchase_tax['name'].'":"'.$request->purchase_tax['val'].'",
                    "'.$request->purchase_freight['name'].'":"'.$request->purchase_freight['val'].'",
                    "'.$request->purchase_other_charges['name'].'":"'.$request->purchase_other_charges['val'].'",
                    "'.$request->bank_group['name'].'":"'.$request->bank_group['val'].'",
                    "'.$request->cash_group['name'].'":"'.$request->cash_group['val'].'",
                    "'.$request->customer_account['name'].'":"'.$request->customer_account['val'].'",
                    "'.$request->supplier_account['name'].'":"'.$request->supplier_account['val'].'",
                    "'.$request->sale_discount['name'].'":"'.$request->sale_discount['val'].'",
                    "'.$request->sale_tax['name'].'":"'.$request->sale_tax['val'].'",
                    "'.$request->sale_freight['name'].'":"'.$request->sale_freight['val'].'",
                    "'.$request->sale_other_charges['name'].'":"'.$request->sale_other_charges['val'].'",
                    "'.$request->save_form['name'].'":"'.$request->save_form['val'].'",
                    "'.$request->create_form['name'].'":"'.$request->create_form['val'].'",
                    "'.$request->back_form['name'].'":"'.$request->back_form['val'].'",
                    "'.$request->qty_decimal['name'].'":"'.$request->qty_decimal['val'].'",
                    "'.$request->rate_decimal['name'].'":"'.$request->rate_decimal['val'].'",
                    "'.$request->amount_decimal['name'].'":"'.$request->amount_decimal['val'].'"
                    }';
            $a .= "]";*/

            if(TblDefiConfiguration::where('branch_id',auth()->user()->branch_id)->exists()){
                $config = TblDefiConfiguration::where('branch_id',auth()->user()->branch_id)->first();
            }else{
                $config = new TblDefiConfiguration();
                $config->configuration_id = Utilities::uuid();
            }
            $config->customer_group = $request->customer_group;
            $config->sale_income = $request->sale_income;
            $config->sale_discount = $request->sale_discount;
            $config->sale_vat_payable = $request->sale_vat_payable;
            $config->sale_stock = $request->sale_stock;
            $config->sale_stock_consumption = $request->sale_stock_consumption;
            $config->sale_cash_ac = $request->sale_cash_ac;

            $config->sale_return_customer_group = $request->sale_return_customer_group;
            $config->sale_return_income = $request->sale_return_income;
            $config->sale_return_discount = $request->sale_return_discount;
            $config->sale_return_vat_payable = $request->sale_return_vat_payable;
            $config->sale_return_stock = $request->sale_return_stock;
            $config->sale_return_stock_consumption = $request->sale_return_stock_consumption;
            $config->sale_return_cash_ac = $request->sale_return_cash_ac;

            $config->sale_fee_income = $request->sale_fee_income;
            $config->sale_fee_discount = $request->sale_fee_discount;
            $config->sale_fee_vat_payable = $request->sale_fee_vat_payable;
            $config->sale_fee_stock = $request->sale_fee_stock;
            $config->sale_fee_stock_consumption = $request->sale_fee_stock_consumption;
            $config->sale_fee_cash_ac = $request->sale_fee_cash_ac;

            $config->display_rent_fee_income = $request->display_rent_fee_income;
            $config->display_rent_fee_discount = $request->display_rent_fee_discount;
            $config->display_rent_fee_vat_payable = $request->display_rent_fee_vat_payable;
            $config->display_rent_fee_stock = $request->display_rent_fee_stock;
            $config->display_rent_fee_stock_consumption = $request->display_rent_fee_stock_consumption;
            $config->display_rent_fee_cash_ac = $request->display_rent_fee_cash_ac;

            $config->rebate_invoice_income = $request->rebate_invoice_income;
            $config->rebate_invoice_discount = $request->rebate_invoice_discount;
            $config->rebate_invoice_vat_payable = $request->rebate_invoice_vat_payable;
            $config->rebate_invoice_stock = $request->rebate_invoice_stock;
            $config->rebate_invoice_stock_consumption = $request->rebate_invoice_stock_consumption;
            $config->rebate_invoice_cash_ac = $request->rebate_invoice_cash_ac;

            $config->supplier_group = $request->supplier_group;
            $config->purchase_stock = $request->purchase_stock;
            $config->purchase_discount = $request->purchase_discount;
            $config->purchase_vat = $request->purchase_vat;

            $config->bank_group = $request->bank_group;
            $config->cash_group = $request->cash_group;
            $config->payment_receive_dr_ac = $request->payment_receive_dr_ac;
            $config->payment_receive_cr_ac = $request->payment_receive_cr_ac;
            $config->general_cash_ac = $request->general_cash_ac;
            $config->excess_cash_ac = $request->excess_cash_ac;
            $config->bank_distribution_cr_ac = $request->bank_distribution_cr_ac;

            $config->business_id = auth()->user()->business_id;
            $config->company_id = auth()->user()->company_id;
            $config->branch_id = auth()->user()->branch_id;
            $config->configuration_user_id = auth()->user()->id;
            $config->save();

            if(TblDefiShortcutKeys::where('branch_id',auth()->user()->branch_id)->exists()){
                $short_keys = TblDefiShortcutKeys::where('branch_id',auth()->user()->branch_id)->first();
            }else{
                $short_keys = new TblDefiShortcutKeys();
                $short_keys->shortcut_keys_id = Utilities::uuid();
            }

            $short_keys->shortcut_keys_form_qty_decimal = $request->qty_decimal;
            $short_keys->shortcut_keys_form_rate_decimal = $request->rate_decimal;
            $short_keys->shortcut_keys_form_amount_decimal = $request->amount_decimal;

            $short_keys->shortcut_keys_form_save = $request->form_save;
            $short_keys->shortcut_keys_form_create = $request->form_create;
            $short_keys->shortcut_keys_form_back = $request->form_back;

            $short_keys->business_id = auth()->user()->business_id;
            $short_keys->company_id = auth()->user()->company_id;
            $short_keys->branch_id = auth()->user()->branch_id;
            $short_keys->shortcut_keys_user_id = auth()->user()->id;
            $short_keys->save();
            if(!empty($request->acc)){
                TblDefiConfigBranches::where(Utilities::currentBCB())->delete();
                foreach ($request->acc as $acc){
                    $configBranch = new TblDefiConfigBranches();
                    $configBranch->configuration_branches_id = Utilities::uuid();
                    $configBranch->business_id = auth()->user()->business_id;
                    $configBranch->company_id = auth()->user()->company_id;
                    $configBranch->branch_id = auth()->user()->branch_id;
                    $configBranch->configuration_branches_user_id = auth()->user()->id;
                    $configBranch->acc_branch_id = $acc['branch_id'];
                    $configBranch->stock_transfer_income = !empty($acc['stock_transfer_income'])?$acc['stock_transfer_income']:"";
                    $configBranch->stock_transfer_stock = !empty($acc['stock_transfer_stock'])?$acc['stock_transfer_stock']:"";
                    $configBranch->stock_transfer_branch = !empty($acc['stock_transfer_branch'])?$acc['stock_transfer_branch']:"";
                    $configBranch->stock_transfer_cash = !empty($acc['stock_transfer_cash'])?$acc['stock_transfer_cash']:"";
                    $configBranch->stock_transfer_vat = !empty($acc['stock_transfer_vat'])?$acc['stock_transfer_vat']:"";
                    $configBranch->stock_transfer_discount = !empty($acc['stock_transfer_discount'])?$acc['stock_transfer_discount']:"";
                    $configBranch->store_receive_stock = !empty($acc['store_receive_stock'])?$acc['store_receive_stock']:"";
                    $configBranch->stock_receive_cash = !empty($acc['stock_receive_cash'])?$acc['stock_receive_cash']:"";
                    $configBranch->stock_receive_branch = !empty($acc['stock_receive_branch'])?$acc['stock_receive_branch']:"";
                    $configBranch->stock_receive_vat = !empty($acc['stock_receive_vat'])?$acc['stock_receive_vat']:"";
                    $configBranch->stock_receive_discount = !empty($acc['stock_receive_discount'])?$acc['stock_receive_discount']:"";
                    $configBranch->save();
                }
            }

            Utilities::addSession('configuration');

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
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage;
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
