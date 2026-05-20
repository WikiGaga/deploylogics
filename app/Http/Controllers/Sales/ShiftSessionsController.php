<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\ShiftSession;
use App\Models\TblSoftBranch;
use App\Services\StagingService;
use App\Traits\HasStaging;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Library\Utilities;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShiftSessionsController extends Controller
{
    use HasStaging;

    public static $page_title = 'Shift Sessions';
    public static $redirect_url = 'shift_sessions';
    public static $menu_dtl_id = '354';
    public static $type = 'shift_sessions';
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
    public function create(Request $request, $id=null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['permission'] = self::$menu_dtl_id.'-edit';
        $data['page_data']['type'] = 'edit';
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['permission'] = self::$menu_dtl_id.'-create';

        // $id=603521645;

        $data['branch'] = TblSoftBranch::get();
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['menu_id'] = self::$menu_dtl_id;



        if(isset($id)){
        $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
        $data['current'] = ShiftSession::with('branch')->where('session_id', $id)->first();
        $data['id'] = $id;
        $data['page_data']['post'] = action('Sales\ShiftSessionsController@post');
        $data['page_data']['is_posted'] = isset($data['current']->posted) && $data['current']->posted == 1;
        if (isset($data['current']->posted) && $data['current']->posted == 1) {
            $data['page_data']['action'] = '';
        }
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['current'] = [];
            $data['id'] = null;

            $doc_data = [
                'biz_type' => 'branch',
                'model' => 'ShiftSession',
                'code_field' => 'session_no',
                'code_prefix' => strtoupper('ssw'),
                'filter_by_prefix' => true,
            ];
            $branchIdForCode = auth()->user()->branch_id;
            $requestedBranch = $request->query('session_branch');
            if ($requestedBranch !== null && $requestedBranch !== '') {
                $requestedBranch = (string) $requestedBranch;
                if (TblSoftBranch::where('branch_id', $requestedBranch)->exists()) {
                    $branchIdForCode = $requestedBranch;
                }
            }
            $defaultBranch = TblSoftBranch::where('branch_id', $branchIdForCode)->first();
            if ($defaultBranch) {
                $doc_data['business_id'] = $defaultBranch->business_id;
                $doc_data['company_id'] = $defaultBranch->company_id;
                $doc_data['branch_id'] = $defaultBranch->branch_id;
            }
            $data['session_no'] = Utilities::documentCode($doc_data);
            $data['selected_session_branch_id'] = $branchIdForCode;
        }

        return view('sales.shift_sessions.form', compact('data'));

        //  $data['att_id']=$id;
        // $data['form_type'] = 'Att';
        // $data['page_data'] = [];
        // $data['page_data']['title'] = self::$page_title;
        // $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        // $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        // $data['menu_id'] = self::$menu_dtl_id;
        // $data['page_data']['pending_pr'] = TRUE;
        // $data['already_exits'] = false;
        // $data['permission'] = self::$menu_dtl_id.'-create';

        // $data['employee'] =  DB::table('tbl_payr_employee')
        // ->select("EMPLOYEE_ID","EMPLOYEE_NAME")
        // ->get();


        //  $data['att_data'] =[];
        //  $data['att_note'] = '';
        //  $data['att_date'] = '';

        // if(isset($id)){
        //     $Tbl_hr_attendence=DB::table('Tbl_hr_attendence')->where('id',$id)->first();

        //     if(!empty($Tbl_hr_attendence)){
        //         $Tbl_hr_attendence_dtl = DB::table('Tbl_hr_attendence_dtl')->where('att_id',$id)->get();
        //         $data['att_data'] = $Tbl_hr_attendence_dtl;
        //         $data['att_no'] = $Tbl_hr_attendence->att_no;
        //         $data['att_note'] = $Tbl_hr_attendence->att_note;
        //         $data['att_date'] = $Tbl_hr_attendence->att_date;
        //         $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
        //     }else{
        //         abort('404');
        //     }

        // }else{
        //     // $data['permission'] = $data['stock_menu_id'].'-create';
        //     $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            // $max_voucher = TblHrEmployeeAttendance::where(Utilities::currentBCB())->max('att_no');
            // $data['att_no'] = $this->documentCode($max_voucher,'ATT');
        // }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id=null)
    {
        // dd($request->all(), $id);
        $data = [];
        $rules = [
            'opening_cash' => 'required|numeric',
            'opening_visa' => 'required|numeric',
            'closing_cash' => 'required|numeric',
            'closing_visa' => 'required|numeric',
        ];
        if (!isset($id)) {
            $rules['session_branch'] = 'required|exists:tbl_soft_branch,branch_id';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, 'Validation failed. Please check the input data.', 422);
        }

        DB::beginTransaction();
        try {

            Log::debug('[shift_sessions.store] begin', [
                'route_id' => $id ?? null,
                'staging_inputs' => $request->only([
                    'current_flow_id',
                    'next_flow_id',
                    'prev_flow_id',
                    'current_actions_id',
                    'staging_action_code',
                    'flow_remarks',
                ]),
            ]);

            if(isset($id)){
                $shiftSession = ShiftSession::where('session_id', $id)->firstOrFail();

                $this->assertCanSaveWithStaging($request, self::$menu_dtl_id, $id, false, $shiftSession);

                $stagingShouldPersist = $this->stagingShouldPersistFormChanges($request, self::$menu_dtl_id, $id, $shiftSession);

                if (!$stagingShouldPersist) {
                    $wasInStaging = !empty($shiftSession->current_stg_id) && (int)($shiftSession->posted ?? 0) === 0;
                    $stagingService = new StagingService();
                    $criteriaApplies = $stagingService->hasStagingOrRemainsInStaging(self::$menu_dtl_id, $id, $wasInStaging);


                    if ($criteriaApplies) {
                        $this->handleStaging($request, self::$menu_dtl_id, $id, $shiftSession, false, [
                            'listing_view' => 'vw_shift_sessions_listing',
                            'form_path' => '/shift_sessions/form',
                            'document_code_key' => 'session_no',
                        ]);
                        $shiftSession->save();
                    }

                    DB::commit();
                    $data = array_merge($data, Utilities::returnJsonEditForm());
                    $isInStaging = !empty($shiftSession->current_stg_id) && (int)($shiftSession->posted ?? 0) === 0;
                    $data['redirect'] = $isInStaging
                        ? '/'.self::$redirect_url.'/form/'.$id
                        : $this->prefixIndexPage.self::$redirect_url;
                    return $this->jsonSuccessResponse($data, trans('message.update'), 200);
                }

                $shiftSession->opening_cash = $request->opening_cash ?? 0; // Set opening cash to 0 if it's null
                $shiftSession->closing_cash = $request->closing_cash;
                $shiftSession->opening_visa = $request->opening_visa ?? 0; // Set opening visa to 0 if it's null
                $shiftSession->closing_visa = $request->closing_visa;
                $shiftSession->save();

            }else{

                $shiftSession = new ShiftSession();
                $shiftSession->opening_cash = $request->opening_cash ?? 0; // Set opening cash to 0 if it's null
                $shiftSession->closing_cash = $request->closing_cash;
                $shiftSession->opening_visa = $request->opening_visa ?? 0; // Set opening visa to 0 if it's null
                $shiftSession->closing_visa = $request->closing_visa;
                $shiftSession->start_date = $request->session_date;
                $shiftSession->end_date = $request->session_end_date;
                $shiftSession->session_status = $request->session_status;

                $branchRow = TblSoftBranch::where('branch_id', $request->session_branch)->firstOrFail();
                $shiftSession->branch_id = $branchRow->branch_id;
                $shiftSession->company_id = $branchRow->company_id;
                $shiftSession->business_id = $branchRow->business_id;

                $doc_data = [
                    'biz_type' => 'branch',
                    'model' => 'ShiftSession',
                    'code_field' => 'session_no',
                    'code_prefix' => strtoupper('ssw'),
                    'filter_by_prefix' => true,
                    'business_id' => $branchRow->business_id,
                    'company_id' => $branchRow->company_id,
                    'branch_id' => $branchRow->branch_id,
                ];
                $shiftSession->session_no = Utilities::documentCode($doc_data);

                $shiftSession->user_id = auth()->user()->id;
                $shiftSession->session_id = Utilities::uuid();

                $shiftSession->save();

            }

            $formId = $shiftSession->session_id;
            $wasInStaging = isset($id) && !empty($shiftSession->current_stg_id) && (int)($shiftSession->posted ?? 0) === 0;
            $stagingService = new StagingService();
            $criteriaApplies = $stagingService->hasStagingOrRemainsInStaging(self::$menu_dtl_id, $formId, $wasInStaging);

            if (!$criteriaApplies) {
                $shiftSession->current_stg_id = null;
                $shiftSession->staging_apply = 1;
                $shiftSession->posted = 1;
                $shiftSession->save();
                \App\Models\TblStgFormLog::where('menu_dtl_id', self::$menu_dtl_id)
                    ->where('document_id', $formId)
                    ->update(['posted' => 1]);
            } else {
                if (isset($shiftSession->posted) && (int) $shiftSession->posted === 1) {
                    $shiftSession->posted = 0;
                }
                if (isset($shiftSession->staging_apply) && (int) $shiftSession->staging_apply === 1) {
                    $shiftSession->staging_apply = 0;
                }
                $flows = null;
                if (empty($shiftSession->current_stg_id)) {
                    $flows = $stagingService->getFormFlows(self::$menu_dtl_id, null, $formId, $wasInStaging);
                    if (!empty($flows['all'])) {
                        $shiftSession->current_stg_id = $flows['all'][0]->stg_flows_id;
                    }
                    Log::debug('[shift_sessions.store] empty current_stg_id — seeded first flow', [
                        'form_id' => $formId,
                        'current_stg_id' => $shiftSession->current_stg_id ?? null,
                        'first_flow_id' => !empty($flows['all']) ? ($flows['all'][0]->stg_flows_id ?? null) : null,
                    ]);
                }

                $this->handleStaging($request, self::$menu_dtl_id, $formId, $shiftSession, false, [
                    'listing_view' => 'vw_shift_sessions_listing',
                    'form_path' => '/shift_sessions/form',
                    'document_code_key' => 'session_no',
                ]);

                $currentFlowId = $request->input('current_flow_id');
                $nextFlowId = $request->input('next_flow_id');
                $actionId = $request->input('current_actions_id');
                $forwardBlockRan = false;
                $forwardMatched = false;
                if ($currentFlowId !== null && $currentFlowId !== '' && $nextFlowId !== null && $nextFlowId !== '' && $actionId !== null && $actionId !== '') {
                    $forwardBlockRan = true;
                    $actions = $stagingService->getFormActions(self::$menu_dtl_id, $currentFlowId, $formId, $wasInStaging);
                    $actionIdResolved = false;
                    Log::debug('[shift_sessions.store] forward block inputs', [
                        'form_id' => $formId,
                        'current_flow_id' => $currentFlowId,
                        'next_flow_id' => $nextFlowId,
                        'current_actions_id' => $actionId,
                        'actions_count' => is_countable($actions) ? count($actions) : null,
                    ]);
                    foreach ($actions as $action) {
                        if ((string) $action->stg_actions_id === (string) $actionId) {
                            $actionIdResolved = true;
                            $name = strtolower($action->stg_actions_name ?? '');
                            $orig = strtolower($action->original_action ?? $name);
                            $forwardMatched = in_array($name, ['forward'], true) || in_array($orig, ['forward'], true);
                            Log::debug('[shift_sessions.store] forward block matched action', [
                                'stg_actions_id' => $action->stg_actions_id ?? null,
                                'stg_actions_name' => $action->stg_actions_name ?? null,
                                'original_action' => $action->original_action ?? null,
                                'is_forward' => $forwardMatched,
                            ]);
                            if ($forwardMatched) {
                                $shiftSession->current_stg_id = $nextFlowId;
                            }
                            break;
                        }
                    }
                }

                $shiftSession->save();
            }

            DB::commit();

            if(isset($id)){
                $data = array_merge($data, Utilities::returnJsonEditForm());
                $isInStaging = !empty($shiftSession->current_stg_id) && (int)($shiftSession->posted ?? 0) === 0;
                $data['redirect'] = $isInStaging
                    ? '/'.self::$redirect_url.'/form/'.$formId
                    : $this->prefixIndexPage.self::$redirect_url;
                return $this->jsonSuccessResponse($data, trans('message.update'), 200);
            }

            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.'/form/'.$formId;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[shift_sessions.store] exception', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'route_id' => $id ?? null,
            ]);
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return $this->jsonErrorResponse($data, $e->getMessage() ?: 'An error occurred while updating the shift session. Please try again.', $status);
        }

    }

    public function post(Request $request)
    {
        $postPerm = self::$menu_dtl_id . '-post';
        if (!auth()->user()->isAbleTo($postPerm)) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to post.'], 403);
        }

        $session_id = $request->session_id;
        if (empty($session_id)) {
            return response()->json(['status' => 'error', 'message' => 'Session id not found.'], 422);
        }

        $row = ShiftSession::where('session_id', $session_id)->first();
        if (!$row) {
            return $this->jsonErrorResponse([], 'Session not found.', 422);
        }
        if (!empty($row->current_stg_id) && (int)($row->posted ?? 0) === 0) {
            return response()->json(['status' => 'error', 'message' => 'Posting is handled by staging for this form.'], 422);
        }

        if (!Schema::hasColumn($row->getTable(), 'posted')) {
            return $this->jsonErrorResponse([], 'Posting is not supported for this record.', 422);
        }

        $row->posted = 1;
        $row->save();

        return response()->json(['status' => 'success']);
    }

    public function UnPosted(Request $request)
    {
        $unpostPerm = self::$menu_dtl_id . '-un_post_module';
        if (!auth()->user()->isAbleTo($unpostPerm)) {
            return $this->jsonErrorResponse([], 'You do not have permission to unpost.', 403);
        }

        $data = [];
        $ids = $request->data;
        if (!is_array($ids) || count($ids) === 0) {
            abort(404);
        }

        foreach ($ids as $id) {
            $row = ShiftSession::where('session_id', $id)->first();
            if ($row && !empty($row->current_stg_id) && (int)($row->posted ?? 0) === 0) {
                return $this->jsonErrorResponse([], 'Unposting is handled by staging for this form.', 422);
            }
        }

        $updated = 0;
        foreach ($ids as $id) {
            $row = ShiftSession::where('session_id', $id)->first();
            if ($row && Schema::hasColumn($row->getTable(), 'posted')) {
                $row->posted = 0;
                $row->save();
                $updated++;
            }
        }

        if ($updated === 0) {
            return $this->jsonErrorResponse([], 'Session not found.', 422);
        }

        return $this->jsonSuccessResponse($data, trans('Successfully Un-Posted'), 200);
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
        // dd($id);

         $data = [];
        DB::beginTransaction();
        try{
            ShiftSession::where('session_id',$id)->delete();
        }
        catch (QueryException $e) {
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
}
