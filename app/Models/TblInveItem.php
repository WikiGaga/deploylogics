<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblInveItem extends Model
{
    protected $table = 'tbl_inve_item';
    protected $primaryKey = 'item_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblInveItemDtl::class, 'item_id')
            ->with('product','barcode','uom');
    }

}
