<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Api\Product\ProductHelpController;
use App\Http\Controllers\ApiController;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormHelpController extends ApiController
{
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
    public function poHelp(Request $request)
    {
        // dd($request->toArray());
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $current_page = isset($request->page)?$request->page:1;
        $search_key = isset($request->search_key)?$request->search_key:"";

        $limit = 10;
        $offset = $limit * ($current_page-1);
        $pageNo = $current_page;

        $qry = DB::table('vw_purc_po_help')
            ->select('purchase_order_id','supplier_id','purchase_order_code','purchase_order_entry_date','supplier_name')
            ->where('business_id',$business_id)
            ->where('company_id',$business_id)
            ->where('branch_id',$branch_id);

        if(!empty($search_key)){
            $qry = $qry->where(DB::raw('lower(purchase_order_code)'),'like',"%".$this->strLowerTrim($search_key)."%");
        }
        $total = $qry->orderby('purchase_order_code', 'DESC')->get();

        $total = count($total);

        $data['title'] = 'Purchase Order';
        $data['typeId'] = '38';
        $data['pageNo'] = $pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages
        $allData = $qry->skip($offset)->take($limit)->orderby('purchase_order_code', 'ASC')->get();
        $doc_data = [];
        foreach ($allData as $doc){
            $ite = [
                'id'  => $doc->purchase_order_id,
                'code' => $doc->purchase_order_code,
                'name' => '',
                'document_id' => $doc->supplier_id,
                'document_name' => $doc->supplier_name,
            ];
            array_push($doc_data,$ite);
        }
        $data['list_data'] = $doc_data;

        return $this->ApiJsonSuccessResponse($data,'PO Help data list');
    }
    public function supplierHelp(Request $request)
    {
        // dd($request->toArray());
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $current_page = isset($request->page)?$request->page:1;
        $search_key = isset($request->search_key)?$request->search_key:"";

        $limit = 10;
        $offset = $limit * ($current_page-1);
        $pageNo = $current_page;

        $qry = DB::table('tbl_purc_supplier')
            ->select('supplier_id','supplier_code','supplier_name','supplier_phone_1')
            ->where('business_id',$business_id)
            ->where('company_id',$business_id);

        if(!empty($search_key)){
            $str = strtoupper($search_key);
            $replaced_str = str_replace(' ', '%', trim($str));
            $qry = $qry->where(DB::raw('upper(supplier_name)'),'like',"%".$replaced_str."%")
                        ->orWhere(DB::raw('upper(supplier_code)'),'like',"%".$replaced_str."%")
                        ->orWhere(DB::raw('upper(supplier_reference_code)'),'like',"%".$replaced_str."%")
                        ->orderByRaw(
                            "CASE WHEN upper(supplier_name) Like '".$str."' THEN 1 ".
                                    "WHEN upper(supplier_name) Like '".$str."%' THEN 2 ".
                                    "WHEN upper(supplier_name) Like '%".$str."' THEN 4".
                                    "ELSE 3 END, supplier_name"
                        );
        }


        $total = $qry->count();
       // $total = $qry->orderby('supplier_code', 'ASC')->count();

        $data['title'] = 'Supplier';
        $data['typeId'] = '26';
        $data['pageNo'] = $pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages
        $allData = $qry->skip($offset)->take($limit)->get();
        //dd($allData);
        $doc_data = [];
        foreach ($allData as $doc){
            $ite = [
                'id'  => $doc->supplier_id,
                'code' => $doc->supplier_code,
                'name' => $this->strUcWords($doc->supplier_name)
            ];
            array_push($doc_data,$ite);
        }
        $data['list_data'] = $doc_data;

        return $this->ApiJsonSuccessResponse($data,'Supplier Help data');
    }
    public function barcodeHelp(Request $request){
        // dd($request->toArray());
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $current_page = isset($request->page)?$request->page:1;
        $search_key = isset($request->search_key)?$request->search_key:"";

        $limit = 10;
        $offset = $limit * ($current_page-1);
        $pageNo = $current_page;

        $qry = DB::table('vw_purc_product_barcode_help')
            ->where('business_id',$business_id)
            ->where('company_id',$business_id)
            ->select('*');


        if(!empty($search_key)){
            $str = strtoupper($search_key);
            $replaced_str = str_replace(' ', '%', trim($str));
            $qry = $qry->where(DB::raw('upper(product_barcode_barcode)'),'like',"%".$replaced_str."%")
                ->orWhere(DB::raw('upper(product_name)'),'like',"%".$replaced_str."%")
                ->orderByRaw(
                    "CASE WHEN upper(product_name) Like '".$str."' THEN 1 ".
                    "WHEN upper(product_name) Like '".$str."%' THEN 2 ".
                    "WHEN upper(product_name) Like '%".$str."' THEN 4".
                    "ELSE 3 END, product_name"
                );
        }

        $total = $qry->count();

        $data['title'] = 'Barcode List';
        $data['typeId'] = '6';
        $data['pageNo'] = $pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages
        $allData = $qry->skip($offset)->take($limit)->get();
       // dd($allData);
        $doc_data = [];
        foreach ($allData as $doc){
            $ite = [
                'product_id'  =>$doc->product_id,
                'product_barcode_id' => $doc->product_barcode_id,
                'uom_id' => $doc->uom_id,
                'product_name' => $this->strUcWords($doc->product_name),
                'product_arabic_name' => $doc->product_arabic_name,
                'product_barcode' => trim($doc->product_barcode_barcode),
                'uom_name' => $doc->uom_name,
                'product_barcode_packing' => $doc->product_barcode_packing,
            ];
            array_push($doc_data,$ite);
        }
        $data['list_data'] = $doc_data;

        return $this->ApiJsonSuccessResponse($data,'Barcode Help data');
    }
    public function LocationsByStoreHelp(Request $request)
    {
        // dd($request->toArray());
        $branch_id = $request->branch_id;
        $store_id = $request->store_id;
        $current_page = isset($request->page)?$request->page:1;
        $search_key = isset($request->search_key)?$request->search_key:"";

        $limit = 10;
        $offset = $limit * ($current_page-1);
        $pageNo = $current_page;

        $qry = DB::table('vw_inve_display_location')
            ->select('display_location_id','display_location_name_string')
            ->where('store_id',$store_id)
            ->where('branch_id',$branch_id);

        if(!empty($search_key)){
            $str = strtoupper($search_key);
            $replaced_str = str_replace(' ', '%', trim($str));
            $qry = $qry->where(DB::raw('upper(display_location_name_string)'),'like',"%".$replaced_str."%")
                ->orderByRaw(
                    "CASE WHEN upper(display_location_name_string) Like '".$str."' THEN 1 ".
                    "WHEN upper(display_location_name_string) Like '".$str."%' THEN 2 ".
                    "WHEN upper(display_location_name_string) Like '%".$str."' THEN 4".
                    "ELSE 3 END, display_location_name_string"
                );
        }


        $total = $qry->count();
        // $total = $qry->orderby('supplier_code', 'ASC')->count();

        $data['title'] = 'Display Location';
        $data['typeId'] = '30';
        $data['pageNo'] = $pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages
        $allData = $qry->skip($offset)->take($limit)->get();
        //dd($allData);
        $doc_data = [];
        foreach ($allData as $doc){
            $ite = [
                'id'  => $doc->display_location_id,
                'name' => $this->strUcWords($doc->display_location_name_string)
            ];
            array_push($doc_data,$ite);
        }
        $data['list_data'] = $doc_data;

        return $this->ApiJsonSuccessResponse($data,'Display Location Help data');
    }

}
