<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiDocumentType extends Model
{
    // tbl_defi_document_type

    protected $table = 'tbl_defi_document_type';
    protected $primaryKey = 'document_type_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }


}
