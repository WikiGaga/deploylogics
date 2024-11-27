<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftListingStudioMetric extends Model
{
    protected $table = 'tbl_soft_listing_studio_metric';
    protected $primaryKey = 'listing_studio_metric_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
