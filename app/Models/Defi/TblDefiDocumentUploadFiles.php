<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiDocumentUploadFiles extends Model
{
    protected $table = 'tbl_defi_document_upload_files';
    protected $primaryKey = 'document_upload_files_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
