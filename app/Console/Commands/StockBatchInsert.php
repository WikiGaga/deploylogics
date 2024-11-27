<?php

namespace App\Console\Commands;

use App\Http\Controllers\Accounts\POSVoucherController;
use App\Library\Utilities;
use App\Models\Defi\TblDefiConstants;
use App\Models\TblSoftBranch;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockBatchInsert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zcmd:stock_batch_insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stock Batch Insert';

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
        $pdo = DB::getPdo();
        $business_id = 1;
        $company_id = 1;
        $branch_id = 1;
        $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_PURC_STOCK_BATCH_INSERT(:p1, :p2, :p3); end;");
        $stmt->bindParam(':p1', $business_id);
        $stmt->bindParam(':p2', $company_id);
        $stmt->bindParam(':p3', $branch_id);
        $stmt->execute();
    }
}
