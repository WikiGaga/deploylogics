<?php

namespace App\Http\Controllers;

use App\Traits\HasStaging;
use Illuminate\Http\Request;

/**
 * Base Form Controller with automatic staging support
 *
 * To use staging in your form controller:
 *
 * Option 1: Extend this class and implement getMenuDtlId()
 *
 * Option 2: Use HasStaging trait in your controller and call handleStaging() in store/update methods
 *
 * Example (Option 2 - Recommended):
 *
 * class YourController extends Controller {
 *     use HasStaging;
 *
 *     public static $menu_dtl_id = '38';
 *
 *     public function create($id = null) {
 *         $data = [...];
 *         $data['menu_dtl_id'] = self::$menu_dtl_id; // Required for view composer
 *         return view('your.form', compact('data'));
 *     }
 *
 *     public function store(Request $request, $id = null) {
 *         $model = new YourModel();
 *         // ... save model ...
 *
 *         // Add this one line to handle staging automatically
 *         $this->handleStaging($request, self::$menu_dtl_id, $model->id, $model, !isset($id));
 *
 *         $model->save();
 *     }
 * }
 *
 * In your form blade file, just add before </form>:
 * @include('staging_activity.auto_include')
 */
abstract class BaseFormController extends Controller
{
    use HasStaging;

    /**
     * Get the menu_dtl_id for this controller
     * Override in child controllers
     */
    abstract protected function getMenuDtlId();

    /**
     * Get the form name for staging (optional, defaults to menu_dtl_id)
     * Override in child controllers if needed
     */
    protected function getFormNameForStaging()
    {
        return $this->getMenuDtlId();
    }

    /**
     * Handle staging after form save/update
     * Call this method from your store/update methods
     */
    protected function handleStaging(Request $request, $formId, $isNew = false)
    {
        $menuDtlId = $this->getMenuDtlId();
        $service = $this->getStagingService();

        if (!$service->hasStaging($menuDtlId, $formId)) {
            return;
        }

        // Handle staging flow update
        if ($request->has('current_flow_id') && $request->has('current_actions_id')) {
            // Log staging activity
            $this->logStagingActivity(
                $menuDtlId,
                $formId,
                $request->current_flow_id,
                $request->current_actions_id,
                $request->flow_remarks ?? null,
                0 // Not posted yet
            );

            // Update to next flow if Forward/Post action
            $actions = $service->getFormActions($menuDtlId, $request->current_flow_id, $formId);
            $actionName = null;
            foreach ($actions as $action) {
                if ($action->stg_actions_id == $request->current_actions_id) {
                    $actionName = $action->stg_actions_name;
                    break;
                }
            }

            if ($actionName == 'forward' && $request->has('next_flow_id')) {
                // Move to next flow stage - return flow ID to update in model
                return [
                    'current_stg_id' => $request->next_flow_id,
                    'staging_apply' => 0
                ];
            } elseif ($actionName == 'back' && $request->has('prev_flow_id')) {
                // Move back to previous flow stage
                return [
                    'current_stg_id' => $request->prev_flow_id,
                    'staging_apply' => 0
                ];
            } elseif ($actionName == 'forward' && !$request->has('next_flow_id')) {
                // Last stage - mark as posted
                \App\Models\TblStgFormLog::where('menu_dtl_id', $menuDtlId)
                    ->where('document_id', $formId)
                    ->where('stg_flows_id', $request->current_flow_id)
                    ->where('stg_actions_id', $request->current_actions_id)
                    ->where('posted', 0)
                    ->update(['posted' => 1]);

                return [
                    'current_stg_id' => $request->current_flow_id,
                    'staging_apply' => 1
                ];
            }
        } elseif ($isNew) {
            // New document - set to first flow
            $flows = $service->getFormFlows($menuDtlId, null, null);
            if (!empty($flows['all'])) {
                return [
                    'current_stg_id' => $flows['all'][0]->stg_flows_id,
                    'staging_apply' => 0
                ];
            }
        }

        return null;
    }
}
