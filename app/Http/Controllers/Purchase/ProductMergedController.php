<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Validator;

class ProductMergedController extends Controller
{
    public static $page_title = 'Product Merged';
    public static $redirect_url = 'product-merged';
    public static $menu_dtl_id = '294';

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
    public function create()
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;

        $data['permission'] = self::$menu_dtl_id.'-create';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Do Merge';

        return view('purchase.product_merged.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'f_product_id' => 'required|max:100',
            'f_product_barcode_id' => 'required|max:100',
            'm_product_id' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        $f_product_id = $request->f_product_id;
        $f_product_barcode_id = $request->f_product_barcode_id;
        $m_product_id = $request->m_product_id;

       /* if($f_product_id == $m_product_id){
            return $this->jsonErrorResponse($data, 'Both Product must be separate', 200);
        }*/
        DB::beginTransaction();
        try{
            $qry = "select * from vw_all_column_data
                    where (table_name like 'TBL_%' or table_name in('DRAFT_PURC_PURCHASE_ORDER_DTL','RPT_IVEN_BATCH_EXPIRY'))
                    and (upper(column_name) = 'PRODUCT_ID' OR upper(column_name) = 'PRODUCT_BARCODE_ID') order by table_name,column_name";
            $lists = DB::select($qry);

            $sort_lists = [];
            foreach ($lists as $list){
                $sort_lists[$list->table_name][$list->column_name] = $list->column_name;
            }
            $table_lists = [];
            foreach ($sort_lists as $k=>$sort_list){
                if(isset($sort_list['PRODUCT_ID']) && isset($sort_list['PRODUCT_BARCODE_ID'])){
                    $table_lists[] = $k;
                }

            }
            foreach ($table_lists as $tbl_name){
                $statement = "update $tbl_name
                        set product_id = $m_product_id
                        where product_id = $f_product_id and product_barcode_id = $f_product_barcode_id";
                DB::statement($statement);
            }

        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();

        return $this->jsonSuccessResponse($data, 'Product successfully Merged.', 200);
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
