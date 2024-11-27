<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductType extends Model
{
    protected $table = 'tbl_purc_product_type';
    protected $primaryKey = 'product_type_id';

    protected $fillable = [
        'product_type_id',
        'product_type_name',
        'product_type_entry_status',
        'product_type_user_id',
        'business_id',
        'company_id',
        'branch_id'
    ];

    protected $guarded = ['created_at', 'updated_at'];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
