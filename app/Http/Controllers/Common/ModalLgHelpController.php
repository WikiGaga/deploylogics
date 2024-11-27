<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblPurcSupplier;
use App\Models\ViewPurcPoHelp;
use App\Models\ViewPurcProductBarcodeRate;
use App\Models\ViewRptSaleInvoice;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblSoftUserPageSetting;
use App\Models\ViewSaleSalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModalLgHelpController extends Controller
{
    public function poHelp(Request $request){
//       / dd("hjkhkj");
        $data = [];
        $filterAjax = false;

        $data['supplier'] = TblPurcSupplier::orderBy('supplier_name')->where(Utilities::currentBC())->get();
        $modal = ViewPurcPoHelp::where('po_grn_status','pending');

        if(isset($request->global_search)){
            if(!empty($request->global_search)){
                $data['global_search'] = $request->global_search;
                $global_search = trim(strtolower(strtoupper($data['global_search'])));
                $replaced_str = str_replace(' ', '%', $global_search);
                $modal = $modal->where(function($qry) use ($replaced_str) {
                    $qry->where(DB::raw('lower(purchase_order_code)'),'LIKE',"%".$replaced_str."%")
                        ->orWhere(DB::raw('lower(supplier_name)'),'LIKE',"%".$replaced_str."%");
                });

            }
            $filterAjax = true;
        }
        if(isset($request->supplier_id)){
            if(!empty($request->supplier_id)){
                $data['supplier_id'] = $request->supplier_id;
                $modal = $modal->where('supplier_id',$request->supplier_id);
            }
            $filterAjax = true;
        }
        if(isset($request->status)){
            if(!empty($request->status)){
                $data['status'] = $request->status;
                if($request->status == 'pending' || $request->status == 'completed'){
                    $modal = $modal->where('po_grn_status',$request->status);
                }
            }
            $filterAjax = true;
        }

        if(isset($global_search)) {
            $modal = $modal->orderByRaw("Case
                    WHEN upper(supplier_name) Like '" . $global_search . "' THEN 1
                    WHEN upper(supplier_name) Like '" . $global_search . "%' THEN 2
                    WHEN upper(supplier_name) Like '%" . $global_search . "' THEN 4
                    Else 3
                END")->orderby('supplier_name');
        }
        $modal = $modal->orderby('created_at','desc')->skip(0)->take(100)->get();

        $data['list'] = $modal;
       // dd($data['list']->toArray());
        if(isset($request->ajax_req) && $request->ajax_req){
            $filterAjax = true;
        }
        if($filterAjax && !isset($request->open_modal)){

            return $this->jsonSuccessResponse($data, '', 200);

        }else{
            return view('help_lg.po.po_modal_body',compact('data'));
        }
    }

}
