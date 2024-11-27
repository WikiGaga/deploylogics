<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftListingStudioDimension extends Model
{
    protected $table = 'tbl_soft_listing_studio_dimension';
    protected $primaryKey = 'listing_studio_dimension_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
