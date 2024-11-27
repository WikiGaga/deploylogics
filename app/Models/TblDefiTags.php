<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiTags extends Model
{
    protected $table = 'tbl_defi_tags';
    protected $primaryKey = 'tags_id';
    protected $fillable = [
        'tags_id',
        'tags_name',
        'tags_type',
        'tags_entry_status',
        'business_id',
        'company_id',
        'branch_id',
        'tags_user_id',
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
