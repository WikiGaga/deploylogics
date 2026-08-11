<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPosVoucherBranchSync extends Model
{
    protected $table = 'tbl_pos_voucher_branch_sync';

    protected $fillable = [
        'branch_id',
        'last_order_updated_at',
        'last_run_at',
        'last_processed_count',
    ];

    protected $casts = [
        'last_order_updated_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];
}
