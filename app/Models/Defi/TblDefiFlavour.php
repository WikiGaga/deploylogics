<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiFlavour extends Model
{
    protected $table = 'tbl_defi_flavour';
    protected $primaryKey = 'flavour_id';
    protected $fillable = [
        'flavour_id',
        'flavour_name',
        'flavour_entry_status',
        'flavour_user_id',
        'business_id',
        'company_id',
        'branch_id'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
