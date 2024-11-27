<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrDocuments extends Model
{
    protected $table = 'tbl_payr_documents';
    protected $primaryKey = 'document_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
