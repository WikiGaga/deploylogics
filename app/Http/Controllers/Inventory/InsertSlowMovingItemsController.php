<?php

namespace App\Http\Controllers\Inventory;

use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblSoftBranch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\TrigerReorderLevelChecking;
use App\Models\TblSlowMovingStock;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InsertSlowMovingItemsController extends Controller
{
    public static $page_title = 'Insert Slow Moving Items';
    public static $redirect_url = 'slow-moving-items';
    public static $menu_dtl_id = '237';

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
    public function create($id=null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['permission'] = self::$menu_dtl_id.'-view';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Inser Slow Moving Items';
        $data['branches'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();

        return view('inventory.slow_moving_stock.form',compact('data'));
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id=null)
    {
        // Post The Slow Moving Stock
        $data = [];
        $validator = Validator::make($request->all(), [
            'branch_ids' => 'required',
            'date_from' => 'required|date|date_format:d-m-Y|before_or_equal:date_to',
            'date_to' => 'required|date|date_format:d-m-Y|after_or_equal:date_from',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $date_from = date('Y-m-d' , strtotime($request->date_from));
        $date_to = date('Y-m-d' , strtotime($request->date_to));
        $branch_ids = $request->branch_ids;

        TrigerReorderLevelChecking::dispatch($date_from,$date_to,$branch_ids)->delay(now()->addSeconds(30));

        return $this->jsonSuccessResponse([] , 'Request In Sent In Queue. This will take Sometime.' , 200);        
    }
    
}
