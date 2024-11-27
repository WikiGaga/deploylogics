<?php

namespace App\Models\Sale;

use App\Models\TblDefiUom;
use App\Models\TblPurcProduct;
use App\Models\TblSchemeBranches;
use Illuminate\Database\Eloquent\Model;

class TblSaleDiscountSetup extends Model
{
    protected $table = 'tbl_sale_discount_setup';

    protected $primaryKey = 'discount_setup_id';

    protected $fillable = [
        'discount_setup_row_id',
        'discount_setup_id',
        'discount_setup_title',
        'discount_setup_code',
        'discount_setup_type',
        'start_date',
        'end_date',
        'sale_type',
        'discount_type',
        'promotion_type',
        'discount_qty',
        'discount_perc',
        'flat_discount_qty',
        'flat_discount_amount',
        'sr_no',
        'product_id',
        'product_barcode_id',
        'product_barcode_barcode',
        'uom_id',
        'packing',
        'group_item_id',
        'cost_rate',
        'mrp',
        'sale_rate',
        'gp_amount',
        'gp_perc',
        'disc_amount',
        'disc_perc',
        'after_disc_gp_amount',
        'after_disc_gp_perc',
        'is_active',
        'remarks',
        'user_id',
        'business_id',
        'company_id',
        'branch_id',
        'amount_for_point',
        'point_quantity',
        'slab_base',
        'is_with_member',
        'is_without_member',
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function discount_setup_membership()
    {
        return $this->hasMany(TblSaleDiscountSetupMembership::class,"discount_setup_id");
    }
    public function scheme_branches()
    {
        return $this->hasMany(TblSchemeBranches::class,"discount_setup_id");
    }
    public function product()
    {
        return $this->belongsTo(TblPurcProduct::class,"product_id");
    }
    public function uom() {
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }

}
