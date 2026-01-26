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
use Exception;




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

        $data['users'] = User::where('business_id', auth()->user()->business_id)
                             ->where('company_id', auth()->user()->company_id)
                             ->where('branch_id', auth()->user()->branch_id)
                             ->select('id', 'name', 'email')
                             ->orderBy('name')
                             ->get();

        $data['roles'] = Role::where('business_id', auth()->user()->business_id)
                             ->where('company_id', auth()->user()->company_id)
                             ->where('branch_id', auth()->user()->branch_id)
                             ->select('id', 'name', 'display_name')
                             ->orderBy('display_name')
                             ->get();

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
            $flowCriteria->menu_flow_criteria_apply_at = date('Y-m-d', strtotime($request->menu_flow_criteria_apply_at));
            $flowCriteria->menu_flow_criteria_status = 1;
            $flowCriteria->menu_flow_criteria_entry_status = 1;
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
            return;
        }

        foreach ($flowStages as $index => $flowStage) {
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

            if (isset($flowStage['action']) && is_array($flowStage['action'])) {
                $this->storeFlowActions($flow->menu_flow_criteria_flow_id, $flowStage['action']);
            }

            if (isset($flowStage['users']) && is_array($flowStage['users'])) {
                $this->storeFlowUsers($flow->menu_flow_criteria_flow_id, $flowStage['users']);
            }

            if (isset($flowStage['designation']) && is_array($flowStage['designation'])) {
                $this->storeFlowDesignations($flow->menu_flow_criteria_flow_id, $flowStage['designation']);
            }

            if (isset($flowStage['bypass_users']) && is_array($flowStage['bypass_users'])) {
                $this->storeFlowBypass($flow->menu_flow_criteria_flow_id, $flowStage['bypass_users'], 'user');
            }

            if (isset($flowStage['bypass_designation']) && is_array($flowStage['bypass_designation'])) {
                $this->storeFlowBypass($flow->menu_flow_criteria_flow_id, $flowStage['bypass_designation'], 'designation');
            }
        }
    }

    private function storeFlowActions($flowId, $actions)
    {
        $actionNames = ['Archive', 'New', 'Pull Back', 'Save'];

        foreach ($actions as $actionName) {
            if (in_array($actionName, $actionNames)) {
                $action = new TblMenuFlowCriteriaFlowAction();
                $action->menu_flow_criteria_flow_action_id = $this->generateUuid();
                $action->menu_flow_criteria_flow_id = $flowId;
                $action->action_name = $actionName;
                $action->send_notification = 0; // Default, can be enhanced later
                $action->save();
            }
        }
    }

    private function storeFlowUsers($flowId, $userIds)
    {
        foreach ($userIds as $userId) {
            if (!empty($userId)) {
                $flowUser = new TblMenuFlowCriteriaFlowUser();
                $flowUser->menu_flow_criteria_flow_user_id = $this->generateUuid();
                $flowUser->menu_flow_criteria_flow_id = $flowId;
                $flowUser->user_id = $userId;
                $flowUser->save();
            }
        }
    }

    private function storeFlowDesignations($flowId, $designationIds)
    {
        foreach ($designationIds as $designationId) {
            if (!empty($designationId)) {
                $flowDesignation = new TblMenuFlowCriteriaFlowDesignation();
                $flowDesignation->menu_flow_criteria_flow_designation_id = $this->generateUuid();
                $flowDesignation->menu_flow_criteria_flow_id = $flowId;
                $flowDesignation->designation_id = $designationId;
                $flowDesignation->save();
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

        $lastRecord = TblMenuFlowCriteria::where('business_id', auth()->user()->business_id)
            ->where('company_id', auth()->user()->company_id)
            ->where('branch_id', auth()->user()->branch_id)
            ->where('menu_flow_criteria_dtl_id', 'LIKE', $prefix . '-%')
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
            '2' => 'Approval',
            '3' => 'Director Approval',
            '4' => 'Manager Approval',
            '5' => 'Posting'
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
        $flowCriteria = TblMenuFlowCriteria::with([
            'conditions',
            'flows.actions',
            'flows.users',
            'flows.designations',
            'flows.bypasses'
        ])->where('menu_flow_criteria_id', $id)
          ->where('business_id', auth()->user()->business_id)
          ->where('company_id', auth()->user()->company_id)
          ->where('branch_id', auth()->user()->branch_id)
          ->firstOrFail();

        $data['menu'] = TblSoftMenuDtl::all();
        $data['flow'] = TblSoftFlow::where('menu_flow_entry_status',1)->get();
        $data['event'] = TblSoftEvent::where('menu_event_entry_status',1)->get();
        $data['action'] = TblSoftAction::where('menu_action_entry_status',1)->get();
        $data['length'] = count($data['action']);
        $data['warranty_period'] = TblPurcWarrentyPeriod::where('warrenty_period_entry_status',1)->get();

        $data['users'] = User::where('business_id', auth()->user()->business_id)
                             ->where('company_id', auth()->user()->company_id)
                             ->where('branch_id', auth()->user()->branch_id)
                             ->select('id', 'name', 'email')
                             ->orderBy('name')
                             ->get();

        $data['roles'] = Role::where('business_id', auth()->user()->business_id)
                             ->where('company_id', auth()->user()->company_id)
                             ->where('branch_id', auth()->user()->branch_id)
                             ->select('id', 'name', 'display_name')
                             ->orderBy('display_name')
                             ->get();

        $data['flowCriteria'] = $flowCriteria;

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
            ->where('business_id', auth()->user()->business_id)
            ->where('company_id', auth()->user()->company_id)
            ->where('branch_id', auth()->user()->branch_id)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            $flowCriteria->menu_flow_criteria_name = $request->menu_flow_criteria_name;
            $flowCriteria->menu_flow_criteria_apply_at = date('Y-m-d', strtotime($request->menu_flow_criteria_apply_at));
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
