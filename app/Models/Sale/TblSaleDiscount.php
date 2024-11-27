<?php

namespace App\Models\Sale;

use App\Models\TblDefiUom;
use App\Models\TblPurcProduct;
use App\Models\TblSchemeBranches;
use Illuminate\Database\Eloquent\Model;

class TblSaleDiscount extends Model
{
    protected $table = 'tbl_sale_discount';

    protected $primaryKey = 'discount_setup_id';

    protected $fillable = [
        'discount_setup_id',
        'discount_title',
        'discount_code',
        'discount_type',
        'start_date',
        'end_date',
        'is_active',
        'remarks',
        'update_id',
        'user_id',
        'business_id',
        'company_id',
        'branch_id',
        'created_at',
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
