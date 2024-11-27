<?php

namespace App\Http\Controllers\Sales;

use App\Models\TblSaleCustomer;
use App\Models\TblSoftFormCases;
use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class MemberInqueryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public static $page_title = 'Member Inquery';
    public static $redirect_url = 'member-inquery';
    public static $menu_dtl_id = '317';

    public function __construct()
    {
        $getStaticPrefix = Utilities::getStaticPrefix(self::$redirect_url);
        $this->current_path = $getStaticPrefix['path'];
        $this->page_form = '/'.self::$redirect_url.'/form';
        $this->page_view = '/'.self::$redirect_url.'/view';
    }

    public function create($id = null)
    {

        
        $data ['formcases']    = TblSoftFormCases::all();
        return view('sales.member_inquery.form',compact('data'));
    }

    
    public function getByCard(Request $request)
    {
        $data = [];
        
        if(!isset($request->SearchCard)){
            return $this->jsonErrorResponse($data, 'Card Number is required', 200);
        }

        $where = "";
        $where .= " and business_id = ".auth()->user()->business_id." ";
        $where .= " and company_id = ".auth()->user()->company_id." ";

        $qry = "SELECT DISTINCT
            CUSTOMER_NAME,
            CUSTOMER_ADDRESS,
            TO_CHAR(EXPIRY_DATE, 'FMMonth DD, YYYY' ) as EXPIRY_DATE,
            MEMBERSHIP_TYPE_ID,
            LOYALTY_BAL
        from 
            VW_MEMBER_INQUIRY
        where 
            card_number = '".$request->SearchCard."'
            $where";

       //dd($qry);
        $items = DB::select($qry);
        
        $data['items'] = $items;

        $paras = [
            'card_number' => $request->SearchCard,
            'branch_ids' => auth()->user()->branch_id,
        ];
        return response()->json(['data'=>$data,'status'=>'success']);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
