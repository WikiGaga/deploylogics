<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewInveDeal extends Model
{
    protected $table = 'vw_inve_deal';
    protected $primaryKey = 'stock_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
