<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class TblPurcBrand extends Model
{
    public $timestamps = true;
    protected $table = 'tbl_purc_brand';
    protected $primaryKey = 'brand_id';

    protected $fillable = [
        'brand_id',
        'brand_name',
        'brand_entry_status',
        'brand_user_id',
        'business_id',
        'company_id',
        'branch_id',
    ];

    //use SoftDeletes;
    protected $hidden = [];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
