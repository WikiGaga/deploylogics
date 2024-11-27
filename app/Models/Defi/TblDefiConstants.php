<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiConstants extends Model
{
    protected $table = 'tbl_defi_constants';
    protected $primaryKey = 'constants_id';
    protected $fillable = [
        'constants_id',
        'constants_key',
        'constants_value',
        'constants_type',
        'constants_status',
        'created_at',
        'updated_at'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
