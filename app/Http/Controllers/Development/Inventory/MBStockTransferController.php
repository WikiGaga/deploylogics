<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Inve\TblInveMBStockTransfer;
use App\Models\Inve\TblInveMBStockTransferDtl;
use App\Models\Inve\TblInveMBStockTransferQty;
use App\Models\Purc\TblPurcPurchasing;
use App\Models\Purc\TblPurcPurchasingDtl;
use App\Models\Purc\TblPurcPurchasingDtlDtl;
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
class MBStockTransferController extends Controller
{
    public static $page_title = 'MB Stock Transfer';
    public static $redirect_url = 'mb-stock-transfer';
    public static $menu_dtl_id = '181';

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
        $data['form_type'] = 'mbst';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblInveMBStockTransfer::where('mb_stock_transfer_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['current'] = TblInveMBStockTransfer::with('purchasing')->where('mb_stock_transfer_id','LIKE',$id)->where(Utilities::currentBCB())->first();
                $data['document_code'] = $data['current']->mb_stock_transfer_code;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'Inve\TblInveMBStockTransfer',
                'code_field'        => 'mb_stock_transfer_code',
                'code_prefix'       => 'MBST',
                'code_type_field'   => 'mb_stock_transfer_type',
                'code_type'         => 'MBST',
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_inve_mb_stock_transfer',
            'col_id' => 'mb_stock_transfer_id',
            'col_code' => 'mb_stock_transfer_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('inventory.multi_branch_stock_transfer.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {
       // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'purchasing_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $mbst = TblInveMBStockTransfer::where('mb_stock_transfer_id',$id)->first();
            }else{
                $mbst = new TblInveMBStockTransfer();
                $mbst->mb_stock_transfer_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'Inve\TblInveMBStockTransfer',
                    'code_field'        => 'mb_stock_transfer_code',
                    'code_prefix'       => 'MBST',
                    'code_type_field'   => 'mb_stock_transfer_type',
                    'code_type'         => 'MBST',
                ];
                $mbst->mb_stock_transfer_code = Utilities::documentCode($doc_data);
            }
            $form_id = $mbst->mb_stock_transfer_id;
            $mbst->mb_stock_transfer_entry_date = date('Y-m-d', strtotime($request->document_date));
            $mbst->purchasing_id = $request->purchasing_id;
            $mbst->mb_stock_transfer_entry_status = 1;
            $mbst->mb_stock_transfer_remarks = $request->remarks;
            $mbst->mb_stock_transfer_type = 'MBST';
            $mbst->business_id = auth()->user()->business_id;
            $mbst->company_id = auth()->user()->company_id;
            $mbst->branch_id = auth()->user()->branch_id;
            $mbst->mb_stock_transfer_user_id = auth()->user()->id;
            $mbst->save();

