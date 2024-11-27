<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSchemeSlab extends Model
{
    protected $table = 'tbl_scheme_slab';
    protected $primaryKey = 'slab_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function scheme(){
        return $this->belongsTo(TblScheme::class , 'scheme_id');
    }

    function dtls(){
        return $this->hasMany(TblSchemeSlabDtl::class , 'slab_id')
        ->with('product','barcode');
    }

}
