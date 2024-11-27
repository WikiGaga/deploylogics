<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RentAgreementNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentagreement:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Whatsapp Notification To The Receiver & Payer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            // Make Your Code
        } catch (Exception $e) {
            Log::error('Unable to send rent agreement notification :' , [ $e->getMessage() ]);
        }
    }
}
