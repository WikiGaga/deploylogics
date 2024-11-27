<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleSchemes extends Model
{
    protected $table = 'tbl_sale_schemes';
    protected $primaryKey = 'schemes_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
