<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use App\Models\TblPurcWarrentyPeriod;
use App\Models\TblSoftFlow;
use App\Models\TblSoftEvent;
use App\Models\TblSoftAction;
use App\Models\TblSoftMenuDtl;
use App\Models\User;
use App\Models\Role;
use App\Models\TblMenuFlowCriteria;
use App\Models\TblMenuFlowCriteriaCondition;
use App\Models\TblMenuFlowCriteriaFlow;
use App\Models\TblMenuFlowCriteriaFlowAction;
use App\Models\TblMenuFlowCriteriaFlowUser;
use App\Models\TblMenuFlowCriteriaFlowDesignation;
use App\Models\TblMenuFlowCriteriaFlowBypass;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;




class FlowCriteriaController extends Controller
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
    public function create()
    {
        $data['menu']     = TblSoftMenuDtl::all();
        $data['flow']     = TblSoftFlow::where('menu_flow_entry_status',1)->get();
        $data['event']    = TblSoftEvent::where('menu_event_entry_status',1)->get();
        $data['action']   = TblSoftAction::where('menu_action_entry_status',1)->get();
        $data['length']   = count($data['action']);
        $data['warranty_period'] = TblPurcWarrentyPeriod::where('warrenty_period_entry_status',1)->get();

        $data['users'] = User::select('id', 'name', 'email')
                             ->orderBy('name')
                             ->get();

        $data['roles'] = Role::select('id', 'name', 'display_name')
                             ->orderBy('display_name')
                             ->get();

        $data['page_data'] = [
            'title'      => 'Flow Criteria',
            'type'       => 'new',
            'create'     => action('Development\FlowCriteriaController@create'),
            'action'     => 'Save',
            'path_index' => url()->previous(),
        ];

        return view('development.flow_criteria.add', compact('data'));
    }

    public function getAjaxData($formtble)
    {

        $columns = Schema::getColumnListing($formtble);
        return response()->json($columns);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_flow_criteria_name' => 'required|string',
            'menu_flow_criteria_apply_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $flowCriteria = new TblMenuFlowCriteria();
            $flowCriteria->menu_flow_criteria_id = $this->generateUuid();
            $flowCriteria->menu_flow_criteria_dtl_id = $this->generateReferenceId();
            $flowCriteria->menu_flow_criteria_name = $request->menu_flow_criteria_name;
            $flowCriteria->menu_dtl_id = $request->menu_dtl_id ?: null;
            $flowCriteria->menu_flow_criteria_apply_at = date('Y-m-d', strtotime($request->menu_flow_criteria_apply_at));
            $enabled = (int) $request->get('menu_flow_criteria_status', 1);
            $flowCriteria->menu_flow_criteria_status = $enabled;
            $flowCriteria->menu_flow_criteria_entry_status = $enabled;
            $flowCriteria->business_id = auth()->user()->business_id;
            $flowCriteria->company_id = auth()->user()->company_id;
            $flowCriteria->branch_id = auth()->user()->branch_id;
            $flowCriteria->created_by = auth()->user()->id;
            $flowCriteria->save();

            if ($request->has('criteria_conditions')) {
                $this->storeCriteriaConditions($flowCriteria->menu_flow_criteria_id, $request->criteria_conditions);
            }

            if ($request->has('flow_criteria_data')) {
                $this->storeFlowStages($flowCriteria->menu_flow_criteria_id, $request->flow_criteria_data);
            } else {
                Log::warning('No flow_criteria_data in request');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Flow Criteria saved successfully! Reference ID: ' . $flowCriteria->menu_flow_criteria_dtl_id,
                'data' => [
                    'id' => $flowCriteria->menu_flow_criteria_id,
                    'reference_id' => $flowCriteria->menu_flow_criteria_dtl_id
                ]
            ], 200);

        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error saving Flow Criteria',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    private function storeCriteriaConditions($criteriaId, $conditions)
    {
        if (!is_array($conditions)) {
            return;
        }

        foreach ($conditions as $index => $condition) {
            if (empty($condition['field']) || empty($condition['operator'])) {
                continue;
            }

            $criteriaCondition = new TblMenuFlowCriteriaCondition();
            $criteriaCondition->menu_flow_criteria_condition_id = $this->generateUuid();
            $criteriaCondition->menu_flow_criteria_id = $criteriaId;
            $criteriaCondition->condition_sr_number = $index + 1;
            $criteriaCondition->condition_field = $condition['field'];
            $criteriaCondition->condition_operator = $condition['operator'];
            $criteriaCondition->condition_value = $condition['value'] ?? '';
            $criteriaCondition->condition_logic_operator = $condition['logic_operator'] ?? null;
            $criteriaCondition->save();
        }
    }

    private function storeFlowStages($criteriaId, $flowStages)
    {
        if (!is_array($flowStages)) {
            Log::warning('storeFlowStages: flowStages is not an array', ['flowStages' => $flowStages]);
            return;
        }

        foreach ($flowStages as $index => $flowStage) {
            try {
                $flow = new TblMenuFlowCriteriaFlow();
                $flow->menu_flow_criteria_flow_id = $this->generateUuid();
                $flow->menu_flow_criteria_id = $criteriaId;
                $flow->stg_flows_id = $flowStage['form_flow_criteria'] ?? null;
                $flow->flow_order = $index + 1;
                $flow->flow_name = $this->getFlowName($flowStage['form_flow_criteria'] ?? 0);
                $flow->lead_time_value = $flowStage['product_warranty_mode'] ?? null;
                $flow->lead_time_unit = $this->getTimeUnit($flowStage['product_warranty_period'] ?? 0);
                $flow->reminder_time_minutes = $flowStage['reminder_time'] ?? null;
                $flow->require_all_users = isset($flowStage['select_user']) && $flowStage['select_user'] == 'all' ? 1 : 0;
                $flow->save();

                if (isset($flowStage['action']) && is_array($flowStage['action']) && count($flowStage['action']) > 0) {
                    $this->storeFlowActions($flow->menu_flow_criteria_flow_id, $flowStage['action']);
                }

                if (isset($flowStage['users'])) {
                    if (is_array($flowStage['users']) && count($flowStage['users']) > 0) {
                        $this->storeFlowUsers($flow->menu_flow_criteria_flow_id, $flowStage['users']);
                    }
                }

                if (isset($flowStage['designation'])) {
                    if (is_array($flowStage['designation']) && count($flowStage['designation']) > 0) {
                        $this->storeFlowDesignations($flow->menu_flow_criteria_flow_id, $flowStage['designation']);
                    }
                }

                if (isset($flowStage['bypass_users']) && is_array($flowStage['bypass_users']) && count($flowStage['bypass_users']) > 0) {
                    $this->storeFlowBypass($flow->menu_flow_criteria_flow_id, $flowStage['bypass_users'], 'user');
                }

                if (isset($flowStage['bypass_designation']) && is_array($flowStage['bypass_designation']) && count($flowStage['bypass_designation']) > 0) {
                    $this->storeFlowBypass($flow->menu_flow_criteria_flow_id, $flowStage['bypass_designation'], 'designation');
                }
            } catch (\Exception $e) {
                Log::error('Error storing flow stage ' . $index . ':', [
                    'error' => $e->getMessage(),
                    'flowStage' => $flowStage
                ]);
                throw $e;
            }
        }
    }

    private function storeFlowActions($flowId, $actions)
    {
        foreach ($actions as $actionName) {
            $normalized = strtolower(trim($actionName));

            $canonicalMap = [
                'create' => 'save',
                'edit' => 'save',
                'save' => 'save',
                'forward' => 'forward',
                'post' => 'post',
                'back' => 'back',
                'cancel' => 'cancel',
                'un post' => 'un_post',
                'un_post' => 'un_post',
                'unpost' => 'un_post',
                'archive' => 'archive',
                'new' => 'new',
                'pull back' => 'pull_back',
            ];

            if (array_key_exists($normalized, $canonicalMap)) {
                $storedName = $canonicalMap[$normalized];

                $action = new TblMenuFlowCriteriaFlowAction();
                $action->menu_flow_criteria_flow_action_id = $this->generateUuid();
                $action->menu_flow_criteria_flow_id = $flowId;
                $action->action_name = $storedName;
                $action->send_notification = 0;
                $action->save();
            }
        }
    }

    private function storeFlowUsers($flowId, $userIds)
    {
        if (!is_array($userIds)) {
            Log::warning('storeFlowUsers: userIds is not an array', ['userIds' => $userIds]);
            return;
        }

        foreach ($userIds as $userId) {
            $userId = is_numeric($userId) ? (int)$userId : $userId;

            if (!empty($userId) && $userId !== '0' && $userId !== 0) {
                try {
                    $flowUser = new TblMenuFlowCriteriaFlowUser();
                    $flowUser->menu_flow_criteria_flow_user_id = $this->generateUuid();
                    $flowUser->menu_flow_criteria_flow_id = $flowId;
                    $flowUser->user_id = $userId;
                    $flowUser->save();
                } catch (\Exception $e) {
                    Log::error('Error saving flow user:', [
                        'flow_id' => $flowId,
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    private function storeFlowDesignations($flowId, $designationIds)
    {
        if (!is_array($designationIds)) {
            Log::warning('storeFlowDesignations: designationIds is not an array', ['designationIds' => $designationIds]);
            return;
        }

        foreach ($designationIds as $designationId) {
            $designationId = is_numeric($designationId) ? (int)$designationId : $designationId;

            if (!empty($designationId) && $designationId !== '0' && $designationId !== 0) {
                try {
                    $flowDesignation = new TblMenuFlowCriteriaFlowDesignation();
                    $flowDesignation->menu_flow_criteria_flow_designation_id = $this->generateUuid();
                    $flowDesignation->menu_flow_criteria_flow_id = $flowId;
                    $flowDesignation->designation_id = $designationId;
                    $flowDesignation->save();
                } catch (\Exception $e) {
                    Log::error('Error saving flow designation:', [
                        'flow_id' => $flowId,
                        'designation_id' => $designationId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    private function storeFlowBypass($flowId, $bypassIds, $type)
    {
        foreach ($bypassIds as $bypassId) {
            if (!empty($bypassId)) {
                $bypass = new TblMenuFlowCriteriaFlowBypass();
                $bypass->menu_flow_criteria_flow_bypass_id = $this->generateUuid();
                $bypass->menu_flow_criteria_flow_id = $flowId;
                $bypass->bypass_type = $type;

                if ($type == 'user') {
                    $bypass->bypass_user_id = $bypassId;
                } else {
                    $bypass->bypass_designation_id = $bypassId;
                }

                $bypass->save();
            }
        }
    }

    private function generateUuid()
    {
        return Uuid::generate()->string;
    }

    private function generateReferenceId()
    {
        $prefix = 'FC';

        $lastRecord = TblMenuFlowCriteria::where('menu_flow_criteria_dtl_id', 'LIKE', $prefix . '-%')
            ->orderBy('menu_flow_criteria_dtl_id', 'DESC')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->menu_flow_criteria_dtl_id, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function getFlowName($flowId)
    {
        $flowNames = [
            '0' => 'Select',
            '1' => 'Data Entry',
            '2' => 'Review',
            '3' => 'Manager Approval',
        ];

        return $flowNames[$flowId] ?? 'Unknown';
    }

    private function getTimeUnit($unitId)
    {
        $timeUnits = [
            '0' => null,
            '1' => 'Minutes',
            '2' => 'Hours',
            '3' => 'Days',
            '4' => 'Weeks',
            '5' => 'Month'
        ];

        return $timeUnits[$unitId] ?? null;
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $flowCriteria = TblMenuFlowCriteria::with([
            'conditions',
            'flows.actions',
            'flows.users',
            'flows.designations',
            'flows.bypasses'
        ])->where('menu_flow_criteria_id', $id)
          ->firstOrFail();

        $data['menu'] = TblSoftMenuDtl::all();
        $data['flow'] = TblSoftFlow::where('menu_flow_entry_status',1)->get();
        $data['event'] = TblSoftEvent::where('menu_event_entry_status',1)->get();
        $data['action'] = TblSoftAction::where('menu_action_entry_status',1)->get();
        $data['length'] = count($data['action']);
        $data['warranty_period'] = TblPurcWarrentyPeriod::where('warrenty_period_entry_status',1)->get();

        $data['users'] = User::select('id', 'name', 'email')
                             ->orderBy('name')
                             ->get();

        $data['roles'] = Role::select('id', 'name', 'display_name')
                             ->orderBy('display_name')
                             ->get();

        $data['flowCriteria'] = $flowCriteria;

        $data['page_data'] = [
            'title'      => 'Flow Criteria',
            'type'       => 'edit',
            'create'     => action('Development\FlowCriteriaController@create'),
            'action'     => 'Update',
            'path_index' => url()->previous(),
        ];

        return view('development.flow_criteria.add', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'menu_flow_criteria_name' => 'required|string',
            'menu_flow_criteria_apply_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $flowCriteria = TblMenuFlowCriteria::where('menu_flow_criteria_id', $id)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            $flowCriteria->menu_flow_criteria_name = $request->menu_flow_criteria_name;
            $flowCriteria->menu_dtl_id = $request->menu_dtl_id ?: $flowCriteria->menu_dtl_id;
            $flowCriteria->menu_flow_criteria_apply_at = date('Y-m-d', strtotime($request->menu_flow_criteria_apply_at));
            $enabled = (int) $request->get('menu_flow_criteria_status', 1);
            $flowCriteria->menu_flow_criteria_status = $enabled;
            $flowCriteria->menu_flow_criteria_entry_status = $enabled;
            $flowCriteria->updated_by = auth()->user()->id;
            $flowCriteria->save();

            $flowCriteria->conditions()->delete();
            if ($request->has('criteria_conditions')) {
                $this->storeCriteriaConditions($flowCriteria->menu_flow_criteria_id, $request->criteria_conditions);
            }

            foreach ($flowCriteria->flows as $flow) {
                $flow->actions()->delete();
                $flow->users()->delete();
                $flow->designations()->delete();
                $flow->bypasses()->delete();
            }
            $flowCriteria->flows()->delete();

            if ($request->has('flow_criteria_data')) {
                $this->storeFlowStages($flowCriteria->menu_flow_criteria_id, $request->flow_criteria_data);
            } else {
                Log::warning('No flow_criteria_data in update request');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Flow Criteria updated successfully! Reference ID: ' . $flowCriteria->menu_flow_criteria_dtl_id,
                'data' => [
                    'id' => $flowCriteria->menu_flow_criteria_id,
                    'reference_id' => $flowCriteria->menu_flow_criteria_dtl_id
                ]
            ], 200);

        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error updating Flow Criteria',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();
        try {
            $flowCriteria = TblMenuFlowCriteria::where('menu_flow_criteria_id', $id)
                ->first();

            if (!$flowCriteria) {
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            }

            foreach ($flowCriteria->flows as $flow) {
                $flow->actions()->delete();
                $flow->users()->delete();
                $flow->designations()->delete();
                $flow->bypasses()->delete();
            }
            $flowCriteria->flows()->delete();
            $flowCriteria->conditions()->delete();
            $flowCriteria->delete();

        } catch (QueryException $e) {
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
