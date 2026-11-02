<?php

namespace App\Console\Commands;

use App\Http\Controllers\Accounts\POSVoucherController;
use App\Models\TblSoftBranch;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class VoucherPosting extends Command
{
    protected $signature = 'voucher:posting
                            {date_from? : Start date in DD-MM-YYYY format}
                            {date_to? : End date in DD-MM-YYYY format}';

    protected $description = 'Post POS vouchers for the specified date range';

    public function handle()
    {
        $dateFrom = $this->argument('date_from')
                    ?: now()->subDay()->format('d-m-Y');
        $dateTo = $this->argument('date_to')
                    ?: ($this->argument('date_from') ?: now()->format('d-m-Y'));

        // Validate dates strictly.
        $parsedDates = [];

        foreach (['date_from' => $dateFrom, 'date_to' => $dateTo] as $name => $value) {
            $date = \DateTimeImmutable::createFromFormat('!d-m-Y', $value);

            if (!$date || $date->format('d-m-Y') !== $value) {
                $this->error("Invalid {$name}. Use DD-MM-YYYY.");
                return 1;
            }

            $parsedDates[$name] = $date;
        }

        if ($parsedDates['date_from'] > $parsedDates['date_to']) {
            $this->error('date_from cannot be later than date_to.');
            return 1;
        }

        $branches = TblSoftBranch::where('branch_active_status', 1)
            ->pluck('branch_id')
            ->toArray();

        $request = new Request([
            'pos_voucher' => '1',
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'pos_branch_ids' => $branches,
        ]);

        app(POSVoucherController::class)->store($request);

        return 0;
    }
}