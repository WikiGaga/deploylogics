<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcManufacturer extends Model
{
    protected $table = 'tbl_purc_manufacturer';
    protected $primaryKey = 'manufacturer_id';

    protected $fillable = [
        'manufacturer_id',
        'manufacturer_name',
        'manufacturer_entry_status',
        'manufacturer_user_id',
        'business_id',
        'company_id',
        'branch_id'
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