            TblInveMBStockTransferDtl::where('mb_stock_transfer_id',$mbst->mb_stock_transfer_id)->delete();
            TblInveMBStockTransferQty::where('mb_stock_transfer_id',$mbst->mb_stock_transfer_id)->delete();
            foreach ($request->pd as $k=>$pd){
                $dtl = new TblInveMBStockTransferDtl();
                $dtl->mb_stock_transfer_dtl_sr = $k;
                $dtl->mb_stock_transfer_dtl_id = Utilities::uuid();
                $dtl->mb_stock_transfer_id = $mbst->mb_stock_transfer_id;
                $dtl->purchasing_dtl_dtl_id = $pd['purchasing_dtl_dtl_id'];
                $dtl->product_barcode_id = $pd['barcode_id'];
                $dtl->product_barcode_barcode = $pd['barcode'];
                $dtl->product_id = $pd['product_id'];
                $dtl->uom_id = $pd['uom_id'];
                $dtl->mb_stock_transfer_dtl_packing = $pd['packing'];
                $dtl->mb_stock_transfer_dtl_quantity = $pd['qty'];
                $dtl->mb_stock_transfer_dtl_rate = $pd['rate'];
                $dtl->mb_stock_transfer_dtl_amount = $pd['amount'];
                $dtl->mb_stock_transfer_dtl_disc_percent = $pd['disc_perc'];
                $dtl->mb_stock_transfer_dtl_disc_amount = $pd['disc_amount'];
                $dtl->mb_stock_transfer_dtl_vat_percent = $pd['vat_perc'];
                $dtl->mb_stock_transfer_dtl_vat_amount = $pd['vat_amount'];
                $dtl->mb_stock_transfer_dtl_net_amount = $pd['net_amount'];
                $dtl->business_id = auth()->user()->business_id;
                $dtl->company_id = auth()->user()->company_id;
                $dtl->branch_id = auth()->user()->branch_id;
                $dtl->mb_stock_transfer_user_dtl_id = auth()->user()->id;
                $dtl->save();
                foreach ($pd['branch'] as $bk=>$branch){
                    $tQty = new TblInveMBStockTransferQty();
                    $tQty->mb_stock_transfer_qty_sr = $bk+1;
                    $tQty->mb_stock_transfer_qty_id = Utilities::uuid();
                    $tQty->mb_stock_transfer_dtl_id = $dtl->mb_stock_transfer_dtl_id;
                    $tQty->mb_stock_transfer_id = $mbst->mb_stock_transfer_id;
                    $tQty->mb_stock_transfer_qty_branch = $branch['id'];
                    $tQty->mb_stock_transfer_qty_qty = $branch['qty'];
                    $tQty->stock_no = $branch['stock_no'];
                    $tQty->save();
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
    public function getStockPurchasing(Request $request)
    {
        $data = [];
        $branch_ids = DB::table('tbl_purc_purchasing_dtl')->where('purchasing_id',$request->purchasing_id)->distinct()->get(['purchasing_dtl_branch_id','stock_no']);
        $purchasing_dtl_no = DB::table('tbl_purc_purchasing_dtl')->where('purchasing_id',$request->purchasing_id)->distinct()->get(['purchasing_dtl_no']);
        $data['branch'] = [];
        foreach ($branch_ids as $k=>$branch_id){
            $data['branch'][$k] = TblSoftBranch::where('branch_id',$branch_id->purchasing_dtl_branch_id)->select('branch_id','branch_name','branch_short_name')->first();
            $data['branch'][$k]['stock_no'] = $branch_id->stock_no;
        }
        $dtl_ids = [];
        foreach ($purchasing_dtl_no as $dtl_no){
            $dtl_ids[] = $dtl_no->purchasing_dtl_no;
        }
        $data['items'] = TblPurcPurchasingDtlDtl::with('product','barcode','uom')->whereIn('purchasing_dtl_id',$dtl_ids)->orderBy('purchasing_dtl_id')->orderBy('purchasing_dtl_dtl_sr_no')->get();

        return view('inventory.multi_branch_stock_transfer.purchasing_data',compact('data'));
    }
    public function getStockPurchasingDtl(Request $request)
    {
        $data = [];
        $branch_ids = DB::table('tbl_inve_mb_stock_transfer_qty')->where('mb_stock_transfer_id',$request->id)->distinct()->get(['mb_stock_transfer_qty_branch','stock_no']);
        $data['branch'] = [];
        foreach ($branch_ids as $k=>$branch_id){
            $data['branch'][$k] = TblSoftBranch::where('branch_id',$branch_id->mb_stock_transfer_qty_branch)->select('branch_id','branch_name','branch_short_name')->first();
            $data['branch'][$k]['stock_no'] = $branch_id->stock_no;
        }
        $data['items'] = TblInveMBStockTransferDtl::with('transfer_qty','product','barcode','uom')->where('mb_stock_transfer_id',$request->id)->get();

        return view('inventory.multi_branch_stock_transfer.purchasing_data_edit',compact('data'));
    }
}
