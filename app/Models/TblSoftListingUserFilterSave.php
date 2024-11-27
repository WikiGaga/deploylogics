<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftListingUserFilterSave extends Model
{
    protected $table = 'tbl_soft_listing_user_filter_save';
    protected $primaryKey = 'listing_user_filter_save_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
