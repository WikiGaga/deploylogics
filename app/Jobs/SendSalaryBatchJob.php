<?php

namespace App\Jobs;

use App\Models\TblWaSalaryNotificationBatch;
use App\Models\TblWaSalaryNotificationDtl;
use App\Services\WhatsappService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendSalaryBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batchId;
    public $tries = 1;
    public $timeout = 600;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    public function handle()
    {
        $batch = TblWaSalaryNotificationBatch::where('batch_id', $this->batchId)->first();
        if (!$batch) {
            throw new Exception('Salary notification batch not found: ' . $this->batchId);
        }

        $batch->status = 'processing';
        $batch->save();

        $details = TblWaSalaryNotificationDtl::where('batch_id', $this->batchId)
            ->where('status', 'queued')
            ->orderBy('row_no')
            ->get();

        if ($details->isEmpty()) {
            $this->refreshBatchCounts($this->batchId);
            return;
        }

        $service = new WhatsappService();

        foreach ($details as $index => $dtl) {
            if ($index > 0) {
                sleep(2);
            }

            try {
                $namedParams = $this->decodeTemplateParams($dtl->template_params);
                $response = $service->sendSalaryNotification($dtl->phone, $namedParams);

                $messageId = isset($response['messages'][0]['id']) ? $response['messages'][0]['id'] : null;

                $dtl->status = 'sent';
                $dtl->meta_message_id = $messageId;
                $dtl->api_response = json_encode($response);
                $dtl->message_exception = null;
                $dtl->sent_at = date('Y-m-d H:i:s');
                $dtl->save();
            } catch (Exception $e) {
                $dtl->status = 'failed';
                $dtl->message_exception = $e->getMessage();
                $dtl->save();

                Log::error('Salary WhatsApp notification failed', [
                    'batch_id' => $this->batchId,
                    'dtl_id' => $dtl->dtl_id,
                    'row_no' => $dtl->row_no,
                    'error' => $e->getMessage(),
                ]);
            }

            $this->refreshBatchCounts($this->batchId);
        }
    }

    public function failed(Throwable $exception)
    {
        Log::error('Salary WhatsApp batch job failed', [
            'batch_id' => $this->batchId,
            'error' => $exception->getMessage(),
        ]);

        $this->refreshBatchCounts($this->batchId);
    }

    protected function decodeTemplateParams($templateParams)
    {
        if (is_object($templateParams) && method_exists($templateParams, 'load')) {
            $templateParams->load();
        }

        $namedParams = json_decode((string) $templateParams, true);
        if (!is_array($namedParams)) {
            throw new Exception('Invalid template parameters for detail row.');
        }

        return $namedParams;
    }

    protected function refreshBatchCounts($batchId)
    {
        $batch = TblWaSalaryNotificationBatch::where('batch_id', $batchId)->first();
        if (!$batch) {
            return;
        }

        $sentCount = TblWaSalaryNotificationDtl::where('batch_id', $batchId)->where('status', 'sent')->count();
        $failedCount = TblWaSalaryNotificationDtl::where('batch_id', $batchId)->where('status', 'failed')->count();
        $pendingCount = TblWaSalaryNotificationDtl::where('batch_id', $batchId)
            ->whereIn('status', ['queued', 'pending'])
            ->count();

        $batch->sent_count = $sentCount;
        $batch->failed_count = $failedCount;

        if ($pendingCount === 0) {
            $batch->completed_at = date('Y-m-d H:i:s');
            $batch->status = $failedCount > 0 ? 'partial' : 'completed';
        } else {
            $batch->status = 'processing';
        }

        $batch->save();
    }
}
