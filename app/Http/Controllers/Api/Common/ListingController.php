<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ViewPurcGRN;
use App\Models\TblPurcDemand;
use App\Models\ViewPurcDemand;
use App\Models\TblSaleSales;
use App\Models\TblInveStock;
use App\Models\ViewPurcProductBarcodeHelp;
use App\Library\ApiUtilities;
use App\Models\TblInveItemStockAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ListingController extends ApiController
{
    public function inlineHelpOpen(Request $request, $helpType, $str = null){
        if($helpType == 'supplierHelp'){
            $hideKeys= ['supplier_id'];
            $keys = ['supplier_code','supplier_name','supplier_address','supplier_phone_1','supplier_contact_person'];
            $merge = array_merge( $keys, $hideKeys);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(supplier_name) like '%".strtolower($str)."%' OR";
                $where .= " lower(supplier_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".Session::get('ApiDataSession')->business_id;
            $where .= " AND branch_id = ".Session::get('ApiDataSession')->branch_id;

            $data['head'] = ['Code','Name','Address','Mobile Number','Contact Person'];
            $data['list'] = DB::select('select '.$selectColumns.' from tbl_purc_supplier '.$where.' FETCH FIRST 50 ROWS ONLY');

        }
        if($helpType == 'productHelp'){
            //$data['show_name'] = 'product_barcode_barcode';
            //$data['hideKeys'] = ['product_id','product_barcode_id','uom_id'];
            //$data['keys'] = ['product_barcode_barcode','product_name','product_arabic_name','uom_name'];
            //$merge = array_merge( $data['keys'], $data['hideKeys']);
            //$selectColumns = "'".implode("','", $merge)."'";
            $dataSql = ViewPurcProductBarcodeHelp::select('product_barcode_barcode','product_name','product_arabic_name','uom_name','product_barcode_packing','product_id','product_barcode_id','uom_id')
                        ->where('product_barcode_id', '<>', '0');
            if($str){
                $dataSql->where(DB::raw('lower(product_barcode_barcode)'),'like','%'.strtolower($str).'%')
                        ->orWhere(DB::raw('lower(product_name)'),'like','%'.strtolower($str).'%');
            }
            $data['head'] = ['Barcode','Name','Arabic Name','UOM'];
            $data['list'] = $dataSql->where('business_id',Session::get('ApiDataSession')->business_id)->limit(50)->get();

        }
        return $this->ApiJsonSuccessResponse($data,'help data');
    }

    // Stock Taking Listing
    public function StockTakingList(Request $request ,$current_page = null){

        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $user_id = $request->user_id;

        $user = User::where('id',$user_id)->first();
        $menu_dtl_id = 55;
        $current_page = isset($request->page)?$request->page:1;

        $limit = 10;
        $offset = $limit * ($current_page-1);
        $pageNo = $current_page;

        if(isset($current_page)){
            $offset = $limit * ($current_page-1);
            $pageNo = $current_page;
        }
        $stockTaking = TblInveStock::where('stock_code_type' , 'sa')
            ->where('stock_user_id',$user_id)
            ->where('business_id',$business_id)
            ->where('company_id',$business_id)
            ->where('branch_id',$branch_id)
            //->where('stock_device_id',2)
            ->orderBy('stock_date','desc')
            ->orderBy('stock_code','desc');


        $total = $stockTaking->count();
        $data['title'] = 'Stock Taking';
        $data['typeId'] = $menu_dtl_id;
        $data['pageNo']=$pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages

        $data['permission_create'] = $user->hasPermission("$menu_dtl_id-create")?true:false;
        $data['permission_listing'] = $user->hasPermission("$menu_dtl_id-view")?true:false;
        $permission_edit = $user->hasPermission("$menu_dtl_id-edit")?true:false;
        $data['permission_edit'] = $permission_edit;

        $data['headings']=array('Code','Date','Total Quantity','Total Amount');
        $allData = $stockTaking->skip($offset)->take($limit)->orderby('stock_date', 'DESC')->orderby('stock_code', 'DESC')->get();

        $doc_data = [];
        foreach ($allData as $doc){
            $ite = [
                'id'  => $doc->stock_id,
                'row1' => $doc->stock_code,
                'row2' => "Date:".date('Y-m-d', strtotime($doc->stock_date))." ~ Description: ".$doc->stock_remarks,
                'row3' => "",
                'action' => [
                    'edit' => $permission_edit,
                    'del' => false,
                    'pdf' => false,
                ],
            ];
            array_push($doc_data,$ite);
        }
        if(count($doc_data) == 0){
            $data['list_data'] = (object)[];
        }else{
            $data['list_data'] = $doc_data;
        }

        if($user->hasPermission("$menu_dtl_id-view")){
            return $this->ApiJsonSuccessResponse($data,'listing data');
        }else{
            $data['list_data'] = (object)[];
            return $this->ApiJsonSuccessResponse($data,'you have no permission listing data');
        }
        return $this->ApiJsonSuccessResponse($data,'listing data');
    }

    // Opening Stock Listing
    public function OpeningStockList(Request $request ,$current_page = null){

        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $user_id = $request->user_id;

        $user = User::where('id',$user_id)->first();
        $menu_dtl_id = 54;
        $current_page = isset($request->page)?$request->page:1;

        $limit = 50;
        $offset = $limit * ($current_page-1);
        $pageNo = $current_page;

        if(isset($current_page)){
            $offset = $limit * ($current_page-1);
            $pageNo = $current_page;
        }
        $stockTaking = TblInveStock::with('store')
            ->where('stock_code_type' , 'bt')
            ->where('stock_user_id',$user_id)
            ->where('business_id',$business_id)
            ->where('company_id',$business_id)
            ->where('branch_id',$branch_id)
            ->where('stock_device_id',2)
            ->orderBy('stock_date','desc')
            ->orderBy('stock_code','desc');

        $total = $stockTaking->count();
        $data['title'] = 'Opening Stock';
        $data['typeId'] = $menu_dtl_id;
        $data['pageNo']=$pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages

        $data['permission_create'] = $user->hasPermission("$menu_dtl_id-create")?true:false;
        $data['permission_listing'] = $user->hasPermission("$menu_dtl_id-view")?true:false;
        $permission_edit = $user->hasPermission("$menu_dtl_id-edit")?true:false;
        $data['permission_edit'] = $permission_edit;

        $allData = $stockTaking->skip($offset)->take($limit)->orderby('stock_date', 'DESC')->orderby('stock_code', 'DESC')->get();

      //  dd($allData->toArray());
        $doc_data = [];
        foreach ($allData as $doc){
            $ite = [
                'id'  => $doc->stock_id,
                'row1' => $doc->stock_code,
                'row2' => "Date:".date('Y-m-d', strtotime($doc->stock_date))." ~ Amount: ".number_format($doc->stock_total_amount,3),
                'row3' => "Store: ".$this->strUcWords($doc->store->store_name),
                'action' => [
                    'edit' => $permission_edit,
                    'del' => false,
                    'pdf' => false,
                ],
            ];
            array_push($doc_data,$ite);
        }
        if(count($doc_data) == 0){
            $data['list_data'] = (object)[];
        }else{
            $data['list_data'] = $doc_data;
        }

        if($user->hasPermission("$menu_dtl_id-view")){
            return $this->ApiJsonSuccessResponse($data,'listing data');
        }else{
            $data['list_data'] = (object)[];
            return $this->ApiJsonSuccessResponse($data,'you have no permission listing data');
        }
        return $this->ApiJsonSuccessResponse($data,'listing data');
    }

    // Opening Stock Listing
    public function StockAdjustmentList(Request $request ,$current_page = null){

        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $user_id = $request->user_id;

        $user = User::where('id',$user_id)->first();
        $menu_dtl_id = 55;
        $current_page = isset($request->page)?$request->page:1;

        $limit = 10;
        $offset = $limit * ($current_page-1);
        $pageNo = $current_page;

        if(isset($current_page)){
            $offset = $limit * ($current_page-1);
            $pageNo = $current_page;
        }
        $stockTaking = TblInveStock::with('store')
            ->where('stock_code_type' , 'sa')
            ->where('stock_user_id',$user_id)
            ->where('business_id',$business_id)
            ->where('company_id',$business_id)
            ->where('branch_id',$branch_id)
            ->where('stock_device_id',2)
            ->orderBy('stock_date','desc')
            ->orderBy('stock_code','desc');

        $total = $stockTaking->count();
        $data['title'] = 'Stock Adjustment';
        $data['typeId'] = $menu_dtl_id;
        $data['pageNo']=$pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages

        $data['permission_create'] = $user->hasPermission("$menu_dtl_id-create")?true:false;
        $data['permission_listing'] = $user->hasPermission("$menu_dtl_id-view")?true:false;
        $permission_edit = $user->hasPermission("$menu_dtl_id-edit")?true:false;
        $data['permission_edit'] = $permission_edit;

        $allData = $stockTaking->skip($offset)->take($limit)->orderby('stock_date', 'DESC')->orderby('stock_code', 'DESC')->get();

      //  dd($allData->toArray());
        $doc_data = [];
        foreach ($allData as $doc){
            $store = isset($doc->store->store_name)?"Store: ".$this->strUcWords($doc->store->store_name)." ~ ":"";
            $ite = [
                'id'  => $doc->stock_id,
                'row1' => $doc->stock_code,
                'row2' => "Date:".date('Y-m-d', strtotime($doc->stock_date))." ~ Amount: ".number_format($doc->stock_total_amount,3),
                'row3' => $store."Notes: ".$doc->stock_remarks,
                'action' => [
                    'edit' => $permission_edit,
                    'del' => false,
                    'pdf' => false,
                ],
            ];
            array_push($doc_data,$ite);
        }
        if(count($doc_data) == 0){
            $data['list_data'] = (object)[];
        }else{
            $data['list_data'] = $doc_data;
        }

        if($user->hasPermission("$menu_dtl_id-view")){
            return $this->ApiJsonSuccessResponse($data,'listing data');
        }else{
            $data['list_data'] = (object)[];
            return $this->ApiJsonSuccessResponse($data,'you have no permission listing data');
        }
        return $this->ApiJsonSuccessResponse($data,'listing data');
    }

    public function GRNList(Request $request, $current_page = null){
    //    dd($request->toArray());
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $user_id = $request->user_id;
        $menu_dtl_id = 23;
        $user = User::where('id',$user_id)->first();
        $current_page = isset($request->page)?$request->page:1;

        $limit = 10;
        $offset = $limit * ($current_page-1);
        $pageNo = $current_page;

        $grnData = DB::table('vw_purc_grn')
                    ->select('grn_id','grn_code','grn_date','supplier_name','grn_remarks','grn_total_net_amount')
                    ->where('grn_type','GRN')
                    ->where('vw_purc_grn.business_id',$business_id)
                    ->where('vw_purc_grn.company_id',$business_id)
                    ->where('vw_purc_grn.branch_id',$branch_id)
                    ->where('vw_purc_grn.grn_device_id',2);

        $total = $grnData->groupBy('grn_id','grn_code','grn_date','supplier_name','grn_remarks','grn_total_net_amount')->orderby('grn_code', 'ASC')->get();

        $total = count($total);

        $data['title'] = 'Goods Received Note';
        $data['typeId'] = $menu_dtl_id;
        $data['permission_create'] = $user->hasPermission("$menu_dtl_id-create")?true:false;
        $data['permission_listing'] = $user->hasPermission("$menu_dtl_id-view")?true:false;
        $permission_edit = $user->hasPermission("$menu_dtl_id-edit")?true:false;
        $data['pageNo'] = $pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages
        $allData = $grnData->skip($offset)->take($limit)->groupBy('grn_id','grn_code','grn_date','supplier_name','grn_remarks','grn_total_net_amount')->orderby('grn_code', 'ASC')->get();
       // dd($allData);
        $doc_data = [];

        foreach ($allData as $doc){
          //  dd($doc);
            $ite = [
                'id'  => $doc->grn_id,
                'row1' => $doc->grn_code,
                'row2' => "Date:".date('Y-m-d', strtotime($doc->grn_date))." ~ Supplier: ".$this->strUcWords($doc->supplier_name),
                'row3' => "Amount:".number_format($doc->grn_total_net_amount,3)." ~ Description:$doc->grn_remarks",
                'action' => [
                    'edit' => $permission_edit,
                    'del' => false,
                    'pdf' => false,
                ],
            ];
            array_push($doc_data,$ite);
        }

        if(count($doc_data) == 0){
            $data['list_data'] = (object)[];
        }else{
            $data['list_data'] = $doc_data;
        }


        if($user->hasPermission("$menu_dtl_id-view")){
            return $this->ApiJsonSuccessResponse($data,'listing data');
        }else{
            $data['list_data'] = (object)[];
            return $this->ApiJsonSuccessResponse($data,'you have no permission listing data');
        }
    }

    public function PurchaseDemandList(Request $request){
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $user_id = $request->user_id;

        $user = User::where('id',$user_id)->first();

        $current_page = isset($request->page)?$request->page:1;

        $limit = 10;
        $offset = $limit * ($current_page-1);
        $pageNo = $current_page;

        $demandData =  DB::table('tbl_purc_demand')
                    ->join('users', 'tbl_purc_demand.salesman_id', '=', 'users.id')
                    ->join('tbl_soft_branch', 'tbl_purc_demand.branch_id', '=', 'tbl_soft_branch.branch_id')
                    ->select('demand_id','demand_no','demand_date','users.name as sales_man_name','tbl_soft_branch.branch_short_name as branch_name')
                    ->where('demand_type','purchase_demand')
                    ->where('tbl_purc_demand.demand_user_id',$user_id)
                    ->where('tbl_purc_demand.business_id',$business_id)
                    ->where('tbl_purc_demand.company_id',$business_id)
                    ->where('tbl_purc_demand.branch_id',$branch_id)
                    ->orderBy('tbl_purc_demand.demand_date','desc')
                    ->orderBy('tbl_purc_demand.demand_no','desc');

        $total = $demandData->count();

        $data['title'] = 'Purchase Demand';
        $data['typeId']=9;$menu_dtl_id=$data['typeId'];
        $data['pageNo']= $pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages

        $data['permission_create'] = $user->hasPermission("$menu_dtl_id-create")?true:false;
        $data['permission_listing'] = $user->hasPermission("$menu_dtl_id-view")?true:false;
        $permission_edit = $user->hasPermission("$menu_dtl_id-edit")?true:false;
        $data['permission_edit'] = $permission_edit;
        $data['permission_print'] = $user->hasPermission("$menu_dtl_id-print")?true:false;;

        // $data['permission_create'] = true;
        // $data['permission_listing'] = true;
        // $permission_edit = true;
        // $data['permission_edit'] = $permission_edit;
        // $data['permission_print'] = true;

        $allData = $demandData->skip($offset)->take($limit)->get();

        $doc_data = [];
        foreach ($allData as $doc){
            $ite = [
                'id'  => $doc->demand_id,
                'row1' => $doc->demand_no,
                'row2' => "Date:".date('Y-m-d', strtotime($doc->demand_date)),
                'row3' => "Demand By: ".$this->strUcWords($doc->sales_man_name)." ~ Branch Name: ".$this->strUcWords($doc->branch_name),
                'action' => [
                    'edit' =>true,
                    'del' =>true,
                    'pdf' =>true,
                ],
            ];
            array_push($doc_data,$ite);
        }

        if(count($doc_data) == 0){
            $data['list_data'] = (object)[];
        }else{
            $data['list_data'] = $doc_data;
        }
        return $this->ApiJsonSuccessResponse($data,'listing data');
    }

    // for opening stock,stock transfer,internal stock transfer
    public function StockList($type,$current_page = null){
        $offset = 0;
        $limit = 10;
        $pageNo = 1;
        if(isset($current_page)){
            $offset = $limit * ($current_page-1);
            $pageNo = $current_page;
        }

        switch ($type){
            case 'opening-stock': {
                $data['title'] = 'Opening Stock';
                $stock_code_type = 'os';
                break;
            }
            case 'stock-transfer': {
                $data['title'] = 'Stock Transfer';
                $stock_code_type = 'st';
                break;
            }
            case 'internal-stock-transfer': {
                $data['title'] = 'Internal Stock Transfer';
                $stock_code_type = 'ist';
                break;
            }
        }

        $stockData = TblInveStock::select('stock_id','stock_code','stock_date','stock_total_qty','stock_total_amount')
                    ->where('stock_code_type',$stock_code_type)
                    ->where(ApiUtilities::currentBCB());
        $total = $stockData->count();

        $data['pageNo']=$pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages
        $data['headings']=array('Code','Date','Total Quantity','Total Amount');
        $data['list_data'] = $stockData->skip($offset)->take($limit)->get();

        return $this->ApiJsonSuccessResponse($data,'listing data');
    }

    public function StockRequestList($current_page = null){
        $offset = 0;
        $limit = 10;
        $pageNo = 1;
        if(isset($current_page)){
            $offset = $limit * ($current_page-1);
            $pageNo = $current_page;
        }

        $requestData = TblPurcDemand::select('demand_id','demand_no','demand_date')
                        ->where(ApiUtilities::currentBCB())
                        ->where('demand_type','stock_request');
        $total = $requestData->count();

        $type = 'stock_request';
        $data['title'] = 'Stock Request';
        $data['pageNo']=$pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages
        $data['headings']=array('Document Code','Document Date');
        $data['list_data'] = $requestData->skip($offset)->take($limit)->get();

        return $this->ApiJsonSuccessResponse($data,'listing data');
    }


    public function TestList($current_page = null){
        $offset = 0;
        $limit = 10;
        $pageNo = 1;
        if(isset($current_page)){
            $offset = $limit * ($current_page-1);
            $pageNo = $current_page;
        }
        $data['pageNo']=$pageNo;
        $data['list_data']  = TblSaleSales::select('sales_code')->skip($offset)->take($limit)->orderBy('created_at','ASC')->get();
        return $this->ApiJsonSuccessResponse($data,'listing data');
    }
}
