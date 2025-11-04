<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleOrderPartner extends Model
{
    protected $table = 'tbl_sale_order_partners';

    protected $primaryKey = 'partner_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
