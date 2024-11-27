<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblReOrderStockAnalysisDtl extends Model
{
    protected $table = 'tbl_re_order_stock_analysis_dtl';
    protected $primaryKey = '';
    protected $fillable = [];
    protected $guarded = [
        'created_at',
        'updated_at'
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
