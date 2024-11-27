<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiWeight extends Model
{
    protected $table = 'tbl_defi_weight';
    protected $primaryKey = 'weight_id';
    protected $fillable = [
        'weight_id',
        'weight_name',
        'weight_entry_status',
        'weight_user_id',
        'business_id',
        'company_id',
        'branch_id'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
