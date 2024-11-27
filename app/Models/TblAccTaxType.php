<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccTaxType extends Model
{
    protected $table = 'tbl_acco_tax_type';
    protected $primaryKey = 'tax_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
