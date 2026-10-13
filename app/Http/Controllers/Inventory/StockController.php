<?php

namespace App\Http\Controllers\Inventory;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoVoucher;
use App\Models\TblDefiConfigBranches;
use App\Models\TblDefiPaymentType;
use App\Models\TblDefiStore;
use App\Models\TblInveStock;
use App\Models\TblInveStockDtl;
use App\Models\TblPurcDemand;
use App\Models\TblPurcDemandDtl;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblInveItemFormulation;
use App\Models\TblPurcGrn;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblPurcSupplier;
use App\Models\TblSoftBranch;
use App\Models\ViewInveDisplayLocation;
use App\Models\ViewInveStock;
use App\Models\TblAccCoa;
use App\Traits\HasStaging;
use App\Services\StagingService;


use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Importer;

class StockController extends Controller
{
    use HasStaging;
    public $menu_id = '';

    public function __construct(StagingService $stagingService)
    {
        $this->stagingService = $stagingService;
        $this->menu_id = '';
    }

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
    public function create(Request $request, $type, $id = null)
    {
        /*
         * Opening Stock = OS
         * Stock Transfer = ST
         * Stock Receiving = SR
         * Stock Adjustment = SA
         * Expired Items = EI
         * Sample Items = SI
         * Damaged Items = DI
         * Repair Items = RI
         * Internal Stock Transfer = ST
         * */
        $locale = Session::get('locale');
        //dd($locale);
        $data['page_data'] = [];
        $data['type'] = $type;
        $user_branches= DB::table('tbl_soft_branch as b')
        ->select('b.business_id','b.company_id','b.branch_id',"b.branch_name","ub.user_id")
        ->join('tbl_soft_user_branch as ub','ub.branch_id','=','b.branch_id')
        ->where('ub.user_id',auth()->user()->id)
        ->get();

        $data['user_branches'] = $user_branches;
        $data['code_getter_type'] = 'inventry';

        switch ($type){
            case 'opening-stock': {
                $data['page_data']['title'] = 'Opening Stock';
                $formUrl = 'opening_stock';
                $data['stock_code_type'] = 'os';
                $data['stock_menu_id'] = '54';
                $this->menu_id = '54';
                break;
            }
            case 'stock-transfer': {
                $data['page_data']['title'] = 'Stock Transfer';
                $formUrl = 'stock_transfer';
                $data['stock_code_type'] = 'st';
                $data['stock_menu_id'] = '65';
                $this->menu_id = '65';
                break;
            }
            case 'stock-distribution': {
                $data['page_data']['title'] = 'Stock Distribution';
                $formUrl = 'stock_distribution';
                $data['stock_code_type'] = 'sd';
                $data['stock_menu_id'] = '359';
                $this->menu_id = '359';
                break;
            }
            case 'stock-adjustment': {
                $data['page_data']['title'] = 'Stock Adjustment';
                $formUrl = 'stock_adjustment';
                $data['stock_code_type'] = 'sa';
                $data['stock_menu_id'] = '55';
                $this->menu_id = '55';
                break;
            }

            case 'damaged-items': {
                $data['page_data']['title'] = 'Damaged Items';
                $formUrl = 'stock_item';
                $data['stock_code_type'] = 'di';
                $data['stock_menu_id'] = '57';
                $this->menu_id = '57';
                break;
            }
            case 'expired-items': {
                $data['page_data']['title'] = 'Expired Items';
                $formUrl = 'stock_item';
                $data['stock_code_type'] = 'ei';
                $data['stock_menu_id'] = '58';
                $this->menu_id = '58';
                break;
            }
            case 'sample-items': {
                $data['page_data']['title'] = 'Sample Items';
                $formUrl = 'stock_item';
                $data['stock_code_type'] = 'sp';
                $data['stock_menu_id'] = '56';
                $this->menu_id = '56';
                break;
            }
            case 'repair-items': {
                $data['page_data']['title'] = 'Repair Items';
                $formUrl = 'stock_item';
                $data['stock_code_type'] = 'ri';
                $data['stock_menu_id'] = '183';
                $this->menu_id = '183';
                break;
            }
            case 'stock-receiving': {
                $data['page_data']['title'] = 'Stock Receiving';
                $formUrl = 'stock_receiving';
                $data['stock_code_type'] = 'str';
                $data['stock_menu_id'] = '76';
                $this->menu_id = '76';
                break;
            }
            case 'internal-stock-transfer': {
                $data['page_data']['title'] = 'Internal Stock Transfer';
                $formUrl = 'internal_stock_transfer';
                $data['stock_code_type'] = 'ist';
                $data['stock_menu_id'] = '131';
                $this->menu_id = '131';
                break;
            }
            case 'disassemble-products': {
                $data['page_data']['title'] = 'Disassemble Product';
                $formUrl = 'assemble_products';
                $data['stock_code_type'] = 'dss';
                $data['stock_menu_id'] = '134';
                $this->menu_id = '134';
                break;
            }
        }

        $data['form_type'] = $type;
        $data['pre_code'] = $data['stock_code_type'];

        $data['page_data']['path_index'] = $this->prefixIndexPage.'stock/'.$type;
        $data['page_data']['create'] = '/stock/'.$type.$this->prefixCreatePage;
        if(isset($id)){
            $allowCrossBranchView = (string) $request->query('view') === '1';
            $baseQuery = TblInveStock::where('stock_id','LIKE',$id)->where(Utilities::currentBC());
            $exists = $allowCrossBranchView
                ? $baseQuery->exists()
                : $baseQuery->wherein('branch_id', $user_branches->pluck('branch_id'))->exists();

            if($exists){
                $menuPermissionPrefix = $data['stock_menu_id'];
                $data['permission'] = [
                    $menuPermissionPrefix.'-edit',
                    $menuPermissionPrefix.'-view',
                ];
                $data['id'] = $id;
                $currentQuery = TblInveStock::with('stock_dtls','product','barcode','supplier','formulation')
                    ->where(Utilities::currentBC())
                    ->where('stock_id',$id)
                    ->where('stock_code_type',$data['stock_code_type']);
                $auditQuery = TblInveStock::with('audit_stock_dtls','product','barcode')
                    ->where(Utilities::currentBC())
                    ->where('stock_id',$id)
                    ->where('stock_code_type',$data['stock_code_type']);
                if(!$allowCrossBranchView){
                    $currentQuery = $currentQuery->wherein('branch_id', $user_branches->pluck('branch_id'));
                    $auditQuery = $auditQuery->wherein('branch_id', $user_branches->pluck('branch_id'));
                }
                $data['current'] = $currentQuery->first();
                $data['current_audit'] = $auditQuery->first();
                if(empty($data['current'])){
                    abort('404');
                }

                $canEdit = auth()->user()->isAbleTo($menuPermissionPrefix.'-edit')
                    && Utilities::documentBranchIsUserAccessible($data['current']->branch_id);
                if($canEdit){
                    $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                }else{
                    $data['page_data'] = array_merge($data['page_data'], Utilities::viewForm());
                }

               // dd($data['current']->toArray());
                $data['display_location'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$data['current']->stock_store_from_id)->orderBy('display_location_name_string')->get();
                $data['page_data']['print'] = '/stock/'.$type.'/print/'.$id;
                $data['stock_code'] = $data['current']->stock_code;
                if($type === 'stock-receiving' && !empty($data['current']->stock_request_id)){
                    $data['source_transfer'] = TblInveStock::where('stock_id', $data['current']->stock_request_id)
                        ->where('stock_code_type', 'st')
                        ->where(Utilities::currentBC())
                        ->first();
                }
                if($type == 'stock-transfer' && (int) $data['current']->stock_receive_status > 0){
                    $data['page_data']['action'] = '';
                }

                $data['page_data']['post'] = action('Inventory\StockController@post', ['type' => $type]);
                $data['page_data']['cancel'] = action('Inventory\StockController@cancel', ['type' => $type]);
                $data['page_data']['document_id_field'] = 'stock_id';
                $data['page_data']['is_posted'] = isset($data['current']->posted) && (int) $data['current']->posted === 1;
                $data['page_data']['is_canceled'] = isset($data['current']->posted) && (int) $data['current']->posted === 2;

                $this->clearFormUpdateActionIfDocumentNotEditable($data['page_data'], $data['current']);

            }else{
                abort('404');
            }
        }else{
            $data['permission'] = $data['stock_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblInveStock',
                'code_field'        => 'stock_code',
                'code_prefix'       => strtoupper($data['stock_code_type']),
                'code_type_field'   => 'stock_code_type',
                'code_type'         => $data['stock_code_type']

            ];
            $data['stock_code'] = Utilities::documentCode($doc_data);
        }
        $data['rate_types'] = config('constants.rate_type');
        $data['store'] = TblDefiStore::where(Utilities::currentBC())->get();
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->where('branch_id','!=',auth()->user()->branch_id)->get();
        if ($type == 'stock-distribution') {
            $data['transfer_branches'] = TblSoftBranch::where(Utilities::currentBC())->orderBy('branch_name')->get();
        } else {
            $data['transfer_branches'] = $data['branch'];
        }
        $data['rate_by'] = config('constants.rate_by');
        $data['rate_types'] = config('constants.rate_type');
        $data['payment_type'] = TblDefiPaymentType::where(Utilities::currentBC())->where('payment_type_entry_status',1)->get();

        $arr = [
            'biz_type' => 'branch',
            'code' => $data['stock_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_inve_stock',
            'col_id' => 'stock_id',
            'col_code' => 'stock_code',
            'code_type_field'   => 'stock_code_type',
            'code_type'         => $data['stock_code_type'],
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        $data['menu_dtl_id'] = $this->menu_id;
        $data['perPrefix'] = $this->menu_id;
        // dd($formUrl);

        return view('inventory.'.$formUrl.'.form',compact('data'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$type, $id = null)
    {
        // dd($request);
        $this->menu_id = $this->resolveStockMenuIdFromType($type);
        $data = [];
        $errData = [];
        $expectedFormTypes = [
            'opening-stock' => 'os',
            'stock-transfer' => 'st',
            'stock-distribution' => 'sd',
            'stock-adjustment' => 'sa',
            'damaged-items' => 'di',
            'expired-items' => 'ei',
            'sample-items' => 'sp',
            'repair-items' => 'ri',
            'stock-receiving' => 'str',
            'internal-stock-transfer' => 'ist',
            'disassemble-products' => 'dss',
        ];
        if (!isset($expectedFormTypes[$type])) {
            abort(404);
        }
        $formType = $expectedFormTypes[$type];
        $request->merge(['stock_code_type' => $formType]);

        $valid = [
            'stock_date' => 'required|date_format:d-m-Y',
            'store' => 'nullable|numeric',
            'new_branch_id' => 'required|numeric',
            // 'store_to' => 'nullable|numeric',
            // 'pd.*.product_id' => 'nullable|numeric',
            // 'pd.*.product_barcode_id' => 'nullable|numeric',
            // 'pd.*.uom_id' => 'nullable|numeric',
            // 'pd.*.pd_barcode' => 'nullable|max:100',
            // 'pd.*.demand_qty' => 'nullable|numeric',
            // 'pd.*.quantity' => 'nullable|numeric',
            // 'pd.*.batch_no' => 'nullable|max:20',
        ];

       $new_branch_id = $request->new_branch_id;
        if($formType == 'os' || $formType == 'sd'){
            $valid['store'] = 'required|numeric|not_in:0';
        }
        if($formType == 'st'){
            $valid['store'] = 'required|numeric|not_in:0';
            $valid['branch_to'] = 'required|numeric|not_in:0|different:new_branch_id';
            $valid['pd'] = 'required|array|min:1';
            $valid['pd.*.product_id'] = 'required|numeric';
            $valid['pd.*.product_barcode_id'] = 'required|numeric';
            $valid['pd.*.uom_id'] = 'required|numeric';
            $valid['pd.*.quantity'] = 'required|numeric|gt:0';
            $valid['pd.*.rate'] = 'required|numeric|min:0';
            $valid['pd.*.amount'] = 'required|numeric|min:0';
        }
        if($formType == 'str'){
            $valid['store_to'] = 'required|numeric|not_in:0';
            $valid['stock_from_id'] = 'required|numeric';
        }
        //dd($valid);
        $validator = Validator::make($request->all(), $valid);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $allowedBranchIds = DB::table('tbl_soft_user_branch')
            ->where('user_id', auth()->user()->id)
            ->pluck('branch_id')
            ->map(function ($branchId) { return (string) $branchId; })
            ->all();
        if (!in_array((string) $request->new_branch_id, $allowedBranchIds, true)) {
            return $this->jsonErrorResponse($data, 'Selected source branch is not accessible.', 422);
        }
        if ($formType === 'st') {
            $sourceStoreExists = TblDefiStore::where('store_id', $request->store)
                ->where('branch_id', $request->new_branch_id)
                ->where(Utilities::currentBC())
                ->exists();
            $destinationBranchExists = TblSoftBranch::where('branch_id', $request->branch_to)
                ->where(Utilities::currentBC())
                ->exists();
            if (!$sourceStoreExists || !$destinationBranchExists) {
                return $this->jsonErrorResponse($data, 'Invalid source store or destination branch.', 422);
            }
        }
        if ($formType === 'str') {
            $destinationStoreExists = TblDefiStore::where('store_id', $request->store_to)
                ->where('branch_id', $request->new_branch_id)
                ->where(Utilities::currentBC())
                ->exists();
            if (!$destinationStoreExists) {
                return $this->jsonErrorResponse($data, 'The receiving store does not belong to the selected branch.', 422);
            }
        }
        if(isset($request->selected_barcode_rate) && !empty($request->selected_barcode_rate)){
            $rate_by = config('constants.rate_by');
            $rate_by=array_flip($rate_by);
            if(!in_array($request->selected_barcode_rate,$rate_by)){
                return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
            }
        }
        $stagingActionCode = strtolower(str_replace([' ', '-'], '_', trim((string) $request->input('staging_action_code', ''))));
        $isWorkflowStagingAction = in_array($stagingActionCode, [
            'forward', 'send_forward', 'back', 'send_back', 'sendback', 'post', 'cancel', 'un_post', 'unpost',
        ], true);

        if(empty($request->pd) && !$isWorkflowStagingAction && $formType !== 'str'){
            return $this->returnjsonerror("Please Enter Product Detail",201);
        }
        if($request->stock_code_type == 'sd' && !$isWorkflowStagingAction && !empty($request->pd)){
            $seen = [];
            foreach($request->pd as $pd){
                $productId = isset($pd['product_id']) ? $pd['product_id'] : '';
                $branchTo = isset($pd['branch_to']) ? $pd['branch_to'] : '';
                $storeTo = isset($pd['store_to']) ? $pd['store_to'] : '';
                if(empty($productId) || empty($branchTo) || $branchTo == '0' || empty($storeTo) || $storeTo == '0'){
                    return $this->returnjsonerror("Each row must have product, To Branch and To Store.",201);
                }
                if((int)$branchTo === (int)$new_branch_id){
                    return $this->returnjsonerror("Destination branch cannot be same as source branch.",201);
                }
                $key = $productId.'_'.$branchTo.'_'.$storeTo;
                if(isset($seen[$key])){
                    return $this->returnjsonerror("Duplicate product with same destination branch and store is not allowed.",201);
                }
                $seen[$key] = true;
            }
        }
        DB::beginTransaction();
        try{
            $formType = $expectedFormTypes[$type];
            // dd($formType);
            if(isset($id)){
                $stock = TblInveStock::where('stock_id',$id)
                    ->where('stock_code_type', $formType)
                    ->where(Utilities::currentBC())
                    ->whereIn('branch_id', $allowedBranchIds)
                    ->first();
                if (empty($stock)) {
                    throw new ModelNotFoundException('Stock document not found.');
                }
                if ($formType === 'st' && (int) $stock->stock_receive_status > 0) {
                    throw ValidationException::withMessages([
                        'stock_id' => 'A received stock transfer cannot be modified.',
                    ]);
                }
                if ($formType === 'str') {
                    if ((string) $request->stock_from_id !== (string) $stock->stock_request_id) {
                        throw ValidationException::withMessages([
                            'stock_from_id' => 'The source transfer of an existing receipt cannot be changed.',
                        ]);
                    }
                    $stockTrans = TblInveStock::with('stock_dtls')
                        ->where('stock_id', $stock->stock_request_id)
                        ->where(Utilities::currentBC())
                        ->where('stock_branch_to_id', $new_branch_id)
                        ->whereIn('stock_receive_status', [1, 2])
                        ->where('posted', 1)
                        ->where('stock_code_type','st')
                        ->lockForUpdate()
                        ->first();
                    if (empty($stockTrans)) {
                        throw ValidationException::withMessages([
                            'stock_from_id' => 'The posted source transfer is no longer available.',
                        ]);
                    }
                    $this->applySourceTransferToReceivingRequest($request, $stockTrans);
                }
            }else{
                if($formType == 'str'){
                    $stockTrans = TblInveStock::with('stock_dtls')->where('stock_id',$request->stock_from_id)
                        ->where(Utilities::currentBC())
                        ->where('stock_branch_to_id', $new_branch_id)
                        ->where('stock_receive_status',0)
                        ->where('posted', 1)
                        ->where('stock_code_type','st')
                        ->lockForUpdate()
                        ->first();
                    if(empty($stockTrans)){
                        DB::rollBack();
                        return $this->returnjsonerror("Only a posted, unreceived stock transfer belonging to this branch can be received.",200);
                    }
                    $this->applySourceTransferToReceivingRequest($request, $stockTrans);
                    $stockTrans->stock_receive_status =  2;
                    $stockTrans->save();
                }
                $stock = new TblInveStock();
                $stock->stock_id =  Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblInveStock',
                    'code_field'        => 'stock_code',
                    'code_prefix'       => strtoupper($formType),
                    'code_type_field'   => 'stock_code_type',
                    'code_type'         => $formType

                ];
                $stock->stock_code =  Utilities::documentCode($doc_data);
            }
            $form_id = $stock->stock_id;
            $stock->stock_code_type =  $formType;

            if(isset($id)){
                $this->assertCanSaveWithStaging($request, $this->menu_id, $form_id, false, $stock);
            }

            if (isset($id) && !$this->stagingShouldPersistFormChanges($request, $this->menu_id, $form_id, $stock)) {
                $wasInStaging = !empty($stock->current_stg_id) && (int)($stock->posted ?? 0) === 0;
                $stagingService = new StagingService();
                $criteriaApplies = $stagingService->shouldUseStagingForDocument($this->menu_id, $form_id, $stock, $wasInStaging, false);

                if ($criteriaApplies) {
                    $this->handleStaging($request, $this->menu_id, $form_id, $stock, false, [
                        'listing_view' => 'vw_inve_stock',
                        'form_path' => '/stock/' . $type . '/form',
                        'document_code_key' => 'stock_code',
                    ]);
                    $stock->save();
                    if ($formType === 'str' && ($stagingActionCode === 'cancel' || (int) ($stock->posted ?? 0) === 2)) {
                        $this->releaseReceivingSourceTransfer($stock);
                    }
                    $this->syncStockAccountingVoucherPosted($stock);
                }
                DB::commit();

                $data = array_merge($data, Utilities::returnJsonEditForm());
                $data['redirect'] = $this->documentFormStayRedirect('/stock/' . $type, $form_id);
                return $this->jsonSuccessResponse($data, trans('message.update'), 200);
            }

            if(isset($id)){
                TblAccoVoucher::where('voucher_document_id',$id)->where(Utilities::currentBC())->where('branch_id',$new_branch_id)->delete();
            }

            $stock->stock_date =   date('Y-m-d', strtotime($request->stock_date));
            $stock->stock_menu_id =  $request->stock_menu_id;
            $stock->sales_rate_type =  isset($request->rate_type)?$request->rate_type:"";
            $stock->sales_rate_perc =  isset($request->rate_perc)?$request->rate_perc:"";
            $stock->start_date =  isset($request->start_date)?$request->start_date:"";
            $stock->end_date =  isset($request->end_date)?$request->end_date:"";
            $stock->is_expiry =  isset($request->is_active)?1:0;
            $stock->sale_rate =  isset($request->sale)?$request->sale:"";
            $stock->cost_rate =  isset($request->cost)?$request->cost:"";
            $stock->stock_total_qty =  0;
            $stock->stock_total_amount =  0;
            $stock->product_id = $request->f_product_id;
            $stock->product_barcode_id = $request->f_product_barcode_id;
            // $stock->product_barcode_barcode = $request->f_barcode;
            $stock->formulation_id = isset($request->formulation_id)?$request->formulation_id:"";
            $stock->product_barcode_packing = isset($request->product_barcode_packing)?$request->product_barcode_packing:"";
            $stock->uom_id = isset($request->uom_id)?$request->uom_id:"";
            $stock->product_qty_base_unit = isset($request->assamble_qty)?$request->assamble_qty:0;
            // $stock->formulation_code = isset($request->formulation_code)?$request->formulation_code:"";
            $stock->assamble_qty = isset($request->assamble_qty)?$request->assamble_qty:"";
            $stock->stock_rate_type = isset($request->rate_type)?$request->rate_type:'';

            $stock->stock_rate_perc = isset($request->rate_perc)?$request->rate_perc:'';

            $stock->ref_grn_id = isset($request->grn_id)?$request->grn_id:'';
            $stock->ref_grn_code = isset($request->grn_code)?$request->grn_code:'';
            if($formType == 'os')
            {
                $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
            }
            if($formType == 'str')
            {
                $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
                $stock->stock_store_to_id =  isset($request->store_to)?$request->store_to:"";
                $stock->stock_branch_from_id =  isset($request->branch_from_id)?$request->branch_from_id:"";
                $stock->stock_branch_to_id =  $new_branch_id;
            }
            if($formType == 'st')
            {
                $store = \App\Models\TblDefiStore::where('branch_id',$request->branch_to)->where('store_default_value',1)->first();
                $store_to = isset($store->store_id)?$store->store_id:"";

                $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
                $stock->stock_store_to_id =  isset($store_to)?$store_to:"";
                $stock->stock_branch_from_id =  $new_branch_id;
                $stock->stock_branch_to_id =  isset($request->branch_to)?$request->branch_to:"";
            }
            if($formType == 'sd')
            {
                $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
                $stock->stock_branch_from_id =  $new_branch_id;
                $stock->stock_store_to_id =  "";
                $stock->stock_branch_to_id =  "";
            }
            $stock->sales_sales_type =  isset($request->sales_sales_type)?$request->sales_sales_type:"";
            $stock->stock_rate_by =  isset($request->selected_barcode_rate)?$request->selected_barcode_rate:"";
            $stock->stock_remarks = $request->stock_remarks;
            $stock->stock_request_id = isset($request->stock_from_id)?$request->stock_from_id:'';
            $stock->stock_location_id = isset($request->stock_location_id)?$request->stock_location_id:'';
            $stock->stock_entry_status = 1;
            $stock->supplier_id = isset($request->supplier_id)?$request->supplier_id:'';


            $stock->stock_total_items = 0;
            $stock->stock_total_gross_net_amount = 0;
            $stock->stock_advance_tax_perc = 0;
            $stock->stock_advance_tax_amount = 0;
            $stock->stock_total_net_amount = 0;
            $stock_total_net_amount = 0;


            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->branch_id = $new_branch_id;
            $stock->stock_user_id = auth()->user()->id;
            $stock->save();

            $del_Dtls = TblInveStockDtl::where('stock_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveStockDtl::where('stock_dtl_id',$del_Dtl->stock_dtl_id)->delete();
            }
            $stock_total_qty = 0;
            $stock_total_amount = 0;
            $stock_total_vat_amount = 0;
            $stock_total_disc_amount = 0;
            $stock_total_gross_amount = 0;
            if(isset($request->pd)){
                $key = 1;
                foreach($request->pd as $pd){
                    $errData = $pd;
                    $dtl = new TblInveStockDtl();
                    $dtl->stock_id =  $stock->stock_id;
                    $dtl->stock_dtl_id =  Utilities::uuid();
                    $dtl->stock_dtl_sr_no =  $key++;
                    $dtl->product_id =  $pd['product_id'];
                    $dtl->product_barcode_id =  $pd['product_barcode_id'];
                    $dtl->product_barcode_barcode =  $pd['pd_barcode'];
                    $dtl->uom_id =  $pd['uom_id'];
                    $dtl->stock_dtl_packing = isset($pd['pd_packing'])?$pd['pd_packing']:'';
                    if($formType == 'sa'){
                        $adjustmentQty = (float)$pd['physical_quantity'] - (float)$pd['stock_quantity'];
                        $dtl->stock_dtl_quantity = $adjustmentQty; // adjustment qty
                    }else{
                        $dtl->stock_dtl_quantity =  $this->addNo($pd['quantity']);
                    }
                    if($formType == 'st')
                    {
                        $grnQty = (isset($pd['grn_qty']) && (float)$pd['grn_qty'] > 0) ? (float)$pd['grn_qty'] : 0;
                        $itemQty = isset($pd['quantity']) ? (float)$pd['quantity'] : 0;

                        if ($grnQty > 0) {
                            $per_itm_gst_amount = round((isset($pd['gst_amount']) ? (float)$pd['gst_amount'] : 0) / $grnQty, 2);
                            $gst_amount = $per_itm_gst_amount * $itemQty;

                            $per_itm_dis_amount = round((isset($pd['grn_dis_amount']) ? (float)$pd['grn_dis_amount'] : 0) / $grnQty, 2);
                            $dis_amount = $per_itm_dis_amount * $itemQty;

                            $per_itm_after_dis_amount = round((isset($pd['after_dis_amount']) ? (float)$pd['after_dis_amount'] : 0) / $grnQty, 2);
                            $after_dis_amount = $per_itm_after_dis_amount * $itemQty;

                            $per_itm_fed_amount = round((isset($pd['fed_amount']) ? (float)$pd['fed_amount'] : 0) / $grnQty, 2);
                            $fed_amount = $per_itm_fed_amount * $itemQty;

                            $per_itm_spec_disc_amount = round((isset($pd['spec_disc_amount']) ? (float)$pd['spec_disc_amount'] : 0) / $grnQty, 2);
                            $spec_disc_amount = $per_itm_spec_disc_amount * $itemQty;
                        } else {
                            $gst_amount = 0;
                            $dis_amount = 0;
                            $after_dis_amount = 0;
                            $fed_amount = 0;
                            $spec_disc_amount = 0;
                        }

                        $dtl->grn_qty =  isset($pd['grn_qty'])?$this->addNo($pd['grn_qty']):"";
                        $dtl->grn_disc_per =  isset($pd['grn_dis_perc'])?$this->addNo($pd['grn_dis_perc']):"";
                        $dtl->grn_disc_amount =  isset($dis_amount)?$this->addNo($dis_amount):"";
                        $dtl->grn_after_disc_amount =  isset($after_dis_amount)?$this->addNo($after_dis_amount):"";
                        $dtl->grn_gst_per =  isset($pd['gst_perc'])?$this->addNo($pd['gst_perc']):"";
                        $dtl->grn_gst_amount =  isset($gst_amount)?$this->addNo($gst_amount):"";
                        $dtl->grn_fed_per =  isset($pd['fed_perc'])?$this->addNo($pd['fed_perc']):"";
                        $dtl->grn_fed_amount =  isset($fed_amount)?$this->addNo($fed_amount):"";
                        $dtl->grn_spec_disc_per =  isset($pd['spec_disc_perc'])?$this->addNo($pd['spec_disc_perc']):"";
                        $dtl->grn_spec_disc_amount =  isset($spec_disc_amount)?$this->addNo($spec_disc_amount):"";
                        $dtl->grn_gross_amount =  isset($pd['grn_gross_amount'])?$this->addNo($pd['grn_gross_amount']):"";
                        $dtl->grm_net_amount =  isset($pd['net_amount'])?$this->addNo($pd['net_amount']):"";
                        $dtl->grn_rate =  isset($pd['unit_price'])?$this->addNo($pd['unit_price']):"";
                    }
                    $dtl->stock_dtl_qty_base_unit =  $this->addNo($pd['quantity']);
                    $dtl->stock_dtl_rate =  isset($pd['rate'])?$this->addNo($pd['rate']):"";
                    $dtl->stock_dtl_sale_rate =  isset($pd['sale_rate'])?$this->addNo($pd['sale_rate']):"";
                    $dtl->stock_dtl_sale_amount =  isset($pd['sale_amount'])?$this->addNo($pd['sale_amount']):"";
                    //$dtl->cost_rate =  isset($pd['cost_rate'])?$this->addNo($pd['cost_rate']):"";

                    /*if($formType == 'st'){
                        if(round($pd['hidden_cost_rate']) != 0){
                            $dtl->cost_rate =  isset($pd['hidden_cost_rate'])?$this->addNo($pd['hidden_cost_rate']):"";
                            $dtl->cost_posting =  isset($pd['hidden_cost_rate'])?1:0;

                            $cost_amount = $pd['hidden_cost_rate'] * $pd['quantity'];
                            $dtl->cost_amount =  isset($cost_amount)?$this->addNo($cost_amount):"";
                        }
                    }*/

                    $dtl->stock_dtl_demand_quantity =  isset($pd['demand_qty'])?$this->addNo($pd['demand_qty']):"";
                    $dtl->stock_dtl_stock_transfer_qty =  isset($pd['stock_transfer_qty'])?$this->addNo($pd['stock_transfer_qty']):"";
                    //add new column formula qty
                    $dtl->stock_dtl_formula_qty = isset($pd['formula_qty'])?$this->addNo($pd['formula_qty']): 0;
                    $dtl->stock_dtl_batch_no =  isset($pd['batch_no'])?$pd['batch_no']:"";
                    $dtl->stock_dtl_store =  isset($pd['pd_store'])?$pd['pd_store']:"";
                    if($formType == 'sd')
                    {
                        $dtl->stock_dtl_branch_to_id = isset($pd['branch_to'])?$pd['branch_to']:"";
                        $dtl->stock_dtl_store_to_id = isset($pd['store_to'])?$pd['store_to']:"";
                        $dtl->stock_dtl_branch_from_id = $new_branch_id;
                        $dtl->stock_dtl_store_from_id = isset($request->store)?$request->store:"";
                    }
                    $dtl->stock_dtl_amount =  isset($pd['amount'])?$this->addNo($pd['amount']):"";
                    $dtl->stock_dtl_disc_percent =  isset($pd['dis_perc'])?$this->addNo($pd['dis_perc']):"";
                    $dtl->stock_dtl_disc_amount =  isset($pd['dis_amount'])?$this->addNo($pd['dis_amount']):"";
                    $dtl->stock_dtl_vat_percent =  isset($pd['vat_perc'])?$this->addNo($pd['vat_perc']):"";
                    $dtl->stock_dtl_vat_amount =  isset($pd['vat_amount'])?$this->addNo($pd['vat_amount']):"";
                    $dtl->stock_dtl_total_amount =  isset($pd['gross_amount'])?$this->addNo($pd['gross_amount']):"";
                    $dtl->stock_dtl_stock_quantity =  isset($pd['stock_quantity'])?$this->addNo($pd['stock_quantity']):"";
                    $dtl->stock_dtl_physical_quantity =  isset($pd['physical_quantity'])?$this->addNo($pd['physical_quantity']):"";
                    $dtl->stock_dtl_production_date =  isset($pd['production_date'])?date('Y-m-d', strtotime($pd['production_date'])):"";
                    $dtl->stock_dtl_expiry_date =  isset($pd['expiry_date'])?date('Y-m-d', strtotime($pd['expiry_date'])):"";
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = $new_branch_id;
                    $dtl->dtl_user_id = auth()->user()->id;
                    $stock_total_qty += isset($pd['quantity'])?$this->addNo($pd['quantity']):0;
                    $stock_total_disc_amount += isset($pd['dis_amount'])?$this->addNo($pd['dis_amount']):0;
                    $stock_total_vat_amount += isset($pd['vat_amount'])?$this->addNo($pd['vat_amount']):0;
                    $stock_total_amount += isset($pd['amount'])?$this->addNo($pd['amount']):0;
                    $stock_total_gross_amount += isset($pd['gross_amount'])?$this->addNo($pd['gross_amount']):0;

                   // dd($dtl);
                    $dtl->save();
                    if($formType == 'st' && isset($pd['demand_qty']) && strlen((string)$pd['demand_qty']) > 0){
                        $demandQty = (float)$pd['demand_qty'];
                        $itemQty = (float)($pd['quantity'] ?? 0);
                        if($demandQty <= $itemQty){
                            $demnadDtl = TblPurcDemandDtl::where('demand_id',$request->stock_from_id)
                                ->where('product_id',$pd['product_id'])
                                ->where('product_barcode_id',$pd['product_barcode_id'])->first();
                            if(!empty($demnadDtl)){
                                $demnadDtl->demand_dtl_approve_status = 'approved';
                                $demnadDtl->save();
                            }
                        }
                        if($demandQty > $itemQty){
                            $demnadDtl = TblPurcDemandDtl::where('demand_id',$request->stock_from_id)
                                ->where('product_id',$pd['product_id'])
                                ->where('product_barcode_id',$pd['product_barcode_id'])->first();
                            if(!empty($demnadDtl)){
                                $demnadDtl->demand_dtl_approve_status = 'pending';
                                $demnadDtl->save();
                            }
                        }
                    }
                    if($request->store == 2 && $formType == 'os'){
                        // if select store *showroom* and Stock Location
                        // then update product with this stock location and user
                        $branches = TblSoftBranch::where('branch_active_status',1)->get();
                        $barcodes = TblPurcProductBarcode::where('product_id',$pd['product_id'])->get();
                        foreach($barcodes as $barcode){
                            foreach($branches as $branch){
                                if($barcode->product_barcode_id == $pd['product_barcode_id'] && $branch->branch_id == auth()->user()->branch_id){
                                    if(TblPurcProductBarcodeDtl::where('product_barcode_id',$pd['product_barcode_id'])->where('branch_id',auth()->user()->branch_id)->exists()){
                                        $barcodeDtl = TblPurcProductBarcodeDtl::where('product_barcode_id',$pd['product_barcode_id'])
                                            ->where('branch_id',auth()->user()->branch_id)->first();
                                    }else{
                                        $barcodeDtl = new TblPurcProductBarcodeDtl();
                                        $barcodeDtl->product_barcode_dtl_id = Utilities::uuid();
                                        $barcodeDtl->product_barcode_id = $pd['product_barcode_id'];
                                        $barcodeDtl->branch_id = $new_branch_id;
                                        $barcodeDtl->product_barcode_stock_limit_neg_stock = 0;
                                        $barcodeDtl->product_barcode_stock_limit_limit_apply = 0;
                                        $barcodeDtl->product_barcode_stock_limit_status = 0;
                                        $barcodeDtl->product_barcode_tax_apply =  0;
                                    }
                                    $barcodeDtl->product_barcode_shelf_stock_location = $request->stock_location_id;
                                    $barcodeDtl->product_barcode_shelf_stock_sales_man = auth()->user()->id;
                                    $barcodeDtl->save();
                                }else{
                                    TblPurcProductBarcodeDtl::create([
                                        'product_barcode_dtl_id' => Utilities::uuid(),
                                        'product_barcode_id' => $barcode->product_barcode_id,
                                        'branch_id' => $branch->branch_id,
                                        'product_barcode_shelf_stock_location' => 0,
                                        'product_barcode_shelf_stock_sales_man' => '',
                                        'product_barcode_stock_limit_neg_stock' => 0,
                                        'product_barcode_stock_limit_limit_apply' => 0,
                                        'product_barcode_stock_limit_status' => 0,
                                        'product_barcode_tax_apply' => 0,
                                    ]);
                                }
                            }
                        }
                    }

                    if($formType == 'str')
                    {
                        $rateupdate = true;

                        //$row = TblSoftBranch::where('branch_id',auth()->user()->branch_id)->first();
                        $row = TblSoftBranch::where('branch_id',$request->branch_from_id)->first();
                        $branch_type = $row->branch_type;

                        // Update Product Prices
                        if($rateupdate && $branch_type == '2')
                        {

                            $pRate = TblPurcProductBarcodePurchRate::where('product_id' , $pd['product_id'])->first();

                            if(!isset($pRate->product_barcode_purchase_rate) || $pRate->product_barcode_purchase_rate == "0"){
                                // Use 'rate' for stock transfer, fallback to 'purc_rate' for other form types
                                $rate_value = isset($pd['rate']) ? $pd['rate'] : (isset($pd['purc_rate']) ? $pd['purc_rate'] : 0);
                                $purc_rate = $this->addNo($rate_value);
                                $purc_rate = (float)$purc_rate/(float)$pd['pd_packing'];

                                $barcodeList = TblPurcProductBarcode::where('product_id',$pd['product_id'])
                                ->where('business_id',auth()->user()->business_id)
                                ->get(['product_id','product_barcode_id','business_id','product_barcode_barcode','product_barcode_packing']);

                                foreach ($barcodeList as $item){
                                    $barcodePurcRate = (float)$purc_rate * (float)$item->product_barcode_packing;

                                    $vExist = TblPurcProductBarcodePurchRate::where('product_barcode_id',$item->product_barcode_id)
                                    ->where('product_id',$item->product_id)
                                    ->where('branch_id',$new_branch_id);
                                    if($vExist->exists()){
                                        $vExist->update([
                                            'product_barcode_cost_rate'=> $barcodePurcRate,
                                            'product_barcode_purchase_rate'=> $barcodePurcRate,
                                            'net_tp'=> $this->addNo($rate_value),
                                            'last_tp'=> $this->addNo($rate_value),
                                        ]);
                                    }else{
                                        $PurchRate = new TblPurcProductBarcodePurchRate();
                                        $PurchRate->product_barcode_purch_id = Utilities::uuid();
                                        $PurchRate->product_id = $item->product_id;
                                        $PurchRate->product_barcode_id = $item->product_barcode_id;
                                        $PurchRate->product_barcode_barcode = $item->product_barcode_barcode;
                                        $PurchRate->product_barcode_purchase_rate = $barcodePurcRate;
                                        $PurchRate->product_barcode_cost_rate = $barcodePurcRate;
                                        $PurchRate->product_barcode_avg_rate = $barcodePurcRate;
                                        $PurchRate->net_tp = $this->addNo($rate_value);
                                        $PurchRate->last_tp = $this->addNo($rate_value);
                                        $PurchRate->business_id = auth()->user()->business_id;
                                        $PurchRate->company_id = auth()->user()->company_id;
                                        $PurchRate->branch_id = $new_branch_id;
                                        $PurchRate->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $stock = TblInveStock::where('stock_id',$stock->stock_id)->first();
            $stock->stock_total_items = isset($request->pd) ? count($request->pd) : 0;
            $stock->stock_total_qty =  $stock_total_qty;
            if($formType == 'st'){
                $stock->stock_total_amount =  $stock_total_gross_amount;
            }else{
                $stock->stock_total_amount =  $stock_total_amount;
            }
            $advanceTaxPercent = max(0, min(100, (float) $request->input('overall_vat_perc', 0)));
            $advanceTaxAmount = (float) $request->input('overall_vat_amount', 0);
            if ($advanceTaxAmount <= 0 && $advanceTaxPercent > 0) {
                $advanceTaxAmount = ($stock_total_gross_amount * $advanceTaxPercent) / 100;
            }
            $stock_total_net_amount = $stock_total_gross_amount + $advanceTaxAmount;
            $stock->stock_total_gross_net_amount = $stock_total_gross_amount;
            $stock->stock_advance_tax_perc = $advanceTaxPercent;
            $stock->stock_advance_tax_amount = $advanceTaxAmount;
            $stock->stock_total_net_amount = $stock_total_net_amount;
            $stock->save();

           // dd($formType);

            if($formType == 'st' || $formType == 'str'){
                // insert update stock transfer voucher
                $table_name = 'tbl_acco_voucher';
                if(isset($id)){
                    $action = 'update';
                    $stock_id = $id;
                    $stock = TblInveStock::where('stock_id',$stock_id)->where(Utilities::currentBC())->where('branch_id',$new_branch_id)->first();
                    $voucher_id = (int)$stock->voucher_id;
                    if(empty($voucher_id)){
                        $action = 'add';
                        $voucher_id = Utilities::uuid();
                    }
                }else{
                    $action = 'add';
                    $stock_id = $stock->stock_id;
                    $voucher_id = Utilities::uuid();
                }

                $cost_amount=0;
                $inner_Dtls = TblInveStockDtl::where('stock_id',$stock_id)->get();
                foreach ($inner_Dtls as $inner_Dtl){
                    $cost_amount += (float) $inner_Dtl->cost_amount;
                }
               // dd($request);
                $where_clause = '';
                switch ($formType) {
                    case "st":
                        $fromConfig = Utilities::getConfigBranch($new_branch_id);
                        $Newaccount = Utilities::getConfigBranch($request->branch_to);

                        if (empty($fromConfig)) {
                            DB::rollback();
                            return $this->jsonErrorResponse($data, 'Stock transfer accounts not configured for the selected branch.', 200);
                        }
                        if (empty($Newaccount)) {
                            DB::rollback();
                            return $this->jsonErrorResponse($data, 'Stock transfer accounts not configured for the destination branch.', 200);
                        }

                        $stock_income = $fromConfig->stock_transfer_income;
                        $stock_account = $fromConfig->stock_transfer_stock;
                        $stock_branch_account = $Newaccount->stock_transfer_branch;
                        $stock_vat_account = $fromConfig->stock_transfer_vat;
                        $stock_disc_account = $fromConfig->stock_transfer_discount;
                        if(isset($request->sales_sales_type) && $request->sales_sales_type == 1){
                            $stock_cash_account = $fromConfig->stock_transfer_cash;
                        }else{
                            $branch_account_code = TblSoftBranch::where('branch_id',$request->branch_to)->first('branch_account_code');
                            $stock_cash_account = $branch_account_code->branch_account_code;
                        }
                        break;
                    case "str":
                        $receiveConfig = Utilities::getConfigBranch($new_branch_id);
                        $Newaccount = Utilities::getConfigBranch($request->branch_from_id);

                        if (empty($receiveConfig)) {
                            DB::rollback();
                            return $this->jsonErrorResponse($data, 'Stock receive accounts not configured for the selected branch.', 200);
                        }
                        if (empty($Newaccount)) {
                            DB::rollback();
                            return $this->jsonErrorResponse($data, 'Stock transfer accounts not configured for the source branch.', 200);
                        }

                        $stock_income="";
                        $stock_account = $receiveConfig->store_receive_stock;
                        $stock_branch_account = $Newaccount->stock_transfer_branch;
                        $stock_vat_account = $receiveConfig->stock_receive_vat;
                        $stock_disc_account = $receiveConfig->stock_receive_discount;
                        if(isset($request->sales_sales_type) && $request->sales_sales_type == 1){
                            $stock_cash_account = $receiveConfig->stock_receive_cash;
                        }else{
                            $branch_account_code = TblSoftBranch::where('branch_id',$new_branch_id)->first('branch_account_code');
                            $stock_cash_account = $branch_account_code->branch_account_code;
                        }
                        break;
                }

                if($formType === 'str' && $stock_total_vat_amount > 0 && empty($stock_vat_account)){
                    DB::rollback();
                    return $this->jsonErrorResponse($data, 'Stock receive VAT account is not configured for the receiving branch.', 200);
                }
                if($formType === 'str' && $stock_total_disc_amount > 0 && empty($stock_disc_account)){
                    DB::rollback();
                    return $this->jsonErrorResponse($data, 'Stock receive discount account is not configured for the receiving branch.', 200);
                }

                //check account code
                // Validate only accounts that this flow actually writes to the voucher.
                // stock_cash_account is not used by the voucher insert logic.
                $ChartArr = [
                    $stock_income,
                    $stock_branch_account,
                    $stock_account,
                ];
                if ($formType === 'str' && $stock_total_vat_amount > 0) {
                    $ChartArr[] = $stock_vat_account;
                }
                if ($formType === 'str' && $stock_total_disc_amount > 0) {
                    $ChartArr[] = $stock_disc_account;
                }
                $ChartArr = array_values(array_filter($ChartArr, function ($accountId) {
                    return $accountId !== null && $accountId !== '';
                }));
                $response = $this->ValidateCharCode($ChartArr);
                if($response == false){
                    DB::rollback();
                    return $this->jsonErrorResponse($data, "Voucher Account Code not correct", 422);
                }
                //voucher start
                $data = [
                    'voucher_id'            =>  $voucher_id,
                    'voucher_document_id'   =>  $stock_id,
                    'voucher_no'            =>  $stock->stock_code,
                    'voucher_date'          =>  date('Y-m-d', strtotime($request->stock_date)),
                    'voucher_type'          =>  strtoupper($formType),
                    'branch_id'             =>  $new_branch_id,
                    'business_id'           =>  auth()->user()->business_id,
                    'company_id'            =>  auth()->user()->company_id,
                    'voucher_user_id'       =>  auth()->user()->id
                ];

                if($stock_income != "")
                {
                    $action = 'add';
                    $data['chart_account_id'] = $stock_income;
                    switch ($formType) {
                        case "st":
                            $data['voucher_sr_no'] = 1;
                            $data['voucher_descrip'] = 'Stock Transfer Income A/c against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = (abs($stock_total_amount) + abs($cost_amount));
                            break;
                    }
                    // for entry net_total
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                }

                if($stock_account != "")
                {
                    $action = 'add';
                    $data['chart_account_id'] = $stock_account;
                    switch ($formType) {
                        case "st":
                            $data['voucher_sr_no'] = 2;
                            $data['voucher_descrip'] = 'Stock Transfer Stock A/c against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs($cost_amount);
                        break;
                        case "str":
                            $data['voucher_sr_no'] = 1;
                            $data['voucher_descrip'] = 'Stock Receive against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = abs($stock_total_amount);
                            $data['voucher_credit'] = 0;
                        break;
                    }
                    // for entry net_total
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                }

                if($stock_branch_account != "")
                {
                    $action = 'add';
                    $data['chart_account_id'] = $stock_branch_account;
                    switch ($formType) {
                        case "st":
                            $data['voucher_sr_no'] = 3;
                            $data['voucher_descrip'] = 'Stock Transfer In Branch A/c against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = abs($stock_total_net_amount);
                            $data['voucher_credit'] = 0;
                        break;
                        case "str":
                            $data['voucher_sr_no'] = 2;
                            $data['voucher_descrip'] = 'Stock Receive In Branch A/c  against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs($stock_total_net_amount);
                        break;
                    }
                    // for credit entry vat_amount_total
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                }

                if($formType === 'str' && $stock_total_vat_amount > 0 && $stock_vat_account != "")
                {
                    $data['chart_account_id'] = $stock_vat_account;
                    $data['voucher_sr_no'] = 3;
                    $data['voucher_descrip'] = 'Stock Receive VAT against inv#'.$stock->stock_code;
                    $data['voucher_debit'] = abs($stock_total_vat_amount);
                    $data['voucher_credit'] = 0;
                    $this->proAccoVoucherInsert($voucher_id,'add',$table_name,$data,$where_clause);
                }

                if($formType === 'str' && $stock_total_disc_amount > 0 && $stock_disc_account != "")
                {
                    $data['chart_account_id'] = $stock_disc_account;
                    $data['voucher_sr_no'] = 4;
                    $data['voucher_descrip'] = 'Stock Receive Discount against inv#'.$stock->stock_code;
                    $data['voucher_debit'] = 0;
                    $data['voucher_credit'] = abs($stock_total_disc_amount);
                    $this->proAccoVoucherInsert($voucher_id,'add',$table_name,$data,$where_clause);
                }

                $allChartAcc = TblAccCoa::whereIn('chart_code',['6-01-10-0001'])->select('chart_account_id','chart_code','chart_name')->get();

                foreach ($allChartAcc as $oneChartAcc){
                    if($oneChartAcc->chart_code == '6-01-10-0001'){
                        $stock_vat_account = $oneChartAcc->chart_account_id;
                    }
                }
                if($advanceTaxAmount > 0)
                {
                    $action = 'add';
                    $data['chart_account_id'] = $stock_vat_account;
                    switch ($formType) {
                        case "st":
                            $data['voucher_sr_no'] = 4;
                            $data['voucher_descrip'] = 'Adv Tax Amount against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs($advanceTaxAmount);
                        break;
                        case "str":
                            $data['voucher_sr_no'] = 5;
                            $data['voucher_descrip'] = 'Adv Tax Amount against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = abs($advanceTaxAmount);
                            $data['voucher_credit'] = 0;
                        break;
                    }
                    // for credit entry vat_amount_total
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                }

                if(!isset($id) || empty($stock->voucher_id)){
                    $stock_vochId = TblInveStock::where('stock_id',$stock_id)->first();
                    $stock_vochId->voucher_id = $voucher_id;
                    $stock_vochId->save();
                }
                // end insert update stock transfer voucher
            }

            $this->finalizeDocumentStaging($request, $this->menu_id, $form_id, $stock, !isset($id), [
                'notification' => [
                    'listing_view' => 'vw_inve_stock',
                    'form_path' => '/stock/' . $type . '/form',
                    'document_code_key' => 'stock_code',
                ],
                'posted_when_exempt' => 0,
                'stg_log_posted_when_exempt' => 0,
                'sync_after_save' => function ($model) {
                    if ($model->stock_code_type === 'str' && (int) ($model->posted ?? 0) === 2) {
                        $this->releaseReceivingSourceTransfer($model);
                    } elseif ($model->stock_code_type === 'str' && (int) ($model->posted ?? 0) === 1) {
                        $this->setReceivingSourceStatus($model, 1);
                    }
                    $this->syncStockAccountingVoucherPosted($model);
                },
            ]);


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
            return $this->jsonErrorResponse($errData, $e->getMessage(), 200);
        }
        DB::commit();

        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->documentFormStayRedirect('/stock/' . $type, $form_id);
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/stock/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function getLocationByStore(Request $request)
    {
        $store_id  = $request->store_id;
        $data = [];
        $data['locations'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$store_id)->orderBy('display_location_name_string')->get();

        if(!empty($data['locations'])){
            $data['status'] = 'success';
        }else{
            $data['status'] = 'error';
        }
        return response()->json($data);
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

    public function print(Request $request, $type,$id)
    {
        switch ($type){
            case 'opening-stock': {
                $data['title'] = 'Opening Stock';
                $data['stock_code_type'] = 'os';
                $data['stock_menu_id'] = 54;
                $this->menu_id = '54';
                break;
            }
            case 'stock-transfer': {
                $data['title'] = 'Stock Transfer';
                $data['stock_code_type'] = 'st';
                $data['stock_menu_id'] = 65;
                $data['type'] = $request->print;
                $url = '/stock/stock-transfer/print/'.$id;
                $data['stock_transfer_link'] = $url;
                $this->menu_id = '65';
                break;
            }
            case 'stock-adjustment': {
                $data['title'] = 'Stock Adjustment';
                $data['stock_code_type'] = 'sa';
                $data['stock_menu_id'] = 55;
                $this->menu_id = '55';
                break;
            }

            case 'stock-audit-adjustment': {
                $data['title'] = 'Stock Audit Adjustment';
                $data['stock_code_type'] = 'sa';
                $data['stock_menu_id'] = 312;
                $this->menu_id = '312';
                $data['type'] = '';
                break;
            }
            case 'damaged-items': {
                $data['title'] = 'Damaged Items';
                $data['stock_code_type'] = 'di';
                $data['stock_menu_id'] = 57;
                $this->menu_id = '57';
                break;
            }
            case 'expired-items': {
                $data['title'] = 'Expired Items';
                $data['stock_code_type'] = 'ei';
                $data['stock_menu_id'] = 58;
                $this->menu_id = '58';
                break;
            }
            case 'sample-items': {
                $data['title'] = 'Sample Items';
                $data['stock_code_type'] = 'sp';
                $data['stock_menu_id'] = 56;
                $this->menu_id = '56';
                break;
            }
            case 'repair-items': {
                $data['title'] = 'Repair Items';
                $data['stock_code_type'] = 'ri';
                $data['stock_menu_id'] = 57;
                $this->menu_id = '57';
                break;
            }
            case 'stock-receiving': {
                $data['title'] = 'Stock Receiving';
                $data['stock_code_type'] = 'str';
                $data['stock_menu_id'] = 76;
                $data['type'] = $request->print;
                $url = '/stock/stock-receiving/print/'.$id;
                $data['print_link'] = $url;
                $this->menu_id = '76';
                break;
            }
            case 'internal-stock-transfer': {
                $data['title'] = 'Internal Stock Transfer';
                $data['stock_code_type'] = 'ist';
                $data['stock_menu_id'] = '131';
                $this->menu_id = '131';
                break;
            }
            case 'disassemble-products': {
                $data['title'] = 'Disassemble Product';
                $data['stock_code_type'] = 'dss';
                $data['stock_menu_id'] = '134';
                $this->menu_id = '134';
                break;
            }
        }

        if(isset($id)){
            if(TblInveStock::where('stock_id','LIKE',$id)->exists()){
                $data['current'] = TblInveStock::with('stock_dtls','product','barcode')->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();
                if(!empty($data['current']->supplier_id) && isset($data['current']->supplier_id)){
                    $data['supplier'] = TblPurcSupplier::where('supplier_id',$data['current']->supplier_id)->select('supplier_id','supplier_name')->first();
                }
                $data['permission'] = $data['stock_menu_id'].'-print';
            }else{
                abort('404');
            }
        }
        $data['store_from'] = TblDefiStore::where('store_id',$data['current']->stock_store_from_id)->first();
        $data['store_to'] = TblDefiStore::where('store_id',$data['current']->stock_store_to_id)->first();
        $data['branch_from'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_from_id)->first();
        $data['branch_to'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_to_id)->first();
        $data['display_location'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$data['current']->stock_store_from_id)->first();
        $data['rate_by'] = config('constants.rate_by');

        if($type == 'stock-audit-adjustment'){
            return view('prints.inventory.stock_adjustment.audit_stock_adjustment', compact('data'));
        }

        if ($data['type'] == '1' && $type == 'stock-transfer') {
            return view('prints.inventory.stock_transfer.stock_transfer_print_landscape', compact('data'));
        }
        else if($data['type'] == '0' || $data['type'] == '' && $type == 'stock-transfer'){
            return view('prints.inventory.stock_transfer.stock_transfer_print',compact('data'));
        }
        else if($data['type'] == '4' && $type == 'stock-transfer'){
            return view('prints.inventory.stock_transfer.dispatch_print',compact('data'));
        }
        else if($data['type'] == '2' || $data['type'] == '' && $type == 'stock-receiving'){
            return view('prints.inventory.stock_recieve.stock_recieve_print',compact('data'));
        }else if($data['type'] == '3' && $type == 'stock-receiving'){
            return view('prints.inventory.stock_recieve.stock_recieve_landscape_print',compact('data'));
        }
    }

    public function fromStockPrint(Request $request, $type,$id) {
        switch ($type){
            case 'opening-stock': {
                $data['title'] = 'Opening Stock';
                $data['stock_code_type'] = 'os';
                $data['stock_menu_id'] = 54;
                $this->menu_id = '54';
                break;
            }
            case 'stock-transfer': {
                $data['title'] = 'Stock Transfer';
                $data['stock_code_type'] = 'st';
                $data['stock_menu_id'] = 65;
                $data['type'] = $request->print;
                $url = '/stock/stock-transfer/from-stock-print/'.$id;
                $data['stock_transfer_link'] = $url;
                $this->menu_id = '65';
                break;
            }
            case 'stock-adjustment': {
                $data['title'] = 'Stock Adjustment';
                $data['stock_code_type'] = 'sa';
                $data['stock_menu_id'] = 55;
                $this->menu_id = '55';
                break;
            }
            case 'damaged-items': {
                $data['title'] = 'Damaged Items';
                $data['stock_code_type'] = 'di';
                $data['stock_menu_id'] = 57;
                $this->menu_id = '57';
                break;
            }
            case 'expired-items': {
                $data['title'] = 'Expired Items';
                $data['stock_code_type'] = 'ei';
                $data['stock_menu_id'] = 58;
                $this->menu_id = '58';
                break;
            }
            case 'sample-items': {
                $data['title'] = 'Sample Items';
                $data['stock_code_type'] = 'sp';
                $data['stock_menu_id'] = 56;
                $this->menu_id = '56';
                break;
            }
            case 'repair-items': {
                $data['title'] = 'Repair Items';
                $data['stock_code_type'] = 'ri';
                $data['stock_menu_id'] = 57;
                $this->menu_id = '57';
                break;
            }
            case 'stock-receiving': {
                $data['title'] = 'Stock Receiving';
                $data['stock_code_type'] = 'str';
                $data['stock_menu_id'] = 76;
                $this->menu_id = '76';
                break;
            }
            case 'internal-stock-transfer': {
                $data['title'] = 'Internal Stock Transfer';
                $data['stock_code_type'] = 'ist';
                $data['stock_menu_id'] = '131';
                $this->menu_id = '131';
                break;
            }
            case 'disassemble-products': {
                $data['title'] = 'Disassemble Product';
                $data['stock_code_type'] = 'dss';
                $data['stock_menu_id'] = '134';
                $this->menu_id = '134';
                break;
            }
        }
        if(isset($id)){
            if(TblInveStock::where('stock_id','LIKE',$id)->where('stock_branch_to_id',auth()->user()->branch_id)->exists()){
                $data['current'] = TblInveStock::with('stock_dtls','product','barcode')->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();
                if(!empty($data['current']->supplier_id) && isset($data['current']->supplier_id)){
                    $data['supplier'] = TblPurcSupplier::where('supplier_id',$data['current']->supplier_id)->select('supplier_id','supplier_name')->first();
                }
            }else{
                abort('404');
            }
        }else{
            abort('404');
        }
        $data['store_from'] = TblDefiStore::where('store_id',$data['current']->stock_store_from_id)->first();
        $data['store_to'] = TblDefiStore::where('store_id',$data['current']->stock_store_to_id)->first();
        $data['branch_from'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_from_id)->first();
        $data['branch_to'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_to_id)->first();
        $data['display_location'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$data['current']->stock_store_from_id)->first();
        $data['rate_by'] = config('constants.rate_by');
        if ($type == 'stock-transfer') {
            return view('prints.inventory.stock_recieve.stock_recieve_from_print',compact('data'));
        }else{
            return view('prints.stock_print_from',compact('data'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getStockRequestDtlData(Request $request)
    {
        $data = [];

        DB::beginTransaction();
        try{
            $rate_type = $request->rate_type;
            $rate_perc = $request->rate_perc;
            $data['stock'] = TblPurcDemand::with('dtls')->where('demand_id',$request->stock_id)->first();

            $data['rate'] = [];

            foreach ($data['stock']->dtls as $k=>$dtl){
                $rate = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$dtl['product_barcode_id'])->where('branch_id',auth()->user()->branch_id)->where('product_category_id',2)->first();
                $data['rate'][$k]['rate'] = $rate['product_barcode_sale_rate_rate'];

                $purc_rate = TblPurcProductBarcodePurchRate::where('product_barcode_barcode', $dtl['product_barcode_barcode'])
                ->where('product_barcode_id',$dtl['product_barcode_id'])
                ->where('branch_id',auth()->user()->branch_id)->first();
                $pur_rate = 0;
                if($rate_type == 'item_cost_rate'){
                    $pur_rate = $purc_rate['product_barcode_cost_rate'];
                }
                if($rate_type == 'item_sale_rate'){
                    $pur_rate = $rate['product_barcode_sale_rate_rate'];
                }
                if($rate_type == 'item_average_rate'){
                    $pur_rate = $purc_rate['product_barcode_avg_rate'];
                }
                if($rate_type == 'item_purchase_rate'){
                    $pur_rate = $purc_rate['product_barcode_purchase_rate'];
                }
                $data['rate'][$k]['purc_rate'] = $pur_rate;


                $vat = TblPurcProductBarcodeDtl::where('product_barcode_id',$dtl['product_barcode_id'])
                ->where('branch_id',auth()->user()->branch_id)->first();
                $vat_purc = 0;
                if(!empty($vat) && $vat->product_barcode_tax_apply == 1){
                    $vat_purc = $vat->product_barcode_tax_value;
                }
                $data['rate'][$k]['vat_purc'] = $vat_purc;
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
        return $this->jsonSuccessResponse($data, 'Stock Request Data loaded', 200);
    }


// Get farmulation Data
    public function getFormulationRequestData(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'stock_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, 'Please Select Formulation Code First', 422);
        }

        $id = $request->stock_id;
        DB::beginTransaction();
        try{

            //$data['current'] = TblInveItemFormulation::with('product','dtls')->where('item_formulation_id',$id)->first();
            $data['formulation'] = TblInveItemFormulation::with('product','rate','dtls')
            ->where(Utilities::currentBCB())->where('item_formulation_id',$id)->first();
            dd($data['formulation']->toArray());
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
        return $this->jsonSuccessResponse($data, 'Formulation Data loaded', 200);

    }


    public function getStockTransferDtlData(Request $request)
    {
        $data = [];

        DB::beginTransaction();
        try{
            $stockId = $request->stock_id;
            $stockCode = trim((string) $request->stock_code);
            $receivingBranchId = auth()->user()->branch_id;
            if ($request->filled('branch_id')) {
                $allowedBranchIds = DB::table('tbl_soft_user_branch')
                    ->where('user_id', auth()->user()->id)
                    ->pluck('branch_id')
                    ->map(function ($id) { return (string) $id; })
                    ->all();
                if (!ctype_digit((string) $request->branch_id)
                    || !in_array((string) $request->branch_id, $allowedBranchIds, true)) {
                    DB::rollBack();
                    return $this->jsonErrorResponse($data, 'Selected receiving branch is not accessible.', 422);
                }
                $receivingBranchId = (int) $request->branch_id;
            }

            $query = TblInveStock::with('stock_dtls')
                ->where(Utilities::currentBC())
                ->where('stock_code_type', 'st')
                ->where('stock_branch_to_id', $receivingBranchId)
                ->where('posted', 1)
                ->where('stock_receive_status', 0);

            if (!empty($stockId)) {
                $query->where('stock_id', $stockId);
            } elseif (!empty($stockCode)) {
                $query->where('stock_code', $stockCode);
            } else {
                DB::rollback();
                return $this->jsonErrorResponse($data, 'Select first Stock Code', 200);
            }

            $stock = $query->first();
            if (empty($stock)) {
                DB::rollback();
                return $this->jsonErrorResponse($data, 'Stock Transfer document not found.', 200);
            }

            $data['stock'] = $stock;
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
        return $this->jsonSuccessResponse($data, 'Stock Transfer Data loaded', 200);
    }

    public function getGRNDtlData(Request $request)
    {
        $data = [];

        DB::beginTransaction();
        try{
            $grnId = $request->grn_id;
            $grnCode = trim((string) $request->grn_code);

            $query = TblPurcGrn::with('grn_dtl.product', 'grn_dtl.barcode')
                ->where('business_id', auth()->user()->business_id)
                ->where('company_id', auth()->user()->company_id)
                ->where('grn_type', 'GRN');

            $branchId = auth()->user()->branch_id;
            if ($request->filled('branch_id')) {
                $allowedBranchIds = DB::table('tbl_soft_user_branch')
                    ->where('user_id', auth()->user()->id)
                    ->pluck('branch_id')
                    ->map(function ($id) { return (string) $id; })
                    ->all();
                if (!ctype_digit((string) $request->branch_id)
                    || !in_array((string) $request->branch_id, $allowedBranchIds, true)) {
                    DB::rollBack();
                    return $this->jsonErrorResponse($data, 'Selected GRN branch is not accessible.', 422);
                }
                $branchId = (int) $request->branch_id;
            }
            $query->where('branch_id', $branchId);

            if (!empty($grnId)) {
                $query->where('grn_id', $grnId);
            } elseif (!empty($grnCode)) {
                $query->where('grn_code', $grnCode);
            } else {
                DB::rollback();
                return $this->jsonErrorResponse($data, 'Please Select GRN No. First', 200);
            }

            $grn = $query->first();
            if (empty($grn)) {
                DB::rollback();
                return $this->jsonErrorResponse($data, 'GRN Document not found for the entered code.', 200);
            }

            $data['grn'] = $grn;

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
        return $this->jsonSuccessResponse($data, 'GRN Data loaded', 200);
    }




    public function destroy($type,$id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $this->menu_id = $this->resolveStockMenuIdFromType($type);
            $userBranchIds = DB::table('tbl_soft_user_branch')
                ->where('user_id', auth()->user()->id)
                ->pluck('branch_id');
            $stock= TblInveStock::where('stock_id',$id)
                ->where('stock_code_type', $this->resolveStockCodeTypeFromType($type))
                ->where(Utilities::currentBC())
                ->whereIn('branch_id', $userBranchIds)
                ->first();
            if (empty($stock)) {
                throw new ModelNotFoundException('Stock document not found.');
            }
            if (!auth()->user()->isAbleTo($this->menu_id . '-delete')) {
                throw new Exception('You do not have permission to delete this document.');
            }
            if ($type === 'stock-transfer' && (int) $stock->stock_receive_status > 0) {
                throw new Exception('A received stock transfer cannot be deleted.');
            }
            if ((int) ($stock->posted ?? 0) === 1 || ((int) ($stock->posted ?? 0) === 0 && ((int) ($stock->staging_apply ?? 0) === 1 || !empty($stock->current_stg_id)))) {
                throw new Exception('A posted or staged document must be canceled through its workflow before deletion.');
            }
            $st_id = $stock->stock_request_id;

            if($type === 'stock-receiving' && !empty($st_id)){
                TblInveStock::where('stock_id',$st_id)
                    ->where('stock_code_type', 'st')
                    ->where(Utilities::currentBC())
                    ->update(['stock_receive_status'=>'0']);
            }

            $stock->stock_dtls()->delete();
            TblAccoVoucher::where('voucher_document_id',$id)->where(Utilities::currentBCB())->delete();
            $stock->delete();

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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }

    public function importFile($type){
        $data = [];
        $data['page_data'] = [];
        $data['form_type'] = $type;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Import';
        $data['page_data']['type'] = 'Import';
        $data['page_data']['path_index'] = $this->prefixIndexPage.'stock/'.$type;
        $doc_data = [
            'biz_type'          => 'branch',
            'model'             => 'TblInveStock',
            'code_field'        => 'stock_code',
            'code_prefix'       => strtoupper('os'),
            'code_type_field'   => 'stock_code_type',
            'code_type'         => 'os'
        ];
        $data['stock_code'] = Utilities::documentCode($doc_data);
        $data['store'] = TblDefiStore::get();
        return view('inventory.opening_stock.import',compact('data'));
    }

    public function importExcle(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'store' => 'required|not_in:0',
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $path = public_path('/upload/');
            $filename = time() . '-' . $file->getClientOriginalName();
            $fileExtension = time() . '-' . $file->getClientOriginalExtension();
            $file->move($path,$filename);

            $excel = Importer::make('Excel');
            $excel->load($path.$filename);
            $collection = $excel->getCollection();
           // dd($collection);
            $formType = $request->form_type; // opening Stock
            $now = new \DateTime("now");
            $today_format = $now->format("d-m-Y");

            $endEntry = 200;
            $count = round(count($collection) / $endEntry);
            if(($count*200) < count($collection)){
                $count += 1;
            }
            $totalSuccess = 0;
            $totalFail = 0;
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblInveStock',
                'code_field'        => 'stock_code',
                'code_prefix'       => strtoupper($formType),
                'code_type_field'   => 'stock_code_type',
                'code_type'         => $formType

            ];
            for($i=1; $count >= $i; $i++){
                $stock_total_qty = 0;
                $stock_total_amount = 0;
                $stock = new TblInveStock();
                $stock->stock_id =  Utilities::uuid();
                $stock->stock_code = Utilities::documentCode($doc_data);
                $stock->stock_code_type =  $formType;
                $stock->stock_date =   date('Y-m-d', strtotime($today_format));
                $stock->stock_menu_id =  54;
                $stock->stock_store_from_id = $request->store;
                $stock->stock_entry_status = 1;
                $stock->business_id = auth()->user()->business_id;
                $stock->company_id = auth()->user()->company_id;
                $stock->branch_id = auth()->user()->branch_id;
                $stock->stock_user_id = auth()->user()->id;
                $stock->save();
                for ($row=(($endEntry*$i)-199); $row <= ($endEntry*$i); $row++){
                    $barcode = TblPurcProductBarcode::where('product_barcode_barcode','like',$collection[$row][6])->first();
                    if(!empty($barcode)){
                        $dtl = new TblInveStockDtl();
                        $dtl->stock_id = (int)$stock->stock_id;
                        $dtl->stock_dtl_id = Utilities::uuid();
                        $dtl->stock_dtl_sr_no = $row;
                        $qty = isset($collection[$row][2])?$this->addNo($collection[$row][2]):0;
                        $amount = isset($collection[$row][5])?$this->addNo($collection[$row][5]):0;
                        $dtl->stock_dtl_quantity =  $qty;
                        $dtl->stock_dtl_rate =  $collection[$row][4];
                        $dtl->stock_dtl_amount =  $collection[$row][5];
                        $dtl->product_barcode_barcode =  $collection[$row][6];
                        $dtl->product_id =  (int)$barcode->product_id;
                        $dtl->product_barcode_id =  (int)$barcode->product_barcode_id;
                        $dtl->uom_id =  (int)$barcode->uom_id;
                        $dtl->stock_dtl_packing =  $barcode->product_barcode_packing;
                        $packing = isset($barcode->product_barcode_packing)?$this->addNo($barcode->product_barcode_packing):0;
                        $dtl->stock_dtl_qty_base_unit =  $qty;
                        $dtl->business_id = auth()->user()->business_id;
                        $dtl->company_id = auth()->user()->company_id;
                        $dtl->branch_id = auth()->user()->branch_id;
                        $dtl->dtl_user_id = auth()->user()->id;

                        $stock_total_qty += $qty;
                        $stock_total_amount += $amount;
                        $dtl->save();
                        $totalSuccess++;
                        if($row == (count($collection)-1)){
                            break;
                        }
                    }else{
                        $totalFail++;
                    }
                }
                $stock = TblInveStock::where('stock_id',$stock->stock_id)->first();
                $stock->stock_total_qty =  $stock_total_qty;
                $stock->stock_total_amount =  $stock_total_amount;
                $stock->save();
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
        $data = array_merge($data, Utilities::returnJsonImportForm());
        $message = 'Total Fail:'.$totalFail.'<br/> Total Success:'.$totalSuccess;
        return $this->jsonSuccessResponse($data, $message, 200);

    }

    public function changeProductGroup (Request $request){
        dd('OK');
    }

    protected function applySourceTransferToReceivingRequest(Request $request, $stockTrans): void
    {
        $rows = [];
        foreach ($stockTrans->stock_dtls as $index => $detail) {
            $rows[$index + 1] = [
                'sr_no' => $index + 1,
                'product_id' => $detail->product_id,
                'product_barcode_id' => $detail->product_barcode_id,
                'pd_barcode' => $detail->product_barcode_barcode,
                'uom_id' => $detail->uom_id,
                'pd_packing' => $detail->stock_dtl_packing,
                'demand_qty' => $detail->stock_dtl_demand_quantity,
                'stock_transfer_qty' => $detail->stock_dtl_quantity,
                'quantity' => $detail->stock_dtl_quantity,
                'rate' => $detail->stock_dtl_rate,
                'sale_rate' => $detail->stock_dtl_sale_rate,
                'sale_amount' => $detail->stock_dtl_sale_amount,
                'amount' => $detail->stock_dtl_amount,
                'dis_perc' => $detail->stock_dtl_disc_percent,
                'dis_amount' => $detail->stock_dtl_disc_amount,
                'vat_perc' => $detail->stock_dtl_vat_percent,
                'vat_amount' => $detail->stock_dtl_vat_amount,
                'gross_amount' => $detail->stock_dtl_total_amount,
                'batch_no' => $detail->stock_dtl_batch_no,
                'production_date' => $detail->stock_dtl_production_date,
                'expiry_date' => $detail->stock_dtl_expiry_date,
            ];
        }
        if (empty($rows)) {
            throw ValidationException::withMessages([
                'stock_from_id' => 'The source transfer has no product details.',
            ]);
        }

        $request->merge([
            'stock_from_id' => $stockTrans->stock_id,
            'branch_from_id' => $stockTrans->stock_branch_from_id,
            'store' => $stockTrans->stock_store_from_id,
            'supplier_id' => $stockTrans->supplier_id,
            'overall_vat_perc' => $stockTrans->stock_advance_tax_perc,
            'overall_vat_amount' => $stockTrans->stock_advance_tax_amount,
            'pd' => $rows,
        ]);
    }

    protected function resolveStockMenuIdFromType($type): string
    {
        switch ($type) {
            case 'opening-stock': return '54';
            case 'stock-transfer': return '65';
            case 'stock-distribution': return '359';
            case 'stock-adjustment': return '55';
            case 'damaged-items': return '57';
            case 'expired-items': return '58';
            case 'sample-items': return '56';
            case 'repair-items': return '183';
            case 'stock-receiving': return '76';
            case 'internal-stock-transfer': return '131';
            case 'disassemble-products': return '134';
            default: return (string) $this->menu_id;
        }
    }

    protected function resolveStockCodeTypeFromType($type): string
    {
        $types = [
            'opening-stock' => 'os',
            'stock-transfer' => 'st',
            'stock-distribution' => 'sd',
            'stock-adjustment' => 'sa',
            'damaged-items' => 'di',
            'expired-items' => 'ei',
            'sample-items' => 'sp',
            'repair-items' => 'ri',
            'stock-receiving' => 'str',
            'internal-stock-transfer' => 'ist',
            'disassemble-products' => 'dss',
        ];
        if (!isset($types[$type])) {
            abort(404);
        }
        return $types[$type];
    }

    protected function syncStockAccountingVoucherPosted($stock): void
    {
        if (empty($stock)) {
            return;
        }

        $query = TblAccoVoucher::where('voucher_document_id', $stock->stock_id);
        if (!empty($stock->voucher_id)) {
            $query->where('voucher_id', $stock->voucher_id);
        }
        $updateData = ['posted' => (int) ($stock->posted ?? 0)];
        if (isset($stock->staging_apply)) {
            $updateData['staging_apply'] = $stock->staging_apply;
        }
        if (isset($stock->current_stg_id) || property_exists($stock, 'current_stg_id')) {
            $updateData['current_stg_id'] = $stock->current_stg_id;
        }
        $query->update($updateData);
    }

    protected function assertTransferNotReceived($row, string $action): void
    {
        if (!empty($row) && $row->stock_code_type === 'st' && (int) $row->stock_receive_status > 0) {
            throw new \RuntimeException('A received stock transfer cannot be ' . $action . '.', 422);
        }
    }

    protected function releaseReceivingSourceTransfer($receiving): void
    {
        if (empty($receiving) || $receiving->stock_code_type !== 'str' || empty($receiving->stock_request_id)) {
            return;
        }
        TblInveStock::where('stock_id', $receiving->stock_request_id)
            ->where('stock_code_type', 'st')
            ->where(Utilities::currentBC())
            ->update(['stock_receive_status' => 0]);
    }

    protected function setReceivingSourceStatus($receiving, int $status): void
    {
        if (empty($receiving) || $receiving->stock_code_type !== 'str' || empty($receiving->stock_request_id)) {
            return;
        }
        TblInveStock::where('stock_id', $receiving->stock_request_id)
            ->where('stock_code_type', 'st')
            ->where(Utilities::currentBC())
            ->update(['stock_receive_status' => $status]);
    }

    public function cancel(Request $request, $type)
    {
        $this->menu_id = $this->resolveStockMenuIdFromType($type);
        DB::beginTransaction();
        try {
            $stock_id = $request->stock_id;
            if (empty($stock_id)) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'Stock id not found.'], 422);
            }
            $row = TblInveStock::where('stock_id', $stock_id)
                ->where('stock_code_type', $this->resolveStockCodeTypeFromType($type))
                ->where(Utilities::currentBC())->first();
            $this->assertTransferNotReceived($row, 'canceled');
            $this->guardUmDocumentAction($this->menu_id, $row, 'cancel', 'cancel');
            $row->posted = 2;
            $row->update();
            $this->releaseReceivingSourceTransfer($row);
            $this->syncStockAccountingVoucherPosted($row);
            DB::commit();
            return response()->json(['status' => 'success', 'message' => trans('message.cancel')]);
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return $this->umJsonErrorFromException($e);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse([], $e->getMessage(), 422);
        }
    }

    public function post(Request $request, $type = null)
    {
        if ($type) {
            $this->menu_id = $this->resolveStockMenuIdFromType($type);
        }
        try {
            $stock_id = $request->stock_id;
            if (empty($stock_id)) {
                return response()->json(['status' => 'error', 'message' => 'Stock id not found.'], 422);
            }
            $row = TblInveStock::where('stock_id', $stock_id)
                ->where('stock_code_type', $this->resolveStockCodeTypeFromType($type))
                ->where(Utilities::currentBC())->first();
            $this->guardUmDocumentAction($this->menu_id, $row, 'post', 'post');
            $row->posted = 1;
            $row->update();
            $this->setReceivingSourceStatus($row, 1);
            $this->syncStockAccountingVoucherPosted($row);
            return response()->json(['status' => 'success']);
        } catch (\RuntimeException $e) {
            return $this->umJsonErrorFromException($e);
        }
    }

    public function Posted(Request $request, $type = null)
    {
        if ($type) {
            $this->menu_id = $this->resolveStockMenuIdFromType($type);
        }
        $data = [];
        $ids = $request->data;
        if(is_array($ids) && count($ids) > 0){
            foreach($ids as $id){
                try {
                    $row = TblInveStock::where('stock_id', $id)
                        ->where('stock_code_type', $this->resolveStockCodeTypeFromType($type))
                        ->where(Utilities::currentBC())->first();
                    $this->guardUmDocumentAction($this->menu_id, $row, 'post', 'post');
                } catch (\RuntimeException $e) {
                    return $this->jsonErrorResponse([], $e->getMessage(), $e->getCode() >= 400 ? $e->getCode() : 422);
                }
            }
            foreach($ids as $id){
                if(TblInveStock::where('stock_id',$id)->where('stock_code_type', $this->resolveStockCodeTypeFromType($type))->where(Utilities::currentBC())->exists()){
                    $row = TblInveStock::where('stock_id',$id)->where('stock_code_type', $this->resolveStockCodeTypeFromType($type))->where(Utilities::currentBC())->first();
                    $row->posted = 1;
                    $row->update();
                    $this->setReceivingSourceStatus($row, 1);
                    $this->syncStockAccountingVoucherPosted($row);
                }
            }
            return $this->jsonSuccessResponse($data, trans('Successfully Posted'), 200);
        }else{
            abort(404);
        }
    }


    public function UnPosted(Request $request, $type = null)
    {
        if ($type) {
            $this->menu_id = $this->resolveStockMenuIdFromType($type);
        }
        $data = [];
        $ids = $request->data;
        if(is_array($ids) && count($ids) > 0){
            foreach($ids as $id){
                try {
                    $row = TblInveStock::where('stock_id', $id)
                        ->where('stock_code_type', $this->resolveStockCodeTypeFromType($type))
                        ->where(Utilities::currentBC())->first();
                    $this->assertTransferNotReceived($row, 'un-posted');
                    $this->guardUmDocumentAction($this->menu_id, $row, 'un_post_module', 'unpost');
                } catch (\RuntimeException $e) {
                    return $this->jsonErrorResponse([], $e->getMessage(), $e->getCode() >= 400 ? $e->getCode() : 422);
                }
            }
            foreach($ids as $id){
                if(TblInveStock::where('stock_id',$id)->where('stock_code_type', $this->resolveStockCodeTypeFromType($type))->where(Utilities::currentBC())->exists()){
                    $row = TblInveStock::where('stock_id',$id)->where('stock_code_type', $this->resolveStockCodeTypeFromType($type))->where(Utilities::currentBC())->first();
                    $row->posted = 0;
                    $row->update();
                    $this->setReceivingSourceStatus($row, 2);
                    $this->syncStockAccountingVoucherPosted($row);
                }
            }
            return $this->jsonSuccessResponse($data, trans('Successfully Un-Posted'), 200);
        }else{
            abort(404);
        }
    }
}
