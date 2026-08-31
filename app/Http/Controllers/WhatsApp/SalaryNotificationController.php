<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use App\Jobs\SendSalaryNotificationJob;
use App\Library\Utilities;
use App\Models\TblWaSalaryNotificationBatch;
use App\Models\TblWaSalaryNotificationDtl;
use App\Services\SalaryExcelParser;
use App\Services\WhatsappService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Validator;

class SalaryNotificationController extends Controller
{
    public static $page_title = 'Salary Notifications';
    public static $redirect_url = 'salary-notifications';
    public static $menu_dtl_id = '363';

    protected $whatsappService;
    protected $parser;

    public function __construct(WhatsappService $whatsappService, SalaryExcelParser $parser)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
        $this->parser = $parser;
    }

    public function create($id = null)
    {
        if ($id) {
            return $this->show($id);
        }

        $data = [];
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;
        $data['menu_id'] = self::$menu_dtl_id;
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['permission'] = self::$menu_dtl_id . '-create';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Upload';
        $data['page_data']['type'] = 'Upload';

        return view('whatsapp.salary_notifications.form', compact('data'));
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return $this->jsonErrorResponse([], $validator->errors()->first(), 422);
        }

        try {
            $file = $request->file('file');
            $previewToken = Utilities::uuid();
            $storedName = $previewToken . '.' . $file->getClientOriginalExtension();
            $storedPath = 'salary-notifications/' . $storedName;
            Storage::disk('local')->putFileAs('salary-notifications', $file, $storedName);

            $absolutePath = storage_path('app/' . $storedPath);
            $parsed = $this->parser->parse($absolutePath);

            Cache::put($this->cacheKey($previewToken), [
                'file_name' => $file->getClientOriginalName(),
                'stored_path' => $storedPath,
                'parsed' => $parsed,
            ], now()->addMinutes(30));

            return $this->jsonSuccessResponse([
                'preview_token' => $previewToken,
                'pay_period' => $parsed['pay_period'],
                'total_rows' => $parsed['total_rows'],
                'valid_rows' => $parsed['valid_rows'],
                'error_rows' => $parsed['error_rows'],
                'rows' => array_map(function ($row) {
                    return [
                        'row_no' => $row['row_no'],
                        'employee_name' => $row['employee_name'],
                        'phone' => $row['phone'],
                        'phone_raw' => $row['phone_raw'],
                        'net_payment' => $row['net_payment'],
                        'preview_text' => $row['preview_text'],
                        'errors' => $row['errors'],
                        'is_valid' => $row['is_valid'],
                    ];
                }, $parsed['rows']),
            ], 'Preview generated successfully.', 200);
        } catch (Exception $e) {
            return $this->jsonErrorResponse([], $e->getMessage(), 422);
        }
    }

    public function send(Request $request)
    {
        if (!$this->whatsappService->isLiveMode()) {
            return $this->jsonErrorResponse([], 'WhatsApp is not in LIVE mode. Set WHATSAPP_MODE=LIVE in .env.', 422);
        }

        $validator = Validator::make($request->all(), [
            'preview_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonErrorResponse([], $validator->errors()->first(), 422);
        }

        $cacheKey = $this->cacheKey($request->preview_token);
        $cached = Cache::get($cacheKey);

        if (!$cached) {
            return $this->jsonErrorResponse([], 'Preview session expired. Please upload the file again.', 422);
        }

        $parsed = $cached['parsed'];
        if ($parsed['error_rows'] > 0) {
            return $this->jsonErrorResponse([], 'Cannot send while there are row errors. Fix the Excel file and preview again.', 422);
        }

        $validRows = array_filter($parsed['rows'], function ($row) {
            return $row['is_valid'];
        });

        if (count($validRows) === 0) {
            return $this->jsonErrorResponse([], 'No valid rows to send.', 422);
        }

        DB::beginTransaction();

        try {
            $batchId = Utilities::uuid();
            $now = date('Y-m-d H:i:s');

            TblWaSalaryNotificationBatch::create([
                'batch_id' => $batchId,
                'pay_period' => $parsed['pay_period'],
                'file_name' => $cached['file_name'],
                'template_name' => config('whatsapp.salary_notification.template_name'),
                'template_lang' => config('whatsapp.salary_notification.template_lang', 'en'),
                'total_rows' => count($validRows),
                'queued_count' => count($validRows),
                'sent_count' => 0,
                'failed_count' => 0,
                'status' => 'queued',
                'user_id' => auth()->id(),
                'business_id' => auth()->user()->business_id,
                'company_id' => auth()->user()->company_id,
                'branch_id' => auth()->user()->branch_id,
                'created_at' => $now,
            ]);

            foreach ($validRows as $row) {
                $dtlId = Utilities::uuid();

                TblWaSalaryNotificationDtl::create([
                    'dtl_id' => $dtlId,
                    'batch_id' => $batchId,
                    'row_no' => $row['row_no'],
                    'employee_name' => $row['employee_name'],
                    'phone' => $row['phone'],
                    'net_payment' => $row['net_payment'],
                    'template_params' => json_encode($row['named_params']),
                    'status' => 'queued',
                    'created_at' => $now,
                ]);

                SendSalaryNotificationJob::dispatch($dtlId)
                    ->onConnection('database')
                    ->onQueue('whatsapp');
            }

            DB::commit();

            Cache::forget($cacheKey);
            if (!empty($cached['stored_path'])) {
                Storage::disk('local')->delete($cached['stored_path']);
            }

            return $this->jsonSuccessResponse([
                'batch_id' => $batchId,
                'queued_count' => count($validRows),
                'redirect_url' => url('/salary-notifications/form/' . $batchId),
                'listing_url' => url('/listing/salary-notifications'),
            ], count($validRows) . ' message(s) queued for sending.', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse([], $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $batch = TblWaSalaryNotificationBatch::with('details')
            ->where('batch_id', $id)
            ->where(Utilities::currentBCB())
            ->first();

        if (!$batch) {
            abort(404);
        }

        $data = [];
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;
        $data['menu_id'] = self::$menu_dtl_id;
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['permission'] = self::$menu_dtl_id . '-view';
        $data['page_data'] = array_merge($data['page_data'], Utilities::viewForm());
        $data['page_data']['type'] = 'Detail';
        $data['batch'] = $batch;
        $data['id'] = $id;

        return view('whatsapp.salary_notifications.show', compact('data'));
    }

    protected function cacheKey($token)
    {
        return 'salary_notification_preview_' . $token;
    }
}
