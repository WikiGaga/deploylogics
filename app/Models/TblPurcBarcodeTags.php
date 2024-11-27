<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcBarcodeTags extends Model
{
    protected $table = 'tbl_purc_barcode_tags';
    protected $primaryKey = 'barcode_tags_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
