<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSystemConfiguration;
use App\Models\TblBranchSyncState;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SystemConfigurationController extends Controller
{
    public static $page_title = 'System Configuration';
    public static $redirect_url = 'system-configuration';
    public static $menu_dtl_id = '348';

    public function create()
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['type'] = 'edit';
        $data['page_data']['action'] = 'Save';

        $branchId = session('user_branch') ?? auth()->user()->branch_id ?? null;

        try {
            if ($branchId) {
                $data['configurations'] = TblSystemConfiguration::getAll(null, $branchId);
            } else {
                $data['configurations'] = [];
            }
        } catch (\Exception $e) {
            $data['configurations'] = [];
            \Log::error('SystemConfiguration getAll error: ' . $e->getMessage());
        }

        if (!isset($data['configurations']) || !is_array($data['configurations'])) {
            $data['configurations'] = [];
        }

        try {
            if ($branchId) {
                if (\Schema::hasTable('branch_sync_state')) {
                    $data['branch_sync_status'] = TblBranchSyncState::where('restaurant_id', $branchId)
                        ->orderBy('entity_type')
                        ->orderBy('last_synced_at', 'desc')
                        ->get();
                } else {
                    $data['branch_sync_status'] = collect([]);
                }
            } else {
                $data['branch_sync_status'] = collect([]);
            }
        } catch (\Exception $e) {
            $data['branch_sync_status'] = collect([]);
            \Log::error('SystemConfiguration branch_sync_status error: ' . $e->getMessage());
        }

        $data['permission'] = !empty(self::$menu_dtl_id) ? self::$menu_dtl_id.'-edit' : '';

        return view('development.system_configuration.form', compact('data'));
    }

    public function store(Request $request)
    {
        $data = [];

        $configDefinitions = $this->getConfigDefinitions();

        $validationRules = [];
        foreach ($configDefinitions as $key => $def) {
            if (isset($def['validation'])) {
                $validationRules[$key] = $def['validation'];
            }
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        DB::beginTransaction();
        try{
            $branchId = session('user_branch') ?? auth()->user()->branch_id;

            foreach ($configDefinitions as $key => $def) {
                $value = $request->input($key);

                if ($def['type'] === 'boolean') {
                    $value = $request->has($key) ? 1 : 0;
                }

                if ($value !== null || $def['type'] === 'boolean') {
                    TblSystemConfiguration::setValue(
                        $key,
                        $value,
                        $def['type'],
                        $def['group'],
                        $def['description'] ?? null,
                        $branchId
                    );
                }
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
        return $this->jsonSuccessResponse($data, 'System configuration saved successfully', 200);
    }

    private function getConfigDefinitions()
    {
        return [
            'purchase_per_purchase_limit' => [
                'type' => 'number',
                'group' => 'purchase',
                'description' => 'Purchase Above Per Purchase Limit',
                'validation' => 'nullable|numeric|min:0',
            ],
            'purchase_monthly_budget' => [
                'type' => 'number',
                'group' => 'purchase',
                'description' => 'Monthly Purchase Above Monthly Budget',
                'validation' => 'nullable|numeric|min:0',
            ],
            'po_per_order_limit' => [
                'type' => 'number',
                'group' => 'purchase',
                'description' => 'PO Above Per Order Limit',
                'validation' => 'nullable|numeric|min:0',
            ],
            'purchase_rate_increase_percentage' => [
                'type' => 'number',
                'group' => 'purchase',
                'description' => 'Purchase Rate Increase Percentage',
                'validation' => 'nullable|numeric|min:0|max:100',
            ],
            'purchase_per_purchase_limit_whatsapp' => [
                'type' => 'boolean',
                'group' => 'purchase',
                'description' => 'Notify via WhatsApp when purchase exceeds per purchase limit',
            ],
            'purchase_per_purchase_limit_inapp' => [
                'type' => 'boolean',
                'group' => 'purchase',
                'description' => 'Notify in-app when purchase exceeds per purchase limit',
            ],
            'purchase_monthly_budget_whatsapp' => [
                'type' => 'boolean',
                'group' => 'purchase',
                'description' => 'Notify via WhatsApp when monthly purchase exceeds budget',
            ],
            'purchase_monthly_budget_inapp' => [
                'type' => 'boolean',
                'group' => 'purchase',
                'description' => 'Notify in-app when monthly purchase exceeds budget',
            ],
            'po_per_order_limit_whatsapp' => [
                'type' => 'boolean',
                'group' => 'purchase',
                'description' => 'Notify via WhatsApp when PO exceeds per order limit',
            ],
            'po_per_order_limit_inapp' => [
                'type' => 'boolean',
                'group' => 'purchase',
                'description' => 'Notify in-app when PO exceeds per order limit',
            ],
            'purchase_rate_increase_whatsapp' => [
                'type' => 'boolean',
                'group' => 'purchase',
                'description' => 'Notify via WhatsApp when purchase rate increases',
            ],
            'purchase_rate_increase_inapp' => [
                'type' => 'boolean',
                'group' => 'purchase',
                'description' => 'Notify in-app when purchase rate increases',
            ],
            'inventory_stock_difference_percentage' => [
                'type' => 'number',
                'group' => 'inventory',
                'description' => 'Stock Difference Percentage (Wastage)',
                'validation' => 'nullable|numeric|min:0|max:100',
            ],
            'inventory_reorder_level' => [
                'type' => 'number',
                'group' => 'inventory',
                'description' => 'Reorder Level',
                'validation' => 'nullable|numeric|min:0',
            ],
            'inventory_minimum_level' => [
                'type' => 'number',
                'group' => 'inventory',
                'description' => 'Minimum Level',
                'validation' => 'nullable|numeric|min:0',
            ],
            'inventory_stock_difference_whatsapp' => [
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Notify via WhatsApp when stock difference exceeds percentage',
            ],
            'inventory_stock_difference_inapp' => [
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Notify in-app when stock difference exceeds percentage',
            ],
            'inventory_reorder_level_whatsapp' => [
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Notify via WhatsApp when stock is below reorder level',
            ],
            'inventory_reorder_level_inapp' => [
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Notify in-app when stock is below reorder level',
            ],
            'inventory_minimum_level_whatsapp' => [
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Notify via WhatsApp when stock is below minimum level',
            ],
            'inventory_minimum_level_inapp' => [
                'type' => 'boolean',
                'group' => 'inventory',
                'description' => 'Notify in-app when stock is below minimum level',
            ],
            'branch_sync_notify_hours' => [
                'type' => 'number',
                'group' => 'branch_sync',
                'description' => 'Notify If Not Synced (Hours)',
                'validation' => 'nullable|numeric|min:0',
            ],
            'branch_sync_notify_whatsapp' => [
                'type' => 'boolean',
                'group' => 'branch_sync',
                'description' => 'Notify via WhatsApp if branch not synced',
            ],
            'branch_sync_notify_inapp' => [
                'type' => 'boolean',
                'group' => 'branch_sync',
                'description' => 'Notify in-app if branch not synced',
            ],
            'api_whatsapp_key' => [
                'type' => 'text',
                'group' => 'api',
                'description' => 'WhatsApp API Key',
            ],
            'api_whatsapp_secret' => [
                'type' => 'text',
                'group' => 'api',
                'description' => 'WhatsApp API Secret',
            ],
            'api_sms_key' => [
                'type' => 'text',
                'group' => 'api',
                'description' => 'SMS API Key',
            ],
            'api_sms_secret' => [
                'type' => 'text',
                'group' => 'api',
                'description' => 'SMS API Secret',
            ],
            'api_email_key' => [
                'type' => 'text',
                'group' => 'api',
                'description' => 'Email API Key',
            ],
            'api_email_secret' => [
                'type' => 'text',
                'group' => 'api',
                'description' => 'Email API Secret',
            ],
            'api_other_config' => [
                'type' => 'json',
                'group' => 'api',
                'description' => 'Other API Configurations (JSON)',
            ],
        ];
    }
}

