<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblFileUpload extends Model
{
    protected $table = 'tbl_file_upload';
    protected $primaryKey = 'file_upload_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
