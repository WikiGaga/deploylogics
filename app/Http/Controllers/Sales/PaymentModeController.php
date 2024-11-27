<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblDefiPaymentType;
use App\Models\TblSoftPOSTerminal;
use App\Models\TblSaleSales;
use App\Models\ViewSaleSalesInvoice;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcodePurchRate;
use Illuminate\Http\Request;
use App\Models\TblSoftBranch;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblDefiStore;
use App\Models\TblAccCoa;
use App\Models\User;
use App\Models\TblDefiMerchant;
use App\Models\TblSaleCustomer;
use App\Models\TblDefiBank;
use App\Models\ViewSaleCustomer;
use App\Models\TblSoftPosInvoiceHeadings;

// db and Validator
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Exception;
// db and Validator
use Illuminate\Validation\Rule;
use Session;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentModeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Sale Payment Mode';
    public static $redirect_url = 'sale-payment-mode';
    public static $menu_dtl_id = '41';

    public function index(Request $request)
    {
        $data = [];
        $data['title'] = self::$page_title;
        $data['case'] = self::$redirect_url;
        $data['form-action'] = '/sale-payment-mode';
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['table_id'] = 'sales_id';
        $data['data_url'] = action('Sales\PaymentModeController@index');

        $data['table_columns'] = [
            "sales_code" => [
                'title' => "Inve NO",
                'type' => 'string',
            ],
            "sales_date" => [
                'title' => "Inve Date",
                'type' => 'date',
            ],
            "fbr_invoice_no" => [
                'title' => "Fbr Inv#",
                'type' => 'string',
            ],
            "customer_name" => [
                'title' => "Client",
                'type' => 'string',
            ],
            "sales_net_amount" => [
                'title' => "Amount",
                'type' => 'string',
            ],
            "sales_sales_man_name" => [
                'title' => "User Name",
                'type' => 'string',
            ],
            "cash_amount" => [
                'title' => "Cash",
                'type' => 'string',
            ],
            "visa_amount" => [
                'title' => "Visa",
                'type' => 'string',
            ],
            "terminal_name" => [
                'title' => "Counter",
                'type' => 'string',
            ],
            "sales_remarks" => [
                'title' => "Remarks",
                'type' => 'string',
            ],
            "created_at" => [
                'title' => "Entry Date",
                'type' => 'datetime',
            ],
            "updated_at" => [
                'title' => "Edit Date",
                'type' => 'datetime',
            ],
        ];

        if($request->ajax()){
            $tbl_1 = " tbl_1";
            $table = " vw_sale_sales_invoice $tbl_1 ";
            $columns = "$tbl_1.sales_id";
            foreach ($data['table_columns'] as $lk => $table_columns){
                $columns .= ','.$tbl_1.'.'.$lk;
            }
            $today = date('d/m/Y');
            $time_from = '12:00:00 am';
            $time_to = '11:59:59 pm';
            $global_filter_bollean = false;
            $result = [];
            if (isset($request['query']['globalFilters'])) {
                $globalFilters = $request['query']['globalFilters'];
                $where = " where $tbl_1.branch_id = ".$globalFilters['branch_id'];
                $global_search = false;
                if(isset($globalFilters['global_search']) && !empty($globalFilters['global_search'])){
                    $generalSearch = str_replace(" " , "%" , $globalFilters['global_search']);
                    $generalSearch = strtolower($generalSearch);
                    $textSearch = "";
                    foreach ($data['table_columns'] as $tkey=>$table_columns){
                        if($table_columns['type'] == 'string'){
                            if($tkey == 'discount_status'){
                                $textSearch .= " lower($tbl_1.is_active_status) like '%$generalSearch%' OR ";
                                $textSearch .= " lower($tbl_1.expire_status) like '%$generalSearch%' OR ";
                            }else{
                                $textSearch .= " lower($tbl_1.".$tkey.") like '%$generalSearch%' OR ";
                            }
                        }
                    }
                    if(!empty($textSearch)){
                        $textSearch = rtrim($textSearch,' OR');
                        $where .= "and ( $textSearch ) ";
                    }

                    $from = "TO_DATE('01/01/2010 ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $where .=  ' AND ('.$tbl_1.'.created_at between '. $from .' AND '. $to.') ';

                    $global_search = true;
                    $global_filter_bollean = true;
                }

                if(isset($globalFilters['date']) && $global_search == false){
                    $date = $globalFilters['date'];
                    if(!empty($date)){
                        if(isset($globalFilters['time_from'])){
                            $time_from = date('h:i:s a',strtotime($globalFilters['time_from']));
                        }
                        if(isset($globalFilters['time_to'])){
                            $time_to = date('h:i:s a',strtotime($globalFilters['time_to']));
                        }
                        if($date == 'today'){
                            $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        $yesterday = date('d/m/Y',strtotime(date('d-m-Y').' -1 day'));
                        if($date == 'yesterday'){
                            $from = "TO_DATE('".$yesterday." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        $l7days = date('d/m/Y',strtotime(date('d-m-Y').' -7 day'));
                        if($date == 'last_7_days'){
                            $from = "TO_DATE('".$l7days." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        $l30days = date('d/m/Y',strtotime(date('d-m-Y').' -30 day'));
                        if($date == 'last_30_days'){
                            $from = "TO_DATE('".$l30days." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        if($date == 'yesterday'){
                            $to = "TO_DATE('".$yesterday." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }else{
                            $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        if($date == 'custom_date'){
                            if(isset($globalFilters['from']) && isset($globalFilters['to'])){
                                $from = "TO_DATE('".date('d/m/Y',strtotime($globalFilters['from']))." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $to = "TO_DATE('".date('d/m/Y',strtotime($globalFilters['to']))." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }else{
                                $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }
                        }
                        if($date == 'all'){
                            $from = "TO_DATE('01/01/2010 ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                        }
                        $where .=  ' AND ('.$tbl_1.'.created_at between '. $from .' AND '. $to.') ';
                    }
                    $global_filter_bollean = true;
                }

                if(isset($globalFilters['inline'])){
                    $inline_filter = $globalFilters['inline'];
                    $inline_where = "";
                    if(!empty($inline_filter)){
                        foreach ($data['table_columns'] as $tkey=>$table_columns){
                            if(isset($inline_filter[$tkey]) && !empty($inline_filter[$tkey])){
                                if($table_columns['type'] == 'string'){
                                    if($tkey == 'discount_status'){
                                        $inline_where .= " and (lower($tbl_1.is_active_status) like '%".strtolower($inline_filter[$tkey])."%' OR ";
                                        $inline_where .= " lower($tbl_1.expire_status) like '%".strtolower($inline_filter[$tkey])."%' ) ";
                                    }else{
                                        $inline_where .= " and lower($tbl_1.$tkey) like '%".strtolower($inline_filter[$tkey])."%'";
                                    }
                                }
                                if(in_array($table_columns['type'],['date','datetime'])){
                                    $created_at = date('d/m/Y',strtotime($inline_filter[$tkey]));
                                    $d_from = "TO_DATE('".$created_at." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                    $d_to = "TO_DATE('".$created_at." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                                    $inline_to_date = "$d_from and $d_to";
                                    $inline_where .= " and ( $tbl_1.$tkey between ".$inline_to_date.") ";
                                }
                            }
                        }
                    }
                    $where .= $inline_where;
                }


                if(!$global_filter_bollean){
                    $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $where .=  ' AND ('.$tbl_1.'.created_at between '. $from .' AND '. $to.') ';
                }

                // custom filter
                if(isset($globalFilters['posted'])){
                    if($globalFilters['posted'] == 'posted'){
                        //$where .=  ' AND '.$tbl_1.'.posted = 1';
                        $fbr_inv = "fbr_invoice_no <> 'Not Available'";
                        $where .=  ' AND '.$tbl_1.'.'.$fbr_inv.'';
                    }
                    if($globalFilters['posted'] == 'unposted'){
                       // $where .=  ' AND '.$tbl_1.'.posted = 0';
                       $fbr_inv = "fbr_invoice_no = 'Not Available'";
                       $fbr_inv1 = "fbr_invoice_no = ''";
                       $fbr_inv2 = "fbr_invoice_no is null";
                        $where .=  ' AND ('.$tbl_1.'.'.$fbr_inv.' OR '.$tbl_1.'.'.$fbr_inv1.' OR '.$tbl_1.'.'.$fbr_inv2.' )';
                    }
                }
                if(isset($globalFilters['payment_mode']) && !empty($globalFilters['payment_mode'])){
                    $where .=  ' AND '.$tbl_1.'.payment_mode_id = '.$globalFilters['payment_mode'];
                }
                if( isset($globalFilters['net_amount_filter']) && !empty($globalFilters['net_amount_filter'])
                    && isset($globalFilters['net_amount_filter_val']) && !empty($globalFilters['net_amount_filter_val'])
                ){
                    $where .=  ' AND '.$tbl_1.'.sales_net_amount '.$globalFilters['net_amount_filter'].' '.$globalFilters['net_amount_filter_val'];
                }

                $sortDirection  = ($request->has('sort.sort') && $request->filled('sort.sort'))? $request->input('sort.sort') : 'desc';
                $sortField  = ($request->has('sort.field') && $request->filled('sort.field'))? $request->input('sort.field') : 'created_at';
                $meta    = [];
                $page  = ($request->has('pagination.page') && $request->filled('pagination.page'))? $request->input('pagination.page') : 1;
                $perpage  = ($request->has('pagination.perpage') && $request->filled('pagination.perpage'))? $request->input('pagination.perpage') : -1;

                $total  = DB::selectOne("select  count(DISTINCT ".$data['table_id'].") count from $table $where");
                $total  = isset($total->count)?$total->count:0;
                // $perpage 0; get all data
                if ($perpage > 0) {
                    $pages  = ceil($total / $perpage); // calculate total pages
                    $page   = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                    $page   = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                    $offset = ($page - 1) * $perpage;
                    if ($offset < 0) {
                        $offset = 0;
                    }
                }
                $groupby = 'group by '.$columns.','.$tbl_1.'.created_at';
                $orderby = " ORDER BY $tbl_1.$sortField $sortDirection ";
                $limit = "OFFSET $offset ROWS FETCH NEXT $perpage ROWS ONLY";
                $qry = "select $columns from $table $where $groupby $orderby $limit";
                //dd($qry);
                $entries = DB::select($qry);

                $meta = [
                    'page'    => $page,
                    'pages'   => $pages,
                    'perpage' => $perpage,
                    'total'   => $total
                ];

                $result = [
                    'meta' => $meta + [
                            'sort'  => $sortDirection,
                            'field' => $sortField,
                        ],
                    'data' => $entries,
                ];
            }

            return response()->json($result);
        }

        $data['branch'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
        $data['payment_type'] = TblDefiPaymentType::where(Utilities::currentBC())->get();

        return view('sales.sale_payment_mode.list',compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'sale-payment-mode';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.$data['form_type'];
        $data['page_data']['create'] = '';
        $data['invoice_menu_id'] = '41';

        if(isset($id)){
            if(TblSaleSales::where('sales_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';

                $qry = TblSaleSales::with('dtls','customer_view','expense','SO')->where('sales_id',$id);
                if($data['form_type'] == 'sale-payment-mode'){
                    $qry = $qry->where('sales_type','POS');
                    //$data['page_data']['action'] = '';
                }

                $data['current'] = $qry->first();
                $data['document_code'] = $data['current']->sales_code;
                $data['page_data']['print'] = '/sale-payment-mode/print/thermal/'.$id;
                $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
            }
            else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblSaleSales::where(Utilities::currentBCB())->where('sales_type','SI')->max('sales_code'),'SI');
            $data['customer'] = TblSaleCustomer::select(['customer_id','customer_name'])->where('customer_default_customer',1)->where(Utilities::currentBC())->first();
            $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',auth()->user()->id)->get();
        
        }
        $data['payment_terms'] = TblAccoPaymentTerm::where(Utilities::currentBC())->get();
        $data['currency'] = TblDefiCurrency::where(Utilities::currentBC())->where('currency_entry_status',1)->get();
        $data['accounts'] = TblAccCoa::where(Utilities::currentBC())->where('chart_sale_expense_account',1)->get();
        $data['payment_type'] = TblDefiPaymentType::where(Utilities::currentBC())->where('payment_type_entry_status',1)->get();
        $data['rate_types'] = config('constants.rate_type');
        $data['merchant_acc'] = TblDefiMerchant::where(Utilities::currentBC())->where('merchant_entry_status',1)->get(['merchant_id','merchant_name']);
        $data['bank_acc'] = TblDefiBank::where(Utilities::currentBC())->where('bank_entry_status',1)->get(['bank_id','bank_name','bank_branch_name']);
        $data['store'] = TblDefiStore::where('store_entry_status',1)->where(Utilities::currentBCB())->get();
         

        return view('sales.sale_payment_mode.invoice',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $data = [];
        $x = parse_url($_SERVER['REQUEST_URI']);
        $type = explode('/',$x['path']);
        
        
        if(isset($request->customer_id)){
            $exitsCustomer = ViewSaleCustomer::where('customer_id',$request->customer_id)->where(Utilities::currentBC())->exists();
            if (!$exitsCustomer) {
                return $this->returnjsonerror("Customer Not Exist",201);
            }
        }
        
        switch ($type[1]){
            case 'sale-payment-mode': {
                $form_type = 'sale-payment-mode';
                break;
            }
        }
        if(!isset($form_type)){
            abort('404');
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $sales = TblSaleSales::where('sales_id',$id)->where('sales_type','POS')->first();
                $sales_type = 'POS';
            }

            $sales->bank_id = $request->bank_acc_id;
            $sales->merchant_id = $request->marchant_id;
            $sales->customer_credit_card_no = $request->credit_card_no;
            $sales->cashreceived = $request->cashreceived;
            $sales->change = $request->cash_return;

            if($request->bank_acc_id != "")
            {
                $sales->cash_amount = isset($request->cashreceived)?$request->cashreceived:0;
            }else{
                $sales->cash_amount = isset($request->cashreceived_id)?$request->cashreceived_id:0;
            }
            
            $sales->visa_amount = $request->visa_amount;
            $sales->save();

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

        $data = array_merge($data, Utilities::returnJsonEditForm());
        $data['redirect'] = $this->prefixIndexPage.$form_type;
        return $this->jsonSuccessResponse($data, trans('message.update'), 200);

    }


    public function print($type,$id)
    {
        $x = parse_url($_SERVER['REQUEST_URI']);
        $case_type = explode('/',$x['path']);
        
        switch ($case_type[1]){
            case 'sale-payment-mode': {
                $form_type = 'POS';
                $data['title'] = 'POS Sales Invoice';
                $data['invoice_menu_id'] = '41';
                break;
            }
        }
        if(!isset($form_type)){
            abort('404');
        }

        $data['permission'] = $data['invoice_menu_id'].'-print';
        if(isset($id)){
            if(TblSaleSales::where('sales_id','LIKE',$id)->exists()){
                $data['current'] = TblSaleSales::with('dtls','customer','expense','SO')->where('sales_id',$id)->where('sales_type',$form_type)->first();
                $data['current_thermal'] = ViewSaleSalesInvoice::where('sales_id',$id)->where('sales_type',$form_type)->first();

            }else{
                abort('404');
            }
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where('id',$data['current']->sales_sales_man)->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_term_id)->where('payment_term_entry_status',1)->first();
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->first();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)->where('payment_type_id',$data['current']->sales_sales_type)->first();
        $data['terminal'] = TblSoftPOSTerminal::where('terminal_id',$data['current']->terminal_id)->first();
       
        $data['invoice_headings'] = TblSoftPosInvoiceHeadings::pluck('heading_arabic_name','heading_key');

        if($type == 'html'){
            return view('prints.sale_invoice_print',compact('data'));
        }
        if($type == 'thermal'){
            return view('prints.sale_invoice_thermal_print',compact('data'));
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

    public function fbrSaleInvoiceTaxPost(Request $request)
    {
        // dd($request->toArray());
        $data = [];
        if(!isset($request->sale_ids) && empty($request->sale_ids)){
            return $this->jsonErrorResponse($data, 'Sale invoice is required', 200);
        }
        DB::beginTransaction();
        try{

            foreach ($request->sale_ids as $sale_ids){

                $ret = $this->fbrSaleInvoiceTaxPosted($sale_ids);

                if($ret['status'] == 'error'){
                    return $this->jsonErrorResponse($data, $ret['message'], 200);
                }
            }

        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();

        return $this->jsonSuccessResponse($data, 'FBR tax successfully posted', 200);
    }

    public function fbrSaleInvoiceTaxPosted($id)
    {
        if (isset($id)) {

            //$sales = TblSaleSales::with('dtls')->where('sales_id',$id);
            //$sales = $sales->where('posted',0);
            //$sales = $sales->first();
            
            $sales = TblSaleSales::with('dtls')->where('sales_id',$id);
            $sales = $sales->first();

            if(!empty($sales)){
                $type = strtolower($sales->sales_type) == 'pos' ?1:2;
                $pay_type = TblDefiPaymentType::where('payment_type_id',$sales->payment_mode_id)->first();
                $Pos_no = TblSoftPOSTerminal::where('terminal_id',$sales->terminal_id)
                    ->where('branch_id',$sales->branch_id)
                    ->where('business_id',$sales->business_id)
                    ->where('company_id',$sales->company_id)->first();
                
                $POS_ID = isset($Pos_no->fbr_pos_id)?$Pos_no->fbr_pos_id:"";

                $PaymentModeName = isset($pay_type->payment_type_name)?$pay_type->payment_type_name:"";
                if(strtolower($PaymentModeName) != 'cash'){
                    $PaymentMode = 2;
                }else{
                    $PaymentMode = 1;
                }
                $obj = [
                    'InvoiceNumber' => $sales->sales_code,
                    'USIN' => 'USIN0',
                    'POSID' => $POS_ID , //801507, // fetch from FBR Web Portal
                    'DateTime' => date('Y-m-d H:i:s',strtotime($sales->created_at)),
                    'TotalBillAmount' => $sales->sales_net_amount,
                    'TotalQuantity' => '',
                    'TotalSaleValue' => '',
                    'TotalTaxCharged' => '',
                    'Discount' => '',
                    'FurtherTax' => 0.0,
                    'PaymentMode' => $PaymentMode,
                    'InvoiceType' => $type,
                ];
                $TotalQuantity = 0;
                $TotalSaleValue = 0;
                $TotalTaxCharged = 0;
                $Discount = 0;
                $dtls = isset($sales->dtls) && count($sales->dtls) != 0?$sales->dtls:[];
                
                $items = [];
                foreach ($dtls as $dtl)
                {
                    $arr_hs_code = TblPurcProductBarcodePurchRate::where('product_id',$dtl->product_id)->first();
                    $hs_code = $arr_hs_code->hs_code;
                    $type = strtolower($dtl->sales_type) == 'pos' ?1:3;
                    $items[] = [
                        'ItemCode' => $dtl->sales_dtl_barcode,
                        'ItemName' => $dtl->product->product_name,
                        'Quantity' => $dtl->sales_dtl_quantity,
                        'PCTCode' => $hs_code,//11001010,
                        'TaxRate' => $dtl->sales_dtl_vat_per,
                        'SaleValue' => $dtl->sales_dtl_amount,
                        'TotalAmount' => $dtl->sales_dtl_net_amount,
                        'TaxCharged' => $dtl->sales_dtl_vat_amount,
                        'Discount' => $dtl->sales_dtl_disc_amount,
                        'FurtherTax' => 0.0,
                        'InvoiceType' => $type,
                        'RefUSIN' => 'USIN0',
                    ];

                    $TotalQuantity += $dtl->sales_dtl_quantity;
                    $TotalSaleValue += $dtl->sales_dtl_amount;
                    $TotalTaxCharged += $dtl->sales_dtl_vat_amount;
                    $Discount += $dtl->sales_dtl_disc_amount;
                }

                $obj['TotalQuantity'] = $TotalQuantity;
                $obj['TotalSaleValue'] = $TotalSaleValue;
                $obj['TotalTaxCharged'] = $TotalTaxCharged;
                $obj['Discount'] = $Discount;
                $obj['items'] = $items;

                $url = "https://gw.fbr.gov.pk/imsp/v1/api/Live/PostData";   // production url
                //$url = "https://esp.fbr.gov.pk:8244/FBR/v1/api/Live/PostData";  // sandbox url
               // dd(json_encode($obj));
               
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_POST => 1,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($obj),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer 1298b5eb-b252-3d97-8622-a4a69d5bf818', // fetch from FBR Web Portal
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

//dd($response);
                $data['res'] = json_decode($response, true);
                $data['id'] = $id;
                $message = isset($data['res']['Response'])?$data['res']['Response']:"";
                $InvoiceNumber = isset($data['res']['InvoiceNumber'])?$data['res']['InvoiceNumber']:"";

                
                if((!empty($InvoiceNumber) && $InvoiceNumber != "Not Available") && isset($data['res']['Code']) && $data['res']['Code'] == 100){
                    $sales->fbr_invoice_no = $InvoiceNumber;
                    $sales->fbr_posted = 1;
                    $sales->fbr_charges = 1;
                    $sales->posted = 1;
                    $sales->save();
                    return ['status'=>'success', 'data'=> $data, 'message'=>$message];
                }else{
                    return ['status'=>'error', 'data'=>$data, 'message'=> $message];
                }
            }
        }
    }

}
