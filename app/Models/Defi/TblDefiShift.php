<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiShift extends Model
{
    protected $table = 'tbl_defi_shift';
    protected $primaryKey = 'shift_id';
    protected $fillable = [
        'shift_id',
        'shift_name',
        'shift_short_name',
        'shift_notes',
        'shift_sr_no',
        'user_id',
        'business_id',
        'company_id',
        'branch_id'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
