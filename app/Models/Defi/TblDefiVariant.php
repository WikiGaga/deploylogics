<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiVariant extends Model
{
    protected $table = 'tbl_defi_variant';
    protected $primaryKey = 'variant_id';
    protected $fillable = [
        'variant_id',
        'variant_name',
        'variant_entry_status',
        'variant_user_id',
        'business_id',
        'company_id',
        'branch_id'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
