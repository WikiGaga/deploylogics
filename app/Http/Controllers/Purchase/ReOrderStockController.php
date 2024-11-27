<?php

namespace App\Http\Controllers\Purchase;

use App\Models\TblSoftBranch;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcProductFOC;
use App\Models\TblPurcProductBarcode;
use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

// db and Validator
//use App\Models\TblReOrderStockAnalysis;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReOrderStockController extends Controller
{
    public static $page_title = 'Re-Order Stock Analysis';
    public static $redirect_url = 're-order-stock';
    public static $menu_dtl_id = '';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;

        $data['permission'] = self::$menu_dtl_id.'-create';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['create'] = '';
        $data['page_data']['action'] = 'Generate Purchase Order';
        $data['branches'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();


        return view('purchase.re_order_stock.form',compact('data'));
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
            'supplier_id' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{


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
            // $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function generatePurchaseOrder(Request $request){

        $data = [];
        if(!isset($request->barcodes) && count($request->barcodes) == 0){
            return $this->jsonErrorResponse($data, 'Check minimum one barcode is checked', 200);
        }

        session(['dataGenerateBarcodes'=>$request->barcodes]);

        return response()->json(['status'=>'success']);

        return $this->jsonSuccessResponse($data, '', 200);
    }

    public function getAccData(Request $request){

        $data = [];
        $supplier_id = $request->supplier_id;
       /* $exitsAcc = TblAccCoa::where('chart_account_id',$supplier_id)->where(Utilities::currentBC())->exists();
        if (empty($exitsAcc)) {
            return $this->jsonErrorResponse($data,"Supplier Code not correct",200);
        }
        */
        $from_date = date('Y-m-d', strtotime($request->from_date));
        $to_date = date('Y-m-d', strtotime($request->to_date));
        if(!isset($request->branch_name)){
            return $this->jsonErrorResponse($data, 'Branch is required', 200);
        }

        $branch_ids = $request->branch_name;
        $leaddays = 1;
        if($request->leaddays != ""){
          $leaddays = $request->leaddays;
        }


        $aging_days0 = 0;
        $aging_days1 = 0;
        $aging_days2 = 0;
        $aging_days3 = 0;
        $aging_days4 = 0;


        $aging_fdays0 = 0;
        $aging_fdays1 = 0;
        $aging_fdays2 = 0;
        $aging_fdays3 = 0;


        $days = 1;

        if($request->aging_days0 > 0){
          $aging_fdays0 = $request->aging_days0+1 ;
          $aging_days0 = $request->aging_days0 ;
        }
        if($request->aging_days1 > 0){
          $aging_fdays1 = $request->aging_days1+1;
          $aging_days1 = $request->aging_days1;
        }
        if($request->aging_days2 > 0){
          $aging_fdays2 = $request->aging_days2+1;
          $aging_days2 = $request->aging_days2;
        }
        if($request->aging_days3 > 0){
          $aging_fdays3 = $request->aging_days3+1;
          $aging_days3 = $request->aging_days3;
        }
        if($request->aging_days4 > 0){
          $aging_days4 = $request->aging_days4;
        }

        $stock_filter = "";

        if($request->stock_filter == "shortage"){
          $stock_filter = " WHERE KAKA.RE_ORDER_SUGGEST > 0";
        }
        if($request->stock_filter == "excess"){
          $stock_filter = " WHERE KAKA.RE_ORDER_SUGGEST < 0";
        }

        $where = "";

        $where .= " and business_id = ".auth()->user()->business_id." ";
        $where .= " and company_id = ".auth()->user()->company_id." ";
        //$where .= " and branch_id = ".$branch_ids." ";

        $qry = "SELECT 
        PRODUCT_BARCODE_BARCODE,
        PRODUCT_BARCODE_ID,
        PRODUCT_ID,
        PRODUCT_NAME,
        PACKAGE_NAME,
        SUPPLIER_NAME,
        TBL_PURC_GRN_DTL_NET_TP,
        TBL_PURC_GRN_DTL_QUANTITY,
        GRN_DATE,
        SALE_LAST_PI,
        SALE_DAYS_AFTER_LAST_PI,
        LAST_SALE_DATE,
        LAST_AUDIT_DATE,
        REORDER_AMOUNT,
        RE_ORDER_QTY,
        RE_ORDER_SUGGEST,
        CURRENT_STOCK,
        EXPIRY_DATE,
        STOCK_IN_FROM_BRANCH,
        DAYS_WITH_STOCK,
        CASE
          WHEN RE_ORDER_SUGGEST > 0 
          THEN 'Short' 
          WHEN RE_ORDER_SUGGEST < 0 
          THEN 'Excess' 
          ELSE 'Balance' 
        END AS STOCK_STATUS,
        PER_DAY,
        QTY1,
        QTY2,
        QTY3,
        QTY4,
        QTY5,
        AGING_GRAND_TOTAL,
        COMP_BRANCH,
        GROUP_ITEM_PARENT_NAME,
        GROUP_ITEM_NAME,
        BRAND_NAME,
        PRODUCT_BARCODE_PACKING,
        UOM_NAME 
        from (
    
        SELECT 
        PRODUCT_BARCODE_BARCODE,
          PRODUCT_BARCODE_ID,
        PRODUCT_ID,
        PRODUCT_NAME,
        0 PACKAGE_NAME,
        SUPPLIER_NAME,
        TBL_PURC_GRN_DTL_NET_TP,
        TBL_PURC_GRN_DTL_QUANTITY,
        GRN_DATE,
        0 SALE_LAST_PI,
        0 SALE_DAYS_AFTER_LAST_PI,
        SALES_DATE LAST_SALE_DATE,
        '' LAST_AUDIT_DATE,
        NVL (TBL_PURC_GRN_DTL_NET_TP,0)  * ( (
          ROUND(
            (
              NVL (QTY1, 0) + NVL (QTY2, 0) + NVL (QTY3, 0) + NVL (QTY4, 0) + NVL (QTY5, 0)
            ) / ".$leaddays.",
            0
          ) * ".$leaddays."
        ) - (NVL (CURRENT_STOCK, 0)))  
        REORDER_AMOUNT,
        (
        ROUND(
          (
            NVL (QTY1, 0) + NVL (QTY2, 0) + NVL (QTY3, 0) + NVL (QTY4, 0) + NVL (QTY5, 0)
          ) / ".$leaddays.",
          0
        ) * ".$leaddays."
        ) - (NVL (CURRENT_STOCK, 0)) RE_ORDER_QTY,
        (
        ROUND(
          (
            NVL (QTY1, 0) + NVL (QTY2, 0) + NVL (QTY3, 0) + NVL (QTY4, 0) + NVL (QTY5, 0)
          ) / ".$leaddays.",
          0
        ) * ".$leaddays."
        ) - (NVL (CURRENT_STOCK, 0)) RE_ORDER_SUGGEST,
        CURRENT_STOCK,
        '' EXPIRY_DATE,
        0 STOCK_IN_FROM_BRANCH,
        CASE
        WHEN ROUND(
          (
            NVL (QTY1, 0) + NVL (QTY2, 0) + NVL (QTY3, 0) + NVL (QTY4, 0) + NVL (QTY5, 0)
          ) / ".$leaddays.",
          0
        ) > 0 
        THEN ROUND(
          NVL (CURRENT_STOCK, 0) / ROUND(
            (
              NVL (QTY1, 0) + NVL (QTY2, 0) + NVL (QTY3, 0) + NVL (QTY4, 0) + NVL (QTY5, 0)
            ) / ".$leaddays.",
            0
          ),
          0
        ) 
        ELSE 0 
        END AS DAYS_WITH_STOCK,
        ''  STOCK_STATUS,
        ROUND(
        (
          NVL (QTY1, 0) + NVL (QTY2, 0) + NVL (QTY3, 0) + NVL (QTY4, 0) + NVL (QTY5, 0)
        ) / ".$leaddays.",
        0
        ) PER_DAY,
        NVL (QTY1, 0) QTY1,
        NVL (QTY2, 0) QTY2,
        NVL (QTY3, 0) QTY3,
        NVL (QTY4, 0) QTY4,
        NVL (QTY5, 0) QTY5,
        NVL (QTY1, 0) + NVL (QTY2, 0) + NVL (QTY3, 0) + NVL (QTY4, 0) + NVL (QTY5, 0) AGING_GRAND_TOTAL,
        '' COMP_BRANCH,
        GROUP_ITEM_PARENT_NAME,
        GROUP_ITEM_NAME,
        BRAND_NAME,
        PRODUCT_BARCODE_PACKING,
        UOM_NAME 
        FROM
      (
            SELECT DISTINCT 
            MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
            MAX(PRODUCT_BARCODE_ID) PRODUCT_BARCODE_ID,
            PROD.PRODUCT_ID,
            PRODUCT_NAME,
            max(UOM_NAME) UOM_NAME ,
            1  PRODUCT_BARCODE_PACKING, 
            BRAND_NAME,
            GROUP_ITEM_NAME,
            GROUP_ITEM_PARENT_NAME,
            (SELECT 
              SUPPLIER_NAME 
            FROM
              VW_PURC_GRN GRN 
            WHERE GRN.PRODUCT_ID = PROD.PRODUCT_ID 
              AND UPPER(GRN_TYPE) = 'GRN' 
              and GRN.BRANCH_ID = ".$branch_ids."
            ORDER BY grn_code DESC,
              CREATED_AT DESC FETCH FIRST 1 ROWS ONLY) SUPPLIER_NAME,
            (SELECT 
              TBL_PURC_GRN_DTL_NET_TP 
            FROM
              VW_PURC_GRN GRN 
            WHERE GRN.PRODUCT_ID = PROD.PRODUCT_ID 
              AND UPPER(GRN_TYPE) = 'GRN' 
              and GRN.BRANCH_ID = ".$branch_ids."
            ORDER BY grn_code DESC,
              CREATED_AT DESC FETCH FIRST 1 ROWS ONLY) TBL_PURC_GRN_DTL_NET_TP,
            (SELECT 
              TBL_PURC_GRN_DTL_QUANTITY 
            FROM
              VW_PURC_GRN GRN 
            WHERE GRN.PRODUCT_ID = PROD.PRODUCT_ID 
              AND UPPER(GRN_TYPE) = 'GRN' 
              and GRN.BRANCH_ID = ".$branch_ids."
            ORDER BY grn_code DESC,
              CREATED_AT DESC FETCH FIRST 1 ROWS ONLY) TBL_PURC_GRN_DTL_QUANTITY,
            (SELECT 
              GRN_DATE 
            FROM
              VW_PURC_GRN GRN 
            WHERE GRN.PRODUCT_ID = PROD.PRODUCT_ID 
              AND UPPER(GRN_TYPE) = 'GRN' 
              and GRN.BRANCH_ID = ".$branch_ids."
            ORDER BY grn_code DESC,
              CREATED_AT DESC FETCH FIRST 1 ROWS ONLY) GRN_DATE,
            (
            SELECT 
              SALE_DTL.SALES_DATE 
            FROM
              TBL_SALE_SALES_DTL SALE,
              TBL_SALE_SALES SALE_DTL 
            WHERE SALE.SALES_ID = SALE_DTL.SALES_ID 
              AND SALE.PRODUCT_ID = PROD.PRODUCT_ID 
              and SALE.BRANCH_ID = ".$branch_ids."
            ORDER BY SALE.CREATED_AT DESC FETCH FIRST 1 ROWS ONLY) SALES_DATE,
            (SELECT 
              SUM(QTY_BASE_UNIT_VALUE) STOCK_QTY 
            FROM
              VW_PURC_STOCK_DTL STOCK 
            WHERE STOCK.PRODUCT_ID = PROD.PRODUCT_ID
              and STOCK.BRANCH_ID = ".$branch_ids."
              AND STOCK.DOCUMENT_DATE <= TO_DATE ('".$to_date."', 'yyyy/mm/dd')
            ) CURRENT_STOCK,
            (
              SELECT  
               CASE
                  WHEN INTERVAL_1 > 0 
                  THEN SALES_DTL_QUANTITY
                 
                  ELSE  0
                END AS QTY1 
                FROM
                   (
                    
                    SELECT 
                    ".$aging_days0." as INTERVAL_1 ,
                      SUM(SALES_DTL_QUANTITY)  SALES_DTL_QUANTITY
            FROM
              TBL_SALE_SALES_DTL SALE,
              TBL_SALE_SALES SALE_DTL 
            WHERE SALE.SALES_ID = SALE_DTL.SALES_ID 
              AND SALE.PRODUCT_ID = PROD.PRODUCT_ID 
              and SALE.BRANCH_ID = ".$branch_ids."
              AND SALES_dATE BETWEEN TO_DATE ('".$from_date."', 'yyyy/mm/dd') 
              AND TO_DATE ('".$from_date."', 'yyyy/mm/dd') + INTERVAL '".$aging_days0."' DAY(4))) QTY1,
              (
                SELECT  
                 CASE
                    WHEN INTERVAL_2 > 0 
                    THEN SALES_DTL_QUANTITY
                   
                    ELSE  0
                  END AS QTY2 
                  FROM
                     (
                SELECT 
                ".$aging_days1." as INTERVAL_2 ,
                 SUM(SALES_DTL_QUANTITY)  SALES_DTL_QUANTITY 
             FROM
              TBL_SALE_SALES_DTL SALE,
              TBL_SALE_SALES SALE_DTL 
            WHERE SALE.SALES_ID = SALE_DTL.SALES_ID 
              AND SALE.PRODUCT_ID = PROD.PRODUCT_ID 
              and SALE.BRANCH_ID = ".$branch_ids."
              AND SALES_dATE BETWEEN TO_DATE ('".$from_date."', 'yyyy/mm/dd') + INTERVAL '".$aging_fdays0."' DAY(4) 
              AND TO_DATE ('".$from_date."', 'yyyy/mm/dd') + INTERVAL '".$aging_days1."' DAY(4))) QTY2,
              (
                SELECT  
                 CASE
                    WHEN INTERVAL_3 > 0 
                    THEN SALES_DTL_QUANTITY
                   
                    ELSE  0
                  END AS QTY3 
                  FROM
                     (
                SELECT 
                ".$aging_days2." as INTERVAL_3 ,
                        SUM(SALES_DTL_QUANTITY)  SALES_DTL_QUANTITY 
                      FROM
              TBL_SALE_SALES_DTL SALE,
              TBL_SALE_SALES SALE_DTL 
            WHERE SALE.SALES_ID = SALE_DTL.SALES_ID 
              AND SALE.PRODUCT_ID = PROD.PRODUCT_ID 
              and SALE.BRANCH_ID = ".$branch_ids."
              AND SALES_dATE BETWEEN TO_DATE ('".$from_date."', 'yyyy/mm/dd') + INTERVAL '".$aging_fdays1."' DAY(4) 
              AND TO_DATE ('".$from_date."', 'yyyy/mm/dd') + INTERVAL '".$aging_days2."' DAY(4))) QTY3,
              (
                SELECT  
                 CASE
                    WHEN INTERVAL_4 > 0 
                    THEN SALES_DTL_QUANTITY
                   
                    ELSE  0
                  END AS QTY4
                  FROM
                     (
                SELECT 
                ".$aging_days3." as INTERVAL_4 ,
                        SUM(SALES_DTL_QUANTITY)  SALES_DTL_QUANTITY 
                      FROM
              TBL_SALE_SALES_DTL SALE,
              TBL_SALE_SALES SALE_DTL 
            WHERE SALE.SALES_ID = SALE_DTL.SALES_ID 
              AND SALE.PRODUCT_ID = PROD.PRODUCT_ID 
              and SALE.BRANCH_ID = ".$branch_ids."
              AND SALES_dATE BETWEEN TO_DATE ('".$from_date."', 'yyyy/mm/dd') + INTERVAL '".$aging_fdays2."' DAY(4) 
              AND TO_DATE ('".$from_date."', 'yyyy/mm/dd') + INTERVAL '".$aging_days3."' DAY(4))) QTY4,
              (
                SELECT  
                 CASE
                    WHEN INTERVAL_5 > 0 
                    THEN SALES_DTL_QUANTITY
                   
                    ELSE  0
                  END AS QTY5 
                  FROM
                     (
                SELECT 
                ".$aging_days4." as INTERVAL_5 ,
                        SUM(SALES_DTL_QUANTITY)  SALES_DTL_QUANTITY 
                      FROM
              TBL_SALE_SALES_DTL SALE,
              TBL_SALE_SALES SALE_DTL 
            WHERE SALE.SALES_ID = SALE_DTL.SALES_ID 
              AND SALE.PRODUCT_ID = PROD.PRODUCT_ID 
              and SALE.BRANCH_ID = ".$branch_ids."
              AND SALES_dATE BETWEEN TO_DATE ('".$from_date."', 'yyyy/mm/dd') + INTERVAL '".$aging_fdays3."' DAY(4) 
              AND TO_DATE ('".$from_date."', 'yyyy/mm/dd') + INTERVAL '".$aging_days4."' DAY(4))) QTY5 
            FROM
              VW_PURC_PRODUCT_BARCODE PROD  ,  
              VW_PURC_PRODUCT_FOC_PROD_WISE PROD_SUP
            WHERE  PROD.PRODUCT_ID = PROD_SUP.PRODUCT_ID
              AND PROD_SUP.SUPPLIER_ID = $supplier_id 
              $where
              GROUP BY PRODUCT_NAME, 
                BRAND_NAME,
                GROUP_ITEM_NAME,
                GROUP_ITEM_PARENT_NAME,
                PROD.PRODUCT_ID
        ) GAGA
        ) KAKA
        $stock_filter";

       //dd($qry);
        $items = DB::select($qry);
        
        $data['items'] = $items;

       // dd($data['items']);

        $paras = [
            'supplier_id' => $supplier_id,
            'created_at' => $from_date,
            'branch_ids' => $branch_ids,
        ];
        return response()->json(['data'=>$data,'status'=>'success']);
    }
    public function delSupData(Request $request)
    {
      $data = [];
      $supplier_id = $request->supplier_id;

      if(!isset($request->barcodes) && count($request->barcodes) == 0){
        return $this->jsonErrorResponse($data, 'Check minimum one barcode is checked', 200);
      }

      $TblPurcProductFOCExists = TblPurcProductFOC::where('supplier_id',$supplier_id)->exists();
      if($TblPurcProductFOCExists)
      {
        foreach($request->barcodes as $val)
        {
          $barcode = TblPurcProductBarcode::where('product_barcode_barcode','like', $val['barcodes'])->first();
          $qrydel = TblPurcProductFOC::where('supplier_id',$supplier_id)->where('product_id',$barcode->product_id)->delete();

          return $this->jsonErrorResponse($data, trans('message.delete'), 200);
        }
      }else{
        return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
      }
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
