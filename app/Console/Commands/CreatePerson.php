<?php

namespace App\Console\Commands;

use App\Library\Utilities;
use App\Models\Person;
use App\Models\TblAccCoa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreatePerson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronJobs:createPerson';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Person create successfully';

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
        \App\Library\MatchTable::mergeTable('tbl_sale_sales','sales_id');
        \App\Library\MatchTable::mergeTable('tbl_sale_sales_dtl','sales_dtl_id');
    }
}
