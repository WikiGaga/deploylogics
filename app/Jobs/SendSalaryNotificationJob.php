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

class SendSalaryNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dtlId;
    public $tries = 3;
    public $timeout = 60;

    public function __construct($dtlId)
    {
        $this->dtlId = $dtlId;
    }

    public function backoff()
    {
        return [30, 120, 300];
    }

    public function handle()
    {
        $dtl = TblWaSalaryNotificationDtl::where('dtl_id', $this->dtlId)->first();
        if (!$dtl) {
            throw new Exception('Salary notification detail row not found: ' . $this->dtlId);
        }

        if ($dtl->status === 'sent') {
            return;
        }

        $batch = TblWaSalaryNotificationBatch::where('batch_id', $dtl->batch_id)->first();
        if ($batch && $batch->status === 'queued') {
            $batch->status = 'processing';
            $batch->save();
        }

        $namedParams = json_decode($dtl->template_params, true);
        if (!is_array($namedParams)) {
            throw new Exception('Invalid template parameters for detail row.');
        }

        $service = new WhatsappService();
        $response = $service->sendSalaryNotification($dtl->phone, $namedParams);

        $messageId = isset($response['messages'][0]['id']) ? $response['messages'][0]['id'] : null;

        $dtl->status = 'sent';
        $dtl->meta_message_id = $messageId;
        $dtl->api_response = json_encode($response);
        $dtl->sent_at = date('Y-m-d H:i:s');
        $dtl->save();

        $this->refreshBatchCounts($dtl->batch_id);
    }

    public function failed(Throwable $exception)
    {
        $dtl = TblWaSalaryNotificationDtl::where('dtl_id', $this->dtlId)->first();
        if (!$dtl) {
            return;
        }

        $dtl->status = 'failed';
        $dtl->message_exception = $exception->getMessage();
        $dtl->save();

        Log::error('Salary WhatsApp notification failed', [
            'dtl_id' => $this->dtlId,
            'batch_id' => $dtl->batch_id,
            'error' => $exception->getMessage(),
        ]);

        $this->refreshBatchCounts($dtl->batch_id);
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
