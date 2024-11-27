<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class TblSaleDiscountSetupMembership extends Model
{
    protected $table = 'tbl_sale_discount_setup_membership';

    protected $primaryKey = 'discount_setup_membership_id';

    protected $fillable = [
        'discount_setup_membership_id',
        'discount_setup_id',
        'membership_type_id',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
