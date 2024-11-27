<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiDocumentUpload extends Model
{
    protected $table = 'tbl_defi_document_upload';
    protected $primaryKey = 'document_upload_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function files() {
        return $this->hasMany(TblDefiDocumentUploadFiles::class,'document_upload_id');
    }
}
