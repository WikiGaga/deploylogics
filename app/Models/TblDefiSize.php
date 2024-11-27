<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiSize extends Model
{
    protected $table = 'tbl_defi_size';
    protected $primaryKey = 'size_id';

    protected $fillable = [
        'size_id',
        'size_name',
        'size_entry_status',
        'business_id',
        'company_id',
        'branch_id',
        'size_user_id',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
