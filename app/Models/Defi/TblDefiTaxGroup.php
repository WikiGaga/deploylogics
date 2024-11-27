<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiTaxGroup extends Model
{
    protected $table = 'tbl_defi_tax_group';
    protected $primaryKey = 'tax_group_id';
    protected $fillable = [
        'tax_group_id',
        'tax_group_name',
        'tax_group_value',
        'tax_group_entry_status',
        'tax_group_user_id',
        'business_id',
        'company_id',
        'branch_id'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
