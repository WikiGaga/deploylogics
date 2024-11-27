<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Purc\TblPurcPurchasing;
use App\Models\Purc\TblPurcPurchasingDtl;
use App\Models\Purc\TblPurcPurchasingDtlDtl;
use App\Models\TblPurcDemand;
use App\Models\User;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PurchasingController extends Controller
{
    public static $page_title = 'Purchasing';
    public static $redirect_url = 'purchasing';
    public static $menu_dtl_id = '180';
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
        $data = [];
        $data['page_data'] = [];
        $data['form_type'] = 'purchasing';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        if(isset($id)){
            if(TblPurcPurchasing::where('purchasing_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcPurchasing::where('purchasing_id',$id)->where('purchasing_type','purchasing')->where(Utilities::currentBCB())->first();
                $stock_ids = DB::select("select DISTINCT STOCK_ID from tbl_purc_purchasing_dtl where purchasing_id = $id");
                foreach ($stock_ids as $stock_id){
                    $data['stock_ids'][] = $stock_id->stock_id;
                }
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'Purc\TblPurcPurchasing',
                'code_field'        => 'purchasing_code',
                'code_prefix'       => strtoupper('pur'),
                'code_type_field'   => 'purchasing_type',
                'code_type'         => 'purchasing',
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);


        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
        $data['stock_request_list'] = TblPurcDemand::with('branch')
            ->where('demand_type','stock_request')
            ->where('demand_entry_status',1)
            ->where('demand_branch_to',auth()->user()->branch_id)->get();
     //   dd($data['current']->dtl->toArray());

        return view('purchase.purchasing.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id=null)
    {
      //  dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'salesman' => 'required',
            'request_code' => 'required',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $pp = TblPurcPurchasing::where('purchasing_id',$id)->first();
            }else{
                $pp = new TblPurcPurchasing();
                $pp->purchasing_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'Purc\TblPurcPurchasing',
                    'code_field'        => 'purchasing_code',
                    'code_prefix'       => strtoupper('pur'),
                    'code_type_field'   => 'purchasing_type',
                    'code_type'         => 'purchasing',
                ];
                $pp->purchasing_code = Utilities::documentCode($doc_data);
            }
            $pp->purchasing_type = 'purchasing';
            $pp->purchasing_entry_date = date('Y-m-d', strtotime($request->document_date));;
            $pp->salesman_id = $request->salesman;
            $pp->business_id = auth()->user()->business_id;
            $pp->company_id = auth()->user()->company_id;
            $pp->branch_id = auth()->user()->branch_id;
            $pp->purchasing_user_id = auth()->user()->id;
            $pp->save();

            $dtls_del = TblPurcPurchasingDtl::where('purchasing_id',$pp->purchasing_id)->get();
            foreach ($dtls_del as $dtl_del){
                TblPurcPurchasingDtlDtl::where('purchasing_dtl_id',$dtl_del->purchasing_dtl_no)->delete();
            }
            TblPurcPurchasingDtl::where('purchasing_id',$pp->purchasing_id)->delete();

            $dtl_sr_no = 1;
            foreach ($request->dtl as $dk=>$dtlVal){
                $total_quantity = 0;
                $dtl_no = Utilities::uuid();
                foreach ($dtlVal['branch'] as $k=>$item){ $total_quantity += $item['qty']; }
                foreach ($dtlVal['branch'] as $k=>$item){
                    $dtl = new TblPurcPurchasingDtl();
                    $dtl->purchasing_dtl_id = Utilities::uuid();
                    $dtl->purchasing_id = $pp->purchasing_id;
                    $dtl->purchasing_dtl_no = $dtl_no;
                    $dtl->purchasing_dtl_sr_no = $dtl_sr_no;
                    $dtl->product_id = $dtlVal['product_id'];
                    $dtl->stock_id = $item['stock_id'];
                    $dtl->stock_no = $item['stock_no'];
                    $dtl->product_barcode_id = $dtlVal['product_barcode_id'];
                    $dtl->uom_id = $dtlVal['uom_id'];
                    $dtl->purchasing_dtl_barcode = $dtlVal['product_barcode_barcode'];
                    $dtl->purchasing_dtl_packing = $dtlVal['packing'];
                    $dtl->purchasing_dtl_branch_id = $item['id'];
                    $dtl->purchasing_dtl_demand_quantity = $item['qty'];
                    $dtl->purchasing_dtl_total_quantity = $total_quantity;
                    $dtl->purchasing_dtl_purc_quantity = (float)$dtlVal['input_purc_qty'];
                    $dtl->purchasing_dtl_diff_quantity = (float)$total_quantity - (float)$dtlVal['input_purc_qty'];
                    $dtl->save();
                }
                if(isset($dtlVal['pd'])){
                    $dtl_dtl_sr_no = 1;
                    foreach ($dtlVal['pd'] as $pd){
                        $dtlDtl = new TblPurcPurchasingDtlDtl();
                        $dtlDtl->purchasing_dtl_dtl_id = Utilities::uuid();
                        $dtlDtl->purchasing_dtl_id = $dtl_no;
                        $dtlDtl->purchasing_dtl_dtl_sr_no = $dtl_dtl_sr_no;
                        $dtlDtl->product_id = $pd['product_id'];
                        $dtlDtl->product_barcode_id = $pd['product_barcode_id'];
                        $dtlDtl->uom_id = $pd['uom_id'];
                        $dtlDtl->purchasing_dtl_dtl_barcode = $pd['pd_barcode'];
                        $dtlDtl->purchasing_dtl_dtl_packing = $pd['pd_packing'];
                        $dtlDtl->purchasing_dtl_dtl_quantity = $pd['quantity'];
                        $dtlDtl->purchasing_dtl_dtl_foc_quantity = $pd['foc_qty'];
                        $dtlDtl->purchasing_dtl_dtl_fc_rate = $pd['fc_rate'];
                        $dtlDtl->purchasing_dtl_dtl_rate = $pd['rate'];
                        $dtlDtl->purchasing_dtl_dtl_amount = $pd['amount'];
                        $dtlDtl->purchasing_dtl_dtl_disc_percent = $pd['dis_perc'];
                        $dtlDtl->purchasing_dtl_dtl_disc_amount = $pd['dis_amount'];
                        $dtlDtl->purchasing_dtl_dtl_vat_percent = $pd['vat_perc'];
                        $dtlDtl->purchasing_dtl_dtl_vat_amount = $pd['vat_amount'];
                        $dtlDtl->purchasing_dtl_dtl_net_amount = $pd['gross_amount'];
                        $dtlDtl->business_id = auth()->user()->business_id;
                        $dtlDtl->company_id = auth()->user()->company_id;
                        $dtlDtl->branch_id = auth()->user()->branch_id;
                        $dtlDtl->purchasing_dtl_dtl_user_id = auth()->user()->id;
                        $dtlDtl->save();
                        $dtl_dtl_sr_no = $dtl_dtl_sr_no+1;
                    }
                }
                $dtl_sr_no = $dtl_sr_no+1;
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

    public function productData(Request $request){

        $items = TblPurcDemand::where('demand_type','stock_request')
            ->where('demand_entry_status',1)
            ->whereIn('demand_id',$request->ids)->pluck('branch_id')->toArray();
        $orig_items = $items;

        for($i=0; $i < count($orig_items); $i++){
            unset($items[$i]);
            if(in_array($orig_items[$i],$items)){
                $data = ["message"=>"Multi Request not allowed for one branch"];
                break;
            }
            $items = $orig_items;
        }
        if(!isset($data)){
            $data = [];
            foreach($request->ids as $k=>$stock_id){
                $items = TblPurcDemand::with('dtls','branch')
                    ->where('demand_type','stock_request')
                    ->where('demand_entry_status',1)
                    ->where('demand_id',$stock_id)->first();
                //  dd($items->toArray());
                if(empty($items)){
                    $data = [];
                    break;
                }
                $branch_id = $items->branch->branch_id;
                $branch = $items->branch->branch_short_name;
                foreach ($items->dtls as $kk=>$item){
                    $data[$item['product_barcode_id']]['products'][] = [
                        'demand_id' => $item['demand_dtl_id'],
                        'demand_dtl_id' => $item['demand_dtl_id'],
                        'product_id' => $item['product_id'],
                        'product_barcode_id' => $item['product_barcode_id'],
                        'uom_id' => $item['demand_dtl_uom'],
                        'product_barcode_barcode' => $item['product_barcode_barcode'],
                        'product_name' => $item['product']['product_name'],
                        'uom_name' => $item['uom']['uom_name'],
                        'demand_dtl_packing' => $item['demand_dtl_packing'],
                    ];
                    $data[$item['product_barcode_id']]['branches'][$k]['stock_id'] = $stock_id;
                    $data[$item['product_barcode_id']]['branches'][$k]['stock_no'] = $items->demand_no;
                    $data[$item['product_barcode_id']]['branches'][$k]['id'] = $branch_id;
                    $data[$item['product_barcode_id']]['branches'][$k]['name'] = $branch;
                    $data[$item['product_barcode_id']]['branches'][$k]['qty'] = $item['demand_dtl_demand_quantity'];
                }
            }
        }
        return view('purchase.purchasing.product_data',compact('data'));
    }

    public function productDataEdit(Request $request){
        $current = TblPurcPurchasing::with('dtl')->where('purchasing_id',$request->purchasing_id)->where('purchasing_type','purchasing')->where(Utilities::currentBCB())->first();
        $data = [];
        foreach ($current->dtl as $k=>$item){
            $data[$item['product_barcode_id']]['products'][] = [
                'demand_id' => $item['stock_id'],
                'demand_dtl_id' => $item['stock_id'],
                'product_id' => $item['product_id'],
                'product_barcode_id' => $item['product_barcode_id'],
                'uom_id' => $item['demand_dtl_uom'],
                'product_barcode_barcode' => $item['purchasing_dtl_barcode'],
                'product_name' => $item['product']['product_name'],
                'uom_name' => $item['uom']['uom_name'],
                'demand_dtl_packing' => $item['purchasing_dtl_packing'],
            ];
            $data[$item['product_barcode_id']]['branches'][$k]['stock_id'] =  $item['stock_id'];
            $data[$item['product_barcode_id']]['branches'][$k]['stock_no'] = $item['stock_no'];
            $data[$item['product_barcode_id']]['branches'][$k]['id'] =  $item['purchasing_dtl_branch_id'];
            $data[$item['product_barcode_id']]['branches'][$k]['name'] = $item['branch'][0]['branch_short_name'];
            $data[$item['product_barcode_id']]['branches'][$k]['qty'] = $item['purchasing_dtl_demand_quantity'];
            $data[$item['product_barcode_id']]['dtl_dtl'] = $item['dtl_dtl'];
            $data[$item['product_barcode_id']]['purc_quantity'] = $item['purchasing_dtl_purc_quantity'];
            $data[$item['product_barcode_id']]['diff_quantity'] = $item['purchasing_dtl_diff_quantity'];
        }
       // dd($data);
        return view('purchase.purchasing.product_data_edit',compact('data'));
    }


}
