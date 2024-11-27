<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblScheme extends Model
{
    protected $table = 'tbl_scheme';
    protected $primaryKey = 'scheme_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function schemeAvail(){
        return $this->hasMany(TblSchemeAvail::class , 'scheme_id')
        ->with('product','barcode');
    }

    function schemeSlab(){
        return $this->hasMany(TblSchemeSlab::class , 'scheme_id')
        ->with('dtls')
        ->orderBy('sr_no' , 'asc');
    }
    
    function schemeSlabDtl(){
        return $this->hasMany(TblSchemeSlabDtl::class , 'scheme_id')
        ->with('product','barcode');
    }
    
    function schemeBranches(){
        return $this->hasMany(TblSchemeBranches::class , 'scheme_id');
    }
}
