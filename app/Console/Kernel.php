<?php

namespace App\Console;

use App\Http\Controllers\Api\WhatsApp\WhatsAppApiController;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Defi\TblDefiConstants;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CreatePerson::class,
        \App\Console\Commands\VoucherPosting::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if(TblDefiConstants::where('constants_key','subdomain')->where('constants_status',1)->exists()){
            $subdomain = TblDefiConstants::where('constants_key','subdomain')->first()->constants_value;
            if($subdomain == 'firstcare'){
                $schedule->command('zcmd:stock_batch_insert')->everyTenMinutes();
            }
            if($subdomain == 'kawther'){
                $schedule->command('zcmd:stock_batch_insert')->everyTenMinutes();
            }
            if($subdomain == 'atayebat'){
                $schedule->command('voucher:posting')->hourlyAt(59);
                $schedule->command('voucher:cash_posting')->dailyAt('03:00');
                $schedule->command('reportverify:closingday')->timezone('Asia/Muscat')->dailyAt("23:55");
                $schedule->command('cheque:notify')->timezone('Asia/Muscat')->dailyAt("09:00");
            }
        }
        // $schedule->command('inspire')->hourly();
       // $schedule->command('cronJobs:createPerson')->everyMinute();
        

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
