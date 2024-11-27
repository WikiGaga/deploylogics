<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftListingStudio extends Model
{
    protected $table = 'tbl_soft_listing_studio';
    protected $primaryKey = 'listing_studio_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function listing_studio_user_filter(){
        return $this->hasMany(TblSoftListingStudioUserFilter::class,'listing_studio_id');
    }
    public function listing_studio_dimension(){
        return $this->hasMany(TblSoftListingStudioDimension::class,'listing_studio_id')->orderby('sr_no');
    }
    public function listing_studio_default_filter(){
        return $this->hasMany(TblSoftListingStudioDefaultFilter::class,'listing_studio_id');
    }
    public function listing_studio_metric(){
        return $this->hasMany(TblSoftListingStudioMetric::class,'listing_studio_id');
    }
    public function join_table(){
        return $this->hasMany(TblSoftListingStudioJoinTable::class,'listing_studio_id');
    }
}
