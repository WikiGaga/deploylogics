<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftListingStudioDefaultFilter extends Model
{
    protected $table = 'tbl_soft_listing_studio_default_filter';
    protected $primaryKey = 'listing_studio_default_filter_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
