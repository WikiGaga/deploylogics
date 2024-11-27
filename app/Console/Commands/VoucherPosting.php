<?php

namespace App\Console\Commands;

use App\Http\Controllers\Accounts\POSVoucherController;
use App\Models\TblSoftBranch;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class VoucherPosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:posting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $vc = new POSVoucherController();
        $now = new \DateTime("now");
        $today_format = $now->format("d-m-Y");
       // $today_format = "30-06-2021";
        $branches = TblSoftBranch::where('branch_active_status',1)->pluck('branch_id')->toArray();

        $request = new Request([
            "pos_voucher" => "1",
            "date_from" => $today_format,
            "date_to" => $today_format,
            "pos_branch_ids" => $branches
        ]);
        $vc->store($request);
    }
}
