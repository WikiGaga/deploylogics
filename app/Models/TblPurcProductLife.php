<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductLife extends Model
{
    protected $table = 'tbl_purc_product_life';

    protected $primaryKey = 'product_life_id';

    protected $fillable = ['product_life_id','product_id','country_id','product_life_period_type','product_life_period'];

    public function country(){
        return $this->belongsTo(TblDefiCountry::class, 'country_id');
    }
}
