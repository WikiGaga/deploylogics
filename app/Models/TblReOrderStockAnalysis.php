<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblReOrderStockAnalysis extends Model
{
    protected $table = 'tbl_re_order_stock_analysis';
    protected $primaryKey = '';
    protected $fillable = [];
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtl() {
        return $this->hasMany(TblReOrderStockAnalysisDtl::class,'re_order_code')->orderBy('re_order_code') ;
    }
}
