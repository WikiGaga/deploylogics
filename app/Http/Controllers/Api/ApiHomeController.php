<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Library\Utilities;
use App\Models\TempPro;
use App\Models\TempProDtl;
use App\Models\TempTestProduct;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Session;
use Auth;
use Illuminate\Validation\ValidationException;

class ApiHomeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;

        $offset = 0;
        $limit = 10;
        $pageNo = 1;
        if(isset($current_page)){
            $offset = $limit * ($current_page-1);
            $pageNo = $current_page;
        }

        $grnData = DB::table('vw_purc_grn')
            ->select('grn_id','grn_code','grn_date','supplier_name')
            ->where('grn_type','GRN')
            ->where('vw_purc_grn.business_id',$business_id)
            ->where('vw_purc_grn.company_id',$business_id)
            ->where('vw_purc_grn.branch_id',$branch_id);

        $total = $grnData->groupBy('grn_id','grn_code','grn_date','supplier_name')->orderby('grn_code', 'ASC')->get();

        $total = count($total);

        $data['title'] = 'Goods Received Note';
        $data['pageNo'] = $pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages
        $allData = $grnData->skip($offset)->take($limit)->groupBy('grn_id','grn_code','grn_date','supplier_name')->orderby('grn_code', 'ASC')->get();

        $doc_data = [];
        foreach ($allData as $doc){
            $ite = [
                'id'  => $doc->grn_id,
                'row1' => $doc->grn_code,
                'row2' => "Date:".date('Y-m-d', strtotime($doc->grn_date))." ~ Supplier: ".$this->strUcWords($doc->supplier_name),
                'row3' => "",
                'action' => [
                    'edit' =>true,
                    'del' =>true,
                    'pdf' =>true,
                ],
            ];
            array_push($doc_data,$ite);
        }
        $data['list_data'] = $doc_data;

        return $this->ApiJsonSuccessResponse($data,'Test Api data');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $data = (object)[];
        return $this->ApiJsonSuccessResponse($data,'empty dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {

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
