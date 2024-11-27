<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcGrnPayments extends Model
{
    //
    protected $table = 'vw_purc_grn_payments';
    protected $primaryKey = 'grn_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

}
