<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcGrnListing extends Model
{
    //
    protected $table = 'vw_purc_grn_listing';
    protected $primaryKey = 'grn_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
