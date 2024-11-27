<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblInveItemFormulation extends Model
{
    protected $table = 'tbl_inve_item_formulation';
    protected $primaryKey = 'item_formulation_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtls() {
        return $this->hasMany(TblInveItemFormulationDtl::class, 'item_formulation_id')
            ->with('product','barcode','uom','constants');
    }
    public function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }

}
