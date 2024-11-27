<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiWHT extends Model
{
    protected $table = 'tbl_defi_wht_type';
    protected $primaryKey = 'wht_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
