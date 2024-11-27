<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSchemeBranches extends Model
{
    protected $table = 'tbl_scheme_branch';
    protected $primaryKey = 'scheme_branch_uuid';

    // public $timestamps = false;
    public $fillable = [
        'scheme_id',
        'branch_id',
        'scheme_branch_uuid',
        'discount_setup_id',
        'start_date',
        'end_date',
        'discount_setup_title',
        'is_active',
        'min_sale_amount',
        'loyalty_rate',
        'is_with_member',
        'is_without_member',
        'slab_base',
    ];




    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
