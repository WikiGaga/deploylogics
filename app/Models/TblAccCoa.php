<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccCoa extends Model
{
    protected $table = 'tbl_acco_chart_account';
    protected $primaryKey = 'chart_account_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function childs() {
        return $this->hasMany(static::class,'parent_account_code','chart_code') ;
    }
    public function childs_chequebook() {
        return $this->hasMany(TblAccoChequeBook::class,'chart_account_id','chart_account_id') ;
    }
    public function chart_branches()
    {
        return $this->hasMany(TblAccCoaBranches::class,"chart_id");
    }
    public function children(){
        return $this->hasMany(self::class, 'parent_account_code', 'chart_code')->with('children')
            ->select(['chart_account_id as id','chart_code','chart_name','parent_account_code'])->orderBy('chart_code');
    }
}
