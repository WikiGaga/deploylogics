<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleSalesContract extends Model
{
    protected $table = 'tbl_sale_sales_contract';
    protected $primaryKey = 'sales_contract_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblSaleSalesContractDtl::class, 'sales_contract_id')
            ->with('product','barcode','uom','packing')
            ->orderBy('sales_contract_sr','asc');
    }
    public function customer() {
        return $this->belongsTo(TblSaleCustomer::class, 'customer_id');
    }
}
