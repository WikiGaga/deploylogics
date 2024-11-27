<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiColor extends Model
{
    protected $table = 'tbl_defi_color';
    protected $primaryKey = 'color_id';

    protected $fillable = [
        'color_id',
        'color_name',
        'color_entry_status',
        'business_id',
        'company_id',
        'branch_id',
        'color_user_id',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
