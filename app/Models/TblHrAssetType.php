<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrAssetType extends Model
{
    protected $table = 'tbl_payr_asset_type';
    protected $primaryKey = 'asset_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
